@extends('layouts.student-mobile')

@section('title', 'Laporan Nilai Siswa')
@section('header_title', 'Laporan Nilai & Hasil Belajar')

@section('content')
<div class="space-y-4 sm:space-y-6 pb-12">
    <!-- Header Banner & Overall Performance Summary (Compact on Mobile) -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-indigo-900 via-indigo-850 to-slate-900 p-4 sm:p-6 text-white shadow-lg border border-indigo-700/40">
        <!-- Background decorative elements -->
        <div class="absolute -right-10 -bottom-10 w-40 h-40 sm:w-48 sm:h-48 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-blue-500/20 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 items-center">
            <!-- Main Avg Score Badge -->
            <div class="lg:col-span-1 flex items-center space-x-3.5 bg-white/10 backdrop-blur-md p-3.5 sm:p-4 rounded-2xl border border-white/15 shadow-inner">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-amber-300 via-amber-400 to-amber-500 text-slate-950 flex items-center justify-center font-black text-lg sm:text-2xl shadow-lg shrink-0 ring-2 ring-amber-300/40 tracking-tight">
                    {{ number_format((float)($stats['average'] ?? 0), 1) }}
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-extrabold text-indigo-200 uppercase tracking-wider block truncate">Rata-Rata Kumulatif</span>
                    <h3 class="text-sm sm:text-lg font-extrabold text-white leading-tight mt-0.5 truncate">Predikat {{ $stats['predicate'] ?? '-' }}</h3>
                    <p class="text-[11px] sm:text-xs text-indigo-200/80 mt-0.5">KKM Sekolah: <span class="font-bold text-amber-300">75.0</span></p>
                </div>
            </div>

            <!-- Stats Pills -->
            <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
                <div class="bg-white/10 backdrop-blur-sm p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-white/10 text-center">
                    <p class="text-[9px] sm:text-[11px] text-indigo-200 font-bold uppercase tracking-wider">Tertinggi</p>
                    <p class="text-base sm:text-xl font-black text-emerald-300 mt-0.5 sm:mt-1">{{ number_format((float)($stats['highest'] ?? 0), 1) }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-white/10 text-center">
                    <p class="text-[9px] sm:text-[11px] text-indigo-200 font-bold uppercase tracking-wider">Terendah</p>
                    <p class="text-base sm:text-xl font-black text-rose-300 mt-0.5 sm:mt-1">{{ number_format((float)($stats['lowest'] ?? 0), 1) }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-white/10 text-center">
                    <p class="text-[9px] sm:text-[11px] text-indigo-200 font-bold uppercase tracking-wider">Evaluasi</p>
                    <p class="text-base sm:text-xl font-black text-white mt-0.5 sm:mt-1">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl border border-white/10 text-center">
                    <p class="text-[9px] sm:text-[11px] text-indigo-200 font-bold uppercase tracking-wider">Ketuntasan</p>
                    <p class="text-base sm:text-xl font-black text-amber-300 mt-0.5 sm:mt-1">{{ number_format((float)($stats['passed_percentage'] ?? 0), 0) }}%</p>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-white/10 flex justify-end">
            <a href="{{ route('student.raport.print') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-400 to-teal-500 hover:from-emerald-500 hover:to-teal-600 text-slate-950 rounded-xl font-extrabold text-xs shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Raport Resmi Saya (A4 / PDF)</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-slate-800/90 rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 shadow-xs border border-slate-200 dark:border-slate-700">
        <form method="GET" action="{{ route('student.grades.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <!-- Search -->
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari catatan / mapel..." 
                    class="w-full pl-8 sm:pl-9 pr-3 py-2 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 text-xs rounded-xl border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 sm:left-3 top-2.5 sm:top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <!-- Subject Filter -->
            <div>
                <select name="subject" onchange="this.form.submit()" class="w-full px-3 py-2 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 text-xs rounded-xl border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">-- Semua Mata Pelajaran --</option>
                    @foreach($subjectsList as $subj)
                        <option value="{{ $subj }}" {{ request('subject') === $subj ? 'selected' : '' }}>{{ $subj }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 text-xs rounded-xl border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">-- Semua Tipe Evaluasi --</option>
                    <option value="daily" {{ request('type') === 'daily' ? 'selected' : '' }}>Tugas Harian</option>
                    <option value="mid_term" {{ request('type') === 'mid_term' ? 'selected' : '' }}>UTS (Tengah Semester)</option>
                    <option value="final_term" {{ request('type') === 'final_term' ? 'selected' : '' }}>UAS (Akhir Semester)</option>
                    <option value="exam" {{ request('type') === 'exam' ? 'selected' : '' }}>Ujian Bulanan</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 sm:py-2.5 px-4 rounded-xl transition-colors shadow-xs cursor-pointer">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'subject', 'type']))
                    <a href="{{ route('student.grades.index') }}" class="bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 font-bold text-xs py-2 sm:py-2.5 px-3 rounded-xl transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Left Column: Subject Summary & Progress Bar -->
        @if($gradesBySubject->count() > 0)
            <div class="lg:col-span-1 bg-white dark:bg-slate-800/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xs border border-slate-200 dark:border-slate-700 h-fit">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100 dark:border-slate-700 mb-3">
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v16a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Ringkasan Mapel</span>
                    </h3>
                    <span class="text-[10px] sm:text-[11px] font-extrabold text-indigo-600 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-md border border-indigo-100 dark:border-indigo-800">{{ $gradesBySubject->count() }} Mapel</span>
                </div>

                <div class="space-y-3 max-h-[450px] sm:max-h-[550px] overflow-y-auto pr-1">
                    @foreach($gradesBySubject as $subject)
                        @php
                            $avg = round($subject->average, 1);
                            $barColor = $avg >= 75 ? 'bg-emerald-500' : ($avg >= 60 ? 'bg-amber-500' : 'bg-rose-500');
                            $textColor = $avg >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($avg >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400');
                            $badgeBg = $avg >= 75 ? 'bg-emerald-50 dark:bg-emerald-950/60 border-emerald-200 dark:border-emerald-800' : ($avg >= 60 ? 'bg-amber-50 dark:bg-amber-950/60 border-amber-200 dark:border-amber-800' : 'bg-rose-50 dark:bg-rose-950/60 border-rose-200 dark:border-rose-800');
                        @endphp
                        <div class="p-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-700/80 hover:border-indigo-300 dark:hover:border-indigo-500 transition-all space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-extrabold text-xs text-slate-800 dark:text-slate-200 truncate max-w-[160px] sm:max-w-[170px]">{{ $subject->subject }}</span>
                                <span class="text-xs font-black px-2 py-0.5 rounded-lg border shrink-0 {{ $badgeBg }} {{ $textColor }}">
                                    {{ number_format($avg, 1) }}
                                </span>
                            </div>
                            <!-- Progress Bar -->
                            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 sm:h-2 overflow-hidden">
                                <div class="{{ $barColor }} h-1.5 sm:h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $avg) }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                <span>{{ $subject->count }} Penilaian</span>
                                <span>Min: {{ number_format($subject->min_score, 1) }} | Max: {{ number_format($subject->max_score, 1) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Right Column: Detail Grade History Logs -->
        <div class="{{ $gradesBySubject->count() > 0 ? 'lg:col-span-2' : 'lg:col-span-3' }} bg-white dark:bg-slate-800/90 rounded-2xl sm:rounded-3xl shadow-xs border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/40 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm">Riwayat Catatan Nilai</h3>
                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">Evaluasi belajar terverifikasi guru pengampu</p>
                    </div>
                    <span class="text-[10px] sm:text-xs font-extrabold text-indigo-600 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 px-2.5 py-0.5 sm:py-1 rounded-full border border-indigo-100 dark:border-indigo-800 shrink-0">
                        {{ $grades->total() }} Data
                    </span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-700/80">
                    @forelse($grades as $grade)
                        @php
                            $isPassed = $grade->score >= 75;
                            $scoreColor = $isPassed ? 'text-emerald-600 dark:text-emerald-400' : ($grade->score >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400');
                            $scoreBg = $isPassed ? 'bg-emerald-50 dark:bg-emerald-950/60 border-emerald-200 dark:border-emerald-800' : ($grade->score >= 60 ? 'bg-amber-50 dark:bg-amber-950/60 border-amber-200 dark:border-amber-800' : 'bg-rose-50 dark:bg-rose-950/60 border-rose-200 dark:border-rose-800');
                            
                            $typeLabel = match($grade->type) {
                                'daily' => 'Harian',
                                'mid_term' => 'UTS',
                                'final_term' => 'UAS',
                                'exam' => 'Ujian',
                                default => 'Ujian'
                            };
                            $typeBg = match($grade->type) {
                                'daily' => 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                                'mid_term' => 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                'final_term' => 'bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                'exam' => 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                default => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600'
                            };
                        @endphp
                        <div class="p-3.5 sm:p-4 hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition-colors">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                                        <h4 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm truncate max-w-[150px] sm:max-w-none">{{ $grade->subject }}</h4>
                                        <span class="text-[9px] sm:text-[10px] font-extrabold tracking-wide uppercase px-1.5 py-0.2 sm:px-2 sm:py-0.5 rounded-md border {{ $typeBg }}">
                                            {{ $typeLabel }}
                                        </span>
                                        <span class="text-[9px] sm:text-[10px] font-bold px-1.5 py-0.2 sm:px-2 sm:py-0.5 rounded-md border {{ $isPassed ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800' }}">
                                            {{ $isPassed ? 'Tuntas' : 'Belum Tuntas' }}
                                        </span>
                                    </div>

                                    <div class="mt-1 flex items-center flex-wrap gap-x-3 gap-y-0.5 text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">
                                        <span class="flex items-center space-x-1 font-medium shrink-0">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>{{ $grade->created_at ? $grade->created_at->translatedFormat('d M Y') : '-' }}</span>
                                        </span>
                                        @if($grade->notes)
                                            <span class="text-slate-600 dark:text-slate-300 font-medium truncate max-w-[140px] sm:max-w-xs">• {{ $grade->notes }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <div class="px-2.5 py-1 sm:px-3.5 sm:py-1.5 rounded-xl sm:rounded-2xl border {{ $scoreBg }} text-center inline-block">
                                        <span class="text-lg sm:text-2xl font-black tracking-tight {{ $scoreColor }}">
                                            {{ number_format($grade->score, 1) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 sm:p-10 text-center">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-slate-100 dark:bg-slate-700/60 text-slate-400 dark:text-slate-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v16a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <p class="text-slate-800 dark:text-white font-bold text-sm">Belum Ada Catatan Nilai</p>
                            <p class="text-xs text-slate-400 dark:text-slate-400 mt-1">Nilai evaluasi belajar akan muncul setelah guru memposting nilai Anda.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($grades->hasPages())
                <div class="p-3 sm:p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    {{ $grades->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
