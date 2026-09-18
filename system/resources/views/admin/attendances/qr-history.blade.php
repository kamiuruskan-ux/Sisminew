@extends('layouts.admin')

@section('title', 'Riwayat Presensi Terminal')
@section('page_title', 'Riwayat Presensi Terminal')

@section('content')
<div class="w-full space-y-6">
    <!-- Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Riwayat Presensi Terminal</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Log pemindaian presensi siswa (RFID, Fingerprint, Face ID, QR Code)</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.attendances.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-2xs">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Daftar Presensi Harian</span>
            </a>

            <a href="{{ route('admin.qr-attendance.scan') }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl font-extrabold text-xs shadow-md shadow-emerald-600/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span>Buka Terminal Presensi</span>
            </a>
        </div>
    </div>

    <!-- Filters Panel (Real-Time Auto Submit) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-xs border border-slate-200/90 dark:border-slate-800">
        <form method="GET" action="{{ route('admin.qr-attendance.history') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5 items-end">
            <x-major-class-select 
                :majors="$majors" 
                :classes="$classes" 
                :selected-major="request('major_id')" 
                :selected-class="request('class_id')" 
                :is-filter="true" 
                layout="inline" 
                major-label="Jurusan / Major" 
                class-label="Kelas Rombel" 
                select-class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs" 
                label-class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" 
                :on-major-change="'$el.closest(\'form\').submit()'"
                :on-class-change="'$el.closest(\'form\').submit()'"
            />

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', request('date')) }}" @change="$el.closest('form').submit()" 
                    class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date', request('date')) }}" @change="$el.closest('form').submit()" 
                    class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
            </div>

            <x-student-select-search :students="$students" :selected="request('student_id')" :class-id="request('class_id')" allow-all all-label="Semua Siswa" label="Pilih Siswa" :on-change="'$el.closest(\'form\').submit()'" />

            <div>
                <a href="{{ route('admin.attendances.print', array_merge(request()->all(), ['type' => 'detail'])) }}" target="_blank" class="w-full h-10 px-3 inline-flex items-center justify-center gap-1.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded-xl text-xs font-extrabold transition shadow-md shadow-rose-600/20" title="Cetak Log Presensi Detail Sesuai Filter">
                    <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Log</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xs border border-slate-200/90 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200/90 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal & Waktu</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Presensi</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Scan Oleh</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-800 dark:text-slate-100 text-xs tracking-tight">
                                            {{ $attendance->date ? $attendance->date->translatedFormat('d M Y') : '-' }}
                                        </p>
                                        <p class="text-[11px] font-mono font-bold text-slate-400 dark:text-slate-500">
                                            Jam {{ $attendance->created_at ? $attendance->created_at->format('H:i') : '-' }} WIB
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-extrabold text-xs border border-slate-200/80 dark:border-slate-700 shrink-0">
                                        {{ strtoupper(substr($attendance->student->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-800 dark:text-slate-100 text-xs tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                            {{ $attendance->student->user->name ?? '-' }}
                                        </p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-mono text-[10px] font-bold border border-slate-200/60 dark:border-slate-700 mt-0.5">
                                            NISN: {{ $attendance->student->nisn ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ $attendance->student->class->name ?? ($attendance->class->name ?? '-') }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusBadge = match($attendance->status) {
                                        'present' => ['bg' => 'bg-emerald-50 dark:bg-emerald-950/60', 'text' => 'text-emerald-700 dark:text-emerald-300', 'border' => 'border-emerald-200 dark:border-emerald-800', 'label' => 'Hadir'],
                                        'late' => ['bg' => 'bg-amber-50 dark:bg-amber-950/60', 'text' => 'text-amber-700 dark:text-amber-300', 'border' => 'border-amber-200 dark:border-amber-800', 'label' => 'Terlambat'],
                                        'excused' => ['bg' => 'bg-sky-50 dark:bg-sky-950/60', 'text' => 'text-sky-700 dark:text-sky-300', 'border' => 'border-sky-200 dark:border-sky-800', 'label' => 'Izin / Sakit'],
                                        default => ['bg' => 'bg-rose-50 dark:bg-rose-950/60', 'text' => 'text-rose-700 dark:text-rose-300', 'border' => 'border-rose-200 dark:border-rose-800', 'label' => 'Alpha'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold border {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} {{ $statusBadge['border'] }}">
                                    {{ $statusBadge['label'] }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ strtoupper(substr($attendance->scanner->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                        {{ $attendance->scanner->name ?? 'Sistem / Scanner' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.attendances.show', $attendance) }}" class="inline-flex items-center gap-1 text-xs font-extrabold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                    <span>Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
                                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-700">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                                <h3 class="font-extrabold text-slate-800 dark:text-slate-200 text-sm tracking-tight mb-1">Belum Ada Riwayat QR Code</h3>
                                <p class="text-xs text-slate-400 font-medium">Belum ada pemindaian QR Code presensi siswa yang tercatat pada sistem.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-800/50 border-t border-slate-200/90 dark:border-slate-800">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
