@extends('layouts.student-mobile')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $student = $user->student;
    $studentMajor = $student?->major ?? $student?->class?->major;
    $majorName = ($studentMajor && ($studentMajor->is_active ?? true)) ? ($studentMajor->name ?? $studentMajor->code) : null;

    // Greeting logic based on Asia/Jakarta timezone
    $hour = now('Asia/Jakarta')->hour;
    if ($hour >= 5 && $hour < 11) {
        $greeting = 'Selamat Pagi';
    } elseif ($hour >= 11 && $hour < 15) {
        $greeting = 'Selamat Siang';
    } elseif ($hour >= 15 && $hour < 18) {
        $greeting = 'Selamat Sore';
    } else {
        $greeting = 'Selamat Malam';
    }
@endphp

<!-- Main Container Layout: Asymmetric 2/3 + 1/3 Split on Desktop (PC) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- LEFT COLUMN (2/3 width on PC) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Welcome Card with Profile -->
        <div class="bg-gradient-to-br from-indigo-600 via-blue-600 to-indigo-700 dark:from-indigo-900 dark:via-blue-900 dark:to-slate-900 rounded-2xl p-5 text-white shadow-xl relative overflow-hidden border border-indigo-500/30">
            <!-- Background Pattern -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-10 translate-x-10 blur-sm pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-28 h-28 bg-white/5 rounded-full translate-y-6 -translate-x-6 pointer-events-none"></div>

            <!-- QR Absen Button - Top Right Corner -->
            <button type="button" onclick="openQrModal()"
                class="absolute top-4 right-4 z-20 flex flex-col items-center gap-0.5 group cursor-pointer">
                <div class="w-9 h-9 bg-white/15 hover:bg-white/25 active:scale-90 border border-white/30 rounded-xl flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <span class="text-[8px] font-bold text-white/80 group-hover:text-white transition-colors leading-none">QR Absen</span>
            </button>

            <div class="relative z-10">
                <div class="flex items-center gap-3.5 pr-12">
                    <!-- Profile Image -->
                    <div class="relative shrink-0">
                        <img src="{{ $student?->photo_url ?? ('https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=ffffff&color=3B82F6&size=128') }}"
                             alt="Profile"
                             class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover border-2 border-white/30 shadow-lg bg-white">
                        <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-400 border-2 border-white rounded-full"></div>
                    </div>

                    <!-- Welcome Text -->
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] text-indigo-200/80 font-medium block leading-none mb-0.5">{{ $greeting }},</span>
                        <h2 class="text-lg sm:text-xl font-extrabold leading-tight truncate">{{ $user->name }}</h2>
                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                            <span class="inline-flex items-center font-bold bg-sky-500/20 px-1.5 py-0.5 rounded-md border border-sky-400/30 text-sky-100 text-[10px]">
                                <svg class="w-2.5 h-2.5 mr-1 text-sky-200 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4.028z"/>
                                </svg>
                                {{ $student?->class?->name ?? 'Belum ada kelas' }}
                            </span>
                            @if($majorName)
                                <span class="inline-flex items-center font-bold bg-violet-500/40 px-1.5 py-0.5 rounded-md border border-violet-300/50 text-white text-[10px] max-w-[120px] truncate">
                                    <svg class="w-2.5 h-2.5 mr-1 text-violet-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    {{ $studentMajor?->code ?: $studentMajor?->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Footer inside Welcome Card -->
                <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-white/20">
                    {{-- NISN --}}
                    <div class="bg-white/10 border border-white/20 rounded-xl py-2.5 px-2 text-center">
                        <p class="text-[8px] text-indigo-200 font-bold uppercase tracking-widest mb-1">NISN</p>
                        <p class="text-[11px] font-extrabold text-white font-mono truncate">{{ $student?->nisn ?? '-' }}</p>
                    </div>
                    {{-- Kehadiran --}}
                    <div class="bg-emerald-500/30 border border-emerald-300/40 rounded-xl py-2.5 px-2 text-center">
                        <p class="text-[8px] text-emerald-200 font-bold uppercase tracking-widest mb-1">Kehadiran</p>
                        <p class="text-sm font-black text-white leading-none">{{ $attendanceStats['percentage'] ?? 0 }}<span class="text-[10px] font-bold text-emerald-200">%</span></p>
                    </div>
                    {{-- Tabungan --}}
                    <a href="{{ route('student.savings.index') }}" class="bg-amber-500/30 border border-amber-300/40 rounded-xl py-2.5 px-2 text-center hover:bg-amber-500/45 active:scale-95 transition-all block group">
                        <p class="text-[8px] text-amber-200 font-bold uppercase tracking-widest mb-1 group-hover:text-amber-100 transition-colors">Tabungan</p>
                        <p class="text-[11px] font-black text-white leading-none truncate">Rp {{ number_format($student?->savings_balance ?? 0, 0, ',', '.') }}</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Menu Portal Siswa Card (Lengkap dengan 12 Menu Utama & Fitur 'Semua Menu' Grid Kotak-Kotak) -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200 dark:border-slate-700 space-y-3">
            <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-100 dark:border-slate-700/80">
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 dark:text-white uppercase tracking-wider flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Menu Portal Siswa</span>
                </h3>
                <button type="button" onclick="openAllMenusModal()" class="text-[11px] font-extrabold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center space-x-1 cursor-pointer">
                    <span>Lihat Semua</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <div class="grid grid-cols-4 sm:grid-cols-6 gap-y-4 gap-x-2">
                <!-- 1. LMS Belajar (Indigo/Deep Blue) -->
                <a href="{{ route('student.lms.index') }}" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-indigo-100 dark:border-indigo-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        @if(($pendingAssignments->count() ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 bg-amber-500 text-white text-[9px] font-black px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-white dark:ring-slate-800 shadow-xs animate-pulse">
                                {{ $pendingAssignments->count() }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-indigo-600 transition-colors">LMS Belajar</span>
                </a>

                <!-- 2. Tabungan Siswa (Cyan/Light Blue) -->
                <a href="{{ route('student.savings.index') }}" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-cyan-100 dark:border-cyan-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-cyan-600 transition-colors">Tabungan</span>
                </a>

                <!-- 3. Materi Modul (Emerald/Green) -->
                <a href="{{ route('student.materials.index') }}" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-emerald-100 dark:border-emerald-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        @if(($materials->count() ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 bg-indigo-500 text-white text-[9px] font-black px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-white dark:ring-slate-800 shadow-xs">
                                {{ $materials->count() }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-emerald-600 transition-colors">Materi Modul</span>
                </a>

                <!-- 4. Ujian CBT (Fuchsia/Pink) -->
                <a href="{{ route('student.exams.index') }}" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-fuchsia-50 dark:bg-fuchsia-950/40 text-fuchsia-600 dark:text-fuchsia-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-fuchsia-100 dark:border-fuchsia-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-fuchsia-600 transition-colors">Ujian CBT</span>
                </a>

                <!-- 5. E-Raport Siswa (Emerald/Green) -->
                <a href="{{ route('student.raport.print') }}" target="_blank" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-emerald-100 dark:border-emerald-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-6-3h12"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-emerald-600 transition-colors">E-Raport</span>
                </a>

                <!-- 6. Laporan Nilai (Teal/Seafoam) -->
                <a href="{{ route('student.grades.index') }}" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-teal-100 dark:border-teal-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-teal-600 transition-colors">Laporan Nilai</span>
                </a>

                <!-- 6. Pengumuman (Purple/Violet) -->
                <a href="{{ route('student.announcements') }}" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-purple-100 dark:border-purple-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if(($announcements->count() ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-black px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-white dark:ring-slate-800 shadow-xs animate-pulse">
                                {{ $announcements->count() }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-purple-600 transition-colors">Pengumuman</span>
                </a>

                <!-- 7. Tagihan & SPP (Rose/Red) -->
                <a href="{{ route('student.payments.index') }}" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-rose-100 dark:border-rose-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        @if(($unpaidBillsCount ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-black px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-white dark:ring-slate-800 shadow-xs animate-pulse">
                                {{ $unpaidBillsCount }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-rose-600 transition-colors">Tagihan & SPP</span>
                </a>

                <!-- 8. Pengajuan Izin (Yellow/Gold) -->
                <a href="{{ route('student.permits.index') }}" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-yellow-50 dark:bg-yellow-950/40 text-yellow-600 dark:text-yellow-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-yellow-100 dark:border-yellow-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-yellow-600 transition-colors">Pengajuan Izin</span>
                </a>

                <!-- 9. E-Library (Sky Blue) -->
                <a href="{{ route('student.library.index') }}" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-sky-100 dark:border-sky-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-sky-600 transition-colors">E-Library</span>
                </a>

                <!-- 10. Leaderboard (Amber Gold) -->
                <a href="{{ route('student.lms.gamification') }}" class="flex flex-col items-center justify-center text-center group">
                    <div class="relative w-12 h-12 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-amber-100 dark:border-amber-800/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14l-1.5 8a4.5 4.5 0 01-4.5 4.5h-2A4.5 4.5 0 016.5 11L5 3zM6 6H3v2a3 3 0 003 3M18 6h3v2a3 3 0 01-3 3M12 15.5V18M9 21h6"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-amber-600 transition-colors">Leaderboard</span>
                </a>

                <!-- 11. Semua Menu (Full Block Color Launcher) -->
                <button type="button" onclick="openAllMenusModal()" class="flex flex-col items-center justify-center text-center group cursor-pointer">
                    <div class="relative w-12 h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl flex items-center justify-center mb-1.5 shadow-md group-hover:scale-105 transition-all border border-indigo-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </div>
                    <span class="text-[10px] font-extrabold text-indigo-600 dark:text-indigo-400 group-hover:text-indigo-700 transition-colors">Semua Menu</span>
                </button>
            </div>
        </div>

        <!-- Row 2: Stats Grid 4 KPI Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- Attendance Card -->
            <a href="{{ route('student.grades.index') }}" class="bg-white dark:bg-slate-800/90 rounded-2xl p-4 shadow-xs border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-500 hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center border border-indigo-100 dark:border-indigo-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">{{ number_format($gradeStats['average'] ?? 0, 1) }}</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Rata-rata Nilai</p>
                <p class="text-sm font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $gradeStats['total'] ?? 0 }} Nilai</p>
            </a>

            <!-- Assignments Card -->
            <a href="{{ route('student.assignments.index') }}" class="bg-white dark:bg-slate-800/90 rounded-2xl p-4 shadow-xs border border-slate-200 dark:border-slate-700 hover:border-amber-300 dark:hover:border-amber-500 hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center border border-amber-100 dark:border-amber-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/50 px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-800">{{ $pendingAssignments->count() }}</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Tugas</p>
                <p class="text-sm font-extrabold text-slate-900 dark:text-white mt-0.5">Belum Selesai</p>
            </a>

            <!-- Schedule Card -->
            <a href="{{ route('student.schedules.index') }}" class="bg-white dark:bg-slate-800/90 rounded-2xl p-4 shadow-xs border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-500 hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center border border-emerald-100 dark:border-emerald-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/50 px-2 py-0.5 rounded-full border border-blue-200 dark:border-blue-800">{{ $todaySchedule->count() }}</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Jadwal Mapel</p>
                <p class="text-sm font-extrabold text-slate-900 dark:text-white mt-0.5">Hari Ini</p>
            </a>

            <!-- Materials Card -->
            <a href="{{ route('student.materials.index') }}" class="bg-white dark:bg-slate-800/90 rounded-2xl p-4 shadow-xs border border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-500 hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center border border-purple-100 dark:border-purple-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-bold text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/50 px-2 py-0.5 rounded-full border border-purple-200 dark:border-purple-800">{{ $materials->count() }}</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Modul Materi</p>
                <p class="text-sm font-extrabold text-slate-900 dark:text-white mt-0.5">Tersedia</p>
            </a>
        </div>

        <!-- Today's Schedule & Urgent Assignments Side-by-Side on PC -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Today's Schedule -->
            <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-5 shadow-xs border border-slate-200 dark:border-slate-700 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center">
                            <svg class="w-4 h-4 mr-2 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Jadwal Hari Ini
                        </h3>
                        <span class="text-[11px] text-slate-400 dark:text-slate-400 font-medium">{{ now()->translatedFormat('d M Y') }}</span>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($todaySchedule->take(3) as $index => $schedule)
                            <div class="flex items-center p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/80 dark:border-slate-700/80 hover:border-emerald-300 dark:hover:border-emerald-500 transition-colors">
                                <div class="w-1.5 h-9 bg-emerald-500 rounded-full mr-3"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $schedule->subject }}</p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }} • R. {{ $schedule->room }}</p>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-300 bg-white dark:bg-slate-800 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-700">#{{ $index + 1 }}</span>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 rounded-xl flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Tidak ada jadwal pelajaran hari ini</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if($todaySchedule->count() > 3)
                    <div class="mt-4 pt-2 border-t border-slate-100 dark:border-slate-700">
                        <a href="{{ route('student.schedules.index') }}" class="block text-center text-xs text-indigo-600 dark:text-indigo-400 font-bold hover:underline">
                            + {{ $todaySchedule->count() - 3 }} jadwal lainnya →
                        </a>
                    </div>
                @endif
            </div>

            <!-- Urgent Assignments -->
            <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-5 shadow-xs border border-slate-200 dark:border-slate-700 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm flex items-center">
                            <svg class="w-4 h-4 mr-2 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tugas Mendesak
                        </h3>
                        <a href="{{ route('student.assignments.index') }}" class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Lihat Semua →</a>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($pendingAssignments->take(3) as $assignment)
                            <div class="p-3 bg-amber-50/50 dark:bg-amber-950/30 rounded-xl border border-amber-200/80 dark:border-amber-800/60 hover:border-amber-300 dark:hover:border-amber-600 transition-colors flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $assignment->title }}</p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $assignment->subject }} • Tenggat: {{ $assignment->due_date->diffForHumans() }}</p>
                                </div>
                                <a href="{{ route('student.assignments.show', \App\Helpers\IdEncrypter::encrypt($assignment->id)) }}" class="text-[11px] font-bold bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-300 border border-slate-200 dark:border-slate-700 px-2.5 py-1 rounded-lg hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 transition-colors shrink-0">
                                    Buka
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Semua tugas sudah dikerjakan!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN (1/3 width on PC) -->
    <div class="lg:col-span-1 space-y-6">

        <!-- Kartu Absensi Digital (QR Code Card) -->
        <div class="bg-gradient-to-br from-purple-700 via-indigo-700 to-purple-800 dark:from-purple-950 dark:via-indigo-950 dark:to-slate-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden border border-purple-500/30">
            <!-- Background Pattern -->
            <div class="absolute top-0 right-0 w-36 h-36 bg-white/5 rounded-full -translate-y-8 translate-x-8 blur-xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-11 h-11 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30 text-white shrink-0 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-sm tracking-tight">Kartu Absensi Digital</h3>
                            <p class="text-[11px] text-purple-200 font-light">Scan QR Code Kehadiran</p>
                        </div>
                    </div>

                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-emerald-500/30 border border-emerald-400/40 text-emerald-200 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        Aktif
                    </span>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-xl p-3 mb-4 border border-white/15 text-xs text-purple-100 flex items-center justify-between">
                    <span>Kelas: <strong class="text-white">{{ $student->class->name ?? '-' }}</strong></span>
                    <span>NISN: <strong class="text-white font-mono">{{ $student->nisn ?? '-' }}</strong></span>
                </div>

                <button onclick="openQrModal()" class="w-full bg-white text-purple-900 px-4 py-3 rounded-xl font-extrabold text-xs uppercase tracking-wider hover:bg-purple-50 transition-all shadow-md flex items-center justify-center space-x-2 border border-white/40 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Tampilkan QR Code</span>
                </button>
            </div>
        </div>

    </div>

</div>

<!-- Modal Semua Menu Portal Siswa Lengkap -->
<div id="allMenusModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 hidden flex items-center justify-center p-4 transition-all duration-300" onclick="closeAllMenusModalOutside(event)">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-xl max-h-[85vh] overflow-hidden flex flex-col transform transition-all border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100" onclick="event.stopPropagation()">
        
        <!-- Header Modal -->
        <div class="bg-gradient-to-r from-primary via-secondary to-indigo-900 text-white p-5 relative shrink-0 border-b border-white/10 shadow-md">
            <button type="button" onclick="closeAllMenusModal()" class="absolute top-3.5 right-3.5 z-20 w-8 h-8 bg-white/15 hover:bg-white/25 text-white rounded-full flex items-center justify-center transition-all border border-white/20 cursor-pointer" title="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-white/15 text-white flex items-center justify-center shrink-0 border border-white/20 shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-white tracking-tight">Semua Fitur Portal Siswa</h3>
                    <p class="text-[11px] text-white/80">Akses lengkap ke seluruh layanan & modul akademik</p>
                </div>
            </div>
        </div>

        <!-- Body Scrollable Content -->
        <div class="p-5 overflow-y-auto space-y-6 flex-1">
            <!-- Group 1: Ruang Belajar & LMS -->
            <div class="space-y-3">
                <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center space-x-1.5 pb-1 border-b border-slate-100 dark:border-slate-800">
                    <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Ruang Belajar & LMS</span>
                </h4>
                <div class="grid grid-cols-4 gap-y-4 gap-x-2">
                    <a href="{{ route('student.lms.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-indigo-100 dark:border-indigo-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            @if(($pendingAssignments->count() ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 bg-amber-500 text-white text-[9px] font-black px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-white dark:ring-slate-800 shadow-xs animate-pulse">
                                    {{ $pendingAssignments->count() }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-indigo-600 transition-colors">LMS Belajar</span>
                    </a>
                    <a href="{{ route('student.lms.gamification') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-amber-100 dark:border-amber-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14l-1.5 8a4.5 4.5 0 01-4.5 4.5h-2A4.5 4.5 0 016.5 11L5 3zM6 6H3v2a3 3 0 003 3M18 6h3v2a3 3 0 01-3 3M12 15.5V18M9 21h6"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-amber-600 transition-colors">Leaderboard</span>
                    </a>
                    <a href="{{ route('student.lms.live') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-rose-100 dark:border-rose-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-rose-600 transition-colors">Sesi Live</span>
                    </a>
                    <a href="{{ route('student.lms.analytics') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-purple-100 dark:border-purple-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-purple-600 transition-colors">Analisis Radar</span>
                    </a>
                </div>
            </div>

            <!-- Group 2: Pelajaran & Tugas -->
            <div class="space-y-3">
                <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center space-x-1.5 pb-1 border-b border-slate-100 dark:border-slate-800">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Pelajaran & Tugas</span>
                </h4>
                <div class="grid grid-cols-4 gap-y-4 gap-x-2">
                    <a href="{{ route('student.schedules.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-emerald-100 dark:border-emerald-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-emerald-600 transition-colors">Jadwal Mapel</span>
                    </a>
                    <a href="{{ route('student.materials.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-emerald-100 dark:border-emerald-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            @if(($materials->count() ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 bg-indigo-500 text-white text-[9px] font-black px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-white dark:ring-slate-800 shadow-xs">
                                    {{ $materials->count() }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-emerald-600 transition-colors">Materi Modul</span>
                    </a>
                    <a href="{{ route('student.assignments.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-lime-50 dark:bg-lime-950/40 text-lime-600 dark:text-lime-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-lime-100 dark:border-lime-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            @if(($pendingAssignments->count() ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 bg-amber-500 text-white text-[9px] font-black px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-white dark:ring-slate-800 shadow-xs animate-pulse">
                                    {{ $pendingAssignments->count() }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-lime-600 transition-colors">Tugas & PR</span>
                    </a>
                </div>
            </div>

            <!-- Group 3: Ujian & Evaluasi -->
            <div class="space-y-3">
                <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center space-x-1.5 pb-1 border-b border-slate-100 dark:border-slate-800">
                    <svg class="w-4 h-4 text-fuchsia-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Ujian & Raport</span>
                </h4>
                <div class="grid grid-cols-4 gap-y-4 gap-x-2">
                    <a href="{{ route('student.exams.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-fuchsia-50 dark:bg-fuchsia-950/40 text-fuchsia-600 dark:text-fuchsia-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-fuchsia-100 dark:border-fuchsia-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-fuchsia-600 transition-colors">Ujian CBT</span>
                    </a>
                    <a href="{{ route('student.raport.print') }}" target="_blank" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-emerald-100 dark:border-emerald-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-6-3h12"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-emerald-600 transition-colors">E-Raport</span>
                    </a>
                    <a href="{{ route('student.permits.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-yellow-50 dark:bg-yellow-950/40 text-yellow-600 dark:text-yellow-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-yellow-100 dark:border-yellow-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-yellow-600 transition-colors">Pengajuan Izin</span>
                    </a>
                    <a href="{{ route('student.grades.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-teal-100 dark:border-teal-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-teal-600 transition-colors">Laporan Nilai</span>
                    </a>
                </div>
            </div>

            <!-- Group 4: Keuangan & Kantin -->
            <div class="space-y-3">
                <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center space-x-1.5 pb-1 border-b border-slate-100 dark:border-slate-800">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Keuangan & Kantin</span>
                </h4>
                <div class="grid grid-cols-4 gap-y-4 gap-x-2">
                    <a href="{{ route('student.canteen.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-orange-100 dark:border-orange-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 2a2 2 0 1 0 0 4 2 2 0 0 0-4 0"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-orange-600 transition-colors">E-Kantin</span>
                    </a>
                    <a href="{{ route('student.savings.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-cyan-50 dark:bg-cyan-950/40 text-cyan-600 dark:text-cyan-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-cyan-100 dark:border-cyan-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-cyan-600 transition-colors">Tabungan</span>
                    </a>
                    <a href="{{ route('student.payments.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-rose-100 dark:border-rose-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            @if(($unpaidBillsCount ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-black px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-white dark:ring-slate-800 shadow-xs animate-pulse">
                                    {{ $unpaidBillsCount }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-rose-600 transition-colors">Tagihan & SPP</span>
                    </a>
                </div>
            </div>

            <!-- Group 5: Fasilitas & Akun -->
            <div class="space-y-3">
                <h4 class="text-[11px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center space-x-1.5 pb-1 border-b border-slate-100 dark:border-slate-800">
                    <svg class="w-4 h-4 text-sky-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Fasilitas & Akun</span>
                </h4>
                <div class="grid grid-cols-4 gap-y-4 gap-x-2">
                    <a href="{{ route('student.library.index') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-sky-100 dark:border-sky-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-sky-600 transition-colors">E-Library</span>
                    </a>
                    <a href="{{ route('student.announcements') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-purple-100 dark:border-purple-800/40">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if(($announcements->count() ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-black px-1.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full ring-2 ring-white dark:ring-slate-800 shadow-xs animate-pulse">
                                    {{ $announcements->count() }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-purple-600 transition-colors">Pengumuman</span>
                    </a>
                    <a href="{{ route('student.profile') }}" class="flex flex-col items-center justify-center text-center group">
                        <div class="relative w-12 h-12 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 transition-transform border border-slate-200 dark:border-slate-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">Profil Saya</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-800 shrink-0">
            <button type="button" onclick="closeAllMenusModal()" class="w-full py-2.5 bg-primary hover:bg-secondary text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-sm cursor-pointer border border-primary/30">
                Tutup Menu
            </button>
        </div>
    </div>
</div>

<!-- QR Code Full Screen Modal -->
<div id="qrModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 hidden flex items-center justify-center p-4 transition-all duration-300" onclick="closeQrModalOutside(event)">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col transform transition-all border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100" onclick="event.stopPropagation()">
        
        <!-- Header Kartu Pelajar (Header Kop Sekolah) -->
        <div class="bg-gradient-to-r from-indigo-900 via-indigo-850 to-slate-900 text-white p-5 relative overflow-hidden shrink-0">
            <!-- Background Glow & Pattern -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
            
            <!-- Close Button -->
            <button type="button" onclick="closeQrModal()" class="absolute top-3.5 right-3.5 z-20 w-8 h-8 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-all border border-white/20 backdrop-blur-sm cursor-pointer" title="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="flex items-center space-x-3 pr-8">
                @if(Setting::get('logo_path'))
                    <img src="{{ asset(Setting::get('logo_path')) }}" alt="Logo" class="h-11 w-11 rounded-xl object-contain bg-white/10 p-1 border border-white/20 shrink-0 shadow-md">
                @else
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-11 w-11 rounded-xl object-cover bg-white/10 p-1 border border-white/20 shrink-0 shadow-md">
                @endif
                <div class="min-w-0">
                    <h3 class="font-extrabold text-sm text-white tracking-tight leading-snug truncate">{{ Setting::get('school_name', 'Sekolah') }}</h3>
                    <p class="text-[9px] text-indigo-200 font-extrabold uppercase tracking-widest mt-0.5">KARTU TANDA SISWA DIGITAL</p>
                </div>
            </div>
        </div>

        <!-- Body Kartu Nama Siswa -->
        <div class="p-5 text-center space-y-4">
            
            <!-- Profil Foto + Informasi Identitas Siswa -->
            <div class="bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-4 flex items-center gap-3.5 text-left shadow-xs">
                <div class="relative shrink-0">
                    @if($student?->photo_url)
                        <img src="{{ $student->photo_url }}" alt="Foto Siswa" class="w-14 h-14 rounded-2xl object-cover border-2 border-white dark:border-slate-700 shadow-md">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=ffffff&size=160" alt="Foto Siswa" class="w-14 h-14 rounded-2xl object-cover border-2 border-white dark:border-slate-700 shadow-md">
                    @endif
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full flex items-center justify-center" title="Status Aktif"></span>
                </div>

                <div class="min-w-0 flex-1 space-y-0.5">
                    <h4 class="font-extrabold text-slate-900 dark:text-white text-sm truncate leading-tight">{{ $user->name }}</h4>
                    <p class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold font-mono">NISN: {{ $student?->nisn ?? '-' }}</p>
                    <div class="flex items-center gap-1.5 pt-0.5 flex-wrap">
                        <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold rounded-md">
                            {{ $student?->class?->name ?? 'Tanpa Kelas' }}
                        </span>
                        @if($student?->major?->name)
                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-bold rounded-md truncate max-w-[120px]">
                                {{ $student->major->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Box QR Code Presensi Vector -->
            <div class="bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-4 inline-block shadow-xs w-full">
                <div class="bg-white dark:bg-slate-950 p-3 rounded-xl border border-slate-200 dark:border-slate-800 inline-block shadow-sm">
                    @if(isset($qrCode) && $qrCode)
                        <div class="w-48 h-48 mx-auto flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:mx-auto">
                            {!! $qrCode !!}
                        </div>
                    @elseif($student?->qr_code_path)
                        <img src="{{ \Illuminate\Support\Str::startsWith($student->qr_code_path, 'img/') ? asset($student->qr_code_path) : asset('img/' . $student->qr_code_path) }}" alt="QR Code Absensi" class="w-48 h-48 object-contain mx-auto">
                    @else
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($student?->nisn ?? $user->email) }}" alt="QR Code Absensi" class="w-48 h-48 object-contain mx-auto">
                    @endif
                </div>

                <div class="mt-2.5">
                    <span class="inline-block px-3 py-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-mono font-bold text-xs rounded-full shadow-xs">
                        NISN: {{ $student?->nisn ?? '-' }}
                    </span>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Scan QR Code ini untuk Presensi Kehadiran</p>
                </div>
            </div>

            <!-- Footer Badge Validitas Kartu -->
            <div class="flex items-center justify-between text-[10px] text-slate-400 px-1 pt-1 border-t border-slate-100 dark:border-slate-800">
                <span class="flex items-center gap-1 font-semibold text-slate-500 dark:text-slate-400">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Kartu Pelajar Sah & Aktif
                </span>
                <span class="font-mono text-slate-400">T.A {{ date('Y') }}/{{ date('Y')+1 }}</span>
            </div>

        </div>

        <!-- Footer Action Button -->
        <div class="px-5 pb-5 pt-0">
            <button type="button" onclick="closeQrModal()" class="w-full py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-sm cursor-pointer border border-slate-700">
                Tutup Kartu
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function openQrModal() {
        document.getElementById('qrModal').classList.remove('hidden');
    }
    function closeQrModal() {
        document.getElementById('qrModal').classList.add('hidden');
    }
    function closeQrModalOutside(e) {
        if (e.target.id === 'qrModal') {
            closeQrModal();
        }
    }

    function openAllMenusModal() {
        document.getElementById('allMenusModal').classList.remove('hidden');
    }
    function closeAllMenusModal() {
        document.getElementById('allMenusModal').classList.add('hidden');
    }
    function closeAllMenusModalOutside(e) {
        if (e.target.id === 'allMenusModal') {
            closeAllMenusModal();
        }
    }
</script>
@endpush
@endsection
