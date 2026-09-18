<!-- ═════════════════════════════════════════════════════════════════════ -->
<!-- DASHBOARD VIEW: BIMBINGAN KONSELING (BK) -->
<!-- ═════════════════════════════════════════════════════════════════════ -->
<div class="space-y-6">

    <!-- Header Section BK -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white shadow-xs">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center border border-cyan-100 dark:border-cyan-900/50 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Portal Bimbingan &amp; Konseling (BK)</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Layanan pendampingan siswa, konseling individu/kelompok, pencatatan kedisiplinan, serta permohonan izin</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('admin.bk.create') }}" class="px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-extrabold text-xs rounded-2xl shadow-xs transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Konseling</span>
            </a>
            <a href="{{ route('admin.bk.violations.create') }}" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-2xl shadow-xs transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Catat Pelanggaran</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics Grid BK -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Sesi Konseling -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Bimbingan BK</p>
                <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center border border-cyan-100 dark:border-cyan-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-cyan-600 dark:text-cyan-400 tracking-tight">{{ number_format($bkStats['total_counselings'] ?? 0) }}</h3>
                <a href="{{ route('admin.bk.index') }}" class="text-[11px] font-bold text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 mt-1 inline-flex items-center gap-1">
                    <span>Lihat Layanan BK &rarr;</span>
                </a>
            </div>
        </div>

        <!-- 2. Konseling Dalam Intervensi -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Dalam Intervensi</p>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 tracking-tight">{{ number_format($bkStats['in_progress_counselings'] ?? 0) }}</h3>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">Siswa aktif dibimbing BK</p>
            </div>
        </div>

        <!-- 3. Catatan Pelanggaran -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Catatan Pelanggaran</p>
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-100 dark:border-rose-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 tracking-tight">{{ number_format($bkStats['total_violations'] ?? 0) }}</h3>
                <a href="{{ route('admin.bk.violations.index') }}" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 dark:text-rose-400 mt-1 inline-flex items-center gap-1">
                    <span>Rekap Poin &amp; Pelanggaran &rarr;</span>
                </a>
            </div>
        </div>

        <!-- 4. Izin Siswa Pending -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Izin Pending Review</p>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">{{ number_format($bkStats['pending_permits'] ?? 0) }}</h3>
                <button type="button" @click="createPermitModal = true" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 mt-1 inline-flex items-center gap-1">
                    <span>Input / Validasi Izin &rarr;</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu Utama BK (Akses Cepat Modul Bimbingan) -->
    <div class="tailadmin-card p-6 border border-[#E2E8F0] dark:border-[#2E3A47] bg-white dark:bg-[#1A222C] rounded-3xl shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-[#2E3A47]">
            <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-[#1C2434] dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Akses Cepat Modul Bimbingan Konseling
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Menu utama pengelolaan konseling, asesmen, poin pelanggaran, dan ketertiban siswa</p>
            </div>
        </div>

        <div class="grid grid-cols-4 sm:grid-cols-6 gap-y-4 sm:gap-y-5 gap-x-2 sm:gap-x-4 pt-2">
            <!-- 1. Layanan Konseling -->
            <a href="{{ route('admin.bk.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-cyan-50 dark:bg-cyan-950/50 text-cyan-600 dark:text-cyan-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-cyan-100 dark:border-cyan-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors leading-tight">Layanan BK</span>
            </a>

            <!-- 2. Asesmen BK -->
            <a href="{{ route('admin.bk.assessments') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-indigo-100 dark:border-indigo-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-tight">Asesmen Siswa</span>
            </a>

            <!-- 3. Pelanggaran Siswa -->
            <a href="{{ route('admin.bk.violations.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-rose-100 dark:border-rose-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors leading-tight">Pelanggaran &amp; Poin</span>
            </a>

            <!-- 4. Input Izin Siswa -->
            <button type="button" @click="createPermitModal = true" class="flex flex-col items-center justify-center text-center group cursor-pointer">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-sky-100 dark:border-sky-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors leading-tight">Input Izin Siswa</span>
            </button>

            <!-- 5. Data Siswa -->
            <a href="{{ route('admin.students.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-amber-100 dark:border-amber-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors leading-tight">Data Siswa</span>
            </a>

            <!-- 6. Absensi Siswa -->
            <a href="{{ route('admin.attendances.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-emerald-100 dark:border-emerald-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors leading-tight">Absensi Siswa</span>
            </a>
        </div>
    </div>

    <!-- 2 Kolom: Konseling Terbaru & Catatan Pelanggaran -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- List Sesi Bimbingan Konseling Terbaru -->
        <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    Sesi Bimbingan Konseling Terbaru
                </h4>
                <a href="{{ route('admin.bk.index') }}" class="text-xs font-bold text-cyan-600 hover:text-cyan-700">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentCounselings ?? [] as $csl)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950/50 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-extrabold text-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $csl->student?->user?->name ?? 'Siswa' }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                {{ $csl->title }} &bull; {{ $csl->date ? $csl->date->translatedFormat('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full bg-cyan-50 text-cyan-600 dark:bg-cyan-950/40 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-800">
                        {{ $csl->status_badge }}
                    </span>
                </div>
                @empty
                <div class="py-6 text-center text-xs font-medium text-slate-400">Belum ada catatan konseling terbaru.</div>
                @endforelse
            </div>
        </div>

        <!-- List Catatan Pelanggaran Siswa -->
        <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Catatan Pelanggaran Kedisiplinan Terbaru
                </h4>
                <a href="{{ route('admin.bk.violations.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentViolations ?? [] as $vio)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center font-extrabold text-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $vio->student?->user?->name ?? 'Siswa' }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                {{ $vio->title }} &bull; {{ $vio->violation_date ? $vio->violation_date->translatedFormat('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-[11px] font-black text-rose-600 bg-rose-50 dark:bg-rose-950/40 rounded-full border border-rose-200 dark:border-rose-800">
                        +{{ $vio->points }} Poin
                    </span>
                </div>
                @empty
                <div class="py-6 text-center text-xs font-medium text-slate-400">Belum ada catatan pelanggaran siswa.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
