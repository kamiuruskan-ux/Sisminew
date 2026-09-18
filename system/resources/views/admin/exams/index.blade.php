@extends('layouts.admin')

@section('title', 'Ujian CBT Online')
@section('page_title', 'Manajemen Ujian CBT (Computer Based Test)')

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(id, title) {
        this.deleteTarget = { id: id, name: title };
        this.deleteFormAction = '{{ url('admin/exams') }}/' + id;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Ujian CBT', 'message' => 'Apakah Anda yakin ingin menghapus ujian :name ini? Data nilai siswa terkait akan ikut terhapus.'])

    <!-- Header Summary & Quick Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-boxdark p-4 sm:p-6 rounded-2xl border border-slate-200 dark:border-strokedark shadow-xs">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 dark:bg-slate-800 dark:border-slate-700 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Kuis & Ujian CBT Online</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kelola bank soal, durasi waktu, penetapan kelas, dan pantau hasil ujian siswa secara otomatis.</p>
            </div>
        </div>
        <div class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-2">
            <a href="{{ route('admin.cbt-capacity.index') }}" class="w-full sm:w-auto px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-indigo-600 dark:text-indigo-400 font-bold rounded-xl text-xs transition-all flex items-center justify-center gap-2 border border-indigo-100 dark:border-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                </svg>
                <span>Analisis Server CBT</span>
            </a>
            <a href="{{ route('admin.exams.create') }}" class="w-full sm:w-auto btn-primary justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Ujian Baru</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
        <div class="bg-white dark:bg-boxdark p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-strokedark shadow-2xs flex items-center space-x-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 dark:bg-slate-800 dark:border-slate-700 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Total Ujian</p>
                <p class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $exams->count() }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-boxdark p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-strokedark shadow-2xs flex items-center space-x-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 dark:bg-slate-800 dark:border-slate-700 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Ujian Aktif / Rilis</p>
                <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $exams->where('is_published', true)->count() }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-boxdark p-4 sm:p-5 rounded-2xl border border-slate-200 dark:border-strokedark shadow-2xs flex items-center space-x-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 dark:bg-slate-800 dark:border-slate-700 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Total Pengerjaan</p>
                <p class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $exams->sum(fn($e) => $e->results->count()) }}</p>
            </div>
        </div>
    </div>

    <!-- Exam Table Card -->
    <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-strokedark flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 dark:text-white text-sm uppercase tracking-wider">Daftar Paket Ujian CBT</h3>
        </div>

        <!-- Desktop View (Visible on Medium screens & above) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Judul & Mata Pelajaran</th>
                        <th>Peruntukan Kelas</th>
                        <th>Durasi</th>
                        <th>Jumlah Soal</th>
                        <th>Peserta Mengikuti</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td>
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white text-sm hover:text-indigo-600 transition-colors">
                                        <a href="{{ route('admin.exams.show', $exam->id) }}">{{ $exam->title }}</a>
                                    </p>
                                    <span class="inline-flex items-center text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        {{ $exam->subject_name }}
                                    </span>
                                    @if($exam->topic)
                                        <div class="mt-1">
                                            <a href="{{ route('admin.lms.chapters.index') }}" class="inline-flex items-center space-x-1 text-[10px] bg-purple-50 text-purple-700 dark:bg-purple-950/40 dark:text-purple-300 font-semibold px-2 py-0.5 rounded border border-purple-200 dark:border-purple-800 transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                <span>Sub-Bab LMS: {{ $exam->topic->title }}</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($exam->class)
                                    <span class="px-2.5 py-1 text-xs font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-lg border border-indigo-100 dark:border-indigo-900/60">
                                        {{ $exam->class->name }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg">
                                        Semua Kelas
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="inline-flex items-center text-xs font-bold text-slate-700 dark:text-slate-300 gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $exam->duration_minutes }} Menit
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400 rounded-lg border border-purple-100 dark:border-purple-900/60 gap-1">
                                    <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ $exam->questions->count() }} Soal
                                </span>
                            </td>
                            <td>
                                <div>
                                    <span class="inline-flex items-center text-xs font-bold text-emerald-600 dark:text-emerald-400 gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ $exam->results->whereIn('status', ['completed', 'needs_grading'])->count() }} Siswa
                                    </span>
                                    @php 
                                        $pendingGradingResults = $exam->results->where('status', 'needs_grading');
                                        $needsGradingCount = $pendingGradingResults->count(); 
                                    @endphp
                                    @if($needsGradingCount > 0)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center space-x-1 text-[10px] font-extrabold bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300 px-2 py-0.5 rounded-md border border-amber-300 dark:border-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                                <span>{{ $needsGradingCount }} Menunggu Koreksi</span>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($exam->is_published)
                                    <span class="px-2.5 py-1 text-[11px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 rounded-full">
                                        Dipublikasikan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[11px] font-extrabold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 rounded-full">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($needsGradingCount > 0)
                                        <a href="{{ route('admin.exams.grade-all', $exam->id) }}" class="px-3 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-xl transition-all shadow-sm font-extrabold text-xs flex items-center space-x-1.5 shrink-0 animate-pulse" title="Koreksi Jawaban Essay Siswa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Koreksi Essay ({{ $needsGradingCount }})</span>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.exams.show', $exam->id) }}" class="p-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-indigo-600 dark:text-indigo-400 rounded-xl transition-colors font-bold text-xs flex items-center space-x-1" title="Bank Soal & Hasil">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>Kelola</span>
                                    </a>
                                    <a href="{{ route('admin.exams.edit', $exam->id) }}" class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors" title="Edit Ujian">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button type="button" @click="confirmDelete({{ $exam->id }}, '{{ addslashes($exam->title) }}')" class="p-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-950/80 text-rose-600 dark:text-rose-400 rounded-xl transition-colors cursor-pointer" title="Hapus Ujian">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                         </svg>
                                     </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400 dark:text-slate-500 font-medium">
                                Belum ada ujian CBT yang dibuat. Klik tombol "Buat Ujian Baru" di atas untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile List View (Visible on Mobile only) -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-strokedark">
            @forelse($exams as $exam)
                @php 
                    $mPendingGradingResults = $exam->results->where('status', 'needs_grading');
                    $mNeedsGradingCount = $mPendingGradingResults->count(); 
                @endphp
                <div class="p-4 space-y-3 bg-white dark:bg-boxdark">
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-1">
                            <a href="{{ route('admin.exams.show', $exam->id) }}" class="font-extrabold text-slate-900 dark:text-white text-sm hover:text-indigo-600 transition-colors line-clamp-2">
                                {{ $exam->title }}
                            </a>
                            <span class="inline-flex items-center text-xs font-semibold text-slate-500 dark:text-slate-400">
                                <svg class="w-3.5 h-3.5 text-indigo-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                {{ $exam->subject_name }}
                            </span>
                        </div>
                        <div class="shrink-0">
                            @if($exam->is_published)
                                <span class="px-2 py-0.5 text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 rounded-full">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-extrabold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 rounded-full">
                                    Draft
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Grid info -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="flex items-center text-slate-600 dark:text-slate-400 font-medium">
                            <span class="text-slate-400 mr-1.5">Kelas:</span>
                            @if($exam->class)
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $exam->class->name }}</span>
                            @else
                                <span class="text-slate-500">Semua Kelas</span>
                            @endif
                        </div>
                        <div class="flex items-center text-slate-600 dark:text-slate-400 font-medium">
                            <span class="text-slate-400 mr-1.5">Durasi:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $exam->duration_minutes }} mnt</span>
                        </div>
                        <div class="flex items-center text-slate-600 dark:text-slate-400 font-medium">
                            <span class="text-slate-400 mr-1.5">Soal:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $exam->questions->count() }}</span>
                        </div>
                        <div class="flex items-center text-slate-600 dark:text-slate-400 font-medium">
                            <span class="text-slate-400 mr-1.5">Ikut:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $exam->results->whereIn('status', ['completed', 'needs_grading'])->count() }} siswa</span>
                        </div>
                    </div>

                    <!-- Actions row -->
                    <div class="pt-2 border-t border-slate-50 dark:border-strokedark space-y-2">
                        @if($mNeedsGradingCount > 0)
                            <a href="{{ route('admin.exams.grade-all', $exam->id) }}" class="w-full py-2 px-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-xl transition-all font-extrabold text-xs flex items-center justify-center space-x-1.5 shadow-xs animate-pulse">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Koreksi Essay ({{ $mNeedsGradingCount }} Siswa)</span>
                            </a>
                        @endif

                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.exams.show', $exam->id) }}" class="flex-1 mr-2 py-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-indigo-600 dark:text-indigo-400 rounded-xl transition-colors font-bold text-xs flex items-center justify-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span>Kelola</span>
                            </a>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.exams.edit', $exam->id) }}" class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors" title="Edit Ujian">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button type="button" @click="confirmDelete({{ $exam->id }}, '{{ addslashes($exam->title) }}')" class="p-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-950/80 text-rose-600 dark:text-rose-400 rounded-xl transition-colors cursor-pointer" title="Hapus Ujian">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 dark:text-slate-500 font-medium">
                    Belum ada ujian CBT yang dibuat. Klik tombol "Buat Ujian Baru" di atas untuk menambahkan.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
