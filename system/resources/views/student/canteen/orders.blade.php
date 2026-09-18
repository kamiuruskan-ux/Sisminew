@extends('layouts.student-mobile')

@section('title', 'Riwayat Pesanan E-Kantin')
@section('header_title', 'Riwayat Pesanan')

@section('content')
<div class="space-y-5 pb-28 lg:pb-10">

    <!-- Top Navigation Bar & Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.canteen.index') }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold shadow-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span>Kembali ke E-Kantin</span>
        </a>

        <a href="{{ route('student.canteen.index') }}" class="inline-flex items-center space-x-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-xs transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>Pesan Lagi</span>
        </a>
    </div>

    <!-- Header Section -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white leading-tight">Riwayat Transaksi E-Kantin</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar transaksi dan status pengambilan makanan di kantin sekolah.</p>
        </div>
        <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 text-xs font-extrabold rounded-xl border border-indigo-100 dark:border-indigo-900/50">
            Total {{ $orders->total() }} Transaksi
        </span>
    </div>

    <!-- Orders Cards List -->
    @if($orders->count() > 0)
        <div class="space-y-3.5">
            @foreach($orders as $order)
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-xs hover:border-indigo-300 dark:hover:border-indigo-700 transition-all space-y-3">
                    
                    <!-- Card Top Header: Order Number & Badges -->
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-mono font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg border border-indigo-100 dark:border-indigo-900">
                                {{ $order->order_number }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-medium">
                                {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <div class="flex items-center space-x-1.5">
                            {!! $order->order_status_badge !!}
                            {!! $order->payment_status_badge !!}
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-900 dark:text-white line-clamp-1">
                                {{ $order->items->pluck('item_name')->implode(', ') }}
                            </span>
                            <span class="text-slate-500 dark:text-slate-400 shrink-0 font-medium text-[11px] ml-2">
                                {{ $order->items->count() }} Menu
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Metode Bayar: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $order->payment_method === 'savings_balance' ? 'Saldo Tabungan' : ($order->payment_method === 'qris' ? 'QRIS / QR Code' : 'Tunai Kasir') }}</span>
                        </p>
                    </div>

                    <!-- Card Footer: Total Amount & Action Button -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Total Pembayaran</span>
                            <span class="text-sm sm:text-base font-extrabold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>

                        <a href="{{ route('student.canteen.order', $order->id) }}" 
                           class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-all active:scale-95">
                            <span>Detail & QR Pay</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach

            <!-- Pagination Links -->
            <div class="mt-6 flex justify-center">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-8 sm:p-12 text-center border border-slate-200 dark:border-slate-800 space-y-3">
            <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h4 class="text-sm font-bold text-slate-900 dark:text-white">Belum Ada Transaksi</h4>
            <p class="text-xs text-slate-500 dark:text-slate-400">Anda belum memiliki riwayat pemesanan makanan atau minuman di E-Kantin.</p>
            <a href="{{ route('student.canteen.index') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all">
                <span>Mulai Belanja di E-Kantin</span>
            </a>
        </div>
    @endif
</div>
@endsection
