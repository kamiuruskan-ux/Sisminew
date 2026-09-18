@extends('layouts.admin')

@section('title', 'Tambah Tugas')
@section('page_title', 'Tambah Tugas Baru')

@section('content')
<div class="w-full space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Buat Penugasan Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Lengkapi formulir di bawah ini untuk membuat tugas atau pekerjaan rumah bagi siswa.</p>
        </div>
        <a href="{{ route('admin.assignments.index') }}"
           class="inline-flex items-center space-x-2 px-3.5 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <form action="{{ route('admin.assignments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-6 space-y-6">
                <!-- Title & Subject -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Tugas <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Latihan Soal Bab 3 Persamaan Kuadrat" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        @error('title')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Mata Pelajaran <span class="text-rose-500">*</span></label>
                        @php
                            $subjList = (isset($subjects) && count($subjects) > 0) ? $subjects : ($globalSubjects ?? []);
                        @endphp
                        <select name="subject" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjList as $sub)
                                @php $subName = is_object($sub) ? $sub->name : $sub; @endphp
                                <option value="{{ $subName }}" {{ old('subject') == $subName ? 'selected' : '' }}>
                                    {{ $subName }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- LMS Sub-Bab Link -->
                <x-lms-topic-select-search 
                    :chapters="$lmsChapters ?? []" 
                    name="lms_topic_id" 
                    :selected="old('lms_topic_id')" 
                    label="Tautkan ke Sub-Bab Modul LMS (Opsional)" 
                />

                <!-- Description -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi & Petunjuk Pengerjaan <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="5" required placeholder="Tuliskan petunjuk pengerjaan tugas, instruksi khusus, dan kriteria penilaian..." class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class & Due Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-major-class-select :required="true" major-label="Jurusan Target" class-label="Kelas Target" select-class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500" label-class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1" />

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tenggat Waktu (Due Date) <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="due_date" value="{{ old('due_date') }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        @error('due_date')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Max Score & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nilai Maksimal <span class="text-rose-500">*</span></label>
                        <input type="number" name="max_score" value="{{ old('max_score', 100) }}" min="1" max="100" required placeholder="100" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        @error('max_score')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                            <option value="published" {{ old('status') == 'published' || old('status') == '' ? 'selected' : '' }}>Published (Dipublikasikan)</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                            <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed (Ditutup)</option>
                        </select>
                        @error('status')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Attachment -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">File Lampiran Tugas (Opsional)</label>
                    <x-file-upload name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png" label="Upload File Soal / Lampiran Tugas" help="PDF, Office, atau Gambar (Maks 10MB)" />
                    @error('attachment')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.assignments.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    Simpan Tugas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


