@extends('layouts.admin')

@section('title', 'Tambah Ujian CBT Baru')
@section('page_title', 'Buat Ujian CBT Baru')

@section('content')
<div class="w-full space-y-6">
    <!-- Header Card -->
    <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Buat Ujian CBT Baru</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Lengkapi formulir di bawah ini untuk mengkonfigurasi paket ujian baru.</p>
            </div>
        </div>
        <a href="{{ route('admin.exams.index') }}" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors text-center inline-flex items-center justify-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Ujian</span>
        </a>
    </div>

    <!-- Main Form Card -->
    <form method="POST" action="{{ route('admin.exams.store') }}" class="space-y-6">
        @csrf

        <!-- Section 1: Informasi Utama & Pengajar -->
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center space-x-2.5 border-b border-slate-100 dark:border-slate-800 pb-3.5">
                <span class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold">1</span>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm uppercase tracking-wider">Informasi Utama & Guru Pengampu</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Judul Ujian / Kuis <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Contoh: Ujian Tengah Semester (UTS) Matematika Wajib Kelas XI" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm font-semibold transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Mata Pelajaran <span class="text-rose-500">*</span></label>
                        @php
                            $subjList = (isset($subjects) && count($subjects) > 0) ? $subjects : ($globalSubjects ?? []);
                        @endphp
                        <select name="subject_name" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm font-semibold transition-all">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjList as $sub)
                                @php $subVal = is_object($sub) ? $sub->name : $sub; @endphp
                                <option value="{{ $subVal }}" {{ old('subject_name') == $subVal ? 'selected' : '' }}>
                                    {{ $subVal }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-teacher-select-search 
                            :teachers="$teachers ?? []" 
                            name="teacher_id" 
                            value-type="id" 
                            :selected="old('teacher_id')" 
                            label="Guru Pengampu / Pengajar Ujian" 
                            placeholder="-- Cari & Pilih Guru Pengampu --" 
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Target Peruntukan Siswa & LMS -->
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center space-x-2.5 border-b border-slate-100 dark:border-slate-800 pb-3.5">
                <span class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-bold">2</span>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm uppercase tracking-wider">Peruntukan Target Siswa & Tautan LMS</h3>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                    <x-major-class-select major-label="Peruntukan Jurusan" class-label="Peruntukan Kelas" select-class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm font-semibold transition-all" label-class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2" />
                </div>

                <!-- LMS Sub-Bab Link -->
                <x-lms-topic-select-search 
                    :chapters="$lmsChapters ?? []" 
                    name="lms_topic_id" 
                    :selected="old('lms_topic_id')" 
                    accent-color="amber"
                    label="Tautkan ke Sub-Bab Modul LMS (Opsional)" 
                />
            </div>
        </div>

        <!-- Section 3: Waktu & Petunjuk Ujian -->
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center space-x-2.5 border-b border-slate-100 dark:border-slate-800 pb-3.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">3</span>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm uppercase tracking-wider">Pengaturan Durasi & Jadwal Pengerjaan</h3>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Durasi (Menit) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="duration_minutes" required min="5" value="{{ old('duration_minutes', 60) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm font-bold transition-all">
                            <span class="absolute right-3.5 top-3 text-xs font-bold text-slate-400">Menit</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Waktu Buka Ujian (Opsional)</label>
                        <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-xs font-medium transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Waktu Selesai (Opsional)</label>
                        <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-xs font-medium transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Deskripsi / Petunjuk Pengerjaan</label>
                    <textarea name="description" rows="3" placeholder="Tuliskan petunjuk pengerjaan ujian untuk siswa, misalnya aturan kalkulator, tata tertib, dsb..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm font-medium transition-all">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Section 4: Status Publikasi Card -->
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark p-5 sm:p-6 shadow-xs">
            <div class="p-4 bg-slate-50 dark:bg-boxdark-2 rounded-xl border border-slate-200 dark:border-slate-700/70 flex items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">Status Publikasi Ujian</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Jika diaktifkan, paket ujian ini langsung dapat diakses oleh siswa sesuai peruntukan kelas.</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
        </div>

        <!-- Action Footer -->
        <div class="pt-2 flex flex-col-reverse sm:flex-row justify-end items-center gap-3">
            <a href="{{ route('admin.exams.index') }}" class="w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-sm transition-colors text-center">
                Batal
            </a>
            <button type="submit" class="w-full sm:w-auto btn-primary justify-center shadow-lg shadow-indigo-500/20 py-3">
                <span>Simpan & Lanjut Tambah Soal Ujian →</span>
            </button>
        </div>
    </form>
</div>
@endsection
