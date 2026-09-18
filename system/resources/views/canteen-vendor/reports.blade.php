@extends('layouts.canteen-vendor')

@section('title', 'Laporan & Grafik Penjualan')
@section('header_title', 'Laporan Penjualan')

@section('content')
<div class="space-y-4 sm:space-y-6 pb-24 w-full">

    <!-- Top Header & Date Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white">Laporan & Analytics Penjualan</h2>
                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400">Pantau omset, grafik tren transaksi, dan statistik menu terlaris.</p>
            </div>

            <!-- Export Buttons -->
            <div class="flex items-center space-x-2 shrink-0">
                <a href="{{ route('canteen.vendor.reports.export-csv', ['period' => $period, 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="px-3 py-2 sm:px-3.5 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl sm:rounded-2xl shadow-sm transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Export CSV</span>
                </a>
                <a href="{{ route('canteen.vendor.reports.print', ['period' => $period, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl sm:rounded-2xl transition-all flex items-center space-x-1.5 no-print">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak</span>
                </a>
            </div>
        </div>

        <!-- Filter Period Buttons & Date Picker -->
        <form method="GET" action="{{ route('canteen.vendor.reports') }}" x-data="{ periodMode: '{{ $period }}' }" class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
            
            <div class="space-y-2.5">
                <!-- Period Pills (Responsive 2x2 Grid on Mobile, Flex on Desktop) -->
                <div class="grid grid-cols-2 sm:flex sm:items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl text-xs font-bold gap-1 sm:space-x-1">
                    <button type="submit" name="period" value="daily" @click="periodMode = 'daily'" class="w-full sm:w-auto px-3 py-2 rounded-xl transition-all text-center {{ $period === 'daily' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Hari Ini
                    </button>
                    <button type="submit" name="period" value="weekly" @click="periodMode = 'weekly'" class="w-full sm:w-auto px-3 py-2 rounded-xl transition-all text-center {{ $period === 'weekly' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Minggu Ini
                    </button>
                    <button type="submit" name="period" value="monthly" @click="periodMode = 'monthly'" class="w-full sm:w-auto px-3 py-2 rounded-xl transition-all text-center {{ $period === 'monthly' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Bulan Ini
                    </button>
                    <button type="button" @click="periodMode = 'custom'" class="w-full sm:w-auto px-3 py-2 rounded-xl transition-all text-center {{ $period === 'custom' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        Custom Tanggal
                    </button>
                </div>

                <!-- Selected Date Info Badge -->
                <div class="flex items-center justify-between sm:justify-start space-x-2 bg-indigo-50/60 dark:bg-indigo-950/40 px-3.5 py-2 rounded-2xl border border-indigo-100 dark:border-indigo-900/40 text-[11px] font-bold text-indigo-700 dark:text-indigo-300">
                    <div class="flex items-center space-x-1.5 min-w-0">
                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="truncate">Periode Laporan:</span>
                    </div>
                    <span class="font-extrabold text-slate-900 dark:text-white shrink-0">{{ $start->translatedFormat('d M Y') }} - {{ $end->translatedFormat('d M Y') }}</span>
                </div>
            </div>

            <!-- Custom Date Range Controls -->
            <input type="hidden" name="period" :value="periodMode">
            <div x-show="periodMode === 'custom'" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-3.5 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900">
                <div>
                    <label class="block text-[11px] font-bold text-indigo-900 dark:text-indigo-200 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate ?? $start->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-indigo-200 dark:border-indigo-800 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-indigo-900 dark:text-indigo-200 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate ?? $end->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-indigo-200 dark:border-indigo-800 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition-all flex items-center justify-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Terapkan Rentang Tanggal</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Key Metrics Cards (4 Grid) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <!-- Total Omset -->
        <div class="bg-emerald-50/60 dark:bg-emerald-950/30 p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl border border-emerald-200/80 dark:border-emerald-900/60 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-[9px] sm:text-xs font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider block truncate">Total Omset</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-black text-xs shrink-0">
                    Rp
                </div>
            </div>
            <span class="text-sm sm:text-xl font-black text-emerald-600 dark:text-emerald-400 block truncate">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </span>
        </div>

        <!-- Total Pesanan -->
        <div class="bg-indigo-50/60 dark:bg-indigo-950/30 p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl border border-indigo-200/80 dark:border-indigo-900/60 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-[9px] sm:text-xs font-bold text-indigo-800 dark:text-indigo-300 uppercase tracking-wider block truncate">Pesanan Selesai</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-black text-xs shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
            <span class="text-sm sm:text-xl font-black text-indigo-600 dark:text-indigo-400 block truncate">
                {{ number_format($totalOrders, 0, ',', '.') }} <span class="text-[10px] sm:text-xs text-indigo-500/80 font-bold">Order</span>
            </span>
        </div>

        <!-- Total Porsi Terjual -->
        <div class="bg-amber-50/60 dark:bg-amber-950/30 p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl border border-amber-200/80 dark:border-amber-900/60 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-[9px] sm:text-xs font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider block truncate">Porsi Terjual</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-amber-500/20 text-amber-700 dark:text-amber-300 flex items-center justify-center font-black text-xs shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <span class="text-sm sm:text-xl font-black text-amber-600 dark:text-amber-400 block truncate">
                {{ number_format($totalItemsSold, 0, ',', '.') }} <span class="text-[10px] sm:text-xs text-amber-500/80 font-bold">Porsi</span>
            </span>
        </div>

        <!-- Rata-rata Pesanan -->
        <div class="bg-purple-50/60 dark:bg-purple-950/30 p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl border border-purple-200/80 dark:border-purple-900/60 shadow-xs space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-[9px] sm:text-xs font-bold text-purple-800 dark:text-purple-300 uppercase tracking-wider block truncate">Rata-rata Order</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-purple-500/20 text-purple-700 dark:text-purple-300 flex items-center justify-center font-black text-xs shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
            <span class="text-sm sm:text-xl font-black text-purple-600 dark:text-purple-400 block truncate">
                Rp {{ number_format($avgOrderValue, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Chart & Analytics Section -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 gap-2">
            <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">Grafik Tren Omset Penjualan (Rp)</h3>
                <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">Visualisasi pendapatan berdasarkan periode terpilih.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[9px] sm:text-[10px] font-extrabold bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 shrink-0">
                Live Data
            </span>
        </div>

        <div class="h-56 sm:h-72 w-full">
            <canvas id="salesRevenueChart"></canvas>
        </div>
    </div>

    <!-- Top Selling Menu & Payment Method Breakdown (2 Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4">
        
        <!-- Top 5 Menu Terlaris -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3.5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">Top 5 Menu Terlaris</h3>
                <span class="text-[10px] font-bold text-slate-400">Paling Banyak Dipesan</span>
            </div>

            @if(count($topSellingItems) > 0)
                <div class="space-y-2.5">
                    @foreach($topSellingItems as $index => $topItem)
                        <div class="flex items-center justify-between p-2.5 sm:p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 gap-2">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-xs">
                                    #{{ $index + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $topItem->item_name }}</h4>
                                    <span class="text-[10px] text-slate-400 font-medium block truncate">Omset: Rp {{ number_format($topItem->total_sales, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] sm:text-[11px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 shrink-0">
                                {{ number_format($topItem->total_qty) }} Porsi
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center text-xs text-slate-400">
                    Belum ada data penjualan pada periode ini.
                </div>
            @endif
        </div>

        <!-- Payment Method Summary -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3.5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">Metode Pembayaran</h3>
                <span class="text-[10px] font-bold text-slate-400">Persentase Omset</span>
            </div>

            <div class="space-y-3">
                <!-- QRIS / Scan QR Code -->
                @php
                    $qrisAmount = $paymentBreakdown['qris'] ?? 0;
                    $qrisPercent = $totalRevenue > 0 ? round(($qrisAmount / $totalRevenue) * 100, 1) : 0;
                @endphp
                <div class="p-3 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900 space-y-1.5">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span class="text-indigo-700 dark:text-indigo-300 flex items-center space-x-1.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Scan QR Transaksi (QRIS)</span>
                        </span>
                        <span class="text-indigo-900 dark:text-white font-extrabold">Rp {{ number_format($qrisAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full h-2 bg-indigo-200 dark:bg-indigo-900 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $qrisPercent }}%"></div>
                    </div>
                    <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold block text-right">{{ $qrisPercent }}% dari total omset</span>
                </div>

                <!-- Tunai / Cash -->
                @php
                    $cashAmount = $paymentBreakdown['cash'] ?? 0;
                    $cashPercent = $totalRevenue > 0 ? round(($cashAmount / $totalRevenue) * 100, 1) : 0;
                @endphp
                <div class="p-3 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900 space-y-1.5">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span class="text-emerald-700 dark:text-emerald-300 flex items-center space-x-1.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Tunai (Cash)</span>
                        </span>
                        <span class="text-emerald-900 dark:text-white font-extrabold">Rp {{ number_format($cashAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full h-2 bg-emerald-200 dark:bg-emerald-900 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-600 rounded-full" style="width: {{ $cashPercent }}%"></div>
                    </div>
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold block text-right">{{ $cashPercent }}% dari total omset</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Completed Orders Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-3.5 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">Rincian Transaksi Selesai</h3>
                <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">Daftar transaksi pesanan siswa pada periode laporan ini.</p>
            </div>
        </div>

        @if($reportOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-3 px-4 whitespace-nowrap">No. Pesanan</th>
                            <th class="py-3 px-4 whitespace-nowrap">Tanggal & Waktu</th>
                            <th class="py-3 px-4 whitespace-nowrap">Pemesan</th>
                            <th class="py-3 px-4 whitespace-nowrap">Menu Items</th>
                            <th class="py-3 px-4 whitespace-nowrap">Metode Pembayaran</th>
                            <th class="py-3 px-4 text-right whitespace-nowrap">Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($reportOrders as $order)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                    {{ $order->order_number }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
                                    {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                    {{ $order->student?->user?->name ?? 'Pelanggan POS' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                    <span class="line-clamp-1 font-medium">
                                        {{ $order->items->map(fn($item) => $item->item_name . ' (' . $item->quantity . 'x)')->implode(', ') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $order->payment_method === 'qris' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                        {{ strtoupper($order->payment_method ?? 'TUNAI') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900 dark:text-white whitespace-nowrap">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex justify-center">
                {{ $reportOrders->links() }}
            </div>
        @else
            <div class="p-8 text-center text-xs text-slate-400">
                Tidak ada transaksi selesai pada periode tanggal ini.
            </div>
        @endif
    </div>

</div>

<!-- Chart.js Script Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesRevenueChart').getContext('2d');
        
        const labels = @js($chartLabels);
        const revenueData = @js($chartRevenueData);
        const orderData = @js($chartOrderData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pendapatan (Rp)',
                        data: revenueData,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#6366f1',
                        pointRadius: 4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Jumlah Transaksi',
                        data: orderData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderWidth: 2,
                        type: 'bar',
                        borderRadius: 6,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            font: { size: 10 }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            stepSize: 1,
                            font: { size: 10 }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 11, weight: 'bold' } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.dataset.yAxisID === 'y') {
                                    label += 'Rp ' + context.raw.toLocaleString('id-ID');
                                } else {
                                    label += context.raw + ' Transaksi';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
