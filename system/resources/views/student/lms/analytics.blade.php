@extends('layouts.student-mobile')

@section('title', 'Analisis Diagnostik Nilai')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Back Button Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.lms.index') }}" class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center space-x-1 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
            <svg class="w-3.5 h-3.5 inline mr-1 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Ruang Belajar</span>
        </a>
    </div>

    <!-- Header Banner -->
    <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-indigo-600 to-purple-800 text-white shadow-xl shadow-indigo-500/20">
        <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] sm:text-xs font-bold text-indigo-100 flex items-center space-x-1.5 w-max">
            <svg class="w-3.5 h-3.5 inline text-indigo-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>DIAGNOSTIC RADAR ANALYTICS</span>
        </span>
        <h1 class="text-xl sm:text-2xl font-black mt-2 tracking-tight">Analisis Diagnostik Penguasaan Materi</h1>
        <p class="text-[11px] sm:text-xs text-indigo-100 mt-1 leading-relaxed">Grafik radar mengukur tingkat penguasaan kamu di tiap topik mata pelajaran untuk evaluasi belajar mandiri.</p>
    </div>

    <!-- Radar Chart Box (Ultra Modern Glass Card) -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 sm:p-6 shadow-xl shadow-indigo-500/5 space-y-4">
        <!-- Background Glow Orbs -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-indigo-500/10 dark:bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-purple-500/10 dark:bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Card Header -->
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-indigo-700 text-white flex items-center justify-center shadow-md shadow-indigo-500/20 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                </div>
                <div>
                    <h3 class="font-black text-[#1C2434] dark:text-white text-sm sm:text-base tracking-tight">Grafik Radar Penguasaan Materi</h3>
                    <p class="text-[11px] text-slate-500">Visualisasi 360° performa relatif antar mata pelajaran</p>
                </div>
            </div>
            @php $avgScore = count($radarData) > 0 ? round(array_sum($radarData)/count($radarData), 1) : 0; @endphp
            <div class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/60 flex items-center justify-between sm:justify-start gap-2 shrink-0">
                <span class="text-[10px] uppercase font-extrabold text-slate-500 dark:text-slate-400">Rata-Rata:</span>
                <span class="text-sm font-black font-mono text-indigo-600 dark:text-indigo-400">{{ $avgScore }}%</span>
            </div>
        </div>

        <!-- Chart Canvas Frame -->
        <div class="relative z-10 max-w-xs sm:max-w-md mx-auto aspect-square w-full py-2">
            <canvas id="radarChart"></canvas>
        </div>

        <!-- Quick Summary Indicators -->
        <div class="relative z-10 grid grid-cols-3 gap-2 pt-3 border-t border-slate-100 dark:border-slate-800 text-center">
            @php
                $maxVal = count($radarData) > 0 ? max($radarData) : 0;
                $minVal = count($radarData) > 0 ? min($radarData) : 0;
                $maxIdx = array_search($maxVal, $radarData);
                $minIdx = array_search($minVal, $radarData);
                $topSubject = $maxIdx !== false ? ($radarLabels[$maxIdx] ?? '-') : '-';
                $lowSubject = $minIdx !== false ? ($radarLabels[$minIdx] ?? '-') : '-';
            @endphp
            <div class="p-2 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200/50 dark:border-emerald-900/30">
                <span class="block text-[9px] font-extrabold uppercase text-emerald-600 dark:text-emerald-400">Topik Tertinggi</span>
                <span class="text-xs font-black text-slate-800 dark:text-white block truncate mt-0.5">{{ $topSubject }}</span>
                <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold block">{{ $maxVal }}%</span>
            </div>
            <div class="p-2 rounded-xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/50 dark:border-amber-900/30">
                <span class="block text-[9px] font-extrabold uppercase text-amber-600 dark:text-amber-400">Fokus Utama</span>
                <span class="text-xs font-black text-slate-800 dark:text-white block truncate mt-0.5">{{ $lowSubject }}</span>
                <span class="text-[10px] font-mono text-amber-600 dark:text-amber-400 font-bold block">{{ $minVal }}%</span>
            </div>
            <div class="p-2 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/20 border border-indigo-200/50 dark:border-indigo-900/30">
                <span class="block text-[9px] font-extrabold uppercase text-indigo-600 dark:text-indigo-400">Status Belajar</span>
                <span class="text-xs font-black text-slate-800 dark:text-white block truncate mt-0.5">{{ $avgScore >= 75 ? 'Optimal' : ($avgScore >= 60 ? 'Cukup' : 'Perlu Didorong') }}</span>
                <span class="text-[10px] font-mono text-indigo-600 dark:text-indigo-400 font-bold block">{{ count($radarLabels) }} Mapel</span>
            </div>
        </div>
    </div>

    <!-- Recommendations Breakdown -->
    <div class="tailadmin-card p-4 sm:p-6 border border-slate-200 dark:border-slate-800 space-y-3 sm:space-y-4">
        <h3 class="font-extrabold text-[#1C2434] dark:text-white text-sm sm:text-base flex items-center space-x-2">
            <svg class="w-4 h-4 text-amber-500 inline mr-1 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 01-2 2h-0a2 2 0 01-2-2v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            <span>Rekomendasi Fokus Belajar</span>
        </h3>
        <div class="space-y-2.5 sm:space-y-3">
            @foreach($radarLabels as $idx => $label)
            @php $val = $radarData[$idx] ?? 0; @endphp
            <div class="p-3 sm:p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 flex items-start sm:items-center justify-between gap-3">
                <div class="min-w-0">
                    <h4 class="font-extrabold text-xs sm:text-sm text-[#1C2434] dark:text-white truncate">{{ $label }}</h4>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 mt-0.5 flex items-start sm:items-center gap-1 leading-relaxed">
                        @if($val >= 80)
                        <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span><strong>Sangat Menguasai!</strong> Pertahankan performa kamu di topik ini.</span>
                        @elseif($val >= 50)
                        <svg class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span><strong>Cukup Menguasai.</strong> Tonton ulang video untuk memperdalam konsep.</span>
                        @else
                        <svg class="w-3.5 h-3.5 text-rose-500 shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span><strong>Perlu Ditingkatkan!</strong> Kerjakan kuis & tonton modul video lebih lanjut.</span>
                        @endif
                    </p>
                </div>
                <span class="text-xs sm:text-sm font-black font-mono px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full shrink-0 {{ $val >= 80 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($val >= 50 ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300') }}">
                    {{ $val }}%
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('radarChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const isDark = document.documentElement.classList.contains('dark');

        const chartWidth = canvas.offsetWidth || 300;
        const gradient = ctx.createRadialGradient(
            chartWidth / 2, chartWidth / 2, 5,
            chartWidth / 2, chartWidth / 2, chartWidth / 2
        );
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.45)');
        gradient.addColorStop(0.6, 'rgba(168, 85, 247, 0.25)');
        gradient.addColorStop(1, 'rgba(236, 72, 153, 0.05)');

        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: {!! json_encode($radarLabels) !!},
                datasets: [{
                    label: 'Penguasaan Materi (%)',
                    data: {!! json_encode($radarData) !!},
                    backgroundColor: gradient,
                    borderColor: '#6366F1',
                    borderWidth: 3,
                    pointBackgroundColor: '#818CF8',
                    pointBorderColor: isDark ? '#0F172A' : '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    r: {
                        angleLines: {
                            color: isDark ? 'rgba(255, 255, 255, 0.18)' : 'rgba(0, 0, 0, 0.12)',
                            lineWidth: 1.5,
                            z: 0
                        },
                        grid: {
                            color: isDark ? 'rgba(255, 255, 255, 0.15)' : 'rgba(0, 0, 0, 0.1)',
                            lineWidth: 1,
                            z: 0
                        },
                        pointLabels: {
                            color: isDark ? '#F8FAFC' : '#0F172A',
                            font: {
                                size: 11,
                                weight: '800'
                            },
                            padding: 10,
                            backdropColor: isDark ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                            backdropPadding: { x: 8, y: 4 },
                            borderRadius: 8,
                            showLabelBackdrop: true,
                            z: 10
                        },
                        ticks: {
                            color: isDark ? '#818CF8' : '#4F46E5',
                            backdropColor: isDark ? '#0F172A' : '#FFFFFF',
                            backdropPadding: { x: 6, y: 3 },
                            borderRadius: 6,
                            showLabelBackdrop: true,
                            stepSize: 20,
                            font: { size: 10, weight: '900', family: 'monospace' },
                            z: 10,
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                }
            }
        });
    });
</script>
@endsection
