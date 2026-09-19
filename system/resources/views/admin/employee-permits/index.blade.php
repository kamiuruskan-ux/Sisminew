@extends('layouts.admin')

@section('title', 'Kelola Izin & Cuti Pegawai')

@section('content')
<div class="space-y-6" x-data="{ 
    approveModal: false, 
    rejectModal: false, 
    createPermitModal: false, 
    previewDocModal: false,
    activePermit: null,
    previewUrl: '',
    previewIsDoc: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(actionUrl, permitName) {
        this.deleteTarget = { name: permitName };
        this.deleteFormAction = actionUrl;
        this.showDeleteModal = true;
    },
    openPreview(url, isDoc) {
        this.previewUrl = url;
        this.previewIsDoc = isDoc;
        this.previewDocModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Permohonan Izin', 'message' => 'Apakah Anda yakin ingin menghapus data permohonan izin :name ini? Tindakan ini tidak dapat dibatalkan.'])

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <span class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                <span>{{ $isPrincipal ? 'Verifikasi Izin & Cuti Pegawai' : 'Permohonan Izin & Cuti Saya' }}</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">
                {{ $isPrincipal ? 'Validasi dan verifikasi permohonan izin/cuti dewan guru dan staf. Izin yang disetujui otomatis masuk ke rekapitulasi presensi sekolah.' : 'Ajukan surat izin atau sakit dan pantau status verifikasi persetujuan Kepala Sekolah.' }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.teacher-attendances.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 font-extrabold text-xs flex items-center gap-2 transition-all shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                <span>Presensi Guru</span>
            </a>
            <button type="button" @click="createPermitModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-extrabold text-xs flex items-center gap-2 shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                <svg class="w-4 h-4 text-indigo-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Ajukan Izin Baru</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-base">
                {{ $stats['total'] }}
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Izin</p>
                <p class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['total'] }} Permohonan</p>
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-amber-200 dark:border-amber-900/50 bg-amber-50/20 dark:bg-amber-900/10 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-base">
                {{ $stats['pending'] }}
            </div>
            <div>
                <p class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Menunggu</p>
                <p class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['pending'] }} {{ $isPrincipal ? 'Perlu Diverifikasi' : 'Diproses' }}</p>
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/20 dark:bg-emerald-900/10 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-base">
                {{ $stats['approved'] }}
            </div>
            <div>
                <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Disetujui</p>
                <p class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['approved'] }} Presensi Masuk</p>
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-800 border border-rose-200 dark:border-rose-900/50 bg-rose-50/20 dark:bg-rose-900/10 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-base">
                {{ $stats['rejected'] }}
            </div>
            <div>
                <p class="text-[11px] font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Ditolak</p>
                <p class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $stats['rejected'] }} Ditolak</p>
            </div>
        </div>
    </div>

    <!-- Filters & Search Form -->
    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs">
        <form action="{{ route('admin.employee-permits.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            @if($isPrincipal)
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Cari Pegawai / Guru</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau NIP..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Pegawai</label>
                    <select name="user_id" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                        <option value="">Semua Pegawai</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Izin</label>
                <select name="permit_type" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                    <option value="">Semua Jenis</option>
                    <option value="sakit" {{ request('permit_type') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ request('permit_type') == 'izin' ? 'selected' : '' }}>Izin Pribadi</option>
                    <option value="cuti" {{ request('permit_type') == 'cuti' ? 'selected' : '' }}>Cuti Tahunan/Khusus</option>
                    <option value="tugas_luar" {{ request('permit_type') == 'tugas_luar' ? 'selected' : '' }}>Tugas / Dinas Luar</option>
                    <option value="lainnya" {{ request('permit_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Status Verifikasi</label>
                <select name="status" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full px-4 py-2 bg-slate-900 hover:bg-black dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl text-xs font-extrabold flex items-center justify-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Filter
                </button>
                <a href="{{ route('admin.employee-permits.index') }}" class="px-3 py-2 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition-all" title="Reset Filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Permits Data Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50/75 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-extrabold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Pegawai</th>
                        <th class="py-3.5 px-4">Jenis Izin</th>
                        <th class="py-3.5 px-4">Rentang Tanggal</th>
                        <th class="py-3.5 px-4">Alasan & Dokumen</th>
                        <th class="py-3.5 px-4">Status & Catatan Kepsek</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($permits as $permit)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-750 transition-colors">
                            <!-- Employee Info -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold flex items-center justify-center shrink-0 border border-indigo-200/40">
                                        {{ strtoupper(substr($permit->user->name ?? 'P', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 dark:text-white text-xs">{{ $permit->user->name ?? 'Pegawai' }}</p>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">NIP: {{ $permit->user->nip ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Permit Type -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $permit->type_badge_class }}">
                                    {{ $permit->type_label }}
                                </span>
                            </td>

                            <!-- Date Range & Duration -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 dark:text-slate-200">
                                    {{ \Carbon\Carbon::parse($permit->start_date)->translatedFormat('d M Y') }}
                                    @if($permit->start_date != $permit->end_date)
                                        <span class="text-slate-400 font-normal">s/d</span>
                                        {{ \Carbon\Carbon::parse($permit->end_date)->translatedFormat('d M Y') }}
                                    @endif
                                </div>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 mt-1 inline-block">
                                    {{ $permit->duration_days }} Hari
                                </span>
                            </td>

                            <!-- Reason & Attachment -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="text-slate-700 dark:text-slate-300 font-medium line-clamp-2" title="{{ $permit->reason }}">
                                    {{ $permit->reason }}
                                </p>
                                @if($permit->emergency_contact)
                                    <p class="text-[10px] text-slate-400 mt-0.5">Kontak: {{ $permit->emergency_contact }}</p>
                                @endif
                                @if($permit->proof_file)
                                    @php
                                        $ext = strtolower(pathinfo($permit->proof_file, PATHINFO_EXTENSION));
                                        $isDoc = in_array($ext, ['pdf', 'doc', 'docx']);
                                    @endphp
                                    <div class="mt-1.5">
                                        <button type="button" 
                                                @click="openPreview('{{ asset($permit->proof_file) }}', {{ $isDoc ? 'true' : 'false' }})"
                                                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            <span>Lihat Berkas Bukti ({{ strtoupper($ext) }})</span>
                                        </button>
                                    </div>
                                @endif
                            </td>

                            <!-- Status & Approver Notes -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $permit->status_badge_class }}">
                                    @if($permit->status === 'approved')
                                        <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($permit->status === 'rejected')
                                        <svg class="w-3 h-3 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @else
                                        <svg class="w-3 h-3 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                    {{ $permit->status_label }}
                                </span>
                                
                                @if($permit->approver)
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 font-medium">
                                        Oleh: {{ $permit->approver->name }} ({{ $permit->approved_at?->format('d/m H:i') }})
                                    </p>
                                @endif
                                
                                @if($permit->notes)
                                    <div class="mt-1 p-1.5 bg-slate-50 dark:bg-slate-700/50 rounded-lg text-[10px] text-slate-600 dark:text-slate-300 font-medium border border-slate-200/50 dark:border-slate-600/50">
                                        <span class="font-bold">Catatan:</span> {{ $permit->notes }}
                                    </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($isPrincipal)
                                        @if($permit->status === 'pending')
                                            <!-- Approve Button -->
                                            <button type="button" 
                                                    @click="activePermit = {{ json_encode($permit) }}; approveModal = true"
                                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] flex items-center gap-1 transition-all shadow-xs"
                                                    title="Setujui Izin Pegawai">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Setujui</span>
                                            </button>

                                            <!-- Reject Button -->
                                            <button type="button" 
                                                    @click="activePermit = {{ json_encode($permit) }}; rejectModal = true"
                                                    class="px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] flex items-center gap-1 transition-all shadow-xs"
                                                    title="Tolak Izin Pegawai">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Tolak</span>
                                            </button>
                                        @else
                                            <!-- Change Decision -->
                                            <div class="flex items-center gap-1">
                                                @if($permit->status === 'approved')
                                                    <button type="button" 
                                                            @click="activePermit = {{ json_encode($permit) }}; rejectModal = true"
                                                            class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-semibold text-[11px] border border-rose-200/50"
                                                            title="Batalkan & Tolak Izin">
                                                        Ubah ke Tolak
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            @click="activePermit = {{ json_encode($permit) }}; approveModal = true"
                                                            class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-semibold text-[11px] border border-emerald-200/50"
                                                            title="Ubah & Setujui Izin">
                                                        Ubah ke Setuju
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Delete Button (if allowed) -->
                                    @if($isPrincipal || $permit->status === 'pending')
                                        <button type="button" 
                                                @click="confirmDelete('{{ route('admin.employee-permits.destroy', $permit->id) }}', '{{ $permit->user->name ?? 'Izin' }} ({{ $permit->type_label }})')"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition-colors"
                                                title="Hapus Permohonan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center text-slate-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-600 dark:text-slate-400">Belum ada data permohonan izin pegawai</p>
                                <p class="text-xs text-slate-400 mt-1">Semua pengajuan surat izin atau sakit akan tertera di tabel ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($permits->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                {{ $permits->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: APPROVE PERMIT (KEPALA SEKOLAH / ADMIN)                            -->
    <!-- ========================================================================= -->
    <div x-show="approveModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-4"
             @click.away="approveModal = false">
            <div class="flex items-center gap-3 text-emerald-600 dark:text-emerald-400">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Setujui Permohonan Izin</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Verifikasi Resmi Kepala Sekolah</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-300">
                Apakah Anda yakin ingin menyetujui izin untuk <strong class="text-slate-900 dark:text-white" x-text="activePermit?.user?.name"></strong>?
                Data presensi akan otomatis dicatat sebagai izin resmi pada tanggal yang bersangkutan.
            </p>

            <form :action="'{{ url('admin/employee-permits') }}/' + activePermit?.id + '/approve'" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan Kepala Sekolah (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Disetujui, lekas sembuh dan titipkan materi ajar kepada piket." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="approveModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Konfirmasi &amp; Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: REJECT PERMIT (KEPALA SEKOLAH / ADMIN)                             -->
    <!-- ========================================================================= -->
    <div x-show="rejectModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-4"
             @click.away="rejectModal = false">
            <div class="flex items-center gap-3 text-rose-600 dark:text-rose-400">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tolak Permohonan Izin</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Berikan penjelasan alasan penolakan</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-300">
                Anda akan menolak pengajuan izin untuk <strong class="text-slate-900 dark:text-white" x-text="activePermit?.user?.name"></strong>.
            </p>

            <form :action="'{{ url('admin/employee-permits') }}/' + activePermit?.id + '/reject'" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Penolakan / Catatan <span class="text-rose-500">*</span></label>
                    <textarea name="notes" rows="3" required placeholder="Contoh: Dokumen bukti dokter belum lengkap / Jadwal ujian sekolah berlangsung." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="rejectModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs flex items-center gap-1.5 shadow-md shadow-rose-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Tolak Izin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: CREATE PERMIT (POPUP MODAL PENGAJUAN IZIN CEPAT)                   -->
    <!-- ========================================================================= -->
    <div x-show="createPermitModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity overflow-y-auto"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-4 my-8"
             @click.away="createPermitModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Formulir Pengajuan Izin Pegawai</h3>
                        <p class="text-[11px] text-slate-400">Isi data permohonan izin atau surat keterangan sakit</p>
                    </div>
                </div>
                <button type="button" @click="createPermitModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.employee-permits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf

                @if($isPrincipal)
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Pegawai / Guru <span class="text-rose-500">*</span></label>
                        <select name="user_id" required class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            <option value="">-- Pilih Guru / Staf --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $emp->id === auth()->id() ? 'selected' : '' }}>{{ $emp->name }} (NIP: {{ $emp->nip ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Izin <span class="text-rose-500">*</span></label>
                        <select name="permit_type" required class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            <option value="sakit">Sakit</option>
                            <option value="izin" selected>Izin Keperluan Pribadi</option>
                            <option value="cuti">Cuti Tahunan / Bersalin</option>
                            <option value="tugas_luar">Tugas / Dinas Luar</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Kontak Darurat</label>
                        <input type="text" name="emergency_contact" placeholder="08xxxxxxxxxx" value="{{ auth()->user()->phone ?? '' }}" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Mulai Tanggal <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sampai Tanggal <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date" required value="{{ date('Y-m-d') }}" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Ketidakhadiran <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="3" required placeholder="Tuliskan keterangan detail alasan berhalangan hadir..." class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Lampiran Berkas Bukti (Surat Dokter / Surat Tugas)</label>
                    <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500/20 outline-none">
                    <p class="text-[10px] text-slate-400 mt-1">Format diperbolehkan: JPG, PNG, WEBP, PDF, DOC (Maks. 10 MB)</p>
                </div>

                @if($isPrincipal)
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl border border-indigo-200/50 dark:border-indigo-800/40">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="auto_approve" value="1" checked class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-indigo-900 dark:text-indigo-200">Langsung Setujui &amp; Sinkronkan ke Presensi Sekarang</span>
                        </label>
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" @click="createPermitModal = false" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs flex items-center gap-2 shadow-md shadow-indigo-600/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Kirim Permohonan Izin</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: PREVIEW DOKUMEN / FOTO LAMPIRAN                                     -->
    <!-- ========================================================================= -->
    <div x-show="previewDocModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs transition-opacity"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-2xl w-full p-4 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-3"
             @click.away="previewDocModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-2">
                <h4 class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    <span>Pratinjau Berkas Bukti Lampiran</span>
                </h4>
                <div class="flex items-center gap-2">
                    <a :href="previewUrl" target="_blank" download class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh Asli</span>
                    </a>
                    <button type="button" @click="previewDocModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 ml-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="max-h-[70vh] overflow-y-auto flex items-center justify-center bg-slate-900/5 dark:bg-slate-900/40 rounded-xl p-2">
                <template x-if="!previewIsDoc">
                    <img :src="previewUrl" alt="Berkas Lampiran" class="max-w-full max-h-[65vh] object-contain rounded-lg">
                </template>
                <template x-if="previewIsDoc">
                    <iframe :src="previewUrl" class="w-full h-[65vh] rounded-lg border border-slate-200 dark:border-slate-700"></iframe>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
