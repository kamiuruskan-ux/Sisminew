@extends('layouts.admin')

@section('title', 'Transaksi Pembayaran Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-indigo-600/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </span>
                Pos Pembayaran Siswa
            </h1>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Pilih siswa untuk melakukan transaksi pembayaran tagihan (Bulanan & Cicilan)</p>
        </div>
        
        <!-- View Switcher -->
        <div class="inline-flex p-1.5 bg-slate-200/70 dark:bg-slate-800/80 rounded-2xl border border-slate-300 dark:border-slate-700 text-xs font-bold shadow-xs self-start md:self-auto">
            <a href="{{ route('admin.student-payments.index') }}" 
               class="px-4 py-2 rounded-xl transition-all {{ request()->routeIs('admin.student-payments.index') ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-300/50 dark:hover:bg-slate-700/50' }}">
                Daftar Siswa & Bayar
            </a>
            <a href="{{ route('admin.student-payments.manual-confirm') }}" 
               class="px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 {{ request()->routeIs('admin.student-payments.manual-confirm') ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-300/50 dark:hover:bg-slate-700/50' }}">
                <span>Konfirmasi Transfer Manual</span>
                @if(isset($pendingCount) && $pendingCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full {{ request()->routeIs('admin.student-payments.manual-confirm') ? 'bg-white text-indigo-600' : 'bg-rose-500 text-white' }} text-[9px] font-black leading-none">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.student-payments.tracking') }}" 
               class="px-4 py-2 rounded-xl transition-all {{ request()->routeIs('admin.student-payments.tracking') ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-300/50 dark:hover:bg-slate-700/50' }}">
                Matriks Tracking Multi-Tahun
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    @php
        $globalTotalTagihan = 0;
        $globalTotalBayar = 0;
        $globalTunggakan = 0;
        foreach($students as $st) {
            $gt = $st->paymentBills->sum('total_amount');
            $gb = $st->paymentBills->sum('paid_amount');
            $globalTotalTagihan += $gt;
            $globalTotalBayar += $gb;
            $globalTunggakan += ($gt - $gb);
        }
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Siswa Terfilter</p>
                <p class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ number_format($students->total()) }} Siswa</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-900/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tagihan (Halaman ini)</p>
                <p class="text-xl font-black text-slate-800 dark:text-white mt-0.5">Rp {{ number_format($globalTotalTagihan, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-900/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sudah Terbayar</p>
                <p class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">Rp {{ number_format($globalTotalBayar, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-100 dark:border-rose-900/40">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sisa Tunggakan</p>
                <p class="text-xl font-black text-rose-600 dark:text-rose-400 mt-0.5">Rp {{ number_format($globalTunggakan, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs">
        <form method="GET" action="{{ route('admin.student-payments.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="w-full sm:w-64 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    @input.debounce.400ms="$el.closest('form').submit()"
                    x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                    placeholder="Cari Nama, NISN, atau NIS..." class="w-full px-3.5 pr-8 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500">
                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-3 top-2 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                @endif
            </div>
            <x-major-class-select :selected-major="request('major_id')" :selected-class="request('class_id')" :is-filter="true" layout="inline" major-label="" class-label="" select-class="w-full sm:w-48 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-xs font-semibold text-slate-800 dark:text-white focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500" :on-major-change="'$el.closest(\'form\').submit()'" :on-class-change="'$el.closest(\'form\').submit()'" />
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-4 min-w-[120px]">NISN</th>
                        <th class="px-5 py-4 min-w-[200px]">Nama Siswa</th>
                        <th class="px-5 py-4 min-w-[120px]">Kelas</th>
                        <th class="px-5 py-4 min-w-[140px]">Total Tagihan</th>
                        <th class="px-5 py-4 min-w-[140px]">Sudah Dibayar</th>
                        <th class="px-5 py-4 min-w-[140px]">Tunggakan</th>
                        <th class="px-5 py-4 text-right min-w-[160px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                    @forelse($students as $student)
                        @php
                            $totalTagihan = $student->paymentBills->sum('total_amount');
                            $totalBayar = $student->paymentBills->sum('paid_amount');
                            $tunggakan = $totalTagihan - $totalBayar;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono text-xs font-bold border border-slate-200 dark:border-slate-700">
                                    {{ $student->nisn }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-black text-xs flex items-center justify-center shrink-0 border border-indigo-200 dark:border-indigo-800">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-900 dark:text-white block text-sm">{{ $student->name }}</span>
                                        @if($student->nis)
                                            <span class="text-[10px] text-slate-400 font-mono block">NIS: {{ $student->nis }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-semibold border border-slate-200 dark:border-slate-700">
                                    {{ $student->schoolClass ? $student->schoolClass->name : '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-700 dark:text-slate-300">
                                Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 font-bold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($totalBayar, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-black {{ $tunggakan > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    Rp {{ number_format($tunggakan, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.student-payments.pay', $student->id) }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-emerald-500/20 inline-flex items-center space-x-1.5 active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>Transaksikan / Bayar</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Tidak ada data siswa ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection

