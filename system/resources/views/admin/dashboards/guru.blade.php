<!-- ═════════════════════════════════════════════════════════════════════ -->
<!-- DASHBOARD VIEW: GURU & TENAGA PENDIDIK -->
<!-- ═════════════════════════════════════════════════════════════════════ -->
<div class="space-y-6">

    <!-- Header Section Guru -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white shadow-xs">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900/50 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-6-3h12"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Portal &amp; Dashboard Guru Pengajar</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kelola kegiatan belajar mengajar, jadwal tatap muka, tugas, materi, dan penilai akademik siswa</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('admin.materials.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-2xl shadow-xs transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Upload Materi</span>
            </a>
            <a href="{{ route('admin.assignments.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-2xl shadow-xs transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Buat Tugas</span>
            </a>
        </div>
    </div>

    @if(isset($pendingGradingResults) && $pendingGradingResults->count() > 0)
        <div class="p-5 rounded-3xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 shadow-xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-amber-900 dark:text-amber-100">Perhatian: Jawaban Essay Ujian CBT Menunggu Koreksi ({{ $pendingGradingResults->count() }})</h4>
                        <p class="text-xs text-amber-700 dark:text-amber-300 font-medium">Siswa telah mengumpulkan jawaban essay pada Ujian CBT yang Anda ampu. Harap lakukan penilaian manual.</p>
                    </div>
                </div>
                <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl transition shrink-0 inline-flex items-center justify-center gap-1">
                    <span>Koreksi Sekarang &rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 pt-2 border-t border-amber-200/60 dark:border-amber-800/60">
                @foreach($pendingGradingResults->take(3) as $pGrad)
                    <a href="{{ route('admin.exams.grade-student', [$pGrad->exam_id, $pGrad->id]) }}" class="p-2.5 bg-white dark:bg-[#1A222C] rounded-xl border border-amber-200 dark:border-amber-800/80 hover:border-amber-400 transition flex items-center justify-between text-xs">
                        <div class="truncate mr-2">
                            <p class="font-bold text-slate-900 dark:text-white truncate">{{ $pGrad->student->user->name ?? $pGrad->student->name }}</p>
                            <p class="text-[10px] text-slate-500 truncate">{{ $pGrad->exam->title ?? 'Ujian' }}</p>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-extrabold bg-amber-100 text-amber-900 rounded-md shrink-0">Koreksi</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4 KPI Metrics Grid Guru -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Jadwal Mengajar -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Jadwal Mengajar</p>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">{{ number_format($teacherStats['my_schedules_count'] ?? 0) }}</h3>
                <a href="{{ route('admin.schedules.index') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 mt-1 inline-flex items-center gap-1">
                    <span>Lihat Jadwal Mengajar &rarr;</span>
                </a>
            </div>
        </div>

        <!-- 2. Materi Pembelajaran -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Materi Pembelajaran</p>
                <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-100 dark:border-sky-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-sky-600 dark:text-sky-400 tracking-tight">{{ number_format($teacherStats['my_materials_count'] ?? 0) }}</h3>
                <a href="{{ route('admin.materials.index') }}" class="text-[11px] font-bold text-sky-600 hover:text-sky-700 dark:text-sky-400 mt-1 inline-flex items-center gap-1">
                    <span>Kelola Modul &amp; Materi &rarr;</span>
                </a>
            </div>
        </div>

        <!-- 3. Penugasan Siswa -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tugas &amp; Project</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">{{ number_format($teacherStats['my_assignments_count'] ?? 0) }}</h3>
                <a href="{{ route('admin.assignments.index') }}" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 mt-1 inline-flex items-center gap-1">
                    <span>Periksa Pengumpulan &rarr;</span>
                </a>
            </div>
        </div>

        <!-- 4. Ujian CBT Online -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Ujian CBT Dibuat</p>
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-100 dark:border-purple-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-purple-600 dark:text-purple-400 tracking-tight">{{ number_format($teacherStats['my_exams_count'] ?? 0) }}</h3>
                <a href="{{ route('admin.exams.index') }}" class="text-[11px] font-bold text-purple-600 hover:text-purple-700 dark:text-purple-400 mt-1 inline-flex items-center gap-1">
                    <span>Kelola Bank Soal &amp; CBT &rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Menu Utama Guru (Akses Cepat) -->
    <div class="tailadmin-card p-6 border border-[#E2E8F0] dark:border-[#2E3A47] bg-white dark:bg-[#1A222C] rounded-3xl shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-[#2E3A47]">
            <div>
                <h3 class="text-xs sm:text-sm font-extrabold text-[#1C2434] dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Akses Cepat Modul Pembelajaran Guru
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">Navigasi instan ke fitur akademik dan pembelajaran harian</p>
            </div>
        </div>

        <div class="grid grid-cols-4 sm:grid-cols-8 gap-y-4 sm:gap-y-5 gap-x-2 sm:gap-x-4 pt-2">
            <!-- 1. Absensi Siswa -->
            <a href="{{ route('admin.attendances.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-indigo-100 dark:border-indigo-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-tight">Absensi Siswa</span>
            </a>

            <!-- 2. Jadwal Mengajar -->
            <a href="{{ route('admin.schedules.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-orange-100 dark:border-orange-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors leading-tight">Jadwal Saya</span>
            </a>

            <!-- 3. Materi Ajar -->
            <a href="{{ route('admin.materials.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-sky-100 dark:border-sky-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors leading-tight">Materi Ajar</span>
            </a>

            <!-- 4. Tugas Siswa -->
            <a href="{{ route('admin.assignments.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-emerald-100 dark:border-emerald-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors leading-tight">Tugas Siswa</span>
            </a>

            <!-- 5. Ujian CBT -->
            <a href="{{ route('admin.exams.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-purple-100 dark:border-purple-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors leading-tight">Ujian CBT</span>
            </a>

            <!-- 6. Entri Nilai -->
            <a href="{{ route('admin.grades.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-rose-100 dark:border-rose-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors leading-tight">Entri Nilai</span>
            </a>

            <!-- 7. E-Raport Siswa -->
            <a href="{{ route('admin.raport.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-teal-100 dark:border-teal-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-6-3h12"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors leading-tight">E-Raport Siswa</span>
            </a>

            <!-- 8. Presensi Guru Saya -->
            <a href="{{ route('admin.teacher-attendances.index') }}" class="flex flex-col items-center justify-center text-center group">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center mb-1.5 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all border border-amber-100 dark:border-amber-800/60">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] sm:text-[11px] font-bold text-slate-700 dark:text-slate-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors leading-tight">Presensi Guru</span>
            </a>
        </div>
    </div>

    <!-- 2 Kolom: Jadwal Mengajar & Tugas Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- List Jadwal Mengajar Saya -->
        <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Jadwal Mengajar Aktif
                </h4>
                <a href="{{ route('admin.schedules.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Semua Jadwal &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($teacherSchedules ?? [] as $sch)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-extrabold text-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $sch->subject_name ?? 'Mata Pelajaran' }} ({{ $sch->class_name ?? 'Kelas' }})
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                {{ $sch->day_name ?? 'Hari' }} &bull; {{ $sch->start_time ?? '-' }} - {{ $sch->end_time ?? '-' }}
                            </p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-extrabold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 rounded-full border border-emerald-200 dark:border-emerald-800">
                        Aktif
                    </span>
                </div>
                @empty
                <div class="py-6 text-center text-xs font-medium text-slate-400">Belum ada data jadwal mengajar aktif.</div>
                @endforelse
            </div>
        </div>

        <!-- List Tugas Dibuat Terbaru -->
        <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Tugas &amp; Project Terbaru Diberikan
                </h4>
                <a href="{{ route('admin.assignments.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($teacherAssignments ?? [] as $asg)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-extrabold text-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $asg->title }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                Kelas {{ $asg->class?->name ?? '-' }} &bull; Tenggat: {{ $asg->due_date ? \Carbon\Carbon::parse($asg->due_date)->translatedFormat('d M Y H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.assignments.show', encode_id($asg->id)) }}" class="px-2.5 py-1 text-[11px] font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 dark:bg-indigo-950/50 rounded-lg">
                        Rincian
                    </a>
                </div>
                @empty
                <div class="py-6 text-center text-xs font-medium text-slate-400">Belum ada tugas yang dibuat.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
