@extends('layouts.admin')

@section('title', 'Laporan Penjualan E-Kantin')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Laporan Penjualan Kantin</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Rekapitulasi omset, statistik transaksi, dan menu terlaris kantin sekolah.</p>
        </div>

        <a href="{{ route('admin.canteen.reports.print') }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl shadow-xs self-start sm:self-auto transition-all">
            Cetak Laporan
        </a>
    </div>

    <!-- Revenue Metrics Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="p-6 rounded-3xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-lg space-y-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-100">Pendapatan Hari Ini</span>
            <h3 class="text-2xl font-black">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-emerald-200">{{ $todayOrders }} Transaksi Berhasil Hari Ini</p>
        </div>

        <div class="p-6 rounded-3xl bg-gradient-to-r from-indigo-600 to-blue-700 text-white shadow-lg space-y-1">
            <span class="text-xs font-semibold uppercase tracking-wider text-indigo-100">Pendapatan Bulan Ini</span>
            <h3 class="text-2xl font-black">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-indigo-200">Total Omset {{ now()->translatedFormat('F Y') }}</p>
        </div>

        <div class="p-6 rounded-3xl bg-slate-900 text-white shadow-lg space-y-1 border border-slate-800">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Menu Paling Laris</span>
            <h3 class="text-lg font-extrabold truncate text-cyan-300">{{ $topItems->first()?->item_name ?? '-' }}</h3>
            <p class="text-[11px] text-slate-400">{{ $topItems->first()?->total_qty ?? 0 }} porsi terjual</p>
        </div>
    </div>

    <!-- Top Selling Products & Recent Orders Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Top Selling Table (Col 5) -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">5 Menu Terlaris Kantin</h3>
            
            <div class="space-y-3">
                @forelse($topItems as $idx => $ti)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 dark:bg-slate-850 text-xs">
                        <div class="flex items-center space-x-3">
                            <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-black text-xs flex items-center justify-center">
                                {{ $idx + 1 }}
                            </span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $ti->item_name }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-indigo-600 dark:text-indigo-400 block">{{ $ti->total_qty }} Porsi</span>
                            <span class="text-[10px] text-slate-400">Rp {{ number_format($ti->total_sales, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada data penjualan.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Paid Orders List (Col 7) -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Transaksi Terakhir Lunas</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-850 text-slate-500 uppercase font-extrabold text-[10px] border-b border-slate-200 dark:border-slate-800">
                            <th class="py-3 px-3">Order ID</th>
                            <th class="py-3 px-3">Pembeli</th>
                            <th class="py-3 px-3">Metode</th>
                            <th class="py-3 px-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($recentPaidOrders as $rpo)
                            <tr>
                                <td class="py-3 px-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $rpo->order_number }}</td>
                                <td class="py-3 px-3 font-bold text-slate-900 dark:text-white">{{ $rpo->student?->user?->name ?? 'Walk-in / Umum' }}</td>
                                <td class="py-3 px-3 uppercase text-[10px] text-slate-500 font-mono">{{ $rpo->payment_method }}</td>
                                <td class="py-3 px-3 text-right font-extrabold text-slate-900 dark:text-white">Rp {{ number_format($rpo->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">Belum ada transaksi lunas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
