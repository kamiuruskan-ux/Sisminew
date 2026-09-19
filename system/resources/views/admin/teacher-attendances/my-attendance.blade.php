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

            <!-- Dinas Luar Note Input -->
            <div x-show="mode === 'dinas_luar'" x-cloak class="space-y-1.5">
                <label class="block text-xs font-bold text-purple-700 dark:text-purple-300 uppercase tracking-wider">Keterangan / Surat Tugas Dinas Luar *</label>
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
            
            <!-- USB Fingerprint Terminal Info Card -->
            <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Terminal Sidik Jari USB Fisik</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">HID DigitalPersona 4500</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Tersedia mesin scanner biometrik fisik di meja piket / gerbang sekolah. Anda dapat melakukan presensi instan tanpa ponsel dengan menempelkan jari pada scanner terminal.
                </p>
                <div class="p-3 bg-slate-50 dark:bg-[#24303F] rounded-2xl border border-slate-100 dark:border-slate-700/60 flex items-center justify-between text-xs">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Status Terminal:</span>
                    <span class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Aktif di Sekolah
                    </span>
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
function teacherAttendanceApp() {
    return {
        currentTime: '00:00:00',
        schoolLat: {{ $schoolLat }},
        schoolLong: {{ $schoolLong }},
        schoolRadius: {{ $schoolRadius }},
        activeSessionType: '{{ $activeSession['type'] ?? 'check_in' }}',
        activeSessionName: '{{ $activeSession['name'] ?? 'Presensi' }}',
        isAlreadyDone: {{ ($activeSession['is_already_done'] ?? false) ? 'true' : 'false' }},
        isHoliday: {{ (!empty($activeSession['holiday']['is_holiday'])) ? 'true' : 'false' }},
        gpsRequired: {{ !empty($attendanceSettings['gps_enabled']) ? 'true' : 'false' }},
        schedule: @json($attendanceSettings),

        // GPS States: 'prompt', 'searching', 'connected', 'denied', 'disabled'
        gpsState: 'searching',
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

        mode: 'reguler',
        dinasNotes: '',

        showConfirmModal: false,
        confirmTitle: '',
        confirmMessage: '',

        init() {
            this.startClock();
            this.checkProtocol();
            if (this.gpsRequired) {
                this.detectPermissionAndLocate();
            } else {
                this.gpsState = 'connected';
                this.inRadius = true;
            }
        },

        startClock() {
            const update = () => {
                const d = new Date();
                this.currentTime = d.toLocaleTimeString('id-ID', { hour12: false });
            };
            update();
            setInterval(update, 1000);
        },

        checkProtocol() {
            if (!window.isSecureContext && location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                this.isInsecureHttp = true;
            }
        },

        async detectPermissionAndLocate() {
            if (!navigator.geolocation) {
                this.gpsState = 'disabled';
                return;
            }

            if (navigator.permissions && navigator.permissions.query) {
                try {
                    const status = await navigator.permissions.query({ name: 'geolocation' });
                    this.handlePermissionStatus(status.state);
                    status.onchange = () => {
                        this.handlePermissionStatus(status.state);
                    };
                    return;
                } catch (e) {
                    // Fallback directly to requesting location
                }
            }

            this.requestLocation();
        },

        handlePermissionStatus(state) {
            if (state === 'granted') {
                this.requestLocation();
            } else if (state === 'denied') {
                this.gpsState = 'denied';
                this.isLocating = false;
            } else {
                this.gpsState = 'prompt';
                this.isLocating = false;
            }
        },

        requestLocation() {
            if (!navigator.geolocation) {
                this.gpsState = 'disabled';
                return;
            }

            this.isLocating = true;
            this.gpsState = 'searching';
            this.searchingMessage = 'Menghubungkan ke satelit GPS presisi tinggi...';

            navigator.geolocation.getCurrentPosition(
                (pos) => this.onPositionSuccess(pos),
                (err) => {
                    this.searchingMessage = 'Mengoptimalkan koordinat via jaringan seluler / Wi-Fi...';
                    navigator.geolocation.getCurrentPosition(
                        (fallbackPos) => this.onPositionSuccess(fallbackPos),
                        (finalErr) => this.onPositionError(finalErr),
                        { enableHighAccuracy: false, timeout: 12000, maximumAge: 60000 }
                    );
                },
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 10000 }
            );
        },

        onPositionSuccess(pos) {
            this.isLocating = false;
            this.userLat = pos.coords.latitude;
            this.userLong = pos.coords.longitude;
            this.accuracy = pos.coords.accuracy || 10;
            this.gpsState = 'connected';

            this.calculateDistance();

            if (this.inRadius) {
                this.currentAddress = '{{ $schoolAddress }}';
            } else {
                this.currentAddress = 'Koordinat: ' + this.userLat.toFixed(5) + ', ' + this.userLong.toFixed(5);
            }
        },

        onPositionError(err) {
            this.isLocating = false;
            if (err.code === err.PERMISSION_DENIED) {
                this.gpsState = 'denied';
            } else if (err.code === err.POSITION_UNAVAILABLE) {
                this.gpsState = 'disabled';
            } else if (err.code === err.TIMEOUT) {
                this.gpsState = 'disabled';
            } else {
                this.gpsState = 'disabled';
            }
        },

        calculateDistance() {
            if (!this.userLat || !this.userLong) return;
            const R = 6371e3; // meters
            const φ1 = (this.schoolLat * Math.PI) / 180;
            const φ2 = (this.userLat * Math.PI) / 180;
            const Δφ = ((this.userLat - this.schoolLat) * Math.PI) / 180;
            const Δλ = ((this.userLong - this.schoolLong) * Math.PI) / 180;

            const a =
                Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            this.distanceMeters = Math.round(R * c);

            this.inRadius = this.distanceMeters <= this.schoolRadius;
        },

        isActionDisabled() {
            if (this.isSubmitting) return true;
            if (this.isHoliday) return true;
            if (this.activeSessionType === 'outside_window') return true;
            if (this.isAlreadyDone) return true;
            if (this.mode === 'reguler' && this.gpsRequired) {
                if (this.gpsState !== 'connected' || !this.inRadius) return true;
            }
            if (this.mode === 'dinas_luar' && !this.dinasNotes.trim()) return true;
            return false;
        },

        getButtonGradientClass() {
            if (this.isActionDisabled()) return 'bg-slate-400 opacity-70 cursor-not-allowed';
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
            if (this.activeSessionType === 'check_in') return 'Presensi Masuk (Check-In)';
            if (this.activeSessionType === 'midday') return 'Konfirmasi Presensi Dzuhur';
            if (this.activeSessionType === 'check_out') return 'Presensi Pulang (Check-Out)';
            return 'Simpan Presensi (' + this.activeSessionName + ')';
        },

        async submitAttendance() {
            if (this.isActionDisabled()) return;

            if (this.mode === 'reguler' && this.gpsRequired && !this.inRadius) {
                alert('Anda berada di luar radius sekolah (' + this.distanceMeters + 'm). Presensi reguler wajib berada dalam radius ' + this.schoolRadius + 'm.');
                return;
            }

            if (this.mode === 'dinas_luar' && !this.dinasNotes.trim()) {
                alert('Harap isi keterangan atau surat tugas dinas luar.');
                return;
            }

            this.isSubmitting = true;

            try {
                const res = await fetch('{{ route('admin.teacher-attendances.self-checkin') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        type: this.activeSessionType,
                        latitude: this.userLat,
                        longitude: this.userLong,
                        accuracy: this.accuracy,
                        attendance_mode: this.mode,
                        dinas_notes: this.dinasNotes
                    })
                });

                const data = await res.json();
                if (data.success) {
                    this.confirmTitle = 'Presensi Berhasil Disimpan!';
                    this.confirmMessage = data.message || 'Presensi Anda telah tercatat dan tersinkronisasi ke sistem.';
                    this.showConfirmModal = true;
                } else {
                    alert(data.message || 'Gagal menyimpan presensi.');
                }
            } catch (e) {
                alert('Terjadi kendala jaringan saat menghubungi server. Silakan coba kembali.');
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
// Redundant push ensuring execution in all layout setups
</script>
@endpush
