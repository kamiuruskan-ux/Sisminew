@extends('layouts.admin')

@section('title', 'Cetak Kartu Siswa')
@section('page_title', 'Cetak Kartu Siswa & QR Presensi')

@section('content')
<div class="space-y-8 w-full pb-28" x-data="{
    selectAll: false,
    selectedStudents: [],
    toggleAll() {
        if (this.selectAll) {
            this.selectedStudents = Array.from(document.querySelectorAll('.student-checkbox')).map(el => el.value);
        } else {
            this.selectedStudents = [];
        }
    }
}">
    <!-- Hero Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 p-6 sm:p-8 lg:p-10 text-white shadow-2xl shadow-indigo-950/20 border border-slate-800/80">
        <!-- Ambient Glowing Lights -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-24 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/10 text-xs font-extrabold text-indigo-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Cetak Kartu Pelajar PVC / Presensi Digital QR</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Cetak Kartu Tanda Siswa (KTS)
                </h1>
                <p class="text-xs sm:text-sm text-indigo-100/80 leading-relaxed font-medium">
                    Cetak kartu pelajar resmi lengkap dengan Kode QR Presensi. Bebas pilih cetak per siswa, cetak pilihan massal, atau cetak 1 kelas penuh sekaligus.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('admin.student-cards.settings') }}" 
                   class="px-5 py-3 rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold text-xs uppercase tracking-wider transition-all duration-200 shadow-lg shadow-purple-600/30 flex items-center space-x-2 border border-purple-400/30">
                    <svg class="w-4 h-4 text-purple-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Pengaturan & Desain Kartu</span>
                </a>

                <div class="px-4 py-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/10 text-xs font-extrabold text-indigo-200 flex items-center space-x-3 shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-emerald-400/20 text-emerald-300 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-indigo-200/70 uppercase tracking-wider">Format Standar</p>
                        <p class="text-white font-mono font-extrabold text-xs">PVC 85.6mm x 54mm</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-5">
        <div class="premium-card p-5 relative overflow-hidden group hover:border-indigo-300 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Siswa</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format(\App\Models\Student::count()) }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">Terdaftar di sistem</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold border border-indigo-100 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
            </div>
        </div>

        <div class="premium-card p-5 relative overflow-hidden group hover:border-emerald-300 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Kelas</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">{{ number_format($classes->count()) }}</p>
                    <p class="text-[10px] text-emerald-600/80 font-semibold">Siap cetak massal</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold border border-emerald-100 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>
        </div>

        <div class="premium-card p-5 relative overflow-hidden group hover:border-blue-300 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Siswa Berfoto</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-blue-600 tracking-tight">{{ number_format(\App\Models\Student::whereNotNull('photo')->count()) }}</p>
                    <p class="text-[10px] text-blue-600/80 font-semibold">Pas foto lengkap</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold border border-blue-100 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>

        <div class="premium-card p-5 relative overflow-hidden group hover:border-cyan-300 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Presensi Kode QR</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-cyan-600 tracking-tight">{{ number_format(\App\Models\Student::whereNotNull('qr_code')->count()) }}</p>
                    <p class="text-[10px] text-cyan-600/80 font-semibold">QR scanner active</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-extrabold border border-cyan-100 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Banner: Cetak Massal Per Kelas -->
    <div class="premium-card p-6 shadow-sm border-slate-200 space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-extrabold shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base">Cetak Instan Massal Per Kelas</h3>
                    <p class="text-xs text-slate-500 font-medium">Klik salah satu kelas di bawah ini untuk mencetak kartu seluruh siswa kelas tersebut secara langsung.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 pt-1">
            @foreach($classes as $c)
                <a href="{{ route('admin.student-cards.print', ['class_id' => $c->id]) }}" target="_blank"
                   class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:border-indigo-500 hover:bg-indigo-50/40 hover:shadow-md transition-all group flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-xs text-slate-900 group-hover:text-indigo-600 transition-colors">Kelas {{ $c->name }}</span>
                        <div class="w-6 h-6 rounded-lg bg-white group-hover:bg-indigo-600 group-hover:text-white text-slate-400 flex items-center justify-center transition-colors shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-500 font-extrabold uppercase tracking-wider">{{ $c->students_count }} Siswa</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Student Table Card with Search, Checkbox Filters & Proportional Action Button -->
    <div class="premium-card overflow-hidden shadow-lg border-slate-200">
        <div class="px-6 sm:px-8 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base tracking-tight">Daftar Siswa & Pemilihan Manual</h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Centang checkbox untuk memilih kombinasi siswa yang ingin dicetak secara bersamaan</p>
            </div>

            <!-- Toolbar Controls: Search, Class Select, & Proportional Bulk Print Button -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Filter & Search Form -->
                <form method="GET" action="{{ route('admin.student-cards.index') }}" class="flex flex-wrap items-center gap-2.5">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               @input.debounce.400ms="$el.closest('form').submit()"
                               x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                               placeholder="Cari nama / NISN..."
                               class="w-48 sm:w-56 pl-9 pr-8 py-2 bg-white border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all shadow-2xs">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        @if(request('search'))
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                        @endif
                    </div>

                    <x-major-class-select :selected-major="request('major_id')" :selected-class="request('class_id')" :is-filter="true" layout="inline" major-label="" class-label="" select-class="px-3 py-2 bg-white border border-slate-200/90 rounded-xl text-xs font-bold text-slate-700 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 shadow-2xs transition-all cursor-pointer" :on-major-change="'$el.closest(\'form\').submit()'" :on-class-change="'$el.closest(\'form\').submit()'" />
                </form>

                <!-- Proportional Header Action Button for Selected Printing -->
                <form method="POST" action="{{ route('admin.student-cards.print-bulk') }}" target="_blank" class="inline-block">
                    @csrf
                    <template x-for="id in selectedStudents" :key="id">
                        <input type="hidden" name="student_ids[]" :value="id">
                    </template>
                    <button type="submit" 
                            :disabled="selectedStudents.length === 0"
                            class="px-4 py-2 rounded-xl transition-all duration-200 font-extrabold text-xs flex items-center space-x-2 shadow-sm
                                   disabled:opacity-40 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 disabled:border-slate-200 border
                                   bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600 shadow-emerald-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Pilihan</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold" :class="selectedStudents.length > 0 ? 'bg-emerald-800 text-white' : 'bg-slate-200 text-slate-600'" x-text="selectedStudents.length">0</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4 w-12 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="w-4 h-4 text-indigo-600 rounded cursor-pointer">
                        </th>
                        <th class="px-6 py-4">Foto & Nama Siswa</th>
                        <th class="px-6 py-4">NISN</th>
                        <th class="px-6 py-4">Kelas & Jurusan</th>
                        <th class="px-6 py-4">Presensi Kode QR</th>
                        <th class="px-6 py-4 text-right">Aksi Single</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                    @forelse($students as $student)
                        <tr class="hover:bg-slate-50/60 transition-colors group" :class="selectedStudents.includes('{{ $student->id }}') ? 'bg-indigo-50/40' : ''">
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" value="{{ $student->id }}" x-model="selectedStudents" class="student-checkbox w-4 h-4 text-indigo-600 rounded cursor-pointer">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 overflow-hidden shrink-0 border border-indigo-100 shadow-2xs flex items-center justify-center font-extrabold text-sm">
                                        @if($student->photo_url)
                                            <img src="{{ $student->photo_url }}" alt="Photo" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 group-hover:text-indigo-600 text-sm transition-colors leading-snug">
                                            {{ $student->user->name ?? '-' }}
                                        </p>
                                        <p class="text-[11px] text-slate-400 font-medium">{{ $student->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-slate-800">
                                <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-slate-700 font-extrabold border border-slate-200/80">{{ $student->nisn }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                                    Kelas {{ $student->class?->name ?? '-' }}
                                </span>
                                @if($student->major)
                                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $student->major->name }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center space-x-1.5 text-[10px] font-mono font-extrabold bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg border border-emerald-200">
                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <span>{{ $student->qr_code }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.student-cards.print', ['student_id' => $student->id]) }}" target="_blank"
                                   class="px-3.5 py-2 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white font-extrabold text-xs transition-all border border-indigo-200/80 shadow-2xs inline-flex items-center space-x-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Cetak Kartu</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto border border-slate-200 shadow-sm">
                                        <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    </div>
                                    <p class="font-extrabold text-sm text-slate-800">Tidak ada data siswa</p>
                                    <p class="text-xs text-slate-400">Siswa tidak ditemukan untuk kriteria filter ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-6 sm:px-8 py-5 border-t border-slate-100 bg-slate-50/50">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    <!-- Floating Action Bar When Checkbox Items Selected -->
    <div x-show="selectedStudents.length > 0" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-slate-950/95 backdrop-blur-md text-white px-6 py-3.5 rounded-2xl shadow-2xl border border-slate-800 flex items-center space-x-6"
         x-cloak>
        <div class="flex items-center space-x-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs font-semibold text-slate-300"><strong class="text-white font-extrabold text-sm" x-text="selectedStudents.length">0</strong> siswa dipilih</span>
        </div>
        
        <div class="flex items-center space-x-3">
            <button type="button" @click="selectedStudents = []; selectAll = false" class="text-xs font-bold text-slate-400 hover:text-white px-3 py-1.5 transition">
                Batal
            </button>

            <form method="POST" action="{{ route('admin.student-cards.print-bulk') }}" target="_blank">
                @csrf
                <template x-for="id in selectedStudents" :key="id">
                    <input type="hidden" name="student_ids[]" :value="id">
                </template>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-extrabold text-xs uppercase tracking-wider transition-all shadow-lg flex items-center space-x-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Kartu Siswa Pilihan</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
