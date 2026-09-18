@extends('layouts.admin')

@section('title', $exam->title)
@section('page_title', 'Kelola Ujian & Bank Soal')

@section('content')
<div class="space-y-6" x-data="{ 
    tab: 'questions', 
    showAddQuestionModal: false, 
    showImportQuestionsModal: false,
    isImportingSoal: false,
    soalStatusText: 'Membaca file Excel...',
    soalStatusIndex: 0,
    soalStatusList: [
        'Membaca berkas Excel...',
        'Memvalidasi format kolom & jenis soal...',
        'Menganalisis kunci jawaban & bobot...',
        'Memasukkan soal ke Bank Soal Ujian...',
        'Hampir selesai, menyinkronkan data...'
    ],
    startSoalImportLoading() {
        this.isImportingSoal = true;
        setInterval(() => {
            this.soalStatusIndex = (this.soalStatusIndex + 1) % this.soalStatusList.length;
            this.soalStatusText = this.soalStatusList[this.soalStatusIndex];
        }, 1200);
    },
    showEditExamModal: false,
    showEditQuestionModal: false,
    editQuestion: null,
    editPreviewUrl: null,
    removeImage: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(id, name) {
        this.deleteTarget = { id: id, name: name };
        this.deleteFormAction = '{{ url('admin/exams/' . $exam->id . '/questions') }}/' + id;
        this.showDeleteModal = true;
    },
    addType: 'pg',
    editType: 'pg',
    addBsStatements: ['', ''],
    addBsAnswers: ['benar', 'salah'],
    addMatchPremises: ['', ''],
    addMatchTargets: ['', ''],
    editBsStatements: [],
    editBsAnswers: [],
    editMatchPremises: [],
    editMatchTargets: [],
    editPgKompleksAnswers: [],
    editEssayKey: '',

    openEditQuestion(q) {
        this.editQuestion = JSON.parse(JSON.stringify(q));
        this.editType = q.type || 'pg';
        this.editBsStatements = Array.isArray(q.options_json) ? [...q.options_json] : [];
        this.editBsAnswers = Array.isArray(q.correct_answer_json) ? [...q.correct_answer_json] : [];
        this.editMatchPremises = (q.options_json && q.options_json.left) ? [...q.options_json.left] : [];
        this.editMatchTargets = (q.options_json && q.options_json.right) ? [...q.options_json.right] : [];
        
        if (Array.isArray(q.correct_answer_json)) {
            this.editPgKompleksAnswers = [...q.correct_answer_json];
        } else if (typeof q.correct_answer === 'string') {
            this.editPgKompleksAnswers = q.correct_answer.split(',').map(s => s.trim());
        } else {
            this.editPgKompleksAnswers = [];
        }

        this.editEssayKey = (q.options_json && q.options_json.sample_answer) 
            ? q.options_json.sample_answer 
            : (q.correct_answer !== 'essay' ? (q.correct_answer || '') : '');

        this.editPreviewUrl = q.image_path ? ((q.image_path.startsWith('img/') ? '{{ asset('') }}' : '{{ asset('img') }}/') + q.image_path) : null;
        this.removeImage = false;
        this.showEditQuestionModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Soal Ujian', 'message' => 'Apakah Anda yakin ingin menghapus :name ini?'])

    <!-- Exam Info Card Header -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">
                <span>{{ $exam->subject_name }}</span>
                <span>•</span>
                <span>{{ $exam->class->name ?? 'Semua Kelas' }}</span>
                <span>•</span>
                <span>{{ $exam->duration_minutes }} Menit</span>
                <span>•</span>
                <span class="text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md text-[11px] font-bold">
                    Guru: {{ $exam->teacher->user->name ?? $exam->teacher->name ?? 'Belum Ditentukan' }}
                </span>
                <span>•</span>
                <span class="px-2 py-0.5 rounded-md text-[10px] {{ $exam->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $exam->is_published ? 'Dipublikasikan' : 'Draft' }}
                </span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $exam->title }}</h2>
            @if($exam->description)
                <p class="text-xs text-slate-500 font-medium mt-1">{{ $exam->description }}</p>
            @endif
            @if($exam->topic)
                <div class="mt-2">
                    <a href="{{ route('admin.lms.chapters.index') }}" class="inline-flex items-center space-x-1 text-xs bg-purple-100 text-purple-800 hover:bg-purple-200 font-bold px-2.5 py-1 rounded-lg border border-purple-300 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Sub-Bab LMS: {{ $exam->topic->chapter->title ?? '' }} &raquo; {{ $exam->topic->title }}</span>
                    </a>
                </div>
            @endif
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" @click="showEditExamModal = true" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl text-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Informasi</span>
            </button>
            <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors">
                ← Kembali
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center space-x-2 border-b border-slate-200">
        <button @click="tab = 'questions'" :class="tab === 'questions' ? 'border-indigo-600 text-indigo-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 font-bold'" class="py-3 px-4 text-sm border-b-2 transition-colors flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Bank Soal ({{ $exam->questions->count() }})</span>
        </button>
        <button @click="tab = 'results'" :class="tab === 'results' ? 'border-indigo-600 text-indigo-600 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 font-bold'" class="py-3 px-4 text-sm border-b-2 transition-colors flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/>
            </svg>
            <span>Hasil & Nilai Siswa ({{ $exam->results->whereIn('status', ['completed', 'needs_grading'])->count() }})</span>
        </button>
    </div>

    <!-- TAB 1: Bank Soal (Questions) -->
    <div x-show="tab === 'questions'" class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h3 class="font-extrabold text-slate-900 text-base">Daftar Pertanyaan Ujian</h3>
            <div class="flex items-center gap-2">
                <button type="button" @click="showImportQuestionsModal = true" class="px-3.5 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-bold text-xs hover:bg-emerald-100 transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Import Soal (Excel)</span>
                </button>
                <button @click="showAddQuestionModal = true" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Buat Soal Baru</span>
                </button>
            </div>
        </div>

        @if($exam->questions->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h4 class="font-extrabold text-slate-800 text-base">Bank Soal Masih Kosong</h4>
                <p class="text-xs text-slate-500 font-medium max-w-sm mx-auto mt-1 mb-4">Ujian ini belum memiliki pertanyaan. Tambahkan soal Pilihan Ganda, PG Kompleks, Benar/Salah, atau Menjodohkan.</p>
                <div class="flex items-center justify-center gap-3">
                    <button type="button" @click="showImportQuestionsModal = true" class="px-4 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-bold text-xs hover:bg-emerald-100 transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Import Soal (Excel)</span>
                    </button>
                    <button @click="showAddQuestionModal = true" class="btn-primary flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Soal Pertama</span>
                    </button>
                </div>
            </div>
        @else
            <div class="space-y-4">
                @foreach($exam->questions as $index => $q)
                    @php $qType = $q->type ?? 'pg'; @endphp
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start space-x-3">
                                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 font-extrabold text-sm flex items-center justify-center shrink-0 mt-0.5">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <div class="flex items-center space-x-2 mb-1">
                                        @if($qType === 'pg')
                                            <span class="px-2 py-0.5 text-[10px] font-extrabold bg-blue-100 text-blue-800 rounded-md">Pilihan Ganda</span>
                                        @elseif($qType === 'pg_kompleks')
                                            <span class="px-2 py-0.5 text-[10px] font-extrabold bg-purple-100 text-purple-800 rounded-md">PG Kompleks</span>
                                        @elseif($qType === 'benar_salah')
                                            <span class="px-2 py-0.5 text-[10px] font-extrabold bg-emerald-100 text-emerald-800 rounded-md">Benar / Salah</span>
                                        @elseif($qType === 'menjodohkan')
                                            <span class="px-2 py-0.5 text-[10px] font-extrabold bg-amber-100 text-amber-800 rounded-md">Menjodohkan</span>
                                        @elseif($qType === 'essay')
                                            <span class="px-2 py-0.5 text-[10px] font-extrabold bg-rose-100 text-rose-800 rounded-md">Essay / Uraian</span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-sm leading-snug">{!! nl2br(e($q->question_text)) !!}</h4>
                                    @if($q->image_path)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($q->image_path, 'img/') ? asset($q->image_path) : asset('img/' . $q->image_path) }}" alt="Gambar Soal" class="mt-2 max-h-48 rounded-xl border border-slate-200 object-contain">
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <span class="px-2.5 py-1 text-xs font-extrabold bg-purple-50 text-purple-700 rounded-lg">
                                    Bobot: {{ $q->score_weight }} Poin
                                </span>
                                <button type="button" 
                                        @click="openEditQuestion({{ \Illuminate\Support\Js::from($q) }})" 
                                        class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors cursor-pointer" title="Edit Soal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                 <button type="button" @click="confirmDelete({{ $q->id }}, 'Soal Nomor {{ $index + 1 }}')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer" title="Hapus Soal">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                     </svg>
                                 </button>

                            </div>
                        </div>

                        <!-- Render Question Preview by Type -->
                        <div class="pl-11 pt-1">
                            @if($qType === 'pg')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                                        @if(!empty($q->{'option_'.$opt}))
                                            <div class="p-2.5 rounded-xl border text-xs font-semibold flex items-center justify-between {{ $q->correct_answer === $opt ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-extrabold' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                                                <span><strong class="uppercase font-bold mr-1">{{ $opt }}.</strong> {{ $q->{'option_'.$opt} }}</span>
                                                @if($q->correct_answer === $opt)
                                                    <span class="text-[10px] font-extrabold bg-emerald-600 text-white px-2 py-0.5 rounded-md uppercase">Kunci Jawaban</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @elseif($qType === 'pg_kompleks')
                                @php $correctKeys = (array) ($q->correct_answer_json ?? explode(',', $q->correct_answer ?? '')); @endphp
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                                        @if(!empty($q->{'option_'.$opt}))
                                            @php $isKeyCorrect = in_array(strtolower($opt), array_map('strtolower', $correctKeys)); @endphp
                                            <div class="p-2.5 rounded-xl border text-xs font-semibold flex items-center justify-between {{ $isKeyCorrect ? 'bg-purple-50 border-purple-300 text-purple-900 font-extrabold' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                                                <span><strong class="uppercase font-bold mr-1">{{ $opt }}.</strong> {{ $q->{'option_'.$opt} }}</span>
                                                @if($isKeyCorrect)
                                                    <span class="text-[10px] font-extrabold bg-purple-600 text-white px-2 py-0.5 rounded-md uppercase">Kunci Benar</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @elseif($qType === 'benar_salah')
                                @if(is_array($q->options_json) && count($q->options_json) > 0)
                                    <div class="overflow-x-auto border border-slate-200 rounded-xl">
                                        <table class="w-full text-xs text-left">
                                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-extrabold">
                                                <tr>
                                                    <th class="p-2.5">Pernyataan</th>
                                                    <th class="p-2.5 w-28 text-center">Kunci Jawaban</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach($q->options_json as $sIdx => $stmt)
                                                    <tr>
                                                        <td class="p-2.5 font-medium text-slate-800">{{ $stmt }}</td>
                                                        <td class="p-2.5 text-center">
                                                            <span class="px-2 py-1 text-[10px] font-black uppercase rounded-md {{ strtolower($q->correct_answer_json[$sIdx] ?? '') === 'benar' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                                {{ strtoupper($q->correct_answer_json[$sIdx] ?? 'BENAR') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-bold text-emerald-900 flex items-center justify-between">
                                        <span>Pernyataan Tunggal</span>
                                        <span>Kunci Jawaban: <strong class="uppercase font-black text-emerald-700">{{ strtoupper($q->correct_answer ?? 'BENAR') }}</strong></span>
                                    </div>
                                @endif
                            @elseif($qType === 'menjodohkan')
                                @php 
                                    $leftList = $q->options_json['left'] ?? [];
                                    $rightList = $q->options_json['right'] ?? [];
                                    $correctMap = $q->correct_answer_json ?? [];
                                @endphp
                                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                                    <table class="w-full text-xs text-left">
                                        <thead class="bg-amber-50 border-b border-amber-200 text-amber-900 font-extrabold">
                                            <tr>
                                                <th class="p-2.5">Soal / Pernyataan (Kiri)</th>
                                                <th class="p-2.5">Pasangan Jawaban (Kanan)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($leftList as $lIdx => $leftItem)
                                                <tr>
                                                    <td class="p-2.5 font-bold text-slate-800">{{ $leftItem }}</td>
                                                    <td class="p-2.5 font-bold text-amber-700 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                        <span>{{ $correctMap[$lIdx] ?? ($rightList[$lIdx] ?? '-') }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($qType === 'essay')
                                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span><strong>Pedoman / Kunci Jawaban Reference:</strong> {{ $q->options_json['sample_answer'] ?? $q->correct_answer ?? '-' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- TAB 2: Hasil & Nilai Siswa -->
    <div x-show="tab === 'results'" class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-cloak>
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider">Rekapitulasi Hasil Ujian Siswa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Mulai Kerjakan</th>
                        <th>Dikumpulkan</th>
                        <th>Skor / Nilai Akhir</th>
                        <th>Pengawasan Anti-Cheat</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $hasEssayQuestions = $exam->questions->where('type', 'essay')->count() > 0; @endphp
                    @forelse($exam->results as $res)
                        @php
                            $violations = $res->answers['_violations'] ?? 0;
                            $reason = $res->answers['_submission_reason'] ?? null;
                        @endphp
                        <tr>
                            <td>
                                <div>
                                    <p class="font-extrabold text-slate-900 text-sm">{{ $res->student->user->name ?? $res->student->name }}</p>
                                    <p class="text-xs text-slate-500 font-medium">NISN: {{ $res->student->nisn }}</p>
                                </div>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-700 rounded-lg">
                                    {{ $res->student->class->name ?? '-' }}
                                </span>
                            </td>
                            <td class="text-xs font-semibold text-slate-600">
                                {{ $res->started_at ? $res->started_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="text-xs font-semibold text-slate-600">
                                {{ $res->submitted_at ? $res->submitted_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td>
                                @if($res->status === 'needs_grading')
                                    <span class="px-3 py-1 text-xs font-extrabold rounded-xl bg-amber-100 text-amber-800 border border-amber-200">
                                        Perlu Koreksi
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-sm font-extrabold rounded-xl {{ $res->score >= 75 ? 'bg-emerald-100 text-emerald-800' : ($res->score >= 60 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ number_format($res->score, 2) }} / 100
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($violations > 0)
                                    <span class="px-2.5 py-1 text-[11px] font-black bg-rose-100 text-rose-800 rounded-lg inline-flex items-center gap-1 border border-rose-200">
                                        <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>{{ $violations }}x Pelanggaran</span>
                                    </span>
                                    @if($reason)
                                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $reason }}</p>
                                    @endif
                                @else
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-700 rounded-lg inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span>Bersih (0)</span>
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($res->status === 'needs_grading')
                                    <span class="px-2.5 py-1 text-[11px] font-extrabold bg-amber-100 text-amber-900 rounded-full animate-pulse border border-amber-300">
                                        Menunggu Koreksi
                                    </span>
                                @elseif($res->status === 'completed')
                                    <span class="px-2.5 py-1 text-[11px] font-extrabold bg-emerald-50 text-emerald-700 rounded-full">
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[11px] font-extrabold bg-slate-100 text-slate-700 rounded-full">
                                        Sedang Mengerjakan
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($hasEssayQuestions && in_array($res->status, ['needs_grading', 'completed']))
                                    <a href="{{ route('admin.exams.grade-student', [$exam->id, $res->id]) }}" class="px-3 py-1.5 {{ $res->status === 'needs_grading' ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border-amber-300 font-extrabold' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-300 font-bold' }} border rounded-xl text-xs transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-current" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>{{ $res->status === 'needs_grading' ? 'Koreksi Essay' : 'Edit Nilai' }}</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400 font-medium">
                                Belum ada siswa yang mengerjakan ujian ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: Tambah Soal Baru -->
    <div x-show="showAddQuestionModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl border border-slate-200 max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="showAddQuestionModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Tambah Soal Ujian Baru</h3>
                <button @click="showAddQuestionModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.exams.questions.store', $exam->id) }}" class="space-y-4" enctype="multipart/form-data">
                @csrf
                
                <!-- Tipe Soal Selector -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe / Jenis Soal <span class="text-rose-500">*</span></label>
                    <select name="type" x-model="addType" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-extrabold text-xs text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                        <option value="pg">1. Pilihan Ganda (PG) - Pilih 1 Jawaban Benar</option>
                        <option value="pg_kompleks">2. Pilihan Ganda Kompleks - Pilih Lebih Dari 1 Jawaban Benar</option>
                        <option value="benar_salah">3. Benar / Salah (True or False)</option>
                        <option value="menjodohkan">4. Menjodohkan (Matching Pair)</option>
                        <option value="essay">5. Essay / Uraian - Jawaban Teks Bebas</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Teks Pertanyaan / Instruksi <span class="text-rose-500">*</span></label>
                    <textarea name="question_text" rows="3" required placeholder="Tuliskan teks pertanyaan atau petunjuk soal..." class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm font-medium"></textarea>
                </div>

                <!-- Upload Gambar Soal (Opsional) -->
                <div x-data="{ addPreviewUrl: null }">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Gambar Soal (Opsional)</label>
                    <div class="relative">
                        <input type="file" name="question_image" accept="image/*"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-300 rounded-xl p-1"
                            @change="const f = $event.target.files[0]; addPreviewUrl = f ? URL.createObjectURL(f) : null">
                    </div>
                    <template x-if="addPreviewUrl">
                        <div class="mt-2 relative inline-block">
                            <img :src="addPreviewUrl" class="max-h-36 rounded-xl border border-indigo-200 object-contain">
                            <button type="button" @click="addPreviewUrl = null; $el.closest('div').previousElementSibling.querySelector('input').value = ''" class="absolute -top-2 -right-2 w-5 h-5 bg-rose-500 text-white rounded-full text-xs font-bold flex items-center justify-center shadow">&times;</button>
                        </div>
                    </template>
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, GIF, WebP. Maks. 2MB.</p>
                </div>

                <!-- 1. FORM PILIHAN GANDA (PG) -->
                <div x-show="addType === 'pg'" class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Opsi Jawaban & Kunci (Pilih 1)</label>
                    @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                        <div class="flex items-center space-x-3">
                            <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-xs flex items-center justify-center uppercase">{{ $opt }}</span>
                            <input type="text" name="option_{{ $opt }}" placeholder="Jawaban Opsi {{ strtoupper($opt) }}" class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                        </div>
                    @endforeach

                    <div class="pt-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kunci Jawaban Benar <span class="text-rose-500">*</span></label>
                        <select name="correct_answer" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-extrabold text-xs text-emerald-600 focus:ring-2 focus:ring-emerald-500">
                            <option value="a">Opsi A</option>
                            <option value="b">Opsi B</option>
                            <option value="c">Opsi C</option>
                            <option value="d">Opsi D</option>
                            <option value="e">Opsi E</option>
                        </select>
                    </div>
                </div>

                <!-- 2. FORM PG KOMPLEKS -->
                <div x-show="addType === 'pg_kompleks'" class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Opsi Jawaban & Centang Kunci Yang Benar (Bisa >1)</label>
                    @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="correct_answers_kompleks[]" value="{{ $opt }}" class="w-4 h-4 text-purple-600 rounded-md focus:ring-purple-500">
                            <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-700 font-extrabold text-xs flex items-center justify-center uppercase">{{ $opt }}</span>
                            <input type="text" name="option_{{ $opt }}" placeholder="Jawaban Opsi {{ strtoupper($opt) }}" class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                        </div>
                    @endforeach
                    <p class="text-[11px] text-purple-600 font-semibold">* Centang kotak di sebelah kiri opsi yang bernilai BENAR.</p>
                </div>

                <!-- 3. FORM BENAR / SALAH -->
                <div x-show="addType === 'benar_salah'" class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Pernyataan & Kunci Jawaban</label>
                    
                    <template x-for="(stmt, index) in addBsStatements" :key="index">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 font-extrabold text-xs flex items-center justify-center shrink-0" x-text="index + 1"></span>
                            <input type="text" name="bs_statements[]" x-model="addBsStatements[index]" placeholder="Tuliskan statement / pernyataan..." class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                            <select name="bs_answers[]" x-model="addBsAnswers[index]" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-emerald-700">
                                <option value="benar">BENAR</option>
                                <option value="salah">SALAH</option>
                            </select>
                            <button type="button" @click="addBsStatements.splice(index, 1); addBsAnswers.splice(index, 1)" class="text-rose-500 font-bold text-xs p-1" title="Hapus Pernyataan">&times;</button>
                        </div>
                    </template>

                    <button type="button" @click="addBsStatements.push(''); addBsAnswers.push('benar')" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Baris Pernyataan</span>
                    </button>
                </div>

                <!-- 4. FORM MENJODOHKAN -->
                <div x-show="addType === 'menjodohkan'" class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pasangkan Item Kiri & Jawaban Kanan</label>

                    <template x-for="(premise, index) in addMatchPremises" :key="index">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 bg-amber-50/50 border border-amber-200 rounded-xl relative">
                            <div>
                                <label class="block text-[10px] font-bold text-amber-900 uppercase">Item Kiri (Soal)</label>
                                <input type="text" name="match_premises[]" x-model="addMatchPremises[index]" placeholder="Contoh: Indonesia" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-amber-900 uppercase">Pasangan Kanan (Jawaban)</label>
                                <input type="text" name="match_targets[]" x-model="addMatchTargets[index]" placeholder="Contoh: Jakarta" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold">
                                <input type="hidden" name="match_answers[]" :value="addMatchTargets[index]">
                            </div>
                            <button type="button" @click="addMatchPremises.splice(index, 1); addMatchTargets.splice(index, 1)" class="absolute -top-2 -right-2 w-5 h-5 bg-rose-500 text-white rounded-full text-xs font-bold flex items-center justify-center shadow-xs">&times;</button>
                        </div>
                    </template>

                    <button type="button" @click="addMatchPremises.push(''); addMatchTargets.push('')" class="text-xs font-bold text-amber-600 hover:text-amber-700 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Pasangan Item</span>
                    </button>
                </div>

                <!-- 5. FORM ESSAY -->
                <div x-show="addType === 'essay'" class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pedoman / Kunci Jawaban Referensi (Opsional)</label>
                    <textarea name="essay_key" rows="3" placeholder="Tuliskan kunci/pedoman jawaban essay untuk acuan penilaian..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium"></textarea>
                    <p class="text-[11px] text-rose-600 font-semibold">* Siswa akan diberikan kolom teks bebas (textarea) untuk menuliskan jawaban uraian secara mandiri.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bobot Skor / Poin</label>
                    <input type="number" name="score_weight" value="10" min="1" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-extrabold text-xs">
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                    <button type="button" @click="showAddQuestionModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary">
                        Simpan Soal Ke Bank
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Edit Soal -->
    <div x-show="showEditQuestionModal && editQuestion" 
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4" 
         x-cloak
         style="display: none;">
        <div class="bg-white rounded-2xl border border-slate-200 max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto" 
             @click.outside="showEditQuestionModal = false; editQuestion = null">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Edit Soal Ujian</h3>
                <button type="button" @click="showEditQuestionModal = false; editQuestion = null" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <template x-if="editQuestion">
                <form method="POST" :action="'{{ url('admin/exams/' . $exam->id . '/questions') }}/' + editQuestion.id" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe / Jenis Soal <span class="text-rose-500">*</span></label>
                        <select name="type" x-model="editType" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-extrabold text-xs text-indigo-600 focus:ring-2 focus:ring-indigo-500">
                            <option value="pg">1. Pilihan Ganda (PG)</option>
                            <option value="pg_kompleks">2. Pilihan Ganda Kompleks</option>
                            <option value="benar_salah">3. Benar / Salah</option>
                            <option value="menjodohkan">4. Menjodohkan</option>
                            <option value="essay">5. Essay / Uraian</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Teks Pertanyaan <span class="text-rose-500">*</span></label>
                        <textarea name="question_text" rows="3" required class="w-full px-4 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-sm font-medium" x-model="editQuestion.question_text"></textarea>
                    </div>

                    <!-- Upload / Ganti Gambar Soal -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Gambar Soal (Opsional)</label>
                        <template x-if="editPreviewUrl && !removeImage">
                            <div class="mb-2 relative inline-block">
                                <img :src="editPreviewUrl" class="max-h-36 rounded-xl border border-slate-200 object-contain">
                                <button type="button"
                                    @click="removeImage = true; editPreviewUrl = null"
                                    class="absolute -top-2 -right-2 w-5 h-5 bg-rose-500 text-white rounded-full text-xs font-bold flex items-center justify-center shadow cursor-pointer">&times;</button>
                            </div>
                        </template>
                        <input type="hidden" name="remove_image" :value="removeImage ? '1' : '0'">
                        <div>
                            <input type="file" name="question_image" accept="image/*"
                                class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-300 rounded-xl p-1"
                                @change="const f = $event.target.files[0]; if (f) { editPreviewUrl = URL.createObjectURL(f); removeImage = false; }">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Pilih file baru untuk mengganti gambar. Format: JPG, PNG, GIF, WebP. Maks. 2MB.</p>
                    </div>

                    <!-- EDIT FORM PG -->
                    <div x-show="editType === 'pg'" class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Opsi Jawaban & Kunci</label>
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                            <div class="flex items-center space-x-3">
                                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-xs flex items-center justify-center uppercase">{{ $opt }}</span>
                                <input type="text" name="option_{{ $opt }}" x-model="editQuestion.option_{{ $opt }}" class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                            </div>
                        @endforeach

                        <div class="pt-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kunci Jawaban Benar</label>
                            <select name="correct_answer" x-model="editQuestion.correct_answer" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-extrabold text-xs text-emerald-600">
                                @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                                    <option value="{{ $opt }}">Opsi {{ strtoupper($opt) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- EDIT FORM PG KOMPLEKS -->
                    <div x-show="editType === 'pg_kompleks'" class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Opsi Jawaban & Kunci Jamak</label>
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="correct_answers_kompleks[]" value="{{ $opt }}" x-model="editPgKompleksAnswers" class="w-4 h-4 text-purple-600 rounded-md">
                                <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-700 font-extrabold text-xs flex items-center justify-center uppercase">{{ $opt }}</span>
                                <input type="text" name="option_{{ $opt }}" x-model="editQuestion.option_{{ $opt }}" class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                            </div>
                        @endforeach
                    </div>

                    <!-- EDIT FORM BENAR SALAH -->
                    <div x-show="editType === 'benar_salah'" class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pernyataan & Kunci Jawaban</label>
                        <template x-for="(stmt, index) in editBsStatements" :key="index">
                            <div class="flex items-center space-x-2">
                                <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 font-extrabold text-xs flex items-center justify-center shrink-0" x-text="index + 1"></span>
                                <input type="text" name="bs_statements[]" x-model="editBsStatements[index]" class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                                <select name="bs_answers[]" x-model="editBsAnswers[index]" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-emerald-700">
                                    <option value="benar">BENAR</option>
                                    <option value="salah">SALAH</option>
                                </select>
                                <button type="button" @click="editBsStatements.splice(index, 1); editBsAnswers.splice(index, 1)" class="text-rose-500 font-bold text-xs p-1">&times;</button>
                            </div>
                        </template>
                        <button type="button" @click="editBsStatements.push(''); editBsAnswers.push('benar')" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Baris</span>
                        </button>
                    </div>

                    <!-- EDIT FORM MENJODOHKAN -->
                    <div x-show="editType === 'menjodohkan'" class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pasangan Item Kiri & Jawaban Kanan</label>
                        <template x-for="(premise, index) in editMatchPremises" :key="index">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 bg-amber-50/50 border border-amber-200 rounded-xl relative">
                                <div>
                                    <label class="block text-[10px] font-bold text-amber-900 uppercase">Item Kiri</label>
                                    <input type="text" name="match_premises[]" x-model="editMatchPremises[index]" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-amber-900 uppercase">Pasangan Kanan</label>
                                    <input type="text" name="match_targets[]" x-model="editMatchTargets[index]" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold">
                                    <input type="hidden" name="match_answers[]" :value="editMatchTargets[index]">
                                </div>
                                <button type="button" @click="editMatchPremises.splice(index, 1); editMatchTargets.splice(index, 1)" class="absolute -top-2 -right-2 w-5 h-5 bg-rose-500 text-white rounded-full text-xs font-bold flex items-center justify-center cursor-pointer">&times;</button>
                            </div>
                        </template>
                        <button type="button" @click="editMatchPremises.push(''); editMatchTargets.push('')" class="text-xs font-bold text-amber-600 hover:text-amber-700 inline-flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Pasangan</span>
                        </button>
                    </div>

                    <!-- EDIT FORM ESSAY -->
                    <div x-show="editType === 'essay'" class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pedoman / Kunci Jawaban Referensi (Opsional)</label>
                        <textarea name="essay_key" rows="3" x-model="editEssayKey" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bobot Skor / Poin</label>
                        <input type="number" name="score_weight" x-model="editQuestion.score_weight" min="1" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-extrabold text-xs">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                        <button type="button" @click="showEditQuestionModal = false; editQuestion = null" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary">
                            Update Soal
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- MODAL: Edit Informasi Ujian -->
    <div x-show="showEditExamModal" 
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4 sm:p-6" 
         x-cloak
         style="display: none;">
        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark max-w-3xl sm:max-w-4xl w-full p-6 sm:p-7 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto" 
             @click.outside="showEditExamModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Edit Informasi Ujian CBT</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Perbarui judul, mata pelajaran, guru pengampu, target kelas, durasi, dan status publikasi.</p>
                    </div>
                </div>
                <button type="button" @click="showEditExamModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold cursor-pointer transition">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.exams.update', $exam->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Judul Ujian / Kuis <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title', $exam->title) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm font-semibold transition-all">
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
                                <option value="{{ $subVal }}" {{ old('subject_name', $exam->subject_name) == $subVal ? 'selected' : '' }}>
                                    {{ $subVal }}
                                </option>
                            @endforeach
                            @if($exam->subject_name && !in_array($exam->subject_name, collect($subjList)->map(fn($s) => is_object($s) ? $s->name : $s)->toArray()))
                                <option value="{{ $exam->subject_name }}" selected>{{ $exam->subject_name }}</option>
                            @endif
                        </select>
                    </div>

                    <div>
                        <x-teacher-select-search 
                            :teachers="$teachers ?? []" 
                            name="teacher_id" 
                            value-type="id" 
                            :selected="old('teacher_id', $exam->teacher_id)" 
                            label="Guru Pengampu / Pengajar Ujian" 
                            placeholder="-- Cari & Pilih Guru Pengampu --" 
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                    <x-major-class-select major-label="Peruntukan Jurusan" class-label="Peruntukan Kelas" :selected-class="$exam->class_id" select-class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm font-semibold transition-all" label-class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2" />
                </div>

                <!-- LMS Sub-Bab Link -->
                <x-lms-topic-select-search 
                    :chapters="$lmsChapters ?? []" 
                    name="lms_topic_id" 
                    :selected="old('lms_topic_id', $exam->lms_topic_id)" 
                    accent-color="amber"
                    label="Tautkan ke Sub-Bab Modul LMS (Opsional)" 
                />

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Durasi (Menit) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="duration_minutes" required min="5" value="{{ old('duration_minutes', $exam->duration_minutes) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm font-bold transition-all">
                            <span class="absolute right-3.5 top-3 text-xs font-bold text-slate-400">Menit</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Waktu Buka Ujian (Opsional)</label>
                        <input type="datetime-local" name="start_time" value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-xs font-medium transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Waktu Selesai (Opsional)</label>
                        <input type="datetime-local" name="end_time" value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-xs font-medium transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Deskripsi / Petunjuk Pengerjaan</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 text-sm font-medium transition-all">{{ old('description', $exam->description) }}</textarea>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-boxdark-2 rounded-xl border border-slate-200 dark:border-slate-700/70 flex items-center justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-xs">Status Publikasi Ujian</h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Jika diaktifkan, paket ujian ini langsung dapat diakses oleh siswa.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $exam->is_published) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end space-x-3">
                    <button type="button" @click="showEditExamModal = false" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL IMPORT SOAL CBT EXCEL -->
    <div x-show="showImportQuestionsModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         x-cloak
         @click.self="if (!isImportingSoal) showImportQuestionsModal = false">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
            
            {{-- Header --}}
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Import Soal Ujian (Excel)</h3>
                        <p class="text-[10px] text-slate-400 font-medium">Unggah berkas spreadsheet format .xlsx</p>
                    </div>
                </div>
                <button x-show="!isImportingSoal" type="button" @click="showImportQuestionsModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">&times;</button>
            </div>

            {{-- Form Upload (Hidden during upload) --}}
            <form x-show="!isImportingSoal" action="{{ route('admin.exams.questions.import', $exam->id) }}" method="POST" enctype="multipart/form-data" @submit="startSoalImportLoading()" class="p-6 space-y-4">
                @csrf
                <div class="p-3.5 bg-indigo-50/80 rounded-2xl border border-indigo-100 text-xs space-y-2 text-indigo-900">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Petunjuk Format Template Soal Excel:</span>
                    </p>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Format kolom baris 1: <strong>No, Jenis Soal, Teks Soal, Pilihan A, Pilihan B, Pilihan C, Pilihan D, Pilihan E, Kunci Jawaban, Bobot Nilai</strong>.
                    </p>
                    <p class="text-[10px] text-indigo-700 font-medium">
                        *Jenis soal yang didukung: <code>pg</code>, <code>pg_kompleks</code>, <code>benar_salah</code>, <code>essay</code>.
                    </p>
                    <div class="pt-1 border-t border-indigo-200/60">
                        <a href="{{ route('admin.exams.download-questions-template') }}" class="inline-flex items-center gap-1.5 text-[11px] font-extrabold text-indigo-600 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download Template Excel (.xlsx)</span>
                        </a>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Berkas Excel (.xlsx / .xls)</label>
                    <x-file-upload name="file" accept=".xlsx,.xls,.csv" required="true" label="Upload Berkas Excel Soal CBT" help="Seret & lepas berkas Excel (.xlsx / .xls) di sini" />
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showImportQuestionsModal = false" class="px-4 py-2.5 text-slate-700 bg-slate-100 rounded-xl text-xs font-bold hover:bg-slate-200 transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Proses Import Soal</span>
                    </button>
                </div>
            </form>

            {{-- ANIMASI LOADING & RUNNING TEXT --}}
            <div x-show="isImportingSoal" class="p-8 text-center space-y-6">
                {{-- Spinner Ring & Icon Animation --}}
                <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-4 border-indigo-100 border-t-indigo-600 animate-spin"></div>
                    <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center animate-pulse">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                </div>

                {{-- Text Running / Ticker Animation --}}
                <div class="space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-wider rounded-full border border-indigo-200">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span>
                        Mengimpor Soal Ujian...
                    </span>
                    <h4 class="text-sm font-black text-slate-900 h-6 transition-all duration-300" x-text="soalStatusText"></h4>
                    <p class="text-xs text-slate-400 font-medium">Mohon tunggu, soal sedang dimasukkan ke bank soal.</p>
                </div>

                {{-- Animated Progress Bar --}}
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden relative">
                    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 h-2 rounded-full w-full animate-pulse"></div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
