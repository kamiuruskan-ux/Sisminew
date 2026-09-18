<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BankAccountController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.settings', ['tab' => 'bank_account']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_name'    => 'required|string|max:255',
            'account_number'  => 'nullable|string|max:100',
            'bank_name'       => 'required|string|max:100',
            'type'            => 'required|in:bank,cash',
            'initial_balance' => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'qr_code'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'is_active'       => 'nullable|boolean',
        ]);

        $validated['current_balance'] = $validated['initial_balance'];
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('qr_code')) {
            $savedQr = save_uploaded_public_file($request->file('qr_code'), 'img/bank_qr');
            $validated['qr_code'] = 'bank_qr/' . basename($savedQr);
        } else {
            unset($validated['qr_code']);
        }

        BankAccount::create($validated);

        return back()->with('success', 'Rekening / Kas Sekolah berhasil ditambahkan.');
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'account_name'    => 'required|string|max:255',
            'account_number'  => 'nullable|string|max:100',
            'bank_name'       => 'required|string|max:100',
            'type'            => 'required|in:bank,cash',
            'initial_balance' => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'qr_code'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'is_active'       => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('qr_code')) {
            // Hapus file lama jika ada
            if ($bankAccount->qr_code) {
                delete_public_file($bankAccount->qr_code, 'img/bank_qr');
            }
            $savedQr = save_uploaded_public_file($request->file('qr_code'), 'img/bank_qr');
            $validated['qr_code'] = 'bank_qr/' . basename($savedQr);
        } else {
            unset($validated['qr_code']);
        }

        // Opsi hapus QR code yang sudah ada
        if ($request->boolean('remove_qr_code')) {
            if ($bankAccount->qr_code) {
                delete_public_file($bankAccount->qr_code, 'img/bank_qr');
            }
            $validated['qr_code'] = null;
        }

        $bankAccount->update($validated);
        $bankAccount->recalculateBalance();

        return back()->with('success', 'Rekening / Kas Sekolah berhasil diperbarui.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        if ($bankAccount->financialTransactions()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus rekening yang sudah memiliki histori transaksi.');
        }

        if ($bankAccount->qr_code) {
            delete_public_file($bankAccount->qr_code, 'img/bank_qr');
        }

        $bankAccount->delete();

        return back()->with('success', 'Rekening / Kas Sekolah berhasil dihapus.');
    }
}
