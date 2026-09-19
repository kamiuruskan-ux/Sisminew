@extends('layouts.admin')

@section('title', 'Pengaturan Presensi Guru & Pegawai')
@section('page_title', 'Pengaturan Presensi Pegawai')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-sm">✓</span>
                <div>
                    <p class="text-xs font-bold text-emerald-900 dark:text-emerald-200">Berhasil Disimpan!</p>
                    <p class="text-xs text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-sm font-bold">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 shadow-xs space-y-1">
            <div class="flex items-center space-x-2 text-rose-800 dark:text-rose-200 font-bold text-xs">
                <span>⚠️</span>
                <span>Terdapat kesalahan pada formulir pengaturan:</span>
            </div>
            <ul class="list-disc list-inside text-xs text-rose-700 dark:text-rose-300 pl-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Banner -->
    <div class="p-6 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#3C50E0] to-indigo-500 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-indigo-500/20 shrink-0">
                ⚙️
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">Pengaturan Sistem Presensi Guru & Pegawai</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Kelola jam operasional sesi presensi, toleransi keterlambatan, validasi geofence GPS, hardware biometrik, dan hari libur sekolah.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.teacher-attendances.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                <span>&larr;</span>
                <span>Daftar Presensi</span>
            </a>
            <a href="{{ route('admin.teacher-attendances.my-attendance') }}" target="_blank"
               class="px-4 py-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-[#3C50E0] dark:text-indigo-400 hover:bg-indigo-100 text-xs font-bold transition flex items-center gap-1.5 border border-indigo-200 dark:border-indigo-800/60">
                <span>👁️</span>
                <span>Lihat Presensi Mandiri</span>
            </a>
        </div>
    </div>

    <!-- MAIN FORM -->
    <form method="POST" action="{{ route('admin.teacher-attendances.update-settings') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- SECTION 1: JADWAL & SESI PRESENSI (3 SESSIONS) -->
        <div class="p-6 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-xs space-y-6">
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-[#3C50E0] dark:text-indigo-400 flex items-center justify-center font-bold text-lg">
                    ⏰
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Jadwal Sesi & Aturan Waktu Presensi</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Tentukan jendela jam otomatis untuk Check-In Pagi, Dzuhur, dan Check-Out Sore</p>
                </div>
            </div>

            <!-- 3 Columns for Sessions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- 1. SESI PAGI (MASUK) -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-[#24303F]/50 border border-slate-200 dark:border-slate-700/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🌅</span> 1. Sesi Pagi (Masuk)
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] font-bold">Check-In</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jam Buka Sesi Pagi *</label>
                            <input type="time" name="attendance_morning_open" value="{{ old('attendance_morning_open', $settings['morning_open']) }}" required
                                   class="w-full text-xs font-mono font-bold px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#1A222C] dark:text-white focus:ring-2 focus:ring-[#3C50E0] outline-none">
                            <span class="text-[10px] text-slate-400">Pendidik dapat mulai melakukan check-in.</span>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Batas Tepat Waktu (Late Threshold) *</label>
                            <input type="time" name="attendance_morning_late" value="{{ old('attendance_morning_late', $settings['morning_late']) }}" required
                                   class="w-full text-xs font-mono font-bold px-3 py-2.5 rounded-xl border border-amber-300 dark:border-amber-700 dark:bg-[#1A222C] text-amber-700 dark:text-amber-400 focus:ring-2 focus:ring-amber-500 outline-none">
                            <span class="text-[10px] text-slate-400">Lewat jam ini dihitung Terlambat.</span>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Toleransi Terlambat (Menit) *</label>
                            <div class="relative">
                                <input type="number" name="attendance_late_tolerance" min="0" max="180" value="{{ old('attendance_late_tolerance', $settings['late_tolerance']) }}" required
                                       class="w-full text-xs font-mono font-bold px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#1A222C] dark:text-white focus:ring-2 focus:ring-[#3C50E0] outline-none pr-14">
                                <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">Menit</span>
                            </div>
                            <span class="text-[10px] text-slate-400">Melebihi toleransi dihitung <strong>Sangat Terlambat</strong>.</span>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jam Tutup Sesi Pagi *</label>
                            <input type="time" name="attendance_morning_close" value="{{ old('attendance_morning_close', $settings['morning_close']) }}" required
                                   class="w-full text-xs font-mono font-bold px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#1A222C] dark:text-white focus:ring-2 focus:ring-[#3C50E0] outline-none">
                            <span class="text-[10px] text-slate-400">Sesi check-in pagi ditutup.</span>
                        </div>
                    </div>
                </div>

                <!-- 2. SESI SIANG (DZUHUR) -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-[#24303F]/50 border border-slate-200 dark:border-slate-700/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-amber-600 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span>☀️</span> 2. Sesi Siang (Dzuhur)
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 text-[10px] font-bold">Dzuhur</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jam Buka Sesi Dzuhur *</label>
                            <input type="time" name="attendance_dzuhur_open" value="{{ old('attendance_dzuhur_open', $settings['dzuhur_open']) }}" required
                                   class="w-full text-xs font-mono font-bold px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#1A222C] dark:text-white focus:ring-2 focus:ring-[#3C50E0] outline-none">
                            <span class="text-[10px] text-slate-400">Waktu mulai konfirmasi sholat Dzuhur berjamaah.</span>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jam Tutup Sesi Dzuhur *</label>
                            <input type="time" name="attendance_dzuhur_close" value="{{ old('attendance_dzuhur_close', $settings['dzuhur_close']) }}" required
                                   class="w-full text-xs font-mono font-bold px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#1A222C] dark:text-white focus:ring-2 focus:ring-[#3C50E0] outline-none">
                            <span class="text-[10px] text-slate-400">Batas akhir pengisian kehadiran sesi Dzuhur.</span>
                        </div>

                        <div class="p-3 bg-amber-50/60 dark:bg-amber-950/30 rounded-xl border border-amber-200/50 dark:border-amber-800/40 text-[11px] text-amber-800 dark:text-amber-300 space-y-1">
                            <p class="font-bold">ℹ️ Logika Otomatis:</p>
                            <p>Tombol <strong>"Konfirmasi Presensi Dzuhur"</strong> otomatis muncul saat jam server berada dalam rentang buka dan tutup di atas.</p>
                        </div>
                    </div>
                </div>

                <!-- 3. SESI SORE (PULANG) -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-[#24303F]/50 border border-slate-200 dark:border-slate-700/60 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🌇</span> 3. Sesi Sore (Pulang)
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 text-[10px] font-bold">Check-Out</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jam Buka Sesi Pulang *</label>
                            <input type="time" name="attendance_afternoon_open" value="{{ old('attendance_afternoon_open', $settings['afternoon_open']) }}" required
                                   class="w-full text-xs font-mono font-bold px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#1A222C] dark:text-white focus:ring-2 focus:ring-[#3C50E0] outline-none">
                            <span class="text-[10px] text-slate-400">Pendidik dapat mulai melakukan check-out kepulangan.</span>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Jam Tutup Sesi Pulang *</label>
                            <input type="time" name="attendance_afternoon_close" value="{{ old('attendance_afternoon_close', $settings['afternoon_close']) }}" required
                                   class="w-full text-xs font-mono font-bold px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#1A222C] dark:text-white focus:ring-2 focus:ring-[#3C50E0] outline-none">
                            <span class="text-[10px] text-slate-400">Batas akhir pencatatan presensi kepulangan harian.</span>
                        </div>

                        <div class="p-3 bg-indigo-50/60 dark:bg-indigo-950/30 rounded-xl border border-indigo-200/50 dark:border-indigo-800/40 text-[11px] text-indigo-800 dark:text-indigo-300 space-y-1">
                            <p class="font-bold">ℹ️ Syarat Check-Out:</p>
                            <p>Check-out hanya dapat dilakukan oleh pendidik yang telah menyelesaikan check-in masuk sebelumnya.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 2: GEOFENCING GPS & LOKASI SEKOLAH -->
        <div class="p-6 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-xs space-y-5">
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-lg">
                    📍
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Geofence GPS & Titik Koordinat Satelit</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Konfigurasi radius toleransi jarak dan titik acuan gerbang sekolah</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Radius Presensi GPS (Meter) *</label>
                    <div class="relative">
                        <input type="number" name="school_attendance_radius" min="5" max="50000" value="{{ old('school_attendance_radius', $settings['gps_radius']) }}" required
                               class="w-full text-xs font-mono font-bold px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#24303F] dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none pr-16">
                        <span class="absolute right-4 top-3 text-xs text-slate-400 font-bold">Meter</span>
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">Jarak toleransi maksimal guru dari gerbang sekolah (contoh: 100m).</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Latitude Gerbang Sekolah</label>
                    <input type="text" name="school_latitude" value="{{ old('school_latitude', $settings['school_latitude']) }}"
                           class="w-full text-xs font-mono font-bold px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#24303F] dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    <span class="text-[11px] text-slate-400 mt-1 block">Koordinat garis lintang (contoh: -0.891700).</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Longitude Gerbang Sekolah</label>
                    <input type="text" name="school_longitude" value="{{ old('school_longitude', $settings['school_longitude']) }}"
                           class="w-full text-xs font-mono font-bold px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#24303F] dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    <span class="text-[11px] text-slate-400 mt-1 block">Koordinat garis bujur (contoh: 119.870700).</span>
                </div>
            </div>
        </div>

        <!-- SECTION 3: METODE & HARDWARE BIOMETRIK TOGGLES -->
        <div class="p-6 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-xs space-y-5">
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-lg">
                    🛡️
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Aktivasi Metode & Sensor Biometrik</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Aktifkan atau nonaktifkan saluran presensi yang diizinkan bagi guru dan staff</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- 1. GPS Validation Toggle -->
                <label class="p-4 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between space-y-3 {{ $settings['gps_enabled'] ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-800' : 'bg-slate-50 dark:bg-[#24303F] border-slate-200 dark:border-slate-700' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">📍</span>
                        <input type="checkbox" name="attendance_gps_enabled" value="1" {{ $settings['gps_enabled'] ? 'checked' : '' }}
                               class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500 border-slate-300 cursor-pointer">
                    </div>
                    <div>
                        <span class="text-xs font-black text-slate-900 dark:text-white block uppercase">Validasi GPS Geofence</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Wajibkan pemeriksaan radius satelit sebelum menyimpan presensi mandiri.</span>
                    </div>
                </label>

                <!-- 2. Fingerprint USB Toggle -->
                <label class="p-4 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between space-y-3 {{ $settings['fingerprint_enabled'] ? 'bg-indigo-50/50 dark:bg-indigo-950/20 border-indigo-300 dark:border-indigo-800' : 'bg-slate-50 dark:bg-[#24303F] border-slate-200 dark:border-slate-700' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">👆</span>
                        <input type="checkbox" name="attendance_fingerprint_enabled" value="1" {{ $settings['fingerprint_enabled'] ? 'checked' : '' }}
                               class="w-5 h-5 text-[#3C50E0] rounded-lg focus:ring-indigo-500 border-slate-300 cursor-pointer">
                    </div>
                    <div>
                        <span class="text-xs font-black text-slate-900 dark:text-white block uppercase">Terminal Scanner Sidik Jari</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Aktifkan terminal scanner fisik USB (DigitalPersona/SecuGen/HID) di meja piket.</span>
                    </div>
                </label>

                <!-- 3. Face Recognition Toggle -->
                <label class="p-4 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between space-y-3 {{ $settings['face_enabled'] ? 'bg-cyan-50/50 dark:bg-cyan-950/20 border-cyan-300 dark:border-cyan-800' : 'bg-slate-50 dark:bg-[#24303F] border-slate-200 dark:border-slate-700' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">👁️</span>
                        <input type="checkbox" name="attendance_face_enabled" value="1" {{ $settings['face_enabled'] ? 'checked' : '' }}
                               class="w-5 h-5 text-cyan-600 rounded-lg focus:ring-cyan-500 border-slate-300 cursor-pointer">
                    </div>
                    <div>
                        <span class="text-xs font-black text-slate-900 dark:text-white block uppercase">Biometrik Face ID</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Izinkan pencocokan biometrik wajah AI live kamera.</span>
                    </div>
                </label>

                <!-- 4. Manual Attendance Toggle -->
                <label class="p-4 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between space-y-3 {{ $settings['manual_enabled'] ? 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-300 dark:border-amber-800' : 'bg-slate-50 dark:bg-[#24303F] border-slate-200 dark:border-slate-700' }}">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">📝</span>
                        <input type="checkbox" name="attendance_manual_enabled" value="1" {{ $settings['manual_enabled'] ? 'checked' : '' }}
                               class="w-5 h-5 text-amber-600 rounded-lg focus:ring-amber-500 border-slate-300 cursor-pointer">
                    </div>
                    <div>
                        <span class="text-xs font-black text-slate-900 dark:text-white block uppercase">Input Presensi Manual</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Izinkan Admin/Operator menginput presensi guru pengganti secara manual.</span>
                    </div>
                </label>

            </div>
        </div>

        <!-- SECTION 4: HARI KERJA & KONFIGURASI HARI LIBUR -->
        <div class="p-6 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-xs space-y-5">
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-lg">
                    📅
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Konfigurasi Hari Libur & Akhir Pekan</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Atur hari libur mingguan dan daftar tanggal libur khusus / cuti bersama</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Weekend Days Selection -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Hari Libur Mingguan (Akhir Pekan)</label>
                    <p class="text-[11px] text-slate-500">Pilih hari-hari di mana presensi guru diliburkan secara otomatis:</p>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2">
                        @php
                            $days = [
                                0 => 'Minggu',
                                1 => 'Senin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Kamis',
                                5 => 'Jumat',
                                6 => 'Sabtu',
                            ];
                        @endphp
                        @foreach($days as $dayIndex => $dayName)
                            <label class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center space-x-2 text-xs font-bold cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800">
                                <input type="checkbox" name="attendance_weekend_days[]" value="{{ $dayIndex }}" 
                                       {{ in_array($dayIndex, $settings['weekend_days']) ? 'checked' : '' }}
                                       class="rounded text-rose-600 focus:ring-rose-500">
                                <span class="{{ $dayIndex === 0 || $dayIndex === 6 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-700 dark:text-slate-300' }}">{{ $dayName }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Custom Holiday Dates -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Daftar Tanggal Libur Khusus / Cuti Bersama</label>
                    <p class="text-[11px] text-slate-500">Masukkan tanggal libur sekolah (format YYYY-MM-DD dipisahkan koma atau baris baru):</p>
                    
                    <textarea name="attendance_holidays" rows="4" placeholder="Contoh: 2026-08-17, 2026-12-25, 2026-05-01"
                              class="w-full text-xs font-mono font-medium p-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#24303F] dark:text-white focus:ring-2 focus:ring-rose-500 outline-none">{{ old('attendance_holidays', $settings['holidays']) }}</textarea>
                    <span class="text-[10px] text-slate-400 block">Sistem secara otomatis mengunci formulir presensi pada tanggal-tanggal tersebut.</span>
                </div>
            </div>
        </div>

        <!-- SUBMIT ACTION BUTTON -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.teacher-attendances.index') }}"
               class="px-6 py-3 rounded-2xl bg-slate-100 dark:bg-[#24303F] text-slate-700 dark:text-slate-300 hover:bg-slate-200 font-extrabold text-xs transition">
                Batal
            </a>
            <button type="submit"
                    class="px-8 py-3.5 rounded-2xl bg-gradient-to-r from-[#3C50E0] to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-indigo-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center gap-2 cursor-pointer">
                <span>💾</span>
                <span>Simpan Pengaturan Presensi</span>
            </button>
        </div>

    </form>
</div>
@endsection
