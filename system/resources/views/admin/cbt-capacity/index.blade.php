@extends('layouts.admin')

@section('title', 'Uji Kekuatan & Performa Server CBT')

@section('content')
<div class="space-y-6" x-data="cbtSimulator()">
    <!-- Page Header & Action Navigation -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.exams.index') }}" class="hover:text-indigo-600 transition-colors">Ujian CBT</a>
                <span>/</span>
                <span class="text-slate-900 dark:text-white">Uji Kekuatan Server</span>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-xs shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                Analisis & Uji Kekuatan Server CBT
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Pemeriksaan otomatis daya tahan server sekolah saat siswa mengerjakan ujian online secara serentak.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.cbt-capacity.index') }}" class="px-4 py-2.5 bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl text-xs transition-all border border-slate-200 dark:border-slate-700 shadow-2xs flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Cek Ulang Kesehatan
            </a>
            <a href="{{ route('admin.exams.index') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-all flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Kelola Ujian
            </a>
        </div>
    </div>

    <!-- 1. Top Metric Cards Grid (Clean Enterprise Dashboard Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Status Server -->
        <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-3 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Server Ujian</span>
                @if($capacityAnalysis['status_grade'] === 'success')
                    <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                @elseif($capacityAnalysis['status_grade'] === 'warning')
                    <span class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                @else
                    <span class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                @endif
            </div>
            <div>
                <div class="text-2xl font-black tracking-tight {{ $capacityAnalysis['status_grade'] === 'success' ? 'text-emerald-600 dark:text-emerald-400' : ($capacityAnalysis['status_grade'] === 'warning' ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                    @if($capacityAnalysis['status_grade'] === 'success')
                        SANGAT AMAN
                    @elseif($capacityAnalysis['status_grade'] === 'warning')
                        OPTIMAL BERTAHAP
                    @else
                        TERBATAS
                    @endif
                </div>
                <div class="text-[11px] text-slate-400 mt-1 font-medium">
                    Sanggup {{ number_format($capacityAnalysis['burst_capacity'], 0, ',', '.') }} siswa ujian serentak.
                </div>
            </div>
        </div>

        <!-- Metric 2: Batas Maksimal Siswa Ujian -->
        <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-3 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Batas Maksimal Siswa</span>
                <span class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
            </div>
            <div>
                <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">
                    {{ number_format($capacityAnalysis['safe_staggered_capacity'], 0, ',', '.') }} Siswa
                </div>
                <div class="text-[11px] text-slate-400 mt-1 font-medium">
                    Alur pengerjaan soal menyebar lancar (1-2 Menit).
                </div>
            </div>
        </div>

        <!-- Metric 3: Trafik Padat (5 Dtk) -->
        <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-3 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Trafik Padat (Spike 5 Dtk)</span>
                <span class="p-2 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ number_format($capacityAnalysis['burst_capacity'], 0, ',', '.') }} Siswa
                </div>
                <div class="text-[11px] text-slate-400 mt-1 font-medium">
                    Siswa aktif menjawab & simpan soal di detik yang sama.
                </div>
            </div>
        </div>

        <!-- Metric 4: Kecepatan Simpan -->
        <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-3 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Kecepatan & Waktu Simpan</span>
                <span class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div>
                <div class="text-2xl font-black text-purple-600 dark:text-purple-400 tracking-tight">
                    {{ number_format($capacityAnalysis['rps'], 0, ',', '.') }} <span class="text-sm font-bold text-slate-500">Jwb/Dtk</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-1 font-medium">
                    Waktu simpan: ~{{ $capacityAnalysis['avg_response_time_ms'] }} milidetik per jawaban.
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Main Desktop Interactive Suite (Row 1: Calculator & Live Simulation; Row 2: Specs & Bottleneck Fixes) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <!-- ROW 1 LEFT: Calculator Card (Col 6) -->
        <div class="lg:col-span-6 flex flex-col">
            <div class="p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-5 flex-1 flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Kalkulator Uji Beban Ujian CBT</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Simulasi kekuatan server berdasarkan jumlah siswa yang mengerjakan ujian.
                            </p>
                        </div>
                    </div>

                    <!-- Preset Buttons -->
                    <div class="space-y-2">
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300">
                            Pilihan Angka Siswa Cepat (Presets):
                        </label>
                        <div class="flex flex-wrap gap-2">
                            @if($ongoingExamStudentsCount > 0)
                            <button type="button" @click="studentsCount = {{ $ongoingExamStudentsCount }}; runSimulation()" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800 font-bold text-xs rounded-xl transition-all active:scale-95 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Ujian Berlangsung ({{ number_format($ongoingExamStudentsCount, 0, ',', '.') }} Siswa)</span>
                            </button>
                            @endif
                            <button type="button" @click="studentsCount = {{ $totalActiveStudents }}; runSimulation()" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800 font-bold text-xs rounded-xl transition-all active:scale-95 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                <span>Total Siswa Sekolah ({{ number_format($totalActiveStudents, 0, ',', '.') }} Siswa)</span>
                            </button>
                        </div>
                    </div>

                    <!-- Input Number Field -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300">
                            Jumlah Siswa Mengerjakan Ujian CBT (Serentak / Berlangsung):
                        </label>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="number" 
                                   min="1" 
                                   max="50000" 
                                   step="1" 
                                   x-model.number="studentsCount" 
                                   @input="runSimulation()"
                                   class="block w-full pl-10 pr-20 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-black text-slate-900 dark:text-white focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder-slate-400 outline-none" 
                                   placeholder="Ketik jumlah siswa CBT...">
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-xs font-extrabold text-indigo-600 dark:text-indigo-400">
                                Siswa
                            </div>
                        </div>
                    </div>

                    <!-- Range Slider -->
                    <div class="space-y-2 pt-1">
                        <div class="flex justify-between text-xs font-bold text-slate-600 dark:text-slate-300">
                            <span>Atau Geser Bar Slider:</span>
                            <span class="font-black text-indigo-600 dark:text-indigo-400" x-text="formatId(studentsCount) + ' Siswa'">-</span>
                        </div>
                        <input type="range" min="0" :max="Math.max(2000, studentsCount)" step="10" x-model.number="studentsCount" @input="runSimulation()" class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                        <div class="relative w-full h-4 text-[10px] text-slate-400 font-medium">
                            <span class="absolute left-0 text-left">0</span>
                            <span class="absolute left-1/4 -translate-x-1/2 text-center" x-text="formatId(Math.round(Math.max(2000, studentsCount) * 0.25))">500</span>
                            <span class="absolute left-1/2 -translate-x-1/2 text-center" x-text="formatId(Math.round(Math.max(2000, studentsCount) * 0.50))">1.000</span>
                            <span class="absolute left-3/4 -translate-x-1/2 text-center" x-text="formatId(Math.round(Math.max(2000, studentsCount) * 0.75))">1.500</span>
                            <span class="absolute right-0 text-right" x-text="formatId(Math.max(2000, studentsCount)) + '+'">2.000+</span>
                        </div>
                    </div>

                    <!-- Quick Presets Numbers -->
                    <div class="pt-1">
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="p in [50, 100, 300, 500, 1000, 2000]">
                                <button type="button" 
                                        @click="studentsCount = p; runSimulation()" 
                                        :class="studentsCount === p ? 'bg-indigo-600 text-white border-indigo-600 shadow-2xs' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100'" 
                                        class="px-3 py-1.5 border font-bold text-xs rounded-xl transition-all active:scale-95" 
                                        x-text="formatId(p)">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Time Window selector -->
                <div class="space-y-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300">
                        Intensitas Trafik Navigasi & Simpan Jawaban (Jeda Waktu):
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button type="button" @click="windowSec = 5; runSimulation()" :class="windowSec === 5 ? 'bg-indigo-600 text-white shadow-2xs font-extrabold' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 font-semibold'" class="py-2 text-xs rounded-xl transition-all active:scale-95">
                            5 Dtk (Padat)
                        </button>
                        <button type="button" @click="windowSec = 15; runSimulation()" :class="windowSec === 15 ? 'bg-indigo-600 text-white shadow-2xs font-extrabold' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 font-semibold'" class="py-2 text-xs rounded-xl transition-all active:scale-95">
                            15 Dtk
                        </button>
                        <button type="button" @click="windowSec = 60; runSimulation()" :class="windowSec === 60 ? 'bg-indigo-600 text-white shadow-2xs font-extrabold' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 font-semibold'" class="py-2 text-xs rounded-xl transition-all active:scale-95">
                            1 Menit
                        </button>
                        <button type="button" @click="windowSec = 120; runSimulation()" :class="windowSec === 120 ? 'bg-indigo-600 text-white shadow-2xs font-extrabold' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 font-semibold'" class="py-2 text-xs rounded-xl transition-all active:scale-95">
                            2 Menit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 1 RIGHT: Results Panel Card (Col 6) -->
        <div class="lg:col-span-6 flex flex-col">
            <div class="p-6 rounded-2xl border transition-all shadow-2xs bg-white dark:bg-slate-900 border-slate-200/80 dark:border-slate-800 flex-1 flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">Hasil Live Uji Kekuatan Server</h3>
                        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full tracking-wider text-white shadow-2xs flex items-center gap-1.5" 
                              :class="simResult.status_level === 'safe' ? 'bg-emerald-600' : (simResult.status_level === 'warning' ? 'bg-amber-600' : 'bg-rose-600')">
                            <template x-if="simResult.status_level === 'safe'">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="simResult.status_level === 'warning'">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </template>
                            <template x-if="simResult.status_level === 'danger'">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </template>
                            <span x-text="simResult.status_badge || 'SIAP & AMAN'">MEMPROSES...</span>
                        </span>
                    </div>

                    <div class="space-y-3 pt-3">
                        <!-- Progress Bar Load % -->
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-slate-600 dark:text-slate-400">Penggunaan Beban Server:</span>
                                <span :class="simResult.status_level === 'safe' ? 'text-emerald-600 dark:text-emerald-400' : (simResult.status_level === 'warning' ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400')" x-text="formatId(simResult.load_percentage) + '%'">-</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden border border-slate-200/60 dark:border-slate-700/60">
                                <div class="h-full transition-all duration-500 rounded-full"
                                     :style="'width: ' + Math.min(100, simResult.load_percentage) + '%'"
                                     :class="simResult.status_level === 'safe' ? 'bg-emerald-500' : (simResult.status_level === 'warning' ? 'bg-amber-500' : 'bg-rose-500')"></div>
                            </div>
                        </div>

                        <div class="space-y-2 pt-2 text-xs">
                            <div class="flex items-center justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400">Siswa Ujian Simulated:</span>
                                <span class="font-black text-slate-900 dark:text-white" x-text="formatId(studentsCount) + ' Siswa'">-</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400">Jawaban Masuk / Detik:</span>
                                <span class="font-black text-slate-900 dark:text-white" x-text="formatId(simResult.rps_needed, 1) + ' Jawaban/Dtk'">-</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400">Batas Maksimal Server:</span>
                                <span class="font-black text-slate-900 dark:text-white" x-text="formatId(simResult.server_max_rps, 1) + ' Jawaban/Dtk'">-</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400">Koneksi Database:</span>
                                <span class="font-bold" :class="simResult.is_db_sufficient ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" x-text="formatId(simResult.db_conn_needed) + ' / ' + formatId(simResult.db_max_conn) + ' Maksimal'">-</span>
                            </div>
                            <div class="flex items-center justify-between py-1.5">
                                <span class="text-slate-500 dark:text-slate-400">Memori Server (RAM):</span>
                                <span class="font-bold" :class="simResult.is_ram_sufficient ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'" x-text="formatId(simResult.ram_needed_mb) + ' MB / ' + formatId(simResult.ram_available_mb) + ' MB'">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommendation Text Box -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 space-y-1">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Saran Kesimpulan Panitia:</span>
                    <p class="text-xs font-semibold leading-relaxed text-slate-700 dark:text-slate-200" x-text="simResult.message">
                        Memuat hasil simulasi...
                    </p>
                </div>
            </div>
        </div>

        <!-- ROW 2 LEFT: Spec Recommendation Card (Col 6) -->
        <div x-show="simResult.spec_recommendation" class="lg:col-span-6 flex flex-col">
            <div class="p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                Rekomendasi Spesifikasi Minimal Server
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Syarat minimal hardware agar server stabil tanpa hambatan.
                            </p>
                        </div>
                        <div class="px-3 py-1 bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/80 rounded-xl text-xs font-black text-indigo-700 dark:text-indigo-300 shrink-0 self-start sm:self-center">
                            Untuk <span x-text="formatId(studentsCount)">-</span> Siswa Ujian
                        </div>
                    </div>

                    <!-- Spec List Items -->
                    <div class="space-y-2.5 pt-3">
                        <!-- CPU -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100/80 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-extrabold text-slate-900 dark:text-white truncate">Prosesor Server (CPU)</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                        Rekomendasi: <b class="text-indigo-600 dark:text-indigo-400 font-black" x-text="simResult.spec_recommendation ? simResult.spec_recommendation.cpu.recommended : '-'"></b> &bull; Saat ini: <span x-text="simResult.spec_recommendation ? simResult.spec_recommendation.cpu.current : '-'"></span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shrink-0" 
                                  :class="simResult.spec_recommendation && simResult.spec_recommendation.cpu.is_sufficient ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-800'" 
                                  x-text="simResult.spec_recommendation && simResult.spec_recommendation.cpu.is_sufficient ? 'Sangat Cukup' : 'Perlu Upgrade'"></span>
                        </div>

                        <!-- RAM -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-purple-100/80 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-extrabold text-slate-900 dark:text-white truncate">Memori Utama (RAM)</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                        Rekomendasi: <b class="text-purple-600 dark:text-purple-400 font-black" x-text="simResult.spec_recommendation ? simResult.spec_recommendation.ram.recommended : '-'"></b> &bull; Saat ini: <span x-text="simResult.spec_recommendation ? simResult.spec_recommendation.ram.current : '-'"></span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shrink-0" 
                                  :class="simResult.spec_recommendation && simResult.spec_recommendation.ram.is_sufficient ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-800'" 
                                  x-text="simResult.spec_recommendation && simResult.spec_recommendation.ram.is_sufficient ? 'Sangat Cukup' : 'Perlu Upgrade'"></span>
                        </div>

                        <!-- Database Connections -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-cyan-100/80 dark:bg-cyan-900/50 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s-8-1.79-8-4"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-extrabold text-slate-900 dark:text-white truncate">Kapasitas Database (MySQL)</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                        Rekomendasi: <b class="text-cyan-600 dark:text-cyan-400 font-black" x-text="simResult.spec_recommendation ? simResult.spec_recommendation.max_connections.recommended : '-'"></b> &bull; Saat ini: <span x-text="simResult.spec_recommendation ? simResult.spec_recommendation.max_connections.current : '-'"></span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shrink-0" 
                                  :class="simResult.spec_recommendation && simResult.spec_recommendation.max_connections.is_sufficient ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-800'" 
                                  x-text="simResult.spec_recommendation && simResult.spec_recommendation.max_connections.is_sufficient ? 'Sangat Cukup' : 'Perlu Adjust'"></span>
                        </div>

                        <!-- OPcache -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100/80 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-extrabold text-slate-900 dark:text-white truncate">Zend OPcache (PHP)</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                        Rekomendasi: <b class="text-emerald-600 dark:text-emerald-400 font-black" x-text="simResult.spec_recommendation ? simResult.spec_recommendation.opcache.recommended : '-'"></b> &bull; Saat ini: <span x-text="simResult.spec_recommendation ? simResult.spec_recommendation.opcache.current : '-'"></span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shrink-0" 
                                  :class="simResult.spec_recommendation && simResult.spec_recommendation.opcache.is_sufficient ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800'" 
                                  x-text="simResult.spec_recommendation && simResult.spec_recommendation.opcache.is_sufficient ? 'Aktif (Optimal)' : 'Nonaktif (Slow)'"></span>
                        </div>

                        <!-- Security APP_DEBUG -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-rose-100/80 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m0-6h4m-2 0V7m0 0V5m0 2h2m-2 0H10"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-extrabold text-slate-900 dark:text-white truncate">Status Mode Debug (APP_DEBUG)</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                        Rekomendasi: <b class="text-slate-700 dark:text-slate-300 font-black" x-text="simResult.spec_recommendation ? simResult.spec_recommendation.app_debug.recommended : '-'"></b> &bull; Saat ini: <span x-text="simResult.spec_recommendation ? simResult.spec_recommendation.app_debug.current : '-'"></span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shrink-0" 
                                  :class="simResult.spec_recommendation && simResult.spec_recommendation.app_debug.is_sufficient ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800'" 
                                  x-text="simResult.spec_recommendation && simResult.spec_recommendation.app_debug.is_sufficient ? 'Aman (Ready)' : 'Aktif (Boros RAM)'"></span>
                        </div>
                    </div>
                </div>

                <!-- Footer Summary Banner inside Card -->
                <div class="p-3 mt-3 bg-indigo-50/70 dark:bg-indigo-950/40 rounded-xl border border-indigo-100 dark:border-indigo-900/60 flex items-center gap-2.5 text-xs text-indigo-900 dark:text-indigo-200">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[11px] font-medium leading-tight">
                        Spesifikasi di atas disesuaikan secara otomatis berdasarkan target <b class="font-black text-indigo-600 dark:text-indigo-400"><span x-text="formatId(studentsCount)"></span> siswa</b>.
                    </span>
                </div>
            </div>
        </div>

        <!-- ROW 2 RIGHT: Catatan & Perbaikan Penting Card (Col 6) -->
        <div class="lg:col-span-6 flex flex-col">
            <div class="p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4 flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Catatan & Status Perbaikan Server
                    </h3>
                    @if(!empty($capacityAnalysis['bottlenecks']))
                    <div class="space-y-3 text-xs pt-3">
                        @foreach($capacityAnalysis['bottlenecks'] as $b)
                            <div class="p-3.5 rounded-xl border flex flex-col justify-between space-y-2 {{ $b['type'] === 'danger' ? 'bg-rose-50/80 border-rose-200 dark:bg-rose-950/30 text-rose-900 dark:text-rose-200' : ($b['type'] === 'warning' ? 'bg-amber-50/80 border-amber-200 dark:bg-amber-950/30 text-amber-900 dark:text-amber-200' : 'bg-blue-50/80 border-blue-200 dark:bg-blue-950/30 text-blue-900 dark:text-blue-200') }}">
                                <div>
                                    <div class="font-extrabold text-xs mb-1">{{ $b['title'] }}</div>
                                    <div class="text-[11px] opacity-90 leading-relaxed">{{ $b['desc'] }}</div>
                                </div>
                                @if(!empty($b['action_target']))
                                <div>
                                    <button type="button" 
                                             @click="runOptimize('{{ $b['action_target'] }}')"
                                             :disabled="isOptimizing"
                                             class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-white {{ $b['type'] === 'danger' ? 'bg-rose-600 hover:bg-rose-700' : ($b['type'] === 'warning' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-indigo-600 hover:bg-indigo-700') }}">
                                        <span>{{ $b['action_label'] ?? 'Jalankan Perbaikan' }}</span>
                                    </button>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="p-4 bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-xl space-y-2 text-xs mt-3">
                        <div class="font-extrabold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Semua Konfigurasi Utama Server Optimal
                        </div>
                        <p class="text-[11px] text-emerald-700 dark:text-emerald-400 leading-relaxed">
                            Tidak ditemukan kendala konfigurasi pada server Anda. Pengaturan MySQL, OPcache, dan mode aplikasi sudah dalam kondisi siap untuk melaksanakan ujian online.
                        </p>
                    </div>
                    @endif
                </div>

                <div class="p-3 mt-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between">
                    <span>Status Pemeriksaan Otomatis:</span>
                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Aktif & Normal
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Technical System Diagnostics (4 Symmetrical Column Cards Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Perangkat Keras (OS & Hardware) -->
        <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-extrabold text-xs text-slate-900 dark:text-white truncate">Perangkat Keras (OS)</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Mesin server utama</p>
                    </div>
                </div>

                <div class="space-y-2.5 pt-3 text-xs">
                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 space-y-0.5">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Sistem Operasi (OS)</span>
                        <div class="font-extrabold text-slate-800 dark:text-slate-200 text-xs truncate" title="{{ $serverInfo['os'] }}">
                            {{ PHP_OS_FAMILY }} ({{ php_uname('s') }})
                        </div>
                    </div>

                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 space-y-0.5">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Inti Pemproses (CPU)</span>
                        <div class="font-black text-indigo-600 dark:text-indigo-400 text-sm">
                            {{ $serverInfo['cpu_cores'] }} Core
                        </div>
                    </div>

                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 space-y-0.5">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Memori (RAM)</span>
                        <div class="font-black text-slate-800 dark:text-slate-200 text-sm">
                            {{ number_format($serverInfo['total_ram_mb'] / 1024, 1, ',', '.') }} GB
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Pengaturan PHP Engine -->
        <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-extrabold text-xs text-slate-900 dark:text-white truncate">Pengaturan PHP</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Limit & penghemat beban</p>
                    </div>
                </div>

                <div class="space-y-2.5 pt-3 text-xs">
                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 space-y-0.5">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Versi Engine PHP</span>
                        <div class="font-black text-purple-600 dark:text-purple-400 text-sm">
                            v{{ $serverInfo['php_version'] }}
                        </div>
                    </div>

                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 space-y-0.5">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Limit Memori PHP</span>
                        <div class="font-extrabold text-slate-800 dark:text-slate-200 text-xs">
                            {{ $serverInfo['memory_limit'] }}
                        </div>
                    </div>

                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Zend OPcache</span>
                            <div class="font-extrabold text-xs {{ $serverInfo['opcache_enabled'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $serverInfo['opcache_enabled'] ? 'Aktif' : 'Nonaktif' }}
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $serverInfo['opcache_enabled'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                            {{ $serverInfo['opcache_enabled'] ? 'ON' : 'OFF' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Database MySQL -->
        <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s-8-1.79-8-4"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-extrabold text-xs text-slate-900 dark:text-white truncate">Database MySQL</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Penyimpanan jawaban</p>
                    </div>
                </div>

                <div class="space-y-2.5 pt-3 text-xs">
                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 space-y-0.5">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Max Connection MySQL</span>
                        <div class="font-black text-cyan-600 dark:text-cyan-400 text-sm">
                            {{ number_format($dbInfo['max_connections'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 space-y-0.5">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Jalur Koneksi Aktif</span>
                        <div class="font-extrabold text-slate-800 dark:text-slate-200 text-xs">
                            {{ $dbInfo['threads_connected'] }} Threads
                        </div>
                    </div>

                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 space-y-0.5">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Buffer Pool (InnoDB)</span>
                        <div class="font-extrabold text-slate-800 dark:text-slate-200 text-xs">
                            {{ $dbInfo['innodb_buffer_pool_mb'] }} MB
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Status Aplikasi & Cache -->
        <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-extrabold text-xs text-slate-900 dark:text-white truncate">Status Aplikasi</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">Optimasi cache sistem</p>
                    </div>
                </div>

                <div class="space-y-2.5 pt-3 text-xs">
                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Mode Debug</span>
                            <div class="font-extrabold text-xs {{ $laravelInfo['app_debug'] ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                {{ $laravelInfo['app_debug'] ? 'Nyala (Bahaya)' : 'Mati (Aman)' }}
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $laravelInfo['app_debug'] ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                            {{ $laravelInfo['app_debug'] ? 'DEBUG' : 'PROD' }}
                        </span>
                    </div>

                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Cache Config</span>
                            <div class="font-extrabold text-xs {{ $laravelInfo['config_cached'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ $laravelInfo['config_cached'] ? 'Sudah' : 'Belum' }}
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $laravelInfo['config_cached'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}">
                            {{ $laravelInfo['config_cached'] ? 'CACHED' : 'UNCACHED' }}
                        </span>
                    </div>

                    <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700/60 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Cache Route</span>
                            <div class="font-extrabold text-xs {{ $laravelInfo['routes_cached'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ $laravelInfo['routes_cached'] ? 'Sudah' : 'Belum' }}
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $laravelInfo['routes_cached'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}">
                            {{ $laravelInfo['routes_cached'] ? 'CACHED' : 'UNCACHED' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Tombol Pembersih & Percepat Akses Server (1-Klik) -->
    <div class="p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-4 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Tombol Pembersih & Percepat Akses Server (1-Klik)
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Klik tombol di bawah ini untuk mengompres data sistem dan mempercepat waktu akses ujian seluruh siswa secara otomatis.
                </p>
            </div>
            <button type="button" 
                    @click="runOptimize('all')" 
                    :disabled="isOptimizing"
                    class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 disabled:opacity-50 text-white font-extrabold rounded-xl text-xs transition-all flex items-center justify-center gap-2 shadow-xs shrink-0">
                <svg x-show="!isOptimizing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <svg x-show="isOptimizing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span x-text="isOptimizing ? 'Memproses Percepatan...' : 'Jalankan Semua Percepatan Server'">Jalankan Semua Percepatan Server</span>
            </button>
        </div>

        <!-- Notification Toast -->
        <template x-if="optToast.message">
            <div :class="optToast.success ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-900/60 dark:text-emerald-300' : 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/60 dark:text-rose-300'" class="p-3 rounded-xl border text-xs font-bold flex items-center justify-between transition-all shadow-2xs">
                <span x-text="optToast.message"></span>
                <button type="button" @click="optToast.message = ''" class="opacity-70 hover:opacity-100 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <!-- Button 1: Config Cache -->
            <button type="button" 
                    @click="runOptimize('config')" 
                    :disabled="isOptimizing"
                    class="p-4 bg-slate-50 hover:bg-slate-100/80 dark:bg-slate-800/80 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl text-left transition-all group flex flex-col justify-between space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs text-emerald-600 dark:text-emerald-400 font-bold group-hover:text-emerald-700">php artisan config:cache</span>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] font-extrabold rounded-md">CONFIG</span>
                </div>
                <div class="text-xs font-extrabold text-slate-800 dark:text-slate-200 flex items-center gap-2 group-hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Percepat Konfigurasi Server</span>
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                    Mengunci seluruh setelan sistem agar aplikasi merespon lebih cepat.
                </div>
            </button>

            <!-- Button 2: Route Cache -->
            <button type="button" 
                    @click="runOptimize('route')" 
                    :disabled="isOptimizing"
                    class="p-4 bg-slate-50 hover:bg-slate-100/80 dark:bg-slate-800/80 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl text-left transition-all group flex flex-col justify-between space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs text-cyan-600 dark:text-cyan-400 font-bold group-hover:text-cyan-700">php artisan route:cache</span>
                    <span class="px-2 py-0.5 bg-cyan-100 text-cyan-700 dark:bg-cyan-950 dark:text-cyan-300 text-[10px] font-extrabold rounded-md">ROUTE</span>
                </div>
                <div class="text-xs font-extrabold text-slate-800 dark:text-slate-200 flex items-center gap-2 group-hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 text-cyan-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Percepat Akses URL Halaman</span>
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                    Membuat perpindahan halaman soal ujian 3x lipat lebih responsif.
                </div>
            </button>

            <!-- Button 3: View Cache -->
            <button type="button" 
                    @click="runOptimize('view')" 
                    :disabled="isOptimizing"
                    class="p-4 bg-slate-50 hover:bg-slate-100/80 dark:bg-slate-800/80 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl text-left transition-all group flex flex-col justify-between space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs text-purple-600 dark:text-purple-400 font-bold group-hover:text-purple-700">php artisan view:cache</span>
                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 text-[10px] font-extrabold rounded-md">VIEW</span>
                </div>
                <div class="text-xs font-extrabold text-slate-800 dark:text-slate-200 flex items-center gap-2 group-hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Percepat Tampilan Layar Ujian</span>
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                    Menyimpan desain halaman agar langsung muncul di HP/komputer siswa.
                </div>
            </button>
        </div>
    </div>
</div>

<script>
function cbtSimulator() {
    return {
        studentsCount: {{ $ongoingExamStudentsCount > 0 ? $ongoingExamStudentsCount : ($totalActiveStudents > 0 ? $totalActiveStudents : max(50, $capacityAnalysis['burst_capacity'])) }},
        windowSec: 5,
        isOptimizing: false,
        optToast: { success: true, message: '' },
        simResult: {
            passed: true,
            status_level: 'safe',
            status_badge: 'MEMPROSES...',
            load_percentage: 0,
            rps_needed: 0,
            server_max_rps: {{ $capacityAnalysis['rps'] }},
            db_conn_needed: 0,
            db_max_conn: {{ $dbInfo['max_connections'] }},
            is_db_sufficient: true,
            ram_needed_mb: 0,
            ram_available_mb: {{ $serverInfo['total_ram_mb'] }},
            is_ram_sufficient: true,
            message: 'Memuat simulasi awal...'
        },
        async runOptimize(target) {
            this.isOptimizing = true;
            this.optToast = { success: true, message: '' };
            try {
                const response = await fetch("{{ route('admin.cbt-capacity.optimize') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ target: target })
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    this.optToast = { success: true, message: result.message };
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    this.optToast = { success: false, message: result.message || 'Gagal menjalankan optimasi' };
                }
            } catch (e) {
                this.optToast = { success: false, message: 'Terjadi kesalahan sistem saat menjalankan optimasi.' };
            } finally {
                this.isOptimizing = false;
            }
        },
        formatId(val, decimals = 0) {
            if (val === null || val === undefined || isNaN(val)) return '-';
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(val);
        },
        init() {
            this.runSimulation();
        },
        async runSimulation() {
            try {
                const response = await fetch("{{ route('admin.cbt-capacity.simulate') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        students: this.studentsCount,
                        window: this.windowSec
                    })
                });
                if (response.ok) {
                    this.simResult = await response.json();
                }
            } catch (e) {
                console.error("Simulation error", e);
            }
        }
    }
}
</script>
@endsection
