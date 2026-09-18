<!-- ═════════════════════════════════════════════════════════════════════ -->
<!-- DASHBOARD VIEW: ADMIN OPERASIONAL AKADEMIK & UTAMA -->
<!-- ═════════════════════════════════════════════════════════════════════ -->
<div class="space-y-6">

    <!-- Menu Kotak-Kotak Modul Admin (Style Dashboard Siswa) -->
    <div class="tailadmin-card p-6 border border-[#E2E8F0] dark:border-[#2E3A47] bg-white dark:bg-[#1A222C] rounded-3xl shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-[#2E3A47]">
            <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-[#1C2434] dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Menu Utama Portal Admin
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Akses cepat ke 16 fitur &amp; modul operasional sekolah</p>
            </div>
        </div>

        <div class="grid grid-cols-4 sm:grid-cols-8 gap-y-4 sm:gap-y-5 gap-x-2 sm:gap-x-4 pt-2">
            <!-- Item 1: Absensi Siswa -->
            @if(auth()->user()->hasPermission('view-attendance'))
            <a href="{{ route('admin.attendances.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-indigo-100 dark:border-indigo-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-tight">Absensi Siswa</span>
            </a>
            @endif

            <!-- Item 2: Input Izin Siswa -->
            @if(auth()->user()->hasPermission('view-attendance'))
            <button type="button" @click="createPermitModal = true" class="flex flex-col items-center justify-center text-center group cursor-pointer">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-sky-100 dark:border-sky-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors leading-tight">Input Izin</span>
            </button>
            @endif

            <!-- Item 3: Data Siswa -->
            @if(auth()->user()->hasPermission('view-students'))
            <a href="{{ route('admin.students.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-amber-100 dark:border-amber-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors leading-tight">Data Siswa</span>
            </a>
            @endif

            <!-- Item 4: Guru & Staf -->
            @if(auth()->user()->hasPermission('view-users'))
            <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-violet-50 dark:bg-violet-950/50 text-violet-600 dark:text-violet-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-violet-100 dark:border-violet-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors leading-tight">Guru &amp; Staf</span>
            </a>
            @endif

            <!-- Item 5: Data Rombel -->
            @if(auth()->user()->hasPermission('view-classes'))
            <a href="{{ route('admin.classes.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-cyan-50 dark:bg-cyan-950/50 text-cyan-600 dark:text-cyan-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-cyan-100 dark:border-cyan-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors leading-tight">Data Rombel</span>
            </a>
            @endif

            <!-- Item 6: Jadwal Pelajaran -->
            @if(auth()->user()->hasPermission('view-learning') || auth()->user()->hasPermission('view-classes'))
            <a href="{{ route('admin.schedules.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-orange-100 dark:border-orange-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors leading-tight">Jadwal Pelajaran</span>
            </a>
            @endif

            <!-- Item 7: E-Raport Siswa -->
            @if(auth()->user()->hasPermission('view-learning'))
            <a href="{{ route('admin.raport.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-emerald-100 dark:border-emerald-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-6-3h12"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors leading-tight">E-Raport Siswa</span>
            </a>
            @endif

            <!-- Item 8: Entri Nilai -->
            @if(auth()->user()->hasPermission('view-learning'))
            <a href="{{ route('admin.grades.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-rose-100 dark:border-rose-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors leading-tight">Entri Nilai</span>
            </a>
            @endif

            <!-- Item 9: CBT Online -->
            @if(auth()->user()->hasPermission('view-learning'))
            <a href="{{ route('admin.exams.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-teal-100 dark:border-teal-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors leading-tight">CBT Online</span>
            </a>
            @endif

            <!-- Item 10: SPMB Online -->
            @if(auth()->user()->hasPermission('view-spmb'))
            <a href="{{ route('admin.spmb.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-purple-100 dark:border-purple-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors leading-tight">SPMB Online</span>
            </a>
            @endif

            <!-- Item 11: Cetak KTS -->
            @if(auth()->user()->hasPermission('view-students'))
            <a href="{{ route('admin.student-cards.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-pink-50 dark:bg-pink-950/50 text-pink-600 dark:text-pink-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-pink-100 dark:border-pink-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors leading-tight">Cetak KTS</span>
            </a>
            @endif

            <!-- Item 12: Pembayaran SPP -->
            @if(auth()->user()->hasPermission('view-payments'))
            <a href="{{ route('admin.student-payments.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-emerald-100 dark:border-emerald-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors leading-tight">Bayar SPP</span>
            </a>
            @endif

            <!-- Item 13: Berita & Artikel -->
            @if(auth()->user()->hasPermission('view-posts'))
            <a href="{{ route('admin.posts.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-blue-100 dark:border-blue-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors leading-tight">Berita &amp; Artikel</span>
            </a>
            @endif

            <!-- Item 14: Galeri Foto -->
            @if(auth()->user()->hasPermission('view-gallery'))
            <a href="{{ route('admin.gallery.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-purple-100 dark:border-purple-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors leading-tight">Galeri Foto</span>
            </a>
            @endif

            <!-- Item 15: Pengumuman -->
            @if(auth()->user()->hasPermission('view-announcements'))
            <a href="{{ route('admin.announcements.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-amber-100 dark:border-amber-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors leading-tight">Pengumuman</span>
            </a>
            @endif

            <!-- Item 16: Pengaturan Sekolah -->
            @if(auth()->user()->hasPermission('view-settings'))
            <a href="{{ route('admin.settings') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-slate-200 dark:border-slate-700">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white transition-colors leading-tight">Pengaturan</span>
            </a>
            @endif
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Siswa -->
        @if(auth()->user()->hasPermission('view-students'))
        <div class="tailadmin-card p-6 flex flex-col justify-between relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-[#3C50E0] dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-800/60 shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                </div>
                <span class="inline-flex items-center text-xs font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 10l7-7 7 7M12 3v18"/></svg>
                    Aktif
                </span>
            </div>
            <div class="mt-4">
                <h4 class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">
                    {{ number_format($stats['total_students'] ?? 0) }}
                </h4>
                <p class="text-xs font-semibold text-[#64748B] dark:text-[#8A99AD] mt-1 flex items-center justify-between">
                    <span>Total Siswa Terdaftar</span>
                    <span class="font-bold text-[#1C2434] dark:text-white">{{ number_format($stats['total_users'] ?? 0) }} Akun</span>
                </p>
            </div>
        </div>
        @endif

        <!-- Card 2: Total Guru -->
        @if(auth()->user()->hasPermission('view-users') || auth()->user()->hasPermission('view-learning'))
        <div class="tailadmin-card p-6 flex flex-col justify-between relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-800/60 shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12"/>
                    </svg>
                </div>
                <span class="inline-flex items-center text-xs font-bold text-[#3C50E0] bg-[#3C50E0]/10 px-2.5 py-1 rounded-full border border-[#3C50E0]/20">
                    Pengajar
                </span>
            </div>
            <div class="mt-4">
                <h4 class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">
                    {{ number_format($stats['total_teachers'] ?? 0) }}
                </h4>
                <p class="text-xs font-semibold text-[#64748B] dark:text-[#8A99AD] mt-1 flex items-center justify-between">
                    <span>Total Tenaga Pendidik</span>
                    <span class="font-bold text-[#1C2434] dark:text-white">{{ number_format($stats['total_schedules'] ?? 0) }} Jadwal</span>
                </p>
            </div>
        </div>
        @endif

        <!-- Card 3: Pendaftaran SPMB -->
        @if(auth()->user()->hasPermission('view-spmb'))
        <div class="tailadmin-card p-6 flex flex-col justify-between relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-800/60 shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="inline-flex items-center text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-950/40 px-2.5 py-1 rounded-full border border-blue-200 dark:border-blue-800">
                    SPMB 2026
                </span>
            </div>
            <div class="mt-4">
                <h4 class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">
                    {{ number_format($stats['total_spmb'] ?? 0) }}
                </h4>
                <p class="text-xs font-semibold text-[#64748B] dark:text-[#8A99AD] mt-1">
                    Calon Siswa Baru Terdaftar
                </p>
            </div>
        </div>
        @endif

        <!-- Card 4: Perlu Verifikasi SPMB -->
        @if(auth()->user()->hasPermission('view-spmb'))
        <div class="tailadmin-card p-6 flex flex-col justify-between relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-800/60 shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @if(($stats['pending_spmb'] ?? 0) > 0)
                    <span class="inline-flex items-center text-xs font-bold text-amber-600 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-1 rounded-full border border-amber-200 dark:border-amber-800 animate-pulse">
                        Pending Review
                    </span>
                @else
                    <span class="inline-flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-800">
                        Selesai
                    </span>
                @endif
            </div>
            <div class="mt-4">
                <h4 class="text-2xl sm:text-3xl font-extrabold {{ ($stats['pending_spmb'] ?? 0) > 0 ? 'text-amber-600' : 'text-[#1C2434] dark:text-white' }} tracking-tight">
                    {{ number_format($stats['pending_spmb'] ?? 0) }}
                </h4>
                <p class="text-xs font-semibold text-[#64748B] dark:text-[#8A99AD] mt-1">
                    Berkas Pendaftaran Menunggu Verifikasi
                </p>
            </div>
        </div>
        @endif
    </div>

    <!-- Secondary Mini Stats Ribbon (4 Columns) -->
    @if(auth()->user()->hasPermission('view-posts') || auth()->user()->hasPermission('view-learning'))
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @if(auth()->user()->hasPermission('view-posts'))
        <div class="tailadmin-card p-4 flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-[#3C50E0] dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-800/60 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Artikel Berita</p>
                <p class="text-lg font-extrabold text-[#1C2434] dark:text-white mt-0.5">{{ number_format($stats['total_posts'] ?? 0) }}</p>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('view-learning'))
        <div class="tailadmin-card p-4 flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-800/60 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Materi Ajar</p>
                <p class="text-lg font-extrabold text-[#1C2434] dark:text-white mt-0.5">{{ number_format($stats['total_materials'] ?? 0) }}</p>
            </div>
        </div>

        <div class="tailadmin-card p-4 flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 border border-purple-100 dark:border-purple-800/60 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Tugas Siswa</p>
                <p class="text-lg font-extrabold text-[#1C2434] dark:text-white mt-0.5">{{ number_format($stats['total_assignments'] ?? 0) }}</p>
            </div>
        </div>

        <div class="tailadmin-card p-4 flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 border border-amber-100 dark:border-amber-800/60 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Tugas Aktif</p>
                <p class="text-lg font-extrabold text-[#1C2434] dark:text-white mt-0.5">{{ number_format($stats['pending_assignments'] ?? 0) }}</p>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- TailAdmin Analytics Charts (2 Columns Grid) -->
    @if(auth()->user()->hasPermission('view-students') || auth()->user()->hasPermission('view-spmb'))
    <div class="grid grid-cols-1 {{ (auth()->user()->hasPermission('view-students') && auth()->user()->hasPermission('view-spmb')) ? 'lg:grid-cols-2' : '' }} gap-6">
        <!-- Chart 1: Students per Class -->
        @if(auth()->user()->hasPermission('view-students'))
        <div class="tailadmin-card p-6 flex flex-col">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                <div>
                    <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base tracking-tight">Distribusi Siswa per Kelas</h3>
                    <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">Jumlah siswa aktif terdaftar per rombel kelas</p>
                </div>
                <span class="px-3 py-1 bg-[#3C50E0]/10 text-[#3C50E0] text-xs font-bold rounded-lg border border-[#3C50E0]/20">
                    Data Rombel
                </span>
            </div>
            <div class="h-72 relative">
                <canvas id="studentsClassChart"></canvas>
            </div>
        </div>
        @endif

        <!-- Chart 2: SPMB Registration Status -->
        @if(auth()->user()->hasPermission('view-spmb'))
        <div class="tailadmin-card p-6 flex flex-col">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                <div>
                    <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base tracking-tight">Status Pendaftaran SPMB</h3>
                    <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">Komposisi status tahapan pendaftar baru</p>
                </div>
                <span class="px-3 py-1 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-lg border border-blue-200 dark:border-blue-800">
                    Proporsi SPMB
                </span>
            </div>
            <div class="h-72 relative flex items-center justify-center">
                <canvas id="spmbStatusChart"></canvas>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Chart 3: Activity Posts Trend (Full Width Line Chart) -->
    @if(auth()->user()->hasPermission('view-posts'))
    <div class="tailadmin-card p-6">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#E2E8F0] dark:border-[#2E3A47]">
            <div>
                <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base tracking-tight">Aktivitas Publikasi Berita</h3>
                <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">Jumlah artikel berita yang diterbitkan per bulan (6 Bulan Terakhir)</p>
            </div>
            <span class="px-3 py-1 bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] text-xs font-bold rounded-lg border border-[#E2E8F0] dark:border-[#2E3A47]">
                Tren Publikasi
            </span>
        </div>
        <div class="h-72 relative">
            <canvas id="postsTrendChart"></canvas>
        </div>
    </div>
    @endif

    <!-- TailAdmin Recent Data & Announcements Section -->
    <div class="grid grid-cols-1 {{ auth()->user()->hasPermission('view-spmb') ? 'xl:grid-cols-3' : '' }} gap-6">
        <!-- Recent SPMB Table (2 Cols) -->
        @if(auth()->user()->hasPermission('view-spmb'))
        <div class="xl:col-span-2 tailadmin-card flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between bg-slate-50/50 dark:bg-[#1A222C]/40">
                <div>
                    <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base tracking-tight">Pendaftaran SPMB Terbaru</h3>
                    <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">Daftar calon siswa yang baru masuk ke sistem</p>
                </div>
                <a href="{{ route('admin.spmb.index') }}" class="text-xs font-bold text-[#3C50E0] hover:underline flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider font-bold border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                        <tr>
                            <th class="px-6 py-3.5">Nama Pendaftar</th>
                            <th class="px-6 py-3.5">Gelombang</th>
                            <th class="px-6 py-3.5 text-center">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] text-[#1C2434] dark:text-[#DEE4EE]">
                        @forelse($recentSpmb ?? [] as $registration)
                            <tr class="hover:bg-[#F1F5F9]/60 dark:hover:bg-[#1A222C]/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3.5">
                                        <div class="w-9 h-9 rounded-xl bg-[#3C50E0]/10 text-[#3C50E0] font-extrabold flex items-center justify-center text-xs border border-[#3C50E0]/20 shrink-0">
                                            {{ strtoupper(substr($registration->full_name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-[#1C2434] dark:text-white">{{ $registration->full_name }}</p>
                                            <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD]">No. Reg: {{ $registration->registration_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-[#64748B] dark:text-[#8A99AD]">
                                    {{ $registration->wave->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $badgeStyle = match($registration->status) {
                                            'accepted' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                            'rejected' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                            'submitted' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 border-slate-200',
                                        };
                                        $statusLabel = match($registration->status) {
                                            'accepted' => 'Diterima',
                                            'rejected' => 'Ditolak',
                                            'submitted' => 'Menunggu',
                                            default => ucfirst($registration->status),
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 text-[10px] font-extrabold rounded-full border {{ $badgeStyle }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.spmb.show', encode_id($registration->id)) }}" class="inline-flex items-center justify-center p-2 text-[#64748B] hover:text-[#3C50E0] hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition-colors" title="Lihat Detail Pendaftaran">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-[#64748B] dark:text-[#8A99AD] text-xs">
                                    Belum ada data pendaftaran SPMB terbaru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Announcements Feed (1 Col) -->
        <div class="tailadmin-card flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E2E8F0] dark:border-[#2E3A47] bg-slate-50/50 dark:bg-[#1A222C]/40">
                <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base tracking-tight">Pengumuman Terbaru</h3>
                <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">Pengumuman dan edaran resmi sekolah</p>
            </div>

            <div class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] overflow-y-auto max-h-[380px]">
                @forelse($announcements ?? [] as $announcement)
                    <div class="p-4 sm:p-5 hover:bg-[#F1F5F9]/60 dark:hover:bg-[#1A222C]/50 transition-colors space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold text-[#3C50E0] bg-[#3C50E0]/10 px-2.5 py-0.5 rounded-md border border-[#3C50E0]/20">
                                {{ $announcement->type ?? 'Umum' }}
                            </span>
                            <span class="text-[11px] text-[#64748B] dark:text-[#8A99AD]">
                                {{ $announcement->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-[#1C2434] dark:text-white leading-snug">{{ $announcement->title }}</h4>
                        <p class="text-xs text-[#64748B] dark:text-[#8A99AD] line-clamp-2 leading-relaxed font-normal">{{ $announcement->content }}</p>
                    </div>
                @empty
                    <div class="p-10 text-center text-[#64748B] dark:text-[#8A99AD] text-xs">
                        Tidak ada pengumuman terbaru.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
