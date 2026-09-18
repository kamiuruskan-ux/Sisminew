@extends('layouts.admin')

@section('title', 'Manajemen Modul LMS')
@section('page_title', 'Manajemen Konten LMS & Live Teaching')

@section('content')
<div class="space-y-8" x-data="{
    activeTab: 'chapters',
    showChapterModal: false,
    showTopicModal: false,
    showQuizModal: false,
    showAssignmentModal: false,
    showExamModal: false,
    showMaterialModal: false,
    showLiveModal: false,
    selectedChapterId: '',
    selectedTopicId: '',
    selectedTopicTitle: '',
    editChapterData: { id: '', subject: '', title: '', description: '', class_id: '', order: 0, cover_image: '' },
    isEditChapter: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(actionUrl, itemName) {
        this.deleteTarget = { name: itemName };
        this.deleteFormAction = actionUrl;
        this.showDeleteModal = true;
    },
    showUnlinkModal: false,
    unlinkFormAction: '',
    unlinkTargetName: '',
    unlinkTypeName: '',
    confirmUnlink(actionUrl, itemName, typeName = 'Konten') {
        this.unlinkFormAction = actionUrl;
        this.unlinkTargetName = itemName;
        this.unlinkTypeName = typeName;
        this.showUnlinkModal = true;
    },

    openAddChapter(subj = '') {
        this.isEditChapter = false;
        this.editChapterData = { id: '', subject: subj, title: '', description: '', class_id: '', order: 0, cover_image: '' };
        this.showChapterModal = true;
    },

    openEditChapter(ch) {
        this.isEditChapter = true;
        this.editChapterData = { id: ch.id, subject: ch.subject, title: ch.title, description: ch.description || '', class_id: ch.class_id || '', order: ch.order || 0, cover_image: ch.cover_image || '' };
        this.showChapterModal = true;
    },

    openAddTopic(chapterId) {
        this.selectedChapterId = chapterId;
        this.showTopicModal = true;
    },

    openAddQuiz(topicId, topicTitle) {
        this.selectedTopicId = topicId;
        this.selectedTopicTitle = topicTitle;
        this.showQuizModal = true;
    },

    openAddAssignment(topicId, topicTitle) {
        this.selectedTopicId = topicId;
        this.selectedTopicTitle = topicTitle;
        this.showAssignmentModal = true;
    },

    openAddExam(topicId, topicTitle) {
        this.selectedTopicId = topicId;
        this.selectedTopicTitle = topicTitle;
        this.showExamModal = true;
    },

    openAddMaterial(topicId, topicTitle) {
        this.selectedTopicId = topicId;
        this.selectedTopicTitle = topicTitle;
        this.showMaterialModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Konten LMS', 'message' => 'Apakah Anda yakin ingin menghapus :name ini? Data kuis & materi terkait akan ikut terhapus.'])

    <!-- Modal Confirm Unlink -->
    <template x-teleport="body">
        <div x-show="showUnlinkModal" x-cloak @click.self="showUnlinkModal = false" @keydown.escape.window="showUnlinkModal = false" class="fixed inset-0 z-[99999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#1A222C] rounded-2xl max-w-md w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800 text-center">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
                
                <div>
                    <h3 class="font-extrabold text-[#1C2434] dark:text-white text-lg">Lepas Tautan <span x-text="unlinkTypeName"></span></h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Apakah Anda yakin ingin melepas tautan <strong class="text-slate-800 dark:text-slate-200" x-text="unlinkTargetName"></strong> dari Sub-Bab ini?
                    </p>
                    <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-2 bg-amber-50 dark:bg-amber-950/40 p-2.5 rounded-xl border border-amber-200/60 dark:border-amber-800/50">
                        💡 Catatan: Data fisik/master tidak akan terhapus, hanya tautan dengan Sub-Bab LMS yang dilepaskan.
                    </p>
                </div>

                <form :action="unlinkFormAction" method="POST" class="flex justify-center space-x-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showUnlinkModal = false" class="px-4 py-2.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-rose-600/30">
                        Ya, Lepas Tautan
                    </button>
                </form>
            </div>
        </div>
    </template>

    <!-- Header Navigation Tabs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Kelola Kurikulum & Konten LMS Per Mata Pelajaran</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Atur Materi Utama Master per Pelajaran, Sub-Bab (Video Modul), Kuis Interaktif, Tugas Kelas, serta Live Class.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <button @click="openAddChapter()" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-md shadow-indigo-600/30 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Bab Baru</span>
            </button>
            <button @click="showLiveModal = true" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-md shadow-rose-600/30 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span>Jadwalkan Live Class</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 dark:border-slate-800 space-x-6 text-sm font-extrabold">
        <button @click="activeTab = 'chapters'" :class="activeTab === 'chapters' ? 'border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 pb-3' : 'text-slate-500 pb-3 hover:text-slate-800'">
            <svg class="w-4 h-4 inline mr-1 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span>Struktur Kurikulum & Sub-Bab ({{ $chapters->count() }})</span>
        </button>
        <button @click="activeTab = 'live'" :class="activeTab === 'live' ? 'border-b-2 border-rose-600 text-rose-600 dark:text-rose-400 pb-3' : 'text-slate-500 pb-3 hover:text-slate-800'">
            <svg class="w-4 h-4 inline mr-1 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <span>Sesi Live Teaching ({{ $liveClasses->count() }})</span>
        </button>
    </div>

    <!-- Tab 1: Chapters & Topics Accordion Grouped by Subject -->
    <div x-show="activeTab === 'chapters'" class="space-y-6">
        <!-- Subject & Class Filter Card -->
        <div class="tailadmin-card p-4 sm:p-5 bg-white dark:bg-slate-900/90 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <form action="{{ route('admin.lms.chapters.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
                <div class="w-full sm:w-72">
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Filter Mata Pelajaran</span>
                    </label>
                    <select name="subject" onchange="this.form.submit()" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-slate-800 dark:text-slate-200 font-semibold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach($globalSubjects ?? [] as $gSub)
                            <option value="{{ $gSub->name }}" {{ request('subject') == $gSub->name ? 'selected' : '' }}>{{ $gSub->name }} ({{ $gSub->category }})</option>
                        @endforeach
                    </select>
                </div>
                <x-major-class-select :selected-major="request('major_id')" :selected-class="request('class_id')" :is-filter="true" layout="inline" major-label="Filter Peruntukan Jurusan" class-label="Filter Peruntukan Kelas" select-class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-slate-800 dark:text-slate-200 font-semibold focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition" label-class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" on-major-change="this.form.submit()" on-class-change="this.form.submit()" />
                @if(request()->hasAny(['subject', 'class_id']))
                <div class="w-full sm:w-auto pt-2 sm:pt-6">
                    <a href="{{ route('admin.lms.chapters.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Reset Filter</span>
                    </a>
                </div>
                @endif
            </form>
        </div>

        @if($groupedChapters->isEmpty())
        <div class="tailadmin-card p-12 text-center space-y-3 bg-white dark:bg-slate-900/80 rounded-3xl border border-slate-200 dark:border-slate-800">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-800">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">Belum Ada Bab Pembelajaran</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Klik tombol 'Tambah Bab Baru' di atas untuk membuat modul belajar pertama.</p>
        </div>
        @else
        @foreach($groupedChapters as $subjectName => $subjectChapters)
        @php
            $totTopics = $subjectChapters->sum(fn($ch) => $ch->topics->count());
            $totQuizzes = $subjectChapters->sum(fn($ch) => $ch->topics->sum(fn($tp) => $tp->quizzes->count()));
            $totAssignments = $subjectChapters->sum(fn($ch) => $ch->topics->sum(fn($tp) => $tp->assignments->count()));
            $totExams = $subjectChapters->sum(fn($ch) => $ch->topics->sum(fn($tp) => $tp->exams->count()));
        @endphp
        <div class="space-y-4" x-data="{ groupOpen: true }">
            <!-- Subject Header Banner -->
            <div class="tailadmin-card p-4 sm:p-5 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-indigo-500/20 text-white rounded-3xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-md shadow-indigo-950/20">
                <div class="flex items-center space-x-3.5 cursor-pointer select-none" @click="groupOpen = !groupOpen">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-black text-base flex items-center justify-center border border-white/20 uppercase shadow-inner shrink-0">
                        {{ strtoupper(substr($subjectName, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center space-x-2.5">
                            <h2 class="text-base sm:text-lg font-black tracking-tight text-white uppercase">{{ $subjectName }}</h2>
                            <span class="px-3 py-0.5 rounded-full bg-white/10 dark:bg-slate-800/80 text-indigo-200 text-[11px] font-extrabold border border-white/15">
                                {{ $subjectChapters->count() }} Bab
                            </span>
                        </div>
                        <p class="text-xs text-indigo-200/80 mt-1 font-medium flex items-center gap-2 flex-wrap">
                            <span>{{ $totTopics }} Sub-Bab</span> • 
                            <span>{{ $totQuizzes }} Kuis Interaktif</span> • 
                            <span>{{ $totAssignments }} Tugas Kelas</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-2.5 self-end sm:self-center shrink-0">
                    <button @click="openAddChapter('{{ addslashes($subjectName) }}')" class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-black transition border border-white/20 flex items-center space-x-1.5 cursor-pointer shadow-xs" title="Tambah Bab Baru untuk {{ $subjectName }}">
                        <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span class="hidden sm:inline">Tambah Bab {{ $subjectName }}</span>
                        <span class="sm:hidden">Tambah Bab</span>
                    </button>
                    <button @click="groupOpen = !groupOpen" class="p-2 text-indigo-200 hover:text-white rounded-xl hover:bg-white/10 transition cursor-pointer">
                        <svg class="w-5 h-5 transform transition-transform duration-200" :class="groupOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Chapters in this Subject Group -->
            <div x-show="groupOpen" class="space-y-4 sm:pl-3 border-l-0 sm:border-l-2 border-indigo-500/20">
                @php
                    $chaptersByClassGroup = $subjectChapters->groupBy(function($ch) {
                        return $ch->class ? $ch->class->name : 'Master Utama (Semua Kelas)';
                    });
                @endphp

                @foreach($chaptersByClassGroup as $targetClassName => $classChapters)
                    @if($chaptersByClassGroup->count() > 1)
                    <div class="flex items-center space-x-3 pt-3 pb-1">
                        <div class="px-3 py-1 rounded-xl text-xs font-black flex items-center space-x-1.5 shadow-2xs {{ $targetClassName === 'Master Utama (Semua Kelas)' ? 'bg-slate-800 text-white dark:bg-slate-700' : 'bg-emerald-600 text-white dark:bg-emerald-700' }}">
                            @if($targetClassName === 'Master Utama (Semua Kelas)')
                                <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            @else
                                <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 01-.491-6.347A48.627 48.627 0 0112 2.094e-7a48.627 48.627 0 018.232 3.803 60.437 60.437 0 01-.491 6.347m-15.482 0a50.57 50.57 0 00-2.658 8.14A59.905 59.905 0 0012 24a59.902 59.902 0 009.641-5.713 50.57 50.57 0 00-2.658-8.14M12 9a3 3 0 100-6 3 3 0 000 6z"/></svg>
                            @endif
                            <span>Peruntukan: {{ $targetClassName }}</span>
                            <span class="ml-1 opacity-80 font-semibold text-[11px]">({{ $classChapters->count() }} Bab)</span>
                        </div>
                        <div class="h-px bg-slate-200/80 dark:bg-slate-800 flex-1"></div>
                    </div>
                    @endif

                    @foreach($classChapters as $ch)
                    <div class="tailadmin-card overflow-hidden bg-white dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs" x-data="{ open: true }">
                        <!-- Chapter Header -->
                        <div class="px-5 py-4 bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center space-x-3.5 cursor-pointer select-none min-w-0" @click="open = !open">
                                @if($ch->cover_image)
                                <img src="{{ asset($ch->cover_image) }}" alt="{{ $ch->title }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0 shadow-2xs">
                                @else
                                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-700 text-white font-black text-xs flex items-center justify-center shadow-xs shrink-0">
                                    {{ $ch->order ?: $loop->iteration }}
                                </span>
                                @endif
                                <div class="min-w-0">
                                    <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                        @if($ch->class)
                                        <span class="px-2.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-extrabold text-[10px] border border-emerald-200/60 dark:border-emerald-800/60 inline-flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <span>Spesifik {{ $ch->class->name }}</span>
                                        </span>
                                        @else
                                        <span class="px-2.5 py-0.5 rounded-md bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-[10px] border border-slate-300/60 dark:border-slate-700 inline-flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                                            <span>Master Utama (Semua Kelas)</span>
                                        </span>
                                        @endif

                                        <span class="px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-extrabold text-[10px] border border-indigo-200/60 dark:border-indigo-800/60">
                                            {{ $ch->topics->count() }} Sub-Bab
                                        </span>
                                    </div>
                                    <h3 class="font-black text-slate-900 dark:text-white text-sm sm:text-base mt-1 truncate">{{ $ch->title }}</h3>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 shrink-0 self-end sm:self-center">
                                <button @click="openAddTopic({{ $ch->id }})" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>Tambah Sub-Bab</span>
                                </button>
                                <button @click="openEditChapter({{ json_encode($ch->only(['id', 'subject', 'title', 'description', 'class_id', 'order', 'cover_image'])) }})" class="p-2 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 dark:hover:bg-amber-900/80 text-amber-600 dark:text-amber-400 rounded-xl border border-amber-200/60 dark:border-amber-800/60 transition cursor-pointer shadow-2xs" title="Edit Bab Pembelajaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                </button>
                                <button type="button" @click="confirmDelete('{{ route('admin.lms.chapters.destroy', $ch->id) }}', 'Bab {{ addslashes($ch->title) }}')" class="p-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900/80 text-rose-600 dark:text-rose-400 rounded-xl border border-rose-200/60 dark:border-rose-800/60 transition cursor-pointer shadow-2xs" title="Hapus Bab Pembelajaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                    <!-- Topics List inside Chapter -->
                    <div x-show="open" class="divide-y divide-slate-100 dark:divide-slate-800">
                        @if($ch->topics->isEmpty())
                        <div class="p-6 text-center text-xs text-slate-400">
                            Belum ada Sub-Bab di Bab ini. Klik 'Tambah Sub-Bab' untuk menambahkan.
                        </div>
                        @else
                        @foreach($ch->topics as $tp)
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <div class="flex items-start space-x-3.5 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-800/60 shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center space-x-2 flex-wrap">
                                        <h4 class="font-extrabold text-slate-900 dark:text-white text-sm truncate">{{ $tp->title }}</h4>
                                        <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 rounded-md text-[10px] font-extrabold border border-amber-200/60 dark:border-amber-800/60 shrink-0">
                                            +{{ $tp->xp_reward }} XP
                                        </span>
                                        @if($tp->video_duration)
                                        <span class="text-[10px] text-slate-400 font-mono flex items-center shrink-0">
                                            <svg class="w-3 h-3 inline mr-1 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            <span>{{ $tp->video_duration }}</span>
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">{{ $tp->description ?: 'Tanpa deskripsi' }}</p>

                                    <!-- Attached Items Badges Chips -->
                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        <button @click="openAddQuiz({{ $tp->id }}, '{{ addslashes($tp->title) }}')" class="px-3 py-1 rounded-xl bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/80 text-indigo-700 dark:text-indigo-300 text-[11px] font-extrabold flex items-center space-x-1.5 border border-indigo-200/60 dark:border-indigo-800/60 transition cursor-pointer shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Kuis Soal ({{ $tp->quizzes->count() }})</span>
                                        </button>

                                        <button @click="openAddAssignment({{ $tp->id }}, '{{ addslashes($tp->title) }}')" class="px-3 py-1 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/80 text-emerald-700 dark:text-emerald-300 text-[11px] font-extrabold flex items-center space-x-1.5 border border-emerald-200/60 dark:border-emerald-800/60 transition cursor-pointer shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span>Tugas Kelas ({{ $tp->assignments->count() }})</span>
                                        </button>

                                        <button @click="openAddMaterial({{ $tp->id }}, '{{ addslashes($tp->title) }}')" class="px-3 py-1 rounded-xl bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/60 dark:hover:bg-purple-900/80 text-purple-700 dark:text-purple-300 text-[11px] font-extrabold flex items-center space-x-1.5 border border-purple-200/60 dark:border-purple-800/60 transition cursor-pointer shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>Bahan Ajar ({{ $tp->materials->count() }})</span>
                                        </button>
                                    </div>

                                    <!-- Linked Assignments List -->
                                    @if($tp->assignments->isNotEmpty())
                                        <div class="mt-3 space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-2">
                                            <p class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Tugas Kelas Terkait:</p>
                                            @foreach($tp->assignments as $asgn)
                                                <div class="flex items-center justify-between p-2 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/50 text-xs">
                                                    <div class="flex items-center space-x-2 min-w-0 pr-2">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                        <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $asgn->title }}</span>
                                                        <span class="text-[10px] text-slate-500 font-medium shrink-0">({{ $asgn->class->name ?? 'Semua Kelas' }})</span>
                                                    </div>
                                                    <div class="flex items-center space-x-2 shrink-0">
                                                        <a href="{{ route('admin.assignments.show', \App\Helpers\IdEncrypter::encrypt($asgn->id)) }}" target="_blank" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg transition inline-flex items-center gap-1">
                                                            <span>Buka & Kelola Pengumpulan</span>
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                        </a>
                                                        <button type="button" 
                                                                @click="confirmUnlink('{{ route('admin.lms.topics.assignment.unlink', $asgn->id) }}', '{{ addslashes($asgn->title) }}', 'Tugas Kelas')" 
                                                                class="text-rose-600 hover:text-rose-800 font-bold text-[10px] underline cursor-pointer">
                                                            Lepas Tautan
                                                        </button>
                                                     </div>
                                                 </div>
                                             @endforeach
                                         </div>
                                     @endif

                                     <!-- Linked Materials List -->
                                     @if($tp->materials->isNotEmpty())
                                         <div class="mt-2.5 space-y-1.5 border-t border-slate-100 dark:border-slate-800 pt-2">
                                             <p class="text-[10px] font-black uppercase tracking-wider text-purple-700 dark:text-purple-400">Bahan Ajar Terkait:</p>
                                             @foreach($tp->materials as $mat)
                                                 <div class="flex items-center justify-between p-2 rounded-xl bg-purple-50/60 dark:bg-purple-950/40 border border-purple-200/60 dark:border-purple-800/50 text-xs">
                                                     <div class="flex items-center space-x-2 min-w-0 pr-2">
                                                         <svg class="w-3.5 h-3.5 text-purple-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                         <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $mat->title }}</span>
                                                         <span class="text-[10px] text-slate-500 font-medium shrink-0">({{ $mat->class->name ?? 'Semua Kelas' }})</span>
                                                     </div>
                                                     <div class="flex items-center space-x-2 shrink-0">
                                                         @if($mat->file_path)
                                                             <a href="{{ route('admin.materials.download', $mat) }}" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white font-bold text-[10px] rounded-lg transition inline-flex items-center gap-1">
                                                                 <span>Unduh Berkas</span>
                                                                 <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                             </a>
                                                         @elseif($mat->external_link)
                                                             <a href="{{ $mat->external_link }}" target="_blank" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[10px] rounded-lg transition inline-flex items-center gap-1">
                                                                 <span>Buka Link</span>
                                                                 <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                             </a>
                                                         @endif
                                                         <button type="button" 
                                                                 @click="confirmUnlink('{{ route('admin.lms.topics.material.unlink', $mat->id) }}', '{{ addslashes($mat->title) }}', 'Bahan Ajar')" 
                                                                 class="text-rose-600 hover:text-rose-800 font-bold text-[10px] underline cursor-pointer">
                                                             Lepas Tautan
                                                         </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 shrink-0">
                                <button type="button" @click="confirmDelete('{{ route('admin.lms.topics.destroy', $tp->id) }}', 'Sub-bab {{ addslashes($tp->title) }}')" class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 rounded-xl text-xs font-bold hover:bg-rose-100 dark:hover:bg-rose-900/80 transition cursor-pointer border border-rose-200/60 dark:border-rose-800/60">
                                    Hapus Sub-Bab
                                </button>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
                @endforeach
                @endforeach
            </div>
        </div>
        @endforeach
        @endif
    </div>

    <!-- Tab 2: Live Classes -->
    <div x-show="activeTab === 'live'" class="space-y-4">
        <div class="tailadmin-card overflow-hidden">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 class="font-extrabold text-[#1C2434] dark:text-white">Daftar Sesi Live Teaching</h3>
                <button @click="showLiveModal = true" class="px-4 py-2 bg-rose-600 text-white font-bold text-xs rounded-xl flex items-center space-x-1">
                    <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Sesi Live Baru</span>
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 dark:bg-slate-800 text-slate-500 uppercase font-bold">
                        <tr>
                            <th class="p-4">Mata Pelajaran & Judul</th>
                            <th class="p-4">Pengajar / Guru</th>
                            <th class="p-4">Jadwal & Durasi</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Tautan Meeting</th>
                            <th class="p-4 text-right">Aksi Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($liveClasses as $lc)
                        <tr>
                            <td class="p-4 font-bold text-[#1C2434] dark:text-white">
                                <span class="text-[10px] px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded block w-max uppercase mb-1">{{ $lc->subject }}</span>
                                {{ $lc->title }}
                            </td>
                            <td class="p-4 font-semibold text-slate-600 dark:text-slate-300">
                                {{ $lc->teacher->name ?? '-' }}
                            </td>
                            <td class="p-4 text-slate-500 font-mono flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 inline mr-1 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span>{{ $lc->scheduled_at->format('d M Y H:i') }} ({{ $lc->duration_minutes }} Mins)</span>
                            </td>
                            <td class="p-4">
                                @if($lc->status === 'live')
                                <span class="px-2.5 py-1 bg-rose-500 text-white font-extrabold text-[10px] rounded-full animate-pulse flex items-center space-x-1 w-max">
                                    <span class="w-2 h-2 rounded-full bg-white inline-block"></span>
                                    <span>LIVE NOW</span>
                                </span>
                                @elseif($lc->status === 'scheduled')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 font-bold text-[10px] rounded-full flex items-center space-x-1 w-max">
                                    <svg class="w-3 h-3 inline text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span>TERJADWAL</span>
                                </span>
                                @else
                                <span class="px-2.5 py-1 bg-slate-200 text-slate-700 font-bold text-[10px] rounded-full">SELESAI</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono text-indigo-600 truncate max-w-[200px]">
                                {{ $lc->meeting_url ?: 'Default Jitsi Room' }}
                            </td>
                            <td class="p-4 text-right">
                                <form action="{{ route('admin.lms.live-classes.status', $lc->id) }}" method="POST" class="inline">
                                    @csrf @method('PUT')
                                    @if($lc->status === 'scheduled')
                                    <input type="hidden" name="status" value="live">
                                    <button class="px-3 py-1 bg-rose-600 text-white font-bold rounded-lg text-xs">Mulai Live</button>
                                    @elseif($lc->status === 'live')
                                    <input type="hidden" name="status" value="ended">
                                    <button class="px-3 py-1 bg-slate-700 text-white font-bold rounded-lg text-xs">Akhiri Sesi</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Chapter -->
    <template x-teleport="body">
        <div x-show="showChapterModal" x-cloak @click.self="showChapterModal = false" @keydown.escape.window="showChapterModal = false" class="fixed inset-0 z-[99999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#1A222C] rounded-2xl max-w-lg w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800">
                <h3 class="font-extrabold text-[#1C2434] dark:text-white text-lg" x-text="isEditChapter ? 'Edit Bab Pembelajaran' : 'Tambah Bab Pembelajaran Baru'"></h3>
                <form :action="isEditChapter ? '{{ url('admin/lms/chapters') }}/' + editChapterData.id : '{{ route('admin.lms.chapters.store') }}'" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <template x-if="isEditChapter"><input type="hidden" name="_method" value="PUT"></template>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Mata Pelajaran</label>
                        <select name="subject" x-model="editChapterData.subject" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($globalSubjects ?? [] as $gSub)
                            <option value="{{ $gSub->name }}">{{ $gSub->name }} ({{ $gSub->category }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Judul Bab</label>
                        <input type="text" name="title" x-model="editChapterData.title" placeholder="Contoh: Bab 1: Biologi Sel" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi Bab (Ringkasan Materi)</label>
                        <textarea name="description" x-model="editChapterData.description" rows="2" placeholder="Penjelasan singkat mengenai materi dalam Bab ini..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Upload Sampul Bab (Gambar / Banner LMS)</label>
                        <input type="file" name="cover_image" accept="image/*" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2 text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                        <template x-if="editChapterData.cover_image">
                            <div class="mt-2 flex items-center space-x-2">
                                <img :src="'{{ asset('') }}' + editChapterData.cover_image" class="w-16 h-10 object-cover rounded-lg border border-slate-200 dark:border-slate-700 shadow-2xs">
                                <span class="text-[10px] text-slate-400 font-semibold">Sampul saat ini</span>
                            </div>
                        </template>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Peruntukan Kelas (Opsional)</label>
                        <select name="class_id" x-model="editChapterData.class_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                            <option value="">Semua Kelas (Master Utama Reusable)</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}">Spesifik {{ $c->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Jika dikosongkan, Bab ini menjadi Materi Utama Master yang dapat diakses oleh seluruh kelas.</p>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="showChapterModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl">Simpan Bab</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Topic -->
    <template x-teleport="body">
        <div x-show="showTopicModal" x-cloak @click.self="showTopicModal = false" @keydown.escape.window="showTopicModal = false" class="fixed inset-0 z-[99999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#1A222C] rounded-2xl max-w-lg w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800">
                <h3 class="font-extrabold text-[#1C2434] dark:text-white text-lg">Tambah Sub-Bab (Modul Materi)</h3>
                <form action="{{ route('admin.lms.topics.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="lms_chapter_id" :value="selectedChapterId">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Judul Sub-Bab / Materi</label>
                        <input type="text" name="title" placeholder="Contoh: 1.1 Struktur dan Organel Sel" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Tautan Video (YouTube Embed / MP4 URL)</label>
                        <input type="url" name="video_url" placeholder="https://www.youtube.com/embed/..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Durasi Pembelajaran</label>
                            <input type="time" name="video_duration" value="00:10:00" step="1" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                            <span class="text-[10px] text-slate-400 mt-0.5 block">Pilih Jam : Menit : Detik</span>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Hadiah XP</label>
                            <input type="number" name="xp_reward" value="50" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Berkas Rangkuman PDF / Dokumen (Opsional)</label>
                        <x-file-upload name="summary_file" accept=".pdf,.docx,.pptx" label="Upload File Rangkuman Modul" help="PDF, DOCX, atau PPTX (Maks 10MB)" />
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="showTopicModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl">Simpan Sub-Bab</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Quiz (Tambah Soal Kuis Interaktif - Modern UI) -->
    <template x-teleport="body">
        <div x-show="showQuizModal" x-cloak @click.self="showQuizModal = false" @keydown.escape.window="showQuizModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99999] bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-4">
            
            <div x-show="showQuizModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="bg-white dark:bg-[#1A222C] rounded-3xl max-w-lg w-full p-6 space-y-5 border border-slate-200 dark:border-slate-800 shadow-2xl max-h-[90vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-600/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base sm:text-lg truncate">Tambah Soal Kuis Interaktif</h3>
                            <p class="text-xs text-indigo-600 dark:text-indigo-400 font-bold mt-0.5 truncate" x-text="'Sub-Bab: ' + selectedTopicTitle"></p>
                        </div>
                    </div>
                    <button type="button" @click="showQuizModal = false" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admin.lms.quizzes.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="lms_topic_id" :value="selectedTopicId">

                    <!-- Pertanyaan Soal -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pertanyaan Soal Kuis</label>
                        <textarea name="question" rows="3" required placeholder="Tuliskan teks pertanyaan kuis dengan jelas..."
                                  class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-3 text-xs text-[#1C2434] dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"></textarea>
                    </div>

                    <!-- Opsi Jawaban (Grid 2x2) -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pilihan Jawaban (Opsi A - D)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 w-5 h-5 rounded-lg bg-indigo-600 text-white font-black text-[10px] flex items-center justify-center uppercase">A</span>
                                    <input type="text" name="option_a" required placeholder="Isi Opsi A"
                                           class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl py-2.5 pl-10 pr-3 text-xs text-[#1C2434] dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                                </div>
                            </div>
                            <div>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 w-5 h-5 rounded-lg bg-indigo-600 text-white font-black text-[10px] flex items-center justify-center uppercase">B</span>
                                    <input type="text" name="option_b" required placeholder="Isi Opsi B"
                                           class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl py-2.5 pl-10 pr-3 text-xs text-[#1C2434] dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                                </div>
                            </div>
                            <div>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 w-5 h-5 rounded-lg bg-indigo-600 text-white font-black text-[10px] flex items-center justify-center uppercase">C</span>
                                    <input type="text" name="option_c" required placeholder="Isi Opsi C"
                                           class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl py-2.5 pl-10 pr-3 text-xs text-[#1C2434] dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                                </div>
                            </div>
                            <div>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 w-5 h-5 rounded-lg bg-indigo-600 text-white font-black text-[10px] flex items-center justify-center uppercase">D</span>
                                    <input type="text" name="option_d" required placeholder="Isi Opsi D"
                                           class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl py-2.5 pl-10 pr-3 text-xs text-[#1C2434] dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kunci Jawaban & Poin XP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40">
                        <div>
                            <label class="block text-xs font-extrabold text-indigo-900 dark:text-indigo-200 mb-1">Kunci Jawaban Benar</label>
                            <select name="correct_option" required class="w-full bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-800/60 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white font-bold focus:ring-2 focus:ring-indigo-500 outline-none">
                                <option value="a">Opsi A</option>
                                <option value="b">Opsi B</option>
                                <option value="c">Opsi C</option>
                                <option value="d">Opsi D</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-amber-800 dark:text-amber-300 mb-1">Reward Poin XP (Jika Benar)</label>
                            <div class="relative flex items-center">
                                <input type="number" name="xp_reward" value="100" min="0" required
                                       class="w-full bg-white dark:bg-slate-800 border border-amber-300 dark:border-amber-700/60 rounded-xl py-2.5 pl-3 pr-10 text-xs font-extrabold text-[#1C2434] dark:text-white focus:ring-2 focus:ring-amber-500 outline-none">
                                <span class="absolute right-3 text-[10px] font-black text-amber-600 dark:text-amber-400">XP</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pembahasan -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Teks Pembahasan (Muncul setelah siswa menjawab)</label>
                        <textarea name="explanation" rows="2" placeholder="Tuliskan penjelasan atau langkah penyelesaian soal yang akan tampil setelah kuis dijawab..."
                                  class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-3 text-xs text-[#1C2434] dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-2.5 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                        <button type="button" @click="showQuizModal = false" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-xs font-extrabold rounded-xl shadow-md shadow-indigo-600/30 transition flex items-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Soal Kuis</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Assignment (Tugas Kelas) -->
    <template x-teleport="body">
        <div x-show="showAssignmentModal" x-cloak @click.self="showAssignmentModal = false" @keydown.escape.window="showAssignmentModal = false" class="fixed inset-0 z-[99999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#1A222C] rounded-2xl max-w-lg w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-[#1C2434] dark:text-white text-lg">Kelola Tugas Kelas Sub-Bab</h3>
                        <p class="text-xs text-emerald-600 font-bold mt-0.5" x-text="'Sub-Bab: ' + selectedTopicTitle"></p>
                    </div>
                    <button type="button" @click="showAssignmentModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admin.lms.topics.assignment.link') }}" method="POST" class="space-y-4"
                      x-data="{
                          selectedId: '',
                          searchOpen: true,
                          searchQuery: '',
                          items: [
                              @foreach($allAssignments ?? [] as $asgnOpt)
                                  { id: '{{ $asgnOpt->id }}', title: {{ json_encode($asgnOpt->title) }}, subject: {{ json_encode($asgnOpt->subject) }}, className: {{ json_encode($asgnOpt->class->name ?? 'Semua Kelas') }} },
                              @endforeach
                          ],
                          get selectedLabel() {
                              if (!this.selectedId) return '-- Cari & Pilih Tugas Kelas --';
                              const found = this.items.find(i => i.id == this.selectedId);
                              return found ? found.title + ' (' + found.subject + ' • ' + found.className + ')' : '-- Cari & Pilih Tugas Kelas --';
                          },
                          get filteredItems() {
                              if (!this.searchQuery) return this.items;
                              const q = this.searchQuery.toLowerCase();
                              return this.items.filter(i => 
                                  i.title.toLowerCase().includes(q) || 
                                  i.subject.toLowerCase().includes(q) || 
                                  i.className.toLowerCase().includes(q)
                              );
                          }
                      }">
                    @csrf
                    <input type="hidden" name="lms_topic_id" :value="selectedTopicId">
                    <input type="hidden" name="assignment_id" x-model="selectedId" required>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Pilih Tugas Kelas (/admin/assignments)</label>
                        <div>
                            <button type="button" @click="searchOpen = !searchOpen"
                                    class="w-full min-h-[42px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold flex items-center justify-between focus:ring-2 focus:ring-emerald-500 outline-none transition text-left">
                                <span x-text="selectedLabel" class="truncate" :class="!selectedId ? 'text-slate-400' : 'text-slate-800 dark:text-white font-bold'"></span>
                                <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2 transition-transform duration-200" :class="{ 'rotate-180': searchOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="searchOpen" x-cloak
                                 class="mt-2 w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl p-2.5 space-y-2 flex flex-col">
                                <div class="relative">
                                    <input type="text" x-model="searchQuery" placeholder="Ketik kata kunci untuk mencari tugas..." 
                                           class="w-full bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-700 text-xs rounded-xl p-2.5 pl-9 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>

                                <div class="overflow-y-auto space-y-1 max-h-48 pr-1">
                                    <template x-for="item in filteredItems" :key="item.id">
                                        <button type="button" @click="selectedId = item.id"
                                                :class="selectedId == item.id ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/30' : 'hover:bg-white dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200'"
                                                class="w-full text-left p-2.5 rounded-xl text-xs flex flex-col transition">
                                            <span class="font-bold" x-text="item.title"></span>
                                            <span class="text-[11px] text-slate-400" x-text="item.subject + ' • ' + item.className"></span>
                                        </button>
                                    </template>
                                    <div x-show="filteredItems.length === 0" class="p-3 text-center text-xs text-slate-400">
                                        Tidak ada tugas kelas yang cocok.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">Pilih tugas yang sudah pernah dibuat untuk ditautkan dengan Sub-Bab ini.</p>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="showAssignmentModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" :disabled="!selectedId" :class="!selectedId ? 'opacity-50 cursor-not-allowed' : ''" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition">Tautkan Tugas</button>
                    </div>
                </form>
            </div>
        </div>
    </template>



    <!-- Modal Material (Bahan Ajar) -->
    <template x-teleport="body">
        <div x-show="showMaterialModal" x-cloak @click.self="showMaterialModal = false" @keydown.escape.window="showMaterialModal = false" class="fixed inset-0 z-[99999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#1A222C] rounded-2xl max-w-lg w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-[#1C2434] dark:text-white text-lg">Tambah Bahan Ajar Pendukung</h3>
                        <p class="text-xs text-purple-600 font-bold mt-0.5" x-text="'Sub-Bab: ' + selectedTopicTitle"></p>
                    </div>
                    <button type="button" @click="showMaterialModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admin.lms.topics.material.link') }}" method="POST" class="space-y-4"
                      x-data="{
                          selectedId: '',
                          searchOpen: true,
                          searchQuery: '',
                          items: [
                              @foreach($allMaterials ?? [] as $matOpt)
                                  { id: '{{ $matOpt->id }}', title: {{ json_encode($matOpt->title) }}, subject: {{ json_encode($matOpt->subject) }}, className: {{ json_encode($matOpt->class->name ?? 'Semua Kelas') }} },
                              @endforeach
                          ],
                          get selectedLabel() {
                              if (!this.selectedId) return '-- Cari & Pilih Bahan Ajar --';
                              const found = this.items.find(i => i.id == this.selectedId);
                              return found ? found.title + ' (' + found.subject + ' • ' + found.className + ')' : '-- Cari & Pilih Bahan Ajar --';
                          },
                          get filteredItems() {
                              if (!this.searchQuery) return this.items;
                              const q = this.searchQuery.toLowerCase();
                              return this.items.filter(i => 
                                  i.title.toLowerCase().includes(q) || 
                                  i.subject.toLowerCase().includes(q) || 
                                  i.className.toLowerCase().includes(q)
                              );
                          }
                      }">
                    @csrf
                    <input type="hidden" name="lms_topic_id" :value="selectedTopicId">
                    <input type="hidden" name="material_id" x-model="selectedId" required>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">Pilih Bahan Ajar Dari /admin/materials</label>
                        <div>
                            <button type="button" @click="searchOpen = !searchOpen"
                                    class="w-full min-h-[42px] px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold flex items-center justify-between focus:ring-2 focus:ring-purple-500 outline-none transition text-left">
                                <span x-text="selectedLabel" class="truncate" :class="!selectedId ? 'text-slate-400' : 'text-slate-800 dark:text-white font-bold'"></span>
                                <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2 transition-transform duration-200" :class="{ 'rotate-180': searchOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="searchOpen" x-cloak
                                 class="mt-2 w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl p-2.5 space-y-2 flex flex-col">
                                <div class="relative">
                                    <input type="text" x-model="searchQuery" placeholder="Ketik kata kunci untuk mencari bahan ajar..." 
                                           class="w-full bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-700 text-xs rounded-xl p-2.5 pl-9 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>

                                <div class="overflow-y-auto space-y-1 max-h-48 pr-1">
                                    <template x-for="item in filteredItems" :key="item.id">
                                        <button type="button" @click="selectedId = item.id"
                                                :class="selectedId == item.id ? 'bg-purple-500/10 text-purple-600 dark:text-purple-400 font-bold border border-purple-500/30' : 'hover:bg-white dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200'"
                                                class="w-full text-left p-2.5 rounded-xl text-xs flex flex-col transition">
                                            <span class="font-bold" x-text="item.title"></span>
                                            <span class="text-[11px] text-slate-400" x-text="item.subject + ' • ' + item.className"></span>
                                        </button>
                                    </template>
                                    <div x-show="filteredItems.length === 0" class="p-3 text-center text-xs text-slate-400">
                                        Tidak ada bahan ajar yang cocok.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">Pilih bahan ajar yang sudah ada untuk ditautkan dengan Sub-Bab ini.</p>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="showMaterialModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" :disabled="!selectedId" :class="!selectedId ? 'opacity-50 cursor-not-allowed' : ''" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition">Tautkan Bahan Ajar</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Live Class -->
    <template x-teleport="body">
        <div x-show="showLiveModal" x-cloak @click.self="showLiveModal = false" @keydown.escape.window="showLiveModal = false" class="fixed inset-0 z-[99999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#1A222C] rounded-2xl max-w-lg w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800">
                <h3 class="font-extrabold text-[#1C2434] dark:text-white text-lg">Jadwalkan Live Teaching Class</h3>
                <form action="{{ route('admin.lms.live-classes.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Mata Pelajaran</label>
                        <select name="subject" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($globalSubjects ?? [] as $gSub)
                            <option value="{{ $gSub->name }}">{{ $gSub->name }} ({{ $gSub->category }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Judul Topik Live</label>
                        <input type="text" name="title" placeholder="Bedah Soal UTBK Logaritma" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 rounded-xl p-2.5 text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Waktu Mulai</label>
                            <input type="datetime-local" name="scheduled_at" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 rounded-xl p-2.5 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Durasi (Menit)</label>
                            <input type="number" name="duration_minutes" value="60" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 rounded-xl p-2.5 text-xs">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="showLiveModal = false" class="px-4 py-2 bg-slate-200 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl">Jadwalkan Live</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
