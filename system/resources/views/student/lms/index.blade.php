@extends('layouts.student-mobile')

@section('title', 'Ruang Belajar LMS')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header Banner & Gamification Status -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 p-4 sm:p-6 text-white shadow-xl shadow-indigo-500/20">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-1.5 sm:space-x-2 px-2.5 py-0.5 sm:py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] sm:text-xs font-bold text-indigo-100 mb-2">
                    <svg class="w-3.5 h-3.5 text-amber-300 fill-current shrink-0" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>RUANG BELAJAR DIGITAL LMS</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight">Halo, {{ \Illuminate\Support\Str::before($student->user->name, ' ') }}!</h1>
                <p class="text-[11px] sm:text-xs text-indigo-100 mt-1 max-w-md leading-relaxed">
                    Siap belajar hari ini? Tonton modul video interaktif, selesaikan kuis, dan tingkatkan XP kamu!
                </p>
            </div>

            <!-- Gamification Mini Card -->
            <div class="flex items-center justify-around sm:justify-start gap-1 sm:gap-3 bg-white/10 backdrop-blur-md p-2.5 sm:p-3.5 rounded-2xl border border-white/20 w-full sm:w-auto shrink-0">
                <div class="text-center px-2 sm:px-3 border-r border-white/20 flex-1 sm:flex-initial">
                    <span class="block text-[9px] sm:text-[10px] text-indigo-200 font-bold uppercase tracking-wider">Level</span>
                    <span class="text-base sm:text-xl font-black text-amber-300">Lvl {{ $gamification->level }}</span>
                </div>
                <div class="text-center px-2 sm:px-3 border-r border-white/20 flex-1 sm:flex-initial">
                    <span class="block text-[9px] sm:text-[10px] text-indigo-200 font-bold uppercase tracking-wider">Total XP</span>
                    <span class="text-base sm:text-xl font-black text-emerald-300">{{ number_format($gamification->xp) }}</span>
                </div>
                <div class="text-center px-2 sm:px-3 flex-1 sm:flex-initial">
                    <span class="block text-[9px] sm:text-[10px] text-indigo-200 font-bold uppercase tracking-wider">Streak</span>
                    <span class="text-base sm:text-xl font-black text-rose-300 flex items-center justify-center space-x-1">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-rose-400 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12 23c6.075 0 11-4.925 11-11 0-4.148-2.29-7.76-5.688-9.658-.337-.188-.758.056-.758.441v1.659c0 1.93-1.57 3.5-3.554 3.5H12c-1.984 0-3.554 1.57-3.554 3.5v.726c0 1.25-.626 2.378-1.637 3.056C5.556 16.032 5 17.447 5 19c0 2.209 3.134 4 7 4z"/></svg>
                        <span>{{ $gamification->current_streak }}d</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Decorative background circles -->
        <div class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-40 h-40 rounded-full bg-purple-500/20 blur-xl pointer-events-none"></div>
    </div>

    <!-- Quick Navigation Links (Gamification, Analytics, Live Class) -->
    <div class="grid grid-cols-3 gap-2 sm:gap-3">
        <a href="{{ route('student.lms.gamification') }}" class="p-2.5 sm:p-3.5 rounded-2xl bg-gradient-to-br from-amber-500/10 to-amber-500/5 border border-amber-500/20 hover:border-amber-500/40 transition text-center group">
            <div class="w-8 h-8 sm:w-9 sm:h-9 mx-auto rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold shadow-md shadow-amber-500/30 group-hover:scale-110 transition-transform shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3h14l-1.5 8a4.5 4.5 0 01-4.5 4.5h-2A4.5 4.5 0 016.5 11L5 3zM6 6H3v2a3 3 0 003 3M18 6h3v2a3 3 0 01-3 3M12 15.5V18M9 21h6"/></svg>
            </div>
            <span class="block text-[11px] sm:text-xs font-black text-slate-800 dark:text-white mt-1.5 sm:mt-2 truncate">Leaderboard</span>
            <span class="text-[9px] sm:text-[10px] text-slate-500 block truncate">Peringkat & Badge</span>
        </a>

        <a href="{{ route('student.lms.analytics') }}" class="p-2.5 sm:p-3.5 rounded-2xl bg-gradient-to-br from-indigo-500/10 to-indigo-500/5 border border-indigo-500/20 hover:border-indigo-500/40 transition text-center group">
            <div class="w-8 h-8 sm:w-9 sm:h-9 mx-auto rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold shadow-md shadow-indigo-600/30 group-hover:scale-110 transition-transform shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <span class="block text-[11px] sm:text-xs font-black text-slate-800 dark:text-white mt-1.5 sm:mt-2 truncate">Analisis Nilai</span>
            <span class="text-[9px] sm:text-[10px] text-slate-500 block truncate">Grafik Radar</span>
        </a>

        <a href="{{ route('student.lms.live') }}" class="p-2.5 sm:p-3.5 rounded-2xl bg-gradient-to-br from-rose-500/10 to-rose-500/5 border border-rose-500/20 hover:border-rose-500/40 transition text-center group relative">
            @if($liveClasses->where('status', 'live')->count() > 0)
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 rounded-full animate-ping"></span>
            @endif
            <div class="w-8 h-8 sm:w-9 sm:h-9 mx-auto rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold shadow-md shadow-rose-600/30 group-hover:scale-110 transition-transform shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <span class="block text-[11px] sm:text-xs font-black text-slate-800 dark:text-white mt-1.5 sm:mt-2 truncate">Live Class</span>
            <span class="text-[9px] sm:text-[10px] text-slate-500 block truncate">Sesi Interactive</span>
        </a>
    </div>

    <!-- Active Live Class Banner if available -->
    @if($liveClasses->isNotEmpty())
    <div class="p-3.5 sm:p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-3 h-3 rounded-full bg-rose-500 animate-pulse shrink-0"></div>
            <div>
                <span class="text-[10px] font-black uppercase text-rose-600 tracking-wider">Live Teaching Terdekat</span>
                <h4 class="font-black text-xs sm:text-sm text-slate-800 dark:text-white line-clamp-1">{{ $liveClasses->first()->title }}</h4>
                <p class="text-[11px] sm:text-xs text-slate-500 font-mono flex items-center mt-0.5">
                    <svg class="w-3.5 h-3.5 inline mr-1 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>{{ $liveClasses->first()->scheduled_at->format('d M H:i') }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('student.lms.live') }}" class="w-full sm:w-auto text-center px-3.5 py-2 bg-rose-600 hover:bg-rose-500 active:scale-95 text-white text-xs font-bold rounded-xl shadow-md shadow-rose-600/30 transition-all shrink-0">
            Masuk Live
        </a>
    </div>
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('lmsCatalogComponent', (initialChapters, subjectsList) => ({
                filterStatus: 'all',
                filterSubject: 'all',
                sortBy: 'recent',
                searchQuery: '',
                chapters: initialChapters || [],
                subjects: subjectsList || [],
                get filteredChapters() {
                    let query = this.searchQuery.trim().toLowerCase();
                    let list = this.chapters.filter(c => {
                        // Search query filter
                        if (query !== '') {
                            let matchSubject = (c.subject || '').toLowerCase().includes(query);
                            let matchTitle = (c.title || '').toLowerCase().includes(query);
                            let matchCategory = (c.category || '').toLowerCase().includes(query);
                            let matchDesc = (c.description || '').toLowerCase().includes(query);
                            if (!matchSubject && !matchTitle && !matchCategory && !matchDesc) return false;
                        }

                        // Filter Subject
                        if (this.filterSubject !== 'all') {
                            if (c.subject !== this.filterSubject) return false;
                        }

                        // Filter Status (Semua, Sedang Dipelajari, Belum Dimulai, Selesai)
                        if (this.filterStatus === 'in_progress') return c.percent > 0 && c.percent < 100;
                        if (this.filterStatus === 'not_started') return c.percent === 0;
                        if (this.filterStatus === 'completed') return c.percent === 100;
                        return true;
                    });

                    if (this.sortBy === 'name_asc') {
                        list.sort((a, b) => a.title.localeCompare(b.title));
                    } else if (this.sortBy === 'progress_desc') {
                        list.sort((a, b) => b.percent - a.percent);
                    } else {
                        list.sort((a, b) => (b.created_at || 0) - (a.created_at || 0));
                    }
                    return list;
                }
            }));
        });
    </script>

    <!-- Chapter Selection Catalog (Per Bab LMS) -->
    <div x-data="lmsCatalogComponent({{ json_encode(array_values($chapterProgress)) }}, {{ json_encode($subjectsList) }})" class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5 sm:space-y-6">
        
        <!-- Header Title Bar & Search Input Box -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center space-x-2.5">
                    <span>Modul & Bab Pembelajaran</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 text-xs font-black" x-text="filteredChapters.length"></span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pilih bab untuk mengakses rangkuman materi, video interaktif, kuis CBT, dan tugas kelas.</p>
            </div>

            <!-- Proportional Search Box -->
            <div class="relative w-full md:w-80 shrink-0">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari bab, mata pelajaran, atau topik..." class="w-full pl-10 pr-9 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-slate-800 transition shadow-sm">
                <button x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Secondary Filter Pills & Subject Dropdown & Sort Selector Row -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-1">
            <!-- Left Side: Filter Status Pills (Swipeable on Mobile) -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-0.5 whitespace-nowrap min-w-0 flex-1 -mx-1 px-1">
                <button @click="filterStatus = 'all'" 
                    :class="filterStatus === 'all' ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0">
                    Semua
                </button>
                <button @click="filterStatus = 'in_progress'" 
                    :class="filterStatus === 'in_progress' ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0">
                    Sedang Dipelajari
                </button>
                <button @click="filterStatus = 'not_started'" 
                    :class="filterStatus === 'not_started' ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0">
                    Belum Dimulai
                </button>
                <button @click="filterStatus = 'completed'" 
                    :class="filterStatus === 'completed' ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'" 
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0">
                    Selesai
                </button>
            </div>

            <!-- Right Side: Filter Mapel + Filter Terbaru (Proportional Grid on Mobile) -->
            <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 shrink-0 w-full md:w-auto">
                <!-- Subject Filter Dropdown (Left of Filter Terbaru) -->
                <div class="relative min-w-0">
                    <select x-model="filterSubject" class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs cursor-pointer truncate">
                        <option value="all">Semua Mapel</option>
                        <template x-for="subj in subjects" :key="subj">
                            <option :value="subj" x-text="subj"></option>
                        </template>
                    </select>
                </div>

                <!-- Sort Selector Dropdown -->
                <div class="relative min-w-0">
                    <select x-model="sortBy" class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-xs cursor-pointer truncate">
                        <option value="recent">Terbaru</option>
                        <option value="name_asc">Nama Bab (A - Z)</option>
                        <option value="progress_desc">Progres Tertinggi</option>
                    </select>
                </div>
            </div>
        </div>

        @if(empty($chapters) || count($chapters) == 0)
        <div class="p-8 sm:p-12 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800 text-center space-y-3 bg-slate-50/50 dark:bg-slate-900/50">
            <div class="w-16 h-16 mx-auto rounded-full bg-blue-50 dark:bg-blue-950 flex items-center justify-center text-blue-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h3 class="font-bold text-slate-700 dark:text-slate-200">Belum Ada Bab Pembelajaran</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">Modul pembelajaran sedang disiapkan oleh Guru. Silakan periksa kembali beberapa saat lagi.</p>
        </div>
        @else

        <!-- 3 Columns PC Grid Layout (Per Chapter Card) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            <template x-for="item in filteredChapters" :key="item.id">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                    <!-- Banner Header Image -->
                    <div class="h-36 sm:h-40 w-full overflow-hidden relative bg-slate-800 shrink-0">
                        <template x-if="item.cover_image">
                            <img :src="'{{ asset('') }}' + item.cover_image" :alt="item.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </template>
                        <template x-if="!item.cover_image">
                            <div class="w-full h-full bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-4 flex flex-col justify-between relative overflow-hidden group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-white/10 blur-md pointer-events-none"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-100 bg-white/20 px-2.5 py-0.5 rounded-full backdrop-blur-sm self-start" x-text="item.subject"></span>
                                <h4 class="text-white font-black text-lg line-clamp-1 drop-shadow-sm" x-text="item.title"></h4>
                            </div>
                        </template>

                        <!-- Subject Tag Overlay on Image -->
                        <div class="absolute top-3 left-3 flex items-center space-x-1.5">
                            <span class="px-2.5 py-0.5 rounded-full bg-indigo-600/90 text-white font-black text-[10px] uppercase tracking-wider backdrop-blur-md shadow-sm" x-text="item.subject"></span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                        <div class="space-y-2.5">
                            <!-- Chapter Title -->
                            <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition" :title="item.title" x-text="item.title"></h3>

                            <!-- Stats Badges Chips Row -->
                            <div class="flex flex-wrap items-center gap-1.5 text-[10px] font-bold">
                                <span class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200/50" x-text="item.total + ' Sub-Bab'"></span>
                                <span class="px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200/50" x-text="item.quizzes_count + ' Kuis'"></span>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/50" x-text="item.assignments_count + ' Tugas'"></span>
                            </div>

                            <!-- Short Description -->
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed h-9" x-text="item.description"></p>
                        </div>

                        <div class="pt-2 space-y-3">
                            <!-- Progress Bar Section -->
                            <div class="space-y-1">
                                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden flex items-center justify-between">
                                    <div class="bg-blue-600 h-full rounded-full transition-all duration-500" :style="'width: ' + item.percent + '%'"></div>
                                </div>
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-slate-500" x-text="item.completed + ' / ' + item.total + ' selesai'"></span>
                                    <span class="text-slate-700 dark:text-slate-300 font-mono font-black" x-text="item.percent + '%'"></span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div>
                                <template x-if="item.percent > 0 && item.percent < 100">
                                    <a :href="'{{ url('student/lms/learn') }}/' + item.id" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-600/20 transition-all text-center block">
                                        Lanjutkan Belajar
                                    </a>
                                </template>
                                <template x-if="item.percent === 100">
                                    <a :href="'{{ url('student/lms/learn') }}/' + item.id" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition-all text-center block">
                                        Selesai (Ulas Kembali)
                                    </a>
                                </template>
                                <template x-if="item.percent === 0">
                                    <a :href="'{{ url('student/lms/learn') }}/' + item.id" class="w-full py-2.5 px-4 bg-white dark:bg-slate-900 border border-blue-600 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/50 active:scale-95 font-bold text-xs rounded-xl transition-all text-center block">
                                        Mulai Kursus
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Fallback message if filtered results empty -->
        <div x-show="filteredChapters.length === 0" class="p-8 text-center text-slate-500 text-xs font-semibold">
            Tidak ada Bab pembelajaran yang cocok dengan filter yang dipilih.
        </div>
        @endif
    </div>
</div>
@endsection
