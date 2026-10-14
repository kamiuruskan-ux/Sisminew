@extends('layouts.admin')

@section('title', 'Kenaikan & Kelulusan Kelas')

@section('content')
<div class="space-y-6" x-data="{
    bulkAction: 'promote',
    defaultTargetClass: '{{ $defaultTargetClass?->id ?? '' }}',
    setAllDecisions(action) {
        document.querySelectorAll('.student-action-select').forEach(el => {
            el.value = action;
            el.dispatchEvent(new Event('change'));
        });
    },
    setAllTargetClasses(classId) {
        document.querySelectorAll('.student-target-class-select').forEach(el => {
            el.value = classId;
        });
    }
}">

    <!-- Page Header & Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-700 p-6 rounded-2xl text-white shadow-lg">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">
                    Tahun Akademik: {{ $sourceYear?->name ?? '2024/2025' }} &rarr; {{ $targetYear?->name ?? '2025/2026' }}
                </span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-400/30 text-emerald-100">
                    Siklus Akhir Tahun
                </span>
            </div>
            <h1 class="text-2xl font-black tracking-tight">Kenaikan & Kelulusan Kelas Santri</h1>
            <p class="text-sm text-emerald-100 max-w-2xl">
                Atur kenaikan tingkat kelas, penempatan rombel baru, kelulusan santri akhir, dan mutasi. Seluruh arsip nilai, presensi, dan riwayat kelas tahun sebelumnya tersimpan secara permanen.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.class-promotions.history') }}" 
               class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 backdrop-blur-sm border border-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Arsip Riwayat Kenaikan
            </a>
            <a href="{{ route('admin.classes.index') }}" 
               class="px-4 py-2.5 bg-white text-slate-800 hover:bg-slate-100 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Master Data Kelas
            </a>
        </div>
    </div>

    <!-- Filter & Konfigurasi Kelas Asal & Tujuan -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-5">
        <form method="GET" action="{{ route('admin.class-promotions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Tahun Ajaran Asal -->
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    1. Tahun Ajaran Asal
                </label>
                <select name="source_year_id" onchange="this.form.submit()" 
                        class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $sourceYearId == $ay->id ? 'selected' : '' }}>
                            {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">T.A saat santri menempuh kelas asal</p>
            </div>

            <!-- Kelas Asal -->
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    2. Kelas Asal (Rombel)
                </label>
                <select name="source_class_id" onchange="this.form.submit()" 
                        class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $sourceClassId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} (Tingkat {{ $c->grade ?? $c->level }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Pilih kelas yang akan dinaikkan</p>
            </div>

            <!-- Tahun Ajaran Baru (Tujuan) -->
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    3. Tahun Ajaran Tujuan
                </label>
                <select name="target_year_id" onchange="this.form.submit()" 
                        class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $targetYearId == $ay->id ? 'selected' : '' }}>
                            {{ $ay->name }} {{ $ay->is_active ? '(Aktif Saat Ini)' : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">T.A baru tempat santri naik kelas</p>
            </div>

            <!-- Rekomendasi Jenjang -->
            <div class="flex flex-col justify-center bg-slate-50 dark:bg-slate-700/50 p-3 rounded-xl border border-slate-200 dark:border-slate-700">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase">Target Rekomendasi</span>
                @if($isFinalGrade)
                    <div class="flex items-center gap-1.5 text-purple-600 dark:text-purple-400 font-bold text-sm mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        <span>Kelulusan / Alumni SDIT (Tingkat 6)</span>
                    </div>
                @else
                    <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold text-sm mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        <span>Naik ke Tingkat {{ $targetSuggestedGrade }}</span>
                    </div>
                @endif
                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Opsi rombel dapat dipilih per santri</span>
            </div>
        </form>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Santri di Rombel Asal</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $stats['total_students'] }} <span class="text-xs font-normal text-slate-400">santri</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Memenuhi Syarat Standar (KKM & Kehadiran)</p>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['eligible_count'] }} <span class="text-xs font-normal text-slate-400">santri ({{ $stats['total_students'] > 0 ? round(($stats['eligible_count'] / $stats['total_students']) * 100) : 0 }}%)</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status Tersimpan di Database</p>
                <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $stats['promoted_count'] }} <span class="text-xs font-normal text-slate-400">telah diproses</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
    </div>

    <!-- Formulir Kenaikan Kelas Masal -->
    <form action="{{ route('admin.class-promotions.process') }}" method="POST">
        @csrf
        <input type="hidden" name="source_year_id" value="{{ $sourceYearId }}">
        <input type="hidden" name="source_class_id" value="{{ $sourceClassId }}">
        <input type="hidden" name="target_year_id" value="{{ $targetYearId }}">

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <!-- Toolbar Cepat & Bulk Action -->
            <div class="p-4 bg-slate-50/80 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Setel Cepat Semua:</span>
                    @if($isFinalGrade)
                        <button type="button" @click="setAllDecisions('graduate')" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-bold transition shadow-xs flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                            Tandai Semua LULUS
                        </button>
                    @else
                        <button type="button" @click="setAllDecisions('promote')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-xs flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Tandai Semua NAIK KELAS
                        </button>
                    @endif
                    <button type="button" @click="setAllDecisions('retain')" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition shadow-xs">
                        Tinggal Kelas
                    </button>
                </div>

                @if(!$isFinalGrade && $targetClasses->isNotEmpty())
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Rombel Tujuan Default:</span>
                    <select class="text-xs rounded-lg border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1 px-2.5 font-semibold"
                            @change="setAllTargetClasses($event.target.value)">
                        <option value="">-- Pilih Rombel --</option>
                        @foreach($targetClasses as $tc)
                            <option value="{{ $tc->id }}" {{ $defaultTargetClass?->id == $tc->id ? 'selected' : '' }}>
                                {{ $tc->name }} (Tingkat {{ $tc->grade ?? $tc->level }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <!-- Tabel Daftar Siswa -->
            @if($students->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">Tidak Ada Santri Ditemukan</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                        Belum ada santri yang terdaftar pada kelas {{ $sourceClass?->name ?? '-' }} di Tahun Akademik {{ $sourceYear?->name ?? '-' }}.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4 min-w-[220px]">Santri</th>
                                <th class="py-3 px-3 text-center">Nilai Rata-rata</th>
                                <th class="py-3 px-3 text-center">Presensi</th>
                                <th class="py-3 px-4 min-w-[170px]">Keputusan</th>
                                <th class="py-3 px-4 min-w-[200px]">Rombel Tujuan (T.A {{ $targetYear?->name ?? 'Baru' }})</th>
                                <th class="py-3 px-4 min-w-[180px]">Catatan / Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                            @foreach($students as $idx => $st)
                                @php
                                    $perf = $studentPerformance[$st->id] ?? [
                                        'avg_score' => 0,
                                        'att_percentage' => 100,
                                        'current_status' => 'active',
                                        'target_class_id' => $defaultTargetClass?->id,
                                        'is_promoted' => false,
                                    ];
                                    $defaultAction = $isFinalGrade ? 'graduate' : 'promote';
                                    if ($perf['current_status'] === 'retained') $defaultAction = 'retain';
                                    elseif ($perf['current_status'] === 'graduated') $defaultAction = 'graduate';
                                    elseif ($perf['current_status'] === 'transferred') $defaultAction = 'transfer';
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition">
                                    <td class="py-3 px-4 text-center font-bold text-slate-400">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white font-bold flex items-center justify-center text-xs shadow-xs shrink-0">
                                                {{ strtoupper(substr($st->user?->name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800 dark:text-white">{{ $st->user?->name ?? 'Santri' }}</p>
                                                <p class="text-[11px] text-slate-400 font-mono">NISN: {{ $st->nisn ?? $st->nis ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-block px-2.5 py-1 rounded-lg font-bold text-xs {{ $perf['avg_score'] >= 75 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($perf['avg_score'] >= 65 ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300') }}">
                                            {{ $perf['avg_score'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-block px-2.5 py-1 rounded-lg font-bold text-xs {{ $perf['att_percentage'] >= 85 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' }}">
                                            {{ $perf['att_percentage'] }}%
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <select name="decisions[{{ $st->id }}][action]" 
                                                class="student-action-select w-full text-xs font-bold rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1.5 focus:ring-emerald-500">
                                            @if($isFinalGrade)
                                                <option value="graduate" {{ $defaultAction === 'graduate' ? 'selected' : '' }}>🎓 LULUS / Alumni</option>
                                                <option value="retain" {{ $defaultAction === 'retain' ? 'selected' : '' }}>⚠️ Mengulang (Tinggal)</option>
                                                <option value="transfer" {{ $defaultAction === 'transfer' ? 'selected' : '' }}>↔️ Mutasi / Pindah</option>
                                            @else
                                                <option value="promote" {{ $defaultAction === 'promote' ? 'selected' : '' }}>✅ Naik Kelas</option>
                                                <option value="retain" {{ $defaultAction === 'retain' ? 'selected' : '' }}>⚠️ Tinggal Kelas</option>
                                                <option value="graduate" {{ $defaultAction === 'graduate' ? 'selected' : '' }}>🎓 Lulus</option>
                                                <option value="transfer" {{ $defaultAction === 'transfer' ? 'selected' : '' }}>↔️ Mutasi / Pindah</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($isFinalGrade)
                                            <span class="text-xs font-semibold text-purple-600 dark:text-purple-400 italic">
                                                Lulusan SDIT (Status Alumni)
                                            </span>
                                            <input type="hidden" name="decisions[{{ $st->id }}][target_class_id]" value="">
                                        @else
                                            <select name="decisions[{{ $st->id }}][target_class_id]" 
                                                    class="student-target-class-select w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1.5 focus:ring-emerald-500">
                                                @foreach($targetClasses as $tc)
                                                    <option value="{{ $tc->id }}" {{ ($perf['target_class_id'] ?? $defaultTargetClass?->id) == $tc->id ? 'selected' : '' }}>
                                                        {{ $tc->name }} (Tk. {{ $tc->grade ?? $tc->level }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="text" name="decisions[{{ $st->id }}][notes]" 
                                               placeholder="Catatan rapat dewan guru..." 
                                               class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white py-1.5">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer & Tombol Simpan -->
                <div class="p-4 bg-slate-50 dark:bg-slate-700/60 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-bold text-slate-700 dark:text-slate-200">Perhatian:</span> Menekan tombol proses akan memperbarui penempatan kelas santri untuk T.A {{ $targetYear?->name ?? 'Baru' }}, dan secara otomatis mengarsipkan kelas lama di tabel riwayat tanpa menghapus nilai rapor masa lalu.
                    </div>

                    <button type="submit" 
                            onclick="return confirm('Apakah Anda yakin ingin memproses kenaikan kelas untuk santri di kelas ini? Riwayat akademik tahun lalu akan diarsipkan dengan aman.')"
                            class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl text-xs shadow-md hover:shadow-lg transition flex items-center gap-2 cursor-pointer shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Proses Kenaikan Kelas Sekarang</span>
                    </button>
                </div>
            @endif
        </div>
    </form>

</div>
@endsection
