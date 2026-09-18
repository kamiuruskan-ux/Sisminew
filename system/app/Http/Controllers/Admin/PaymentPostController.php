<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentPostController extends Controller
{
    public function index()
    {
        $paymentPosts = PaymentPost::withCount('paymentBills')->latest()->get();
        return view('admin.payment-posts.index', compact('paymentPosts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:payment_posts,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['code'] = strtoupper(Str::slug($validated['code']));

        PaymentPost::create($validated);

        return back()->with('success', 'Pos Pembayaran berhasil ditambahkan.');
    }

    public function update(Request $request, PaymentPost $paymentPost)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:payment_posts,code,' . $paymentPost->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['code'] = strtoupper(Str::slug($validated['code']));

        $paymentPost->update($validated);

        return back()->with('success', 'Pos Pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentPost $paymentPost)
    {
        if ($paymentPost->paymentBills()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus pos pembayaran yang sudah memiliki tarif tagihan.');
        }

        $paymentPost->delete();

        return back()->with('success', 'Pos Pembayaran berhasil dihapus.');
    }
}
