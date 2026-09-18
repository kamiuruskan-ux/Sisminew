@extends('layouts.admin')

@section('title', 'Input Nilai Massal')
@section('page_title', 'Input Nilai Massal')

@section('content')
<div class="w-full space-y-6" x-data="{
    fillAllScore(score) {
        const visibleCheckboxes = document.querySelectorAll('.class-students:not(.hidden) .student-checkbox');
        visibleCheckboxes.forEach(cb => {
            if (cb.checked) {
                const row = cb.closest('tr');
                const scoreInput = row.querySelector('input[type=&quot;number&quot;]');
                if (scoreInput) {
                    scoreInput.value = score;
                    scoreInput.dispatchEvent(new Event('input'));
                }
            }
        });
    },
    selectAll(status) {
        const masterCb = document.getElementById('master-checkbox');
        if (masterCb) masterCb.checked = status;
        const visibleCheckboxes = document.querySelectorAll('.class-students:not(.hidden) .student-checkbox');
        visibleCheckboxes.forEach(cb => {
            cb.checked = status;
            toggleStudentRow(cb);
        });
        updateSelectedCount();
    },
    selectFilledOnly() {
        const visibleRows = document.querySelectorAll('.class-students:not(.hidden) tr:not(:has(td[colspan]))');
        visibleRows.forEach(row => {
            const cb = row.querySelector('.student-checkbox');
            const scoreInput = row.querySelector('input[type=&quot;number&quot;]');
            if (cb && scoreInput) {
                const isFilled = scoreInput.value !== '' && !isNaN(scoreInput.value);
                cb.checked = isFilled;
                toggleStudentRow(cb);
            }
        });
        updateSelectedCount();
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <a href="{{ route('admin.grades.index') }}" class="w-10 h-10 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700/80 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-200 hover:shadow-md transition-all flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Input Nilai Massal</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Entry & seleksi nilai sekaligus untuk siswa dalam satu kelas</p>
            </div>
        </div>

        <a href="{{ route('admin.grades.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-2xs">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Nilai</span>
        </a>
    </div>

    <!-- Main Card Form -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200/80 dark:border-slate-800 overflow-hidden">
        <form action="{{ route('admin.grades.bulk.store') }}" method="POST" id="bulk-grades-form">
            @csrf
            
            <!-- Selection Section -->
            <div class="p-6 sm:p-8 bg-slate-50/70 dark:bg-slate-800/40 border-b border-slate-200/80 dark:border-slate-800 space-y-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-xs border border-indigo-100 dark:border-indigo-900">
                            1
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 dark:text-white text-sm tracking-tight">Pilih Parameter Penilaian</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Kelas, Mata Pelajaran, dan Tipe Ujian/Tugas</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-300 px-3 py-1 rounded-full uppercase border border-indigo-100 dark:border-indigo-900">Langkah 1 dari 2</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-2">
                    <!-- Kelas Select -->
                    <x-major-class-select :required="true" major-label="Jurusan" class-label="Kelas Rombel" select-class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition shadow-2xs" label-class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5" />

                    <!-- Mapel Select -->
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Mata Pelajaran <span class="text-rose-500">*</span>
                        </label>
                        @php
                            $subjList = (isset($subjects) && count($subjects) > 0) ? $subjects : ($globalSubjects ?? []);
                        @endphp
                        <select name="subject" id="subject" required class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition shadow-2xs">
                            <option value="">Pilih Mata Pelajaran...</option>
                            @foreach($subjList as $subject)
                                @php $sName = is_object($subject) ? $subject->name : $subject; @endphp
                                <option value="{{ $sName }}">{{ $sName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipe Select -->
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Tipe Penilaian <span class="text-rose-500">*</span>
                        </label>
                        <select name="type" id="type" required class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition shadow-2xs">
                            <option value="">Pilih Tipe Penilaian...</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}">
                                    @if($type === 'daily') Harian 
                                    @elseif($type === 'mid_term') UTS 
                                    @elseif($type === 'final_term') UAS 
                                    @else Ujian @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Students Table Section -->
            <div class="p-6 sm:p-8">
                <!-- Active Section when filters are complete -->
                <div id="students-section" class="hidden space-y-6">
                    <!-- Top Toolbar for Students Section -->
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-4 bg-slate-50/80 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-extrabold shadow-sm shadow-indigo-600/25 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm tracking-tight">
                                    Daftar Siswa Kelas <span id="selected-class-name" class="text-indigo-600 dark:text-indigo-400"></span>
                                </h3>
                                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5 flex items-center gap-2">
                                    <span>Total: <span id="student-count" class="font-bold text-slate-700 dark:text-slate-300">0</span> Siswa</span>
                                    <span>•</span>
                                    <span class="text-indigo-600 dark:text-indigo-400 font-extrabold">Terpilih: <span id="selected-count">0</span> Siswa</span>
                                </p>
                            </div>
                        </div>

                        <!-- Quick Bulk Controls & Score Fill Bar -->
                        <div class="flex flex-wrap items-center gap-2.5">
                            <!-- Bulk Selection Buttons -->
                            <div class="inline-flex items-center bg-white dark:bg-slate-700 p-1 rounded-xl border border-slate-200 dark:border-slate-600 shadow-2xs">
                                <button type="button" @click="selectAll(true)" class="px-2.5 py-1 text-[11px] font-extrabold text-slate-700 dark:text-slate-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Pilih Semua</button>
                                <span class="text-slate-300 dark:text-slate-600">|</span>
                                <button type="button" @click="selectAll(false)" class="px-2.5 py-1 text-[11px] font-extrabold text-slate-700 dark:text-slate-200 hover:text-rose-600 dark:hover:text-rose-400 transition">Batal Pilih</button>
                                <span class="text-slate-300 dark:text-slate-600">|</span>
                                <button type="button" @click="selectFilledOnly()" class="px-2.5 py-1 text-[11px] font-extrabold text-indigo-600 dark:text-indigo-400 hover:underline transition">Hanya Yg Berisi Nilai</button>
                            </div>

                            <!-- Fill All Quick Action Bar -->
                            <div class="flex items-center space-x-1.5 bg-white dark:bg-slate-700 p-1 rounded-xl border border-slate-200 dark:border-slate-600 shadow-2xs">
                                <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-400 uppercase tracking-wider px-2 hidden xl:inline">Set Nilai Terpilih:</span>
                                <button type="button" @click="fillAllScore(75)" class="px-2 py-0.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 hover:border-indigo-300 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-extrabold transition">75</button>
                                <button type="button" @click="fillAllScore(80)" class="px-2 py-0.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 hover:border-indigo-300 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-extrabold transition">80</button>
                                <button type="button" @click="fillAllScore(85)" class="px-2 py-0.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 hover:border-indigo-300 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-extrabold transition">85</button>
                                <button type="button" @click="fillAllScore(90)" class="px-2 py-0.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 hover:border-indigo-300 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-extrabold transition">90</button>
                                <button type="button" @click="fillAllScore(100)" class="px-2 py-0.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 hover:border-indigo-300 text-indigo-600 dark:text-indigo-400 rounded-lg text-xs font-extrabold transition">100</button>
                                <button type="button" @click="fillAllScore('')" class="px-2 py-0.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-600 dark:text-rose-400 rounded-lg text-xs font-extrabold hover:bg-rose-100 transition" title="Kosongkan Nilai Terpilih">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- Table Card -->
                    <div class="border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200/90 dark:border-slate-800">
                                <tr>
                                    <th class="px-4 py-3.5 w-12 text-center">
                                        <input type="checkbox" id="master-checkbox" checked class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition cursor-pointer" onchange="toggleMasterCheckbox(this)">
                                    </th>
                                    <th class="px-4 py-3.5 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-16 text-center">No</th>
                                    <th class="px-5 py-3.5 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-36">NISN</th>
                                    <th class="px-5 py-3.5 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Siswa</th>
                                    <th class="px-5 py-3.5 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right w-48">Nilai (0 - 100)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900">
                                @foreach($classes as $class)
                                    <tbody class="class-students hidden" data-class-id="{{ $class->id }}">
                                        @php $studentsInClass = $students->where('class_id', $class->id); $no = 1; @endphp
                                        @forelse($studentsInClass as $student)
                                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors group bg-indigo-50/40 dark:bg-indigo-950/20">
                                                <td class="px-4 py-3.5 text-center">
                                                    <input type="checkbox" checked class="student-checkbox w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition cursor-pointer" onchange="toggleStudentRow(this)">
                                                </td>
                                                <td class="px-4 py-3.5 text-xs font-extrabold text-slate-400 dark:text-slate-500 text-center">{{ $no++ }}</td>
                                                <td class="px-5 py-3.5">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-mono text-[11px] font-bold border border-slate-200/60 dark:border-slate-700">
                                                        {{ $student->nisn ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="px-5 py-3.5">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-extrabold text-xs border border-slate-200/80 dark:border-slate-700 shrink-0">
                                                            {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                                                        </div>
                                                        <p class="font-extrabold text-slate-800 dark:text-slate-100 text-xs tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                            {{ $student->user->name }}
                                                        </p>
                                                    </div>
                                                </td>
                                                <td class="px-5 py-3.5 text-right">
                                                    <div class="inline-flex items-center justify-end">
                                                        <input type="number" 
                                                               name="grades[{{ $student->id }}][score]" 
                                                               min="0" 
                                                               max="100" 
                                                               step="0.01" 
                                                               placeholder="0 - 100" 
                                                               class="score-input w-32 px-3.5 py-2 bg-slate-50/90 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-xl text-right font-mono font-extrabold text-xs text-slate-900 dark:text-white focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition shadow-2xs"
                                                               oninput="validateScore(this)">
                                                        <input type="hidden" name="grades[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                        </svg>
                                                    </div>
                                                    <p class="font-extrabold text-xs text-slate-600 dark:text-slate-300">Belum ada siswa terdaftar di kelas ini</p>
                                                    <p class="text-[11px] text-slate-400 font-medium mt-1">Silakan tambahkan data siswa ke kelas ini terlebih dahulu.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State Prompt -->
                <div id="empty-state" class="text-center py-16 px-4">
                    <div class="w-16 h-16 rounded-3xl bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-base tracking-tight mb-1">Pilih Parameter Penilaian</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium max-w-md mx-auto leading-relaxed">
                        Silakan pilih <strong class="text-slate-700 dark:text-slate-300 font-bold">Kelas Rombel</strong>, <strong class="text-slate-700 dark:text-slate-300 font-bold">Mata Pelajaran</strong>, dan <strong class="text-slate-700 dark:text-slate-300 font-bold">Tipe Penilaian</strong> di atas untuk memuat daftar siswa.
                    </p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-6 sm:px-8 py-5 bg-slate-50/90 dark:bg-slate-800/80 border-t border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Siswa yang tidak dicentang atau nilainya kosong tidak akan disimpan/diubah.</span>
                </div>

                <div class="flex items-center space-x-3 w-full sm:w-auto">
                    <a href="{{ route('admin.grades.index') }}" class="flex-1 sm:flex-none px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-extrabold text-xs hover:bg-slate-50 transition text-center shadow-2xs">
                        Batal
                    </a>
                    <button type="submit" id="submit-btn" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-xl font-extrabold text-xs shadow-md shadow-indigo-600/25 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Nilai Terpilih</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const classSelect = document.getElementById('class_id');
const subjectSelect = document.getElementById('subject');
const typeSelect = document.getElementById('type');
const studentsSection = document.getElementById('students-section');
const emptyState = document.getElementById('empty-state');
const selectedClassName = document.getElementById('selected-class-name');
const studentCount = document.getElementById('student-count');
const selectedCount = document.getElementById('selected-count');
const submitBtn = document.getElementById('submit-btn');
const masterCheckbox = document.getElementById('master-checkbox');

function toggleMasterCheckbox(masterCb) {
    const visibleCheckboxes = document.querySelectorAll('.class-students:not(.hidden) .student-checkbox');
    visibleCheckboxes.forEach(cb => {
        cb.checked = masterCb.checked;
        toggleStudentRow(cb, false);
    });
    updateSelectedCount();
    checkFormValidity();
}

function toggleStudentRow(cb, triggerUpdate = true) {
    const row = cb.closest('tr');
    const scoreInput = row.querySelector('.score-input');
    if (cb.checked) {
        row.classList.add('bg-indigo-50/40', 'dark:bg-indigo-950/20');
        scoreInput.disabled = false;
        scoreInput.classList.remove('opacity-40', 'cursor-not-allowed', 'bg-slate-100');
    } else {
        row.classList.remove('bg-indigo-50/40', 'dark:bg-indigo-950/20');
        scoreInput.disabled = true;
        scoreInput.classList.add('opacity-40', 'cursor-not-allowed', 'bg-slate-100');
    }
    if (triggerUpdate) {
        updateSelectedCount();
        checkFormValidity();
    }
}

function validateScore(input) {
    let value = parseFloat(input.value);
    if (value < 0) input.value = 0;
    if (value > 100) input.value = 100;
    
    // Auto check checkbox if score is typed
    if (input.value !== '' && !isNaN(input.value)) {
        const row = input.closest('tr');
        const cb = row.querySelector('.student-checkbox');
        if (cb && !cb.checked) {
            cb.checked = true;
            toggleStudentRow(cb);
        }
    }
    checkFormValidity();
}

function updateSelectedCount() {
    const visibleChecked = document.querySelectorAll('.class-students:not(.hidden) .student-checkbox:checked');
    if (selectedCount) {
        selectedCount.textContent = visibleChecked.length;
    }
}

function checkFormValidity() {
    const visibleCheckedInputs = document.querySelectorAll('.class-students:not(.hidden) tr:has(.student-checkbox:checked) .score-input');
    const hasValues = Array.from(visibleCheckedInputs).some(input => input.value !== '' && !isNaN(input.value));
    submitBtn.disabled = !hasValues;
}

function updateStudentCount() {
    const visibleRows = document.querySelectorAll('.class-students:not(.hidden) tr:not(:has(td[colspan]))');
    if (studentCount) {
        studentCount.textContent = visibleRows.length;
    }
    // Set default all checked when class changes
    const visibleCheckboxes = document.querySelectorAll('.class-students:not(.hidden) .student-checkbox');
    visibleCheckboxes.forEach(cb => {
        cb.checked = true;
        toggleStudentRow(cb, false);
    });
    if (masterCheckbox) masterCheckbox.checked = true;
    updateSelectedCount();
}

classSelect.addEventListener('change', function() {
    document.querySelectorAll('.class-students').forEach(el => el.classList.add('hidden'));
    const classId = this.value;
    
    if (classId && subjectSelect.value && typeSelect.value) {
        const selectedOption = this.options[this.selectedIndex];
        selectedClassName.textContent = selectedOption ? selectedOption.text : '';
        const targetBody = document.querySelector(`.class-students[data-class-id="${classId}"]`);
        if (targetBody) targetBody.classList.remove('hidden');
        studentsSection.classList.remove('hidden');
        emptyState.classList.add('hidden');
        updateStudentCount();
        checkFormValidity();
    } else {
        studentsSection.classList.add('hidden');
        emptyState.classList.remove('hidden');
    }
});

subjectSelect.addEventListener('change', function() {
    if (classSelect.value && typeSelect.value && this.value) {
        const classId = classSelect.value;
        document.querySelectorAll('.class-students').forEach(el => el.classList.add('hidden'));
        const targetBody = document.querySelector(`.class-students[data-class-id="${classId}"]`);
        if (targetBody) targetBody.classList.remove('hidden');
        studentsSection.classList.remove('hidden');
        emptyState.classList.add('hidden');
        updateStudentCount();
        checkFormValidity();
    }
});

typeSelect.addEventListener('change', function() {
    if (classSelect.value && subjectSelect.value && this.value) {
        const classId = classSelect.value;
        document.querySelectorAll('.class-students').forEach(el => el.classList.add('hidden'));
        const targetBody = document.querySelector(`.class-students[data-class-id="${classId}"]`);
        if (targetBody) targetBody.classList.remove('hidden');
        studentsSection.classList.remove('hidden');
        emptyState.classList.add('hidden');
        updateStudentCount();
        checkFormValidity();
    }
});

// Initialize TomSelect if available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof TomSelect !== 'undefined') {
        new TomSelect('#class_id', {
            placeholder: 'Pilih kelas...',
            searchField: ['text'],
            create: false
        });
        new TomSelect('#subject', {
            placeholder: 'Pilih mapel...',
            searchField: ['text'],
            create: false
        });
        new TomSelect('#type', {
            placeholder: 'Pilih tipe...',
            searchField: ['text'],
            create: false
        });
    }
});
</script>
@endsection
