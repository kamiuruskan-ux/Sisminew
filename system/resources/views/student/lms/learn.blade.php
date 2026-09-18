@extends('layouts.student-mobile')

@section('title', $activeTopic ? $activeTopic->title : $chapter->title)

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="{
    activeTab: 'summary',
    showMobileCurriculum: false,
    watchSeconds: {{ $progress ? $progress->watch_seconds : 0 }},
    isCompleted: {{ $progress && $progress->is_completed ? 'true' : 'false' }},
    targetSeconds: {{ $activeTopic ? $activeTopic->duration_in_seconds : 10 }},
    showXpModal: false,
    earnedXp: {{ $activeTopic ? $activeTopic->xp_reward : 50 }},
    isQuizCorrect: true,
    studentXp: {{ $gamification->xp }},

    formatTime(sec) {
        let m = Math.floor(sec / 60);
        let s = sec % 60;
        return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
    },

    init() {
        if (!this.isCompleted) {
            setInterval(() => {
                this.watchSeconds += 1;
                if (this.watchSeconds >= this.targetSeconds && !this.isCompleted) {
                    this.completeTopic();
                }
            }, 1000);
        }
    },

    completeTopic() {
        fetch('{{ route('student.lms.complete-topic', $activeTopic ? $activeTopic->id : 0) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ watch_seconds: this.watchSeconds })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.earnedXp = data.awarded_xp > 0 ? data.awarded_xp : {{ $activeTopic ? $activeTopic->xp_reward : 50 }};
                this.isQuizCorrect = true;
                if (data.new_total_xp !== undefined) {
                    this.studentXp = data.new_total_xp;
                }
                this.isCompleted = true;
                this.showXpModal = true;
            }
        });
    },

    submitQuiz(quizId, option) {
        if (!option) return Promise.reject();
        return fetch('{{ url('/student/lms/submit-quiz') }}/' + quizId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ option: option })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.already_submitted) {
                this.earnedXp = data.awarded_xp;
                this.isQuizCorrect = !!data.is_correct;
                if (data.new_total_xp !== undefined) {
                    this.studentXp = data.new_total_xp;
                }
                this.showXpModal = true;
            }
            return data;
        });
    }
}">

    <!-- Top Course Player Header -->
    <div class="bg-slate-900 text-white rounded-2xl p-3 sm:p-4 shadow-xl border border-slate-800 space-y-2.5 sm:space-y-0 sm:flex sm:items-center sm:justify-between gap-3">
        <!-- Main Title & Breadcrumb Area -->
        <div class="flex items-center space-x-2.5 sm:space-x-3 min-w-0 flex-1">
            <a href="{{ route('student.lms.index') }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition shrink-0" title="Kembali ke Katalog">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-1.5 sm:space-x-2 truncate">
                    <span class="px-2 py-0.5 rounded bg-indigo-600 text-white text-[9px] sm:text-[10px] font-black uppercase tracking-wider shrink-0">{{ $chapter->subject }}</span>
                    <span class="text-[10px] sm:text-xs text-slate-400 font-mono truncate">{{ $chapter->title }}</span>
                </div>
                <h1 class="text-xs sm:text-base font-black text-white truncate mt-0.5">{{ $activeTopic ? $activeTopic->title : 'Pilih Modul Pembelajaran' }}</h1>
            </div>
        </div>

        <!-- Controls & Status Badges Row (Border Separator on Mobile) -->
        <div class="flex items-center justify-between sm:justify-end gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-800/80 shrink-0">
            <!-- Mobile Drawer Button -->
            <button @click="showMobileCurriculum = !showMobileCurriculum" class="lg:hidden px-2.5 py-1 rounded-xl bg-slate-800 hover:bg-slate-700 text-indigo-300 text-[10px] sm:text-xs font-bold border border-slate-700 flex items-center space-x-1 shrink-0">
                <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <span>Daftar Modul</span>
            </button>

            <div class="flex items-center space-x-1.5 sm:space-x-2 shrink-0">
                <!-- Status Badge (Clean Alpine x-show to prevent duplication) -->
                <span x-show="isCompleted" x-cloak class="px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center space-x-1 shrink-0">
                    <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Selesai (+{{ $activeTopic ? $activeTopic->xp_reward : 50 }} XP)</span>
                </span>
                <span x-show="!isCompleted" class="px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700 flex items-center space-x-1 shrink-0">
                    <svg class="w-3.5 h-3.5 text-indigo-400 animate-spin shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="hidden sm:inline">Sedang Mempelajari</span>
                    <span class="sm:hidden">Belajar</span>
                </span>

                <!-- Watch Timer -->
                @if($activeTopic)
                <span class="px-2 py-1 rounded-full bg-slate-800 text-indigo-300 font-mono text-[9px] sm:text-xs border border-slate-700 flex items-center space-x-1 shrink-0" title="Durasi Pembelajaran">
                    <svg class="w-3 h-3 text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="isCompleted ? 'Selesai' : formatTime(watchSeconds) + ' / ' + formatTime(targetSeconds)"></span>
                </span>
                @endif

                <!-- Gamification Level -->
                <span class="px-2 py-1 rounded-full bg-indigo-500/20 text-indigo-300 font-black text-[9px] sm:text-xs border border-indigo-500/30 shrink-0">
                    Lvl {{ $gamification->level }}
                </span>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer for Curriculum (Collapsible) -->
    <div x-show="showMobileCurriculum" x-collapse x-cloak class="lg:hidden bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-lg space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
            <h3 class="font-black text-xs text-slate-900 dark:text-white uppercase tracking-wider">Kurikulum {{ $chapter->subject }}</h3>
            <button @click="showMobileCurriculum = false" class="text-xs text-slate-400 hover:text-slate-200">Tutup</button>
        </div>
        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
            @foreach($allChapters as $chIndex => $chItem)
            <div class="space-y-1" x-data="{ openCh: {{ ($activeTopic && $activeTopic->lms_chapter_id == $chItem->id) ? 'true' : 'false' }} }">
                <button @click="openCh = !openCh" class="w-full p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 font-bold text-xs text-slate-800 dark:text-slate-200 flex items-center justify-between text-left">
                    <span class="truncate">Bab {{ $chIndex + 1 }}: {{ $chItem->title }}</span>
                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="openCh ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="openCh" x-collapse class="pl-2 space-y-1">
                    @foreach($chItem->topics as $tpIndex => $tpItem)
                    @php
                        $isTpActive = ($activeTopic && $activeTopic->id == $tpItem->id);
                        $isTpDone = ($tpItem->studentProgress && $tpItem->studentProgress->is_completed);
                    @endphp
                    <a href="{{ route('student.lms.learn', [$chItem->id, $tpItem->id]) }}"
                       class="p-2 rounded-lg text-xs font-medium flex items-center justify-between gap-2 {{ $isTpActive ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                        <div class="flex items-center space-x-2 min-w-0">
                            @if($isTpDone)
                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                            <span class="w-3 h-3 rounded-full border border-slate-400 shrink-0"></span>
                            @endif
                            <span class="truncate text-[11px]">{{ $tpIndex + 1 }}. {{ $tpItem->title }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Main PC Player Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
        
        <!-- Left Sidebar: Curriculum Accordion (On PC: Left Side) -->
        <div class="order-2 lg:order-1 lg:col-span-4 space-y-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4 lg:sticky lg:top-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-xs sm:text-sm text-slate-900 dark:text-white uppercase tracking-wider">Kurikulum {{ $chapter->subject }}</h3>
                    <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400">{{ count($allChapters) }} Bab</span>
                </div>

                <div class="space-y-2.5 max-h-[650px] overflow-y-auto pr-1 no-scrollbar">
                    @foreach($allChapters as $chIndex => $chItem)
                    <div class="space-y-1.5" x-data="{ openCh: {{ ($activeTopic && $activeTopic->lms_chapter_id == $chItem->id) ? 'true' : 'false' }} }">
                        <button @click="openCh = !openCh" class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 hover:bg-slate-100 dark:hover:bg-slate-800 font-extrabold text-xs text-slate-800 dark:text-slate-200 flex items-center justify-between text-left transition cursor-pointer">
                            <span class="truncate">Bab {{ $chIndex + 1 }}: {{ $chItem->title }}</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="openCh ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="openCh" x-collapse class="pl-2 space-y-1">
                            @foreach($chItem->topics as $tpIndex => $tpItem)
                            @php
                                $isTpActive = ($activeTopic && $activeTopic->id == $tpItem->id);
                                $isTpDone = ($tpItem->studentProgress && $tpItem->studentProgress->is_completed);
                            @endphp
                            <a href="{{ route('student.lms.learn', [$chItem->id, $tpItem->id]) }}"
                               class="p-2.5 rounded-xl text-xs font-bold transition flex items-center justify-between gap-2 {{ $isTpActive ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                <div class="flex items-center space-x-2 min-w-0">
                                    @if($isTpDone)
                                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                    <span class="w-4 h-4 rounded-full border border-slate-300 dark:border-slate-600 shrink-0"></span>
                                    @endif
                                    <span class="truncate text-xs">{{ $tpIndex + 1 }}. {{ $tpItem->title }}</span>
                                </div>

                                <div class="flex items-center space-x-1 shrink-0">
                                    @if(count($tpItem->assignments) > 0)
                                    <span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[9px]">Tugas</span>
                                    @endif
                                    @if(count($tpItem->quizzes) > 0)
                                    <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 text-[9px]">Kuis</span>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Area: Video Player + Description & Content Tabs Directly Below Video -->
        <div class="order-1 lg:order-2 lg:col-span-8 space-y-4 sm:space-y-5">
            @if($activeTopic)
            <!-- 1. Video / Media Box -->
            <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-black aspect-video shadow-2xl border border-slate-800">
                @if($activeTopic->video_url)
                    @if(\Illuminate\Support\Str::contains($activeTopic->video_url, ['youtube.com', 'youtu.be']))
                        @php
                            $embedUrl = str_replace('watch?v=', 'embed/', $activeTopic->video_url);
                            if (\Illuminate\Support\Str::contains($activeTopic->video_url, 'youtu.be/')) {
                                $id = last(explode('/', $activeTopic->video_url));
                                $embedUrl = "https://www.youtube.com/embed/" . $id;
                            }
                        @endphp
                        <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                    @else
                        <video controls class="w-full h-full object-cover">
                            <source src="{{ $activeTopic->video_url }}" type="video/mp4">
                        </video>
                    @endif
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center p-4 sm:p-6 text-center text-slate-400 space-y-2 sm:space-y-3 bg-gradient-to-b from-slate-900 to-slate-950">
                        <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-2xl bg-indigo-600/20 text-indigo-400 flex items-center justify-center border border-indigo-500/30 shrink-0">
                            <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-xs sm:text-base">Modul Bacaan & Latihan Pembelajaran</h4>
                            <p class="text-[11px] sm:text-xs text-slate-400 max-w-md mt-0.5">Topik ini tidak menyertakan pemutar video. Pelajari rangkuman, bahan ajar, serta kuis/tugas di bawah.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- 2. Content Tabs Directly Below Video Player -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <!-- Navigation Tabs + Prev/Next Topic Navigation -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 pb-3">
                    <!-- Tab Pills -->
                    <div class="flex space-x-1.5 sm:space-x-2 text-xs font-black overflow-x-auto pb-1 sm:pb-0 no-scrollbar">
                        <button @click="activeTab = 'summary'" 
                                class="px-3 py-2 rounded-xl shrink-0 whitespace-nowrap text-xs font-bold flex items-center space-x-1.5 transition cursor-pointer"
                                :class="activeTab === 'summary' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Deskripsi & Materi</span>
                        </button>

                        <button @click="activeTab = 'materials'" 
                                class="px-3 py-2 rounded-xl shrink-0 whitespace-nowrap text-xs font-bold flex items-center space-x-1.5 transition cursor-pointer"
                                :class="activeTab === 'materials' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Bahan Ajar ({{ count($activeTopic->materials) + ($activeTopic->summary_file ? 1 : 0) }})</span>
                        </button>

                        <button @click="activeTab = 'quizzes'" 
                                class="px-3 py-2 rounded-xl shrink-0 whitespace-nowrap text-xs font-bold flex items-center space-x-1.5 transition cursor-pointer"
                                :class="activeTab === 'quizzes' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Kuis Interaktif ({{ count($activeTopic->quizzes) }})</span>
                        </button>

                        <button @click="activeTab = 'assignments'" 
                                class="px-3 py-2 rounded-xl shrink-0 whitespace-nowrap text-xs font-bold flex items-center space-x-1.5 transition cursor-pointer"
                                :class="activeTab === 'assignments' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 dark:text-slate-400'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>Tugas Kelas ({{ count($activeTopic->assignments) }})</span>
                        </button>
                    </div>

                    <!-- Topic Navigation Controls (Prev / Next) -->
                    @if($prevTopic || $nextTopic)
                    <div class="flex items-center space-x-2 shrink-0 self-end sm:self-center">
                        @if($prevTopic)
                        <a href="{{ route('student.lms.learn', [$prevTopic['chapter_id'], $prevTopic['topic_id']]) }}" 
                           class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center space-x-1 transition border border-slate-200 dark:border-slate-700" 
                           title="Modul Sebelumnya">
                            <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            <span>Sebelumnya</span>
                        </a>
                        @endif

                        @if($nextTopic)
                        <a href="{{ route('student.lms.learn', [$nextTopic['chapter_id'], $nextTopic['topic_id']]) }}" 
                           class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold flex items-center space-x-1 transition shadow-md shadow-indigo-600/20 shrink-0" 
                           title="Modul Berikutnya">
                            <span>Berikutnya</span>
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- TAB 1: Summary / Description -->
                <div x-show="activeTab === 'summary'" class="space-y-4">
                    <div class="prose dark:prose-invert max-w-none text-xs leading-relaxed text-slate-700 dark:text-slate-300 space-y-2">
                        <h3 class="font-extrabold text-sm text-slate-900 dark:text-white">Tentang Modul Ini</h3>
                        <p>{{ $activeTopic->description ?: 'Topik pembelajaran ini dirancang untuk memberikan pemahaman menyeluruh tentang materi yang dibahas.' }}</p>
                    </div>
                </div>

                <!-- TAB 2: Materials & Downloads -->
                <div x-show="activeTab === 'materials'" class="space-y-3">
                    @if($activeTopic->summary_file)
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 flex items-center justify-between gap-3">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                PDF
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white truncate">File Rangkuman Utama</h4>
                                <p class="text-[11px] text-slate-500 truncate">Berkas ringkasan modul pembelajaran</p>
                            </div>
                        </div>
                        <a href="{{ \Illuminate\Support\Str::startsWith($activeTopic->summary_file, ['http://', 'https://', 'doc/', 'img/']) ? asset($activeTopic->summary_file) : asset('doc/' . $activeTopic->summary_file) }}" target="_blank" class="px-3 sm:px-4 py-1.5 sm:py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-sm transition shrink-0">
                            Unduh
                        </a>
                    </div>
                    @endif

                    @forelse($activeTopic->materials as $mat)
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                {{ $mat->file_type ?: 'DOC' }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $mat->title }}</h4>
                                <p class="text-[11px] text-slate-500 truncate">{{ $mat->description ?: 'Bahan ajar tambahan' }}</p>
                            </div>
                        </div>
                        @if($mat->file_path)
                        <a href="{{ \Illuminate\Support\Str::startsWith($mat->file_path, ['http://', 'https://', 'doc/', 'img/']) ? asset($mat->file_path) : (file_exists(public_path('doc/' . $mat->file_path)) ? asset('doc/' . $mat->file_path) : asset('img/' . $mat->file_path)) }}" target="_blank" class="px-3 py-1.5 bg-slate-800 dark:bg-slate-700 text-white text-xs font-bold rounded-xl hover:bg-slate-700 transition shrink-0">
                            Buka File
                        </a>
                        @elseif($mat->external_link)
                        <a href="{{ $mat->external_link }}" target="_blank" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-500 transition shrink-0">
                            Buka Link
                        </a>
                        @endif
                    </div>
                    @empty
                        @if(!$activeTopic->summary_file)
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada lampiran bahan ajar tambahan.</p>
                        @endif
                    @endforelse
                </div>

                <!-- TAB 3: CBT Quizzes & Practice Exams -->
                <div x-show="activeTab === 'quizzes'" class="space-y-4">
                    <!-- Section A: Interactive Quick Quizzes -->
                    @if($activeTopic->quizzes->isNotEmpty())
                    <div class="space-y-3">
                        <h4 class="font-black text-xs text-indigo-600 uppercase tracking-wider">Kuis Interaktif Topik</h4>
                        @foreach($activeTopic->quizzes as $qIndex => $qz)
                        @php
                            $studentAns = $qz->studentAnswer;
                            $hasAnswered = !is_null($studentAns);
                            $initialOpt = $hasAnswered ? $studentAns->selected_option : null;
                            $initialResult = $hasAnswered ? [
                                'is_correct' => (bool)$studentAns->is_correct,
                                'user_option' => $studentAns->selected_option,
                                'correct_option' => strtolower($qz->correct_option),
                                'explanation' => $qz->explanation,
                                'explanation_video' => $qz->explanation_video,
                                'awarded_xp' => (int)$studentAns->awarded_xp,
                            ] : null;
                        @endphp
                        <div class="p-3.5 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 space-y-3" 
                             x-data="{ 
                                selectedOpt: {{ json_encode($initialOpt) }}, 
                                submitted: {{ $hasAnswered ? 'true' : 'false' }}, 
                                quizResult: {{ json_encode($initialResult) }} 
                             }">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-black text-indigo-600">Soal {{ $qIndex + 1 }}</span>
                                    @if($hasAnswered)
                                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">Sudah Dijawab</span>
                                    @endif
                                </div>
                                <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 px-2 py-0.5 bg-amber-50 dark:bg-amber-950/50 rounded border border-amber-200/60 dark:border-amber-800/40">+{{ $qz->xp_reward }} XP</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800 dark:text-white leading-relaxed">{{ $qz->question }}</p>

                            <div class="grid grid-cols-1 gap-2">
                                @foreach(['a' => $qz->option_a, 'b' => $qz->option_b, 'c' => $qz->option_c, 'd' => $qz->option_d] as $optKey => $optVal)
                                @if($optVal)
                                <button type="button" 
                                        @click="if(!submitted) selectedOpt = '{{ $optKey }}'"
                                        :disabled="submitted"
                                        class="p-2.5 sm:p-3 rounded-xl border text-left text-xs font-bold transition flex items-center justify-between cursor-pointer"
                                        :class="selectedOpt === '{{ $optKey }}' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300'">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] uppercase font-black shrink-0"
                                              :class="selectedOpt === '{{ $optKey }}' ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-300 dark:border-slate-600 text-slate-500'">
                                            {{ $optKey }}
                                        </span>
                                        <span>{{ $optVal }}</span>
                                    </div>
                                    <div x-show="selectedOpt === '{{ $optKey }}'" class="text-indigo-600 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </button>
                                @endif
                                @endforeach
                            </div>

                            <!-- Explicit Submit Button -->
                            <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                <button type="button"
                                        @click="if(selectedOpt && !submitted) { submitted = true; submitQuiz({{ $qz->id }}, selectedOpt).then(res => { quizResult = res; }); }"
                                        :disabled="!selectedOpt || submitted"
                                        class="w-full sm:w-auto px-4 py-2.5 sm:py-2 rounded-xl font-extrabold text-xs transition flex items-center justify-center space-x-1.5 shadow-sm cursor-pointer"
                                        :class="submitted ? 'bg-emerald-600 text-white cursor-default opacity-90' : (selectedOpt ? 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-indigo-600/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-400 cursor-not-allowed')">
                                    <template x-if="submitted">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </template>
                                    <template x-if="!submitted">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    </template>
                                    <span x-text="submitted ? 'Jawaban Terkirim' : 'Kirim Jawaban'">Kirim Jawaban</span>
                                </button>
                                <span x-show="!selectedOpt && !submitted" class="text-[11px] text-slate-400 italic text-center sm:text-left">Pilih salah satu opsi terlebih dahulu</span>
                            </div>

                            <!-- Feedback Result Container -->
                            <template x-if="quizResult">
                                <div class="p-3.5 rounded-xl border mt-3 space-y-1.5"
                                     :class="quizResult.is_correct ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200' : 'bg-rose-50 dark:bg-rose-950/40 border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-200'">
                                    <div class="flex items-center space-x-2 font-black text-xs">
                                        <template x-if="quizResult.is_correct">
                                            <span class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>JAWABAN BENAR! (+<span x-text="quizResult.awarded_xp"></span> XP)</span>
                                            </span>
                                        </template>
                                        <template x-if="!quizResult.is_correct">
                                            <span class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>JAWABAN KURANG TEPAT (Kunci: <span class="uppercase" x-text="quizResult.correct_option"></span>)</span>
                                            </span>
                                        </template>
                                    </div>
                                    <p class="text-xs font-medium leading-relaxed" x-text="quizResult.explanation || 'Pembahasan telah diperbarui.'"></p>
                                </div>
                            </template>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($activeTopic->quizzes->isEmpty())
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada kuis interaktif di topik ini.</p>
                    @endif
                </div>

                <!-- TAB 4: Assignments / Homework for Class -->
                <div x-show="activeTab === 'assignments'" class="space-y-4">
                    @forelse($activeTopic->assignments as $asg)
                    @php
                        $submission = $asg->submissions ? $asg->submissions->first() : null;
                    @endphp
                    <div class="p-3.5 sm:p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-1.5">
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] uppercase">
                                Tenggat: {{ \Carbon\Carbon::parse($asg->due_date)->format('d M Y H:i') }}
                            </span>
                            @if($submission)
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500 text-white font-extrabold text-[10px]">
                                    Sudah Dikumpulkan {{ $submission->score !== null ? '(Nilai: ' . $submission->score . ')' : '' }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px]">
                                    Belum Dikumpulkan
                                </span>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">{{ $asg->title }}</h4>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 leading-relaxed">{{ $asg->description }}</p>
                        </div>
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center gap-2">
                            <span class="text-xs font-bold text-slate-500">Skor Maks: {{ $asg->max_score }}</span>
                            <a href="{{ route('student.assignments.show', $asg->id) }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                {{ $submission ? 'Lihat Pengumpulkan' : 'Kumpulkan Tugas' }}
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada tugas kelas terikat untuk topik ini.</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- XP Award Modal Popup -->
    <div x-show="showXpModal" 
         x-cloak 
         @click.self="showXpModal = false" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
        
        <div x-show="showXpModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-4"
             class="bg-gradient-to-b from-indigo-900 via-indigo-950 to-slate-950 text-white rounded-2xl sm:rounded-3xl max-w-[300px] sm:max-w-sm w-full p-5 sm:p-7 text-center space-y-3.5 sm:space-y-4 shadow-2xl border border-indigo-500/40 relative">
            
            <!-- Sparkle / Status Glowing Icon -->
            <div class="relative mx-auto w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center">
                <div class="absolute inset-0 rounded-full animate-ping" :class="isQuizCorrect ? 'bg-amber-400/30' : 'bg-rose-500/30'"></div>
                <div class="relative w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center shadow-lg shrink-0"
                     :class="isQuizCorrect ? 'bg-gradient-to-tr from-amber-400 to-amber-300 text-amber-950 shadow-amber-400/40' : 'bg-gradient-to-tr from-rose-500 to-rose-400 text-white shadow-rose-500/40'">
                    <template x-if="isQuizCorrect">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 fill-current" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </template>
                    <template x-if="!isQuizCorrect">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </template>
                </div>
            </div>

            <div class="space-y-1">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-black uppercase tracking-widest border"
                      :class="isQuizCorrect ? 'bg-amber-400/20 text-amber-300 border-amber-400/30' : 'bg-rose-500/20 text-rose-300 border-rose-500/30'">
                    <span x-text="isQuizCorrect ? 'Pemberitahuan Poin' : 'Hasil Kuis'"></span>
                </span>
                <h3 class="text-lg sm:text-2xl font-black tracking-tight text-white" x-text="isQuizCorrect ? 'LUAR BIASA!' : 'TETAP SEMANGAT!'">LUAR BIASA!</h3>
                <p class="text-[11px] sm:text-xs text-indigo-200 leading-snug" x-text="isQuizCorrect ? 'Selamat! Kamu mendapatkan tambahan poin pengalaman!' : 'Jawaban kamu kurang tepat. Pelajari pembahasan di bawah!'"></p>
            </div>

            <div class="py-2.5 px-4 sm:py-3 sm:px-6 bg-white/10 rounded-xl sm:rounded-2xl inline-block border border-white/20 shadow-inner">
                <span class="text-2xl sm:text-3xl font-black drop-shadow-md" :class="isQuizCorrect ? 'text-amber-300' : 'text-slate-300'">+<span x-text="earnedXp"></span> XP</span>
            </div>

            <button @click="showXpModal = false" class="w-full py-2.5 sm:py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 active:scale-95 text-white font-black text-xs sm:text-sm rounded-xl transition-all shadow-lg shadow-indigo-600/40">
                <span x-text="isQuizCorrect ? 'Lanjutkan Belajar' : 'Lihat Pembahasan'"></span>
            </button>
        </div>
    </div>
</div>
@endsection
