@extends('layouts.admin')

@section('title', 'Nilai Tugas')

@section('page_title', 'Beri Nilai Tugas')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="mb-6 pb-4 border-b">
            <h3 class="font-bold text-lg text-gray-800">{{ $submission->assignment->title }}</h3>
            <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                <span>Siswa: <strong>{{ $submission->student->user->name }}</strong></span>
                <span>Kelas: <strong>{{ $submission->student->class->name ?? '-' }}</strong></span>
                <span>Submit: <strong>{{ $submission->submitted_at ? $submission->submitted_at->translatedFormat('d M Y, H:i') : 'Belum submit' }}</strong></span>
            </div>
        </div>

        @if($submission->content)
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-700 mb-2">Jawaban Siswa:</h4>
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $submission->content }}</p>
            </div>
        @endif

        @if($submission->attachment)
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                <a href="{{ route('admin.assignment-submissions.download', $submission) }}" class="text-primary font-medium flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Lampiran Siswa
                </a>
            </div>
        @endif

        <form action="{{ route('admin.assignment-submissions.store-grade', $submission) }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai (0-{{ $submission->assignment->max_score }}) *</label>
                    <input type="number" name="score" value="{{ old('score', $submission->score) }}" min="0" max="{{ $submission->assignment->max_score }}" step="0.01" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Feedback / Catatan *</label>
                    <textarea name="feedback" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('feedback', $submission->feedback) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Berikan feedback konstruktif untuk siswa</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="graded" {{ old('status', $submission->status) === 'graded' ? 'selected' : '' }}>Sudah Dinilai</option>
                        <option value="late" {{ old('status', $submission->status) === 'late' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.assignment-submissions.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-lg">Simpan Nilai</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
