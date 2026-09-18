<!-- ═════════════════════════════════════════════════════════════════════ -->
<!-- DASHBOARD VIEW: OPERATOR TU & DATA AKADEMIK -->
<!-- ═════════════════════════════════════════════════════════════════════ -->
<div class="space-y-6">

    <!-- Header Section Operator TU -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white shadow-xs">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-900/50 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-orange-600 dark:text-orange-400">Portal Operator TU &amp; Data Akademik</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pengelolaan master data siswa, rombel kelas, jadwal pelajaran, cetak KTS, serta validasi SPMB</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('admin.students.create') }}" class="px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-extrabold text-xs rounded-2xl shadow-xs transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Siswa</span>
            </a>
            <a href="{{ route('admin.spmb.index') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs rounded-2xl shadow-xs transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Verifikasi SPMB</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics Grid Operator TU -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Siswa -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Siswa Aktif</p>
                <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center border border-orange-100 dark:border-orange-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-orange-600 dark:text-orange-400 tracking-tight">{{ number_format($operatorStats['total_students'] ?? 0) }}</h3>
                <a href="{{ route('admin.students.index') }}" class="text-[11px] font-bold text-orange-600 hover:text-orange-700 dark:text-orange-400 mt-1 inline-flex items-center gap-1">
                    <span>Kelola Master Siswa &rarr;</span>
                </a>
            </div>
        </div>

        <!-- 2. Rombel / Kelas -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rombel Kelas</p>
                <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center border border-cyan-100 dark:border-cyan-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-cyan-600 dark:text-cyan-400 tracking-tight">{{ number_format($operatorStats['total_classes'] ?? 0) }}</h3>
                <a href="{{ route('admin.classes.index') }}" class="text-[11px] font-bold text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 mt-1 inline-flex items-center gap-1">
                    <span>Struktur Kelas &rarr;</span>
                </a>
            </div>
        </div>

        <!-- 3. Pendaftar SPMB -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total SPMB</p>
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-blue-600 dark:text-blue-400 tracking-tight">{{ number_format($operatorStats['total_spmb'] ?? 0) }}</h3>
                <a href="{{ route('admin.spmb.index') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 mt-1 inline-flex items-center gap-1">
                    <span>Validasi Berkas SPMB &rarr;</span>
                </a>
            </div>
        </div>

        <!-- 4. Total Jadwal Pelajaran -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Jadwal Pelajaran</p>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">{{ number_format($operatorStats['total_schedules'] ?? 0) }}</h3>
                <a href="{{ route('admin.schedules.index') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 mt-1 inline-flex items-center gap-1">
                    <span>Kelola Jadwal &rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Menu Utama Operator TU -->
    <div class="tailadmin-card p-6 border border-[#E2E8F0] dark:border-[#2E3A47] bg-white dark:bg-[#1A222C] rounded-3xl shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-[#2E3A47]">
            <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-[#1C2434] dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Akses Cepat Modul Tata Usaha &amp; Akademik
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Modul utama pengolahan data induk dan administrasi sekolah</p>
            </div>
        </div>

        <div class="grid grid-cols-4 sm:grid-cols-8 gap-y-4 sm:gap-y-5 gap-x-2 sm:gap-x-4 pt-2">
            <!-- 1. Data Siswa -->
            <a href="{{ route('admin.students.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-amber-100 dark:border-amber-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors leading-tight">Data Siswa</span>
            </a>

            <!-- 2. Data Rombel -->
            <a href="{{ route('admin.classes.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-cyan-50 dark:bg-cyan-950/50 text-cyan-600 dark:text-cyan-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-cyan-100 dark:border-cyan-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors leading-tight">Data Rombel</span>
            </a>

            <!-- 3. Cetak KTS -->
            <a href="{{ route('admin.student-cards.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-pink-50 dark:bg-pink-950/50 text-pink-600 dark:text-pink-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-pink-100 dark:border-pink-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors leading-tight">Cetak KTS</span>
            </a>

            <!-- 4. SPMB Online -->
            <a href="{{ route('admin.spmb.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-blue-100 dark:border-blue-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-tight">SPMB Online</span>
            </a>

            <!-- 5. Jadwal Pelajaran -->
            <a href="{{ route('admin.schedules.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-indigo-100 dark:border-indigo-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-tight">Jadwal Pelajaran</span>
            </a>

            <!-- 6. Guru & Staf -->
            <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-violet-50 dark:bg-violet-950/50 text-violet-600 dark:text-violet-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-violet-100 dark:border-violet-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors leading-tight">Guru &amp; Staf</span>
            </a>

            <!-- 7. Absensi Siswa -->
            <a href="{{ route('admin.attendances.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-emerald-100 dark:border-emerald-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors leading-tight">Absensi Siswa</span>
            </a>

            <!-- 8. Input Izin Siswa -->
            <button type="button" @click="createPermitModal = true" class="flex flex-col items-center justify-center text-center group cursor-pointer">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-sky-100 dark:border-sky-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors leading-tight">Input Izin</span>
            </button>
        </div>
    </div>

    <!-- 2 Kolom: SPMB Verifikasi & Ringkasan Rombel -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- List Pendaftaran SPMB Terbaru -->
        <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Pendaftaran SPMB Terbaru (Verifikasi Berkas)
                </h4>
                <a href="{{ route('admin.spmb.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentSpmb ?? [] as $spm)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-extrabold text-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $spm->full_name }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                No: {{ $spm->registration_number }} &bull; Gel: {{ $spm->wave?->name ?? '-' }}
                            </p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full bg-amber-50 text-amber-600 border border-amber-200">
                        {{ ucfirst($spm->status) }}
                    </span>
                </div>
                @empty
                <div class="py-6 text-center text-xs font-medium text-slate-400">Belum ada pendaftaran SPMB terbaru.</div>
                @endforelse
            </div>
        </div>

        <!-- List Rombel Kelas -->
        <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Ringkasan Rombel Kelas
                </h4>
                <a href="{{ route('admin.classes.index') }}" class="text-xs font-bold text-cyan-600 hover:text-cyan-700">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentClasses ?? [] as $cls)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950/50 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-extrabold text-xs shrink-0">
                            🏫
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $cls->name }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                Wali Kelas: {{ $cls->teacher?->name ?? 'Belum ditentukan' }}
                            </p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-[11px] font-extrabold text-indigo-600 bg-indigo-50 dark:bg-indigo-950/40 rounded-full border border-indigo-200">
                        {{ $cls->students_count ?? 0 }} Siswa
                    </span>
                </div>
                @empty
                <div class="py-6 text-center text-xs font-medium text-slate-400">Belum ada data rombel kelas.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
