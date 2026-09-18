@extends('layouts.canteen-vendor')

@section('title', 'Dashboard Vendor Kantin')
@section('header_title', $stall->name)

@section('content')
<div x-data="{ selectedOrder: null }" class="space-y-5 sm:space-y-6 pb-24 w-full">

    <!-- Store Profile Banner & Quick Status Switch -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl text-white p-4 sm:p-6 border border-indigo-500/20 shadow-xl" style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#6366f1') }} 0%, {{ Setting::get('secondary_color', '#4f46e5') }} 100%);">
        <!-- Background Pattern -->
        <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full -translate-y-12 translate-x-12 blur-sm pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-8 -translate-x-8 pointer-events-none"></div>

        <!-- Buka/Tutup Stand Button (Top Right Icon Only) -->
        <div class="absolute top-2.5 right-2.5 sm:top-3.5 sm:right-3.5 z-20">
            <form method="POST" action="{{ route('canteen.vendor.toggle-status') }}">
                @csrf
                <button type="submit" 
                        class="w-7 h-7 sm:w-8.5 sm:h-8.5 rounded-full flex items-center justify-center transition-all shadow-md {{ $stall->is_active ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}"
                        title="{{ $stall->is_active ? 'Tutup Stand Sementara' : 'Buka Stand Sekarang' }}">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9"/>
                    </svg>
                </button>
            </form>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10 sm:pr-12">
            <div class="flex items-center space-x-3 sm:space-x-4 min-w-0 pr-10 sm:pr-0">
                <img src="{{ $stall->logo_url }}" alt="{{ $stall->name }}" class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl object-cover border-2 border-white/30 shadow-lg shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-sm sm:text-lg font-black truncate leading-tight">{{ $stall->name }}</h2>
                        <span class="px-2 py-0.5 text-[8px] sm:text-[10px] font-extrabold {{ $stall->is_open_now ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }} rounded-full shadow-xs shrink-0">
                            {{ $stall->is_open_now ? 'BUKA' : 'TUTUP' }}
                        </span>
                    </div>
                    <p class="text-[10px] sm:text-xs text-white/70 truncate mt-1">{{ $stall->operating_hours }}</p>
                </div>
            </div>

            <!-- Right Actions (Balance Wallet & Transactions today) -->
            <div class="flex flex-row items-center gap-2 sm:gap-3.5 w-full sm:w-auto shrink-0 mt-1 sm:mt-0">
                <!-- Today's Completed Orders Stat -->
                <div class="flex-1 sm:flex-initial flex items-center space-x-2 bg-white/10 backdrop-blur-md rounded-2xl px-2.5 py-1.5 sm:px-3.5 sm:py-2 border border-white/10 shadow-inner min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/15 flex items-center justify-center shrink-0 text-white">
                        <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="text-left min-w-0">
                        <span class="text-[8px] sm:text-[9px] text-white/70 font-bold uppercase tracking-wider block leading-none">Trx Selesai</span>
                        <span class="text-[11px] sm:text-xs font-black text-white mt-1 block leading-none truncate">{{ $statusCounts['completed'] ?? 0 }} Trx</span>
                    </div>
                </div>

                <!-- Canteen Wallet/Balance Display -->
                <a href="{{ route('canteen.vendor.finance') }}" class="flex-1 sm:flex-initial flex items-center space-x-2 bg-white/10 backdrop-blur-md rounded-2xl px-2.5 py-1.5 sm:px-3.5 sm:py-2 border border-white/10 shadow-inner hover:bg-white/15 transition-all min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/15 flex items-center justify-center shrink-0 text-white">
                        <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-left min-w-0">
                        <span class="text-[8px] sm:text-[9px] text-white/70 font-bold uppercase tracking-wider block leading-none">Saldo Kantin</span>
                        <span class="text-[11px] sm:text-xs font-black text-white mt-1 block leading-none truncate">Rp {{ number_format($stall->balance, 0, ',', '.') }}</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards (4 Grid) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Today Revenue -->
        <div class="bg-emerald-500/5 dark:bg-emerald-500/10 rounded-3xl p-4 sm:p-5 border border-emerald-500/20 dark:border-emerald-500/20 shadow-xs relative overflow-hidden flex flex-col justify-between transition-all hover:border-emerald-500 hover:shadow-md">
            <div class="flex items-center justify-between w-full">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">Omset Hari Ini</span>
                    <span class="text-sm sm:text-lg lg:text-xl font-black text-slate-900 dark:text-white block truncate mt-1">
                        Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                    </span>
                </div>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-xs border border-emerald-100/50 dark:border-emerald-900/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-blue-500/5 dark:bg-blue-500/10 rounded-3xl p-4 sm:p-5 border border-blue-500/20 dark:border-blue-500/20 shadow-xs relative overflow-hidden flex flex-col justify-between transition-all hover:border-blue-500 hover:shadow-md">
            <div class="flex items-center justify-between w-full">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">Omset Bulan Ini</span>
                    <span class="text-sm sm:text-lg lg:text-xl font-black text-slate-900 dark:text-white block truncate mt-1">
                        Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}
                    </span>
                </div>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-xs border border-blue-100/50 dark:border-blue-900/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>

        <!-- Active Orders -->
        <div class="bg-amber-500/5 dark:bg-amber-500/10 rounded-3xl p-4 sm:p-5 border border-amber-500/20 dark:border-amber-500/20 shadow-xs relative overflow-hidden flex flex-col justify-between transition-all hover:border-amber-500 hover:shadow-md">
            <div class="flex items-center justify-between w-full">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">Pesanan Aktif</span>
                    <span class="text-sm sm:text-lg lg:text-xl font-black text-slate-900 dark:text-white block truncate mt-1">
                        {{ $pendingOrdersCount }} <span class="text-[10px] sm:text-xs text-slate-450 dark:text-slate-500 font-bold">Antrean</span>
                    </span>
                </div>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-xs border border-amber-100/50 dark:border-amber-900/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Total Menu Items -->
        <div class="bg-purple-500/5 dark:bg-purple-500/10 rounded-3xl p-4 sm:p-5 border border-purple-500/20 dark:border-purple-500/20 shadow-xs relative overflow-hidden flex flex-col justify-between transition-all hover:border-purple-500 hover:shadow-md">
            <div class="flex items-center justify-between w-full">
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider block">Total Produk</span>
                    <span class="text-sm sm:text-lg lg:text-xl font-black text-slate-900 dark:text-white block truncate mt-1">
                        {{ $totalProductsCount }} <span class="text-[10px] sm:text-xs text-slate-450 dark:text-slate-500 font-bold">Produk</span>
                    </span>
                </div>
                <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 shadow-xs border border-purple-100/50 dark:border-purple-900/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Orders Breakdown Summary Badges -->
    <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
        <!-- Menunggu -->
        <div class="p-3 sm:p-4 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/30 rounded-2xl shadow-xs text-center flex flex-col justify-center items-center">
            <span class="text-[10px] sm:text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Menunggu</span>
            <span class="text-lg sm:text-2xl font-black text-amber-700 dark:text-amber-300 mt-0.5">{{ $statusCounts['pending'] }}</span>
        </div>

        <!-- Diproses -->
        <div class="p-3 sm:p-4 bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200/60 dark:border-blue-900/30 rounded-2xl shadow-xs text-center flex flex-col justify-center items-center">
            <span class="text-[10px] sm:text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Diproses</span>
            <span class="text-lg sm:text-2xl font-black text-blue-700 dark:text-blue-300 mt-0.5">{{ $statusCounts['processing'] }}</span>
        </div>

        <!-- Siap Diambil -->
        <div class="p-3 sm:p-4 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-900/30 rounded-2xl shadow-xs text-center flex flex-col justify-center items-center">
            <span class="text-[10px] sm:text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Siap Diambil</span>
            <span class="text-lg sm:text-2xl font-black text-emerald-700 dark:text-emerald-300 mt-0.5">{{ $statusCounts['ready'] }}</span>
        </div>
    </div>

    <!-- Quick Management Shortcut Badges -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 sm:gap-3">
        <a href="{{ route('canteen.vendor.pos') }}" class="p-3 bg-primary text-white rounded-2xl shadow-md flex items-center space-x-2.5 hover:opacity-90 transition-all col-span-2 sm:col-span-1">
            <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center shrink-0 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-xs font-extrabold text-white block truncate">Kasir POS</span>
                <span class="text-[10px] text-white/80 block truncate">Scan QR / Tunai</span>
            </div>
        </a>

        <a href="{{ route('canteen.vendor.products') }}" class="p-3 bg-yellow-500/5 dark:bg-yellow-500/10 rounded-2xl border border-yellow-500/20 dark:border-yellow-500/20 shadow-xs flex items-center space-x-2.5 hover:border-yellow-500 hover:bg-yellow-500/10 transition-all">
            <div class="w-8 h-8 rounded-xl bg-yellow-50 dark:bg-yellow-950/60 text-yellow-600 dark:text-yellow-400 flex items-center justify-center shrink-0 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-xs font-extrabold text-slate-900 dark:text-white block truncate">Kelola Produk</span>
                <span class="text-[10px] text-slate-400 block truncate">Atur daftar menu</span>
            </div>
        </a>

        <a href="{{ route('canteen.vendor.categories') }}" class="p-3 bg-cyan-500/5 dark:bg-cyan-500/10 rounded-2xl border border-cyan-500/20 dark:border-cyan-500/20 shadow-xs flex items-center space-x-2.5 hover:border-cyan-500 hover:bg-cyan-500/10 transition-all">
            <div class="w-8 h-8 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-xs font-extrabold text-slate-900 dark:text-white block truncate">Kategori Menu</span>
                <span class="text-[10px] text-slate-400 block truncate">Kelola jenis menu</span>
            </div>
        </a>

        <a href="{{ route('canteen.vendor.stock') }}" class="p-3 bg-purple-500/5 dark:bg-purple-500/10 rounded-2xl border border-purple-500/20 dark:border-purple-500/20 shadow-xs flex items-center space-x-2.5 hover:border-purple-500 hover:bg-purple-500/10 transition-all">
            <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-xs font-extrabold text-slate-900 dark:text-white block truncate">Manajemen Stok</span>
                <span class="text-[10px] text-slate-400 block truncate">Atur porsi produk</span>
            </div>
        </a>

        <a href="{{ route('canteen.vendor.reports') }}" class="p-3 bg-emerald-500/5 dark:bg-emerald-500/10 rounded-2xl border border-emerald-500/20 dark:border-emerald-500/20 shadow-xs flex items-center space-x-2.5 hover:border-emerald-500 hover:bg-emerald-500/10 transition-all">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-xs font-extrabold text-slate-900 dark:text-white block truncate">Laporan &amp; Grafik</span>
                <span class="text-[10px] text-slate-400 block truncate">Analisis penjualan</span>
            </div>
        </a>

        <a href="{{ route('canteen.vendor.profile') }}" class="p-3 bg-pink-500/5 dark:bg-pink-500/10 rounded-2xl border border-pink-500/20 dark:border-pink-500/20 shadow-xs flex items-center space-x-2.5 hover:border-pink-500 hover:bg-pink-500/10 transition-all col-span-2 sm:col-span-1">
            <div class="w-8 h-8 rounded-xl bg-pink-50 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center shrink-0 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div class="min-w-0">
                <span class="text-xs font-extrabold text-slate-900 dark:text-white block truncate">Profil Stand</span>
                <span class="text-[10px] text-slate-400 block truncate">Pengaturan toko</span>
            </div>
        </a>
    </div>

    <!-- Recent Orders Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-2 px-1">
            <div class="min-w-0 flex-1">
                <h3 class="text-xs sm:text-base font-extrabold text-slate-900 dark:text-white truncate">Pesanan Masuk Terbaru</h3>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 leading-tight truncate">Pantau & ubah status pesanan siswa secara real-time.</p>
            </div>
            <a href="{{ route('canteen.vendor.orders') }}" class="shrink-0 px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-xl bg-primary/10 hover:bg-primary/20 text-[11px] sm:text-xs text-primary font-extrabold flex items-center space-x-1 transition-all">
                <span>Lihat Semua</span>
                <span>&rarr;</span>
            </a>
        </div>

        @if($recentOrders->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                @foreach($recentOrders as $order)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3 flex flex-col justify-between">
                        
                        <!-- Header Card: Order Number & Badges -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 border-b border-slate-100 dark:border-slate-800 pb-2.5">
                            <div class="flex items-center justify-between sm:justify-start space-x-2">
                                <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-[11px] sm:text-xs truncate max-w-[170px] sm:max-w-none">{{ $order->order_number }}</span>
                                <span class="text-[10px] text-slate-400 font-medium shrink-0">{{ $order->created_at->format('H:i') }} WIB</span>
                            </div>
                            <div class="flex items-center space-x-1 shrink-0">
                                {!! $order->order_status_badge !!}
                                {!! $order->payment_status_badge !!}
                            </div>
                        </div>

                        <!-- Customer & Items Brief -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                    {{ $order->student?->user?->name ?? 'Pelanggan POS' }}
                                </h4>
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg shrink-0">
                                    {{ $order->student?->class?->name ?? 'Umum' }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2">
                                <span class="font-bold text-slate-700 dark:text-slate-200">Menu:</span> 
                                {{ $order->items->map(fn($i) => $i->quantity . 'x ' . $i->item_name)->implode(', ') }}
                            </p>

                            @if($order->notes)
                                <div class="text-[11px] p-2 bg-amber-500/10 border border-amber-500/20 text-amber-800 dark:text-amber-200 rounded-xl font-medium line-clamp-1">
                                    <span class="font-bold">Catatan:</span> {{ $order->notes }}
                                </div>
                            @endif
                        </div>

                        <!-- Footer & Status Actions -->
                        <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 dark:border-slate-800 gap-2">
                            <div class="min-w-0">
                                <span class="text-[9px] sm:text-[10px] text-slate-400 uppercase font-bold block leading-none mb-1">Total Bayar</span>
                                <span class="text-xs sm:text-sm font-black text-emerald-600 dark:text-emerald-400 block truncate">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center space-x-1.5 shrink-0">
                                <!-- Detail Modal Opener -->
                                <button type="button" 
                                        @click="selectedOrder = {{ json_encode([
                                            'order_number' => $order->order_number,
                                            'student_name' => $order->student?->user?->name ?? 'Pelanggan POS',
                                            'student_class' => $order->student?->class?->name ?? '-',
                                            'total_amount' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                                            'payment_method' => $order->payment_method === 'savings_balance' ? 'Saldo Tabungan' : strtoupper($order->payment_method ?? 'TUNAI'),
                                            'notes' => $order->notes ?? '-',
                                            'items' => $order->items->map(fn($i) => [
                                                'name' => $i->item_name,
                                                'qty' => $i->quantity,
                                                'price' => 'Rp ' . number_format($i->price, 0, ',', '.'),
                                                'subtotal' => 'Rp ' . number_format($i->subtotal, 0, ',', '.')
                                            ])
                                        ]) }}" 
                                        class="px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-[11px] sm:text-xs rounded-xl hover:bg-slate-200 transition-all">
                                    Detail
                                </button>

                                <!-- Status Form Button -->
                                <form method="POST" action="{{ route('canteen.vendor.orders.update-status', $order->id) }}">
                                    @csrf
                                    @if($order->order_status === 'pending')
                                        <input type="hidden" name="order_status" value="processing">
                                        <button type="submit" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] sm:text-xs rounded-xl shadow-xs transition-all flex items-center space-x-1">
                                            <span>Mulai Proses</span>
                                        </button>
                                    @elseif($order->order_status === 'processing')
                                        <input type="hidden" name="order_status" value="ready">
                                        <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] sm:text-xs rounded-xl shadow-xs transition-all flex items-center space-x-1">
                                            <span>Siap Diambil</span>
                                        </button>
                                    @elseif($order->order_status === 'ready')
                                        <input type="hidden" name="order_status" value="completed">
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] sm:text-xs rounded-xl shadow-xs transition-all flex items-center space-x-1">
                                            <span>Selesai</span>
                                        </button>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] sm:text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-400 font-bold rounded-xl uppercase">Selesai</span>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 text-center border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 mx-auto flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Pesanan Masuk Hari Ini</p>
                <p class="text-[11px] text-slate-400">Pesanan dari aplikasi siswa akan muncul di sini secara otomatis.</p>
            </div>
        @endif
    </div>

    <!-- Order Detail Modal -->
    <div x-show="selectedOrder !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="selectedOrder = null"></div>
        <div class="relative transform rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl transition-all w-full max-w-md border border-slate-200 dark:border-slate-800 space-y-4 my-auto z-10 max-h-[90vh] overflow-y-auto scrollbar-none">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Detail Pesanan</h3>
                        <span class="font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400" x-text="selectedOrder?.order_number"></span>
                    </div>
                    <button type="button" @click="selectedOrder = null" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <template x-if="selectedOrder">
                    <div class="space-y-4 text-xs">
                        <div class="bg-slate-50 dark:bg-slate-800 p-3 rounded-2xl space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pemesan:</span>
                                <span class="font-bold text-slate-900 dark:text-white" x-text="selectedOrder.student_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Kelas:</span>
                                <span class="font-bold text-slate-900 dark:text-white" x-text="selectedOrder.student_class"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pembayaran:</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="selectedOrder.payment_method"></span>
                            </div>
                        </div>

                        <div>
                            <span class="font-bold text-slate-700 dark:text-slate-300 block mb-2">Item Menu Yang Dipesan:</span>
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                <template x-for="item in selectedOrder.items" :key="item.name">
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                        <div>
                                            <span class="font-bold text-slate-900 dark:text-white block" x-text="item.name"></span>
                                            <span class="text-[11px] text-slate-400" x-text="item.qty + ' x ' + item.price"></span>
                                        </div>
                                        <span class="font-black text-slate-900 dark:text-white" x-text="item.subtotal"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                            <span class="font-extrabold text-slate-700 dark:text-slate-300">Total Pembayaran:</span>
                            <span class="text-base font-black text-emerald-600 dark:text-emerald-400" x-text="selectedOrder.total_amount"></span>
                        </div>
                    </div>
                </template>

                <div class="pt-2 flex justify-end">
                    <button type="button" @click="selectedOrder = null" class="w-full py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-2xl text-xs">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
