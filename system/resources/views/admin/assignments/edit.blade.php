@extends('layouts.admin')

@section('title', 'Edit Tugas')
@section('page_title', 'Edit Tugas')

@section('content')
<div class="w-full space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Edit Penugasan Siswa</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui informasi instruksi, tenggat waktu, atau lampiran tugas.</p>
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
        <form action="{{ route('admin.assignments.update', $encryptedId) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <!-- Title & Subject -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Tugas <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $assignment->title) }}" required placeholder="Judul tugas..." class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
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
                                @php $subVal = is_object($sub) ? $sub->name : $sub; @endphp
                                <option value="{{ $subVal }}" {{ old('subject', $assignment->subject) == $subVal ? 'selected' : '' }}>
                                    {{ $subVal }}
                                </option>
                            @endforeach
                            @if($assignment->subject && !in_array($assignment->subject, collect($subjList)->map(fn($s) => is_object($s) ? $s->name : $s)->toArray()))
                                <option value="{{ $assignment->subject }}" selected>{{ $assignment->subject }}</option>
                            @endif
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
                    :selected="old('lms_topic_id', $assignment->lms_topic_id)" 
                    label="Tautkan ke Sub-Bab Modul LMS (Opsional)" 
                />

                <!-- Description -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi & Petunjuk Pengerjaan <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="5" required placeholder="Petunjuk pengerjaan..." class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none">{{ old('description', $assignment->description) }}</textarea>
                    @error('description')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class & Due Date -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-major-class-select :required="true" major-label="Jurusan Target" class-label="Kelas Target" :selected-class="$assignment->class_id" select-class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500" label-class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1" />

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tenggat Waktu (Due Date) <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="due_date" value="{{ old('due_date', $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('Y-m-d\TH:i') : '') }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        @error('due_date')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Max Score & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nilai Maksimal <span class="text-rose-500">*</span></label>
                        <input type="number" name="max_score" value="{{ old('max_score', $assignment->max_score) }}" min="1" max="100" required placeholder="100" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        @error('max_score')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                            <option value="published" {{ old('status', $assignment->status) == 'published' ? 'selected' : '' }}>Published (Dipublikasikan)</option>
                            <option value="draft" {{ old('status', $assignment->status) == 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                            <option value="closed" {{ old('status', $assignment->status) == 'closed' ? 'selected' : '' }}>Closed (Ditutup)</option>
                        </select>
                        @error('status')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Attachment -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lampiran Baru (Opsional)</label>
                    <x-file-upload name="attachment" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png" existing="{{ $assignment->attachment }}" label="Unggah / Ganti File Lampiran" help="Kosongkan jika tidak ingin mengubah lampiran saat ini" />
                    
                    @if($assignment->attachment)
                        <div class="mt-3 p-3 bg-indigo-50/50 border border-indigo-100 rounded-xl flex items-center justify-between text-xs">
                            <span class="text-indigo-900 font-semibold truncate">Lampiran saat ini: {{ basename($assignment->attachment) }}</span>
                            <a href="{{ asset($assignment->attachment) }}" target="_blank" class="px-2.5 py-1 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition">
                                Unduh / Lihat
                            </a>
                        </div>
                    @endif
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
                    Perbarui Tugas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


