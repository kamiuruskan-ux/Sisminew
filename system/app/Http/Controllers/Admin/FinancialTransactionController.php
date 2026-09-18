<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinancialTransactionController extends Controller
{
    public function index(Request $request)
    {
        $categories = FinancialCategory::where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        $query = FinancialTransaction::with(['financialCategory', 'bankAccount', 'creator']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        if ($request->filled('financial_category_id')) {
            $query->where('financial_category_id', $request->financial_category_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(20)->withQueryString();

        $totalIncome = FinancialTransaction::when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
            $q->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        })->where('type', 'pemasukan')->sum('amount');

        $totalExpense = FinancialTransaction::when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
            $q->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        })->where('type', 'pengeluaran')->sum('amount');

        $netBalance = $totalIncome - $totalExpense;
        $totalAccountAssets = $bankAccounts->sum('current_balance');

        return view('admin.financial-transactions.index', compact(
            'transactions',
            'categories',
            'bankAccounts',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'totalAccountAssets'
        ));
    }

    public function create(Request $request)
    {
        return redirect()->route('admin.financial-transactions.index', $request->all());
    }

    public function income(Request $request)
    {
        $categories = FinancialCategory::where('type', 'pemasukan')->where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        $transactions = FinancialTransaction::with(['financialCategory', 'bankAccount'])
            ->where('type', 'pemasukan')
            ->latest('transaction_date')
            ->paginate(15);

        return view('admin.financial-transactions.income', compact('transactions', 'categories', 'bankAccounts'));
    }

    public function expense(Request $request)
    {
        $categories = FinancialCategory::where('type', 'pengeluaran')->where('is_active', true)->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        $transactions = FinancialTransaction::with(['financialCategory', 'bankAccount'])
            ->where('type', 'pengeluaran')
            ->latest('transaction_date')
            ->paginate(15);

        return view('admin.financial-transactions.expense', compact('transactions', 'categories', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:pemasukan,pengeluaran',
            'financial_category_id' => 'required|exists:financial_categories,id',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'recipient_or_payee' => 'nullable|string|max:255',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $validated['transaction_number'] = FinancialTransaction::generateTransactionNumber($validated['type']);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'xls', 'xlsx']);
            $targetSubfolder = $isDoc ? 'doc/financial_proofs' : 'img/financial_proofs';
            $savedFile = save_uploaded_public_file($file, $targetSubfolder);
            $validated['proof_file'] = ($isDoc ? 'doc/financial_proofs/' : 'financial_proofs/') . basename($savedFile);
        }

        FinancialTransaction::create($validated);

        $typeLabel = $validated['type'] === 'pemasukan' ? 'Kas Masuk (Pemasukan)' : 'Kas Keluar (Pengeluaran)';

        return back()->with('success', "Transaksi {$typeLabel} berhasil dicatat.");
    }

    public function update(Request $request, FinancialTransaction $financialTransaction)
    {
        $validated = $request->validate([
            'financial_category_id' => 'required|exists:financial_categories,id',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'recipient_or_payee' => 'nullable|string|max:255',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('proof_file')) {
            if ($financialTransaction->proof_file) {
                delete_public_file($financialTransaction->proof_file, 'img/financial_proofs');
            }
            $file = $request->file('proof_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'xls', 'xlsx']);
            $targetSubfolder = $isDoc ? 'doc/financial_proofs' : 'img/financial_proofs';
            $savedFile = save_uploaded_public_file($file, $targetSubfolder);
            $validated['proof_file'] = ($isDoc ? 'doc/financial_proofs/' : 'financial_proofs/') . basename($savedFile);
        } else {
            unset($validated['proof_file']);
        }

        $financialTransaction->update($validated);

        return back()->with('success', 'Transaksi keuangan berhasil diperbarui.');
    }

    public function destroy(FinancialTransaction $financialTransaction)
    {
        if ($financialTransaction->proof_file) {
            delete_public_file($financialTransaction->proof_file, 'img/financial_proofs');
        }

        $financialTransaction->delete();

        return back()->with('success', 'Transaksi keuangan berhasil dihapus.');
    }
}
