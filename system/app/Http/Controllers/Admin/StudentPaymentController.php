<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\BankAccount;
use App\Models\ClassModel;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\PaymentPost;
use App\Models\SavingsTransaction;
use App\Models\Student;
use App\Models\StudentPaymentBill;
use App\Models\StudentPaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentPaymentController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassModel::where('is_active', true)->get();
        $query = Student::with(['user', 'schoolClass', 'paymentBills']);

        if ($request->filled('major_id')) {
            $query->where('major_id', $request->major_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(15)->withQueryString();

        $pendingCount = \App\Models\PaymentTransaction::where('reference_type', 'spp')
            ->where('payment_gateway', 'manual')
            ->where('status', 'pending')
            ->count();

        return view('admin.student-payments.index', compact('students', 'classes', 'pendingCount'));
    }

    public function manualConfirm(Request $request)
    {
        $pendingManualPayments = \App\Models\PaymentTransaction::with('user.student')
            ->where('reference_type', 'spp')
            ->where('payment_gateway', 'manual')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $pendingCount = $pendingManualPayments->count();

        return view('admin.student-payments.manual-confirm', compact('pendingManualPayments', 'pendingCount'));
    }

    public function tracking(Request $request)
    {
        $academicYears = AcademicYear::latest()->get();
        $classes = ClassModel::where('is_active', true)->get();
        $paymentPosts = PaymentPost::all();

        $selectedYearId = $request->get('academic_year_id');
        $selectedClassId = $request->get('class_id');
        $selectedPostId = $request->get('payment_post_id');
        $selectedType = $request->get('bill_type', 'all');
        $statusFilter = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Student::with([
            'user',
            'schoolClass',
            'paymentBills' => function ($q) use ($selectedYearId, $selectedPostId, $selectedType, $statusFilter) {
                $q->whereHas('paymentBill', function ($pb) use ($selectedYearId, $selectedPostId, $selectedType) {
                    if ($selectedType && $selectedType !== 'all') {
                        $pb->where('type', $selectedType);
                    }
                    if ($selectedYearId) {
                        $pb->where('academic_year_id', $selectedYearId);
                    }
                    if ($selectedPostId) {
                        $pb->where('payment_post_id', $selectedPostId);
                    }
                });

                if ($statusFilter && $statusFilter !== 'all') {
                    $q->where('status', $statusFilter);
                }

                $q->with(['paymentBill.academicYear', 'paymentBill.paymentPost', 'details' => function ($d) {
                    $d->orderBy('month_no', 'asc');
                }]);
            }
        ]);

        if ($selectedClassId) {
            $query->where('class_id', $selectedClassId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(20)->withQueryString();

        $totalUnpaidAmount = 0;
        $totalUnpaidCount = 0;

        foreach ($students as $student) {
            foreach ($student->paymentBills as $bill) {
                foreach ($bill->details as $detail) {
                    if ($detail->status !== 'paid') {
                        $totalUnpaidAmount += ($detail->amount - $detail->paid_amount);
                        $totalUnpaidCount++;
                    }
                }
            }
        }

        $pendingCount = \App\Models\PaymentTransaction::where('reference_type', 'spp')
            ->where('payment_gateway', 'manual')
            ->where('status', 'pending')
            ->count();

        return view('admin.student-payments.tracking', compact(
            'students',
            'academicYears',
            'classes',
            'paymentPosts',
            'selectedYearId',
            'selectedClassId',
            'selectedPostId',
            'selectedType',
            'statusFilter',
            'search',
            'totalUnpaidAmount',
            'totalUnpaidCount',
            'pendingCount'
        ));
    }

    public function pay(Student $student)
    {
        $student->load(['user', 'schoolClass', 'paymentBills.paymentBill.paymentPost', 'paymentBills.paymentBill.academicYear', 'paymentBills.details']);
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $transactions = FinancialTransaction::where('reference_type', 'StudentPayment')
            ->where('reference_id', $student->id)
            ->latest()
            ->get();

        $pendingManualPayments = \App\Models\PaymentTransaction::where('user_id', $student->user_id)
            ->where('reference_type', 'spp')
            ->where('payment_gateway', 'manual')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.student-payments.pay', compact('student', 'bankAccounts', 'transactions', 'pendingManualPayments'));
    }

    public function processPayment(Request $request, Student $student)
    {
        $request->validate([
            'bank_account_id' => 'required',
            'payment_type' => 'required|in:bulanan,bebas',
            'notes' => 'nullable|string',
        ]);

        if ($request->payment_type === 'bulanan') {
            $request->validate([
                'detail_ids' => 'required|array|min:1',
                'detail_ids.*' => 'exists:student_payment_details,id',
            ]);
        } else {
            $request->validate([
                'detail_id' => 'required|exists:student_payment_details,id',
                'amount' => 'required|numeric|min:1000',
            ]);
        }

        $isSavingsPayment = $request->bank_account_id === 'savings';

        DB::beginTransaction();
        try {
            // Get or Create FinancialCategory for Student Payments
            $category = FinancialCategory::firstOrCreate(
                ['code' => 'KAT-SISWA-01'],
                [
                    'name' => 'Pembayaran Siswa (SPP & Tagihan)',
                    'type' => 'pemasukan',
                    'description' => 'Pemasukan dari pembayaran tagihan sekolah siswa',
                    'is_active' => true,
                ]
            );

            if ($isSavingsPayment) {
                $bankAccount = BankAccount::where('is_active', true)->first();
            } else {
                $bankAccount = BankAccount::findOrFail($request->bank_account_id);
            }

            if ($request->payment_type === 'bulanan') {
                $details = StudentPaymentDetail::with('studentPaymentBill.paymentBill.paymentPost')
                    ->whereIn('id', $request->detail_ids)
                    ->get();

                $totalPayment = $details->sum(function ($d) {
                    return $d->amount - $d->paid_amount;
                });

                if ($totalPayment <= 0) {
                    return back()->with('error', 'Detail tagihan yang dipilih sudah lunas.');
                }

                if ($isSavingsPayment) {
                    if ($student->savings_balance < $totalPayment) {
                        return back()->with('error', 'Saldo tabungan siswa tidak mencukupi (Saldo: Rp ' . number_format($student->savings_balance, 0, ',', '.') . ', Tagihan: Rp ' . number_format($totalPayment, 0, ',', '.') . ').');
                    }
                    $student->savings_balance -= $totalPayment;
                    $student->save();

                    SavingsTransaction::create([
                        'student_id' => $student->id,
                        'transaction_type' => 'payment',
                        'amount' => $totalPayment,
                        'balance_after' => $student->savings_balance,
                        'reference_no' => 'SAV-PAY-' . time() . '-' . rand(100, 999),
                        'student_payment_detail_id' => $details->first()?->id,
                        'notes' => 'Pembayaran SPP via Saldo Tabungan oleh Petugas',
                        'created_by' => auth()->id(),
                    ]);
                }

                // Create Financial Transaction (Cashbook Income)
                $txNumber = FinancialTransaction::generateTransactionNumber('pemasukan');
                $descList = $details->map(function ($d) {
                    return $d->studentPaymentBill->paymentBill->name . ' (' . $d->month_name . ')';
                })->implode(', ');

                $transaction = FinancialTransaction::create([
                    'transaction_number' => $txNumber,
                    'type' => 'pemasukan',
                    'financial_category_id' => $category->id,
                    'bank_account_id' => $bankAccount?->id,
                    'amount' => $totalPayment,
                    'transaction_date' => now()->toDateString(),
                    'description' => "Pembayaran {$student->name} (NISN: {$student->nisn}): " . $descList . ($isSavingsPayment ? ' [Via Tabungan]' : ''),
                    'recipient_or_payee' => $student->name,
                    'created_by' => auth()->id(),
                    'reference_type' => 'StudentPayment',
                    'reference_id' => $student->id,
                ]);

                foreach ($details as $detail) {
                    $detail->update([
                        'paid_amount' => $detail->amount,
                        'status' => 'paid',
                        'paid_at' => now(),
                        'bank_account_id' => $bankAccount?->id,
                        'financial_transaction_id' => $transaction->id,
                        'notes' => $request->notes . ($isSavingsPayment ? ' (Dibayar via Tabungan Siswa)' : ''),
                    ]);

                    $detail->studentPaymentBill->recalculateStatus();
                }
            } else {
                // Bebas / Cicilan
                $detail = StudentPaymentDetail::with('studentPaymentBill.paymentBill.paymentPost')->findOrFail($request->detail_id);
                $payAmount = floatval($request->amount);
                $remaining = $detail->amount - $detail->paid_amount;

                if ($payAmount > $remaining) {
                    return back()->with('error', "Nominal pembayaran (Rp " . number_format($payAmount, 0, ',', '.') . ") melebihi sisa tagihan (Rp " . number_format($remaining, 0, ',', '.') . ").");
                }

                if ($isSavingsPayment) {
                    if ($student->savings_balance < $payAmount) {
                        return back()->with('error', 'Saldo tabungan siswa tidak mencukupi (Saldo: Rp ' . number_format($student->savings_balance, 0, ',', '.') . ', Tagihan: Rp ' . number_format($payAmount, 0, ',', '.') . ').');
                    }
                    $student->savings_balance -= $payAmount;
                    $student->save();

                    SavingsTransaction::create([
                        'student_id' => $student->id,
                        'transaction_type' => 'payment',
                        'amount' => $payAmount,
                        'balance_after' => $student->savings_balance,
                        'reference_no' => 'SAV-PAY-' . time() . '-' . rand(100, 999),
                        'student_payment_detail_id' => $detail->id,
                        'notes' => 'Pembayaran tagihan via Saldo Tabungan oleh Petugas',
                        'created_by' => auth()->id(),
                    ]);
                }

                $newPaidAmount = $detail->paid_amount + $payAmount;
                $newStatus = ($newPaidAmount >= $detail->amount) ? 'paid' : 'partial';

                // Create Financial Transaction
                $txNumber = FinancialTransaction::generateTransactionNumber('pemasukan');
                $transaction = FinancialTransaction::create([
                    'transaction_number' => $txNumber,
                    'type' => 'pemasukan',
                    'financial_category_id' => $category->id,
                    'bank_account_id' => $bankAccount?->id,
                    'amount' => $payAmount,
                    'transaction_date' => now()->toDateString(),
                    'description' => "Cicilan {$detail->studentPaymentBill->paymentBill->name} {$student->name} (NISN: {$student->nisn})" . ($isSavingsPayment ? ' [Via Tabungan]' : ''),
                    'recipient_or_payee' => $student->name,
                    'created_by' => auth()->id(),
                    'reference_type' => 'StudentPayment',
                    'reference_id' => $student->id,
                ]);

                $detail->update([
                    'paid_amount' => $newPaidAmount,
                    'status' => $newStatus,
                    'paid_at' => now(),
                    'bank_account_id' => $bankAccount?->id,
                    'financial_transaction_id' => $transaction->id,
                    'notes' => $request->notes . ($isSavingsPayment ? ' (Dibayar via Tabungan Siswa)' : ''),
                ]);

                $detail->studentPaymentBill->recalculateStatus();
            }

            DB::commit();

            // Send WA Notification to parent if enabled
            $waTarget = $student->parent_phone ?? $student->phone;
            if ($waTarget && \App\Models\Setting::get('wa_notify_payment', '1') == '1') {
                $schoolName = \App\Models\Setting::get('school_name', 'Sekolah');
                $formattedAmount = 'Rp ' . number_format($transaction->amount, 0, ',', '.');
                $waMsg = "Kwitansi Pembayaran - {$schoolName}\n\nTerima kasih, pembayaran sekolah a.n. *{$student->name}* (NISN: {$student->nisn}) telah diterima.\n\nKeterangan: {$transaction->description}\nTotal Pembayaran: *{$formattedAmount}*\nNo. Transaksi: {$transaction->transaction_number}\nTanggal: " . now()->format('d M Y H:i') . " WIB\n\nStatus: BERHASIL LUNAS";
                \App\Services\WhatsAppService::sendMessage($waTarget, $waMsg);
            }

            return redirect()->route('admin.student-payments.receipt', ['transaction' => $transaction->id])
                ->with('success', 'Pembayaran berhasil dicatat & masuk ke Buku Kas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function receipt(FinancialTransaction $transaction)
    {
        $transaction->load(['bankAccount', 'financialCategory', 'creator']);
        $student = null;

        if ($transaction->reference_type === 'StudentPayment' && $transaction->reference_id) {
            $student = Student::with('schoolClass')->find($transaction->reference_id);
        }

        $paidDetails = StudentPaymentDetail::with('studentPaymentBill.paymentBill.paymentPost')
            ->where('financial_transaction_id', $transaction->id)
            ->get();

        return view('admin.student-payments.receipt', compact('transaction', 'student', 'paidDetails'));
    }

    public function printReceipt(FinancialTransaction $transaction)
    {
        $transaction->load(['bankAccount', 'financialCategory', 'creator']);
        $student = null;

        if ($transaction->reference_type === 'StudentPayment' && $transaction->reference_id) {
            $student = Student::with('schoolClass')->find($transaction->reference_id);
        }

        $paidDetails = StudentPaymentDetail::with('studentPaymentBill.paymentBill.paymentPost')
            ->where('financial_transaction_id', $transaction->id)
            ->get();

        return view('admin.student-payments.print', compact('transaction', 'student', 'paidDetails'));
    }

    public function printHistory(Request $request, Student $student)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        $transactions = FinancialTransaction::where('reference_type', 'StudentPayment')
            ->where('reference_id', $student->id)
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->oldest()
            ->get();

        $schoolName = \App\Models\Setting::get('school_name', 'SEKOLAH');
        $schoolAddress = \App\Models\Setting::get('school_address', '');
        $schoolPhone = \App\Models\Setting::get('school_phone', '');
        $schoolEmail = \App\Models\Setting::get('school_email', '');

        return view('admin.student-payments.print-history', compact('student', 'transactions', 'startDate', 'endDate', 'schoolName', 'schoolAddress', 'schoolPhone', 'schoolEmail'));
    }

    public function printHistoryReceipt(Request $request, Student $student)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);

        $transactions = FinancialTransaction::where('reference_type', 'StudentPayment')
            ->where('reference_id', $student->id)
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $paidDetails = StudentPaymentDetail::with('studentPaymentBill.paymentBill.paymentPost')
            ->whereIn('financial_transaction_id', $transactions->pluck('id'))
            ->get();

        $totalAmount = $paidDetails->sum('paid_amount');

        $schoolName = \App\Models\Setting::get('school_name', 'SEKOLAH');
        $schoolAddress = \App\Models\Setting::get('school_address', '');
        $schoolPhone = \App\Models\Setting::get('school_phone', '');
        $schoolEmail = \App\Models\Setting::get('school_email', '');

        return view('admin.student-payments.print-history-receipt', compact('student', 'paidDetails', 'startDate', 'endDate', 'totalAmount', 'schoolName', 'schoolAddress', 'schoolPhone', 'schoolEmail'));
    }

    public function approvePayment(Student $student, \App\Models\PaymentTransaction $transaction)
    {
        if ($transaction->status !== 'pending' || $transaction->reference_type !== 'spp') {
            return back()->with('error', 'Transaksi tidak valid atau sudah diproses.');
        }

        try {
            \App\Http\Controllers\PaymentController::completePayment($transaction, [
                'approved_by' => auth()->id(),
                'approved_at' => now()->toDateTimeString(),
                'note' => 'Disetujui oleh admin/bendahara manual'
            ]);

            return back()->with('success', 'Pembayaran SPP/Tagihan siswa berhasil diverifikasi dan disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    public function rejectPayment(Student $student, \App\Models\PaymentTransaction $transaction)
    {
        if ($transaction->status !== 'pending' || $transaction->reference_type !== 'spp') {
            return back()->with('error', 'Transaksi tidak valid atau sudah diproses.');
        }

        DB::beginTransaction();
        try {
            // Delete payment proof image if exists
            if ($transaction->payment_proof) {
                delete_public_file($transaction->payment_proof, 'img/spp/proofs');
            }

            $transaction->update([
                'status' => 'failed',
                'notes' => 'Pembayaran ditolak oleh admin/bendahara: ' . now()->toDateTimeString()
            ]);

            DB::commit();
            return back()->with('success', 'Pembayaran manual transfer siswa telah ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }

    public function destroyTransaction(FinancialTransaction $transaction)
    {
        DB::beginTransaction();
        try {
            $student = null;
            if ($transaction->reference_type === 'StudentPayment' && $transaction->reference_id) {
                $student = Student::find($transaction->reference_id);
            }

            // Get all payment details associated with this transaction
            $details = StudentPaymentDetail::with('studentPaymentBill.paymentBill')
                ->where('financial_transaction_id', $transaction->id)
                ->get();

            foreach ($details as $detail) {
                $bill = $detail->studentPaymentBill;
                $isBulanan = $bill && $bill->paymentBill && $bill->paymentBill->type === 'bulanan';

                if ($isBulanan) {
                    $detail->update([
                        'paid_amount' => 0,
                        'status' => 'unpaid',
                        'paid_at' => null,
                        'bank_account_id' => null,
                        'financial_transaction_id' => null,
                        'notes' => null,
                    ]);
                } else {
                    $newPaidAmount = max(0, (int) $detail->paid_amount - (int) $transaction->amount);
                    $newStatus = $newPaidAmount > 0 ? 'partial' : 'unpaid';
                    $detail->update([
                        'paid_amount' => $newPaidAmount,
                        'status' => $newStatus,
                        'paid_at' => $newPaidAmount > 0 ? $detail->paid_at : null,
                        'bank_account_id' => $newPaidAmount > 0 ? $detail->bank_account_id : null,
                        'financial_transaction_id' => $newPaidAmount > 0 ? $detail->financial_transaction_id : null,
                        'notes' => $newPaidAmount > 0 ? $detail->notes : null,
                    ]);
                }

                if ($bill) {
                    $bill->recalculateStatus();
                }
            }

            // Refund savings if transaction was paid using student savings
            $detailIds = $details->pluck('id')->toArray();
            $savingsTx = null;
            if (!empty($detailIds)) {
                $savingsTx = SavingsTransaction::whereIn('student_payment_detail_id', $detailIds)
                    ->where('transaction_type', 'payment')
                    ->latest()
                    ->first();
            }

            if (!$savingsTx && $student && str_contains($transaction->description ?? '', '[Via Tabungan]')) {
                $savingsTx = SavingsTransaction::where('student_id', $student->id)
                    ->where('transaction_type', 'payment')
                    ->where('amount', $transaction->amount)
                    ->latest()
                    ->first();
            }

            if ($savingsTx && $student) {
                $student->savings_balance += $savingsTx->amount;
                $student->save();
                $savingsTx->delete();
            } elseif ($student && str_contains($transaction->description ?? '', '[Via Tabungan]')) {
                $student->savings_balance += $transaction->amount;
                $student->save();
            }

            // Delete proof file if existing
            if ($transaction->payment_proof) {
                delete_public_file($transaction->payment_proof, 'img/spp/proofs');
            }
            if ($transaction->proof_file) {
                delete_public_file($transaction->proof_file, 'img/spp/proofs');
            }

            // Delete financial transaction
            $transaction->delete();

            DB::commit();
            return back()->with('success', 'Data transaksi pembayaran berhasil dihapus dan status tagihan telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}

