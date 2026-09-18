@extends('layouts.student-mobile')

@section('title', 'Leaderboard & Prestasi XP')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Back Button Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.lms.index') }}" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center space-x-1 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
            <svg class="w-3.5 h-3.5 inline mr-1 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Ruang Belajar</span>
        </a>
    </div>

    <!-- Header Banner -->
    <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-amber-500 via-orange-500 to-amber-700 text-white shadow-xl shadow-amber-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] sm:text-xs font-bold text-amber-100 flex items-center space-x-1 w-max">
                <svg class="w-3.5 h-3.5 text-amber-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14l-1.5 8a4.5 4.5 0 01-4.5 4.5h-2A4.5 4.5 0 016.5 11L5 3zM6 6H3v2a3 3 0 003 3M18 6h3v2a3 3 0 01-3 3M12 15.5V18M9 21h6"/></svg>
                <span>GAMIFIKASI & LEADERBOARD</span>
            </span>
            <h1 class="text-xl sm:text-2xl font-black mt-2 tracking-tight">Papan Peringkat & Prestasi</h1>
            <p class="text-[11px] sm:text-xs text-amber-100 mt-1 leading-relaxed">Kumpulkan XP dari setiap modul video dan kuis untuk memuncaki papan peringkat!</p>
        </div>

        <div class="flex items-center justify-between sm:justify-start gap-3 bg-white/10 backdrop-blur-md p-3 sm:p-4 rounded-2xl border border-white/20 w-full sm:w-auto shrink-0">
            <div class="text-left sm:text-center">
                <span class="block text-[9px] sm:text-[10px] text-amber-100 font-bold uppercase tracking-wider">Peringkat Kamu Saat Ini</span>
                <span class="text-xl sm:text-2xl font-black text-white">#{{ $leaderboard->search(fn($item) => $item->student_id === $student->id) !== false ? $leaderboard->search(fn($item) => $item->student_id === $student->id) + 1 : '-' }}</span>
            </div>
            <span class="px-2.5 py-1 rounded-xl bg-white/20 text-xs font-black text-white sm:hidden">
                Top {{ count($leaderboard) }}
            </span>
        </div>
    </div>

    <!-- Student Stats & Badges Grid (3 columns on mobile) -->
    <div class="grid grid-cols-3 gap-2 sm:gap-4">
        <!-- Level Card -->
        <div class="tailadmin-card p-3 sm:p-5 text-center space-y-1.5 sm:space-y-2 border border-slate-200 dark:border-slate-800">
            <div class="w-8 h-8 sm:w-11 sm:h-11 mx-auto rounded-xl sm:rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-black flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <h4 class="text-[9px] sm:text-xs font-extrabold text-slate-500 uppercase truncate">Level</h4>
            <span class="text-sm sm:text-2xl font-black text-[#1C2434] dark:text-white block">Lvl {{ $gamification->level }}</span>
            <span class="text-[9px] sm:text-[10px] text-slate-400 block font-mono truncate">{{ $gamification->xp }} XP</span>
        </div>

        <!-- Total XP Card -->
        <div class="tailadmin-card p-3 sm:p-5 text-center space-y-1.5 sm:space-y-2 border border-slate-200 dark:border-slate-800">
            <div class="w-8 h-8 sm:w-11 sm:h-11 mx-auto rounded-xl sm:rounded-2xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-black flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-indigo-600 dark:text-indigo-400 fill-current" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h4 class="text-[9px] sm:text-xs font-extrabold text-slate-500 uppercase truncate">Total XP</h4>
            <span class="text-sm sm:text-2xl font-black text-indigo-600 dark:text-indigo-400 block truncate">{{ number_format($gamification->xp) }}</span>
            <span class="text-[9px] sm:text-[10px] text-slate-400 block font-mono truncate">Akumulasi</span>
        </div>

        <!-- Streak Card -->
        <div class="tailadmin-card p-3 sm:p-5 text-center space-y-1.5 sm:space-y-2 border border-slate-200 dark:border-slate-800">
            <div class="w-8 h-8 sm:w-11 sm:h-11 mx-auto rounded-xl sm:rounded-2xl bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-black flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-rose-600 dark:text-rose-400 fill-current" viewBox="0 0 24 24"><path d="M12 23c6.075 0 11-4.925 11-11 0-4.148-2.29-7.76-5.688-9.658-.337-.188-.758.056-.758.441v1.659c0 1.93-1.57 3.5-3.554 3.5H12c-1.984 0-3.554 1.57-3.554 3.5v.726c0 1.25-.626 2.378-1.637 3.056C5.556 16.032 5 17.447 5 19c0 2.209 3.134 4 7 4z"/></svg>
            </div>
            <h4 class="text-[9px] sm:text-xs font-extrabold text-slate-500 uppercase truncate">Streak</h4>
            <span class="text-sm sm:text-2xl font-black text-rose-600 dark:text-rose-400 block truncate">{{ $gamification->current_streak }} Hari</span>
            <span class="text-[9px] sm:text-[10px] text-slate-400 block truncate">Beruntun</span>
        </div>
    </div>

    <!-- Leaderboard Top 10 Table -->
    <div class="tailadmin-card overflow-hidden border border-slate-200 dark:border-slate-800">
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
            <h3 class="font-black text-[#1C2434] dark:text-white text-sm sm:text-base flex items-center space-x-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-500 inline mr-1 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14l-1.5 8a4.5 4.5 0 01-4.5 4.5h-2A4.5 4.5 0 016.5 11L5 3zM6 6H3v2a3 3 0 003 3M18 6h3v2a3 3 0 01-3 3M12 15.5V18M9 21h6"/></svg>
                <span>Top 10 Papan Peringkat Siswa</span>
            </h3>
            <span class="text-[11px] sm:text-xs text-slate-400 font-medium">Diperbarui Real-time</span>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-800">
            @foreach($leaderboard as $rankIndex => $lb)
            @php
                $isMe = ($lb->student_id === $student->id);
            @endphp
            <div class="p-3 sm:p-4 flex items-center justify-between gap-2.5 {{ $isMe ? 'bg-indigo-50/80 dark:bg-indigo-950/40 font-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-800/40' }}">
                <div class="flex items-center space-x-2.5 sm:space-x-4 min-w-0">
                    <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl font-black text-xs flex items-center justify-center shrink-0 {{ $rankIndex === 0 ? 'bg-amber-400 text-amber-950 text-sm sm:text-base shadow-md shadow-amber-400/40' : ($rankIndex === 1 ? 'bg-slate-300 text-slate-900' : ($rankIndex === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500')) }}">
                        {{ $rankIndex + 1 }}
                    </span>
                    <div class="min-w-0">
                        <h4 class="font-extrabold text-xs sm:text-sm text-[#1C2434] dark:text-white flex items-center space-x-1.5 truncate">
                            <span class="truncate">{{ $lb->student->user->name ?? 'Siswa' }}</span>
                            @if($isMe)
                            <span class="px-1.5 py-0.5 rounded bg-indigo-600 text-white text-[8px] sm:text-[9px] font-black shrink-0">KAMU</span>
                            @endif
                        </h4>
                        <span class="text-[10px] sm:text-xs text-slate-400 font-mono block truncate">Lvl {{ $lb->level }} • {{ $lb->current_streak }}d Streak</span>
                    </div>
                </div>

                <div class="text-right shrink-0">
                    <span class="text-xs sm:text-sm font-black text-amber-500 font-mono">+{{ number_format($lb->xp) }} XP</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
