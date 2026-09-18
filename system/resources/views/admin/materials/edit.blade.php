@extends('layouts.admin')

@section('title', 'Edit Materi')
@section('page_title', 'Edit Materi')

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
                <h1 class="text-base sm:text-xl font-black text-slate-900 dark:text-white tracking-tight truncate">Perbarui Materi</h1>
                <p class="text-[10px] text-slate-400 font-medium mt-0.5 truncate">{{ $material->title }}</p>
            </div>
        </div>
        <button type="submit" form="materialForm"
                class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 bg-gradient-to-r from-primary to-secondary text-white rounded-xl text-xs font-black uppercase tracking-wider hover:brightness-110 shadow-sm transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Update</span>
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
        <form id="materialForm" action="{{ route('admin.materials.update', $material) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="p-5 sm:p-8 space-y-6">

                {{-- Judul & Mapel --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                            Judul Materi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title"
                               value="{{ old('title', $material->title) }}" required
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
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
                                    <option value="{{ $sName }}" {{ old('subject', $material->subject) == $sName ? 'selected' : '' }}>{{ $sName }}</option>
                                @endforeach
                                @if($material->subject && !in_array($material->subject, collect($subjList)->map(fn($s) => is_object($s) ? $s->name : $s)->toArray()))
                                    <option value="{{ $material->subject }}" selected>{{ $material->subject }}</option>
                                @endif
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
                    :selected="old('lms_topic_id', $material->lms_topic_id)" 
                    accent-color="purple"
                    label="Tautkan ke Sub-Bab Modul LMS (Opsional)" 
                />

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                        Deskripsi Materi <span class="text-slate-300 font-semibold">(Opsional)</span>
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition resize-none"
                              placeholder="Berikan ringkasan singkat isi materi pembelajaran ini...">{{ old('description', $material->description) }}</textarea>
                </div>

                {{-- Kelas & Status --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-major-class-select :required="true" major-label="Jurusan / Program Keahlian" class-label="Target Kelas" :selected-class="$material->class_id" select-class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition" label-class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2" />
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                            Status Publikasi
                        </label>
                        <div class="relative">
                            <select name="is_published" required
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition">
                                <option value="1" {{ old('is_published', $material->is_published) ? 'selected' : '' }}>Publikasikan Langsung</option>
                                <option value="0" {{ !old('is_published', $material->is_published) ? 'selected' : '' }}>Simpan Sebagai Draft</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sumber saat ini --}}
                @if($material->file_path || $material->external_link)
                    <div class="p-4 bg-indigo-50/60 dark:bg-indigo-950/20 rounded-2xl border border-indigo-100 dark:border-indigo-900/40">
                        <p class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-3">Sumber Materi Saat Ini</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @if($material->file_path)
                                <div class="flex items-center gap-3 bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                                    <div class="w-9 h-9 bg-indigo-600 text-white rounded-xl flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ basename($material->file_path) }}</p>
                                        <a href="{{ route('admin.materials.download', $material) }}" class="text-[10px] font-bold text-indigo-600 hover:underline">Unduh Berkas</a>
                                    </div>
                                </div>
                            @endif
                            @if($material->external_link)
                                <div class="flex items-center gap-3 bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                                    <div class="w-9 h-9 bg-blue-600 text-white rounded-xl flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $material->external_link }}</p>
                                        <a href="{{ $material->external_link }}" target="_blank" class="text-[10px] font-bold text-blue-600 hover:underline">Buka Link</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Ganti Sumber --}}
                <div class="border-t border-slate-100 dark:border-slate-800 pt-5">
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Ganti Sumber Materi <span class="font-semibold text-slate-300">(Opsional)</span></p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- File Upload --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Unggah / Ganti Berkas Modul
                            </label>
                            <x-file-upload name="file_path" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.jpg,.png" existing="{{ $material->file_path }}" label="Pilih / Seret Berkas Baru" help="Kosongkan jika tidak ingin mengubah berkas saat ini" />
                        </div>

                        {{-- External Link --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Ganti Link
                            </label>
                            <input type="url" name="external_link"
                                   value="{{ old('external_link', $material->external_link) }}"
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition"
                                   placeholder="https://drive.google.com/...">
                            <div class="mt-2.5 p-3 bg-indigo-50 dark:bg-indigo-950/30 rounded-xl border border-indigo-100 dark:border-indigo-900/40 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 leading-relaxed">
                                    Perbarui tautan jika materi telah dipindahkan.
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Perubahan langsung berdampak pada akses siswa
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
                        Update Materi
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection


