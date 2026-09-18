@extends('layouts.student-mobile')

@section('title', $exam->title . ' - CBT Online')
@section('header_title', 'Ujian CBT: ' . $exam->title)

@section('content')
<div class="space-y-6 select-none" 
     x-data="{
        totalSeconds: {{ $exam->duration_minutes * 60 }},
        minutes: 0,
        seconds: 0,
        currentQuestion: 1,
        totalQuestions: {{ $exam->questions->count() }},
        showSubmitModal: false,
        
        // Anti-Cheat & Fullscreen State
        isExamStarted: false,
        isFullscreen: false,
        showFullscreenWarning: false,
        showViolationWarning: false,
        violationCount: 0,
        maxViolations: 3,
        lastViolationReason: '',
        isAutoSubmitting: false,
        hasSubmitted: false,
        timerInterval: null,

        startExam() {
            this.requestFullscreenMode();
            this.isExamStarted = true;
            this.initTimer();
            this.initAntiCheat();
        },

        requestFullscreenMode() {
            const elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen().catch(err => console.log('Fullscreen error:', err));
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
            this.isFullscreen = true;
            this.showFullscreenWarning = false;
        },

        initTimer() {
            this.updateTime();
            this.timerInterval = setInterval(() => {
                if (this.totalSeconds > 0) {
                    this.totalSeconds--;
                    this.updateTime();
                } else {
                    clearInterval(this.timerInterval);
                    this.submitExam('Waktu Ujian Telah Habis!');
                }
            }, 1000);
        },

        updateTime() {
            this.minutes = Math.floor(this.totalSeconds / 60);
            this.seconds = this.totalSeconds % 60;
        },

        initAntiCheat() {
            // 1. Fullscreen Change Listener
            const handleFullscreenChange = () => {
                const isFull = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
                this.isFullscreen = isFull;
                if (!isFull && this.isExamStarted && !this.hasSubmitted) {
                    this.recordViolation('Keluar dari mode layar penuh (Fullscreen)');
                    this.showFullscreenWarning = true;
                } else {
                    this.showFullscreenWarning = false;
                }
            };
            document.addEventListener('fullscreenchange', handleFullscreenChange);
            document.addEventListener('webkitfullscreenchange', handleFullscreenChange);

            // 2. Tab Switch / Window Blur Detection (Page Visibility API)
            document.addEventListener('visibilitychange', () => {
                if (document.hidden && this.isExamStarted && !this.hasSubmitted) {
                    this.recordViolation('Berpindah tab atau membuka aplikasi lain');
                }
            });

            // Window Blur (Window loses focus, e.g. clicking outside or alt-tab)
            let blurTimeout = null;
            window.addEventListener('blur', () => {
                if (this.isExamStarted && !this.hasSubmitted) {
                    blurTimeout = setTimeout(() => {
                        if (this.isExamStarted && !this.hasSubmitted) {
                            this.recordViolation('Membuka aplikasi lain atau jendela kehilangan fokus');
                        }
                    }, 600);
                }
            });
            window.addEventListener('focus', () => {
                if (blurTimeout) clearTimeout(blurTimeout);
            });

            // 3. Disable Shortcuts (Ctrl+C, Ctrl+V, Ctrl+T, Ctrl+N, Ctrl+W, F12, dll)
            window.addEventListener('keydown', (e) => {
                if (!this.isExamStarted || this.hasSubmitted) return;

                // F12 (DevTools)
                if (e.key === 'F12' || e.keyCode === 123) {
                    e.preventDefault();
                    return false;
                }

                // Ctrl/Cmd shortcuts
                if (e.ctrlKey || e.metaKey) {
                    const key = e.key.toLowerCase();
                    // Block Ctrl+C (Copy), Ctrl+V (Paste), Ctrl+X (Cut), Ctrl+U (View Source), Ctrl+S (Save), Ctrl+P (Print), Ctrl+A (Select All)
                    if (['c', 'v', 'x', 'u', 's', 'p', 'a'].includes(key)) {
                        e.preventDefault();
                        return false;
                    }
                    // Block Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C (Devtools)
                    if (e.shiftKey && ['i', 'j', 'c'].includes(key)) {
                        e.preventDefault();
                        return false;
                    }
                }
            });

            // 4. Disable Context Menu (Right Click)
            window.addEventListener('contextmenu', (e) => {
                if (this.isExamStarted) {
                    e.preventDefault();
                    return false;
                }
            });

            // 5. Prevent accidental tab close or reload
            window.addEventListener('beforeunload', (e) => {
                if (this.isExamStarted && !this.hasSubmitted) {
                    e.preventDefault();
                    e.returnValue = 'Ujian sedang berlangsung! Jika Anda meninggalkan halaman ini, jawaban Anda akan hangus.';
                }
            });
        },

        recordViolation(reason) {
            if (this.hasSubmitted || this.isAutoSubmitting) return;

            this.violationCount++;
            this.lastViolationReason = reason;

            if (this.violationCount >= this.maxViolations) {
                this.isAutoSubmitting = true;
                alert(`PERINGATAN KECURANGAN MAKSIMAL!\n\nAnda telah melakukan ${this.maxViolations} kali pelanggaran (${reason}).\n\nUjian Anda otomatis dikumpulkan sekarang.`);
                this.submitExam('Pelanggaran batas maksimal kecurangan (' + reason + ')');
            } else {
                this.showViolationWarning = true;
            }
        },

        submitExam(reason = null) {
            if (this.hasSubmitted) return;
            this.hasSubmitted = true;
            const form = document.getElementById('examForm');
            if (form) {
                const violationInput = document.createElement('input');
                violationInput.type = 'hidden';
                violationInput.name = 'violation_count';
                violationInput.value = this.violationCount;
                form.appendChild(violationInput);

                if (reason) {
                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'submission_reason';
                    reasonInput.value = reason;
                    form.appendChild(reasonInput);
                }
                form.submit();
            }
        }
    }">

    <!-- ===== 1. GATEKEEPER MODAL: KONFIRMASI MULAI UJIAN & MASUK FULLSCREEN ===== -->
    <div x-show="!isExamStarted" 
         class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-md flex items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100 p-6 sm:p-8 text-center space-y-5">
            <div class="w-16 h-16 mx-auto rounded-3xl bg-indigo-500/10 text-indigo-600 border border-indigo-200 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>

            <div>
                <span class="px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-black uppercase tracking-wider rounded-full">
                    Sistem Pengawasan CBT Online
                </span>
                <h2 class="text-lg sm:text-xl font-black text-slate-900 mt-2">{{ $exam->title }}</h2>
                <p class="text-xs text-slate-500 mt-1">{{ $exam->subject_name }} &bull; Durasi: <strong>{{ $exam->duration_minutes }} Menit</strong> &bull; {{ $exam->questions->count() }} Soal</p>
            </div>

            {{-- Aturan Anti-Kecurangan --}}
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-left space-y-2.5 text-xs text-slate-700">
                <div class="font-extrabold text-slate-900 flex items-center gap-1.5 pb-1 border-b border-slate-200">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Aturan Ketat Ujian Online:
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-600 font-bold shrink-0">1.</span>
                    <span>Halaman akan otomatis beralih ke <strong>Mode Layar Penuh (Fullscreen)</strong>.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-rose-600 font-bold shrink-0">2.</span>
                    <span><strong>Dilarang keras</strong> membuka tab baru, browser lain, atau aplikasi lain.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-amber-600 font-bold shrink-0">3.</span>
                    <span>Toleransi pelanggaran maksimal <strong>3 kali</strong>. Jika melanggar lagi, ujian langsung <strong>dikumpulkan otomatis</strong>.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-indigo-600 font-bold shrink-0">4.</span>
                    <span>Fitur copy-paste dan klik kanan <strong>dinonaktifkan</strong> selama ujian.</span>
                </div>
            </div>

            <button type="button" 
                    @click="startExam()"
                    class="w-full py-3.5 px-6 bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-700 hover:from-indigo-700 hover:to-purple-800 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-xl shadow-indigo-200 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                <span>Masuk Layar Penuh & Mulai Ujian</span>
            </button>
        </div>
    </div>

    <!-- ===== 2. STICKY EXAM TIMER & ANTI-CHEAT HEADER BAR ===== -->
    <div class="bg-slate-900 text-white rounded-2xl px-3 py-2.5 shadow-lg sticky top-20 z-30 flex items-center justify-between gap-2 border border-white/10">
        <div class="flex items-center gap-2 min-w-0">
            <div class="w-8 h-8 shrink-0 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-400/30 flex items-center justify-center font-black text-[10px]">
                CBT
            </div>
            <div class="min-w-0">
                <h3 class="font-extrabold text-xs text-white truncate">{{ $exam->title }}</h3>
                <div class="flex items-center gap-2 text-[10px] text-slate-400">
                    <span class="truncate">{{ $exam->subject_name }}</span>
                    <span class="hidden sm:inline">&bull;</span>
                    {{-- Status Fullscreen Badge --}}
                    <span class="hidden sm:inline-flex items-center gap-1 font-bold" :class="isFullscreen ? 'text-emerald-400' : 'text-amber-400'">
                        <span class="w-1.5 h-1.5 rounded-full" :class="isFullscreen ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400'"></span>
                        <span x-text="isFullscreen ? 'Layar Penuh Aktif' : 'Layar Penuh Mati'"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            {{-- Violation Strike Badge --}}
            <div class="px-2 py-1 rounded-xl text-[10px] font-extrabold flex items-center gap-1 border transition-all"
                 :class="violationCount > 0 ? 'bg-rose-500/20 text-rose-300 border-rose-400/40 animate-pulse' : 'bg-slate-800 text-slate-400 border-white/10'">
                <svg class="w-3 h-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Pelanggaran: <strong x-text="violationCount"></strong>/<span x-text="maxViolations"></span></span>
            </div>

            {{-- Timer --}}
            <div class="flex shrink-0 items-center gap-1 bg-amber-500/10 border border-amber-400/30 text-amber-300 px-2.5 py-1 rounded-xl">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-mono font-extrabold text-xs sm:text-sm" x-text="`${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`"></span>
            </div>
        </div>
    </div>

    <!-- ===== 3. FORM SOAL UJIAN ===== -->
    <form id="examForm" method="POST" action="{{ route('student.exams.submit', $exam->id) }}" class="space-y-6">
        @csrf

        @if($exam->questions->count() > 0)
            @foreach($exam->questions as $index => $q)
                @php $qType = $q->type ?? 'pg'; @endphp
                <div x-show="currentQuestion === {{ $index + 1 }}" class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-5">
                    
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100">
                        <span class="px-3 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold text-xs rounded-full">
                            Soal {{ $index + 1 }} / {{ $exam->questions->count() }}
                        </span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @if($qType === 'pg')
                                <span class="px-2 py-0.5 text-[10px] font-extrabold bg-blue-100 text-blue-800 rounded-md">Pilihan Ganda</span>
                            @elseif($qType === 'pg_kompleks')
                                <span class="px-2 py-0.5 text-[10px] font-extrabold bg-purple-100 text-purple-800 rounded-md">PG Kompleks</span>
                            @elseif($qType === 'benar_salah')
                                <span class="px-2 py-0.5 text-[10px] font-extrabold bg-emerald-100 text-emerald-800 rounded-md">Benar / Salah</span>
                            @elseif($qType === 'menjodohkan')
                                <span class="px-2 py-0.5 text-[10px] font-extrabold bg-amber-100 text-amber-800 rounded-md">Menjodohkan</span>
                            @elseif($qType === 'essay')
                                <span class="px-2 py-0.5 text-[10px] font-extrabold bg-rose-100 text-rose-800 rounded-md">Essay</span>
                            @endif
                            <span class="text-[10px] text-slate-400 font-semibold">Bobot: {{ $q->score_weight }} Poin</span>
                        </div>
                    </div>

                    <div class="text-slate-900 font-bold text-sm sm:text-base leading-relaxed select-none">
                        {!! nl2br(e($q->question_text)) !!}
                    </div>

                    @if($q->image_path)
                        <div class="pt-1">
                            <img src="{{ \Illuminate\Support\Str::startsWith($q->image_path, 'img/') ? asset($q->image_path) : asset('img/' . $q->image_path) }}" alt="Gambar Soal {{ $index + 1 }}" class="max-h-64 rounded-xl border border-slate-200 shadow-sm object-contain select-none pointer-events-none">
                        </div>
                    @endif

                    <!-- 1. TYPE PG (Pilihan Ganda biasa) -->
                    @if($qType === 'pg')
                        <div class="space-y-3 pt-2">
                            @foreach(['a' => $q->option_a, 'b' => $q->option_b, 'c' => $q->option_c, 'd' => $q->option_d, 'e' => $q->option_e] as $key => $val)
                                @if(!empty($val))
                                    <label class="flex items-start space-x-3 p-3.5 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/30 transition-all cursor-pointer group">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}" class="mt-0.5 w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                        <span class="font-extrabold text-xs uppercase text-indigo-600 shrink-0 w-5">{{ strtoupper($key) }}.</span>
                                        <span class="text-xs text-slate-700 font-medium leading-relaxed group-hover:text-slate-900">{{ $val }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    
                    <!-- 2. TYPE PG KOMPLEKS (Checkboxes) -->
                    @elseif($qType === 'pg_kompleks')
                        <div class="p-3 bg-purple-50 border border-purple-100 rounded-xl text-xs font-semibold text-purple-800 mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span><strong>Petunjuk:</strong> Anda dapat memilih lebih dari satu jawaban yang dianggap benar.</span>
                        </div>
                        <div class="space-y-3 pt-1">
                            @foreach(['a' => $q->option_a, 'b' => $q->option_b, 'c' => $q->option_c, 'd' => $q->option_d, 'e' => $q->option_e] as $key => $val)
                                @if(!empty($val))
                                    <label class="flex items-start space-x-3 p-3.5 rounded-xl border border-slate-200 hover:border-purple-400 hover:bg-purple-50/30 transition-all cursor-pointer group">
                                        <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $key }}" class="mt-0.5 w-4 h-4 text-purple-600 rounded-md focus:ring-purple-500">
                                        <span class="font-extrabold text-xs uppercase text-purple-600 shrink-0 w-5">{{ strtoupper($key) }}.</span>
                                        <span class="text-xs text-slate-700 font-medium leading-relaxed group-hover:text-slate-900">{{ $val }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>

                    <!-- 3. TYPE BENAR / SALAH -->
                    @elseif($qType === 'benar_salah')
                        @if(is_array($q->options_json) && count($q->options_json) > 0)
                            <div class="space-y-2 pt-2">
                                @foreach($q->options_json as $sIdx => $stmt)
                                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                                        <p class="font-semibold text-xs text-slate-800 leading-relaxed">{{ $sIdx + 1 }}. {{ $stmt }}</p>
                                        <div class="flex gap-3">
                                            <label class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-lg border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 cursor-pointer">
                                                <input type="radio" name="answers[{{ $q->id }}][{{ $sIdx }}]" value="benar" class="w-3.5 h-3.5 text-emerald-600 focus:ring-emerald-500">
                                                <span class="text-xs font-bold text-emerald-700">BENAR</span>
                                            </label>
                                            <label class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-lg border border-rose-300 bg-rose-50 hover:bg-rose-100 cursor-pointer">
                                                <input type="radio" name="answers[{{ $q->id }}][{{ $sIdx }}]" value="salah" class="w-3.5 h-3.5 text-rose-600 focus:ring-rose-500">
                                                <span class="text-xs font-bold text-rose-700">SALAH</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <label class="flex items-center justify-center p-4 rounded-xl border border-emerald-300 bg-emerald-50/50 hover:bg-emerald-100/50 cursor-pointer font-extrabold text-emerald-800 space-x-2">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="benar" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                    <span>BENAR</span>
                                </label>
                                <label class="flex items-center justify-center p-4 rounded-xl border border-rose-300 bg-rose-50/50 hover:bg-rose-100/50 cursor-pointer font-extrabold text-rose-800 space-x-2">
                                    <input type="radio" name="answers[{{ $q->id }}]" value="salah" class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                                    <span>SALAH</span>
                                </label>
                            </div>
                        @endif

                    <!-- 4. TYPE MENJODOHKAN -->
                    @elseif($qType === 'menjodohkan')
                        @php
                            $leftItems = $q->options_json['left'] ?? [];
                            $rightItems = $q->options_json['right'] ?? [];
                        @endphp
                        <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl text-xs font-semibold text-amber-900 mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span><strong>Petunjuk:</strong> Pasangkan setiap pernyataan di sebelah kiri dengan pilihan jawaban yang tepat pada dropdown di sebelah kanan.</span>
                        </div>
                        <div class="space-y-3 pt-2">
                            @foreach($leftItems as $lIdx => $premise)
                                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="font-bold text-xs text-slate-800 sm:w-1/2">
                                        <span class="text-amber-600 mr-1.5 font-black">{{ $lIdx + 1 }}.</span> {{ $premise }}
                                    </div>
                                    <div class="sm:w-1/2">
                                        <select name="answers[{{ $q->id }}][{{ $lIdx }}]" class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-white font-extrabold text-xs text-amber-900 focus:ring-2 focus:ring-amber-500">
                                            <option value="">-- Pilih Pasangan Jawaban --</option>
                                            @foreach($rightItems as $rVal)
                                                <option value="{{ $rVal }}">{{ $rVal }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    <!-- 5. TYPE ESSAY -->
                    @elseif($qType === 'essay')
                        <div class="p-3 bg-rose-50 border border-rose-100 rounded-xl text-xs font-semibold text-rose-900 mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span><strong>Petunjuk:</strong> Tuliskan jawaban uraian Anda secara lengkap pada kolom teks di bawah ini.</span>
                        </div>
                        <div class="pt-2">
                            <textarea name="answers[{{ $q->id }}]" rows="5" placeholder="Tuliskan jawaban essay Anda di sini..." class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 text-xs sm:text-sm font-medium leading-relaxed"></textarea>
                        </div>
                    @endif

                </div>
            @endforeach

            <!-- Question Navigation Pill Buttons -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-3">
                <p class="text-xs font-bold text-slate-600 uppercase tracking-wider">Navigasi Nomor Soal:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($exam->questions as $index => $q)
                        <button type="button" 
                                @click="currentQuestion = {{ $index + 1 }}" 
                                :class="currentQuestion === {{ $index + 1 }} ? 'bg-indigo-600 text-white font-black scale-105' : 'bg-slate-100 text-slate-700 font-bold hover:bg-slate-200'"
                                class="w-9 h-9 rounded-xl text-xs flex items-center justify-center transition-all border border-slate-200 cursor-pointer">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="space-y-3 pt-2">
                {{-- Baris 1: Navigasi Prev / Next --}}
                <div class="flex items-center justify-between gap-3">
                    <button type="button" 
                            x-show="currentQuestion > 1" 
                            @click="currentQuestion--" 
                            class="flex-1 px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition-all text-center">
                        &larr; Sebelumnya
                    </button>
                    <div x-show="currentQuestion <= 1" class="flex-1"></div>

                    <button type="button" 
                            x-show="currentQuestion < totalQuestions" 
                            @click="currentQuestion++" 
                            class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition-all text-center">
                        Berikutnya &rarr;
                    </button>
                    <div x-show="currentQuestion >= totalQuestions" class="flex-1"></div>
                </div>

                {{-- Baris 2: Tombol Submit --}}
                <button type="button" 
                        @click="showSubmitModal = true"
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Kumpulkan Jawaban Ujian</span>
                </button>
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-2xl border border-slate-200">
                <p class="text-xs text-slate-500 font-medium">Soal belum diunggah untuk ujian ini.</p>
            </div>
        @endif
    </form>

    <!-- ===== 4. MODAL PERINGATAN KECURANGAN (TAB SWITCH / APP SWITCH) ===== -->
    <div x-show="showViolationWarning" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4"
         style="display: none;">
        <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-rose-500 p-6 text-center space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-rose-100 border border-rose-300 text-rose-600 flex items-center justify-center animate-bounce">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>

            <div>
                <span class="px-3 py-1 bg-rose-100 text-rose-800 text-[10px] font-black uppercase tracking-wider rounded-full border border-rose-200">
                    Peringatan Kecurangan Terdeteksi!
                </span>
                <h3 class="text-base font-black text-slate-900 mt-2">Anda Meninggalkan Halaman Ujian!</h3>
                <p class="text-xs text-rose-600 font-semibold mt-1" x-text="lastViolationReason"></p>
            </div>

            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 text-xs space-y-1.5 text-slate-700">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-500">Pelanggaran Ke:</span>
                    <span class="font-black text-rose-600 font-mono text-sm"><span x-text="violationCount"></span> / <span x-text="maxViolations"></span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-500">Sisa Kesempatan:</span>
                    <span class="font-extrabold text-slate-900"><span x-text="Math.max(0, maxViolations - violationCount)"></span> Kali Lagi</span>
                </div>
                <p class="text-[11px] text-rose-700 pt-2 border-t border-rose-200 font-medium flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Jika Anda melanggar <span x-text="maxViolations"></span> kali, ujian akan <strong>otomatis dikumpulkan paksa</strong> dengan jawaban terakhir!</span>
                </p>
            </div>

            <button type="button" 
                    @click="showViolationWarning = false; requestFullscreenMode()"
                    class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-rose-200 transition-all cursor-pointer">
                Saya Mengerti & Kembali ke Ujian
            </button>
        </div>
    </div>

    <!-- ===== 5. MODAL PERINGATAN KELUAR LAYAR PENUH (FULLSCREEN LOST) ===== -->
    <div x-show="showFullscreenWarning && !showViolationWarning" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4"
         style="display: none;">
        <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden border border-amber-300 p-6 text-center space-y-4">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </div>

            <div>
                <h3 class="text-base font-black text-slate-900">Mode Layar Penuh Terputus!</h3>
                <p class="text-xs text-slate-500 mt-1">Ujian CBT wajib dikerjakan dalam mode layar penuh (Fullscreen).</p>
            </div>

            <button type="button" 
                    @click="requestFullscreenMode()"
                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-200 transition-all cursor-pointer">
                Aktifkan Kembali Layar Penuh
            </button>
        </div>
    </div>

    <!-- ===== 6. MODAL KONFIRMASI KUMPULKAN JAWABAN ===== -->
    <div
        x-show="showSubmitModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showSubmitModal = false"></div>

        {{-- Modal Card --}}
        <div
            x-show="showSubmitModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden z-10"
        >
            {{-- Header stripe --}}
            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 pt-6 pb-10 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-white font-extrabold text-lg leading-tight">Kumpulkan Jawaban?</h2>
                <p class="text-emerald-100 text-xs mt-1">Pastikan semua soal sudah dijawab</p>
            </div>

            {{-- Body --}}
            <div class="-mt-5 px-6 pb-6">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-5 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 font-semibold">Mata Pelajaran</span>
                        <span class="text-xs font-extrabold text-slate-800">{{ $exam->subject_name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 font-semibold">Judul Ujian</span>
                        <span class="text-xs font-extrabold text-slate-800 text-right max-w-[60%] leading-snug">{{ $exam->title }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-500 font-semibold">Jumlah Soal</span>
                        <span class="text-xs font-extrabold text-slate-800">{{ $exam->questions->count() }} Soal</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 pt-2 mt-2">
                        <span class="text-xs text-slate-500 font-semibold">Sisa Waktu</span>
                        <span class="text-xs font-black text-amber-600 font-mono" x-text="`${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`"></span>
                    </div>
                </div>

                <p class="text-xs text-slate-500 text-center mb-5 leading-relaxed">
                    Setelah dikumpulkan, jawaban <strong class="text-slate-700">tidak dapat diubah</strong>. Apakah Anda yakin?
                </p>

                <div class="flex gap-3">
                    <button type="button"
                            @click="showSubmitModal = false"
                            class="flex-1 py-3 rounded-2xl border-2 border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="button"
                            @click="submitExam('Diserahkan oleh siswa')"
                            class="flex-1 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Ya, Kumpulkan!
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
