@extends('layouts.admin')

@section('title', 'Sistem Penilaian Kinerja Guru & Karyawan (KPI)')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .kpi-card-gradient {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);
    }
    .kpi-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #3c50e0;
        cursor: pointer;
        border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
</style>
@endpush

@section('content')
<div x-data="kpiDashboard()" x-init="initDashboard()" class="space-y-6">
    
    <!-- Top Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20V10"></path>
                        <path d="M18 20V4"></path>
                        <path d="M6 20v-4"></path>
                    </svg>
                </span>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Penilaian Kinerja Guru &amp; Pegawai (KPI)
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Evaluasi komprehensif 5 Pilar Kompetensi Standar Rapor Terpadu
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('admin.kpi.index') }}" class="flex flex-wrap items-center gap-2">
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
            </form>

            <!-- Export Excel Button -->
            <a href="{{ route('admin.kpi.export', ['year' => $year, 'month' => $month]) }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 rounded-xl hover:bg-emerald-100 transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Excel (.xlsx)</span>
            </a>

            @if(auth()->user()->hasRole(['super-admin', 'admin', 'operator', 'kepala-sekolah']))
            <!-- Bobot KPI Settings Button -->
            <button @click="openSettingsModal = true" 
                    type="button" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 rounded-xl hover:bg-indigo-100 transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Konfigurasi Bobot</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Rerata Skor Sekolah -->
        <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Rerata Skor KPI</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $averageScore }} <span class="text-xs font-normal text-slate-400">/ 100</span></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-lg">
                    @if($averageScore >= 91) A @elseif($averageScore >= 76) B @elseif($averageScore >= 61) C @else D @endif
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
                <span class="font-bold text-slate-700 dark:text-slate-300">{{ count($kpiList) }}</span> Guru &amp; Pegawai dievaluasi
            </div>
        </div>

        <!-- Distribusi Predikat -->
        <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Distribusi Predikat</p>
            <div class="grid grid-cols-4 gap-1 text-center">
                <div class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800">
                    <span class="text-[10px] font-bold text-emerald-600 block">A (Mumtaz)</span>
                    <span class="text-sm font-black text-emerald-700 dark:text-emerald-300">{{ $predicateCounts['A'] ?? 0 }}</span>
                </div>
                <div class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800">
                    <span class="text-[10px] font-bold text-indigo-600 block">B (Baik)</span>
                    <span class="text-sm font-black text-indigo-700 dark:text-indigo-300">{{ $predicateCounts['B'] ?? 0 }}</span>
                </div>
                <div class="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800">
                    <span class="text-[10px] font-bold text-amber-600 block">C (Cukup)</span>
                    <span class="text-sm font-black text-amber-700 dark:text-amber-300">{{ $predicateCounts['C'] ?? 0 }}</span>
                </div>
                <div class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800">
                    <span class="text-[10px] font-bold text-rose-600 block">D (Kurang)</span>
                    <span class="text-sm font-black text-rose-700 dark:text-rose-300">{{ $predicateCounts['D'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Gate Kehadiran Rule -->
        <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Lolos Gate Presensi</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $gatePassRate }}%</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">
                Threshold batas minimum: <strong class="text-slate-700 dark:text-slate-300">{{ $settings['gate_threshold'] }}%</strong> kehadiran
            </p>
        </div>

        <!-- Rerata Amalan Yaumiyah / Tarbiyah -->
        <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Komp. Tarbiyah &amp; Adab</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $componentAverages['comp_4'] }} <span class="text-xs font-normal text-slate-400">pts</span></h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/70 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">
                Terhubung otomatis dari log Mutabaah &amp; Kajian
            </p>
        </div>
    </div>

    <!-- 5 Pillars Average Breakdown Bar -->
    <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <h2 class="text-sm font-extrabold text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                Rata-rata Nilai Sekolah per 5 Pilar Kompetensi
            </h2>
            <span class="text-xs text-slate-400">Bobot dikonfigurasi dinamis</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <!-- Pilar 1 -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-bold text-slate-700 dark:text-slate-300">1. Disiplin &amp; Presensi</span>
                    <span class="font-black text-indigo-600 dark:text-indigo-400">{{ $componentAverages['comp_1'] }}</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                    <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $componentAverages['comp_1']) }}%"></div>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Bobot: {{ $settings['weight_comp_1'] }}%</span>
            </div>

            <!-- Pilar 2 -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-bold text-slate-700 dark:text-slate-300">2. Pedagogik / KBM</span>
                    <span class="font-black text-emerald-600 dark:text-emerald-400">{{ $componentAverages['comp_2'] }}</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $componentAverages['comp_2']) }}%"></div>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Bobot: {{ $settings['weight_comp_2'] }}%</span>
            </div>

            <!-- Pilar 3 -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-bold text-slate-700 dark:text-slate-300">3. Profesional &amp; LMS</span>
                    <span class="font-black text-amber-600 dark:text-amber-400">{{ $componentAverages['comp_3'] }}</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                    <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $componentAverages['comp_3']) }}%"></div>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Bobot: {{ $settings['weight_comp_3'] }}%</span>
            </div>

            <!-- Pilar 4 -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-bold text-slate-700 dark:text-slate-300">4. Tarbiyah &amp; Adab</span>
                    <span class="font-black text-sky-600 dark:text-sky-400">{{ $componentAverages['comp_4'] }}</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                    <div class="bg-sky-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $componentAverages['comp_4']) }}%"></div>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Bobot: {{ $settings['weight_comp_4'] }}%</span>
            </div>

            <!-- Pilar 5 -->
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-bold text-slate-700 dark:text-slate-300">5. Sosial &amp; Tim</span>
                    <span class="font-black text-purple-600 dark:text-purple-400">{{ $componentAverages['comp_5'] }}</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                    <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $componentAverages['comp_5']) }}%"></div>
                </div>
                <span class="text-[10px] text-slate-400 mt-1 block">Bobot: {{ $settings['weight_comp_5'] }}%</span>
            </div>
        </div>
    </div>

    <!-- Leaderboard Top 5 Guru Kinerja Terbaik -->
    @if(count($topPerformers) > 0)
    <div class="bg-gradient-to-r from-indigo-900 via-slate-900 to-indigo-950 p-6 rounded-2xl text-white shadow-md relative overflow-hidden">
        <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-10 -translate-y-10">
            <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2">
                    <span class="p-1.5 rounded-lg bg-amber-400/20 text-amber-300">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </span>
                    <h2 class="text-sm font-extrabold uppercase tracking-widest text-white">Leaderboard Guru &amp; Pegawai Teladan</h2>
                </div>
                <span class="text-xs text-indigo-200">Periode: {{ $months[$month] ?? '' }} {{ $year }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                @foreach($topPerformers as $idx => $top)
                <div class="bg-white/10 backdrop-blur-xs rounded-xl p-3.5 border border-white/15 hover:bg-white/15 transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black {{ $idx === 0 ? 'bg-amber-400 text-slate-900' : ($idx === 1 ? 'bg-slate-300 text-slate-900' : ($idx === 2 ? 'bg-amber-700 text-white' : 'bg-white/20 text-white')) }}">
                            #{{ $idx + 1 }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $top['predicate'] === 'A' ? 'bg-emerald-500/80 text-white' : 'bg-indigo-400/80 text-white' }}">
                            Predikat {{ $top['predicate'] }}
                        </span>
                    </div>
                    <p class="font-bold text-sm text-white truncate">{{ $top['user']->name }}</p>
                    <p class="text-[11px] text-indigo-200 truncate mt-0.5">{{ $top['user']->jabatan ?? ($top['user']->roles->first()->name ?? 'Guru') }}</p>
                    <div class="mt-2.5 pt-2 border-t border-white/10 flex items-center justify-between text-xs">
                        <span class="text-indigo-200">Skor Akhir:</span>
                        <span class="font-black text-amber-300 text-base">{{ $top['final_score'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Main Teachers Table Section -->
    <div class="bg-white dark:bg-[#1C2434] rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs overflow-hidden">
        
        <!-- Table Search & Filters -->
        <div class="p-5 border-b border-slate-100 dark:border-[#2E3A47] flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="flex items-center space-x-2">
                <h3 class="font-extrabold text-slate-800 dark:text-white text-base">Daftar Penilaian Pegawai</h3>
                <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    {{ count($kpiList) }} Orang
                </span>
            </div>

            <!-- Search input -->
            <form method="GET" action="{{ route('admin.kpi.index') }}" class="w-full md:w-auto flex items-center gap-2">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="month" value="{{ $month }}">
                <div class="relative w-full md:w-64">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari nama / NIP..." 
                           class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 pl-8 pr-3 py-2 focus:ring-1 focus:ring-primary focus:border-primary">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-3 py-2 rounded-xl bg-slate-800 dark:bg-slate-700 text-white text-xs font-bold hover:bg-slate-700">Filter</button>
            </form>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-[#2E3A47] text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama Pegawai &amp; Jabatan</th>
                        <th class="py-3.5 px-3 text-center">Presensi &amp; Gate</th>
                        <th class="py-3.5 px-3 text-center">1. Disiplin</th>
                        <th class="py-3.5 px-3 text-center">2. Pedagogik</th>
                        <th class="py-3.5 px-3 text-center">3. Profesional</th>
                        <th class="py-3.5 px-3 text-center">4. Tarbiyah</th>
                        <th class="py-3.5 px-3 text-center">5. Sosial</th>
                        <th class="py-3.5 px-3 text-center">Skor Akhir</th>
                        <th class="py-3.5 px-3 text-center">Predikat</th>
                        <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#2E3A47] text-xs">
                    @forelse($kpiList as $index => $item)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                        
                        <!-- Teacher Info -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-bold flex items-center justify-center border border-indigo-200/50 dark:border-indigo-800/50 shrink-0 overflow-hidden">
                                    @if(!empty($item['user']->avatar_url))
                                        <img src="{{ $item['user']->avatar_url }}" alt="{{ $item['user']->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span>{{ strtoupper(substr($item['user']->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div class="overflow-hidden">
                                    <p class="font-bold text-slate-800 dark:text-white truncate hover:text-primary transition-colors">
                                        {{ $item['user']->name }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 truncate">
                                        {{ $item['user']->nip ? 'NIP: ' . $item['user']->nip : ($item['user']->jabatan ?? ($item['user']->roles->first()->name ?? 'Guru')) }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- Presensi & Gate -->
                        <td class="py-3.5 px-3 text-center">
                            <div class="flex flex-col items-center">
                                <span class="font-black text-slate-800 dark:text-slate-200">{{ $item['attendance_percentage'] }}%</span>
                                @if($item['gate_passed'])
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 mt-0.5">
                                        Lolos
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-rose-100 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 mt-0.5" title="Di bawah threshold 85%">
                                        Terkunci C
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Comp 1 -->
                        <td class="py-3.5 px-3 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ $item['score_comp_1'] }}
                        </td>

                        <!-- Comp 2 -->
                        <td class="py-3.5 px-3 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ $item['score_comp_2'] }}
                        </td>

                        <!-- Comp 3 -->
                        <td class="py-3.5 px-3 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ $item['score_comp_3'] }}
                        </td>

                        <!-- Comp 4 -->
                        <td class="py-3.5 px-3 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ $item['score_comp_4'] }}
                        </td>

                        <!-- Comp 5 -->
                        <td class="py-3.5 px-3 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ $item['score_comp_5'] }}
                        </td>

                        <!-- Skor Akhir -->
                        <td class="py-3.5 px-3 text-center">
                            <span class="text-sm font-black text-indigo-600 dark:text-indigo-400">
                                {{ $item['final_score'] }}
                            </span>
                        </td>

                        <!-- Predikat -->
                        <td class="py-3.5 px-3 text-center">
                            @php
                                $badgeClass = match($item['predicate']) {
                                    'A' => 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border-emerald-300/50',
                                    'B' => 'bg-indigo-100 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 border-indigo-300/50',
                                    'C' => 'bg-amber-100 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border-amber-300/50',
                                    'D' => 'bg-rose-100 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 border-rose-300/50',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-black border {{ $badgeClass }}">
                                {{ $item['predicate'] }}
                            </span>
                        </td>

                        <!-- Aksi Buttons -->
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center space-x-1.5">
                                <!-- Evaluasi Supervisi Button -->
                                @if(auth()->user()->hasRole(['super-admin', 'admin', 'operator', 'kepala-sekolah', 'wakasek-kurikulum']))
                                <button @click="openEvaluationModal({{ $item['user']->id }})" 
                                        type="button" 
                                        class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400 hover:bg-indigo-100 transition-colors"
                                        title="Input / Edit Evaluasi Supervisi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @endif

                                <!-- Rapor Detail Page -->
                                <a href="{{ route('admin.kpi.raport', ['userId' => $item['user']->id, 'year' => $year, 'month' => $month]) }}" 
                                   class="p-1.5 rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200 transition-colors"
                                   title="Buka Rapor Kinerja Lengkap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                <!-- Print A4 Button -->
                                <a href="{{ route('admin.kpi.raport.print', ['userId' => $item['user']->id, 'year' => $year, 'month' => $month]) }}" 
                                   target="_blank" 
                                   class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400 hover:bg-emerald-100 transition-colors"
                                   title="Cetak Rapor Resmi (A4/Kop)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="py-10 text-center text-slate-400">
                            Tidak ada data pegawai yang ditemukan untuk periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL INPUT EVALUASI KEPALA SEKOLAH / SUPERVISOR                          -->
    <!-- ========================================================================= -->
    <div x-show="showEvalModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="showEvalModal = false" 
             class="bg-white dark:bg-[#1C2434] w-full max-w-4xl rounded-2xl border border-slate-200 dark:border-[#2E3A47] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 dark:border-[#2E3A47] flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40">
                <div class="flex items-center space-x-3">
                    <span class="p-2 rounded-xl bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white" x-text="'Evaluasi Supervisi: ' + evalData.user.name"></h3>
                        <p class="text-xs text-slate-500" x-text="'Periode: {{ $months[$month] ?? '' }} {{ $year }} • ' + (evalData.user.jabatan || 'Guru')"></p>
                    </div>
                </div>
                <button @click="showEvalModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body (Tabs of 5 Pillars) -->
            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                
                <!-- Live Score Summary Banner -->
                <div class="p-4 rounded-xl bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex flex-wrap items-center justify-between gap-4 shadow-sm">
                    <div>
                        <span class="text-xs text-indigo-300 font-bold uppercase tracking-wider block">Skor Akhir Terkalkulasi</span>
                        <div class="flex items-baseline space-x-2 mt-0.5">
                            <span class="text-3xl font-black text-amber-300" x-text="computedFinalScore"></span>
                            <span class="text-xs text-slate-400">/ 100</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-black uppercase ml-2" 
                                  :class="computedPredicate === 'A' ? 'bg-emerald-500 text-white' : (computedPredicate === 'B' ? 'bg-indigo-500 text-white' : 'bg-amber-500 text-slate-900')"
                                  x-text="'Predikat ' + computedPredicate"></span>
                        </div>
                    </div>
                    <div class="text-right text-xs">
                        <span class="text-slate-300">Gate Presensi:</span>
                        <span class="font-bold ml-1" :class="evalData.kpi.gate_passed ? 'text-emerald-400' : 'text-rose-400'" 
                              x-text="evalData.kpi.attendance_percentage + '% (' + (evalData.kpi.gate_passed ? 'Lolos' : 'Di Bawah 85%') + ')'"></span>
                        <p class="text-[11px] text-slate-400 mt-1">Status: <span class="text-indigo-200 font-semibold" x-text="evalData.kpi.is_capped ? 'Terkunci C/D' : 'Reguler'"></span></p>
                    </div>
                </div>

                <!-- Form Inputs -->
                <form id="evalForm" @submit.prevent="submitEvaluation()">
                    
                    <!-- 5 Accordion Panels for Pillars -->
                    <div class="space-y-4">
                        
                        <!-- PILAR 1: DISIPLIN & KEHADIRAN (AUTO) -->
                        <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                            <div class="bg-slate-50 dark:bg-slate-800/80 px-4 py-3 flex items-center justify-between border-b border-slate-200 dark:border-slate-700">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                    <h4 class="font-extrabold text-xs uppercase tracking-wider text-slate-800 dark:text-white">
                                        Pilar 1: Disiplin &amp; Kehadiran Kerja (Bobot 20% + Gate)
                                    </h4>
                                </div>
                                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400" x-text="'Skor: ' + evalData.kpi.score_comp_1"></span>
                            </div>
                            <div class="p-4 space-y-3 bg-white dark:bg-[#1C2434] text-xs">
                                <template x-for="(item, code) in (evalData.definitions.comp_1?.indicators || {})" :key="code">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/60">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="'IND ' + code + ': ' + item.name"></span>
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 uppercase">Otomatis</span>
                                            </div>
                                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="evalData.kpi.items[code]?.source_detail || item.desc"></p>
                                        </div>
                                        <div class="flex items-center space-x-2 shrink-0">
                                            <span class="font-black text-sm text-indigo-600 dark:text-indigo-400" x-text="evalData.kpi.items[code]?.score || 0"></span>
                                            <span class="text-slate-400 text-xs">pts</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- PILAR 2: PEDAGOGIK / PEMBELAJARAN (SUPERVISI KS + AUTO JURNAL) -->
                        <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                            <div class="bg-slate-50 dark:bg-slate-800/80 px-4 py-3 flex items-center justify-between border-b border-slate-200 dark:border-slate-700">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <h4 class="font-extrabold text-xs uppercase tracking-wider text-slate-800 dark:text-white">
                                        Pilar 2: Kompetensi Pedagogik &amp; Pengajaran (Bobot 45%)
                                    </h4>
                                </div>
                                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400" x-text="'Rerata Pilar: ' + computedPillarScore('comp_2')"></span>
                            </div>
                            <div class="p-4 space-y-3 bg-white dark:bg-[#1C2434] text-xs">
                                <template x-for="(item, code) in (evalData.definitions.comp_2?.indicators || {})" :key="code">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <div class="max-w-md">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="'IND ' + code + ': ' + item.name"></span>
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold"
                                                      :class="item.type === 'auto' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300'"
                                                      x-text="item.type === 'auto' ? 'Otomatis' : 'Supervisi'"></span>
                                            </div>
                                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="evalData.kpi.items[code]?.source_detail || item.desc"></p>
                                        </div>

                                        <div class="flex items-center space-x-3 shrink-0">
                                            <template x-if="item.type === 'auto'">
                                                <div class="text-right">
                                                    <span class="font-black text-sm text-indigo-600 dark:text-indigo-400" x-text="evalFormItems[code]?.score || 0"></span>
                                                    <span class="text-slate-400 text-xs">pts</span>
                                                </div>
                                            </template>
                                            <template x-if="item.type !== 'auto'">
                                                <div class="flex items-center space-x-2">
                                                    <input type="range" min="50" max="100" step="1" 
                                                           x-model.number="evalFormItems[code].score" 
                                                           @input="recalculateScores()"
                                                           class="w-28 kpi-slider accent-primary">
                                                    <input type="number" min="0" max="100" 
                                                           x-model.number="evalFormItems[code].score" 
                                                           @input="recalculateScores()"
                                                           class="w-16 text-center font-bold text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-1">
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- PILAR 3: PROFESIONAL & PENGEMBANGAN DIRI -->
                        <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                            <div class="bg-slate-50 dark:bg-slate-800/80 px-4 py-3 flex items-center justify-between border-b border-slate-200 dark:border-slate-700">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <h4 class="font-extrabold text-xs uppercase tracking-wider text-slate-800 dark:text-white">
                                        Pilar 3: Kompetensi Profesional &amp; Pengembangan Diri (Bobot 20%)
                                    </h4>
                                </div>
                                <span class="text-xs font-black text-amber-600 dark:text-amber-400" x-text="'Rerata Pilar: ' + computedPillarScore('comp_3')"></span>
                            </div>
                            <div class="p-4 space-y-3 bg-white dark:bg-[#1C2434] text-xs">
                                <template x-for="(item, code) in (evalData.definitions.comp_3?.indicators || {})" :key="code">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <div class="max-w-md">
                                            <span class="font-bold text-slate-800 dark:text-slate-200" x-text="'IND ' + code + ': ' + item.name"></span>
                                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="item.desc"></p>
                                        </div>
                                        <div class="flex items-center space-x-2 shrink-0">
                                            <input type="range" min="50" max="100" step="1" 
                                                   x-model.number="evalFormItems[code].score" 
                                                   @input="recalculateScores()"
                                                   class="w-28 kpi-slider accent-primary">
                                            <input type="number" min="0" max="100" 
                                                   x-model.number="evalFormItems[code].score" 
                                                   @input="recalculateScores()"
                                                   class="w-16 text-center font-bold text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-1">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- PILAR 4: KEPRIBADIAN & NILAI TARBIYAH -->
                        <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                            <div class="bg-slate-50 dark:bg-slate-800/80 px-4 py-3 flex items-center justify-between border-b border-slate-200 dark:border-slate-700">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                    <h4 class="font-extrabold text-xs uppercase tracking-wider text-slate-800 dark:text-white">
                                        Pilar 4: Kepribadian &amp; Nilai Tarbiyah (Bobot 20%)
                                    </h4>
                                </div>
                                <span class="text-xs font-black text-sky-600 dark:text-sky-400" x-text="'Rerata Pilar: ' + computedPillarScore('comp_4')"></span>
                            </div>
                            <div class="p-4 space-y-3 bg-white dark:bg-[#1C2434] text-xs">
                                <template x-for="(item, code) in (evalData.definitions.comp_4?.indicators || {})" :key="code">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <div class="max-w-md">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="'IND ' + code + ': ' + item.name"></span>
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-extrabold"
                                                      :class="item.type === 'auto' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300'"
                                                      x-text="item.type === 'auto' ? 'Log Mutabaah/Kajian' : 'Survei/Halaqah'"></span>
                                            </div>
                                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="evalData.kpi.items[code]?.source_detail || item.desc"></p>
                                        </div>
                                        <div class="flex items-center space-x-2 shrink-0">
                                            <template x-if="item.type === 'auto'">
                                                <div class="text-right">
                                                    <span class="font-black text-sm text-sky-600 dark:text-sky-400" x-text="evalFormItems[code]?.score || 0"></span>
                                                    <span class="text-slate-400 text-xs">pts</span>
                                                </div>
                                            </template>
                                            <template x-if="item.type !== 'auto'">
                                                <div class="flex items-center space-x-2">
                                                    <input type="range" min="50" max="100" step="1" 
                                                           x-model.number="evalFormItems[code].score" 
                                                           @input="recalculateScores()"
                                                           class="w-28 kpi-slider accent-primary">
                                                    <input type="number" min="0" max="100" 
                                                           x-model.number="evalFormItems[code].score" 
                                                           @input="recalculateScores()"
                                                           class="w-16 text-center font-bold text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-1">
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- PILAR 5: SOSIAL & KOLABORASI -->
                        <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden">
                            <div class="bg-slate-50 dark:bg-slate-800/80 px-4 py-3 flex items-center justify-between border-b border-slate-200 dark:border-slate-700">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                    <h4 class="font-extrabold text-xs uppercase tracking-wider text-slate-800 dark:text-white">
                                        Pilar 5: Kompetensi Sosial &amp; Kolaborasi (Bobot 15%)
                                    </h4>
                                </div>
                                <span class="text-xs font-black text-purple-600 dark:text-purple-400" x-text="'Rerata Pilar: ' + computedPillarScore('comp_5')"></span>
                            </div>
                            <div class="p-4 space-y-3 bg-white dark:bg-[#1C2434] text-xs">
                                <template x-for="(item, code) in (evalData.definitions.comp_5?.indicators || {})" :key="code">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <div class="max-w-md">
                                            <span class="font-bold text-slate-800 dark:text-slate-200" x-text="'IND ' + code + ': ' + item.name"></span>
                                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="item.desc"></p>
                                        </div>
                                        <div class="flex items-center space-x-2 shrink-0">
                                            <input type="range" min="50" max="100" step="1" 
                                                   x-model.number="evalFormItems[code].score" 
                                                   @input="recalculateScores()"
                                                   class="w-28 kpi-slider accent-primary">
                                            <input type="number" min="0" max="100" 
                                                   x-model.number="evalFormItems[code].score" 
                                                   @input="recalculateScores()"
                                                   class="w-16 text-center font-bold text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-1">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- FEEDBACK & REKOMENDASI KEPALA SEKOLAH -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3">
                            <h4 class="font-extrabold text-xs uppercase tracking-wider text-slate-800 dark:text-white">
                                Catatan Kualitatif Kepala Sekolah / Supervisor
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Apresiasi &amp; Keunggulan:</label>
                                    <textarea x-model="feedbackAppreciation" rows="3" class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-700 dark:text-slate-200" placeholder="Tuliskan apresiasi pencapaian..."></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rekomendasi Perbaikan &amp; Pembinaan:</label>
                                    <textarea x-model="feedbackImprovement" rows="3" class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-slate-700 dark:text-slate-200" placeholder="Tuliskan rekomendasi perbaikan..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Footer -->
                    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end space-x-3">
                        <button type="button" @click="showEvalModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSaving" class="px-5 py-2 rounded-xl bg-primary hover:bg-opacity-90 text-white text-xs font-bold shadow-md flex items-center gap-1.5">
                            <svg x-show="isSaving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Simpan Hasil Evaluasi KPI</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL KONFIGURASI BOBOT KPI & GATE THRESHOLD (ADMIN)                      -->
    <!-- ========================================================================= -->
    <div x-show="openSettingsModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div @click.away="openSettingsModal = false" 
             class="bg-white dark:bg-[#1C2434] w-full max-w-lg rounded-2xl border border-slate-200 dark:border-[#2E3A47] shadow-2xl overflow-hidden">
            
            <div class="px-6 py-4 border-b border-slate-100 dark:border-[#2E3A47] flex items-center justify-between bg-slate-50 dark:bg-slate-800/40">
                <div class="flex items-center space-x-2">
                    <span class="p-1.5 rounded-lg bg-indigo-100 dark:bg-indigo-950 text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    </span>
                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Konfigurasi Bobot KPI &amp; Gate Presensi</h3>
                </div>
                <button @click="openSettingsModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.kpi.update-settings') }}" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">1. Bobot Disiplin &amp; Kehadiran Kerja (%)</label>
                    <input type="number" step="0.5" name="weight_comp_1" value="{{ $settings['weight_comp_1'] }}" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">2. Bobot Pedagogik / Pengajaran (%)</label>
                    <input type="number" step="0.5" name="weight_comp_2" value="{{ $settings['weight_comp_2'] }}" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">3. Bobot Profesional &amp; LMS (%)</label>
                    <input type="number" step="0.5" name="weight_comp_3" value="{{ $settings['weight_comp_3'] }}" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">4. Bobot Kepribadian &amp; Nilai Tarbiyah (%)</label>
                    <input type="number" step="0.5" name="weight_comp_4" value="{{ $settings['weight_comp_4'] }}" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">5. Bobot Kompetensi Sosial &amp; Kolaborasi (%)</label>
                    <input type="number" step="0.5" name="weight_comp_5" value="{{ $settings['weight_comp_5'] }}" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2">
                </div>

                <div class="pt-3 border-t border-slate-200 dark:border-slate-700 grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Gate Threshold Kehadiran (%)</label>
                        <input type="number" step="1" min="50" max="100" name="gate_threshold" value="{{ $settings['gate_threshold'] }}" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2">
                        <p class="text-[10px] text-slate-400 mt-0.5">Jika di bawah nilai ini, skor otomatis dikunci predikat C/D.</p>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Pinalti Keterlambatan (%/mnt)</label>
                        <input type="number" step="0.1" min="0" max="5" name="late_penalty_rate" value="{{ $settings['late_penalty_rate'] }}" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2">
                        <p class="text-[10px] text-slate-400 mt-0.5">Potongan skor per menit telat presensi pagi WITA.</p>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-t border-slate-200 dark:border-slate-700 flex justify-end space-x-2">
                    <button type="button" @click="openSettingsModal = false" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white font-bold">Simpan Konfigurasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function kpiDashboard() {
    return {
        showEvalModal: false,
        openSettingsModal: false,
        isSaving: false,
        currentUserId: null,
        evalData: {
            user: {},
            kpi: { items: {}, weights: {} },
            definitions: {}
        },
        evalFormItems: {},
        feedbackAppreciation: '',
        feedbackImprovement: '',
        computedFinalScore: 0,
        computedPredicate: 'C',

        initDashboard() {
            // Initial setup
        },

        async openEvaluationModal(userId) {
            this.currentUserId = userId;
            this.showEvalModal = true;
            try {
                const res = await fetch(`{{ url('admin/kpi/evaluation-data') }}/${userId}?year={{ $year }}&month={{ $month }}`);
                const data = await res.json();
                if (data.success) {
                    this.evalData = data;
                    this.feedbackAppreciation = data.kpi.feedback_appreciation || '';
                    this.feedbackImprovement = data.kpi.feedback_improvement || '';
                    
                    // Initialize editable form items
                    this.evalFormItems = {};
                    for (const [code, item] of Object.entries(data.kpi.items)) {
                        this.evalFormItems[code] = {
                            score: item.score,
                            notes: item.notes || ''
                        };
                    }
                    this.recalculateScores();
                }
            } catch (err) {
                console.error("Gagal memuat data evaluasi:", err);
            }
        },

        computedPillarScore(pillarKey) {
            const indicators = this.evalData.definitions[pillarKey]?.indicators || {};
            const codes = Object.keys(indicators);
            if (codes.length === 0) return 0;

            let sum = 0;
            for (const code of codes) {
                sum += Number(this.evalFormItems[code]?.score || 0);
            }
            return (sum / codes.length).toFixed(2);
        },

        recalculateScores() {
            const comp1 = Number(this.computedPillarScore('comp_1'));
            const comp2 = Number(this.computedPillarScore('comp_2'));
            const comp3 = Number(this.computedPillarScore('comp_3'));
            const comp4 = Number(this.computedPillarScore('comp_4'));
            const comp5 = Number(this.computedPillarScore('comp_5'));

            const weights = this.evalData.kpi.weights || { comp_1: 20, comp_2: 35, comp_3: 15, comp_4: 15, comp_5: 15 };
            const totalW = (weights.comp_1 || 20) + (weights.comp_2 || 35) + (weights.comp_3 || 15) + (weights.comp_4 || 15) + (weights.comp_5 || 15);

            let score = ((comp1 * weights.comp_1) + (comp2 * weights.comp_2) + (comp3 * weights.comp_3) + (comp4 * weights.comp_4) + (comp5 * weights.comp_5)) / totalW;
            this.computedFinalScore = Number(score.toFixed(2));

            // Predicate
            let pred = 'D';
            if (this.computedFinalScore >= 91.0) pred = 'A';
            else if (this.computedFinalScore >= 76.0) pred = 'B';
            else if (this.computedFinalScore >= 61.0) pred = 'C';

            // Gate checking
            if (!this.evalData.kpi.gate_passed) {
                if (pred === 'A' || pred === 'B') {
                    pred = 'C';
                }
            }
            this.computedPredicate = pred;
        },

        async submitEvaluation() {
            this.isSaving = true;
            try {
                const payload = {
                    _token: '{{ csrf_token() }}',
                    user_id: this.currentUserId,
                    period_year: {{ $year }},
                    period_month: {{ $month }},
                    items: this.evalFormItems,
                    feedback_appreciation: this.feedbackAppreciation,
                    feedback_improvement: this.feedbackImprovement,
                };

                const res = await fetch(`{{ route('admin.kpi.save-evaluation') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await res.json();
                if (result.success) {
                    this.showEvalModal = false;
                    window.location.reload();
                } else {
                    alert(result.message || 'Gagal menyimpan evaluasi');
                }
            } catch (err) {
                console.error("Gagal simpan:", err);
                alert("Terjadi kesalahan sistem saat menyimpan evaluasi.");
            } finally {
                this.isSaving = false;
            }
        }
    };
}
</script>
@endpush
@endsection
