<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\PaymentTransaction;
use App\Models\SpmbRegistration;
use App\Models\StudentPaymentDetail;
use App\Models\Setting;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Handle Midtrans Webhook Notification callback.
     */
    public function midtransCallback(Request $request)
    {
        Log::info('Midtrans Webhook Received: ', $request->all());

        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');
        $transactionStatus = $request->input('transaction_status');

        // Verify signature
        $serverKey = Setting::get('payment_midtrans_server_key');
        $localSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($localSignature !== $signatureKey) {
            Log::warning('Midtrans Webhook Invalid Signature for Order: ' . $orderId);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $transaction = PaymentTransaction::where('invoice_number', $orderId)->first();
        if (!$transaction) {
            Log::warning('Midtrans Webhook Order Not Found: ' . $orderId);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Process status
        if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
            if ($transaction->status !== 'completed') {
                $this->completePayment($transaction, $request->all());
            }
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $transaction->update([
                'status' => $transactionStatus === 'expire' ? 'expired' : 'failed',
                'payload' => array_merge((array)$transaction->payload, ['callback' => $request->all()]),
            ]);
        }

        return response()->json(['message' => 'Callback processed successfully']);
    }

    /**
     * Handle Tripay Callback.
     */
    public function tripayCallback(Request $request)
    {
        Log::info('Tripay Callback Received: ', $request->all());

        $privateKey = Setting::get('payment_tripay_private_key');
        $callbackSignature = $request->header('X-Callback-Signature');
        $json = $request->getContent();

        $localSignature = hash_hmac('sha256', $json, $privateKey);

        if ($callbackSignature !== $localSignature) {
            Log::warning('Tripay Callback Invalid Signature');
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
        }

        if ($request->input('event') !== 'payment_status') {
            return response()->json(['success' => true, 'message' => 'Ignored event']);
        }

        $orderId = $request->input('merchant_ref');
        $status = strtoupper($request->input('status'));

        $transaction = PaymentTransaction::where('invoice_number', $orderId)->first();
        if (!$transaction) {
            Log::warning('Tripay Callback Order Not Found: ' . $orderId);
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        if ($status === 'PAID') {
            if ($transaction->status !== 'completed') {
                $this->completePayment($transaction, $request->all());
            }
        } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
            $transaction->update([
                'status' => strtolower($status),
                'payload' => array_merge((array)$transaction->payload, ['callback' => $request->all()]),
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Handle Duitku Webhook Callback.
     */
    public function duitkuCallback(Request $request)
    {
        Log::info('Duitku Callback Received: ', $request->all());

        $merchantCode = $request->input('merchantCode');
        $amount = $request->input('amount');
        $merchantOrderId = $request->input('merchantOrderId');
        $signature = $request->input('signature');
        $resultCode = $request->input('resultCode');

        // Verify signature
        $apiKey = Setting::get('payment_duitku_api_key');
        $expectedSignature = md5($merchantCode . $amount . $merchantOrderId . $apiKey);

        if ($signature !== $expectedSignature) {
            Log::warning('Duitku Callback Invalid Signature for Order: ' . $merchantOrderId);
            return response('Invalid signature', 400)->header('Content-Type', 'text/plain');
        }

        $transaction = PaymentTransaction::where('invoice_number', $merchantOrderId)->first();
        if (!$transaction) {
            Log::warning('Duitku Callback Order Not Found: ' . $merchantOrderId);
            return response('Transaction not found', 404)->header('Content-Type', 'text/plain');
        }

        if ($resultCode === '00') {
            if ($transaction->status !== 'completed') {
                self::completePayment($transaction, $request->all());
            }
        } else {
            $transaction->update([
                'status' => 'failed',
                'payload' => array_merge((array)$transaction->payload, ['callback' => $request->all()]),
            ]);
        }

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Complete payment transaction, updating references and inserting financial record.
     */
    public static function completePayment(PaymentTransaction $transaction, array $callbackData)
    {
        DB::beginTransaction();
        try {
            $transaction->update([
                'status' => 'completed',
                'paid_at' => now(),
                'payload' => array_merge((array)$transaction->payload, ['callback' => $callbackData]),
            ]);

            $schoolName = Setting::get('school_name', 'Sekolah');

            if ($transaction->reference_type === 'spmb') {
                $spmb = SpmbRegistration::find($transaction->reference_id);
                if ($spmb) {
                    $spmb->update([
                        'payment_status' => 'paid',
                        // Status is draft so they can fill stages 2-5
                        'status' => 'draft',
                    ]);

                    // Get default or first active bank account
                    $bankAccount = BankAccount::where('is_active', true)->where('type', 'bank')->first();
                    $bankAccountId = $bankAccount ? $bankAccount->id : null;

                    // Get or create Financial Category for SPMB
                    $category = FinancialCategory::firstOrCreate(
                        ['code' => 'KAT-SPMB-01'],
                        [
                            'name' => 'Penerimaan SPMB (Uang Pendaftaran)',
                            'type' => 'pemasukan',
                            'description' => 'Pemasukan dari uang pendaftaran calon siswa baru',
                            'is_active' => true,
                        ]
                    );

                    // Generate a transaction code
                    $txNumber = FinancialTransaction::generateTransactionNumber('pemasukan');

                    // Create financial transaction record
                    FinancialTransaction::create([
                        'transaction_number' => $txNumber,
                        'type' => 'pemasukan',
                        'financial_category_id' => $category->id,
                        'bank_account_id' => $bankAccountId,
                        'amount' => $transaction->amount,
                        'transaction_date' => now()->toDateString(),
                        'description' => "Uang Pendaftaran SPMB: {$spmb->full_name} ({$spmb->registration_number})",
                        'recipient_or_payee' => $spmb->full_name,
                        'reference_type' => 'SpmbRegistration',
                        'reference_id' => $spmb->id,
                    ]);

                    // Send WhatsApp Notification to applicant
                    if ($spmb->phone && Setting::get('wa_notify_spmb', '1') == '1') {
                        $msg = "Pembayaran Pendaftaran Berhasil - {$schoolName}\n\nHalo *{$spmb->full_name}*,\n\nPembayaran uang pendaftaran SPMB Anda sebesar *Rp " . number_format($transaction->amount, 0, ',', '.') . "* telah kami terima secara resmi.\n\nStatus Akun: *Aktif & Lunas Uang Pendaftaran*\nNomor Pendaftaran: *{$spmb->registration_number}*\n\nSilakan login kembali ke portal SPMB untuk melengkapi pengisian data diri Anda (Tahap 2 - 5) dan mengunggah berkas persyaratan.\n\nTerima kasih.";
                        WhatsAppService::sendMessage($spmb->phone, $msg);
                    }
                }
            } elseif ($transaction->reference_type === 'spp') {
                $payload = $transaction->payload;
                $detailIds = isset($payload['detail_ids']) ? $payload['detail_ids'] : [$transaction->reference_id];
                
                // Get default or first active bank account
                $bankAccount = BankAccount::where('is_active', true)->where('type', 'bank')->first();
                $bankAccountId = $bankAccount ? $bankAccount->id : null;

                // Get or create Financial Category for Student payments
                $category = FinancialCategory::firstOrCreate(
                    ['code' => 'KAT-SISWA-01'],
                    [
                        'name' => 'Pembayaran Siswa (SPP & Tagihan)',
                        'type' => 'pemasukan',
                        'description' => 'Pemasukan dari pembayaran tagihan sekolah siswa',
                        'is_active' => true,
                    ]
                );

                $details = StudentPaymentDetail::whereIn('id', $detailIds)->get();
                if ($details->count() > 0) {
                    $firstDetail = $details->first();
                    $student = $firstDetail->studentPaymentBill->student;

                    // Generate a transaction code
                    $txNumber = FinancialTransaction::generateTransactionNumber('pemasukan');

                    $descriptionList = $details->map(function ($d) {
                        return $d->studentPaymentBill->paymentBill->name . ' (' . $d->month_name . ')';
                    })->implode(', ');

                    // Create financial transaction record for the aggregate payment
                    $financialTx = FinancialTransaction::create([
                        'transaction_number' => $txNumber,
                        'type' => 'pemasukan',
                        'financial_category_id' => $category->id,
                        'bank_account_id' => $bankAccountId,
                        'amount' => $transaction->amount,
                        'transaction_date' => now()->toDateString(),
                        'description' => "Pembayaran Online SPP/Tagihan: {$descriptionList} Siswa: {$student->name}",
                        'recipient_or_payee' => $student->name,
                        'reference_type' => 'StudentPayment',
                        'reference_id' => $student->id,
                    ]);

                    foreach ($details as $detail) {
                        // Update payment detail status
                        $detail->update([
                            'paid_amount' => $detail->amount,
                            'status' => 'paid',
                            'paid_at' => now(),
                            'bank_account_id' => $bankAccountId,
                            'financial_transaction_id' => $financialTx->id,
                            'notes' => 'Lunas via Online Payment Gateway (' . strtoupper($transaction->payment_gateway) . ')',
                        ]);

                        // Recalculate status of the bill
                        $detail->studentPaymentBill->recalculateStatus();
                    }

                    // Send WhatsApp receipt notification to student / parent
                    $waTarget = $student->parent_phone ?? $student->phone;
                    if ($waTarget && Setting::get('wa_notify_payment', '1') == '1') {
                        $msg = "Kwitansi Pembayaran Online - {$schoolName}\n\nHalo,\n\nTerima kasih. Pembayaran tagihan secara online telah berhasil diproses:\n\nSiswa: *{$student->name}* (NISN: {$student->nisn})\nTagihan: *{$descriptionList}*\nNominal: *Rp " . number_format($transaction->amount, 0, ',', '.') . "*\nStatus: *LUNAS*\nMetode: *" . strtoupper($transaction->payment_gateway) . "*\nTanggal: " . now()->format('d-m-Y H:i') . " WIB\n\nSimpan pesan ini sebagai bukti pembayaran sah Anda.\nTerima kasih.";
                        WhatsAppService::sendMessage($waTarget, $msg);
                    }
                }
            } elseif ($transaction->reference_type === 'savings_deposit') {
                $student = \App\Models\Student::find($transaction->reference_id);
                if ($student) {
                    // 1. Add savings balance
                    $student->savings_balance += $transaction->amount;
                    $student->save();

                    // 2. Create savings transaction log
                    $refNo = 'SAV-DEP-' . time() . '-' . rand(100, 999);
                    \App\Models\SavingsTransaction::create([
                        'student_id' => $student->id,
                        'transaction_type' => 'deposit',
                        'amount' => $transaction->amount,
                        'balance_after' => $student->savings_balance,
                        'reference_no' => $refNo,
                        'notes' => 'Setoran Tabungan Online via ' . strtoupper($transaction->payment_gateway),
                        'created_by' => $transaction->user_id,
                    ]);

                    // 3. Create financial transaction record (income for school/bank)
                    $category = FinancialCategory::firstOrCreate(
                        ['code' => 'KAT-TAB-IN'],
                        [
                            'name' => 'Pemasukan Tabungan Siswa',
                            'type' => 'pemasukan',
                            'description' => 'Setoran tabungan dari siswa',
                            'is_active' => true,
                        ]
                    );

                    $bankAccount = BankAccount::where('is_active', true)->where('type', 'bank')->first();
                    $bankAccountId = $bankAccount ? $bankAccount->id : null;

                    FinancialTransaction::create([
                        'transaction_number' => FinancialTransaction::generateTransactionNumber('pemasukan'),
                        'type' => 'pemasukan',
                        'financial_category_id' => $category->id,
                        'bank_account_id' => $bankAccountId,
                        'amount' => $transaction->amount,
                        'transaction_date' => now()->toDateString(),
                        'description' => "Setoran Tabungan Online: {$student->name} (NISN: {$student->nisn})",
                        'recipient_or_payee' => $student->name,
                        'reference_type' => 'StudentSavings',
                        'reference_id' => $student->id,
                        'created_by' => $transaction->user_id,
                    ]);

                    // 4. Send WhatsApp Notification
                    $waTarget = $student->parent_phone ?? $student->phone;
                    if ($waTarget && Setting::get('wa_notify_payment', '1') == '1') {
                        $msg = "Notifikasi Setoran Tabungan - {$schoolName}\n\nHalo,\n\nSetoran tabungan Anda secara online telah berhasil diproses:\n\nSiswa: *{$student->name}* (NISN: {$student->nisn})\nNominal: *Rp " . number_format($transaction->amount, 0, ',', '.') . "*\nStatus: *BERHASIL*\nMetode: *" . strtoupper($transaction->payment_gateway) . "*\nTanggal: " . now()->format('d-m-Y H:i') . " WIB\n\nSaldo Tabungan Terbaru: *Rp " . number_format($student->savings_balance, 0, ',', '.') . "*\n\nTerima kasih.";
                        WhatsAppService::sendMessage($waTarget, $msg);
                    }
                }
            }

            DB::commit();
            Log::info('Payment transaction successfully completed: ' . $transaction->invoice_number);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing payment for invoice ' . $transaction->invoice_number . ': ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
