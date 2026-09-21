@extends('layouts.admin')

@section('title', 'Rekapitulasi Mutabaah Ibadah Guru & Pegawai')

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
        <div class="flex items-center space-x-3">
            <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </span>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Rekapitulasi Mutabaah Yaumiyah Pegawai
                </h1>
                <p class="text-xs text-slate-500">
                    Monitoring kepatuhan pengisian amalan yaumiyah dan capaian ibadah guru &amp; tenaga kependidikan
                </p>
            </div>
        </div>

        <!-- Filter Period & Search -->
        <form method="GET" action="{{ route('admin.employee-mutabaah.recap') }}" class="flex flex-wrap items-center gap-2">
            <select name="month" onchange="this.form.submit()" class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2">
                @php
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                @endphp
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>

            <select name="year" onchange="this.form.submit()" class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 px-3 py-2">
                @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <div class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari guru..." 
                       class="text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2 text-slate-700 dark:text-slate-200">
            </div>
            <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold">Filter</button>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pegawai Terdata</p>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ count($recapData) }} Orang</h3>
            <p class="text-[11px] text-slate-500 mt-1">Periode: {{ $months[$month] ?? '' }} {{ $year }}</p>
        </div>

        @php
            $avgCompliance = count($recapData) > 0 ? round(array_sum(array_column($recapData, 'compliance_percent')) / count($recapData), 1) : 0;
            $avgMutabaah = count($recapData) > 0 ? round(array_sum(array_column($recapData, 'average_score')) / count($recapData), 1) : 0;
        @endphp
        <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tingkat Kepatuhan Pengisian</p>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $avgCompliance }}%</h3>
            <p class="text-[11px] text-slate-500 mt-1">Rasio hari terisi terhadap hari kalender</p>
        </div>

        <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Rerata Nilai Amalan Yaumiyah</p>
            <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $avgMutabaah }} <span class="text-xs font-normal text-slate-400">/ 100</span></h3>
            <p class="text-[11px] text-slate-500 mt-1">Standar nilai terhubung otomatis ke KPI</p>
        </div>
    </div>

    <!-- Table of Employees -->
    <div class="bg-white dark:bg-[#1C2434] rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Nama Pegawai &amp; Jabatan</th>
                        <th class="py-3 px-3 text-center">Hari Terisi</th>
                        <th class="py-3 px-3 text-center">Kepatuhan (%)</th>
                        <th class="py-3 px-3 text-center">Subuh Berjamaah</th>
                        <th class="py-3 px-3 text-center">Tahajjud &amp; Witir</th>
                        <th class="py-3 px-3 text-center">Tilawah Rerata</th>
                        <th class="py-3 px-3 text-center">Rerata Skor</th>
                        <th class="py-3 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#2E3A47]">
                    @forelse($recapData as $idx => $r)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                        <td class="py-3 px-4">
                            <p class="font-bold text-slate-800 dark:text-white">{{ $r['user']->name }}</p>
                            <p class="text-[11px] text-slate-400">{{ $r['user']->nip ? 'NIP: ' . $r['user']->nip : ($r['user']->jabatan ?? 'Guru / Pegawai') }}</p>
                        </td>
                        <td class="py-3 px-3 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ $r['days_filled'] }} / {{ $totalDaysInMonth }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            <div class="flex items-center justify-center space-x-1.5">
                                <div class="w-16 bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, $r['compliance_percent']) }}%"></div>
                                </div>
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $r['compliance_percent'] }}%</span>
                            </div>
                        </td>
                        <td class="py-3 px-3 text-center font-semibold text-slate-600 dark:text-slate-300">
                            {{ $r['subuh_percent'] }}%
                        </td>
                        <td class="py-3 px-3 text-center font-semibold text-slate-600 dark:text-slate-300">
                            {{ $r['tahajjud_percent'] }}%
                        </td>
                        <td class="py-3 px-3 text-center font-semibold text-slate-600 dark:text-slate-300">
                            {{ $r['tilawah_avg'] }} Hlm
                        </td>
                        <td class="py-3 px-3 text-center font-black text-sm text-indigo-600 dark:text-indigo-400">
                            {{ $r['average_score'] }}
                        </td>
                        <td class="py-3 px-3 text-center">
                            @if($r['compliance_percent'] >= 80)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                    Disiplin
                                </span>
                            @elseif($r['compliance_percent'] >= 50)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                                    Cukup
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                    Kurang
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada data pegawai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
