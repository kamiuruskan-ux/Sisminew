<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\ClassModel;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use App\Models\PaymentBill;
use App\Models\StudentPaymentBill;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->get('report_type', 'cashbook'); // cashbook, income, expense, student_bills
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $bankAccountId = $request->get('bank_account_id');
        $categoryId = $request->get('financial_category_id');
        $classId = $request->get('class_id');

        $bankAccounts = BankAccount::where('is_active', true)->get();
        $categories = FinancialCategory::where('is_active', true)->get();
        $classes = ClassModel::where('is_active', true)->get();

        // 1. Calculate Opening Balance before $startDate
        $bankAccountQuery = BankAccount::where('is_active', true);
        if ($bankAccountId) {
            $bankAccountQuery->where('id', $bankAccountId);
        }
        $initialBalance = $bankAccountQuery->sum('initial_balance');

        $priorIncomeQuery = FinancialTransaction::where('type', 'pemasukan')
            ->where('transaction_date', '<', $startDate);
        $priorExpenseQuery = FinancialTransaction::where('type', 'pengeluaran')
            ->where('transaction_date', '<', $startDate);

        if ($bankAccountId) {
            $priorIncomeQuery->where('bank_account_id', $bankAccountId);
            $priorExpenseQuery->where('bank_account_id', $bankAccountId);
        }
        if ($categoryId) {
            $priorIncomeQuery->where('financial_category_id', $categoryId);
            $priorExpenseQuery->where('financial_category_id', $categoryId);
        }

        $priorIncome = $priorIncomeQuery->sum('amount');
        $priorExpense = $priorExpenseQuery->sum('amount');

        $openingBalance = $initialBalance + $priorIncome - $priorExpense;

        // 2. Fetch Period Transactions
        $query = FinancialTransaction::with(['financialCategory', 'bankAccount', 'creator'])
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($bankAccountId) {
            $query->where('bank_account_id', $bankAccountId);
        }

        if ($categoryId) {
            $query->where('financial_category_id', $categoryId);
        }

        if ($reportType === 'income') {
            $query->where('type', 'pemasukan');
        } elseif ($reportType === 'expense') {
            $query->where('type', 'pengeluaran');
        }

        $transactions = $query->orderBy('transaction_date', 'asc')->orderBy('id', 'asc')->get();

        // 3. Calculate Running Balances & Summary Metrics
        $totalIncome = $transactions->where('type', 'pemasukan')->sum('amount');
        $totalExpense = $transactions->where('type', 'pengeluaran')->sum('amount');
        
        $runningBalance = $openingBalance;
        foreach ($transactions as $tx) {
            if ($tx->type === 'pemasukan') {
                $runningBalance += $tx->amount;
            } else {
                $runningBalance -= $tx->amount;
            }
            $tx->running_balance = $runningBalance;
        }

        $endingBalance = $runningBalance;
        $netBalance = $totalIncome - $totalExpense;

        // 4. Category Breakdown
        $categoryBreakdown = $transactions->groupBy('financial_category_id')->map(function ($items) {
            $catName = $items->first()->financialCategory->name ?? 'Lain-lain';
            $inc = $items->where('type', 'pemasukan')->sum('amount');
            $exp = $items->where('type', 'pengeluaran')->sum('amount');
            return [
                'name' => $catName,
                'income' => $inc,
                'expense' => $exp,
                'net' => $inc - $exp,
            ];
        });

        // 5. Student Bills Summary if report_type == student_bills
        $studentBills = collect();
        if ($reportType === 'student_bills') {
            $sbQuery = StudentPaymentBill::with(['student.schoolClass', 'paymentBill.paymentPost']);
            if ($classId) {
                $sbQuery->whereHas('student', function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                });
            }
            $studentBills = $sbQuery->get();
        }

        return view('admin.financial-reports.index', compact(
            'reportType',
            'startDate',
            'endDate',
            'bankAccountId',
            'categoryId',
            'classId',
            'bankAccounts',
            'categories',
            'classes',
            'transactions',
            'openingBalance',
            'totalIncome',
            'totalExpense',
            'endingBalance',
            'netBalance',
            'categoryBreakdown',
            'studentBills'
        ));
    }

    public function print(Request $request)
    {
        $reportType = $request->get('report_type', 'cashbook');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $bankAccountId = $request->get('bank_account_id');
        $categoryId = $request->get('financial_category_id');
        $classId = $request->get('class_id');

        $bankAccount = $bankAccountId ? BankAccount::find($bankAccountId) : null;
        $category = $categoryId ? FinancialCategory::find($categoryId) : null;
        $schoolClass = $classId ? ClassModel::find($classId) : null;

        // 1. Opening Balance Calculation
        $bankAccountQuery = BankAccount::where('is_active', true);
        if ($bankAccountId) {
            $bankAccountQuery->where('id', $bankAccountId);
        }
        $initialBalance = $bankAccountQuery->sum('initial_balance');

        $priorIncomeQuery = FinancialTransaction::where('type', 'pemasukan')
            ->where('transaction_date', '<', $startDate);
        $priorExpenseQuery = FinancialTransaction::where('type', 'pengeluaran')
            ->where('transaction_date', '<', $startDate);

        if ($bankAccountId) {
            $priorIncomeQuery->where('bank_account_id', $bankAccountId);
            $priorExpenseQuery->where('bank_account_id', $bankAccountId);
        }
        if ($categoryId) {
            $priorIncomeQuery->where('financial_category_id', $categoryId);
            $priorExpenseQuery->where('financial_category_id', $categoryId);
        }

        $openingBalance = $initialBalance + $priorIncomeQuery->sum('amount') - $priorExpenseQuery->sum('amount');

        // 2. Transactions Query
        $query = FinancialTransaction::with(['financialCategory', 'bankAccount'])
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($bankAccountId) {
            $query->where('bank_account_id', $bankAccountId);
        }

        if ($categoryId) {
            $query->where('financial_category_id', $categoryId);
        }

        if ($reportType === 'income') {
            $query->where('type', 'pemasukan');
        } elseif ($reportType === 'expense') {
            $query->where('type', 'pengeluaran');
        }

        $transactions = $query->orderBy('transaction_date', 'asc')->orderBy('id', 'asc')->get();

        $totalIncome = $transactions->where('type', 'pemasukan')->sum('amount');
        $totalExpense = $transactions->where('type', 'pengeluaran')->sum('amount');

        $runningBalance = $openingBalance;
        foreach ($transactions as $tx) {
            if ($tx->type === 'pemasukan') {
                $runningBalance += $tx->amount;
            } else {
                $runningBalance -= $tx->amount;
            }
            $tx->running_balance = $runningBalance;
        }

        $endingBalance = $runningBalance;

        $studentBills = collect();
        if ($reportType === 'student_bills') {
            $sbQuery = StudentPaymentBill::with(['student.schoolClass', 'paymentBill.paymentPost']);
            if ($classId) {
                $sbQuery->whereHas('student', function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                });
            }
            $studentBills = $sbQuery->get();
        }

        return view('admin.financial-reports.print', compact(
            'reportType',
            'startDate',
            'endDate',
            'bankAccount',
            'category',
            'schoolClass',
            'transactions',
            'openingBalance',
            'totalIncome',
            'totalExpense',
            'endingBalance',
            'studentBills'
        ));
    }
}
