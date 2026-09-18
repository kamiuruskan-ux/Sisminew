@extends('layouts.admin')

@section('title', 'Edit Nilai Siswa')
@section('page_title', 'Edit Nilai Akademis Siswa')

@section('content')
<div class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Nilai Siswa</h2>
            <p class="text-xs text-slate-500 font-medium">Perbarui skor atau catatan nilai akademis.</p>
        </div>
        <a href="{{ route('admin.grades.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
        <form method="POST" action="{{ route('admin.grades.update', $grade->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <x-major-class-select :required="true" major-label="Jurusan" class-label="Kelas" :selected-class="$grade->class_id" select-class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-semibold" label-class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" />

                <x-student-select-search :students="$students" :selected="old('student_id', $grade->student_id)" required label="Siswa" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    @php
                        $subjList = (isset($subjects) && count($subjects) > 0) ? $subjects : ($globalSubjects ?? []);
                    @endphp
                    <select name="subject" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-semibold">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($subjList as $subj)
                            @php $sName = is_object($subj) ? $subj->name : $subj; @endphp
                            <option value="{{ $sName }}" {{ old('subject', $grade->subject) == $sName ? 'selected' : '' }}>{{ $sName }}</option>
                        @endforeach
                        @if($grade->subject && !in_array($grade->subject, collect($subjList)->map(fn($s) => is_object($s) ? $s->name : $s)->toArray()))
                            <option value="{{ $grade->subject }}" selected>{{ $grade->subject }}</option>
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori Nilai <span class="text-rose-500">*</span></label>
                    <select name="type" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-semibold">
                        <option value="daily" {{ old('type', $grade->type) == 'daily' ? 'selected' : '' }}>Nilai Harian / Tugas</option>
                        <option value="mid_term" {{ old('type', $grade->type) == 'mid_term' ? 'selected' : '' }}>UTS (Ujian Tengah Semester)</option>
                        <option value="final_term" {{ old('type', $grade->type) == 'final_term' ? 'selected' : '' }}>UAS (Ujian Akhir Semester)</option>
                        <option value="exam" {{ old('type', $grade->type) == 'exam' ? 'selected' : '' }}>Ujian Praktek / Kuis</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Skor / Nilai (0 - 100) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" name="score" min="0" max="100" required value="{{ old('score', $grade->score) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm font-extrabold text-indigo-600">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Guru (Opsional)</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-medium">{{ old('notes', $grade->notes) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3">
                <a href="{{ route('admin.grades.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Update Nilai Siswa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
