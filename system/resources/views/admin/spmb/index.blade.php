@extends('layouts.admin')

@section('title', 'Pendaftaran SPMB')
@section('page_title', 'Pendaftaran Siswa Baru')

@section('content')
<div class="space-y-6 w-full pb-16" x-data="{
    selected: [],
    selectAll: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    isBulkDelete: false,
    
    toggleSelectAll() {
        if (this.selectAll) {
            this.selected = [{{ implode(',', $registrations->pluck('id')->toArray()) }}];
        } else {
            this.selected = [];
        }
    },
    
    updateSelectAll() {
        const allIds = [{{ implode(',', $registrations->pluck('id')->toArray()) }}];
        this.selectAll = allIds.length > 0 && allIds.every(id => this.selected.includes(id));
    },

    confirmSingleDelete(id, name) {
        this.isBulkDelete = false;
        this.deleteTarget = { id: id, name: name };
        this.deleteFormAction = '{{ url('admin/spmb') }}/' + id;
        this.showDeleteModal = true;
    },

    confirmBulkDelete() {
        if (this.selected.length === 0) return;
        this.isBulkDelete = true;
        this.deleteTarget = { name: this.selected.length + ' data pendaftaran SPMB terpilih' };
        this.deleteFormAction = '{{ route('admin.spmb.bulk-destroy') }}';
        this.showDeleteModal = true;
    }
}">
    @component('components.delete-modal', ['title' => 'Hapus Pendaftaran SPMB', 'message' => 'Apakah Anda yakin ingin menghapus :name ini? Tindakan ini tidak dapat dibatalkan.'])
        <template x-if="isBulkDelete">
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </template>
    @endcomponent

    @if(session('quota_warning'))
        <div class="p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border-2 border-amber-400 text-amber-900 dark:text-amber-200 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-black text-lg">
                    !
                </div>
                <div>
                    <h4 class="font-extrabold text-sm text-amber-950 dark:text-amber-100">Peringatan: Kuota Gelombang Penuh</h4>
                    <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5 leading-relaxed">{{ session('quota_warning')['message'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                <a href="{{ request()->fullUrl() }}" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs hover:bg-slate-300 transition">
                    Batalkan
                </a>
                <form action="{{ session('quota_warning')['action'] }}" method="POST">
                    @csrf
                    <input type="hidden" name="override_quota" value="1">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition shadow-md flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        <span>Override & Konfirmasi</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- TailAdmin Hero Header -->
    <div class="tailadmin-card bg-gradient-to-r from-[#1C2434] via-[#24303F] to-[#1C2434] p-6 sm:p-8 text-white relative overflow-hidden border border-[#2E3A47] shadow-lg">
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-[#3C50E0]/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-[#3C50E0]/20 rounded-lg text-xs font-bold text-sky-300 border border-[#3C50E0]/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sistem SPMB Online <strong class="text-white">Aktif</strong></span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    Pendaftaran Siswa Baru (SPMB)
                </h1>
                <p class="text-xs sm:text-sm text-[#8A99AD] leading-relaxed font-medium">
                    Kelola dan lakukan verifikasi berkas pendaftaran calon peserta didik baru secara sistematis dan real-time.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('spmb.register') }}" target="_blank" 
                   class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition-all border border-white/10 flex items-center space-x-2 backdrop-blur-md shadow-xs">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Formulir Public</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Monitoring Kuota Gelombang Card -->
    @if(isset($activeWave) && $activeWave)
        @php
            $targetQuota = $activeWave->quota ?? 0;
            $paidCount = $activeWave->paid_count;
            $pendingCount = $activeWave->pending_proof_count;
            $reservedCount = $activeWave->reserved_count;
            $remainingQuota = $activeWave->remaining_quota;
            $isFull = $activeWave->isQuotaFull();
            $paidPercent = $targetQuota > 0 ? min(100, round(($paidCount / $targetQuota) * 100)) : 0;
            $pendingPercent = $targetQuota > 0 ? min(100 - $paidPercent, round(($pendingCount / $targetQuota) * 100)) : 0;
            $totalPercent = min(100, $paidPercent + $pendingPercent);
        @endphp
        <div class="tailadmin-card p-6 bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] shadow-sm space-y-5">
            <!-- Header bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-2xl {{ $isFull ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' : 'bg-[#3C50E0]/10 text-[#3C50E0] border border-[#3C50E0]/20' }} flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white">Monitoring Kuota Pendaftaran: {{ $activeWave->name }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider {{ $isFull ? 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200' }}">
                                {{ $isFull ? 'KUOTA PENUH' : 'KUOTA TERSEDIA' }}
                            </span>
                        </div>
                        <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">
                            Tahun Ajaran: <strong class="text-[#1C2434] dark:text-white">{{ $activeWave->academicYear->name ?? $activeWave->year }}</strong> • Periode: {{ $activeWave->start_date ? $activeWave->start_date->format('d M Y') : '-' }} s/d {{ $activeWave->end_date ? $activeWave->end_date->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>

                @if(isset($waves) && count($waves) > 1)
                    <form method="GET" action="{{ route('admin.spmb.index') }}" class="flex items-center gap-2">
                        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                        @if(request('payment_status'))<input type="hidden" name="payment_status" value="{{ request('payment_status') }}">@endif
                        <label class="text-[10px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase whitespace-nowrap">Pilih Gelombang:</label>
                        <select name="wave_id" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-slate-50 dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] rounded-xl text-[#1C2434] dark:text-white font-semibold outline-none">
                            <option value="all">Semua Gelombang</option>
                            @foreach($waves as $w)
                                <option value="{{ $w->id }}" {{ $activeWave->id == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }} ({{ $w->status }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>

            <!-- 4 Metric Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Quota -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#1A222C]/60 border border-[#E2E8F0] dark:border-[#2E3A47] space-y-1">
                    <p class="text-[10px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Kapasitas Kuota</p>
                    <p class="text-2xl font-black text-[#1C2434] dark:text-white">{{ $targetQuota ? number_format($targetQuota) : '∞' }} <span class="text-xs font-bold text-[#64748B]">Siswa</span></p>
                    <p class="text-[10px] text-[#64748B] dark:text-[#8A99AD]">Batas pendaftaran resmi</p>
                </div>

                <!-- Paid (Lunas) -->
                <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200/80 dark:border-emerald-800/40 space-y-1">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-extrabold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Lunas Pembayaran</p>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <p class="text-2xl font-black text-emerald-700 dark:text-emerald-400">{{ number_format($paidCount) }} <span class="text-xs font-bold">Siswa</span></p>
                    <p class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80 font-semibold">Kursi terkunci permanen</p>
                </div>

                <!-- Pending Proof (Mereservasi Kursi) -->
                <div class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-800/40 space-y-1">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-extrabold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Menunggu Verifikasi</p>
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    </div>
                    <p class="text-2xl font-black text-amber-700 dark:text-amber-400">{{ number_format($pendingCount) }} <span class="text-xs font-bold">Siswa</span></p>
                    <p class="text-[10px] text-amber-600/80 dark:text-amber-400/80 font-semibold">Bukti upload (mereservasi kursi)</p>
                </div>

                <!-- Remaining Seats -->
                <div class="p-4 rounded-2xl {{ $remainingQuota === 0 ? 'bg-rose-50/60 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800/40' : 'bg-blue-50/60 dark:bg-blue-950/20 border-blue-200/80 dark:border-blue-800/40' }} border space-y-1">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-extrabold {{ $remainingQuota === 0 ? 'text-rose-700 dark:text-rose-400' : 'text-blue-700 dark:text-blue-400' }} uppercase tracking-wider">Sisa Kursi Tersedia</p>
                        <span class="w-2 h-2 rounded-full {{ $remainingQuota === 0 ? 'bg-rose-500' : 'bg-blue-500' }}"></span>
                    </div>
                    <p class="text-2xl font-black {{ $remainingQuota === 0 ? 'text-rose-700 dark:text-rose-400' : 'text-blue-700 dark:text-blue-400' }}">
                        {{ $targetQuota > 0 ? number_format($remainingQuota) : 'Tanpa Batas' }} <span class="text-xs font-bold">Kursi</span>
                    </p>
                    <p class="text-[10px] {{ $remainingQuota === 0 ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-blue-600/80 dark:text-blue-400/80 font-semibold' }}">
                        {{ $remainingQuota === 0 ? 'Pendaftaran otomatis tertutup' : 'Dapat diperebutkan pendaftar' }}
                    </p>
                </div>
            </div>

            <!-- Progress Bar Visualization -->
            <div class="space-y-2 pt-1">
                <div class="flex items-center justify-between text-xs font-bold">
                    <div class="flex items-center space-x-4">
                        <span class="text-[#1C2434] dark:text-white font-extrabold">Akumulasi Kuota Terpakai:</span>
                        <div class="flex items-center space-x-3 text-[11px]">
                            <span class="flex items-center space-x-1.5 text-emerald-600 dark:text-emerald-400">
                                <span class="w-2.5 h-2.5 rounded-sm bg-emerald-500"></span>
                                <span>Lunas ({{ $paidCount }})</span>
                            </span>
                            <span class="flex items-center space-x-1.5 text-amber-600 dark:text-amber-400">
                                <span class="w-2.5 h-2.5 rounded-sm bg-amber-500"></span>
                                <span>Pending Bukti ({{ $pendingCount }})</span>
                            </span>
                            <span class="flex items-center space-x-1.5 text-slate-500 dark:text-slate-400">
                                <span class="w-2.5 h-2.5 rounded-sm bg-slate-300 dark:bg-slate-700"></span>
                                <span>Sisa Kursi ({{ $remainingQuota }})</span>
                            </span>
                        </div>
                    </div>
                    <span class="text-xs font-extrabold {{ $isFull ? 'text-rose-600 dark:text-rose-400' : 'text-[#3C50E0]' }}">
                        {{ $reservedCount }} / {{ $targetQuota }} Siswa ({{ $totalPercent }}% Terisi)
                    </span>
                </div>

                <div class="w-full bg-slate-200 dark:bg-slate-700 h-3 rounded-full overflow-hidden flex p-0.5">
                    @if($paidPercent > 0)
                        <div style="width: {{ $paidPercent }}%" class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-l-full" title="Lunas: {{ $paidCount }} ({{ $paidPercent }}%)"></div>
                    @endif
                    @if($pendingPercent > 0)
                        <div style="width: {{ $pendingPercent }}%" class="bg-gradient-to-r from-amber-400 to-amber-500 h-full {{ $paidPercent == 0 ? 'rounded-l-full' : '' }} {{ $remainingQuota == 0 ? 'rounded-r-full' : '' }}" title="Pending Bukti: {{ $pendingCount }} ({{ $pendingPercent }}%)"></div>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between text-[11px] text-[#64748B] dark:text-[#8A99AD] pt-1">
                    <span>*Calon siswa yang telah mengunggah bukti transfer otomatis <strong>mereservasi 1 kursi</strong> selama menunggu verifikasi bendahara.</span>
                    @if(isset($activeWaveUnpaid) && $activeWaveUnpaid > 0)
                        <span class="text-amber-600 dark:text-amber-400 font-semibold mt-1 sm:mt-0">• {{ $activeWaveUnpaid }} akun terdaftar belum membayar / belum upload bukti.</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- TailAdmin Quick Stats Grid (5 Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Total -->
        <div class="tailadmin-card p-5 relative overflow-hidden group">
            <div class="flex items-center justify-between relative z-10">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Total Pendaftar</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">{{ number_format(\App\Models\SpmbRegistration::count()) }}</p>
                    <p class="text-[10px] text-[#64748B] dark:text-[#8A99AD] font-semibold">Semua berkas masuk</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-[#3C50E0]/10 text-[#3C50E0] flex items-center justify-center font-bold border border-[#3C50E0]/20 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="tailadmin-card p-5 relative overflow-hidden group">
            <div class="flex items-center justify-between relative z-10">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Pending Review</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 dark:text-amber-400 tracking-tight">{{ number_format(\App\Models\SpmbRegistration::where('status', 'submitted')->count()) }}</p>
                    <p class="text-[10px] text-amber-600/80 font-bold">Butuh tindakan admin</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold border border-amber-200 dark:border-amber-800 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Verified -->
        <div class="tailadmin-card p-5 relative overflow-hidden group">
            <div class="flex items-center justify-between relative z-10">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Terverifikasi</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-blue-600 dark:text-blue-400 tracking-tight">{{ number_format(\App\Models\SpmbRegistration::where('status', 'verified')->count()) }}</p>
                    <p class="text-[10px] text-blue-600/80 font-bold">Berkas lengkap</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold border border-blue-200 dark:border-blue-800 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Accepted -->
        <div class="tailadmin-card p-5 relative overflow-hidden group">
            <div class="flex items-center justify-between relative z-10">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Diterima / Lulus</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight">{{ number_format(\App\Models\SpmbRegistration::where('status', 'accepted')->count()) }}</p>
                    <p class="text-[10px] text-emerald-600/80 font-bold">Siswa diterima</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold border border-emerald-200 dark:border-emerald-800 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
        </div>

        <!-- Rejected -->
        <div class="tailadmin-card p-5 relative overflow-hidden group">
            <div class="flex items-center justify-between relative z-10">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Ditolak / Gugur</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-rose-600 dark:text-rose-400 tracking-tight">{{ number_format(\App\Models\SpmbRegistration::where('status', 'rejected')->count()) }}</p>
                    <p class="text-[10px] text-rose-500/80 font-bold">Tidak memenuhi syarat</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold border border-rose-200 dark:border-rose-800 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Container Card -->
    <div class="tailadmin-card overflow-hidden">
        <!-- Filter Tabs Header & Bulk Delete Action -->
        <div class="px-6 border-b border-[#E2E8F0] dark:border-[#2E3A47] bg-slate-50/50 dark:bg-[#1A222C]/40 py-3 space-y-3">
            <div class="flex items-center justify-between gap-4 flex-wrap sm:flex-nowrap">
                <div class="flex space-x-2 overflow-x-auto py-1">
                    @foreach(['all' => 'Semua Pendaftar', 'submitted' => 'Pending Review', 'verified' => 'Terverifikasi', 'accepted' => 'Diterima', 'rejected' => 'Ditolak'] as $key => $label)
                        <a href="?status={{ $key === 'all' ? '' : $key }}{{ request('payment_status') ? '&payment_status=' . request('payment_status') : '' }}{{ request('wave_id') ? '&wave_id=' . request('wave_id') : '' }}"
                           class="px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider rounded-xl transition-all flex items-center space-x-2 whitespace-nowrap
                                  {{ request('status') === $key || ($key === 'all' && !request('status')) 
                                     ? 'bg-[#3C50E0] text-white shadow-md shadow-[#3C50E0]/20' 
                                     : 'bg-white dark:bg-[#24303F] text-[#64748B] dark:text-[#8A99AD] hover:bg-slate-100 dark:hover:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47]' }}">
                            <span>{{ $label }}</span>
                            @php
                                $countQ = \App\Models\SpmbRegistration::query();
                                if ($key !== 'all') $countQ->where('status', $key);
                                if (request('payment_status') && request('payment_status') !== 'all') $countQ->where('payment_status', request('payment_status'));
                                if (request('wave_id') && request('wave_id') !== 'all') $countQ->where('wave_id', request('wave_id'));
                                $count = $countQ->count();
                            @endphp
                            @if($count > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ request('status') === $key || ($key === 'all' && !request('status')) ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-[#1A222C] text-[#1C2434] dark:text-white' }}">{{ $count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Payment Status Secondary Filters -->
            <div class="flex items-center space-x-2 overflow-x-auto pt-2 border-t border-slate-200/60 dark:border-[#2E3A47]/60">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#64748B] dark:text-[#8A99AD] shrink-0 mr-1">Status Bayar:</span>
                @foreach(['all' => 'Semua', 'paid' => 'Lunas', 'pending' => 'Menunggu Verifikasi', 'unpaid' => 'Belum Bayar'] as $pKey => $pLabel)
                    @php
                        $isActivePayment = request('payment_status') === $pKey || ($pKey === 'all' && !request('payment_status'));
                    @endphp
                    <a href="?payment_status={{ $pKey === 'all' ? '' : $pKey }}{{ request('status') ? '&status=' . request('status') : '' }}{{ request('wave_id') ? '&wave_id=' . request('wave_id') : '' }}"
                       class="px-2.5 py-1 text-[11px] font-bold rounded-lg transition-all flex items-center space-x-1.5 whitespace-nowrap {{ $isActivePayment ? 'bg-slate-800 text-white dark:bg-white dark:text-slate-900 shadow-xs' : 'text-[#64748B] dark:text-[#8A99AD] hover:bg-slate-200/60 dark:hover:bg-slate-800/60' }}">
                        <span>{{ $pLabel }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Bulk Action Banner Bar -->
        <div x-show="selected.length > 0" x-cloak x-transition class="flex items-center justify-between px-6 py-3 bg-rose-50 dark:bg-rose-950/40 border-b border-rose-200 dark:border-rose-800">
            <div class="flex items-center space-x-2 text-rose-700 dark:text-rose-300 font-bold text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="selected.length + ' data pendaftaran dipilih'"></span>
            </div>
            <button type="button" @click="confirmBulkDelete()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center space-x-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus Data Terpilih</span>
            </button>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider font-bold border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                    <tr>
                        <th class="px-4 py-3.5 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-[#3C50E0] focus:ring-[#3C50E0] cursor-pointer" title="Pilih Semua">
                        </th>
                        <th class="px-6 py-3.5">No. Registrasi & Calon Siswa</th>
                        <th class="px-6 py-3.5">Gelombang & Jurusan</th>
                        <th class="px-6 py-3.5">Tanggal Daftar</th>
                        <th class="px-6 py-3.5 text-center">Status Pembayaran</th>
                        <th class="px-6 py-3.5 text-center">Status Verifikasi</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] text-[#1C2434] dark:text-[#DEE4EE]">
                    @forelse($registrations as $registration)
                        <tr class="hover:bg-[#F1F5F9]/60 dark:hover:bg-[#1A222C]/50 transition-colors" :class="{ 'bg-rose-50/30 dark:bg-rose-950/20': selected.includes({{ $registration->id }}) }">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" :value="{{ $registration->id }}" x-model="selected" @change="updateSelectAll()" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-[#3C50E0] focus:ring-[#3C50E0] cursor-pointer">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-10 h-10 bg-[#3C50E0]/10 text-[#3C50E0] rounded-xl flex items-center justify-center font-extrabold text-xs border border-[#3C50E0]/20 shrink-0">
                                        {{ strtoupper(substr($registration->full_name, 0, 2)) }}
                                    </div>
                                    <div class="space-y-0.5 min-w-0">
                                        <span class="inline-block px-2 py-0.5 bg-[#3C50E0]/10 text-[#3C50E0] font-mono text-[10px] font-bold rounded-md border border-[#3C50E0]/20">
                                            {{ $registration->registration_number }}
                                        </span>
                                        <a href="{{ route('admin.spmb.show', encode_id($registration->id)) }}" class="font-bold text-[#1C2434] dark:text-white hover:text-[#3C50E0] transition-colors block truncate">
                                            {{ $registration->full_name }}
                                        </a>
                                        <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] truncate">{{ $registration->email }} • {{ $registration->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#F1F5F9] dark:bg-[#1A222C] text-[#1C2434] dark:text-white border border-[#E2E8F0] dark:border-[#2E3A47]">
                                        {{ $registration->wave?->name ?? 'Gelombang Umum' }}
                                    </span>
                                    <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] font-semibold truncate">
                                        {{ $registration->firstChoiceMajor?->name ?? 'Semua Jurusan' }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-[#1C2434] dark:text-white">{{ $registration->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-[#64748B] dark:text-[#8A99AD]">{{ $registration->created_at->format('H:i') }} WIB</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($registration->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span>Lunas</span>
                                    </span>
                                @elseif($registration->payment_status === 'pending')
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800" title="Menunggu verifikasi admin (kursi direservasi)">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>Menunggu Verifikasi</span>
                                        </span>
                                        @if($registration->payment_proof)
                                            <a href="{{ route('admin.spmb.show', encode_id($registration->id)) }}" class="text-[9px] text-[#3C50E0] hover:underline font-extrabold mt-1 inline-flex items-center gap-0.5">
                                                <span>Cek Bukti</span>
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Belum Bayar</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $badgeStyle = match($registration->status) {
                                        'accepted' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                        'rejected' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                        'verified' => 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                        'submitted' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                        default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 border-slate-200',
                                    };
                                    $statusLabel = match($registration->status) {
                                        'accepted' => 'Diterima',
                                        'rejected' => 'Ditolak',
                                        'verified' => 'Terverifikasi',
                                        'submitted' => 'Menunggu',
                                        default => ucfirst($registration->status),
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold border {{ $badgeStyle }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a href="{{ route('admin.spmb.show', encode_id($registration->id)) }}"
                                       class="px-3.5 py-2 rounded-xl bg-[#3C50E0]/10 text-[#3C50E0] hover:bg-[#3C50E0] hover:text-white font-bold text-xs transition-all border border-[#3C50E0]/20 inline-flex items-center space-x-1.5">
                                        <span>Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    @permission('delete-spmb')
                                    <button type="button" @click="confirmSingleDelete('{{ encode_id($registration->id) }}', '{{ addslashes($registration->full_name . ' (' . $registration->registration_number . ')') }}')"
                                            class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-600 hover:text-white text-rose-600 dark:text-rose-400 font-bold text-xs transition-all border border-rose-200 dark:border-rose-800 inline-flex items-center cursor-pointer"
                                            title="Hapus Data">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    @endpermission
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-[#64748B] dark:text-[#8A99AD] text-xs font-semibold">
                                Belum ada data pendaftar untuk kriteria status ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="px-6 py-4 border-t border-[#E2E8F0] dark:border-[#2E3A47] bg-slate-50/50 dark:bg-[#1A222C]/40">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

