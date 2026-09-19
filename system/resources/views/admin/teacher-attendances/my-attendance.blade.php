@extends('layouts.admin')

@section('title', 'Presensi Mandiri Guru & Pegawai')
@section('page_title', 'Presensi Mandiri')

@section('content')
<div class="space-y-6" x-data="teacherAttendanceApp()">

    <!-- Header & Profile Card -->
    <div class="p-6 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center font-black text-xl shadow-md shrink-0"
                 style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#3C50E0') }} 0%, {{ Setting::get('secondary_color', '#2563eb') }} 100%);">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">{{ $user->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 uppercase">
                        {{ $user->roles->pluck('name')->implode(', ') ?: 'Pendidik' }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mt-1">
                    @if($user->nip)
                        <span class="font-mono">NIP: {{ $user->nip }}</span>
                        <span>&bull;</span>
                    @endif
                    <span>{{ $user->email }}</span>
                    <span>&bull;</span>
                    <span>{{ $schoolName }}</span>
                </div>
            </div>
        </div>

        <!-- Live Server Clock & Sesi Aktif -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 shrink-0">
            <div class="p-3 rounded-2xl bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-slate-700/60 text-right">
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Waktu Server ({{ $timezoneLabel }})</span>
                <span class="text-xl font-black font-mono text-slate-900 dark:text-white" x-text="currentTime">00:00:00</span>
            </div>
        </div>
    </div>

    <!-- Sesi Presensi & Status Hari Ini Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- 1. Pagi (Masuk) -->
        <div class="tailadmin-card p-5 border rounded-2xl shadow-xs transition-all {{ ($todayAttendance && $todayAttendance->check_in) ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : (($activeSession['type'] === 'check_in') ? 'bg-indigo-50/30 dark:bg-indigo-950/30 border-indigo-300 dark:border-indigo-700 ring-2 ring-indigo-500/20' : 'bg-white dark:bg-[#1A222C] border-slate-200 dark:border-slate-800') }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">1. Sesi Pagi (Masuk)</span>
                    @if($activeSession['type'] === 'check_in')
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    @endif
                </div>
                @if($todayAttendance && $todayAttendance->check_in)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">✓ Sudah Hadir</span>
                @elseif($activeSession['type'] === 'check_in')
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 animate-pulse">Sesi Aktif</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500">Belum Absen</span>
                @endif
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black font-mono {{ ($todayAttendance && $todayAttendance->check_in) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                        {{ $todayAttendance && $todayAttendance->check_in ? substr($todayAttendance->check_in, 0, 5) : '--:--' }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        Batas: {{ $attendanceSettings['morning_late'] ?? '07:30' }} {{ $timezoneLabel }} (Buka: {{ $attendanceSettings['morning_open'] ?? '06:00' }} - {{ $attendanceSettings['morning_close'] ?? '11:59' }})
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl {{ ($todayAttendance && $todayAttendance->check_in) ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center font-bold">
                    🌅
                </div>
            </div>
        </div>

        <!-- 2. Siang (Dzuhur) -->
        <div class="tailadmin-card p-5 border rounded-2xl shadow-xs transition-all {{ ($todayAttendance && $todayAttendance->midday_at) ? 'bg-amber-50/40 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/40' : (($activeSession['type'] === 'midday') ? 'bg-amber-50/30 dark:bg-amber-950/30 border-amber-300 dark:border-amber-700 ring-2 ring-amber-500/20' : 'bg-white dark:bg-[#1A222C] border-slate-200 dark:border-slate-800') }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">2. Sesi Siang (Dzuhur)</span>
                    @if($activeSession['type'] === 'midday')
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    @endif
                </div>
                @if($todayAttendance && $todayAttendance->midday_at)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300">✓ Sudah Hadir</span>
                @elseif($activeSession['type'] === 'midday')
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 animate-pulse">Sesi Aktif</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500">Belum Absen</span>
                @endif
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black font-mono {{ ($todayAttendance && $todayAttendance->midday_at) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}">
                        {{ $todayAttendance && $todayAttendance->midday_at ? substr($todayAttendance->midday_at, 0, 5) : '--:--' }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        Jadwal: {{ $attendanceSettings['dzuhur_open'] ?? '12:00' }} - {{ $attendanceSettings['dzuhur_close'] ?? '13:30' }} {{ $timezoneLabel }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl {{ ($todayAttendance && $todayAttendance->midday_at) ? 'bg-amber-500/10 text-amber-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center font-bold">
                    ☀️
                </div>
            </div>
        </div>

        <!-- 3. Sore (Pulang) -->
        <div class="tailadmin-card p-5 border rounded-2xl shadow-xs transition-all {{ ($todayAttendance && $todayAttendance->check_out) ? 'bg-indigo-50/40 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-800/40' : (($activeSession['type'] === 'check_out') ? 'bg-indigo-50/30 dark:bg-indigo-950/30 border-indigo-300 dark:border-indigo-700 ring-2 ring-indigo-500/20' : 'bg-white dark:bg-[#1A222C] border-slate-200 dark:border-slate-800') }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">3. Sesi Sore (Pulang)</span>
                    @if($activeSession['type'] === 'check_out')
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    @endif
                </div>
                @if($todayAttendance && $todayAttendance->check_out)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">✓ Sudah Pulang</span>
                @elseif($activeSession['type'] === 'check_out')
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 animate-pulse">Sesi Aktif</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500">Belum Pulang</span>
                @endif
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black font-mono {{ ($todayAttendance && $todayAttendance->check_out) ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                        {{ $todayAttendance && $todayAttendance->check_out ? substr($todayAttendance->check_out, 0, 5) : '--:--' }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        KBM Selesai: {{ $attendanceSettings['afternoon_open'] ?? '14:00' }} - {{ $attendanceSettings['afternoon_close'] ?? '18:00' }} {{ $timezoneLabel }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl {{ ($todayAttendance && $todayAttendance->check_out) ? 'bg-indigo-500/10 text-indigo-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center font-bold">
                    🌇
                </div>
            </div>
        </div>
    </div>

    <!-- Active Session Banner -->
    @if(!empty($activeSession['holiday']['is_holiday']))
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">🏖️</span>
                <div>
                    <p class="text-xs font-bold text-amber-900 dark:text-amber-200">
                        Hari Libur: <strong>{{ $activeSession['holiday']['reason'] }}</strong>
                    </p>
                    <p class="text-[11px] text-amber-700 dark:text-amber-400">
                        Tidak ada kewajiban presensi pada hari libur yang telah dikonfigurasi.
                    </p>
                </div>
            </div>
            <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 rounded-xl text-xs font-black">
                Libur Sekolah
            </span>
        </div>
    @else
        <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">⏱️</span>
                <div>
                    <p class="text-xs font-bold text-indigo-900 dark:text-indigo-200">
                        Sesi Presensi Aktif: <strong class="text-indigo-700 dark:text-indigo-300">{{ $activeSession['name'] ?? 'Sesi Presensi' }}</strong>
                        @if(($activeSession['delay_minutes'] ?? 0) > 0)
                            <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-900/80 text-amber-800 dark:text-amber-200">
                                ⚠️ Terlambat {{ $activeSession['delay_minutes'] }} Menit
                            </span>
                        @endif
                    </p>
                    <p class="text-[11px] text-indigo-600 dark:text-indigo-400">
                        {{ $activeSession['message'] ?? 'Sistem mendeteksi sesi kehadiran secara otomatis berdasarkan jam server terkini.' }}
                    </p>
                </div>
            </div>
            @if($todayAttendance && $todayAttendance->verification_status)
                <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 rounded-xl text-xs font-extrabold flex items-center gap-1">
                    ✓ Biometrik / Geofence Terverifikasi
                </span>
            @endif
        </div>
    @endif

    <!-- REAL-TIME ATTENDANCE TELEMETRY STATUS PANEL (REQUIREMENT 8) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        <!-- 1. Status GPS -->
        <div class="p-3.5 rounded-2xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-2xs space-y-1.5 transition-all">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Status GPS</span>
            <div class="flex items-center gap-1.5 font-black text-xs" :class="gpsStatusTextClass">
                <span class="w-2.5 h-2.5 rounded-full" :class="gpsStatusDotClass"></span>
                <span x-text="gpsStatusBadge">🟡 Memeriksa...</span>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate" x-text="gpsStatusSubText">Izin Browser</p>
        </div>

        <!-- 2. Status Fingerprint -->
        <div class="p-3.5 rounded-2xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-2xs space-y-1.5 transition-all">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Status Fingerprint</span>
            <div class="flex items-center gap-1.5 font-black text-xs" :class="fpStatusTextClass">
                <span class="w-2.5 h-2.5 rounded-full" :class="fpStatusDotClass"></span>
                <span x-text="fpStatusBadge">🔴 Scanner tidak ditemukan</span>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate" x-text="fpStatusSubText">U.are.U 4500 (Local Service)</p>
        </div>

        <!-- 3. Status Jadwal -->
        <div class="p-3.5 rounded-2xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-2xs space-y-1.5 transition-all">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Status Jadwal</span>
            <div class="flex items-center gap-1.5 font-black text-xs text-indigo-600 dark:text-indigo-400">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>
                <span x-text="scheduleStatusBadge">🟢 Sesi Presensi</span>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate" x-text="scheduleStatusSubText">Jam Server</p>
        </div>

        <!-- 4. Status Radius -->
        <div class="p-3.5 rounded-2xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-2xs space-y-1.5 transition-all">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Status Radius</span>
            <div class="flex items-center gap-1.5 font-black text-xs" :class="radiusStatusTextClass">
                <span class="w-2.5 h-2.5 rounded-full" :class="radiusStatusDotClass"></span>
                <span x-text="radiusStatusBadge">🟡 Menghitung...</span>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate" x-text="radiusStatusSubText">Maks {{ $schoolRadius }}m</p>
        </div>

        <!-- 5. Status Kesiapan Presensi -->
        <div class="p-3.5 rounded-2xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-2xs space-y-1.5 transition-all col-span-2 sm:col-span-1">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 block">Status Presensi</span>
            <div class="flex items-center gap-1.5 font-black text-xs" :class="readinessStatusTextClass">
                <span class="w-2.5 h-2.5 rounded-full" :class="readinessStatusDotClass"></span>
                <span x-text="readinessStatusBadge">Memvalidasi...</span>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate" x-text="readinessStatusSubText">Validasi Siap</p>
        </div>
    </div>

    <!-- MAIN TWO COLUMN SECTION: GPS RADAR & ATTENDANCE ACTIONS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT (COL 7): Real-time GPS Radar & Presensi Form -->
        <div class="lg:col-span-7 tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-5">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg shrink-0">
                        📍
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Radar GPS Geofence</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Deteksi koordinat satelit & radius presensi sekolah</p>
                    </div>
                </div>

                <button type="button" @click="requestLocation()" 
                        class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <span :class="isLocating ? 'animate-spin' : ''">🔄</span>
                    <span>Refresh GPS</span>
                </button>
            </div>

            <!-- GPS STATES DISPLAY (PART 4 & PART 5 COMPLIANT) -->

            <!-- State 0: Insecure HTTP Warning -->
            <template x-if="isInsecureHttp">
                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-xs">
                        <span>⚠️</span>
                        <span>Perhatian: Protokol HTTP Terdeteksi</span>
                    </div>
                    <p class="text-xs leading-relaxed">
                        Browser modern (Chrome/Safari) memblokir izin GPS pada koneksi HTTP biasa. Pastikan aplikasi dibuka melalui alamat <strong>HTTPS</strong> yang aman agar sensor satelit berfungsi optimal.
                    </p>
                </div>
            </template>

            <!-- State 1: Permission Not Requested (Prompt) -->
            <template x-if="gpsState === 'prompt'">
                <div class="p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-center space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-600 flex items-center justify-center text-xl font-bold">
                        📍
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-amber-900 dark:text-amber-100 uppercase tracking-wider">Izin Akses Lokasi Diperlukan</h4>
                        <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                            Aplikasi memerlukan izin akses lokasi untuk memvalidasi posisi Anda terhadap radius sekolah ({{ $schoolRadius }}m).
                        </p>
                    </div>
                    <button type="button" @click="requestLocation()" 
                            class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-md transition-all cursor-pointer">
                        Izinkan Akses Lokasi (Allow Location Access)
                    </button>
                </div>
            </template>

            <!-- State 2: Searching GPS / Loading -->
            <template x-if="gpsState === 'searching'">
                <div class="p-5 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800 text-center space-y-3">
                    <div class="w-8 h-8 mx-auto border-3 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                    <div>
                        <h4 class="text-xs font-extrabold text-indigo-900 dark:text-indigo-200 uppercase tracking-wider">Mencari Sinyal GPS Satelit...</h4>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300 mt-1" x-text="searchingMessage">Menghubungkan ke satelit koordinat perangkat...</p>
                    </div>
                </div>
            </template>

            <!-- State 3: Permission Denied -->
            <template x-if="gpsState === 'denied'">
                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 space-y-3">
                    <div class="flex items-center gap-2 font-bold text-xs text-rose-700 dark:text-rose-300">
                        <span>🔴</span>
                        <span>Izin Akses Lokasi Ditolak oleh Browser atau Perangkat</span>
                    </div>
                    <p class="text-xs leading-relaxed text-rose-800 dark:text-rose-300">
                        Aplikasi tidak dapat membaca koordinat Anda karena izin GPS diblokir.
                    </p>
                    <div class="p-3 bg-white/70 dark:bg-slate-900/50 rounded-xl text-[11px] text-slate-700 dark:text-slate-300 space-y-1 border border-rose-200/50">
                        <p class="font-bold">Cara Membuka Izin Lokasi:</p>
                        <p>1. Klik ikon <strong>Gembok / Setelan Situs (🔒)</strong> di samping URL browser.</p>
                        <p>2. Cari menu <strong>Izin / Permissions &rarr; Lokasi (Location)</strong> dan pilih <strong>Izinkan (Allow)</strong>.</p>
                        <p>3. Tekan tombol Coba Lagi di bawah.</p>
                    </div>
                    <div class="flex items-center gap-2 pt-1">
                        <button type="button" @click="requestLocation()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                            🔄 Coba Lagi
                        </button>
                        <button type="button" @click="mode = 'dinas_luar'" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                            💼 Beralih ke Mode Dinas Luar
                        </button>
                    </div>
                </div>
            </template>

            <!-- State 4: GPS Disabled / Position Unavailable -->
            <template x-if="gpsState === 'disabled'">
                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 space-y-3">
                    <div class="flex items-center gap-2 font-bold text-xs text-amber-700 dark:text-amber-300">
                        <span>⚠️</span>
                        <span>Layanan GPS Tidak Tersedia / Dinonaktifkan</span>
                    </div>
                    <p class="text-xs leading-relaxed text-amber-800 dark:text-amber-300">
                        Sensor GPS pada perangkat Anda dalam kondisi mati atau sinyal satelit tidak tertangkap di dalam ruangan.
                    </p>
                    <div class="p-3 bg-white/70 dark:bg-slate-900/50 rounded-xl text-[11px] text-slate-700 dark:text-slate-300 space-y-1">
                        <p class="font-bold">Panduan Pengaktifan:</p>
                        <p>&bull; Buka menu Setelan Cepat HP (geser layar dari atas ke bawah) &rarr; Nyalakan toggle <strong>Lokasi / GPS</strong>.</p>
                        <p>&bull; Pastikan mode akurasi tinggi aktif.</p>
                    </div>
                    <button type="button" @click="requestLocation()" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                        🔄 Coba Deteksi Ulang
                    </button>
                </div>
            </template>

            <!-- State 5: Permission Granted & Location Retrieved -->
            <template x-if="gpsState === 'connected'">
                <div class="space-y-4">
                    <!-- Status Banner GPS Connected -->
                    <div class="p-3.5 rounded-2xl flex items-center justify-between border transition-all"
                         :class="inRadius || mode === 'dinas_luar' ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800/60' : 'bg-rose-50 dark:bg-rose-950/30 border-rose-200 dark:border-rose-800/60'">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-3 h-3 rounded-full shrink-0" :class="inRadius || mode === 'dinas_luar' ? 'bg-emerald-500 animate-ping' : 'bg-rose-500 animate-ping'"></span>
                            <div>
                                <p class="text-xs font-black tracking-wide" :class="inRadius || mode === 'dinas_luar' ? 'text-emerald-800 dark:text-emerald-200' : 'text-rose-800 dark:text-rose-200'">
                                    <span x-text="inRadius || mode === 'dinas_luar' ? '🟢 GPS Terhubung & Siap Presensi' : '🔴 Di Luar Radius Presensi Sekolah'"></span>
                                </p>
                                <p class="text-[11px]" :class="inRadius || mode === 'dinas_luar' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                                    <span x-text="inRadius ? 'Posisi Anda terverifikasi di dalam area sekolah.' : (mode === 'dinas_luar' ? 'Mode dinas luar aktif (bebas geofence).' : 'Harap mendekat ke gerbang sekolah untuk melakukan presensi reguler.')"></span>
                                </p>
                            </div>
                        </div>
                        <span class="text-xs font-extrabold px-2.5 py-1 rounded-xl text-white font-mono"
                              :class="inRadius ? 'bg-emerald-600' : (mode === 'dinas_luar' ? 'bg-purple-600' : 'bg-rose-600')"
                              x-text="distanceMeters !== null ? distanceMeters + 'm' : '-'"></span>
                    </div>

                    <!-- Telemetry Details Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#24303F] border border-slate-200/70 dark:border-slate-700/60">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Koordinat Terkini</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200 block truncate mt-0.5" x-text="userLat ? userLat.toFixed(5) + ', ' + userLong.toFixed(5) : '-'"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#24303F] border border-slate-200/70 dark:border-slate-700/60">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Akurasi Satelit</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200 block mt-0.5" x-text="accuracy ? '± ' + Math.round(accuracy) + ' Meter' : '-'"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#24303F] border border-slate-200/70 dark:border-slate-700/60 col-span-2 sm:col-span-1">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Jarak ke Gerbang</span>
                            <span class="font-mono font-bold block mt-0.5" :class="inRadius ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                                  x-text="distanceMeters !== null ? distanceMeters + 'm (Maks ' + schoolRadius + 'm)' : '-'"></span>
                        </div>
                    </div>

                    <!-- Verified Address -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#24303F] border border-slate-200/70 dark:border-slate-700/60 text-xs">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Alamat / Lokasi Terdeteksi</span>
                        <p class="font-medium text-slate-800 dark:text-slate-200 mt-0.5 leading-relaxed" x-text="currentAddress || '{{ $schoolAddress }}'"></p>
                    </div>
                </div>
            </template>

            <!-- Mode Selector: Reguler vs Dinas Luar -->
            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Pilih Moda Kehadiran</label>
                <div class="grid grid-cols-2 gap-2 bg-slate-100 dark:bg-[#24303F] p-1.5 rounded-2xl">
                    <button type="button" @click="mode = 'reguler'"
                            :class="mode === 'reguler' ? 'bg-white dark:bg-[#1A222C] text-[#3C50E0] dark:text-indigo-400 shadow-sm font-black' : 'text-slate-600 dark:text-slate-400 font-semibold'"
                            class="py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>🏛️</span>
                        <span>WFO (Di Sekolah)</span>
                    </button>
                    <button type="button" @click="mode = 'dinas_luar'"
                            :class="mode === 'dinas_luar' ? 'bg-white dark:bg-[#1A222C] text-purple-600 dark:text-purple-400 shadow-sm font-black' : 'text-slate-600 dark:text-slate-400 font-semibold'"
                            class="py-2.5 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>💼</span>
                        <span>Dinas Luar</span>
                    </button>
                </div>
            </div>

            <!-- Dinas Luar Active Session Banner (Requirement 4) -->
            <div x-show="mode === 'dinas_luar'" x-cloak class="p-4 rounded-2xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-wider text-purple-600 dark:text-purple-400 flex items-center gap-1">
                        <span>💼</span>
                        <span>Mode Dinas / Penugasan Luar</span>
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-200 dark:bg-purple-900/60 text-purple-800 dark:text-purple-300">
                        Bebas Radius Geofence
                    </span>
                </div>
                <div class="text-xs text-purple-900 dark:text-purple-100 font-medium">
                    Anda sedang melakukan: 
                    <strong class="font-extrabold text-sm block sm:inline mt-0.5 sm:mt-0 text-purple-700 dark:text-purple-300" x-text="dinasSessionInfo.dinasLabel">
                        Absen Masuk (Dinas Luar)
                    </strong>
                </div>
                <p class="text-[11px] text-purple-700 dark:text-purple-300 leading-relaxed">
                    Sesi ditentukan otomatis berdasarkan jam server terkini (<span x-text="scheduleConfig.morning_open + '-' + scheduleConfig.morning_close">07.00-11.59</span> Masuk, <span x-text="scheduleConfig.dzuhur_open + '-' + scheduleConfig.dzuhur_close">12.00-13.30</span> Siang, <span x-text="scheduleConfig.afternoon_open + '-' + scheduleConfig.afternoon_close">14.00-18.00</span> Pulang). Anda tidak perlu memilih sesi sendiri.
                </p>
            </div>

            <!-- Dinas Luar Note Input -->
            <div x-show="mode === 'dinas_luar'" x-cloak class="space-y-1.5">
                <label class="block text-xs font-bold text-purple-700 dark:text-purple-300 uppercase tracking-wider">Keterangan / Nomor Surat Tugas Dinas Luar *</label>
                <input type="text" x-model="dinasNotes" placeholder="Contoh: Menghadiri Rapat Koordinasi KKG di Dinas Pendidikan..."
                       class="w-full text-xs px-4 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800 dark:bg-[#24303F] dark:text-white focus:ring-2 focus:ring-purple-500 outline-none">
            </div>

            <!-- ACTION ATTENDANCE SESSION CONTROLLER (PART 1 & PART 5 COMPLIANT) -->
            <div class="pt-2">
                @if(!empty($activeSession['holiday']['is_holiday']))
                    <!-- STATE: HOLIDAY -->
                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-center space-y-2">
                        <span class="text-3xl">🏖️</span>
                        <h4 class="text-xs font-black uppercase tracking-wider text-amber-900 dark:text-amber-200">Presensi Ditutup (Hari Libur)</h4>
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            Hari ini adalah <strong>{{ $activeSession['holiday']['reason'] }}</strong>. Tidak ada kewajiban presensi.
                        </p>
                    </div>

                @elseif($activeSession['type'] === 'outside_window')
                    <!-- STATE: OUTSIDE ATTENDANCE WINDOW -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-slate-700 text-center space-y-2">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-200/70 dark:bg-slate-700 flex items-center justify-center text-xl">
                            ⏱️
                        </div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200">Di Luar Jadwal Presensi</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                            {{ $activeSession['next_window'] ?? 'Saat ini tidak ada sesi presensi yang sedang berlangsung.' }}
                        </p>
                        <div class="pt-1">
                            <span class="inline-block px-3 py-1 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-[11px] font-bold">
                                Tombol Presensi Terkunci Otomatis
                            </span>
                        </div>
                    </div>

                @elseif($activeSession['is_already_done'])
                    <!-- STATE: SESSION COMPLETED (NO DUPLICATE ACTION) -->
                    <div class="p-5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center space-y-2">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl font-black shadow-xs">
                            ✓
                        </div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-emerald-900 dark:text-emerald-200">
                            {{ $activeSession['name'] }} Selesai
                        </h4>
                        <p class="text-xs text-emerald-700 dark:text-emerald-400">
                            @if($activeSession['type'] === 'check_in')
                                Anda telah berhasil melakukan presensi Masuk pada pukul <strong>{{ substr($todayAttendance->check_in, 0, 5) }} {{ $timezoneLabel }}</strong>.
                            @elseif($activeSession['type'] === 'midday')
                                Konfirmasi presensi Dzuhur telah tercatat pada pukul <strong>{{ substr($todayAttendance->midday_at ?? '12:00', 0, 5) }} {{ $timezoneLabel }}</strong>.
                            @elseif($activeSession['type'] === 'check_out')
                                Presensi kepulangan telah tercatat pada pukul <strong>{{ substr($todayAttendance->check_out, 0, 5) }} {{ $timezoneLabel }}</strong>. Seluruh presensi hari ini lengkap!
                            @else
                                Kehadiran sesi ini telah terverifikasi dalam sistem.
                            @endif
                        </p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 pt-1 font-medium">
                            Menunggu jadwal sesi berikutnya sesuai jam server.
                        </p>
                    </div>

                @else
                    <!-- STATE: ACTIVE SESSION READY (EXACTLY ONE ACTION BUTTON) -->
                    <div class="space-y-3">
                        <!-- GPS Status Helper when GPS is enabled in settings -->
                        @if(!empty($attendanceSettings['gps_enabled']))
                            <div x-show="mode === 'reguler' && gpsState !== 'connected'" class="p-3 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200 dark:border-amber-800/60 flex items-center justify-between text-xs text-amber-800 dark:text-amber-300">
                                <span class="flex items-center gap-1.5 font-bold">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                    <span x-text="gpsState === 'searching' ? 'Menghubungkan sinyal GPS satelit...' : (gpsState === 'prompt' ? 'Izin akses lokasi diperlukan' : 'GPS tidak aktif / terblokir')"></span>
                                </span>
                                <button type="button" @click="requestLocation()" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-[10px] font-bold cursor-pointer transition">
                                    Aktifkan GPS
                                </button>
                            </div>
                            <div x-show="mode === 'reguler' && gpsState === 'connected' && !inRadius" class="p-3 bg-rose-50 dark:bg-rose-950/30 rounded-xl border border-rose-200 dark:border-rose-800/60 flex items-center justify-between text-xs text-rose-800 dark:text-rose-300">
                                <span class="font-bold flex items-center gap-1.5">
                                    <span>📍</span>
                                    <span>Di luar radius sekolah (<span x-text="distanceMeters + 'm'"></span> / Maks {{ $schoolRadius }}m)</span>
                                </span>
                                <span class="text-[10px] font-medium text-rose-600 dark:text-rose-400">Dekati area sekolah</span>
                            </div>
                        @endif

                        <!-- The Single Valid Action Button -->
                        <button type="button" @click="submitAttendance()"
                                :disabled="isActionDisabled()"
                                class="w-full py-4 px-5 rounded-2xl font-black text-sm text-white shadow-lg transition-all flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none hover:scale-[1.01] active:scale-[0.99]"
                                :class="getButtonGradientClass()">
                            <span x-show="!isSubmitting">
                                @if($activeSession['type'] === 'check_in') 🌅
                                @elseif($activeSession['type'] === 'midday') ☀️
                                @elseif($activeSession['type'] === 'check_out') 🌇
                                @else 📍 @endif
                            </span>
                            <span x-show="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span x-text="getSubmitButtonLabel()">{{ $activeSession['action_label'] ?? 'Simpan Presensi' }}</span>
                        </button>

                        @if(($activeSession['delay_minutes'] ?? 0) > 0 && $activeSession['type'] === 'check_in')
                            <p class="text-[11px] text-center font-bold text-amber-600 dark:text-amber-400">
                                ⚠️ Anda melewati batas jam masuk ({{ $attendanceSettings['morning_late'] ?? '07:30' }}). Keterlambatan: {{ $activeSession['delay_minutes'] }} menit.
                            </p>
                        @endif
                    </div>
                @endif

                <p class="text-[11px] text-center text-slate-400 dark:text-slate-500 mt-2">
                    Presensi otomatis tervalidasi jam server &bull; {{ $schoolName }}
                </p>
            </div>

        </div>

        <!-- RIGHT (COL 5): BIOMETRIC SCANNER INFO & STATS -->
        <div class="lg:col-span-5 space-y-5">
            
            <!-- USB Fingerprint Scanner Interactive Terminal Card (Requirements 1 & 6) -->
            <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Scanner Sidik Jari USB</h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">DigitalPersona U.are.U 4500 (Local SDK)</p>
                        </div>
                    </div>
                    <button type="button" @click="refreshFingerprintScanner()" 
                            title="Deteksi Ulang Scanner"
                            class="p-2 rounded-xl bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition cursor-pointer text-xs">
                        🔄
                    </button>
                </div>

                <!-- Interactive Biometric Terminal Box -->
                <div class="p-4 rounded-2xl border transition-all text-center space-y-3"
                     :class="fpBoxClass">
                    <div class="relative w-16 h-16 mx-auto rounded-full flex items-center justify-center transition-all"
                         :class="fpIconContainerClass">
                        <svg class="w-8 h-8" :class="fpIconClass" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/>
                        </svg>
                        <span x-show="fpState === 'waiting_finger'" class="absolute inset-0 rounded-full border-2 border-amber-400 animate-ping opacity-75"></span>
                        <span x-show="fpState === 'reading'" class="absolute inset-0 rounded-full border-2 border-blue-500 animate-spin opacity-90"></span>
                    </div>

                    <div>
                        <div class="font-extrabold text-xs" :class="fpStatusTextClass" x-text="fpStatusBadge">
                            🔴 Scanner tidak ditemukan
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5" x-text="fpInstructionText">
                            Hubungkan scanner U.are.U 4500 USB atau jalankan DigitalPersona Local Device Access di https://localhost:52181.
                        </p>
                    </div>

                    <div class="pt-1 text-[10px] text-slate-400 font-medium">
                        🛡️ Presensi sidik jari sekolah diverifikasi langsung tanpa syarat sinyal GPS.
                    </div>
                </div>
            </div>

            <!-- Monthly Recap Card -->
            <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Rekap Kehadiran Bulan Ini</h4>
                        <p class="text-[11px] text-slate-500">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                    </div>
                    <span class="text-sm font-black text-indigo-600 dark:text-indigo-400">{{ $attendanceRate }}%</span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/40">
                        <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 uppercase block">Tepat Waktu</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-0.5 block">{{ $presentCount }} Hari</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/40">
                        <span class="text-[10px] font-bold text-amber-700 dark:text-amber-300 uppercase block">Terlambat</span>
                        <span class="text-xl font-black text-amber-600 dark:text-amber-400 font-mono mt-0.5 block">{{ $lateCount }} Hari</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-800/40">
                        <span class="text-[10px] font-bold text-blue-700 dark:text-blue-300 uppercase block">Sakit</span>
                        <span class="text-xl font-black text-blue-600 dark:text-blue-400 font-mono mt-0.5 block">{{ $sickCount }} Hari</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200/60 dark:border-purple-800/40">
                        <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300 uppercase block">Izin</span>
                        <span class="text-xl font-black text-purple-600 dark:text-purple-400 font-mono mt-0.5 block">{{ $permissionCount }} Hari</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ATTENDANCE HISTORY TABLE (RIWAYAT PRESENSI SAYA) -->
    <div class="tailadmin-card bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 dark:text-white text-sm uppercase tracking-wider">Riwayat Kehadiran Terakhir (14 Hari Terakhir)</h3>
            <span class="text-xs text-slate-500 font-medium">Hanya data pribadi Anda</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-[#24303F] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Pagi (Masuk)</th>
                        <th class="px-6 py-3.5">Siang (Dzuhur)</th>
                        <th class="px-6 py-3.5">Sore (Pulang)</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Moda / Lokasi</th>
                        <th class="px-6 py-3.5">Metode</th>
                        <th class="px-6 py-3.5 text-right">Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                    @forelse($recentAttendances as $att)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-bold font-mono">
                                {{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $att->check_in ? substr($att->check_in, 0, 5) : '-' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                                {{ $att->midday_at ? substr($att->midday_at, 0, 5) : '-' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $att->check_out ? substr($att->check_out, 0, 5) : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($att->status === 'present')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">Tepat Waktu</span>
                                @elseif($att->status === 'late')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">Terlambat</span>
                                @elseif($att->status === 'sick')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400">Sakit</span>
                                @elseif($att->status === 'permission')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400">Izin</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600">Alpa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="capitalize font-medium">{{ $att->work_location === 'outstation' ? 'Dinas Luar' : ($att->work_location === 'home' ? 'WFH' : 'WFO Sekolah') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold {{ $att->method === 'fingerprint' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-cyan-100 dark:bg-cyan-950 text-cyan-700 dark:text-cyan-300' }}">
                                    {{ $att->method === 'fingerprint' ? 'Sidik Jari USB' : 'GPS Mobile' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($att->verification_status)
                                    <span class="text-emerald-600 dark:text-emerald-400 font-extrabold text-[11px] inline-flex items-center gap-1">
                                        ✓ Terverifikasi
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400 text-xs">
                                Belum ada riwayat presensi yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ATTENDANCE CONFIRMATION MODAL -->
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white dark:bg-[#1A222C] rounded-3xl max-w-md w-full p-6 text-center space-y-4 shadow-2xl border border-slate-200 dark:border-slate-800"
             @click.outside="showConfirmModal = false">
            <div class="w-16 h-16 mx-auto rounded-3xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center text-3xl font-bold shadow-lg shadow-emerald-500/20">
                ✓
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white" x-text="confirmTitle">Presensi Berhasil Disimpan!</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1" x-text="confirmMessage"></p>
            </div>
            <div class="p-3.5 bg-slate-50 dark:bg-[#24303F] rounded-2xl text-xs space-y-1 text-left font-medium border border-slate-100 dark:border-slate-700">
                <div class="flex justify-between">
                    <span class="text-slate-400">Pendidik:</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Waktu:</span>
                    <span class="font-bold font-mono text-emerald-600" x-text="currentTime + ' {{ $timezoneLabel }}'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Sesi:</span>
                    <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="activeSessionName"></span>
                </div>
            </div>
            <button type="button" @click="window.location.reload()" 
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md transition">
                Selesai &amp; Perbarui Halaman
            </button>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    if (!window.WebSdkCore) {
        window.WebSdkCore = {
            log: function() {},
            channelOptions: {},
            channelClient: {}
        };
    }
</script>
<!-- DigitalPersona Web SDK Official Bundles -->
<script src="{{ asset('vendor/digitalpersona/websdk.client.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/digitalpersona/dp.core.umd.min.js') }}"></script>
<script src="{{ asset('vendor/digitalpersona/dp.devices.umd.min.js') }}"></script>

<!-- Modular Attendance Services -->
<script src="{{ asset('js/attendance/LoggerService.js') }}"></script>
<script src="{{ asset('js/attendance/PermissionService.js') }}"></script>
<script src="{{ asset('js/attendance/RadiusService.js') }}"></script>
<script src="{{ asset('js/attendance/GPSService.js') }}"></script>
<script src="{{ asset('js/attendance/ScheduleService.js') }}"></script>
<script src="{{ asset('js/attendance/FingerprintService.js') }}"></script>
<script src="{{ asset('js/attendance/AttendanceService.js') }}"></script>

<script>
function teacherAttendanceApp() {
    return {
        // Core Coordinates & Geofence
        schoolLat: {{ $schoolLat }},
        schoolLong: {{ $schoolLong }},
        schoolRadius: {{ $schoolRadius }},
        schoolName: '{{ $schoolName }}',
        schoolAddress: '{{ $schoolAddress }}',
        timezoneLabel: '{{ $timezoneLabel }}',
        userId: {{ $user->id }},
        csrfToken: '{{ csrf_token() }}',

        // Dynamic State
        currentTime: '{{ now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('H:i:s') }}',
        activeSessionType: '{{ $activeSession['type'] ?? 'check_in' }}',
        activeSessionName: '{{ $activeSession['name'] ?? 'Presensi' }}',
        isAlreadyDone: {{ ($activeSession['is_already_done'] ?? false) ? 'true' : 'false' }},
        isHoliday: {{ (!empty($activeSession['holiday']['is_holiday'])) ? 'true' : 'false' }},
        gpsRequired: {{ !empty($attendanceSettings['gps_enabled']) ? 'true' : 'false' }},
        scheduleConfig: @json($attendanceSettings),

        // GPS Telemetry
        gpsState: 'searching', // 'prompt' | 'searching' | 'retrying' | 'connected' | 'denied' | 'disabled'
        isInsecureHttp: false,
        isLocating: false,
        isSubmitting: false,
        searchingMessage: 'Mendeteksi koordinat GPS satelit...',
        userLat: null,
        userLong: null,
        accuracy: null,
        distanceMeters: null,
        inRadius: false,
        currentAddress: '',

        // Attendance Mode & Dinas Luar (Requirement 4)
        mode: 'reguler', // 'reguler' | 'dinas_luar'
        dinasNotes: '',
        dinasSessionInfo: {
            sessionType: 'check_in',
            sessionName: 'Sesi Pagi (Masuk)',
            dinasLabel: 'Absen Masuk (Dinas Luar)',
            isActive: true
        },

        // Fingerprint Hardware Scanner (Requirement 1)
        fpState: 'device_disconnected', // 'device_disconnected' | 'device_connected' | 'waiting_finger' | 'reading' | 'sample_acquired' | 'service_unavailable' | 'error'
        fpStatusBadge: '🔴 Scanner tidak ditemukan',
        fpStatusTextClass: 'text-rose-600 dark:text-rose-400',
        fpStatusDotClass: 'bg-rose-500',
        fpStatusSubText: 'U.are.U 4500 (Local Service)',
        fpInstructionText: 'Hubungkan scanner U.are.U 4500 USB atau jalankan DigitalPersona Local Device Access di https://localhost:52181.',
        fpBoxClass: 'bg-slate-50/50 dark:bg-slate-900/30 border-slate-200 dark:border-slate-800',
        fpIconContainerClass: 'bg-slate-100 dark:bg-slate-800 text-slate-400',
        fpIconClass: 'text-slate-400',

        // Status Panel Telemetry (Requirement 8)
        gpsStatusBadge: '🟡 Memeriksa...',
        gpsStatusTextClass: 'text-amber-600 dark:text-amber-400',
        gpsStatusDotClass: 'bg-amber-500 animate-ping',
        gpsStatusSubText: 'Izin Browser',

        scheduleStatusBadge: '🟢 Sesi Presensi',
        scheduleStatusSubText: 'Jam Server',

        radiusStatusBadge: '🟡 Menghitung...',
        radiusStatusTextClass: 'text-amber-600 dark:text-amber-400',
        radiusStatusDotClass: 'bg-amber-500',
        radiusStatusSubText: 'Maks {{ $schoolRadius }}m',

        readinessStatusBadge: 'Memvalidasi...',
        readinessStatusTextClass: 'text-slate-600 dark:text-slate-300',
        readinessStatusDotClass: 'bg-slate-400',
        readinessStatusSubText: 'Validasi Sistem',

        // Confirmation Modal
        showConfirmModal: false,
        confirmTitle: '',
        confirmMessage: '',

        init() {
            // 1. Check secure context
            this.isInsecureHttp = !AttendancePermissionService.isSecure;
            if (this.isInsecureHttp) {
                AttendanceLogger.warn('PERMISSION', 'Insecure HTTP context detected. Browser may restrict GPS.');
            }

            // 2. Initialize ScheduleService (Server Clock & Sesi Aktif)
            const initialServerTime = '{{ now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('H:i:s') }}';
            const initialServerDate = '{{ now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('Y-m-d') }}';
            AttendanceScheduleService.init({
                schedule: this.scheduleConfig,
                timezoneLabel: this.timezoneLabel,
                serverTime: initialServerTime,
                serverDate: initialServerDate,
                activeSession: {
                    type: this.activeSessionType,
                    name: this.activeSessionName,
                    is_already_done: this.isAlreadyDone,
                    is_holiday: this.isHoliday
                }
            });

            AttendanceScheduleService.on('tick', (timeStr) => {
                this.currentTime = timeStr;
                this.updateDinasSessionInfo();
                this.updateStatusPanel();
            });

            // Heartbeat ticker to guarantee clock always advances
            setInterval(() => {
                if (window.AttendanceScheduleService && typeof AttendanceScheduleService.getServerTimeString === 'function') {
                    const t = AttendanceScheduleService.getServerTimeString();
                    if (t && t !== '00:00:00') {
                        this.currentTime = t;
                    }
                }
            }, 1000);

            AttendanceScheduleService.on('sessionChange', (newSession) => {
                AttendanceLogger.schedule('Active session updated by server:', newSession);
                this.activeSessionType = newSession.type;
                this.activeSessionName = newSession.name;
                this.isAlreadyDone = Boolean(newSession.is_already_done);
                this.updateDinasSessionInfo();
                this.updateStatusPanel();
            });

            // 3. Initialize AttendanceService Config
            AttendanceUnifiedService.init({
                csrfToken: this.csrfToken,
                userId: this.userId,
                school: {
                    latitude: this.schoolLat,
                    longitude: this.schoolLong,
                    radius: this.schoolRadius,
                    name: this.schoolName
                }
            });

            // 4. Initialize GPS Service with permission lifecycle (Requirement 2)
            this.setupGpsListeners();
            if (this.gpsRequired) {
                AttendanceGPSService.init().catch(() => {});
            } else {
                this.gpsState = 'connected';
                this.inRadius = true;
                this.updateStatusPanel();
            }

            // 5. Initialize DigitalPersona Fingerprint Web SDK (Requirement 1)
            this.setupFingerprintListeners();
            // Allow small tick for DOM and SDK scripts to hydrate
            setTimeout(() => {
                AttendanceFingerprintService.init().catch(() => {});
            }, 600);

            this.updateDinasSessionInfo();
            this.updateStatusPanel();
        },

        setupGpsListeners() {
            AttendanceGPSService.on('stateChange', (state, payload) => {
                this.gpsState = state;
                this.isLocating = (state === 'searching' || state === 'retrying');

                if (state === 'searching') {
                    this.searchingMessage = 'Menghubungkan ke satelit GPS presisi tinggi (15s timeout)...';
                } else if (state === 'retrying') {
                    this.searchingMessage = `Mengoptimalkan sinyal satelit (Percobaan ${payload?.retry || 1}/${payload?.maxRetries || 3})...`;
                }

                this.updateStatusPanel();
            });

            AttendanceGPSService.on('position', (coords) => {
                this.userLat = coords.latitude;
                this.userLong = coords.longitude;
                this.accuracy = coords.accuracy || 10;
                this.gpsState = 'connected';
                this.isLocating = false;

                // Calculate distance via RadiusService
                this.distanceMeters = AttendanceRadiusService.calculateDistance(
                    this.schoolLat,
                    this.schoolLong,
                    this.userLat,
                    this.userLong
                );

                this.inRadius = AttendanceRadiusService.isWithinRadius(this.distanceMeters, this.schoolRadius);

                if (this.inRadius) {
                    this.currentAddress = this.schoolAddress;
                } else {
                    this.currentAddress = `Koordinat Satelit: ${this.userLat.toFixed(5)}, ${this.userLong.toFixed(5)}`;
                }

                this.updateStatusPanel();
            });

            AttendanceGPSService.on('error', (err) => {
                this.isLocating = false;
                this.updateStatusPanel();
            });
        },

        setupFingerprintListeners() {
            AttendanceFingerprintService.on('statusChange', (data) => {
                this.fpState = data.status;
                this.fpStatusBadge = data.badge;

                if (data.status === 'device_connected') {
                    this.fpStatusTextClass = 'text-emerald-600 dark:text-emerald-400';
                    this.fpStatusDotClass = 'bg-emerald-500';
                    this.fpStatusSubText = 'Scanner Terhubung (Siap)';
                    this.fpInstructionText = 'Scanner U.are.U 4500 aktif. Tempelkan jari Anda pada prisma sensor untuk presensi.';
                    this.fpBoxClass = 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-800';
                    this.fpIconContainerClass = 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-300';
                    this.fpIconClass = 'text-emerald-600 dark:text-emerald-300';

                } else if (data.status === 'waiting_finger') {
                    this.fpStatusTextClass = 'text-amber-600 dark:text-amber-400';
                    this.fpStatusDotClass = 'bg-amber-500 animate-pulse';
                    this.fpStatusSubText = 'Menunggu Sidik Jari';
                    this.fpInstructionText = 'Sensor aktif. Silakan tempelkan jari pada sensor scanner biometrik.';
                    this.fpBoxClass = 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-300 dark:border-amber-700';
                    this.fpIconContainerClass = 'bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-300';
                    this.fpIconClass = 'text-amber-600 dark:text-amber-300';

                } else if (data.status === 'reading') {
                    this.fpStatusTextClass = 'text-blue-600 dark:text-blue-400';
                    this.fpStatusDotClass = 'bg-blue-500 animate-spin';
                    this.fpStatusSubText = 'Sedang Membaca Sensor...';
                    this.fpInstructionText = 'Jari terdeteksi. Memindai kontur sidik jari dan kualitas citra...';
                    this.fpBoxClass = 'bg-blue-50/50 dark:bg-blue-950/20 border-blue-300 dark:border-blue-700';
                    this.fpIconContainerClass = 'bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300';
                    this.fpIconClass = 'text-blue-600 dark:text-blue-300';

                } else if (data.status === 'sample_acquired') {
                    this.fpStatusTextClass = 'text-emerald-600 dark:text-emerald-400 font-black';
                    this.fpStatusDotClass = 'bg-emerald-500 animate-bounce';
                    this.fpStatusSubText = 'Berhasil Dibaca!';
                    this.fpInstructionText = 'Sidik jari berhasil dibaca. Mengirimkan verifikasi biometrik ke server sekolah...';
                    this.fpBoxClass = 'bg-emerald-100/60 dark:bg-emerald-950/40 border-emerald-400 dark:border-emerald-600';
                    this.fpIconContainerClass = 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30';
                    this.fpIconClass = 'text-white';

                } else {
                    // Disconnected or Service unavailable
                    this.fpStatusTextClass = 'text-rose-600 dark:text-rose-400';
                    this.fpStatusDotClass = 'bg-rose-500';
                    this.fpStatusSubText = (data.status === 'service_unavailable') ? 'Service Port 52181 Mati' : 'U.are.U 4500 Belum Tersambung';
                    this.fpInstructionText = 'Hubungkan scanner USB U.are.U 4500 atau pastikan service DigitalPersona Local Device Access aktif pada https://localhost:52181.';
                    this.fpBoxClass = 'bg-slate-50 dark:bg-[#24303F] border-slate-200 dark:border-slate-700';
                    this.fpIconContainerClass = 'bg-slate-100 dark:bg-slate-800 text-slate-400';
                    this.fpIconClass = 'text-slate-400';
                }

                this.updateStatusPanel();
            });

            // Auto-Submit attendance when finger is captured on this scanner (Requirement 1 & 6)
            AttendanceFingerprintService.on('sampleCaptured', async (sampleData) => {
                if (this.isSubmitting) return;

                AttendanceLogger.fingerprint('Fingerprint captured on Presensi Mandiri terminal. Submitting directly to server...');
                this.isSubmitting = true;

                try {
                    const result = await AttendanceUnifiedService.submitFingerprintAttendance(sampleData, {
                        userId: this.userId,
                        deviceName: 'HID DigitalPersona U.are.U 4500'
                    });

                    if (result.success) {
                        this.confirmTitle = 'Presensi Biometrik Berhasil!';
                        this.confirmMessage = result.message || 'Sidik jari Anda telah terverifikasi dan tercatat ke sistem.';
                        this.showConfirmModal = true;
                    } else {
                        alert(result.message || 'Sidik jari tidak dikenali oleh sistem presensi.');
                    }
                } catch (e) {
                    alert('Gagal memproses verifikasi sidik jari ke server sekolah.');
                } finally {
                    this.isSubmitting = false;
                }
            });
        },

        updateDinasSessionInfo() {
            this.dinasSessionInfo = AttendanceScheduleService.getDinasLuarSessionInfo();
        },

        updateStatusPanel() {
            // 1. Status GPS Badge
            if (this.gpsState === 'connected') {
                this.gpsStatusBadge = '🟢 GPS Aktif';
                this.gpsStatusTextClass = 'text-emerald-600 dark:text-emerald-400';
                this.gpsStatusDotClass = 'bg-emerald-500';
                this.gpsStatusSubText = this.accuracy ? `Akurasi ± ${Math.round(this.accuracy)}m` : 'Satelit Terkunci';
            } else if (this.gpsState === 'searching' || this.gpsState === 'retrying') {
                this.gpsStatusBadge = '🟡 Mencari GPS...';
                this.gpsStatusTextClass = 'text-amber-600 dark:text-amber-400';
                this.gpsStatusDotClass = 'bg-amber-500 animate-ping';
                this.gpsStatusSubText = 'Mengunci Satelit';
            } else if (this.gpsState === 'denied') {
                this.gpsStatusBadge = '🔴 Izin Ditolak';
                this.gpsStatusTextClass = 'text-rose-600 dark:text-rose-400';
                this.gpsStatusDotClass = 'bg-rose-500';
                this.gpsStatusSubText = 'Buka Izin Lokasi';
            } else {
                this.gpsStatusBadge = '⚠️ GPS Mati';
                this.gpsStatusTextClass = 'text-amber-600 dark:text-amber-400';
                this.gpsStatusDotClass = 'bg-amber-500';
                this.gpsStatusSubText = 'Sensor Nonaktif';
            }

            // 2. Status Jadwal Badge
            const currentSession = AttendanceScheduleService.resolveCurrentSession();
            if (currentSession.type === 'check_in') {
                this.scheduleStatusBadge = '🟢 Sesi Pagi (Masuk)';
                this.scheduleStatusSubText = `${this.scheduleConfig.morning_open} - ${this.scheduleConfig.morning_close} ${this.timezoneLabel}`;
            } else if (currentSession.type === 'midday') {
                this.scheduleStatusBadge = '☀️ Sesi Siang (Dzuhur)';
                this.scheduleStatusSubText = `${this.scheduleConfig.dzuhur_open} - ${this.scheduleConfig.dzuhur_close} ${this.timezoneLabel}`;
            } else if (currentSession.type === 'check_out') {
                this.scheduleStatusBadge = '🌇 Sesi Sore (Pulang)';
                this.scheduleStatusSubText = `${this.scheduleConfig.afternoon_open} - ${this.scheduleConfig.afternoon_close} ${this.timezoneLabel}`;
            } else {
                this.scheduleStatusBadge = '⏱️ Di Luar Jadwal';
                this.scheduleStatusSubText = 'Presensi Ditutup';
            }

            // 3. Status Radius Badge
            if (this.mode === 'dinas_luar') {
                this.radiusStatusBadge = '💼 Bebas Radius';
                this.radiusStatusTextClass = 'text-purple-600 dark:text-purple-400';
                this.radiusStatusDotClass = 'bg-purple-500';
                this.radiusStatusSubText = 'Dinas Luar Aktif';
            } else if (this.inRadius) {
                this.radiusStatusBadge = '🟢 Dalam Area';
                this.radiusStatusTextClass = 'text-emerald-600 dark:text-emerald-400';
                this.radiusStatusDotClass = 'bg-emerald-500';
                this.radiusStatusSubText = this.distanceMeters !== null ? `Jarak: ${this.distanceMeters}m` : 'Area Sekolah';
            } else if (this.distanceMeters !== null) {
                this.radiusStatusBadge = '🔴 Di Luar Radius';
                this.radiusStatusTextClass = 'text-rose-600 dark:text-rose-400';
                this.radiusStatusDotClass = 'bg-rose-500';
                this.radiusStatusSubText = `${this.distanceMeters}m (Maks ${this.schoolRadius}m)`;
            } else {
                this.radiusStatusBadge = '🟡 Menghitung...';
                this.radiusStatusTextClass = 'text-amber-600 dark:text-amber-400';
                this.radiusStatusDotClass = 'bg-amber-500 animate-pulse';
                this.radiusStatusSubText = 'Menunggu GPS';
            }

            // 4. Status Kesiapan Presensi
            if (this.isAlreadyDone) {
                this.readinessStatusBadge = '✓ Sesi Selesai';
                this.readinessStatusTextClass = 'text-emerald-600 dark:text-emerald-400 font-black';
                this.readinessStatusDotClass = 'bg-emerald-500';
                this.readinessStatusSubText = 'Tercatat di Server';
            } else if (currentSession.type === 'outside_window') {
                this.readinessStatusBadge = 'Terkunci (Jadwal)';
                this.readinessStatusTextClass = 'text-slate-500 dark:text-slate-400';
                this.readinessStatusDotClass = 'bg-slate-400';
                this.readinessStatusSubText = 'Menunggu Sesi Dibuka';
            } else if (this.mode === 'dinas_luar') {
                this.readinessStatusBadge = 'Siap Presensi Dinas';
                this.readinessStatusTextClass = 'text-purple-600 dark:text-purple-400 font-black';
                this.readinessStatusDotClass = 'bg-purple-500 animate-pulse';
                this.readinessStatusSubText = 'Lampirkan Catatan';
            } else if (this.inRadius) {
                this.readinessStatusBadge = '🟢 Siap Presensi';
                this.readinessStatusTextClass = 'text-emerald-600 dark:text-emerald-400 font-black';
                this.readinessStatusDotClass = 'bg-emerald-500 animate-ping';
                this.readinessStatusSubText = 'Geofence Valid';
            } else {
                this.readinessStatusBadge = 'Menunggu Validasi';
                this.readinessStatusTextClass = 'text-amber-600 dark:text-amber-400';
                this.readinessStatusDotClass = 'bg-amber-500';
                this.readinessStatusSubText = 'Periksa GPS / Area';
            }
        },

        // Request Location / Real Refresh GPS (Requirement 3)
        async requestLocation() {
            try {
                this.isLocating = true;
                await AttendanceGPSService.refreshLocation();
            } catch (err) {
                AttendanceLogger.warn('GPS', 'Refresh GPS failed:', err.message);
            } finally {
                this.isLocating = false;
                this.updateStatusPanel();
            }
        },

        // Manual Re-detect Scanner (Requirement 1)
        async refreshFingerprintScanner() {
            try {
                await AttendanceFingerprintService.refreshScanner();
            } catch (e) {
                AttendanceLogger.warn('FINGERPRINT', 'Scanner refresh failed:', e);
            }
        },

        isActionDisabled() {
            if (this.isSubmitting) return true;
            if (this.isHoliday) return true;
            if (this.activeSessionType === 'outside_window') return true;
            if (this.isAlreadyDone) return true;

            if (this.mode === 'reguler' && this.gpsRequired) {
                if (this.gpsState !== 'connected' || !this.inRadius) return true;
            }

            if (this.mode === 'dinas_luar' && !this.dinasNotes.trim()) {
                return true;
            }

            return false;
        },

        getButtonGradientClass() {
            if (this.isActionDisabled()) return 'bg-slate-400 opacity-70 cursor-not-allowed';

            if (this.mode === 'dinas_luar') {
                return 'bg-gradient-to-r from-purple-600 to-indigo-600 shadow-purple-600/30';
            }

            if (this.activeSessionType === 'check_in') {
                return 'bg-gradient-to-r from-emerald-600 to-teal-600 shadow-emerald-600/30';
            }
            if (this.activeSessionType === 'midday') {
                return 'bg-gradient-to-r from-amber-500 to-orange-500 shadow-amber-500/30';
            }
            if (this.activeSessionType === 'check_out') {
                return 'bg-gradient-to-r from-indigo-600 to-violet-600 shadow-indigo-600/30';
            }
            return 'bg-gradient-to-r from-indigo-600 to-teal-600 shadow-indigo-600/30';
        },

        getSubmitButtonLabel() {
            if (this.isSubmitting) return 'Memverifikasi Presensi ke Server...';

            // Dinas Luar mode explicit label (Requirement 4)
            if (this.mode === 'dinas_luar') {
                if (this.activeSessionType === 'check_in') return '💼 Simpan Absen Masuk (Dinas Luar)';
                if (this.activeSessionType === 'midday') return '💼 Simpan Absen Siang (Dinas Luar)';
                if (this.activeSessionType === 'check_out') return '💼 Simpan Absen Pulang (Dinas Luar)';
                return `💼 Simpan Presensi (${this.dinasSessionInfo.dinasLabel})`;
            }

            // Reguler mode
            if (this.activeSessionType === 'check_in') return '🌅 Presensi Masuk (Check-In)';
            if (this.activeSessionType === 'midday') return '☀️ Konfirmasi Presensi Dzuhur';
            if (this.activeSessionType === 'check_out') return '🌇 Presensi Pulang (Check-Out)';
            return 'Simpan Presensi (' + this.activeSessionName + ')';
        },

        async submitAttendance() {
            if (this.isActionDisabled()) return;

            this.isSubmitting = true;

            try {
                const currentSession = AttendanceScheduleService.resolveCurrentSession();
                const result = await AttendanceUnifiedService.submitGpsAttendance({
                    mode: this.mode,
                    session: currentSession,
                    coords: AttendanceGPSService.currentCoords,
                    dinasNotes: this.dinasNotes
                });

                if (result.success) {
                    this.confirmTitle = 'Presensi Berhasil Disimpan!';
                    this.confirmMessage = result.message || 'Presensi Anda telah tercatat dan tersinkronisasi ke server sekolah.';
                    this.showConfirmModal = true;
                } else {
                    alert(result.message || 'Gagal menyimpan presensi.');
                }
            } catch (err) {
                alert(err.message || 'Terjadi kendala jaringan saat menghubungi server.');
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endsection

@push('scripts')
<script>
// Fallback redundant push
</script>
@endpush

