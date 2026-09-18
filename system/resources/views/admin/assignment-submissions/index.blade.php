@extends('layouts.admin')

@section('title', 'Pengumpulan Tugas')

@section('page_title', 'Pengumpulan Tugas')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pengumpulan Tugas</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola pengumpulan dan penilaian tugas siswa</p>
        </div>
    </div>

    <!-- Filters (Real-Time Auto Submit) -->
    <div class="bg-white dark:bg-[#24303F] rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-800">
        <form action="{{ route('admin.assignment-submissions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Tugas</label>
                <select name="assignment_id" id="filter_assignment_id" @change="$el.closest('form').submit()" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-[#1C2434] dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Tugas</option>
                    @foreach($assignments as $assignment)
                        <option value="{{ $assignment->id }}" {{ request('assignment_id') == $assignment->id ? 'selected' : '' }}>{{ $assignment->title }}</option>
                    @endforeach
                </select>
            </div>
            <x-student-select-search :students="$students" :selected="request('student_id')" allow-all all-label="Semua Siswa" label="Siswa" :on-change="'$el.closest(\'form\').submit()'" />
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Status</label>
                <select name="status" @change="$el.closest('form').submit()" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-[#1C2434] dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Dikumpul</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Sudah Dikumpul</option>
                    <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Sudah Dinilai</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Submit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $submission->assignment->title }}</p>
                                <p class="text-xs text-gray-500">{{ $submission->assignment->subject }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $submission->student->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $submission->student->class->name ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($submission->status === 'pending') bg-gray-100 text-gray-800
                                    @elseif($submission->status === 'submitted') bg-blue-100 text-blue-800
                                    @elseif($submission->status === 'graded') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @if($submission->status === 'pending') Belum Dikumpul
                                    @elseif($submission->status === 'submitted') Sudah Dikumpul
                                    @elseif($submission->status === 'graded') Sudah Dinilai
                                    @else Terlambat @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($submission->submitted_at)
                                    {{ $submission->submitted_at->translatedFormat('d M Y, H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($submission->score !== null)
                                    <span class="text-lg font-bold {{ $submission->score >= 75 ? 'text-green-600' : ($submission->score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $submission->score }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">Belum dinilai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    @if($submission->status !== 'graded')
                                        <a href="{{ route('admin.assignment-submissions.grade', $submission) }}" 
                                           class="p-2 text-gray-400 hover:bg-green-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Beri Nilai">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($submission->attachment)
                                        <a href="{{ route('admin.assignment-submissions.download', $submission) }}" 
                                           class="p-2 text-gray-400 hover:bg-blue-600 hover:text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md" title="Download Jawaban">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-500">Belum ada pengumpulan tugas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $submissions->links() }}
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new TomSelect('#filter_student_id', {
        placeholder: 'Cari siswa...',
        allowEmptyOption: true,
        searchField: ['text'],
        create: false
    });
    new TomSelect('#filter_assignment_id', {
        placeholder: 'Cari tugas...',
        allowEmptyOption: true,
        searchField: ['text'],
        create: false
    });
});
</script>
@endsection
