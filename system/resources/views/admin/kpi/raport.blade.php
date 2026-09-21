@extends('layouts.admin')

@section('title', 'Lembar Rapor Kinerja Pegawai (KPI) - ' . $user->name)

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Top Action & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.kpi.index', ['year' => $year, 'month' => $month]) }}" 
               class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Lembar Rapor Kinerja Guru &amp; Pegawai
                </h1>
                <p class="text-xs text-slate-500">
                    Audit trail indikator penilaian, radar kompetensi, dan capaian kinerja terpadu
                </p>
            </div>
        </div>

        <!-- Print Action -->
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.kpi.raport.print', ['userId' => $user->id, 'year' => $year, 'month' => $month]) }}" 
               target="_blank" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Lembar Rapor Resmi (A4)</span>
            </a>
        </div>
    </div>

    <!-- Teacher Identity & Overall KPI Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Teacher Profile & Predicate Card -->
        <div class="lg:col-span-1 bg-white dark:bg-[#1C2434] p-6 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-4 mb-5">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-black text-2xl flex items-center justify-center border border-indigo-200/60 dark:border-indigo-800/60 shrink-0 overflow-hidden shadow-inner">
                        @if(!empty($user->avatar_url))
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="overflow-hidden">
                        <h2 class="text-base font-extrabold text-slate-900 dark:text-white truncate">{{ $user->name }}</h2>
                        <p class="text-xs text-slate-500 font-semibold">{{ $user->jabatan ?? ($user->roles->first()->name ?? 'Tenaga Pendidik') }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">NIP: {{ $user->nip ?? '-' }}</p>
                    </div>
                </div>

                <!-- Final Score Big Display -->
                <div class="p-4 rounded-xl bg-gradient-to-br from-indigo-900 to-slate-900 text-white shadow-md text-center">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-indigo-300">Skor Akhir Kinerja</span>
                    <div class="text-4xl font-black text-amber-300 my-1">
                        {{ $kpi['final_score'] }} <span class="text-sm font-normal text-slate-300">/ 100</span>
                    </div>
                    <div class="mt-2">
                        @php
                            $badgeStyle = match($kpi['predicate']) {
                                'A' => 'bg-emerald-500 text-white',
                                'B' => 'bg-indigo-500 text-white',
                                'C' => 'bg-amber-500 text-slate-900',
                                'D' => 'bg-rose-500 text-white',
                                default => 'bg-slate-500 text-white'
                            };
                        @endphp
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $badgeStyle }}">
                            Predikat {{ $kpi['predicate'] }} ({{ $kpi['predicate_label'] }})
                        </span>
                    </div>
                </div>

                <!-- Attendance & Gate info -->
                <div class="mt-4 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/70 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Tingkat Kehadiran:</span>
                        <span class="font-bold text-slate-800 dark:text-white">{{ $kpi['attendance_percentage'] }}%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Gate Threshold (Min {{ $settings['gate_threshold'] }}%):</span>
                        @if($kpi['gate_passed'])
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">LOLOS GATE</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">DI BAWAH GATE</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Hari Kerja Efektif:</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $kpi['effective_workdays'] }} hari ({{ $kpi['excused_days'] }} izin resmi)</span>
                    </div>
                </div>
            </div>

            <!-- Period badge -->
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-[#2E3A47] text-center text-xs text-slate-400">
                Periode Evaluasi: <strong class="text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</strong>
            </div>
        </div>

        <!-- Right: 5 Pillars Radar Chart & Summary -->
        <div class="lg:col-span-2 bg-white dark:bg-[#1C2434] p-6 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-sm uppercase tracking-wider text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    Radar Perbandingan 5 Pilar Kompetensi
                </h3>
                <span class="text-xs text-slate-400">Skala 0 - 100 Poin</span>
            </div>

            <div class="h-64 sm:h-72 w-full flex items-center justify-center">
                <canvas id="kpiRadarChart"></canvas>
            </div>

            <div class="grid grid-cols-5 gap-2 text-center text-xs pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="p-1 rounded bg-slate-50 dark:bg-slate-800">
                    <span class="text-[10px] text-slate-400 block truncate">1. Disiplin</span>
                    <strong class="text-indigo-600 dark:text-indigo-400">{{ $kpi['score_comp_1'] }}</strong>
                </div>
                <div class="p-1 rounded bg-slate-50 dark:bg-slate-800">
                    <span class="text-[10px] text-slate-400 block truncate">2. Pedagogik</span>
                    <strong class="text-emerald-600 dark:text-emerald-400">{{ $kpi['score_comp_2'] }}</strong>
                </div>
                <div class="p-1 rounded bg-slate-50 dark:bg-slate-800">
                    <span class="text-[10px] text-slate-400 block truncate">3. Profesional</span>
                    <strong class="text-amber-600 dark:text-amber-400">{{ $kpi['score_comp_3'] }}</strong>
                </div>
                <div class="p-1 rounded bg-slate-50 dark:bg-slate-800">
                    <span class="text-[10px] text-slate-400 block truncate">4. Tarbiyah</span>
                    <strong class="text-sky-600 dark:text-sky-400">{{ $kpi['score_comp_4'] }}</strong>
                </div>
                <div class="p-1 rounded bg-slate-50 dark:bg-slate-800">
                    <span class="text-[10px] text-slate-400 block truncate">5. Sosial</span>
                    <strong class="text-purple-600 dark:text-purple-400">{{ $kpi['score_comp_5'] }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Qualitative Feedback & Supervisor Notes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Apresiasi -->
        <div class="p-5 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/70 dark:border-emerald-800/40">
            <div class="flex items-center space-x-2 text-emerald-800 dark:text-emerald-300 font-extrabold text-xs uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Apresiasi &amp; Keunggulan Kinerja</span>
            </div>
            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                {{ $kpi['feedback_appreciation'] }}
            </p>
        </div>

        <!-- Rekomendasi -->
        <div class="p-5 rounded-2xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-800/40">
            <div class="flex items-center space-x-2 text-amber-800 dark:text-amber-300 font-extrabold text-xs uppercase tracking-wider mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Rekomendasi Perbaikan &amp; Pembinaan</span>
            </div>
            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                {{ $kpi['feedback_improvement'] }}
            </p>
        </div>
    </div>

    <!-- Audit Trail Breakdown Table (All 5 Pillars & 23 Indicators) -->
    <div class="bg-white dark:bg-[#1C2434] rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-[#2E3A47] flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-sm uppercase tracking-wider text-slate-800 dark:text-white">
                    Audit Trail Rincian Penilaian per Indikator
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Transparansi rumus perhitungan, sumber data, dan kontribusi nilai</p>
            </div>
            <span class="text-xs font-bold text-slate-500">23 Indikator Terpadu</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-[#2E3A47] text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3 px-4 w-16 text-center">Kode</th>
                        <th class="py-3 px-4">Nama Indikator Penilaian</th>
                        <th class="py-3 px-3 text-center">Tipe / Sumber</th>
                        <th class="py-3 px-4">Audit Bukti Data Riil</th>
                        <th class="py-3 px-3 text-center w-20">Nilai (0-100)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#2E3A47]">
                    @foreach($definitions as $pillarKey => $pillar)
                        <!-- Pillar Header Row -->
                        <tr class="bg-indigo-50/40 dark:bg-indigo-950/20 font-black text-indigo-900 dark:text-indigo-200">
                            <td colspan="4" class="py-2.5 px-4 uppercase text-[11px] tracking-wider">
                                {{ strtoupper($pillar['name']) }} (Bobot: {{ $kpi['weights'][$pillarKey] }}%)
                            </td>
                            <td class="py-2.5 px-3 text-center font-black text-indigo-600 dark:text-indigo-400 text-sm">
                                {{ $kpi['score_' . $pillarKey] }}
                            </td>
                        </tr>

                        <!-- Indicators for this pillar -->
                        @foreach($pillar['indicators'] as $indCode => $indDef)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                            <td class="py-3 px-4 text-center font-bold text-slate-400">{{ $indCode }}</td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-slate-800 dark:text-white">{{ $indDef['name'] }}</p>
                                <p class="text-[11px] text-slate-400">{{ $indDef['desc'] }}</p>
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($indDef['type'] === 'auto')
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300 uppercase">
                                        Log Sistem
                                    </span>
                                @elseif($indDef['type'] === 'hybrid')
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 uppercase">
                                        Survei/Log
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 uppercase">
                                        Supervisi KS
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                {{ $kpi['items'][$indCode]['source_detail'] ?? ($kpi['items'][$indCode]['notes'] ?? 'Terverifikasi sesuai standar') }}
                            </td>
                            <td class="py-3 px-3 text-center font-black text-slate-800 dark:text-white">
                                {{ $kpi['items'][$indCode]['score'] ?? 0 }}
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('kpiRadarChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: [
                '1. Disiplin ({{ $kpi['score_comp_1'] }})',
                '2. Pedagogik ({{ $kpi['score_comp_2'] }})',
                '3. Profesional ({{ $kpi['score_comp_3'] }})',
                '4. Tarbiyah ({{ $kpi['score_comp_4'] }})',
                '5. Sosial ({{ $kpi['score_comp_5'] }})'
            ],
            datasets: [{
                label: 'Capaian Pegawai',
                data: [
                    {{ $kpi['score_comp_1'] }},
                    {{ $kpi['score_comp_2'] }},
                    {{ $kpi['score_comp_3'] }},
                    {{ $kpi['score_comp_4'] }},
                    {{ $kpi['score_comp_5'] }}
                ],
                fill: true,
                backgroundColor: 'rgba(99, 102, 241, 0.25)',
                borderColor: '#4f46e5',
                pointBackgroundColor: '#4f46e5',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#4f46e5',
                borderWidth: 2
            },
            {
                label: 'Target Minimal (80)',
                data: [80, 80, 80, 80, 80],
                fill: false,
                borderColor: 'rgba(156, 163, 175, 0.5)',
                borderDash: [5, 5],
                pointRadius: 0,
                borderWidth: 1.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: 'rgba(156, 163, 175, 0.2)' },
                    grid: { color: 'rgba(156, 163, 175, 0.2)' },
                    pointLabels: {
                        font: { size: 11, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                        color: '#64748b'
                    },
                    suggestedMin: 50,
                    suggestedMax: 100,
                    ticks: { stepSize: 10, backdropColor: 'transparent' }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 11, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
