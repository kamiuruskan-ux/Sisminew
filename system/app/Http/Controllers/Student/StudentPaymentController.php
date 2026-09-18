<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\PaymentPost;
use App\Models\PaymentTransaction;
use App\Models\SavingsTransaction;
use App\Models\StudentPaymentBill;
use App\Models\StudentPaymentDetail;
use App\Models\Setting;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StudentPaymentController extends Controller
{
    /**
     * Display student payment posts overview cards.
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $studentBills = StudentPaymentBill::with(['paymentBill.paymentPost', 'details'])
            ->where('student_id', $student->id)
            ->get();

        // Group by Payment Post ID
        $postGroups = [];
        $totalUnpaidOverall = 0;
        $totalUnpaidItemsOverall = 0;

        foreach ($studentBills as $sBill) {
            $post = $sBill->paymentBill->paymentPost;
            if (!$post) continue;

            $postId = $post->id;
            if (!isset($postGroups[$postId])) {
                $postGroups[$postId] = [
                    'post' => $post,
                    'type' => $sBill->paymentBill->type,
                    'total_bills' => 0,
                    'unpaid_bills' => 0,
                    'unpaid_amount' => 0,
                    'bills' => [],
                ];
            }

            $postGroups[$postId]['bills'][] = $sBill;
            $postGroups[$postId]['total_bills']++;

            $unpaidAmountForBill = max(0, $sBill->total_amount - $sBill->paid_amount);
            if ($unpaidAmountForBill > 0) {
                $postGroups[$postId]['unpaid_bills']++;
                $postGroups[$postId]['unpaid_amount'] += $unpaidAmountForBill;
                $totalUnpaidOverall += $unpaidAmountForBill;
            }

            foreach ($sBill->details as $detail) {
                if ($detail->status !== 'paid') {
                    $totalUnpaidItemsOverall++;
                }
            }
        }

        // Pending manual transactions count
        $pendingCount = PaymentTransaction::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return view('student.payments.index', compact(
            'student',
            'postGroups',
            'totalUnpaidOverall',
            'totalUnpaidItemsOverall',
            'pendingCount'
        ));
    }

    /**
     * Display transaction & checkout page for specific payment post.
     */
    public function showPos(PaymentPost $paymentPost)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $paymentBills = StudentPaymentBill::with(['paymentBill.paymentPost', 'details'])
            ->where('student_id', $student->id)
            ->whereHas('paymentBill', function ($q) use ($paymentPost) {
                $q->where('payment_post_id', $paymentPost->id);
            })
            ->latest()
            ->get();

        if ($paymentBills->isEmpty()) {
            return redirect()->route('student.payments.index')->with('error', 'Tidak ada tagihan untuk pos ini.');
        }

        $activeGateways = [];
        if (Setting::get('payment_manual_enabled', '1') == '1') $activeGateways[] = 'manual';
        if (Setting::get('payment_midtrans_enabled', '0') == '1') $activeGateways[] = 'midtrans';
        if (Setting::get('payment_tripay_enabled', '0') == '1') $activeGateways[] = 'tripay';
        if (Setting::get('payment_duitku_enabled', '0') == '1') $activeGateways[] = 'duitku';

        $tripayChannels = [];
        if (in_array('tripay', $activeGateways)) {
            $tripayChannels = PaymentGatewayService::getTripayChannels();
        }

        $bankAccounts = BankAccount::where('is_active', true)->where('type', 'bank')->get();

        $pendingTransactions = PaymentTransaction::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('payment_gateway', 'manual')
            ->latest()
            ->get();

        return view('student.payments.show', compact(
            'student',
            'paymentPost',
            'paymentBills',
            'activeGateways',
            'tripayChannels',
            'bankAccounts',
            'pendingTransactions'
        ));
    }

    /**
     * Process checkout for student bill.
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return back()->withErrors(['error' => 'Profil siswa tidak ditemukan.']);
        }

        $request->validate([
            'payment_gateway' => 'required|in:midtrans,tripay,duitku,manual,savings',
            'payment_channel' => 'required_if:payment_gateway,tripay',
            'payment_type' => 'required|in:bulanan,bebas',
        ]);

        $detailIds = [];
        $totalAmount = 0;
        $itemName = 'Pembayaran Sekolah';

        if ($request->payment_type === 'bulanan') {
            $request->validate([
                'detail_ids' => 'required|array|min:1',
                'detail_ids.*' => 'exists:student_payment_details,id',
            ]);

            $details = StudentPaymentDetail::with('studentPaymentBill.paymentBill')
                ->whereIn('id', $request->detail_ids)
                ->get();

            $detailIds = $details->pluck('id')->toArray();
            $totalAmount = $details->sum(function ($d) {
                return $d->amount - $d->paid_amount;
            });

            $descList = $details->map(function ($d) {
                return $d->studentPaymentBill->paymentBill->name . ' (' . $d->month_name . ')';
            })->implode(', ');
            $itemName = 'SPP: ' . $descList;
        } else {
            // Bebas / Cicilan
            $request->validate([
                'detail_id' => 'required|exists:student_payment_details,id',
                'amount' => 'required|numeric|min:1000',
            ]);

            $detail = StudentPaymentDetail::with('studentPaymentBill.paymentBill')->findOrFail($request->detail_id);
            $detailIds = [$detail->id];
            $totalAmount = floatval($request->amount);

            $remaining = $detail->amount - $detail->paid_amount;
            if ($totalAmount > $remaining) {
                return back()->withErrors(['error' => "Nominal melebihi sisa tagihan (Sisa: Rp " . number_format($remaining, 0, ',', '.') . ")"]);
            }

            $itemName = 'Cicilan: ' . $detail->studentPaymentBill->paymentBill->name;
        }

        if ($totalAmount <= 0) {
            return back()->withErrors(['error' => 'Nominal tagihan tidak valid atau sudah lunas.']);
        }

        $invoiceNumber = 'INV-SPP-' . $student->id . '-' . time();

        $customerDetails = [
            'name' => $student->name,
            'email' => $user->email,
            'phone' => $student->phone ?? $user->phone ?? '',
        ];

        // Create transaction entry
        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'reference_type' => 'spp',
            'reference_id' => $detailIds[0], // First detail ID as anchor
            'payment_gateway' => $request->payment_gateway,
            'invoice_number' => $invoiceNumber,
            'amount' => $totalAmount,
            'status' => 'pending',
            'payload' => [
                'detail_ids' => $detailIds,
                'payment_type' => $request->payment_type,
            ],
        ]);

        if ($request->payment_gateway === 'savings') {
            $request->validate([
                'pin' => 'required|numeric|digits:6',
            ], [
                'pin.required' => 'PIN keamanan wajib diisi untuk pembayaran tabungan.',
                'pin.numeric' => 'PIN harus berupa angka.',
                'pin.digits' => 'PIN harus terdiri dari 6 digit.',
            ]);

            if (empty($student->pin)) {
                $transaction->delete();
                return back()->withErrors(['pin' => 'Anda belum mengatur PIN keamanan. Silakan atur PIN Anda terlebih dahulu di menu profil.']);
            }

            if (!\Illuminate\Support\Facades\Hash::check($request->pin, $student->pin)) {
                $transaction->delete();
                return back()->withErrors(['pin' => 'PIN keamanan yang Anda masukkan salah.']);
            }

            if ($student->savings_balance < $totalAmount) {
                $transaction->delete();
                return back()->withErrors([
                    'error' => 'Saldo tabungan Anda tidak mencukupi (Saldo: Rp ' . number_format($student->savings_balance, 0, ',', '.') . ', Tagihan: Rp ' . number_format($totalAmount, 0, ',', '.') . ')'
                ]);
            }

            DB::beginTransaction();
            try {
                // 1. Deduct savings balance
                $student->savings_balance -= $totalAmount;
                $student->save();

                // 2. Log savings transaction
                $refNo = 'SAV-PAY-' . time() . '-' . rand(100, 999);
                SavingsTransaction::create([
                    'student_id' => $student->id,
                    'transaction_type' => 'payment',
                    'amount' => $totalAmount,
                    'balance_after' => $student->savings_balance,
                    'reference_no' => $refNo,
                    'student_payment_detail_id' => $detailIds[0],
                    'notes' => 'Pembayaran tagihan: ' . $itemName,
                    'created_by' => $user->id,
                ]);

                // 3. Update payment details
                if ($request->payment_type === 'bulanan') {
                    $detailsToUpdate = StudentPaymentDetail::whereIn('id', $detailIds)->get();
                    $affectedBills = [];
                    foreach ($detailsToUpdate as $d) {
                        $d->paid_amount = $d->amount;
                        $d->status = 'paid';
                        $d->save();
                        $affectedBills[$d->student_payment_bill_id] = $d->studentPaymentBill;
                    }
                    foreach ($affectedBills as $bill) {
                        $bill->recalculateStatus();
                    }
                } else {
                    $d = StudentPaymentDetail::findOrFail($detailIds[0]);
                    $d->paid_amount += $totalAmount;
                    if ($d->paid_amount >= $d->amount) {
                        $d->status = 'paid';
                    } else {
                        $d->status = 'partial';
                    }
                    $d->save();
                    $d->studentPaymentBill->recalculateStatus();
                }

                // 4. Mark transaction as completed
                $transaction->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                // 5. Create financial transaction entry
                $category = FinancialCategory::firstOrCreate(
                    ['code' => 'KAT-SISWA-01'],
                    [
                        'name' => 'Pembayaran Siswa (SPP & Tagihan)',
                        'type' => 'pemasukan',
                        'description' => 'Pemasukan dari pembayaran SPP dan tagihan siswa',
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
                    'amount' => $totalAmount,
                    'transaction_date' => now()->toDateString(),
                    'description' => 'Pembayaran tagihan via Tabungan: ' . $itemName . ' oleh ' . $student->name,
                    'recipient_or_payee' => $student->name,
                    'reference_type' => 'StudentPayment',
                    'reference_id' => $student->id,
                    'created_by' => auth()->id(),
                ]);

                DB::commit();

                return redirect()->route('student.payments.index')->with(
                    'success',
                    'Pembayaran tagihan sebesar Rp ' . number_format($totalAmount, 0, ',', '.') . ' menggunakan Saldo Tabungan berhasil!'
                );
            } catch (\Exception $e) {
                DB::rollBack();
                $transaction->delete();
                return back()->withErrors(['error' => 'Gagal memproses pembayaran tabungan: ' . $e->getMessage()]);
            }
        }

        if ($request->payment_gateway === 'midtrans') {
            $response = PaymentGatewayService::createMidtransTransaction($invoiceNumber, $totalAmount, $customerDetails, $itemName);
            if ($response['success']) {
                $transaction->update([
                    'snap_token' => $response['snap_token'],
                    'payment_url' => $response['redirect_url'],
                ]);
                return redirect()->away($response['redirect_url']);
            }
            $transaction->delete();
            return back()->withErrors(['error' => $response['message']]);
        }

        if ($request->payment_gateway === 'tripay') {
            $response = PaymentGatewayService::createTripayTransaction($invoiceNumber, $totalAmount, $request->payment_channel, $customerDetails, $itemName);
            if ($response['success']) {
                $transaction->update([
                    'payment_url' => $response['payment_url'],
                    'payment_method_code' => $request->payment_channel,
                ]);
                return redirect()->away($response['payment_url']);
            }
            $transaction->delete();
            return back()->withErrors(['error' => $response['message']]);
        }

        if ($request->payment_gateway === 'duitku') {
            $response = PaymentGatewayService::createDuitkuTransaction($invoiceNumber, $totalAmount, $customerDetails, $itemName);
            if ($response['success']) {
                $transaction->update([
                    'payment_url' => $response['payment_url'],
                ]);
                return redirect()->away($response['payment_url']);
            }
            $transaction->delete();
            return back()->withErrors(['error' => $response['message']]);
        }

        // Manual Transfer Checkout
        return redirect()->route('student.payments.index')->with([
            'success' => 'Silakan lakukan transfer bank manual dan unggah bukti transfer di bawah.',
            'pending_manual_transaction' => $transaction->id,
        ]);
    }

    /**
     * Upload manual transfer proof for student bill.
     */
    public function uploadManualProof(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return back()->withErrors(['error' => 'Profil siswa tidak ditemukan.']);
        }

        $request->validate([
            'transaction_id' => 'required|exists:payment_transactions,id',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'notes' => 'nullable|string|max:500',
        ]);

        $transaction = PaymentTransaction::findOrFail($request->transaction_id);

        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'docx', 'doc']);
            $targetSubfolder = $isDoc ? 'doc/spp/proofs' : 'img/spp/proofs';
            $savedProof = save_uploaded_public_file($file, $targetSubfolder);

            $bank = BankAccount::find($request->bank_account_id);
            $bankInfo = $bank ? "Transfer ke: {$bank->bank_name} - {$bank->account_number} a.n. {$bank->account_name}" : '';
            $notes = $request->notes ? $bankInfo . "\nCatatan: " . $request->notes : $bankInfo;

            $transaction->update([
                'payment_proof' => ($isDoc ? 'doc/spp/proofs/' : 'spp/proofs/') . basename($savedProof),
                'notes' => $notes,
                'payment_method_code' => $bank ? $bank->bank_name : null,
            ]);

            // Notify Admin via WhatsApp if configured
            $adminPhone = Setting::get('school_whatsapp');
            if ($adminPhone && Setting::get('wa_notify_payment', '1') == '1') {
                $schoolName = Setting::get('school_name', 'Sekolah');
                $msg = "Notifikasi Pembayaran SPP Manual - {$schoolName}\n\nHalo Admin,\n\nSiswa telah mengunggah bukti transfer manual tagihan sekolah:\n\nNama: *{$student->name}* (NISN: {$student->nisn})\nNominal: *Rp " . number_format($transaction->amount, 0, ',', '.') . "*\n\nSilakan periksa di dashboard admin dan lakukan verifikasi pembayaran.\nTerima kasih.";
                \App\Services\WhatsAppService::sendMessage($adminPhone, $msg);
            }

            return redirect()->route('student.payments.index')->with('success', 'Bukti transfer pembayaran berhasil diunggah. Menunggu konfirmasi verifikasi admin.');
        }

        return back()->withErrors(['error' => 'Gagal mengunggah berkas bukti transfer.']);
    }
}

