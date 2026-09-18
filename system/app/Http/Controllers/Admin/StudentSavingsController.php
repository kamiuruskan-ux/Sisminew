<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ClassModel;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\SavingsTransaction;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentSavingsController extends Controller
{
    /**
     * Display a listing of student savings accounts.
     */
    public function index(Request $request)
    {
        $classes = ClassModel::where('is_active', true)->get();

        $query = Student::with(['user', 'schoolClass']);

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

        $students = $query->paginate(20)->withQueryString();

        // Statistics
        $totalSavings = Student::sum('savings_balance');
        $totalDeposits = SavingsTransaction::where('transaction_type', 'deposit')->sum('amount');
        $totalWithdrawals = SavingsTransaction::where('transaction_type', 'withdraw')->sum('amount');
        $studentsWithSavings = Student::where('savings_balance', '>', 0)->count();

        $pendingCount = \App\Models\PaymentTransaction::where('reference_type', 'savings_deposit')
            ->where('payment_gateway', 'manual')
            ->where('status', 'pending')
            ->count();

        return view('admin.savings.index', compact(
            'students',
            'classes',
            'totalSavings',
            'totalDeposits',
            'totalWithdrawals',
            'studentsWithSavings',
            'pendingCount'
        ));
    }

    public function manualConfirm(Request $request)
    {
        $pendingManualDeposits = \App\Models\PaymentTransaction::with('user.student')
            ->where('reference_type', 'savings_deposit')
            ->where('payment_gateway', 'manual')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $pendingCount = $pendingManualDeposits->count();

        return view('admin.savings.manual-confirm', compact('pendingManualDeposits', 'pendingCount'));
    }

    /**
     * Display student savings account detail & transaction history.
     */
    public function show(Student $student)
    {
        $student->load(['user', 'schoolClass', 'savingsTransactions.creator']);

        $transactions = SavingsTransaction::with(['creator', 'studentPaymentDetail.studentPaymentBill.paymentBill'])
            ->where('student_id', $student->id)
            ->latest()
            ->paginate(15);

        $totalDeposit = SavingsTransaction::where('student_id', $student->id)->where('transaction_type', 'deposit')->sum('amount');
        $totalWithdraw = SavingsTransaction::where('student_id', $student->id)->where('transaction_type', 'withdraw')->sum('amount');
        $totalPaidBills = SavingsTransaction::where('student_id', $student->id)->where('transaction_type', 'payment')->sum('amount');

        // Fetch pending manual savings deposits
        $pendingManualDeposits = \App\Models\PaymentTransaction::where('user_id', $student->user_id)
            ->where('reference_type', 'savings_deposit')
            ->where('payment_gateway', 'manual')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $pendingCount = \App\Models\PaymentTransaction::where('reference_type', 'savings_deposit')
            ->where('payment_gateway', 'manual')
            ->where('status', 'pending')
            ->count();

        return view('admin.savings.show', compact(
            'student',
            'transactions',
            'totalDeposit',
            'totalWithdraw',
            'totalPaidBills',
            'pendingManualDeposits',
            'pendingCount'
        ));
    }

    /**
     * Approve pending manual savings deposit.
     */
    public function approveDeposit(Student $student, \App\Models\PaymentTransaction $transaction)
    {
        if ($transaction->status !== 'pending' || $transaction->reference_type !== 'savings_deposit') {
            return back()->withErrors(['error' => 'Transaksi tidak valid atau sudah diproses.']);
        }

        try {
            \App\Http\Controllers\PaymentController::completePayment($transaction, [
                'approved_by_admin' => true,
                'admin_id' => Auth::id(),
            ]);

            return back()->with('success', 'Setoran manual sebesar Rp ' . number_format($transaction->amount, 0, ',', '.') . ' berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyetujui setoran: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject pending manual savings deposit.
     */
    public function rejectDeposit(Student $student, \App\Models\PaymentTransaction $transaction)
    {
        if ($transaction->status !== 'pending' || $transaction->reference_type !== 'savings_deposit') {
            return back()->withErrors(['error' => 'Transaksi tidak valid atau sudah diproses.']);
        }

        try {
            DB::beginTransaction();

            // Delete proof file if exists
            if ($transaction->payment_proof) {
                delete_public_file($transaction->payment_proof, 'img/savings/proofs');
            }

            $transaction->update([
                'status' => 'failed',
                'notes' => $transaction->notes . "\nDitolak oleh Admin/Bendahara pada " . now()->format('d/m/Y H:i'),
            ]);

            DB::commit();

            return back()->with('success', 'Setoran manual berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menolak setoran: ' . $e->getMessage()]);
        }
    }

    /**
     * Process deposit into student savings.
     */
    public function deposit(Request $request, Student $student)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'notes' => 'nullable|string|max:255',
            'record_as_financial_income' => 'nullable|boolean',
        ]);

        $amount = (float) $request->amount;

        DB::beginTransaction();
        try {
            $student->savings_balance += $amount;
            $student->save();

            $refNo = 'SAV-DEP-' . time() . '-' . rand(100, 999);

            $transaction = SavingsTransaction::create([
                'student_id' => $student->id,
                'transaction_type' => 'deposit',
                'amount' => $amount,
                'balance_after' => $student->savings_balance,
                'reference_no' => $refNo,
                'notes' => $request->notes ?? 'Setor tunai tabungan siswa oleh admin/bendahara',
                'created_by' => Auth::id(),
            ]);

            // If requested, record in financial transactions as cash input
            if ($request->boolean('record_as_financial_income')) {
                $category = FinancialCategory::firstOrCreate(
                    ['code' => 'KAT-TAB-IN'],
                    [
                        'name' => 'Pemasukan Tabungan Siswa',
                        'type' => 'pemasukan',
                        'description' => 'Setoran tabungan dari siswa',
                        'is_active' => true,
                    ]
                );

                $bankAccount = BankAccount::where('is_active', true)->first();
                $txNumber = FinancialTransaction::generateTransactionNumber('pemasukan');

                FinancialTransaction::create([
                    'transaction_number' => $txNumber,
                    'type' => 'pemasukan',
                    'financial_category_id' => $category->id,
                    'bank_account_id' => $bankAccount?->id,
                    'amount' => $amount,
                    'transaction_date' => now()->toDateString(),
                    'description' => 'Setor tabungan siswa ' . $student->name . ' (' . $student->nisn . ')',
                    'recipient_or_payee' => $student->name,
                    'created_by' => Auth::id(),
                    'reference_type' => 'StudentSavings',
                    'reference_id' => $student->id,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.savings.show', $student->id)
                ->with('success', 'Setor tunai tabungan sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses setoran: ' . $e->getMessage()]);
        }
    }

    /**
     * Process withdrawal from student savings.
     */
    public function withdraw(Request $request, Student $student)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'notes' => 'required|string|max:255',
        ]);

        $amount = (float) $request->amount;

        if ($student->savings_balance < $amount) {
            return back()->withErrors([
                'amount' => 'Saldo tabungan tidak mencukupi. Saldo saat ini: Rp ' . number_format($student->savings_balance, 0, ',', '.')
            ]);
        }

        DB::beginTransaction();
        try {
            $student->savings_balance -= $amount;
            $student->save();

            $refNo = 'SAV-WDR-' . time() . '-' . rand(100, 999);

            SavingsTransaction::create([
                'student_id' => $student->id,
                'transaction_type' => 'withdraw',
                'amount' => $amount,
                'balance_after' => $student->savings_balance,
                'reference_no' => $refNo,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('admin.savings.show', $student->id)
                ->with('success', 'Penarikan tabungan sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses penarikan: ' . $e->getMessage()]);
        }
    }

    /**
     * Print savings passbook / mutasi report.
     */
    public function printPassbook(Student $student)
    {
        $student->load(['user', 'schoolClass']);
        $transactions = SavingsTransaction::with('creator')
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return view('admin.savings.print', compact('student', 'transactions'));
    }
}
