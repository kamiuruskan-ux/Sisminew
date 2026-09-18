@extends('layouts.admin')

@section('title', 'Bimbingan & Konseling')
@section('page_title', 'Bimbingan & Konseling Siswa')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(actionUrl, itemName) {
        this.deleteTarget = { name: itemName };
        this.deleteFormAction = actionUrl;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Layanan BK', 'message' => 'Apakah Anda yakin ingin menghapus rekam layanan BK :name ini? Tindakan ini tidak dapat dibatalkan.'])

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Layanan Bimbingan & Konseling</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Layanan bimbingan perkembangan pribadi, sosial, belajar, dan karir serta penanganan kedisiplinan siswa.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.bk.create') }}"
               class="inline-flex items-center space-x-2 px-4 py-2.5 bg-[#3C50E0] hover:bg-[#3C50E0]/90 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-[#3C50E0]/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Layanan BK</span>
            </a>
            <a href="{{ route('admin.bk.assessments') }}"
               class="inline-flex items-center space-x-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-purple-600/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Asesmen & Bimbingan Karir</span>
            </a>
        </div>
    </div>


    <!-- KPI Ribbon Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Total -->
        <div class="tailadmin-card p-4 flex flex-col justify-between">
            <span class="text-[10px] font-extrabold text-[#64748B] uppercase tracking-wider">TOTAL LAYANAN</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-[#1C2434] dark:text-white">{{ number_format($totalSessions) }}</span>
                <span class="text-[10px] font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-950/40 px-2 py-0.5 rounded-full">Sesi</span>
            </div>
        </div>

        <!-- Pribadi -->
        <div class="tailadmin-card p-4 flex flex-col justify-between">
            <span class="text-[10px] font-extrabold text-blue-600 dark:text-blue-400 uppercase tracking-wider">PRIBADI</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ number_format($pribadiCount) }}</span>
                <span class="text-[10px] text-slate-400">Emosi & Diri</span>
            </div>
        </div>

        <!-- Sosial -->
        <div class="tailadmin-card p-4 flex flex-col justify-between">
            <span class="text-[10px] font-extrabold text-purple-600 dark:text-purple-400 uppercase tracking-wider">SOSIAL</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-purple-600 dark:text-purple-400">{{ number_format($sosialCount) }}</span>
                <span class="text-[10px] text-slate-400">Relasi & Teman</span>
            </div>
        </div>

        <!-- Belajar -->
        <div class="tailadmin-card p-4 flex flex-col justify-between">
            <span class="text-[10px] font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-wider">BELAJAR</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ number_format($belajarCount) }}</span>
                <span class="text-[10px] text-slate-400">Motivasi & Hasil</span>
            </div>
        </div>

        <!-- Karier -->
        <div class="tailadmin-card p-4 flex flex-col justify-between">
            <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">KARIER</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($karierCount) }}</span>
                <span class="text-[10px] text-slate-400">Minat & Kuliah</span>
            </div>
        </div>

        <!-- Kedisiplinan -->
        <div class="tailadmin-card p-4 flex flex-col justify-between">
            <span class="text-[10px] font-extrabold text-rose-600 dark:text-rose-400 uppercase tracking-wider">KEDISIPLINAN</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ number_format($kedisiplinanCount) }}</span>
                <span class="text-[10px] text-slate-400">Perilaku</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Panel -->
    <div class="tailadmin-card p-5">
        <form method="GET" action="{{ route('admin.bk.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Keyword -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1">Cari Siswa / Topik</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        @input.debounce.400ms="$el.closest('form').submit()"
                        x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                        placeholder="Cari nama, NISN, topik..." class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 pr-8 text-[#1C2434] dark:text-white">
                    @if(request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                    @endif
                </div>
            </div>

            <!-- Filter Kategori -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1">Kategori BK</label>
                <select name="category" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                    <option value="">Semua Kategori</option>
                    <option value="pribadi" {{ request('category') === 'pribadi' ? 'selected' : '' }}>Pribadi & Emosi</option>
                    <option value="sosial" {{ request('category') === 'sosial' ? 'selected' : '' }}>Sosial & Pertemanan</option>
                    <option value="belajar" {{ request('category') === 'belajar' ? 'selected' : '' }}>Masalah Belajar</option>
                    <option value="karier" {{ request('category') === 'karier' ? 'selected' : '' }}>Bimbingan Karier & Perguruan Tinggi</option>
                    <option value="kedisiplinan" {{ request('category') === 'kedisiplinan' ? 'selected' : '' }}>Kedisiplinan & Perilaku</option>
                </select>
            </div>

            <!-- Filter Jenis Layanan -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1">Jenis Layanan</label>
                <select name="service_type" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                    <option value="">Semua Jenis</option>
                    <option value="individu" {{ request('service_type') === 'individu' ? 'selected' : '' }}>Konseling Individu</option>
                    <option value="kelompok" {{ request('service_type') === 'kelompok' ? 'selected' : '' }}>Bimbingan Kelompok</option>
                    <option value="klasikal" {{ request('service_type') === 'klasikal' ? 'selected' : '' }}>Bimbingan Klasikal</option>
                </select>
            </div>

            <x-major-class-select :selected-major="request('major_id')" :selected-class="request('class_id')" :is-filter="true" layout="inline" major-label="Jurusan" class-label="Kelas" select-class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white" label-class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1" :on-major-change="'$el.closest(\'form\').submit()'" :on-class-change="'$el.closest(\'form\').submit()'" />
        </form>
    </div>

    <!-- Data Table -->
    <div class="tailadmin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider font-bold border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                    <tr>
                        <th class="px-6 py-4">Siswa & Kelas</th>
                        <th class="px-6 py-4">Kategori & Topik BK</th>
                        <th class="px-6 py-4">Jenis Layanan</th>
                        <th class="px-6 py-4">Tanggal & Tempat</th>
                        <th class="px-6 py-4">Guru BK / Konselor</th>
                        <th class="px-6 py-4">Status Intervensi</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] text-[#1C2434] dark:text-[#DEE4EE]">
                    @forelse($counselings as $item)
                        <tr class="hover:bg-[#F1F5F9]/60 dark:hover:bg-[#1A222C]/50 transition-colors">
                            <!-- Siswa & Kelas -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-600 font-extrabold flex items-center justify-center text-xs border border-indigo-500/20 shrink-0">
                                        {{ strtoupper(substr($item->student->name ?? 'S', 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.bk.show', $item->id) }}" class="font-bold text-[#1C2434] dark:text-white hover:text-[#3C50E0] hover:underline">
                                            {{ $item->student->name ?? '-' }}
                                        </a>
                                        <div class="flex items-center space-x-2 text-[10px] text-slate-400 mt-0.5">
                                            <span>NISN: {{ $item->student->nisn ?? '-' }}</span>
                                            <span>•</span>
                                            <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $item->student->class->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kategori & Topik -->
                            <td class="px-6 py-4">
                                <div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold 
                                        {{ $item->category === 'pribadi' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200' : '' }}
                                        {{ $item->category === 'sosial' ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-200' : '' }}
                                        {{ $item->category === 'belajar' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200' : '' }}
                                        {{ $item->category === 'karier' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200' : '' }}
                                        {{ $item->category === 'kedisiplinan' ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200' : '' }}">
                                        {{ $item->category_label }}
                                    </span>
                                    <p class="font-bold text-xs text-[#1C2434] dark:text-white mt-1 max-w-xs truncate" title="{{ $item->title }}">{{ $item->title }}</p>
                                    @if($item->is_confidential)
                                        <span class="text-[9px] text-rose-500 font-bold tracking-wider uppercase inline-flex items-center space-x-1 mt-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            <span>Kerahasiaan Tinggi</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Jenis Layanan -->
                            <td class="px-6 py-4 font-semibold">
                                {{ $item->service_type_label }}
                            </td>

                            <!-- Tanggal & Tempat -->
                            <td class="px-6 py-4">
                                <p class="font-bold text-xs font-mono">{{ $item->date ? $item->date->format('d M Y') : '-' }}</p>
                                <p class="text-[10px] text-slate-400">{{ $item->place ?? 'Ruang BK' }}</p>
                            </td>

                            <!-- Guru BK -->
                            <td class="px-6 py-4">
                                <p class="font-bold text-xs">{{ $item->counselor->name ?? '-' }}</p>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold
                                    {{ $item->status === 'completed' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200' : '' }}
                                    {{ $item->status === 'in_progress' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200' : '' }}
                                    {{ $item->status === 'scheduled' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200' : '' }}
                                    {{ $item->status === 'referred' ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-200' : '' }}">
                                    {{ $item->status_badge }}
                                </span>
                            </td>

                            <!-- Action -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.bk.show', $item->id) }}" class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-300 font-bold rounded-lg text-xs hover:bg-indigo-600 hover:text-white transition">
                                        Detail & Rekam
                                    </a>
                                    <button type="button" @click="confirmDelete('{{ route('admin.bk.destroy', $item->id) }}', '{{ addslashes($item->student->name ?? 'Siswa') }} ({{ addslashes($item->title) }})')" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                                Belum ada data layanan bimbingan & konseling (BK).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($counselings->hasPages())
            <div class="p-4 border-t border-[#E2E8F0] dark:border-[#2E3A47]">
                {{ $counselings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
