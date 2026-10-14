@extends('layouts.admin')

@section('title', 'Laporan Rekapitulasi Presensi & Briefing')
@section('page_title', 'Rekap Presensi & Laporan')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: '{{ $tab }}',
    briefingSubTab: '{{ $briefingTab }}',
    searchQuery: '{{ $search }}'
}">
    <!-- ======================================================== -->
    <!-- TOP NAVIGATION TABS (KEHADIRAN UMUM / LAPORAN BRIEFING / SANTRI) -->
    <!-- ======================================================== -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-200 dark:border-slate-800">
        <!-- Tab 1: Kehadiran Umum -->
        <button type="button" @click="activeTab = 'general'"
                :class="activeTab === 'general' ? 'bg-white dark:bg-[#1A222C] text-indigo-600 dark:text-indigo-400 border-indigo-400 dark:border-indigo-600 shadow-sm font-black' : 'bg-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border-transparent font-bold'"
                class="px-5 py-2.5 rounded-2xl border text-xs flex items-center gap-2 transition cursor-pointer whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            <span class="tracking-wider uppercase">KEHADIRAN UMUM</span>
        </button>

        <!-- Tab 2: Laporan Briefing -->
        <button type="button" @click="activeTab = 'briefing'"
                :class="activeTab === 'briefing' ? 'bg-white dark:bg-[#1A222C] text-indigo-600 dark:text-indigo-400 border-indigo-400 dark:border-indigo-600 shadow-sm font-black' : 'bg-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white border-transparent font-bold'"
                class="px-5 py-2.5 rounded-2xl border text-xs flex items-center gap-2 transition cursor-pointer whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span class="tracking-wider uppercase">LAPORAN BRIEFING</span>
        </button>

        <!-- Tab 3: Rekap Nilai & Absen Santri (Halaqah) -->
        <a href="{{ route('admin.halaqah.index') }}"
           class="px-5 py-2.5 rounded-2xl border border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-xs font-bold flex items-center gap-2 transition cursor-pointer whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span class="tracking-wider uppercase">REKAP NILAI & ABSEN SANTRI</span>
        </a>
    </div>

    <!-- ======================================================== -->
    <!-- NOTIFIKASI PENDING APPROVAL DINAS LUAR (KEPALA SEKOLAH / ADMIN) -->
    <!-- ======================================================== -->
    @if(isset($pendingDinasLuar) && $pendingDinasLuar->isNotEmpty())
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border-2 border-amber-300 dark:border-amber-700/80 shadow-md">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-amber-500 text-white font-bold text-sm shadow-sm shrink-0">
                        ⚠️
                    </span>
                    <div>
                        <h4 class="text-sm font-black text-amber-950 dark:text-amber-200 uppercase tracking-tight">
                            Menunggu Persetujuan (Approval) Dinas Luar ({{ $pendingDinasLuar->count() }} Pengajuan)
                        </h4>
                        <p class="text-xs text-amber-800/90 dark:text-amber-300/80">
                            Presensi dengan mode Dinas Luar belum tercatat resmi sampai diapprove oleh Kepala Sekolah / Super Admin.
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-amber-200 dark:border-amber-800/60 bg-white/80 dark:bg-[#1A222C]/90">
                <table class="w-full text-left text-xs">
                    <thead class="bg-amber-100/60 dark:bg-amber-950/40 text-amber-900 dark:text-amber-200 font-bold uppercase text-[10px] tracking-wider border-b border-amber-200 dark:border-amber-800">
                        <tr>
                            <th class="px-4 py-2.5">Nama Guru / Pegawai</th>
                            <th class="px-3 py-2.5">Tanggal & Waktu</th>
                            <th class="px-3 py-2.5">Agenda / Keterangan Dinas</th>
                            <th class="px-3 py-2.5 text-center">Status</th>
                            <th class="px-4 py-2.5 text-right">Tindakan Kepala Sekolah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100 dark:divide-amber-900/40">
                        @foreach($pendingDinasLuar as $pending)
                            <tr class="hover:bg-amber-50/50 dark:hover:bg-amber-950/20 transition">
                                <td class="px-4 py-3 font-bold text-slate-800 dark:text-slate-100">
                                    {{ $pending->user->name ?? 'Pegawai' }}
                                    <div class="text-[10px] font-normal text-slate-500">{{ $pending->user->jabatan ?? 'Guru / Pegawai' }}</div>
                                </td>
                                <td class="px-3 py-3 font-mono text-slate-700 dark:text-slate-300">
                                    {{ \Carbon\Carbon::parse($pending->date)->translatedFormat('d M Y') }}
                                    <span class="block text-[11px] text-slate-500 font-sans">Masuk: {{ substr($pending->check_in, 0, 5) ?? '-' }} WITA</span>
                                </td>
                                <td class="px-3 py-3 text-slate-700 dark:text-slate-300">
                                    <span class="font-medium">{{ $pending->notes ?? 'Dinas Luar Sekolah' }}</span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 border border-amber-300/50">
                                        Pending Approval
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.teacher-attendances.approve-dinas', $pending->id) }}" onsubmit="return confirm('Setujui presensi dinas luar untuk {{ $pending->user->name ?? '' }}?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1 cursor-pointer">
                                                <span>✓</span> Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.teacher-attendances.reject-dinas', $pending->id) }}" onsubmit="return confirm('Tolak presensi dinas luar ini?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1 cursor-pointer">
                                                <span>✕</span> Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- TAB 1: KEHADIRAN UMUM (FOTO 1) -->
    <!-- ======================================================== -->
    <div x-show="activeTab === 'general'" x-cloak class="space-y-6">
        <!-- Title & Subtitle Header -->
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 text-[10px] font-black uppercase tracking-wider mb-2 border border-indigo-200 dark:border-indigo-800">
                <span>📊</span> PUSAT STATISTIK KEHADIRAN
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight uppercase">
                REKAPITULASI & LAPORAN BULANAN
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-wide">
                ANALISIS, CETAK, DAN EKSPOR LAPORAN KEHADIRAN DEWAN GURU SDIT AL-FAHMI.
            </p>
        </div>

        <!-- Panel Filter Utama Rekapitulasi (Photo 1) -->
        <div class="tailadmin-card p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                    <span>⚙️</span> PANEL FILTER UTAMA REKAPITULASI
                </div>
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 rounded-xl gap-1">
                    <a href="{{ route('admin.teacher-attendances.recap', array_merge(request()->query(), ['mode' => 'today', 'tab' => 'general'])) }}"
                       class="px-4 py-1.5 rounded-lg text-xs font-extrabold transition {{ $mode === 'today' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        HARI INI
                    </a>
                    <a href="{{ route('admin.teacher-attendances.recap', array_merge(request()->query(), ['mode' => 'range', 'tab' => 'general'])) }}"
                       class="px-4 py-1.5 rounded-lg text-xs font-extrabold transition {{ $mode === 'range' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        PERIODE TANGGAL
                    </a>
                    <a href="{{ route('admin.teacher-attendances.recap', array_merge(request()->query(), ['mode' => 'month', 'tab' => 'general'])) }}"
                       class="px-4 py-1.5 rounded-lg text-xs font-extrabold transition {{ $mode === 'month' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        BULANAN
                    </a>
                </div>
            </div>

            <!-- Filter Formasi Jabatan Dropdown -->
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider">FORMASI JABATAN:</span>
                <select name="jabatan" onchange="window.location.href = this.value" class="bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl px-3 py-2 font-bold focus:outline-none focus:border-indigo-600">
                    <option value="{{ route('admin.teacher-attendances.recap', array_merge(request()->query(), ['jabatan' => 'all'])) }}" {{ $filterJabatan === 'all' ? 'selected' : '' }}>
                        📁 Semua Formasi Jabatan ({{ count($jabatanList) }})
                    </option>
                    @foreach($jabatanList as $j)
                        <option value="{{ route('admin.teacher-attendances.recap', array_merge(request()->query(), ['jabatan' => $j])) }}" {{ $filterJabatan === $j ? 'selected' : '' }}>
                            {{ $j }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- 3 Stat Cards (Photo 1) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            <!-- Card 1: Hadir Hari Ini -->
            <div class="tailadmin-card p-5 relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">HADIR HARI INI</span>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $hadirHariIni }}</span>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">dari {{ $totalEmployees }} Orang</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('admin.teacher-attendances.index') }}" class="text-xs font-extrabold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                        <span>Lihat Siapa Hadir</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Card 2: Halaqah Aktif -->
            <div class="tailadmin-card p-5 relative overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">HALAQAH AKTIF</span>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900 dark:text-white font-mono">{{ $halaqahAktif }}</span>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Santri</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('admin.halaqah.index') }}" class="text-xs font-extrabold text-sky-600 dark:text-sky-400 hover:underline flex items-center gap-1">
                        <span>Monitoring Halaqah</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Card 3: Rata Kedisiplinan (Dark Style Card) -->
            <div class="p-5 rounded-2xl bg-[#0B132B] dark:bg-[#070D1F] text-white relative overflow-hidden flex flex-col justify-between shadow-lg shadow-slate-900/10">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">RATA KEDISIPLINAN</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-emerald-400 font-mono">{{ $rataKedisiplinan }}%</span>
                        <span class="text-xs font-extrabold text-slate-400 tracking-wider">IKHLAS</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800 text-[11px] text-slate-400">
                    Akumulasi ketepatan waktu hadir seluruh pegawai
                </div>
            </div>
        </div>

        <!-- Pusat Ekspor Laporan Bulanan Terjadwal (Photo 1) -->
        <div class="tailadmin-card p-5 sm:p-6 space-y-4">
            <div>
                <div class="flex items-center gap-2 text-indigo-700 dark:text-indigo-400 font-black text-xs uppercase tracking-wide">
                    <span>📄</span> PUSAT EKSPOR LAPORAN BULANAN TERJADWAL (FORMAT TANGGAL LENGKAP)
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Pilih bulan dan tahun di bawah untuk mendownload rekapitulasi data absensi & mengajar mendetail.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.teacher-attendances.export') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">FILTER BULAN:</label>
                    <select name="month" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl p-2.5 font-bold focus:outline-none focus:border-indigo-600">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">FILTER TAHUN:</label>
                    <select name="year" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl p-2.5 font-bold focus:outline-none focus:border-indigo-600">
                        @foreach(range(date('Y') - 2, date('Y') + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs font-black rounded-xl transition shadow-md shadow-indigo-600/25 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>EKSPOR ABSENSI BULANAN</span>
                    </button>
                </div>

                <div>
                    <a href="{{ route('admin.teacher-attendances.export', ['type' => 'daily', 'month' => $month, 'year' => $year]) }}" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-black rounded-xl transition shadow-md shadow-emerald-600/25 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>EKSPOR LAPORAN HARIAN</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Analitik Kehadiran & Performa Guru Bulanan (Photo 1) -->
        <div class="tailadmin-card overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-1.5 text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">
                            <span>🎓</span> ANALITIK KEHADIRAN & PERFORMA GURU BULANAN
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            Disiplin absensi terverifikasi koordinat GPS murni sekolah.
                        </p>
                    </div>
                </div>

                <!-- Search & Filter Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2 relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            🔍
                        </span>
                        <input type="text" 
                               x-model="searchQuery" 
                               @keyup.enter="window.location.href = '{{ route('admin.teacher-attendances.recap') }}?search=' + encodeURIComponent(searchQuery) + '&month={{ $month }}&year={{ $year }}&jabatan={{ $filterJabatan }}'"
                               placeholder="Cari berdasarkan nama guru, NIP, atau jabatan..."
                               class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl pl-9 pr-4 py-2.5 font-medium focus:outline-none focus:border-indigo-600">
                    </div>

                    <div>
                        <select name="jabatan" onchange="window.location.href = this.value" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl px-3 py-2.5 font-bold focus:outline-none focus:border-indigo-600">
                            <option value="{{ route('admin.teacher-attendances.recap', array_merge(request()->query(), ['jabatan' => 'all'])) }}" {{ $filterJabatan === 'all' ? 'selected' : '' }}>
                                📁 Semua Formasi Jabatan ({{ count($jabatanList) }})
                            </option>
                            @foreach($jabatanList as $j)
                                <option value="{{ route('admin.teacher-attendances.recap', array_merge(request()->query(), ['jabatan' => $j])) }}" {{ $filterJabatan === $j ? 'selected' : '' }}>
                                    {{ $j }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Recap Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-[#1A222C] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-extrabold border-b border-slate-200 dark:border-slate-800 text-[10px]">
                        <tr>
                            <th class="px-6 py-3.5">Nama Guru / Staff</th>
                            <th class="px-3 py-3.5 text-center bg-sky-50/50 dark:bg-sky-950/20 text-sky-700 dark:text-sky-300">Sesi Pagi</th>
                            <th class="px-3 py-3.5 text-center bg-amber-50/50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300">Sesi Siang</th>
                            <th class="px-3 py-3.5 text-center bg-indigo-50/50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-300">Sesi Sore</th>
                            <th class="px-3 py-3.5 text-center">Tepat Waktu</th>
                            <th class="px-3 py-3.5 text-center">Terlambat</th>
                            <th class="px-3 py-3.5 text-center">Sakit</th>
                            <th class="px-3 py-3.5 text-center">Izin</th>
                            <th class="px-3 py-3.5 text-center">Alpa</th>
                            <th class="px-4 py-3.5 text-center">Total Hari</th>
                            <th class="px-6 py-3.5 text-right">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                        @forelse($recapData as $row)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-[#1A222C]/50 transition-colors">
                                <td class="px-6 py-3.5 font-bold">
                                    {{ $row['teacher']->name }}
                                    <div class="text-[10px] text-slate-400 font-normal">
                                        {{ $row['teacher']->jabatan ?: ($row['teacher']->roles->first()?->name ?? 'Staf / Guru') }}
                                        @if($row['teacher']->nip) • NIP: {{ $row['teacher']->nip }} @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3.5 text-center font-mono font-bold text-sky-600 dark:text-sky-400 bg-sky-50/30 dark:bg-sky-950/10">{{ $row['morning_count'] }}</td>
                                <td class="px-3 py-3.5 text-center font-mono font-bold text-amber-600 dark:text-amber-400 bg-amber-50/30 dark:bg-amber-950/10">{{ $row['midday_count'] }}</td>
                                <td class="px-3 py-3.5 text-center font-mono font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50/30 dark:bg-indigo-950/10">{{ $row['checkout_count'] }}</td>
                                <td class="px-3 py-3.5 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $row['present'] }}</td>
                                <td class="px-3 py-3.5 text-center font-bold text-amber-600 dark:text-amber-400">{{ $row['late'] }}</td>
                                <td class="px-3 py-3.5 text-center font-bold text-blue-600 dark:text-blue-400">{{ $row['sick'] }}</td>
                                <td class="px-3 py-3.5 text-center font-bold text-purple-600 dark:text-purple-400">{{ $row['permission'] }}</td>
                                <td class="px-3 py-3.5 text-center font-bold text-rose-600 dark:text-rose-400">{{ $row['absent'] }}</td>
                                <td class="px-4 py-3.5 text-center font-extrabold">{{ $row['total'] }} Hari</td>
                                <td class="px-6 py-3.5 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <span class="font-black font-mono text-xs {{ $row['percentage'] >= 85 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            {{ $row['percentage'] }}%
                                        </span>
                                        <div class="w-14 bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                            <div class="{{ $row['percentage'] >= 85 ? 'bg-emerald-500' : 'bg-rose-500' }} h-full rounded-full" style="width: {{ $row['percentage'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-12 text-center text-slate-400">
                                    Tidak ada data kehadiran ditemukan untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- TAB 2: LAPORAN BRIEFING (FOTO 2) -->
    <!-- ======================================================== -->
    <div x-show="activeTab === 'briefing'" x-cloak class="space-y-6">
        <!-- Sub-Tabs: LOG & REKAP ABSENSI BRIEFING vs HISTORY POIN & MATERI BRIEFING -->
        <div class="flex items-center gap-2 p-1.5 bg-slate-100 dark:bg-slate-800/80 rounded-2xl w-fit">
            <button type="button" @click="briefingSubTab = 'cards'"
                    :class="briefingSubTab === 'cards' ? 'bg-indigo-600 text-white shadow-sm font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold'"
                    class="px-5 py-2 rounded-xl text-xs flex items-center gap-2 transition cursor-pointer">
                <span>💼</span>
                <span class="tracking-wider uppercase">LOG & REKAP ABSENSI BRIEFING</span>
            </button>

            <button type="button" @click="briefingSubTab = 'history'"
                    :class="briefingSubTab === 'history' ? 'bg-indigo-600 text-white shadow-sm font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold'"
                    class="px-5 py-2 rounded-xl text-xs flex items-center gap-2 transition cursor-pointer">
                <span>📄</span>
                <span class="tracking-wider uppercase">HISTORY POIN & MATERI BRIEFING</span>
            </button>
        </div>

        <!-- SUB-TAB 1: LOG & REKAP PER FORMASI JABATAN (Photo 2) -->
        <div x-show="briefingSubTab === 'cards'" class="space-y-6">
            <!-- Header Banner -->
            <div class="tailadmin-card p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">
                        <span>💼</span> LAPORAN KEHADIRAN & STATISTIK BRIEFING HARIAN
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Analisis partisipasi briefing harian terfilter berdasarkan Formasi Jabatan (Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}).
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.teacher-attendances.export-briefing', ['date' => $date, 'jabatan' => $filterJabatan]) }}"
                       class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs font-black shadow-md shadow-indigo-600/25 transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Ekspor Laporan Briefing</span>
                    </a>
                </div>
            </div>

            <!-- Cards per Formasi Jabatan Grid (Photo 2) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($briefingJabatanCards as $card)
                    <div class="tailadmin-card p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#1A222C] flex flex-col justify-between hover:border-indigo-400 dark:hover:border-indigo-600 transition shadow-xs">
                        <div>
                            <!-- Header: Jabatan & Total Staff -->
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">JABATAN</span>
                                <h3 class="text-sm font-black text-slate-900 dark:text-white mt-0.5 truncate" title="{{ $card['jabatan'] }}">
                                    {{ $card['jabatan'] }}
                                </h3>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-0.5">
                                    Total Staff: <span class="font-extrabold text-slate-700 dark:text-slate-300">{{ $card['total_staff'] }} Orang</span>
                                </p>
                            </div>

                            <!-- Metrics: Presensi & Partisipasi -->
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400 block">PRESENSI</span>
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200 font-mono">{{ $card['attended_count'] }} Kali</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">PARTISIPASI</span>
                                    <span class="text-xs font-black font-mono {{ $card['partisipasi_pct'] >= 80 ? 'text-emerald-600 dark:text-emerald-400' : ($card['partisipasi_pct'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-500') }}">
                                        {{ $card['partisipasi_pct'] }}%
                                    </span>
                                </div>
                            </div>

                            <!-- Rincian Partisipasi Scrollable Staff List -->
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                                <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400 block mb-2">
                                    RINCIAN PARTISIPASI:
                                </span>
                                <div class="max-h-36 overflow-y-auto space-y-1.5 pr-1 text-xs">
                                    @forelse($card['staff_list'] as $st)
                                        <div class="flex items-center justify-between py-1 border-b border-slate-50 dark:border-slate-800/40 last:border-none">
                                            <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[130px]" title="{{ $st['name'] }}">
                                                {{ $st['name'] }}
                                            </span>
                                            @if($st['has_attended'])
                                                <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.5 rounded border border-emerald-200 dark:border-emerald-800/60 font-mono">
                                                    Hadir ({{ $st['attended_time'] }})
                                                </span>
                                            @else
                                                <span class="text-[10px] font-bold text-rose-500 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-1.5 py-0.5 rounded border border-rose-200 dark:border-rose-900/40">
                                                    Belum Hadir
                                                </span>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-[10px] text-slate-400 italic">Tidak ada pegawai dalam formasi ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full tailadmin-card p-12 text-center text-slate-400">
                        Tidak ada formasi jabatan yang ditemukan.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- SUB-TAB 2: HISTORY POIN & MATERI BRIEFING -->
        <div x-show="briefingSubTab === 'history'" class="space-y-4">
            <div class="tailadmin-card overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                            Riwayat Materi & Poin Pengarahan Briefing
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Dokumentasi instruksi harian, materi pembinaan, dan presensi per tanggal sesi.
                        </p>
                    </div>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($briefingHistory as $bHist)
                        <div class="p-5 hover:bg-slate-50/60 dark:hover:bg-[#1A222C]/50 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-mono">
                                        {{ \Carbon\Carbon::parse($bHist->date)->translatedFormat('l, d M Y') }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-mono">
                                        {{ $bHist->start_time ?? '07:00' }} - {{ $bHist->end_time ?? '08:00' }} WITA
                                    </span>
                                    @if($bHist->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                            Aktif / Buka
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs font-bold text-slate-600 dark:text-slate-300 flex items-center gap-2">
                                    <span>Total Hadir: <b class="text-indigo-600 font-mono">{{ $bHist->attendances_count }} Pegawai</b></span>
                                    <span>• Dibuka oleh: {{ $bHist->creator->name ?? 'Kepala Sekolah' }}</span>
                                </div>
                            </div>
                            <h4 class="text-sm font-black text-slate-900 dark:text-white">
                                {{ $bHist->title }}
                            </h4>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1.5 leading-relaxed bg-slate-50 dark:bg-[#1A222C]/70 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                                {{ $bHist->notes ?? 'Tidak ada catatan arahan tambahan.' }}
                            </p>
                        </div>
                    @empty
                        <div class="p-12 text-center text-slate-400 text-xs">
                            Belum ada riwayat sesi briefing yang tersimpan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
