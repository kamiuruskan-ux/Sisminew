@extends('layouts.student-mobile')

@section('title', 'Jadwal Pelajaran')
@section('header_title', 'Jadwal Pelajaran')

@section('content')
@php
    $today = now()->format('l');
    $todaySchedules = $schedules->where('day', $today);
    $dayNames = daftar_hari_indo();
    $activeDay = request('day', 'all');
@endphp
<div class="space-y-5" x-data="{ selectedDay: '{{ request('day', 'all') }}' }">

    <!-- Header Banner: Class & Weekly Schedule Summary (Compact on Mobile) -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-indigo-900 via-indigo-850 to-slate-900 p-4 sm:p-6 text-white shadow-lg border border-indigo-700/40">
        <!-- Background Glows -->
        <div class="absolute -right-10 -bottom-10 w-40 h-40 sm:w-48 sm:h-48 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-blue-500/20 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex items-center justify-between gap-3">
            <div class="flex items-center space-x-3 sm:space-x-4 min-w-0">
                <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white flex items-center justify-center shrink-0 shadow-inner">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="text-base sm:text-2xl font-extrabold tracking-tight truncate mt-0.5">
                        {{ auth()->user()->student?->class?->name ?? 'Belum Ada Kelas' }}
                    </h2>
                    <p class="text-[11px] sm:text-xs text-indigo-200/80 mt-0.5 truncate">T.A {{ date('Y') }}/{{ date('Y')+1 }} • {{ $schedules->count() }} Mapel Mingguan</p>
                </div>
            </div>

            <div class="shrink-0">
                <div class="bg-white/10 backdrop-blur-md px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl border border-white/15 text-center">
                    <p class="text-[9px] sm:text-[10px] uppercase font-extrabold text-indigo-200 tracking-wider">Total</p>
                    <p class="text-sm sm:text-lg font-black text-white leading-tight mt-0.5">{{ $schedules->count() }} <span class="hidden sm:inline text-xs font-semibold">Mapel</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Highlight Box (If any schedule today) -->
    @if($todaySchedules->count() > 0)
        <div class="bg-gradient-to-r from-emerald-500/10 via-emerald-500/5 to-transparent dark:from-emerald-950/40 dark:via-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-emerald-500/30 shadow-xs">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-emerald-500/20">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    Jadwal Hari Ini <span class="hidden sm:inline ml-1 font-normal text-slate-500">({{ now()->translatedFormat('l, d M Y') }})</span>
                </h3>
                <span class="text-[10px] sm:text-xs font-extrabold text-emerald-700 dark:text-emerald-300 bg-emerald-500/20 px-2.5 py-0.5 sm:py-1 rounded-full border border-emerald-500/30">
                    {{ $todaySchedules->count() }} Mapel
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($todaySchedules as $schedule)
                    <div class="p-3.5 sm:p-4 bg-white dark:bg-slate-800/90 rounded-xl sm:rounded-2xl border border-emerald-500/30 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h4 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm truncate">{{ $schedule->subject }}</h4>
                                <span class="text-[9px] sm:text-[10px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800 shrink-0">Hari Ini</span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">{{ $schedule->name }}</p>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-between gap-1 text-[10px] sm:text-[11px]">
                            <span class="flex items-center font-extrabold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 px-2 py-0.5 rounded-md border border-indigo-100 dark:border-indigo-800 shrink-0">
                                <svg class="w-3 h-3 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }}
                            </span>
                            <span class="flex items-center text-slate-600 dark:text-slate-300 font-semibold bg-slate-100 dark:bg-slate-700/60 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700 truncate max-w-[50%]">
                                {{ \Illuminate\Support\Str::startsWith(strtolower($schedule->room), ['ruang', 'lab', 'lapangan', 'r.']) ? $schedule->room : 'Ruang ' . $schedule->room }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Mobile Day Filter Tabs Bar (Horizontal scroll on HP) -->
    <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-2 shadow-xs border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 px-0.5">
            <button @click="selectedDay = 'all'" 
                    :class="selectedDay === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all cursor-pointer">
                Semua Hari
            </button>
            @foreach($days as $dayCode => $dayName)
                @php
                    $count = $schedules->where('day', $dayCode)->count();
                    $isTodayDay = ($dayCode === $today);
                @endphp
                @if($count > 0)
                    <button @click="selectedDay = '{{ $dayCode }}'" 
                            :class="selectedDay === '{{ $dayCode }}' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition-all flex items-center gap-1.5 cursor-pointer">
                        <span>{{ $dayName }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full" 
                              :class="selectedDay === '{{ $dayCode }}' ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'">
                            {{ $count }}
                        </span>
                        @if($isTodayDay)
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        @endif
                    </button>
                @endif
            @endforeach
        </div>
    </div>

    <!-- All Schedules by Day in Responsive Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($days as $dayCode => $dayName)
            @php
                $daySchedules = $schedules->where('day', $dayCode);
                $isCurrentDay = ($dayCode === $today);
            @endphp
            @if($daySchedules->count() > 0)
                <div x-show="selectedDay === 'all' || selectedDay === '{{ $dayCode }}'"
                     x-transition
                     class="bg-white dark:bg-slate-800/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xs border {{ $isCurrentDay ? 'border-indigo-500/50 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-700' }} flex flex-col justify-between">
                    <div>
                        <!-- Header Kartu Hari -->
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100 dark:border-slate-700 mb-3">
                            <h3 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm flex items-center">
                                <span class="w-2.5 h-2.5 {{ $isCurrentDay ? 'bg-emerald-500 animate-pulse' : 'bg-indigo-600 dark:bg-indigo-400' }} rounded-full mr-2"></span>
                                Hari {{ $dayName }}
                                @if($isCurrentDay)
                                    <span class="ml-2 text-[9px] sm:text-[10px] font-extrabold text-emerald-600 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">Hari Ini</span>
                                @endif
                            </h3>
                            <span class="text-[10px] sm:text-[11px] font-extrabold text-indigo-600 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg border border-indigo-100 dark:border-indigo-800">
                                {{ $daySchedules->count() }} Mapel
                            </span>
                        </div>

                        <!-- Daftar Pelajaran Hari Ini -->
                        <div class="space-y-2.5">
                            @foreach($daySchedules as $schedule)
                                <div class="p-3 sm:p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-700/80 hover:border-indigo-300 dark:hover:border-indigo-500 transition-all space-y-1.5">
                                    <div class="flex items-start justify-between gap-2">
                                        <h4 class="font-extrabold text-slate-900 dark:text-white text-xs leading-snug truncate">{{ $schedule->subject }}</h4>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $schedule->name }}</p>

                                    <div class="flex items-center justify-between gap-1 text-[10px] sm:text-[11px] pt-1">
                                        <span class="flex items-center font-extrabold text-indigo-700 dark:text-indigo-300 bg-white dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700 shrink-0">
                                            <svg class="w-3 h-3 mr-1 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ date('H:i', strtotime($schedule->start_time)) }} - {{ date('H:i', strtotime($schedule->end_time)) }}
                                        </span>
                                        <span class="flex items-center text-slate-600 dark:text-slate-300 font-semibold bg-white dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700 truncate max-w-[50%]">
                                            {{ \Illuminate\Support\Str::startsWith(strtolower($schedule->room), ['ruang', 'lab', 'lapangan', 'r.']) ? $schedule->room : 'Ruang ' . $schedule->room }}
                                        </span>
                                    </div>

                                    <!-- Nama Guru -->
                                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 flex items-center pt-1.5 border-t border-slate-200/60 dark:border-slate-700/60 truncate">
                                        <svg class="w-3 h-3 mr-1.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span class="truncate">{{ $schedule->teacher }}</span>
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-full bg-white dark:bg-slate-800/90 rounded-2xl sm:rounded-3xl p-8 sm:p-10 shadow-xs border border-slate-200 dark:border-slate-700 text-center">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-slate-100 dark:bg-slate-700/60 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-slate-800 dark:text-white font-bold text-sm">Belum Ada Jadwal Pelajaran</p>
                <p class="text-xs text-slate-400 dark:text-slate-400 mt-1">Jadwal akan muncul setelah admin memperbarui data kelas Anda.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
