@extends('layouts.student-mobile')

@section('title', 'Kuis & Ujian Online (CBT)')
@section('header_title', 'Ujian Online CBT')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-indigo-900 via-purple-900 to-slate-900 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden">
        <div class="flex items-center justify-between relative z-10">
            <div>
                <span class="px-3 py-1 bg-white/10 text-indigo-200 text-[10px] font-extrabold uppercase tracking-wider rounded-full border border-white/20 inline-block mb-2">
                    Computer Based Test (CBT)
                </span>
                <h2 class="text-lg sm:text-xl font-extrabold text-white">Ruang Kuis & Ujian Online</h2>
                <p class="text-xs text-indigo-200 mt-1">Kerjakan ujian online dengan timer otomatis dan penilaian instan.</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/20">
                <svg class="w-7 h-7 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Exams List -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-900 text-sm">Daftar Ujian Aktif</h3>
            <span class="text-xs font-bold text-slate-500">{{ $exams->count() }} Ujian Tersedia</span>
        </div>

        @if($exams->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($exams as $exam)
                    @php
                        $examResult = $results[$exam->id] ?? null;
                        $isSubmitted = $examResult && in_array($examResult->status, ['completed', 'needs_grading']);
                    @endphp
                    <div class="p-5 rounded-2xl border {{ $isSubmitted ? ($examResult->status === 'needs_grading' ? 'border-amber-200 bg-amber-50/30' : 'border-emerald-200 bg-emerald-50/30') : 'border-slate-200 bg-white hover:border-indigo-300' }} transition-all flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2.5 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-bold rounded-full">
                                    {{ $exam->subject_name }}
                                </span>
                                <span class="text-[11px] font-bold text-slate-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $exam->duration_minutes }} Menit
                                </span>
                            </div>

                            <h4 class="font-extrabold text-slate-900 text-base leading-snug">{{ $exam->title }}</h4>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $exam->description ?? 'Tidak ada deskripsi tambahan.' }}</p>

                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-600">
                                <span>Jumlah Soal: <strong class="text-slate-900">{{ $exam->questions->count() }} Soal</strong></span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-md">
                                    <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    Anti-Cheat & Layar Penuh
                                </span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            @if($isSubmitted)
                                @if($examResult->status === 'needs_grading')
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-slate-500">Objektif:</span>
                                        <span class="px-2.5 py-1 font-mono font-bold text-xs rounded-xl bg-amber-100 text-amber-900 border border-amber-200">
                                            {{ number_format($examResult->score, 2) }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-extrabold text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Menunggu Koreksi Guru</span>
                                    </span>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-slate-500">Nilai Anda:</span>
                                        @php
                                            $score = floatval($examResult->score);
                                            $badgeClass = $score >= 80
                                                ? 'bg-emerald-500 text-white'
                                                : ($score >= 60
                                                    ? 'bg-amber-400 text-amber-900'
                                                    : 'bg-rose-500 text-white');
                                        @endphp
                                        <span class="px-3 py-1 font-mono font-black text-xs rounded-xl {{ $badgeClass }}">
                                            {{ number_format($examResult->score, 2) }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-extrabold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Sudah Dikumpulkan
                                    </span>
                                @endif
                            @else
                                <span class="text-xs font-bold text-slate-500">Belum Dikumpulkan</span>
                                <a href="{{ route('student.exams.show', $exam->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                                    Mulai Ujian &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-xs text-slate-500 font-medium">Belum ada ujian online yang dipublikasikan saat ini.</p>
            </div>
        @endif
    </div>

</div>
@endsection
