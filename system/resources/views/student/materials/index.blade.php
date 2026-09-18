@extends('layouts.student-mobile')

@section('title', 'Materi Pembelajaran')
@section('header_title', 'Materi Pembelajaran')

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="{ searchQuery: '' }">
    <!-- Header Banner Card -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 p-4 sm:p-6 text-white shadow-xl shadow-indigo-500/20">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-2.5 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] sm:text-xs font-bold text-indigo-100 mb-2">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>MODUL & BAHAN AJAR DIGITAL</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight">Materi Pembelajaran</h1>
                <p class="text-[11px] sm:text-xs text-indigo-100 mt-1 max-w-md leading-relaxed">
                    Unduh dan pelajari modul, dokumen PDF, video, serta pranala bahan ajar yang diunggah oleh guru Anda.
                </p>
            </div>

            <!-- Total Counter Card -->
            <div class="flex items-center justify-between sm:justify-start gap-3 bg-white/10 backdrop-blur-md p-3 sm:p-4 rounded-2xl border border-white/20 shrink-0">
                <div>
                    <span class="block text-[9px] sm:text-[10px] text-indigo-200 font-bold uppercase tracking-wider">Total Modul</span>
                    <span class="text-lg sm:text-2xl font-black text-amber-300">{{ $materials->total() }} File</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                </div>
            </div>
        </div>

        <!-- Decorative Circles -->
        <div class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-40 h-40 rounded-full bg-purple-500/20 blur-xl pointer-events-none"></div>
    </div>

    <!-- Search Input Bar -->
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <input type="text" 
               x-model="searchQuery"
               placeholder="Cari judul materi, guru, atau mata pelajaran..." 
               class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-white dark:bg-slate-900 text-slate-800 dark:text-white placeholder-slate-400 text-xs sm:text-sm rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 focus:outline-none focus:border-indigo-500 shadow-sm transition">
    </div>

    <!-- Materials Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($materials as $material)
            @php
                $fileTypeStr = strtolower($material->file_type ?? '');
                $filePathStr = strtolower($material->file_path ?? '');
                $isPdf = $fileTypeStr === 'pdf' || \Illuminate\Support\Str::endsWith($filePathStr, '.pdf');
                $isVideo = str_contains($fileTypeStr, 'video') || \Illuminate\Support\Str::endsWith($filePathStr, ['.mp4', '.mkv', '.avi']) || !empty($material->external_link);
            @endphp
            <div x-show="searchQuery === '' || '{{ strtolower(addslashes($material->title . ' ' . $material->subject . ' ' . ($material->teacher ? $material->teacher->name : ''))) }}'.includes(searchQuery.toLowerCase())"
                 class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-4 group">
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center shrink-0 text-white shadow-md group-hover:scale-105 transition-transform
                                    {{ $isPdf ? 'bg-gradient-to-tr from-rose-600 to-red-500 shadow-rose-500/20' : ($isVideo ? 'bg-gradient-to-tr from-blue-600 to-cyan-500 shadow-blue-500/20' : 'bg-gradient-to-tr from-indigo-600 to-purple-600 shadow-indigo-500/20') }}">
                            @if($isPdf)
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @elseif($isVideo)
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                                @if($material->subject)
                                <span class="text-[9px] sm:text-[10px] font-black text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950 px-2 py-0.5 rounded-full border border-indigo-200/60 dark:border-indigo-800/60 uppercase tracking-wider">
                                    {{ $material->subject }}
                                </span>
                                @endif
                                <span class="text-[9px] sm:text-[10px] font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                    {{ $material->class ? $material->class->name : 'Semua Kelas' }}
                                </span>
                            </div>
                            <h3 class="font-black text-sm sm:text-base text-slate-900 dark:text-white leading-snug line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $material->title }}</h3>
                        </div>
                    </div>

                    @if($material->description)
                        <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">{{ $material->description }}</p>
                    @endif

                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-2.5 border-t border-slate-100 dark:border-slate-800">
                        <span class="flex items-center font-bold text-slate-700 dark:text-slate-300 truncate min-w-0 mr-2">
                            <svg class="w-3.5 h-3.5 mr-1 text-indigo-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="truncate">{{ $material->teacher ? $material->teacher->name : 'Guru' }}</span>
                        </span>
                        <span class="shrink-0 font-mono text-[10px] sm:text-[11px] text-slate-400">{{ $material->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    @if($material->file_path)
                        <a href="{{ route('student.materials.download', $material) }}" class="w-full inline-flex items-center justify-center space-x-2 text-xs bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white px-4 py-2.5 rounded-xl font-bold transition shadow-md shadow-indigo-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Unduh Modul File</span>
                        </a>
                    @elseif($material->external_link)
                        <a href="{{ $material->external_link }}" target="_blank" class="w-full inline-flex items-center justify-center space-x-2 text-xs bg-blue-600 hover:bg-blue-500 active:scale-95 text-white px-4 py-2.5 rounded-xl font-bold transition shadow-md shadow-blue-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <span>Buka Pranala Tautan ↗</span>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-12 border-2 border-dashed border-slate-200 dark:border-slate-800 text-center space-y-3">
                <div class="w-16 h-16 bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mx-auto shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-base font-black text-slate-800 dark:text-white">Belum Ada Materi Pembelajaran</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Guru kelas Anda belum mengunggah file atau pranala modul pembelajaran untuk saat ini.</p>
            </div>
        @endforelse
    </div>

    @if($materials->hasPages())
        <div class="mt-6">
            {{ $materials->links() }}
        </div>
    @endif
</div>
@endsection
