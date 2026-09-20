@extends('layouts.admin')

@section('title', 'Daftar Alumni')
@section('page_title', 'Direktori Alumni Sekolah')

@section('content')
<div class="space-y-6" x-data="{
    showGraduateModal: false,
    showRevertModal: false,
    revertStudent: null,
    revertFormAction: '',
    confirmRevert(id, name) {
        this.revertStudent = { id: id, name: name };
        this.revertFormAction = '{{ url('admin/alumni') }}/' + id + '/revert-status';
        this.showRevertModal = true;
    }
}">

    <!-- Top Action Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">Direktori Kelulusan</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs font-semibold text-slate-500">{{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Daftar &amp; Arsip Alumni</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Pencatatan data siswa yang telah menyelesaikan pendidikan (lulus) dan riwayat angkatan.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Navigasi Cepat ke Siswa Aktif -->
            <a href="{{ route('admin.students.index') }}" 
               class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Data Siswa Aktif</span>
            </a>

            <!-- Export Excel -->
            <a href="{{ route('admin.alumni.export', request()->query()) }}" 
               class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-600 hover:text-white text-emerald-700 dark:text-emerald-300 text-xs font-bold rounded-xl transition-all border border-emerald-200 dark:border-emerald-800 shadow-2xs" title="Export Alumni ke Excel (.xlsx)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Alumni</span>
            </a>

            <!-- Tombol Luluskan Kelas (Kelulusan) -->
            <button type="button" @click="showGraduateModal = true"
                    class="inline-flex items-center space-x-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:brightness-110 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-600/30 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>+ Luluskan Siswa / Rombel</span>
            </button>
        </div>
    </div>

    <!-- TailAdmin KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="tailadmin-card p-5 sm:p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Total Alumni Terdata</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white mt-1 tracking-tight">{{ number_format($totalAlumni) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-200 dark:border-indigo-800 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </div>
        </div>

        <div class="tailadmin-card p-5 sm:p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Lulusan Tahun Ini ({{ date('Y') }})</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white mt-1 tracking-tight">{{ number_format($thisYearGraduates) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-200 dark:border-blue-800 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="tailadmin-card p-5 sm:p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Alumni Laki-Laki</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white mt-1 tracking-tight">{{ number_format($maleCount) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-200 dark:border-teal-800 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>

        <div class="tailadmin-card p-5 sm:p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Alumni Perempuan</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white mt-1 tracking-tight">{{ number_format($femaleCount) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-200 dark:border-rose-800 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filter & Search Panel -->
    <div class="tailadmin-card p-5 sm:p-6">
        <form method="GET" action="{{ route('admin.alumni.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Search Keyword -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Cari Alumni</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, NISN, NIK, email..." 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl pl-10 pr-8 py-2.5 focus:outline-none focus:border-[#3C50E0]">
                    <svg class="w-4 h-4 text-[#64748B] dark:text-[#8A99AD] absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if(request('search'))
                        <a href="{{ route('admin.alumni.index', request()->except('search')) }}" class="absolute right-3 top-2.5 text-[#64748B] hover:text-rose-500 transition font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
            </div>

            <!-- Filter Tahun Kelulusan -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Tahun Kelulusan / Angkatan</label>
                <select name="graduation_year" onchange="this.form.submit()" 
                        class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-[#3C50E0]">
                    <option value="">Semua Angkatan Kelulusan</option>
                    @for($y = (int)date('Y'); $y >= 2010; $y--)
                        <option value="{{ $y }}" {{ request('graduation_year') == (string)$y ? 'selected' : '' }}>Lulusan Tahun {{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Filter Jenis Kelamin -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                <select name="gender" onchange="this.form.submit()" 
                        class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-[#3C50E0]">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="tailadmin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#E2E8F0] dark:border-[#2E3A47] bg-slate-50/75 dark:bg-[#1A222C]/50 text-[11px] font-extrabold uppercase tracking-wider text-[#64748B] dark:text-[#8A99AD]">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama Siswa / Alumni</th>
                        <th class="py-3.5 px-4">NISN / NIS</th>
                        <th class="py-3.5 px-4 text-center">Tahun Lulus</th>
                        <th class="py-3.5 px-4">Kontak Siswa / Orang Tua</th>
                        <th class="py-3.5 px-4">Catatan Kelulusan</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] text-xs">
                    @forelse($alumniList as $idx => $student)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-[#1A222C]/80 transition">
                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                            {{ $alumniList->firstItem() + $idx }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black shrink-0 border border-indigo-200/80 text-xs overflow-hidden">
                                    @if($student->photo)
                                        <img src="{{ asset('storage/' . $student->photo) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($student->name, 0, 2)) }}
                                    @endif
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-900 dark:text-white block hover:text-indigo-600">{{ $student->name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-mono text-xs text-slate-700 dark:text-slate-300 block font-semibold">{{ $student->nisn ?: '-' }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">NIS: {{ $student->nis ?: '-' }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-black bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800">
                                Angkatan {{ $student->graduation_year ?: ($student->entry_year ?: '-') }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ $student->phone ?: ($student->parent_phone ?: '-') }}</p>
                            <p class="text-[10px] text-slate-400 truncate max-w-[180px]">{{ $student->email ?: ($student->user?->email ?: '-') }}</p>
                        </td>
                        <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                            {{ $student->alumni_notes ?: 'Alumni resmi terdaftar' }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('admin.students.show', $student->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-800 dark:text-slate-300" title="Lihat Profil">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <button type="button" @click="confirmRevert('{{ $student->id }}', '{{ addslashes($student->name) }}')" class="px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-[10px] font-bold border border-amber-200 transition" title="Batalkan status alumni & kembalikan ke siswa aktif">
                                    Kembalikan Aktif
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-600 dark:text-slate-400">Belum ada siswa yang berstatus Alumni.</p>
                                <p class="text-xs text-slate-400">Gunakan tombol "+ Luluskan Siswa / Rombel" untuk meluluskan satu kelas secara otomatis.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alumniList->hasPages())
        <div class="p-4 border-t border-[#E2E8F0] dark:border-[#2E3A47]">
            {{ $alumniList->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Luluskan Kelas (Kelulusan Rombel) -->
    <div x-show="showGraduateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" @click="showGraduateModal = false"></div>

            <div class="relative inline-block w-full max-w-lg p-6 sm:p-8 my-8 text-left align-middle transition-all transform bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] shadow-2xl rounded-3xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Proses Kelulusan Kelas (Alumni)</h3>
                            <p class="text-xs text-slate-400">Pindahkan seluruh siswa dalam satu rombel kelas menjadi Alumni.</p>
                        </div>
                    </div>
                    <button type="button" @click="showGraduateModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admin.alumni.graduate-class') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Pilih Kelas yang Lulus <span class="text-rose-500">*</span></label>
                        <select name="class_id" required class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl px-3.5 py-2.5 font-semibold text-slate-800 dark:text-white focus:outline-none focus:border-indigo-600">
                            <option value="">-- Pilih Rombongan Belajar --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->level ? 'Tingkat ' . $c->level : '' }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Seluruh siswa aktif di kelas ini akan dipindahkan statusnya menjadi Alumni.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tahun Kelulusan / Angkatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="graduation_year" value="{{ date('Y') }}" required class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl px-3.5 py-2.5 font-bold text-slate-800 dark:text-white focus:outline-none focus:border-indigo-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Catatan / Angkatan (Opsional)</label>
                        <input type="text" name="alumni_notes" placeholder="Contoh: Angkatan 15 / Lulusan Tahun Pelajaran {{ date('Y')-1 }}/{{ date('Y') }}" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl px-3.5 py-2.5 text-slate-800 dark:text-white focus:outline-none focus:border-indigo-600">
                    </div>

                    <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Siswa yang diluluskan akan otomatis dilepas dari kelas aktif saat ini sehingga kelas tersebut siap menerima siswa baru di tahun ajaran berikutnya.</span>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="showGraduateModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/30">Proses Kelulusan Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Kembalikan ke Aktif -->
    <div x-show="showRevertModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" @click="showRevertModal = false"></div>

            <div class="relative inline-block w-full max-w-md p-6 my-8 text-left align-middle bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] shadow-2xl rounded-3xl">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-2">Kembalikan Status Siswa Aktif</h3>
                <p class="text-xs text-slate-500 mb-4">
                    Apakah Anda yakin ingin mengembalikan status <strong class="text-slate-900 dark:text-white" x-text="revertStudent?.name"></strong> menjadi Siswa Aktif kembali?
                </p>

                <form :action="revertFormAction" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tempatkan ke Kelas (Opsional)</label>
                        <select name="class_id" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl px-3.5 py-2.5 text-slate-800 dark:text-white">
                            <option value="">-- Jangan tempatkan ke kelas dulu --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="showRevertModal = false" class="px-4 py-2 rounded-xl border text-slate-600 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md shadow-amber-600/30">Ya, Kembalikan ke Aktif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
