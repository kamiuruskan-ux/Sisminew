<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FinancialCategoryController extends Controller
{
    public function index()
    {
        $categories = FinancialCategory::withCount('financialTransactions')->latest()->get();
        return view('admin.financial-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:financial_categories,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:pemasukan,pengeluaran',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper(Str::slug($validated['code']));
        $validated['is_active'] = $request->has('is_active');

        FinancialCategory::create($validated);

        return back()->with('success', 'Kategori Keuangan berhasil ditambahkan.');
    }

    public function update(Request $request, FinancialCategory $financialCategory)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:financial_categories,code,' . $financialCategory->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:pemasukan,pengeluaran',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper(Str::slug($validated['code']));
        $validated['is_active'] = $request->has('is_active');

        $financialCategory->update($validated);

        return back()->with('success', 'Kategori Keuangan berhasil diperbarui.');
    }

    public function destroy(FinancialCategory $financialCategory)
    {
        if ($financialCategory->financialTransactions()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus kategori yang sudah memiliki transaksi.');
        }

        $financialCategory->delete();

        return back()->with('success', 'Kategori Keuangan berhasil dihapus.');
    }
}
