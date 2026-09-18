@extends('layouts.admin')

@section('title', 'Absensi Siswa')
@section('page_title', 'Absensi Siswa')

@section('content')
<div class="w-full space-y-6" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    createPermitModal: false,
    confirmDelete(id, studentName) {
        this.deleteTarget = { id: id, name: studentName };
        this.deleteFormAction = '{{ url('admin/attendances') }}/' + id;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Data Absensi', 'message' => 'Apakah Anda yakin ingin menghapus catatan absensi siswa :name ini? Tindakan ini tidak dapat dibatalkan.'])

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Absensi Siswa</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">Kelola & rekapitulasi data presensi harian siswa sekolah</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Button Input Absensi Massal/Biasa -->
            <a href="{{ route('admin.attendances.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white rounded-xl font-extrabold text-xs shadow-md transition-all">
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Input Absensi</span>
            </a>

            <!-- Button Input Izin (Modal Popup) -->
            <button type="button" @click="createPermitModal = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-xl font-extrabold text-xs shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                <svg class="w-4 h-4 text-indigo-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Input Izin</span>
            </button>

            <!-- Button Riwayat Scan -->
            <a href="{{ route('admin.qr-attendance.history') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-purple-50 dark:bg-purple-950/50 hover:bg-purple-100 dark:hover:bg-purple-900/60 border border-purple-200 dark:border-purple-800/80 text-purple-700 dark:text-purple-300 rounded-xl font-extrabold text-xs transition-all shadow-2xs">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Riwayat Scan</span>
            </a>

            <!-- Button Terminal Presensi -->
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
        <form action="{{ route('admin.attendances.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5 items-end">
            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date', request('date')) }}" @change="$el.closest('form').submit()" class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
            </div>

            <!-- Tanggal Akhir -->
            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date', request('date')) }}" @change="$el.closest('form').submit()" class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
            </div>

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

            <!-- Status Filter -->
            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Status Presensi</label>
                <select name="status" @change="$el.closest('form').submit()" class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                    <option value="">Semua Status</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Hadir</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                    <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Izin / Sakit</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>

            <!-- Action Button Cetak Ringkasan -->
            <div>
                <a href="{{ route('admin.attendances.print', array_merge(request()->all(), ['type' => 'summary'])) }}" target="_blank" class="w-full h-10 px-3 inline-flex items-center justify-center gap-1.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded-xl text-xs font-extrabold transition shadow-md shadow-rose-600/20" title="Cetak Ringkasan Kehadiran Per Siswa">
                    <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Ringkasan</span>
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
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Presensi</th>
                        <th class="px-6 py-4 text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Catatan</th>
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
                                    <span class="font-extrabold text-slate-800 dark:text-slate-100 text-xs tracking-tight">
                                        {{ $attendance->date ? $attendance->date->translatedFormat('d M Y') : '-' }}
                                    </span>
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
                                    {{ $attendance->class->name ?? ($attendance->student->class->name ?? '-') }}
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

                            <td class="px-6 py-4 text-xs font-medium text-slate-600 dark:text-slate-300 max-w-xs truncate">
                                {{ $attendance->notes ?? '-' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button type="button" @click="confirmDelete({{ $attendance->id }}, '{{ addslashes($attendance->student->user->name ?? 'Absensi') }}')" class="w-8 h-8 inline-flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl transition-all cursor-pointer" title="Hapus Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
                                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-700">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <h3 class="font-extrabold text-slate-800 dark:text-slate-200 text-sm tracking-tight mb-1">Belum Ada Data Presensi</h3>
                                <p class="text-xs text-slate-400 font-medium">Silakan lakukan presensi via QR Code atau input massal.</p>
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

    <!-- Modal Input Izin Siswa (Proportional Form Inputs) -->
    <div x-show="createPermitModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="bg-white dark:bg-[#1A222C] rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-[#2E3A47] space-y-5 my-8" @click.outside="createPermitModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#2E3A47] pb-4">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    Input Permohonan Izin Siswa
                </h3>
                <button type="button" @click="createPermitModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-[#24303F] text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-sm transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <form action="{{ route('admin.student-permits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Searchable Student Select -->
                <div>
                    <x-student-select-search 
                        :students="$students" 
                        name="student_id" 
                        required="true"
                        label="Pilih Siswa"
                        accent-color="indigo"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Jenis Izin <span class="text-rose-500">*</span></label>
                        <select name="permit_type" required class="w-full h-10 px-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                            <option value="sakit">Sakit</option>
                            <option value="izin" selected>Izin Keperluan</option>
                            <option value="dispensasi">Dispensasi / Tugas</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tgl Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tgl Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Alasan Keperluan <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="2.5" required class="w-full p-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs" placeholder="Tuliskan keterangan detail permohonan izin..."></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Lampiran Bukti Surat/Foto (Opsional)</label>
                    <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300">
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-[#2E3A47]">
                    <button type="button" @click="createPermitModal = false" class="px-4 py-2.5 rounded-xl text-xs font-extrabold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-md shadow-indigo-600/20 transition">
                        Simpan & Sync Ke Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
