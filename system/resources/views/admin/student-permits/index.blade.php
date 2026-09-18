@extends('layouts.admin')

@section('title', 'Kelola Permohonan Izin Siswa')

@section('content')
<div class="space-y-6" x-data="{ 
    approveModal: false, 
    rejectModal: false, 
    createPermitModal: false, 
    activePermit: null,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(actionUrl, permitName) {
        this.deleteTarget = { name: permitName };
        this.deleteFormAction = actionUrl;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Permohonan Izin', 'message' => 'Apakah Anda yakin ingin menghapus data :name ini? Tindakan ini tidak dapat dibatalkan.'])

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <span class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                Permohonan Izin & Sakit Siswa
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">Verifikasi permohonan izin siswa. Izin yang disetujui otomatis masuk ke rekap presensi.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.attendances.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 font-extrabold text-xs flex items-center gap-2 transition-all shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Rekap Presensi
            </a>
            <button type="button" @click="createPermitModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-extrabold text-xs flex items-center gap-2 shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                <svg class="w-4 h-4 text-indigo-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Input Izin</span>
            </button>
        </div>
    </div>

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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold">
                {{ $stats['total'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Izin</p>
                <p class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['total'] }} Permohonan</p>
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-amber-200 dark:border-amber-900/50 bg-amber-50/20 dark:bg-amber-900/10 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                {{ $stats['pending'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Menunggu</p>
                <p class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['pending'] }} Perlu Diproses</p>
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/20 dark:bg-emerald-900/10 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                {{ $stats['approved'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Disetujui</p>
                <p class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['approved'] }} Presensi Masuk</p>
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-rose-200 dark:border-rose-900/50 bg-rose-50/20 dark:bg-rose-900/10 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">
                {{ $stats['rejected'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Ditolak</p>
                <p class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['rejected'] }} Ditolak</p>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('admin.student-permits.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5 items-end">
            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Status</label>
                <select name="status" @change="$el.closest('form').submit()" class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tipe Izin</label>
                <select name="permit_type" @change="$el.closest('form').submit()" class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                    <option value="">Semua Jenis</option>
                    <option value="sakit" {{ request('permit_type') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ request('permit_type') === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="dispensasi" {{ request('permit_type') === 'dispensasi' ? 'selected' : '' }}>Dispensasi</option>
                    <option value="lainnya" {{ request('permit_type') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <x-major-class-select 
                :selected-major="request('major_id')" 
                :selected-class="request('class_id')" 
                :is-filter="true" 
                layout="contents" 
                major-label="Jurusan" 
                class-label="Kelas" 
                select-class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs" 
                label-class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" 
                :on-major-change="'$el.closest(\'form\').submit()'"
                :on-class-change="'$el.closest(\'form\').submit()'"
            />

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tgl Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" @change="$el.closest('form').submit()" class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tgl Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" @change="$el.closest('form').submit()" class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 font-extrabold uppercase tracking-wider border-b border-slate-200/90 dark:border-slate-800">
                    <tr>
                        <th class="p-4">Siswa / Kelas</th>
                        <th class="p-4">Jenis & Periode</th>
                        <th class="p-4">Alasan & Keperluan</th>
                        <th class="p-4 text-center">Bukti Surat/Foto</th>
                        <th class="p-4 text-center">Status Presensi</th>
                        <th class="p-4">Waktu Pengajuan</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($permits as $permit)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-all">
                            <td class="p-4">
                                <div class="font-bold text-slate-900 dark:text-white text-sm">
                                    {{ $permit->student->user->name ?? $permit->student->full_name ?? 'Siswa' }}
                                </div>
                                <div class="text-slate-500 dark:text-slate-400 text-xs flex items-center gap-2 mt-0.5">
                                    <span>NISN: {{ $permit->student->nisn ?? '-' }}</span>
                                    <span>•</span>
                                    <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $permit->class->name ?? $permit->student->class->name ?? '-' }}</span>
                                </div>
                            </td>

                            <td class="p-4">
                                @php
                                    $badgeClass = match($permit->permit_type) {
                                        'sakit' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                        'izin' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800',
                                        'dispensasi' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                                        default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold border inline-block mb-1 {{ $badgeClass }}">
                                    {{ $permit->type_label }}
                                </span>
                                <div class="text-slate-700 dark:text-slate-300 font-semibold text-xs">
                                    {{ $permit->start_date->format('d/m/Y') }} 
                                    @if($permit->start_date->ne($permit->end_date))
                                        s/d {{ $permit->end_date->format('d/m/Y') }}
                                    @endif
                                </div>
                            </td>

                            <td class="p-4 max-w-xs">
                                <p class="text-slate-800 dark:text-slate-200 line-clamp-2" title="{{ $permit->reason }}">
                                    {{ $permit->reason }}
                                </p>
                                @if($permit->notes)
                                    <div class="mt-1 text-slate-500 italic text-[11px] bg-slate-100 dark:bg-slate-700/50 p-1.5 rounded-lg border border-slate-200 dark:border-slate-600">
                                        Catatan: {{ $permit->notes }}
                                    </div>
                                @endif
                            </td>

                            <td class="p-4 text-center">
                                @if($permit->proof_file)
                                    <a href="{{ \Illuminate\Support\Str::startsWith($permit->proof_file, ['http://', 'https://', 'doc/', 'img/']) ? asset($permit->proof_file) : asset('doc/' . $permit->proof_file) }}" 
                                       target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 text-indigo-600 dark:text-indigo-300 font-bold hover:bg-indigo-100 transition-all text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Tanpa lampiran</span>
                                @endif
                            </td>

                            <td class="p-4 text-center">
                                @if($permit->status === 'pending')
                                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-800 animate-pulse inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Menunggu Verifikasi
                                    </span>
                                @elseif($permit->status === 'approved')
                                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800 inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Disetujui (Sync Presensi)
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-300 dark:border-rose-800 inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Ditolak
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 text-slate-500 dark:text-slate-400 text-xs">
                                <div>{{ $permit->created_at->format('d M Y') }}</div>
                                <div class="text-[11px] text-slate-400">{{ $permit->created_at->format('H:i') }} WIB</div>
                            </td>

                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($permit->status === 'pending')
                                        <button type="button" 
                                                @click="activePermit = {{ json_encode($permit) }}; approveModal = true"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1"
                                                title="Setujui Izin">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Setujui
                                        </button>

                                        <button type="button" 
                                                @click="activePermit = {{ json_encode($permit) }}; rejectModal = true"
                                                class="px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1"
                                                title="Tolak Izin">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Tolak
                                        </button>
                                    @endif

                                    <button type="button" @click="confirmDelete('{{ route('admin.student-permits.destroy', $permit) }}', 'permohonan {{ addslashes($permit->student->name ?? 'Siswa') }} ({{ $permit->type_label }})')" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-all cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 dark:text-slate-500">
                                Belum ada data permohonan izin siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($permits->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
                {{ $permits->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Create Permohonan Izin (Proportional Form Inputs) -->
    <div x-show="createPermitModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-5" @click.outside="createPermitModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    Input Permohonan Izin Siswa
                </h3>
                <button type="button" @click="createPermitModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
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

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Jenis Izin <span class="text-rose-500">*</span></label>
                        <select name="permit_type" required class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                            <option value="sakit">Sakit</option>
                            <option value="izin" selected>Izin Keperluan</option>
                            <option value="dispensasi">Dispensasi / Tugas</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tgl Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tgl Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Alasan Keperluan <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="2.5" required class="w-full p-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs" placeholder="Tuliskan keterangan detail permohonan izin..."></textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Lampiran Bukti Surat/Foto (Opsional)</label>
                    <x-file-upload name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" label="Upload Dokumen Bukti Izin" help="Seret & lepas foto/PDF surat izin di sini" />
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" @click="createPermitModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md">
                        Simpan & Sync Ke Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Approve Modal -->
    <div x-show="approveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 dark:border-slate-700 space-y-4" @click.outside="approveModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                    Setujui Izin Siswa
                </h3>
                <button type="button" @click="approveModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            
            <template x-if="activePermit">
                <form :action="'{{ url('admin/student-permits') }}/' + activePermit.id + '/approve'" method="POST" class="space-y-4">
                    @csrf
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/30 rounded-xl border border-emerald-200 dark:border-emerald-800/50 text-xs text-emerald-800 dark:text-emerald-300">
                        Persetujuan izin ini akan secara otomatis memperbarui presensi siswa <strong x-text="activePermit.student?.user?.name || activePermit.student?.full_name"></strong> menjadi <span class="font-bold uppercase" x-text="activePermit.permit_type"></span> pada rentang tanggal tersebut.
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" rows="2" class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:ring-emerald-500" placeholder="Misal: Izin disetujui untuk pengobatan"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="approveModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs">
                            Ya, Setujui & Sync Presensi
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 dark:border-slate-700 space-y-4" @click.outside="rejectModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 text-rose-600">
                    <span class="p-1.5 rounded-lg bg-rose-500/10 text-rose-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></span>
                    Tolak Izin Siswa
                </h3>
                <button type="button" @click="rejectModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <template x-if="activePermit">
                <form :action="'{{ url('admin/student-permits') }}/' + activePermit.id + '/reject'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Alasan Penolakan</label>
                        <textarea name="notes" rows="3" required class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-slate-800 dark:text-slate-200 focus:ring-rose-500" placeholder="Jelaskan alasan permohonan ditolak (misal: Bukti surat tidak valid)..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="rejectModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-xs">
                            Konfirmasi Tolak
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
