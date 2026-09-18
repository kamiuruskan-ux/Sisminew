@extends('layouts.admin')

@section('title', 'Pelanggaran Siswa & Kedisiplinan BK')

@section('content')
<div class="space-y-6" x-data="{ 
    createViolationModal: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(actionUrl, itemName) {
        this.deleteTarget = { name: itemName };
        this.deleteFormAction = actionUrl;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Catatan Pelanggaran', 'message' => 'Apakah Anda yakin ingin menghapus catatan pelanggaran :name ini? Poin pelanggaran siswa terkait akan berkurang.'])
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#1A222C] p-6 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] shadow-xs">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center font-bold text-xl border border-rose-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-[#1C2434] dark:text-white">Pelanggaran Siswa & Kedisiplinan BK</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pencatatan pelanggaran tata tertib, sistem akumulasi poin, dan penanganan SP siswa</p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.bk.violations.categories') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-extrabold text-xs rounded-xl border border-slate-200 dark:border-[#2E3A47] transition flex items-center space-x-2 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Master Kategori & Poin</span>
            </a>

            <button type="button" @click="createViolationModal = true" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-extrabold text-xs rounded-xl shadow-md shadow-rose-600/20 transition flex items-center space-x-2 cursor-pointer">
                <svg class="w-4 h-4 text-rose-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Pelanggaran</span>
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

    <!-- Stat Cards Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Kejadian</p>
                <h3 class="text-2xl font-black text-[#1C2434] dark:text-white mt-1">{{ number_format($totalViolations) }}</h3>
                <p class="text-[10px] text-slate-500 font-medium mt-0.5">Catatan pelanggaran terdaftar</p>
            </div>
            <div class="w-12 h-12 bg-indigo-500/10 text-indigo-500 rounded-2xl flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-amber-500">Akumulasi Poin</p>
                <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($totalPoints) }}</h3>
                <p class="text-[10px] text-slate-500 font-medium mt-0.5">Total akumulasi seluruh siswa</p>
            </div>
            <div class="w-12 h-12 bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-rose-500">Status Surat SP</p>
                <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $sp1Count + $sp2Count + $sp3Count }} Siswa</h3>
                <p class="text-[10px] text-slate-500 font-medium mt-0.5">SP-1: {{ $sp1Count }} | SP-2: {{ $sp2Count }} | SP-3: {{ $sp3Count }}</p>
            </div>
            <div class="w-12 h-12 bg-rose-500/10 text-rose-500 rounded-2xl flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-500">Selesai / Pembinaan</p>
                <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format(\App\Models\BkStudentViolation::where('status', 'resolved')->count()) }}</h3>
                <p class="text-[10px] text-slate-500 font-medium mt-0.5">Siswa telah dibina & tuntas</p>
            </div>
            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-500 rounded-2xl flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-[#1A222C] p-6 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] shadow-xs">
        <form method="GET" action="{{ route('admin.bk.violations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 items-end">
            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Kata Kunci Pencarian</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           placeholder="Cari nama siswa, NISN, atau judul..." 
                           class="w-full h-10 px-3.5 pr-8 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500 dark:text-white outline-none transition shadow-2xs">
                    @if(request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                    @endif
                </div>
            </div>

            <x-major-class-select :selected-major="request('major_id')" :selected-class="request('class_id')" :is-filter="true" layout="inline" major-label="Jurusan" class-label="Kelas Rombel" select-class="w-full h-10 px-3.5 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 outline-none transition shadow-2xs" label-class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" :on-major-change="'$el.closest(\'form\').submit()'" :on-class-change="'$el.closest(\'form\').submit()'" />

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Status Sanksi</label>
                <select name="status" @change="$el.closest('form').submit()" class="w-full h-10 px-3.5 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 outline-none transition shadow-2xs">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending / Menunggu</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Dipproses BK</option>
                    <option value="sp1" {{ request('status') == 'sp1' ? 'selected' : '' }}>SP 1 (Peringatan 1)</option>
                    <option value="sp2" {{ request('status') == 'sp2' ? 'selected' : '' }}>SP 2 (Peringatan 2)</option>
                    <option value="sp3" {{ request('status') == 'sp3' ? 'selected' : '' }}>SP 3 (Peringatan 3)</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved (Selesai)</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table of Student Violations -->
    <div class="bg-white dark:bg-[#1A222C] rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] overflow-hidden shadow-xs">
        <div class="p-6 border-b border-slate-200 dark:border-[#2E3A47] flex items-center justify-between">
            <h3 class="font-extrabold text-sm text-[#1C2434] dark:text-white">Daftar Catatan Pelanggaran Siswa</h3>
            <span class="text-xs text-slate-400">Total {{ $violations->total() }} Catatan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#24303F] text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                        <th class="py-4 px-6">Tanggal</th>
                        <th class="py-4 px-6">Siswa &amp; Kelas</th>
                        <th class="py-4 px-6">Pelanggaran &amp; Kategori</th>
                        <th class="py-4 px-6 text-center">Poin</th>
                        <th class="py-4 px-6">Status Sanksi</th>
                        <th class="py-4 px-6">Petugas / BK</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-[#2E3A47] text-xs">
                    @forelse($violations as $item)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-[#24303F]/50 transition">
                            <td class="py-4 px-6 whitespace-nowrap font-mono font-semibold text-slate-600 dark:text-slate-300">
                                {{ \Carbon\Carbon::parse($item->violation_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-extrabold text-[#1C2434] dark:text-white">{{ $item->student->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">NISN: {{ $item->student->nisn ?? '-' }} &bull; {{ $item->student->class->name ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 max-w-xs">
                                <div class="font-extrabold text-slate-800 dark:text-slate-200 leading-tight">{{ $item->title }}</div>
                                @if($item->category)
                                    <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-[#24303F] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $item->category->name }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                    +{{ $item->points }} Poin
                                </span>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($item->status == 'pending')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-amber-500/10 text-amber-600 border border-amber-500/20">Pending</span>
                                @elseif($item->status == 'processed')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-blue-500/10 text-blue-600 border border-blue-500/20">Diproses BK</span>
                                @elseif($item->status == 'sp1')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-orange-500/15 text-orange-600 border border-orange-500/30">Surat SP-1</span>
                                @elseif($item->status == 'sp2')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-rose-500/15 text-rose-600 border border-rose-500/30">Surat SP-2</span>
                                @elseif($item->status == 'sp3')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-red-600 text-white shadow-xs">Surat SP-3</span>
                                @elseif($item->status == 'resolved')
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">Selesai / Tuntas</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">{{ strtoupper($item->status) }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap text-slate-600 dark:text-slate-400 font-medium">
                                {{ $item->counselor->name ?? 'Admin BK' }}
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('admin.bk.violations.show', $item->id) }}" class="p-2 inline-flex bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 text-slate-700 dark:text-slate-300 rounded-lg transition" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.bk.violations.edit', $item->id) }}" class="p-2 inline-flex bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button type="button" @click="confirmDelete('{{ route('admin.bk.violations.destroy', $item->id) }}', 'pelanggaran {{ addslashes($item->student->name ?? 'Siswa') }} ({{ addslashes($item->category->name ?? 'Pelanggaran') }})')" class="p-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 rounded-lg transition cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 font-medium">
                                Belum ada catatan pelanggaran siswa yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($violations->hasPages())
            <div class="p-6 border-t border-slate-200 dark:border-[#2E3A47]">
                {{ $violations->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Catat Pelanggaran Baru (Proportional Styled) -->
    <div x-show="createViolationModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="bg-white dark:bg-[#1A222C] rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-[#2E3A47] space-y-5 my-8" @click.outside="createViolationModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#2E3A47] pb-4">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    Catat Pelanggaran Siswa
                </h3>
                <button type="button" @click="createViolationModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-[#24303F] text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-sm transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <form action="{{ route('admin.bk.violations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ 
                selectedCatId: '',
                categoriesList: {{ json_encode($categories) }},
                autoPoints: 0,
                updatePoints() {
                    let cat = this.categoriesList.find(c => c.id == this.selectedCatId);
                    if (cat) { this.autoPoints = cat.points; }
                }
            }">
                @csrf

                <!-- Searchable Student Select -->
                <div>
                    <x-student-select-search 
                        :students="$students" 
                        name="student_id" 
                        required="true"
                        label="Pilih Siswa"
                        accent-color="rose"
                    />
                </div>

                <!-- Kategori & Judul Pelanggaran (2 Cols) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Kategori Pelanggaran (Opsional)</label>
                        <select name="violation_category_id" x-model="selectedCatId" @change="updatePoints()" class="w-full h-10 px-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 outline-none transition shadow-2xs">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->points }} Poin)</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Judul Pelanggaran <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required class="w-full h-10 px-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 outline-none transition shadow-2xs" placeholder="Misal: Terlambat Masuk Sekolah">
                    </div>
                </div>

                <!-- Tanggal, Poin, & Status Sanksi (3 Cols) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Kejadian <span class="text-rose-500">*</span></label>
                        <input type="date" name="violation_date" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 outline-none transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Bobot Poin <span class="text-rose-500">*</span></label>
                        <input type="number" name="points" :value="autoPoints" min="0" required class="w-full h-10 px-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 outline-none transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Status Sanksi <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full h-10 px-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 outline-none transition shadow-2xs">
                            <option value="pending" selected>Pending</option>
                            <option value="processed">Diproses BK</option>
                            <option value="sp1">Surat SP-1</option>
                            <option value="sp2">Surat SP-2</option>
                            <option value="sp3">Surat SP-3</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                </div>

                <!-- Tindakan / Sanksi Disepakati -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tindakan / Sanksi Disepakati (Opsional)</label>
                    <input type="text" name="penalty" class="w-full h-10 px-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 outline-none transition shadow-2xs" placeholder="Misal: Pembersihan perpustakaan selama 3 hari">
                </div>

                <!-- Deskripsi Kronologi -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi Kronologi (Opsional)</label>
                    <textarea name="notes" rows="2.5" class="w-full p-3.5 border border-slate-200 dark:border-[#2E3A47] bg-slate-50 dark:bg-[#24303F] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 outline-none transition shadow-2xs" placeholder="Tuliskan kronologi singkat pelanggaran..."></textarea>
                </div>

                <!-- Lampiran Bukti File/Foto -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Lampiran Bukti File/Foto (Opsional)</label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.docx" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 dark:file:bg-rose-950/60 dark:file:text-rose-300">
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-[#2E3A47]">
                    <button type="button" @click="createViolationModal = false" class="px-4 py-2.5 rounded-xl text-xs font-extrabold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 shadow-md shadow-rose-600/20 transition">
                        Simpan Catatan Pelanggaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
