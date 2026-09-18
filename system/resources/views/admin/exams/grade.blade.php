@extends('layouts.admin')

@section('title', 'Koreksi Essay Ujian CBT - ' . $exam->title)
@section('page_title', 'Koreksi Manual Soal Essay Ujian CBT')

@section('content')
<div class="w-full space-y-6">
    <!-- Header & Exam Summary Card -->
    <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark p-6 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                <span>{{ $exam->subject_name }}</span>
                <span>•</span>
                <span>{{ $exam->class->name ?? 'Semua Kelas' }}</span>
                <span>•</span>
                <span class="text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg text-[11px] font-bold">
                    Guru Pengampu: {{ $exam->teacher->user->name ?? $exam->teacher->name ?? 'Belum Ditentukan' }}
                </span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Koreksi Essay: {{ $exam->title }}
            </h2>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400 font-medium">
                <span>Total Peserta: <strong class="text-slate-700 dark:text-slate-200">{{ count($results) }} Siswa</strong></span>
                <span>•</span>
                <span>Menunggu Koreksi: <strong class="text-amber-600 dark:text-amber-400">{{ $results->where('status', 'needs_grading')->count() }} Siswa</strong></span>
                <span>•</span>
                <span>Jumlah Soal Essay: <strong class="text-indigo-600 dark:text-indigo-400">{{ count($essayQuestions) }} Soal</strong></span>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.exams.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Ujian
            </a>
        </div>
    </div>

    <!-- Form Koreksi Essay (Grouping by Question) -->
    <form method="POST" action="{{ route('admin.exams.store-grade-all', $exam->id) }}" class="space-y-8">
        @csrf

        @forelse($essayQuestions as $qIndex => $q)
            <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark p-6 shadow-sm space-y-6">
                <!-- Header Soal (DI PALING ATAS UTAMA) -->
                <div class="bg-indigo-50/70 dark:bg-indigo-950/40 rounded-xl p-5 border border-indigo-100 dark:border-indigo-900/50 space-y-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                {{ $qIndex + 1 }}
                            </span>
                            <span class="px-2.5 py-0.5 text-xs font-black uppercase tracking-wider bg-indigo-200 text-indigo-900 dark:bg-indigo-900 dark:text-indigo-200 rounded-md">
                                Soal Essay Uraian
                            </span>
                        </div>
                        <span class="px-3 py-1 text-xs font-extrabold bg-white dark:bg-slate-800 text-indigo-700 dark:text-indigo-300 rounded-lg border border-indigo-200 dark:border-indigo-800 shrink-0">
                            Bobot Maksimal: {{ $q->score_weight }} Poin
                        </span>
                    </div>

                    <div class="text-sm leading-relaxed space-y-1">
                        <span class="text-xs font-black uppercase text-indigo-700 dark:text-indigo-300 tracking-wider block">Pertanyaan Uraian / Essay:</span>
                        <div class="font-bold text-slate-900 dark:text-white text-base">{!! nl2br(e($q->question_text)) !!}</div>
                    </div>

                    @if($q->image_path)
                        <div class="pt-2">
                            <img src="{{ \Illuminate\Support\Str::startsWith($q->image_path, 'img/') ? asset($q->image_path) : asset('img/' . $q->image_path) }}" 
                                 alt="Gambar Soal" 
                                 class="max-h-56 rounded-xl border border-slate-200 dark:border-slate-700 object-contain shadow-2xs">
                        </div>
                    @endif

                    @if(!empty($q->options_json['sample_answer']) || !empty($q->correct_answer))
                        <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 rounded-lg text-xs font-medium text-amber-900 dark:text-amber-200">
                            <span class="font-black uppercase text-[10px] text-amber-700 dark:text-amber-400 tracking-wider block mb-0.5">Kunci Referensi / Pedoman Guru:</span>
                            <div class="leading-relaxed font-semibold">{!! nl2br(e($q->options_json['sample_answer'] ?? $q->correct_answer)) !!}</div>
                        </div>
                    @endif
                </div>

                <!-- Lista Jawaban Seluruh Siswa Untuk Soal Ini -->
                <div class="space-y-4 pt-1">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2">
                        <span>Jawaban Siswa untuk Soal #{{ $qIndex + 1 }} ({{ count($results) }} Siswa)</span>
                        <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400">Input poin hasil koreksi di setiap baris</span>
                    </h4>

                    @foreach($results as $resIndex => $result)
                        @php
                            $studentName = $result->student->user->name ?? $result->student->name;
                            $submittedAnswers = $result->answers ?? [];
                            $savedEssayScores = $submittedAnswers['_essay_scores'] ?? [];
                            $savedEssayFeedback = $submittedAnswers['_essay_feedback'] ?? [];
                            $studentAnswer = $submittedAnswers[$q->id] ?? null;
                            $currentScore = old('essay_scores.'.$result->id.'.'.$q->id, $savedEssayScores[$q->id] ?? 0);
                            $currentFeedback = old('essay_feedback.'.$result->id.'.'.$q->id, $savedEssayFeedback[$q->id] ?? '');
                        @endphp

                        <div class="p-4 bg-slate-50/70 dark:bg-boxdark-2/70 rounded-xl border border-slate-200 dark:border-slate-700/80 space-y-3">
                            <!-- Info Siswa & Status -->
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center space-x-2.5">
                                    <span class="w-6 h-6 rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ $resIndex + 1 }}
                                    </span>
                                    <span class="font-extrabold text-slate-900 dark:text-white text-sm">
                                        {{ $studentName }}
                                    </span>
                                    <span class="text-xs font-medium text-slate-400">({{ $result->student->nisn }} - {{ $result->student->class->name ?? '-' }})</span>
                                </div>
                                <span class="text-[11px] font-bold text-slate-400">
                                    Dikumpulkan: {{ $result->submitted_at ? $result->submitted_at->format('H:i') : '-' }}
                                </span>
                            </div>

                            <!-- Jawaban Siswa -->
                            <div class="p-3 bg-white dark:bg-boxdark rounded-lg border border-slate-200 dark:border-slate-700 flex items-start gap-2">
                                <span class="text-[11px] font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider shrink-0 mt-0.5">Jawaban Siswa:</span>
                                <div class="flex-1 min-w-0">
                                    @if($studentAnswer !== null && trim($studentAnswer) !== '')
                                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-100 whitespace-pre-wrap leading-relaxed">{{ $studentAnswer }}</div>
                                    @else
                                        <div class="text-xs font-bold text-rose-500 italic">(Siswa tidak mengisi jawaban)</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Input Poin & Feedback 1 Baris Sejajar -->
                            <div class="flex flex-col md:flex-row items-center gap-3 pt-1">
                                <div class="w-full md:w-72 shrink-0 flex items-center gap-2">
                                    <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider shrink-0">
                                        Poin (0 - {{ $q->score_weight }}):
                                    </label>
                                    <div class="relative flex-1">
                                        <input type="number" 
                                               name="essay_scores[{{ $result->id }}][{{ $q->id }}]" 
                                               step="0.5" 
                                               min="0" 
                                               max="{{ $q->score_weight }}" 
                                               required 
                                               value="{{ $currentScore }}" 
                                               class="w-full px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark text-slate-900 dark:text-white font-extrabold text-sm focus:ring-2 focus:ring-indigo-500">
                                        <span class="absolute right-3 top-2 text-xs font-bold text-slate-400">/ {{ $q->score_weight }}</span>
                                    </div>
                                </div>

                                <div class="w-full flex-1 flex items-center gap-2">
                                    <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider shrink-0 hidden sm:inline">
                                        Catatan:
                                    </label>
                                    <input type="text" 
                                           name="essay_feedback[{{ $result->id }}][{{ $q->id }}]" 
                                           value="{{ $currentFeedback }}" 
                                           placeholder="Catatan / feedback guru untuk {{ $studentName }} (opsional)" 
                                           class="w-full px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-boxdark text-slate-900 dark:text-white font-medium text-xs focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark p-12 text-center text-slate-400 dark:text-slate-500 font-medium">
                Belum ada soal essay untuk ujian CBT ini.
            </div>
        @endforelse

        @if(count($essayQuestions) > 0 && count($results) > 0)
            <!-- Bottom Sticky Action Bar -->
            <div class="sticky bottom-4 z-20 bg-white/95 dark:bg-boxdark/95 backdrop-blur-md rounded-2xl border border-slate-200 dark:border-strokedark p-4 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Nilai akhir seluruh {{ count($results) }} siswa akan dihitung secara otomatis setelah Anda menyimpan.
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.exams.index') }}" class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition-colors text-center">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-xl text-xs shadow-md transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan & Terbitkan Seluruh Nilai Siswa ({{ count($results) }} Siswa)
                    </button>
                </div>
            </div>
        @endif
    </form>
</div>
@endsection
