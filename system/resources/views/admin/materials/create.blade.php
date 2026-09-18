@extends('layouts.admin')

@section('title', 'Tambah Materi')
@section('page_title', 'Tambah Materi')

@section('content')
<div class="w-full space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('admin.materials.index') }}"
               class="shrink-0 w-9 h-9 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-all rounded-xl group">
                <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h1 class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight truncate">Unggah Materi</h1>
                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5 hidden sm:block">Distribusi bahan ajar baru ke siswa</p>
            </div>
        </div>
        <button type="submit" form="materialForm"
                class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white rounded-xl text-xs font-black uppercase tracking-wider hover:brightness-110 shadow-sm transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Simpan</span>
        </button>
    </div>

    {{-- Feedback Messages --}}
    @if($errors->any())
        <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl p-4">
            <div class="flex items-center gap-2.5 text-rose-600 dark:text-rose-400 mb-3">
                <div class="w-8 h-8 bg-rose-100 dark:bg-rose-900/50 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xs font-black uppercase tracking-widest">Validasi Gagal</h3>
            </div>
            <ul class="space-y-1 pl-2">
                @foreach($errors->all() as $error)
                    <li class="text-xs font-semibold text-rose-500 dark:text-rose-400">• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Form --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <form id="materialForm" action="{{ route('admin.materials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-5 sm:p-8 space-y-6">

                {{-- Judul & Mapel --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                            Judul Materi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               value="{{ old('title') }}"
                               required
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white placeholder:text-slate-300 dark:placeholder:text-slate-600 focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                               placeholder="Contoh: Bab 5 - Persamaan Linear">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                            Mata Pelajaran <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            @php
                                $subjList = (isset($subjects) && count($subjects) > 0) ? $subjects : ($globalSubjects ?? []);
                            @endphp
                            <select name="subject" required
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <option value="">Pilih Mapel</option>
                                @foreach($subjList as $subject)
                                    @php $sName = is_object($subject) ? $subject->name : $subject; @endphp
                                    <option value="{{ $sName }}" {{ old('subject') == $sName ? 'selected' : '' }}>{{ $sName }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LMS Sub-Bab Link -->
                <x-lms-topic-select-search 
                    :chapters="$lmsChapters ?? []" 
                    name="lms_topic_id" 
                    :selected="old('lms_topic_id')" 
                    accent-color="purple"
                    label="Tautkan ke Sub-Bab Modul LMS (Opsional)" 
                />

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                        Deskripsi Materi <span class="text-slate-300 font-semibold">(Opsional)</span>
                    </label>
                    <textarea name="description"
                              rows="3"
                              class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-300 dark:placeholder:text-slate-600 focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none"
                              placeholder="Berikan ringkasan singkat isi materi pembelajaran ini...">{{ old('description') }}</textarea>
                </div>

                {{-- Kelas & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-major-class-select :required="true" major-label="Jurusan / Program Keahlian" class-label="Target Kelas" select-class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition" label-class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2" />
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                            Status Publikasi
                        </label>
                        <div class="relative">
                            <select name="is_published" required
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <option value="1" {{ old('is_published', true) ? 'selected' : '' }}>Publikasikan Langsung</option>
                                <option value="0" {{ !old('is_published') ? 'selected' : '' }}>Simpan Sebagai Draft</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Upload & Link -- border separator --}}
                <div class="border-t border-slate-100 dark:border-slate-800 pt-5">
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Sumber Materi</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- File Upload --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Unggah Berkas Modul
                            </label>
                            <x-file-upload name="file_path" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.jpg,.png" label="Unggah Berkas Pembelajaran" help="PDF, Office, Gambar, atau Dokumentasi (Maks 20MB)" />
                        </div>

                        {{-- External Link --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Link Eksternal
                            </label>
                            <input type="url"
                                   name="external_link"
                                   value="{{ old('external_link') }}"
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-300 dark:placeholder:text-slate-600 focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                                   placeholder="https://drive.google.com/...">
                            <div class="mt-2.5 p-3 bg-indigo-50 dark:bg-indigo-950/30 rounded-xl border border-indigo-100 dark:border-indigo-900/40 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 leading-relaxed">
                                    Gunakan jika materi berada di Google Drive, YouTube, atau Cloud Storage lainnya.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-5 sm:px-8 py-4 bg-slate-50/70 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Data terjamin oleh sistem
                </p>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.materials.index') }}"
                       class="flex-1 sm:flex-none text-center px-5 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 transition">
                        Batalkan
                    </a>
                    <button type="submit"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-primary to-secondary text-white rounded-xl text-xs font-black uppercase tracking-wider hover:brightness-110 shadow-sm transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Materi
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection


