@if(isset($userKpi) && $userKpi)
    @php
        $badgeClass = match($userKpi['predicate'] ?? 'C') {
            'A' => 'bg-emerald-500 text-white',
            'B' => 'bg-indigo-600 text-white',
            'C' => 'bg-amber-500 text-slate-900',
            'D' => 'bg-rose-500 text-white',
            default => 'bg-slate-500 text-white',
        };
    @endphp

    <div class="rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-xs p-5 sm:p-6 space-y-5 transition-all">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center space-x-3.5">
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900/50 shrink-0 font-bold">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20V10"></path>
                        <path d="M18 20V4"></path>
                        <path d="M6 20v-4"></path>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
                            Score Card Penilaian Kinerja Pegawai (KPI)
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $badgeClass }}">
                            Predikat {{ $userKpi['predicate'] }} ({{ $userKpi['predicate_label'] ?? 'Cukup' }})
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                        Evaluasi Standar Rapor Terpadu Periode {{ \Carbon\Carbon::create(null, $userKpi['month'])->translatedFormat('F') }} {{ $userKpi['year'] }}
                    </p>
                </div>
            </div>

            <!-- Gate & Action Button -->
            <div class="flex items-center space-x-2.5 shrink-0">
                <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-extrabold {{ $userKpi['gate_passed'] ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800' }}">
                    <span>{{ $userKpi['gate_passed'] ? '🛡️ Gate Presensi: Lolos' : '⚠️ Gate Presensi: Dibawah 85%' }}</span>
                </span>
                <a href="{{ route('admin.kpi.raport', auth()->id()) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center space-x-1.5">
                    <span>Lihat Rapor Lengkap</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Main Body: Score Highlight & 5 Pillars -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            
            <!-- Left Highlight: Overall Final Score -->
            <div class="lg:col-span-4 p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-indigo-950 text-white shadow-xs relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
                <span class="text-[11px] uppercase tracking-wider font-extrabold text-indigo-300 block">Skor Akhir Kinerja Anda</span>
                <div class="flex items-baseline space-x-2 mt-1">
                    <span class="text-4xl sm:text-5xl font-black text-amber-300">{{ $userKpi['final_score'] }}</span>
                    <span class="text-sm font-bold text-slate-400">/ 100</span>
                </div>
                <div class="mt-3 pt-3 border-t border-white/10 flex items-center justify-between text-xs">
                    <span class="text-slate-300 font-medium">Tingkat Presensi:</span>
                    <span class="font-extrabold text-emerald-400">{{ $userKpi['attendance_percentage'] }}%</span>
                </div>
                <div class="mt-1 flex items-center justify-between text-xs">
                    <span class="text-slate-300 font-medium">Hari Kerja Efektif:</span>
                    <span class="font-bold text-slate-200">{{ $userKpi['effective_workdays'] }} Hari</span>
                </div>
            </div>

            <!-- Right: 5 Pillars Progress Bars -->
            <div class="lg:col-span-8 space-y-3">
                <p class="text-xs font-black uppercase tracking-wider text-slate-400 mb-1">Pencapaian Per 5 Pilar Kompetensi</p>
                
                @php
                    $pillars = [
                        ['label' => '1. Disiplin & Presensi', 'score' => $userKpi['score_comp_1'], 'weight' => $userKpi['weights']['comp_1'], 'color' => 'bg-indigo-500'],
                        ['label' => '2. Pedagogik / KBM', 'score' => $userKpi['score_comp_2'], 'weight' => $userKpi['weights']['comp_2'], 'color' => 'bg-emerald-500'],
                        ['label' => '3. Profesional & LMS', 'score' => $userKpi['score_comp_3'], 'weight' => $userKpi['weights']['comp_3'], 'color' => 'bg-amber-500'],
                        ['label' => '4. Tarbiyah & Adab', 'score' => $userKpi['score_comp_4'], 'weight' => $userKpi['weights']['comp_4'], 'color' => 'bg-sky-500'],
                        ['label' => '5. Sosial & Tim', 'score' => $userKpi['score_comp_5'], 'weight' => $userKpi['weights']['comp_5'], 'color' => 'bg-purple-500'],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($pillars as $p)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800/80">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $p['label'] }}</span>
                                <span class="font-black text-slate-900 dark:text-white">{{ $p['score'] }} <span class="text-[10px] text-slate-400 font-normal">pts</span></span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                <div class="{{ $p['color'] }} h-full rounded-full transition-all duration-500" style="width: {{ min(100, $p['score']) }}%"></div>
                            </div>
                            <div class="text-[10px] text-slate-400 mt-1 flex justify-between">
                                <span>Bobot: {{ $p['weight'] }}%</span>
                                <span>{{ $p['score'] >= 91 ? 'Mumtaz' : ($p['score'] >= 76 ? 'Baik' : 'Cukup') }}</span>
                            </div>
                        </div>
                    @endforeach

                    <!-- Quick Links Box -->
                    <div class="p-3 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/40 flex flex-col justify-between">
                        <span class="text-[11px] font-extrabold text-indigo-700 dark:text-indigo-300">Tingkatkan Skor KPI Anda:</span>
                        <div class="flex items-center gap-2 mt-2">
                            <a href="{{ route('admin.employee-mutabaah.index') }}" class="flex-1 text-center px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-lg transition shadow-2xs">
                                📖 Mutabaah
                            </a>
                            <a href="{{ route('admin.kajian-pekanan.index') }}" class="flex-1 text-center px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-[11px] rounded-lg transition shadow-2xs">
                                🕌 Lapor Kajian
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
