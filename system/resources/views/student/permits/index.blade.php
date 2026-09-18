@extends('layouts.student-mobile')

@section('title', 'Riwayat Pengajuan Izin / Sakit')
@section('header_title', 'Izin & Sakit')

@section('content')
<div class="space-y-4 sm:space-y-5" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(actionUrl, permitName) {
        this.deleteTarget = { name: permitName };
        this.deleteFormAction = actionUrl;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Batalkan Permohonan Izin', 'message' => 'Apakah Anda yakin ingin membatalkan :name ini? Tindakan ini tidak dapat dibatalkan.'])

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 p-4 sm:p-6 text-white shadow-lg border border-indigo-700/40">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3 sm:space-x-3.5">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white flex items-center justify-center shrink-0 shadow-inner">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base sm:text-xl font-extrabold tracking-tight">Pengajuan Izin Siswa</h2>
                    <p class="text-[11px] sm:text-xs text-indigo-200/80 mt-0.5">Ajukan surat izin atau keterangan sakit sekolah</p>
                </div>
            </div>
            <a href="{{ route('student.permits.create') }}" class="w-full sm:w-auto h-10 px-4 rounded-xl bg-indigo-500 hover:bg-indigo-400 active:scale-95 text-white font-extrabold text-xs flex items-center justify-center gap-1.5 shadow-md shrink-0 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Ajukan Izin Baru</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-3.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-3.5 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Permit List -->
    <div class="space-y-3">
        @forelse($permits as $permit)
            <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/90 dark:border-slate-700/80 space-y-3">
                <div class="flex flex-wrap items-start justify-between gap-2 border-b border-slate-100 dark:border-slate-700/60 pb-3">
                    <div class="space-y-1">
                        @php
                            $badgeClass = match($permit->permit_type) {
                                'sakit' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                'izin' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800',
                                'dispensasi' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                                default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800',
                            };
                        @endphp
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] sm:text-[11px] font-extrabold border inline-block {{ $badgeClass }}">
                                {{ $permit->type_label }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">
                                {{ $permit->created_at->translatedFormat('d M Y H:i') }}
                            </span>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm flex items-center gap-1.5 pt-0.5">
                            <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>{{ $permit->start_date->translatedFormat('d M Y') }}</span>
                            @if($permit->start_date->ne($permit->end_date))
                                <span class="text-slate-400 font-normal">s/d</span>
                                <span>{{ $permit->end_date->translatedFormat('d M Y') }}</span>
                            @endif
                        </h3>
                    </div>

                    <!-- Status Pill -->
                    <div class="shrink-0">
                        @if($permit->status === 'pending')
                            <span class="px-2.5 py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-800 inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span>Menunggu</span>
                            </span>
                        @elseif($permit->status === 'approved')
                            <span class="px-2.5 py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800 inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> <span>Disetujui</span>
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-300 dark:border-rose-800 inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> <span>Ditolak</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Reason text -->
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Alasan Permohonan:</span>
                    <p class="text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-100 dark:border-slate-700/60 leading-relaxed font-medium">
                        {{ $permit->reason }}
                    </p>
                </div>

                <!-- Admin Response note if rejected/notes exist -->
                @if($permit->notes)
                    <div class="p-3 bg-purple-50 dark:bg-purple-950/40 rounded-xl text-xs text-purple-900 dark:text-purple-200 border border-purple-200 dark:border-purple-800/60 space-y-0.5">
                        <strong class="font-extrabold text-[11px] uppercase tracking-wider block text-purple-700 dark:text-purple-300">Catatan Sekolah / BK:</strong>
                        <p class="font-medium leading-relaxed">{{ $permit->notes }}</p>
                    </div>
                @endif

                <!-- Footer details & actions -->
                <div class="pt-2 flex items-center justify-between gap-2 text-xs">
                    <div>
                        @if($permit->proof_file)
                            <a href="{{ \Illuminate\Support\Str::startsWith($permit->proof_file, ['http://', 'https://', 'doc/', 'img/']) ? asset($permit->proof_file) : asset('doc/' . $permit->proof_file) }}" 
                               target="_blank" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-300 font-bold rounded-lg border border-indigo-100 dark:border-indigo-800 hover:bg-indigo-100 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Lihat Bukti Foto/Dokumen</span>
                            </a>
                        @else
                            <span class="text-[11px] text-slate-400 italic">Tanpa Lampiran Bukti</span>
                        @endif
                    </div>

                    <div>
                        @if($permit->status === 'pending')
                            <button type="button" 
                                    @click="confirmDelete('{{ route('student.permits.destroy', $permit) }}', 'permohonan {{ $permit->type_label }} ({{ \Carbon\Carbon::parse($permit->start_date)->translatedFormat('d M Y') }})')" 
                                    class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold rounded-lg border border-rose-100 dark:border-rose-800 hover:bg-rose-100 transition cursor-pointer">
                                Batalkan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-8 shadow-xs border border-slate-200 dark:border-slate-700 text-center space-y-3">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-slate-700 text-indigo-500 rounded-2xl flex items-center justify-center mx-auto shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="font-extrabold text-slate-800 dark:text-white text-xs sm:text-sm">Belum Ada Pengajuan Izin</p>
                <p class="text-[11px] text-slate-400 max-w-xs mx-auto leading-relaxed">Jika Anda berhalangan hadir sekolah, silakan ajukan surat izin atau surat dokter dengan menekan tombol di atas.</p>
            </div>
        @endforelse
    </div>

    @if($permits->hasPages())
        <div class="pt-2">
            {{ $permits->links() }}
        </div>
    @endif

</div>
@endsection
