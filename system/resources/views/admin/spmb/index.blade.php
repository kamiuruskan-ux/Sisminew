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
        <div class="px-6 border-b border-[#E2E8F0] dark:border-[#2E3A47] bg-slate-50/50 dark:bg-[#1A222C]/40">
            <div class="flex items-center justify-between gap-4 py-3 flex-wrap sm:flex-nowrap">
                <div class="flex space-x-2 overflow-x-auto py-1">
                    @foreach(['all' => 'Semua Pendaftar', 'submitted' => 'Pending Review', 'verified' => 'Terverifikasi', 'accepted' => 'Diterima', 'rejected' => 'Ditolak'] as $key => $label)
                        <a href="?status={{ $key === 'all' ? '' : $key }}"
                           class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl transition-all flex items-center space-x-2 whitespace-nowrap
                                  {{ request('status') === $key || ($key === 'all' && !request('status')) 
                                     ? 'bg-[#3C50E0] text-white shadow-md shadow-[#3C50E0]/20' 
                                     : 'bg-white dark:bg-[#24303F] text-[#64748B] dark:text-[#8A99AD] hover:bg-slate-100 dark:hover:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47]' }}">
                            <span>{{ $label }}</span>
                            @php
                                $count = $key === 'all' 
                                    ? \App\Models\SpmbRegistration::count() 
                                    : \App\Models\SpmbRegistration::where('status', $key)->count();
                            @endphp
                            @if($count > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ request('status') === $key || ($key === 'all' && !request('status')) ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-[#1A222C] text-[#1C2434] dark:text-white' }}">{{ $count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
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
                            <td colspan="6" class="px-6 py-12 text-center text-[#64748B] dark:text-[#8A99AD] text-xs font-semibold">
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

