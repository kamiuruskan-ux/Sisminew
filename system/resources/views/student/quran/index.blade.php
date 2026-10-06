@extends('layouts.student-mobile')

@section('title', "Mutaba'ah Al-Qur'an")
@section('header_title', "Mutaba'ah Al-Qur'an")

@section('content')
<div class="space-y-6 pb-12">

    {{-- HERO BANNER MUTABA'AH --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 p-6 sm:p-8 text-white shadow-xl border border-emerald-500/30">
        {{-- Background Ornament --}}
        <div class="absolute -right-8 -bottom-8 w-44 h-44 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute right-12 top-6 opacity-15 pointer-events-none hidden sm:block">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-emerald-100 text-xs font-bold border border-white/20">
                    <span>📖 Mutaba'ah Halaqah Al-Qur'an</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-ping"></span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Buku Pantauan Al-Qur'an Digital
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100 max-w-xl leading-relaxed">
                    Pantau capaian setoran harian Tahsin, Tahfidz (Hafalan), dan Tilawah ananda secara transparan dan terintegrasi langsung bersama Musyrif.
                </p>
            </div>

            {{-- Info Musyrif Pembimbing --}}
            <div class="bg-white/15 backdrop-blur-md rounded-2xl p-4 border border-white/20 sm:min-w-[260px]">
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-200 mb-1">Musyrif / Guru Pembimbing</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white text-emerald-700 flex items-center justify-center font-black shadow-sm text-sm">
                        {{ substr($halaqahMembership?->teacher?->name ?? 'G', 0, 1) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-white leading-tight">
                            Ust. {{ $halaqahMembership?->teacher?->name ?? 'Belum Ditentukan' }}
                        </h4>
                        <p class="text-[11px] text-emerald-200">
                            {{ $halaqahMembership?->group_name ?? "Halaqah Tingkat {$studentGrade}" }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK STATS CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        {{-- Total Setoran --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Setoran</span>
                <div class="w-7 h-7 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalSetoran }}</p>
            <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5">Kali pertemuan</p>
        </div>

        {{-- Tahsin --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Tahsin</span>
                <div class="w-7 h-7 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $tahsinCount }}</p>
            <p class="text-[11px] font-semibold text-slate-500 mt-0.5">{{ $latestTahsin ? ($latestTahsin->jilid_level ?? 'Jilid') : 'Belum ada' }}</p>
        </div>

        {{-- Tahfidz --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Tahfidz</span>
                <div class="w-7 h-7 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $tahfidzCount }}</p>
            <p class="text-[11px] font-semibold text-teal-600 dark:text-teal-400 mt-0.5">{{ $latestTahfidz ? "Juz {$latestTahfidz->juz_number}" : 'Belum ada' }}</p>
        </div>

        {{-- Tilawah --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Tilawah</span>
                <div class="w-7 h-7 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $tilawahCount }}</p>
            <p class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 mt-0.5">{{ $latestTilawah ? ($latestTilawah->surah_name ?? 'Surah') : 'Belum ada' }}</p>
        </div>

        {{-- Ziyadah vs Muroja'ah --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Ziyadah / Mur.</span>
                <div class="w-7 h-7 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $ziyadahCount }} <span class="text-xs text-slate-400">/</span> {{ $murojaahCount }}</p>
            <p class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 mt-0.5">Baru vs Ulang</p>
        </div>

        {{-- Rata-rata Nilai --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Rata Nilai</span>
                <div class="w-7 h-7 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 dark:text-white">{{ $avgScore }}</p>
            <p class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 mt-0.5">{{ $mumtazCount }} Mumtaz</p>
        </div>
    </div>

    {{-- CAPAIAN TERAKHIR SANTRI (3 PROGRAM CARDS) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- 1. Capaian Terakhir Tahsin --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">
                    📖 PROGRAM TAHSIN
                </span>
                <span class="text-[11px] text-slate-400 font-medium">
                    {{ $latestTahsin ? $latestTahsin->assessment_date->format('d/m/Y') : '-' }}
                </span>
            </div>
            <div class="pt-4 space-y-2">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Posisi Bimbingan Terakhir</p>
                <h4 class="text-lg font-black text-slate-900 dark:text-white">
                    {{ $latestTahsin ? $latestTahsin->material_summary : 'Belum ada riwayat Tahsin' }}
                </h4>
                @if($latestTahsin)
                    <div class="flex items-center gap-2 pt-1">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Nilai: <strong class="text-emerald-600">{{ $latestTahsin->score_cognitive }}</strong></span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ $latestTahsin->predicate }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. Capaian Terakhir Tahfidz --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                    ⭐ PROGRAM TAHFIDZ (HAFALAN)
                </span>
                <span class="text-[11px] text-slate-400 font-medium">
                    {{ $latestTahfidz ? $latestTahfidz->assessment_date->format('d/m/Y') : '-' }}
                </span>
            </div>
            <div class="pt-4 space-y-2">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Hafalan Terakhir</p>
                <h4 class="text-lg font-black text-slate-900 dark:text-white">
                    {{ $latestTahfidz ? $latestTahfidz->material_summary : 'Belum ada riwayat Tahfidz' }}
                </h4>
                @if($latestTahfidz)
                    <div class="flex items-center gap-2 pt-1">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Nilai: <strong class="text-emerald-600">{{ $latestTahfidz->score_cognitive }}</strong></span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ $latestTahfidz->predicate }}</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ $latestTahfidz->record_category === 'murojaah' ? 'Muroja\'ah' : 'Ziyadah' }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- 3. Capaian Terakhir Tilawah --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                    📜 PROGRAM TILAWAH AL-QUR'AN
                </span>
                <span class="text-[11px] text-slate-400 font-medium">
                    {{ $latestTilawah ? $latestTilawah->assessment_date->format('d/m/Y') : '-' }}
                </span>
            </div>
            <div class="pt-4 space-y-2">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Tilawah Terakhir</p>
                <h4 class="text-lg font-black text-slate-900 dark:text-white">
                    {{ $latestTilawah ? $latestTilawah->material_summary : 'Belum ada riwayat Tilawah' }}
                </h4>
                @if($latestTilawah)
                    <div class="flex items-center gap-2 pt-1">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Nilai: <strong class="text-emerald-600">{{ $latestTilawah->score_cognitive }}</strong></span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ $latestTilawah->predicate }}</span>
                    </div>
                @endif
            </div>
        </div>
     {{-- TARGET CAPAIAN TINGKAT KELAS & UJIAN SYAHADAH (JILID & TASMI') --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Target Kurikulum Tingkat Kelas --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                        Target Capaian Tingkat {{ $studentGrade }}
                    </h3>
                </div>
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">Standar Sekolah</span>
            </div>

            @if($targets->isEmpty())
                <div class="text-center py-8 text-slate-400 text-xs font-medium">
                    Belum ada data target khusus untuk Tingkat {{ $studentGrade }}.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($targets as $tgt)
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                        Semester {{ $tgt->semester }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                        {{ strtoupper($tgt->program_type) }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-extrabold text-slate-800 dark:text-white">{{ $tgt->title }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    @if($tgt->target_juz) Target Juz {{ $tgt->target_juz }} @endif
                                    @if($tgt->target_surah_start) (Surah {{ $tgt->target_surah_start }} s/d {{ $tgt->target_surah_end }}) @endif
                                    @if($tgt->target_jilid) Target Jilid: {{ $tgt->target_jilid }} @endif
                                </p>
                            </div>
                            <span class="px-2 py-1 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-[10px] font-bold border border-slate-200 dark:border-slate-600 shrink-0">
                                Kurikulum
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Ujian Kenaikan Jilid Tahsin & Syahadah --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                        Ujian Kenaikan Jilid
                    </h3>
                </div>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Metode Tilawah</span>
            </div>

            @if($jilidExams->isEmpty())
                <div class="text-center py-8 space-y-2">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Riwayat Ujian Jilid</p>
                    <p class="text-[11px] text-slate-400 max-w-sm mx-auto">
                        Setelah menuntaskan evaluasi jilid berjalan, ananda dapat mengikuti Munaqasyah Kenaikan Jilid dan mendapatkan Syahadah resmi.
                    </p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($jilidExams as $exam)
                        <div class="p-3.5 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-800/40 flex items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                                        {{ $exam->current_jilid }} ➜ {{ $exam->target_jilid }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $exam->isLulus() ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $exam->isLulus() ? 'NAIK JILID' : 'PERBAIKAN' }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-white">
                                    Predikat: {{ $exam->predicate }} (Nilai: {{ $exam->score_final }})
                                </h4>
                                <p class="text-[10px] text-slate-500 font-mono">
                                    No: {{ $exam->certificate_number ?? '-' }}
                                </p>
                            </div>

                            <a href="{{ route('admin.halaqah.jilid-exam.certificate', $exam->id) }}" target="_blank"
                               class="px-2.5 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Syahadah</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Ujian Tasmi' 1 Juz Sekali Duduk & Syahadah --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                        Ujian Tasmi' 1 Juz
                    </h3>
                </div>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Tahfidz Bil-Ghoib</span>
            </div>

            @if($tasmiExams->isEmpty())
                <div class="text-center py-8 space-y-2">
                    <div class="w-12 h-12 mx-auto rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Riwayat Ujian Tasmi'</p>
                    <p class="text-[11px] text-slate-400 max-w-sm mx-auto">
                        Setelah menuntaskan hafalan 1 juz, ananda dapat mengikuti Ujian Tasmi' Sekali Duduk dan mendapatkan Syahadah resmi.
                    </p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($tasmiExams as $exam)
                        <div class="p-3.5 rounded-2xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-800/40 flex items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-200 text-amber-900 dark:bg-amber-900 dark:text-amber-200">
                                        {{ $exam->juz_tested }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $exam->status === 'lulus' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800' }}">
                                        {{ strtoupper($exam->status) }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-white">
                                    Predikat: {{ $exam->predicate }} (Nilai: {{ $exam->score_final }})
                                </h4>
                                <p class="text-[10px] text-slate-500 font-mono">
                                    No. Syahadah: {{ $exam->certificate_number ?? '-' }}
                                </p>
                            </div>

                            <a href="{{ route('admin.halaqah.tasmi.certificate', $exam->id) }}" target="_blank"
                               class="px-2.5 py-1.5 bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Syahadah</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- RIWAYAT SETORAN LENGKAP & FILTER --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                    Logbook Riwayat Setoran Harian
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar evaluasi lengkap yang dicatat oleh Ustadz pembimbing</p>
            </div>

            {{-- Filter Form --}}
            <form action="{{ route('student.quran.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                {{-- Filter Program --}}
                <select name="program" onchange="this.form.submit()" class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold outline-none">
                    <option value="all" {{ $filterProgram === 'all' ? 'selected' : '' }}>Semua Program</option>
                    <option value="tahsin" {{ $filterProgram === 'tahsin' ? 'selected' : '' }}>Tahsin</option>
                    <option value="tahfidz" {{ $filterProgram === 'tahfidz' ? 'selected' : '' }}>Tahfidz</option>
                    <option value="tilawah" {{ $filterProgram === 'tilawah' ? 'selected' : '' }}>Tilawah</option>
                </select>

                {{-- Filter Kategori --}}
                <select name="category" onchange="this.form.submit()" class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold outline-none">
                    <option value="all" {{ $filterCategory === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="ziyadah" {{ $filterCategory === 'ziyadah' ? 'selected' : '' }}>Ziyadah (Baru)</option>
                    <option value="murojaah" {{ $filterCategory === 'murojaah' ? 'selected' : '' }}>Muroja'ah (Ulang)</option>
                </select>
            </form>
        </div>

        @if($records->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h4 class="text-sm font-black text-slate-800 dark:text-white">Belum Ada Data Catatan</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                    Catatan setoran akan tampil di sini begitu Ustadz pembimbing memasukkan penilaian halaqah.
                </p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($records as $rec)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-emerald-300 dark:hover:border-emerald-700 transition">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-black text-slate-700 dark:text-slate-300">
                                    {{ $rec->assessment_date->translatedFormat('l, d F Y') }}
                                </span>
                                {{-- Program Badge --}}
                                @if($rec->program_type === 'tahsin')
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300">TAHSIN</span>
                                @elseif($rec->program_type === 'tahfidz')
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">TAHFIDZ</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">TILAWAH</span>
                                @endif

                                {{-- Category Badge --}}
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold {{ $rec->record_category === 'murojaah' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300' : 'bg-teal-100 text-teal-700 dark:bg-teal-950 dark:text-teal-300' }}">
                                    {{ $rec->record_category === 'murojaah' ? 'Muroja\'ah' : 'Ziyadah' }}
                                </span>

                                {{-- Kehadiran --}}
                                <span class="text-[10px] font-bold text-slate-400">
                                    Status: <strong class="{{ $rec->attendance_status === 'hadir' ? 'text-emerald-600' : 'text-rose-500' }}">{{ ucfirst($rec->attendance_status) }}</strong>
                                </span>
                            </div>

                            {{-- Material Summary --}}
                            <p class="text-sm font-black text-slate-900 dark:text-white">
                                {{ $rec->material_summary }}
                            </p>

                            {{-- Teacher Note --}}
                            @if($rec->teacher_notes)
                                <p class="text-xs text-slate-500 dark:text-slate-400 italic">
                                    "{{ $rec->teacher_notes }}"
                                </p>
                            @endif

                            <p class="text-[10px] text-slate-400">
                                Musyrif: Ust. {{ $rec->teacher?->name ?? 'Pembimbing' }}
                            </p>
                        </div>

                        {{-- Score & Predicate Badge --}}
                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-1.5 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200 dark:border-slate-700">
                            <div class="text-right">
                                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 leading-none">
                                    {{ $rec->score_cognitive }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-400 block">Nilai Tajwid</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $rec->predicate === 'Mumtaz' ? 'bg-emerald-600 text-white' : ($rec->predicate === 'Jayyid Jiddan' ? 'bg-teal-600 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200') }}">
                                {{ $rec->predicate }}
                            </span>
                        </div>
                    </div>
                @endforeach

                <div class="pt-4">
                    {{ $records->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
