<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SavingsTransaction;
use App\Models\BankAccount;
use App\Models\PaymentTransaction;
use App\Models\Setting;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class StudentSavingsController extends Controller
{
    /**
     * Display student savings dashboard & transaction list.
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $transactions = SavingsTransaction::with('studentPaymentDetail.studentPaymentBill.paymentBill')
            ->where('student_id', $student->id)
            ->latest()
            ->paginate(15);

        $totalDeposit = SavingsTransaction::where('student_id', $student->id)->where('transaction_type', 'deposit')->sum('amount');
        $totalWithdraw = SavingsTransaction::where('student_id', $student->id)->where('transaction_type', 'withdraw')->sum('amount');
        $totalPaidBills = SavingsTransaction::where('student_id', $student->id)->where('transaction_type', 'payment')->sum('amount');

        $bankAccounts = BankAccount::where('is_active', true)->where('type', 'bank')->get();

        // Also fetch any pending manual savings deposit transaction so they can upload proof on index page if needed
        $pendingManualDeposit = PaymentTransaction::where('user_id', $user->id)
            ->where('reference_type', 'savings_deposit')
            ->where('payment_gateway', 'manual')
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('student.savings.index', compact(
            'student',
            'transactions',
            'totalDeposit',
            'totalWithdraw',
            'totalPaidBills',
            'pendingManualDeposit',
            'bankAccounts'
        ));
    }

    /**
     * Show deposit form for student savings.
     */
    public function showDepositForm()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Profil siswa tidak ditemukan.');
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
            ->where('reference_type', 'savings_deposit')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $pendingDeposit = $pendingTransactions->first();

        return view('student.savings.deposit', compact(
            'student',
            'activeGateways',
            'tripayChannels',
            'bankAccounts',
            'pendingTransactions',
            'pendingDeposit'
        ));
    }

    /**
     * Checkout deposit for student savings.
     */
    public function checkoutDeposit(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return back()->withErrors(['error' => 'Profil siswa tidak ditemukan.']);
        }

        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_gateway' => 'required|in:midtrans,tripay,duitku,manual',
            'payment_channel' => 'required_if:payment_gateway,tripay',
            'pending_id' => 'nullable|exists:payment_transactions,id',
        ], [
            'amount.min' => 'Nominal setoran minimal adalah Rp 10.000',
        ]);

        $totalAmount = floatval($request->amount);

        // Check if there is an existing pending transaction
        $existingPending = PaymentTransaction::where('user_id', $user->id)
            ->where('reference_type', 'savings_deposit')
            ->where('status', 'pending')
            ->first();

        if ($existingPending && !$request->filled('pending_id')) {
            return back()->withErrors(['error' => 'Anda masih memiliki setoran pending yang belum diselesaikan. Silakan ubah atau batalkan setoran tersebut terlebih dahulu.']);
        }

        if ($request->filled('pending_id')) {
            $transaction = PaymentTransaction::where('user_id', $user->id)
                ->where('id', $request->pending_id)
                ->where('status', 'pending')
                ->first();

            if (!$transaction) {
                return back()->withErrors(['error' => 'Setoran pending tidak ditemukan atau sudah diproses.']);
            }

            $transaction->update([
                'amount' => $totalAmount,
                'payment_gateway' => $request->payment_gateway,
                'snap_token' => null,
                'payment_url' => null,
                'payment_method_code' => null,
                'payment_proof' => null,
            ]);
        } else {
            $itemName = 'Setoran Tabungan Siswa: ' . $student->name;
            $invoiceNumber = 'INV-SAV-' . $student->id . '-' . time();

            // Create transaction entry
            $transaction = PaymentTransaction::create([
                'user_id' => $user->id,
                'reference_type' => 'savings_deposit',
                'reference_id' => $student->id,
                'payment_gateway' => $request->payment_gateway,
                'invoice_number' => $invoiceNumber,
                'amount' => $totalAmount,
                'status' => 'pending',
                'payload' => [
                    'type' => 'deposit',
                ],
            ]);
        }

        if ($request->payment_gateway === 'midtrans') {
            $customerDetails = [
                'name' => $student->name,
                'email' => $user->email,
                'phone' => $student->phone ?? $user->phone ?? '',
            ];
            $itemName = 'Setoran Tabungan Siswa: ' . $student->name;
            $response = PaymentGatewayService::createMidtransTransaction($transaction->invoice_number, $totalAmount, $customerDetails, $itemName);
            if ($response['success']) {
                $transaction->update([
                    'snap_token' => $response['snap_token'],
                    'payment_url' => $response['redirect_url'],
                ]);
                return redirect()->away($response['redirect_url']);
            }
            if (!$request->filled('pending_id')) {
                $transaction->delete();
            }
            return back()->withErrors(['error' => $response['message']]);
        }

        if ($request->payment_gateway === 'tripay') {
            $customerDetails = [
                'name' => $student->name,
                'email' => $user->email,
                'phone' => $student->phone ?? $user->phone ?? '',
            ];
            $itemName = 'Setoran Tabungan Siswa: ' . $student->name;
            $response = PaymentGatewayService::createTripayTransaction($transaction->invoice_number, $totalAmount, $request->payment_channel, $customerDetails, $itemName);
            if ($response['success']) {
                $transaction->update([
                    'payment_url' => $response['payment_url'],
                    'payment_method_code' => $request->payment_channel,
                ]);
                return redirect()->away($response['payment_url']);
            }
            if (!$request->filled('pending_id')) {
                $transaction->delete();
            }
            return back()->withErrors(['error' => $response['message']]);
        }

        if ($request->payment_gateway === 'duitku') {
            $customerDetails = [
                'name' => $student->name,
                'email' => $user->email,
                'phone' => $student->phone ?? $user->phone ?? '',
            ];
            $itemName = 'Setoran Tabungan Siswa: ' . $student->name;
            $response = PaymentGatewayService::createDuitkuTransaction($transaction->invoice_number, $totalAmount, $customerDetails, $itemName);
            if ($response['success']) {
                $transaction->update([
                    'payment_url' => $response['payment_url'],
                ]);
                return redirect()->away($response['payment_url']);
            }
            if (!$request->filled('pending_id')) {
                $transaction->delete();
            }
            return back()->withErrors(['error' => $response['message']]);
        }

        // Manual Transfer Checkout
        return redirect()->route('student.savings.deposit.show')->with([
            'success' => 'Silakan lakukan transfer bank manual dan unggah bukti transfer di bawah.',
            'pending_manual_transaction' => $transaction->id,
        ]);
    }

    /**
     * Upload manual transfer proof for savings deposit.
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
            $targetSubfolder = $isDoc ? 'doc/savings/proofs' : 'img/savings/proofs';
            $savedProof = save_uploaded_public_file($file, $targetSubfolder);

            $bank = BankAccount::find($request->bank_account_id);
            $bankInfo = $bank ? "Transfer ke: {$bank->bank_name} - {$bank->account_number} a.n. {$bank->account_name}" : '';
            $notes = $request->notes ? $bankInfo . "\nCatatan: " . $request->notes : $bankInfo;

            $transaction->update([
                'payment_proof' => ($isDoc ? 'doc/savings/proofs/' : 'savings/proofs/') . basename($savedProof),
                'notes' => $notes,
                'payment_method_code' => $bank ? $bank->bank_name : null,
            ]);

            // Notify Admin via WhatsApp if configured
            $adminPhone = Setting::get('school_whatsapp');
            if ($adminPhone && Setting::get('wa_notify_payment', '1') == '1') {
                $schoolName = Setting::get('school_name', 'Sekolah');
                $msg = "Notifikasi Setoran Tabungan Manual - {$schoolName}\n\nHalo Admin,\n\nSiswa telah mengunggah bukti transfer manual untuk setoran tabungan:\n\nNama: *{$student->name}* (NISN: {$student->nisn})\nNominal: *Rp " . number_format($transaction->amount, 0, ',', '.') . "*\n\nSilakan periksa di dashboard admin dan lakukan verifikasi.\nTerima kasih.";
                \App\Services\WhatsAppService::sendMessage($adminPhone, $msg);
            }

            return redirect()->route('student.savings.index')->with('success', 'Bukti transfer setoran berhasil diunggah. Menunggu konfirmasi verifikasi admin.');
        }

        return back()->withErrors(['error' => 'Gagal mengunggah berkas bukti transfer.']);
    }

    /**
     * Cancel pending savings deposit.
     */
    public function cancelDeposit(PaymentTransaction $transaction)
    {
        $user = Auth::user();
        if ($transaction->user_id !== $user->id || $transaction->status !== 'pending' || $transaction->reference_type !== 'savings_deposit') {
            return back()->withErrors(['error' => 'Transaksi tidak valid.']);
        }

        // Delete proof file if exists
        if ($transaction->payment_proof) {
            delete_public_file($transaction->payment_proof, 'img/savings/proofs');
        }

        $transaction->delete();

        return redirect()->route('student.savings.deposit.show')->with('success', 'Transaksi setoran pending berhasil dibatalkan.');
    }
}
