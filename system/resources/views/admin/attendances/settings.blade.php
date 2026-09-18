@extends('layouts.admin')

@section('title', 'Pengaturan Presensi Siswa')
@section('page_title', 'Pengaturan & Konfigurasi Presensi Siswa')

@section('content')
<div class="space-y-8 w-full pb-28" x-data="{
    activeMethod: '{{ Setting::get('attendance_active_method', Setting::get('attendance_default_method', 'qr')) }}',
    
    entryTime: '{{ Setting::get('attendance_entry_time', '07:00') }}',
    lateTime: '{{ Setting::get('attendance_late_time', '07:15') }}',
    exitTime: '{{ Setting::get('attendance_exit_time', '15:00') }}',
    faceConfidence: {{ Setting::get('attendance_face_confidence', '75') }},
    qrRefresh: {{ Setting::get('attendance_qr_refresh_seconds', '30') }}
}">

    <!-- Hero Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 p-6 sm:p-8 lg:p-10 text-white shadow-2xl shadow-indigo-950/20 border border-slate-800/80">
        <!-- Ambient Background Glow -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-24 w-96 h-96 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/10 text-xs font-extrabold text-indigo-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span>Konfigurasi Metode Absensi Siswa</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Pengaturan Presensi Siswa
                </h1>
                <p class="text-xs sm:text-sm text-indigo-100/80 leading-relaxed font-medium">
                    Pilih <b>1 Metode Absensi Utama</b> yang berlaku untuk terminal presensi siswa (<b>RFID Card</b>, <b>Fingerprint</b>, <b>Scan Wajah</b>, atau <b>Scan QR Code</b>), integrasi perangkat keras, aturan toleransi keterlambatan, serta notifikasi WhatsApp.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('admin.attendances.index') }}" 
                   class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs transition-all border border-white/10 flex items-center space-x-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Rekap Presensi Siswa</span>
                </a>
                <a href="{{ route('admin.qr-attendance.scan') }}" target="_blank" rel="noopener"
                   class="px-5 py-3 rounded-2xl bg-cyan-600 hover:bg-cyan-500 text-white font-extrabold text-xs transition-all flex items-center space-x-2 shadow-lg shadow-cyan-600/30 border border-cyan-400/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Buka Terminal Presensi</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form method="POST" action="{{ route('admin.attendances.update-settings') }}" class="space-y-8">
        @csrf
        @method('PUT')

        <input type="hidden" name="attendance_active_method" :value="activeMethod">

        <!-- SECTION 1: Pilihan Metode Presensi Siswa Single Active -->
        <div class="premium-card p-6 sm:p-8 space-y-6 shadow-md border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                <div class="flex items-center space-x-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-extrabold border border-indigo-100 dark:border-indigo-900/50 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 4.12a21.959 21.959 0 01-4.329 2.07M15 15.5a8.38 8.38 0 001.76-2.5m1.523-2.15a13.96 13.96 0 00.717-3.85C19 4.9 15.866 2 12 2g"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Pilihan Metode Absensi Aktif</h3>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Pilih 1 metode utama yang berlaku di terminal presensi siswa</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full border border-indigo-200 dark:border-indigo-800">1 Metode Aktif</span>
            </div>

            <!-- 4 Methods Cards Single Selection Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <!-- 1. RFID Card Option Card -->
                <div @click="activeMethod = 'rfid'"
                     class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between space-y-4"
                     :class="activeMethod === 'rfid' ? 'bg-purple-500/10 border-purple-500 shadow-lg ring-2 ring-purple-500/20' : 'bg-slate-50 dark:bg-slate-900/40 border-slate-200 dark:border-slate-800 opacity-60 hover:opacity-100'">
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center border border-purple-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input type="radio" name="active_method_radio" value="rfid" :checked="activeMethod === 'rfid'" class="text-purple-600 focus:ring-purple-500">
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">NFC / RFID Card</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">Tap Kartu Pelajar (NFC/RFID) pada reader USB / terminal serial.</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-bold" :class="activeMethod === 'rfid' ? 'text-purple-600 dark:text-purple-400' : 'text-slate-400'">
                            <span x-text="activeMethod === 'rfid' ? 'Metode Terpilih' : 'Pilih Metode'"></span>
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded"
                              :class="activeMethod === 'rfid' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-400'">
                            <span x-text="activeMethod === 'rfid' ? 'Aktif' : 'Nonaktif'"></span>
                        </span>
                    </div>
                </div>

                <!-- 2. Fingerprint Option Card -->
                <div @click="activeMethod = 'finger'"
                     class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between space-y-4"
                     :class="activeMethod === 'finger' ? 'bg-emerald-500/10 border-emerald-500 shadow-lg ring-2 ring-emerald-500/20' : 'bg-slate-50 dark:bg-slate-900/40 border-slate-200 dark:border-slate-800 opacity-60 hover:opacity-100'">
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 4.12a21.959 21.959 0 01-4.329 2.07M15 15.5a8.38 8.38 0 001.76-2.5m1.523-2.15a13.96 13.96 0 00.717-3.85C19 4.9 15.866 2 12 2g"/></svg>
                            </div>
                            <input type="radio" name="active_method_radio" value="finger" :checked="activeMethod === 'finger'" class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">Fingerprint (Sidik Jari)</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">Presensi menggunakan mesin sidik jari biometrik terhubung IP LAN/WiFi.</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-bold" :class="activeMethod === 'finger' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'">
                            <span x-text="activeMethod === 'finger' ? 'Metode Terpilih' : 'Pilih Metode'"></span>
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded"
                              :class="activeMethod === 'finger' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-400'">
                            <span x-text="activeMethod === 'finger' ? 'Aktif' : 'Nonaktif'"></span>
                        </span>
                    </div>
                </div>

                <!-- 3. Face Recognition Option Card -->
                <div @click="activeMethod = 'face'"
                     class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between space-y-4"
                     :class="activeMethod === 'face' ? 'bg-amber-500/10 border-amber-500 shadow-lg ring-2 ring-amber-500/20' : 'bg-slate-50 dark:bg-slate-900/40 border-slate-200 dark:border-slate-800 opacity-60 hover:opacity-100'">
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <input type="radio" name="active_method_radio" value="face" :checked="activeMethod === 'face'" class="text-amber-600 focus:ring-amber-500">
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">Scan Wajah (Face ID)</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">Pemberian presensi otomatis via deteksi & pencocokan wajah kamera.</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-bold" :class="activeMethod === 'face' ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400'">
                            <span x-text="activeMethod === 'face' ? 'Metode Terpilih' : 'Pilih Metode'"></span>
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded"
                              :class="activeMethod === 'face' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-400'">
                            <span x-text="activeMethod === 'face' ? 'Aktif' : 'Nonaktif'"></span>
                        </span>
                    </div>
                </div>

                <!-- 4. QR Code Scan Option Card -->
                <div @click="activeMethod = 'qr'"
                     class="relative p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 flex flex-col justify-between space-y-4"
                     :class="activeMethod === 'qr' ? 'bg-cyan-500/10 border-cyan-500 shadow-lg ring-2 ring-cyan-500/20' : 'bg-slate-50 dark:bg-slate-900/40 border-slate-200 dark:border-slate-800 opacity-60 hover:opacity-100'">
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center border border-cyan-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            </div>
                            <input type="radio" name="active_method_radio" value="qr" :checked="activeMethod === 'qr'" class="text-cyan-600 focus:ring-cyan-500">
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">Scan QR Code (KTS)</h4>
                            <p class="text-xs text-slate-400 font-medium mt-1 leading-relaxed">Scanning QR Code pada Kartu Tanda Siswa fisik atau digital.</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-bold" :class="activeMethod === 'qr' ? 'text-cyan-600 dark:text-cyan-400' : 'text-slate-400'">
                            <span x-text="activeMethod === 'qr' ? 'Metode Terpilih' : 'Pilih Metode'"></span>
                        </span>
                        <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded"
                              :class="activeMethod === 'qr' ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-400'">
                            <span x-text="activeMethod === 'qr' ? 'Aktif' : 'Nonaktif'"></span>
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 2: Konfigurasi Device & Integrasi Hardware -->
        <div class="premium-card p-6 sm:p-8 space-y-6 shadow-md border-slate-200 dark:border-slate-800">
            <div class="flex items-center space-x-3.5 border-b border-slate-100 dark:border-slate-800 pb-4">
                <div class="w-10 h-10 rounded-2xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-extrabold border border-cyan-100 dark:border-cyan-900/50 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 002 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Parameter & Integrasi Perangkat Hardware</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Konfigurasi endpoint, IP address, secret key, & sensitivitas deteksi</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- RFID Device Settings -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 space-y-4">
                    <div class="flex items-center space-x-2 font-extrabold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-purple-600 dark:text-purple-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>1. Integrasi Reader RFID / NFC</span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Device API Key / Secret RFID</label>
                        <input type="text" name="attendance_rfid_device_key" value="{{ Setting::get('attendance_rfid_device_key') }}"
                               placeholder="Contoh: rfid_sec_key_998127"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-mono">
                        <p class="text-[11px] text-slate-400">Token rahasia autentikasi HTTP Push dari perangkat RFID Reader ke server.</p>
                    </div>
                </div>

                <!-- Fingerprint Device Settings -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 space-y-4">
                    <div class="flex items-center space-x-2 font-extrabold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 4.12a21.959 21.959 0 01-4.329 2.07M15 15.5a8.38 8.38 0 001.76-2.5m1.523-2.15a13.96 13.96 0 00.717-3.85C19 4.9 15.866 2 12 2g"/></svg>
                        <span>2. Mesin Fingerprint (IP / Local LAN)</span>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2 space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">IP Address Mesin</label>
                            <input type="text" name="attendance_finger_device_ip" value="{{ Setting::get('attendance_finger_device_ip', '192.168.1.201') }}"
                                   placeholder="192.168.1.201"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-mono">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Port UDP</label>
                            <input type="text" name="attendance_finger_device_port" value="{{ Setting::get('attendance_finger_device_port', '4370') }}"
                                   placeholder="4370"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-mono">
                        </div>
                    </div>
                </div>

                <!-- Face Recognition Settings -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 space-y-4">
                    <div class="flex items-center space-x-2 font-extrabold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-amber-600 dark:text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>3. Sensitivitas Face Recognition</span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <label class="text-slate-700 dark:text-slate-300">Minimum Face Match Confidence</label>
                            <span class="text-indigo-600 dark:text-indigo-400 font-mono font-black" x-text="faceConfidence + '%'"></span>
                        </div>
                        <input type="range" name="attendance_face_confidence" min="50" max="100" step="5" x-model="faceConfidence"
                               class="w-full accent-indigo-600 cursor-pointer">
                        <p class="text-[11px] text-slate-400">Persentase kemiripan minimum agar wajah siswa dinyatakan terverifikasi.</p>
                    </div>
                </div>

                <!-- Dynamic QR Scan Settings -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 space-y-4">
                    <div class="flex items-center space-x-2 font-extrabold text-slate-900 dark:text-white text-xs uppercase tracking-wider text-cyan-600 dark:text-cyan-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        <span>4. Masa Berlaku Refresh QR Dinamis</span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Interval Refresh Token (Detik)</label>
                        <select name="attendance_qr_refresh_seconds" x-model="qrRefresh"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-medium">
                            <option value="10">10 Detik (Sangat Ketat)</option>
                            <option value="30">30 Detik (Rekomendasi Standard)</option>
                            <option value="60">60 Detik (1 Menit)</option>
                            <option value="120">120 Detik (2 Menit)</option>
                        </select>
                        <p class="text-[11px] text-slate-400">Mencegah screenshot QR Code disalahgunakan oleh siswa lain.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 3: Aturan Jam Masuk & Batas Terlambat -->
        <div class="premium-card p-6 sm:p-8 space-y-6 shadow-md border-slate-200 dark:border-slate-800">
            <div class="flex items-center space-x-3.5 border-b border-slate-100 dark:border-slate-800 pb-4">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-extrabold border border-amber-100 dark:border-amber-900/50 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Aturan Jam Presensi & Toleransi Keterlambatan</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Penetapan jam masuk resmi, batas waktu hadir, serta jam pulang sekolah</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Jam Masuk -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                        <span>Jam Masuk Utama</span>
                        <span class="text-emerald-500 font-mono">Hadir</span>
                    </label>
                    <div class="relative">
                        <input type="time" name="attendance_entry_time" value="{{ Setting::get('attendance_entry_time', '07:00') }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-bold">
                    </div>
                    <p class="text-[11px] text-slate-400">Siswa dianggap <b>Hadir Tepat Waktu</b> jika scan pada atau sebelum jam ini.</p>
                </div>

                <!-- Batas Terlambat -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                        <span>Batas Jam Terlambat</span>
                        <span class="text-amber-500 font-mono">Terlambat</span>
                    </label>
                    <div class="relative">
                        <input type="time" name="attendance_late_time" value="{{ Setting::get('attendance_late_time', '07:15') }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-bold">
                    </div>
                    <p class="text-[11px] text-slate-400">Scan setelah jam masuk hingga jam ini ditandai sebagai <b>Terlambat</b>.</p>
                </div>

                <!-- Jam Pulang -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                        <span>Jam Pulang Sekolah</span>
                        <span class="text-cyan-500 font-mono">Check-Out</span>
                    </label>
                    <div class="relative">
                        <input type="time" name="attendance_exit_time" value="{{ Setting::get('attendance_exit_time', '15:00') }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-bold">
                    </div>
                    <p class="text-[11px] text-slate-400">Waktu dimulainya scan pulang/keluar sekolah untuk siswa.</p>
                </div>

                <!-- Toleransi Pulang -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Toleransi Pulang Awal (Menit)</label>
                    <div class="relative">
                        <input type="number" name="attendance_early_exit_tolerance" value="{{ Setting::get('attendance_early_exit_tolerance', '15') }}" min="0" max="120"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-xs font-mono text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 font-bold">
                    </div>
                    <p class="text-[11px] text-slate-400">Dispen waktu pulang lebih awal tanpa dicatat pelanggaran.</p>
                </div>

            </div>
        </div>

        <!-- SECTION 4: Otomatisasi Notifikasi WhatsApp & Preferensi -->
        <div class="premium-card p-6 sm:p-8 space-y-6 shadow-md border-slate-200 dark:border-slate-800">
            <div class="flex items-center space-x-3.5 border-b border-slate-100 dark:border-slate-800 pb-4">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-extrabold border border-emerald-100 dark:border-emerald-900/50 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Otomatisasi Notifikasi & Audio Feedback</h3>
                    <p class="text-xs text-slate-400 font-semibold mt-0.5">Pengiriman WhatsApp real-time ke orang tua dan suara salam scanner</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- 1. Notifikasi WA Orang Tua -->
                <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-start space-x-4 bg-slate-50/50 dark:bg-slate-900/30">
                    <label class="relative inline-flex items-center cursor-pointer mt-0.5">
                        <input type="checkbox" name="attendance_wa_notify_parents" value="1" {{ Setting::get('attendance_wa_notify_parents', '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                    <div>
                        <h4 class="font-extrabold text-slate-900 dark:text-white text-xs">WhatsApp Real-Time ke Wali</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Kirim pesan WhatsApp otomatis ke HP Orang Tua saat anak melakukan presensi masuk/terlambat.</p>
                    </div>
                </div>

                <!-- 2. Suara Feedback / Audio TTS -->
                <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-start space-x-4 bg-slate-50/50 dark:bg-slate-900/30">
                    <label class="relative inline-flex items-center cursor-pointer mt-0.5">
                        <input type="checkbox" name="attendance_sound_feedback" value="1" {{ Setting::get('attendance_sound_feedback', '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                    <div>
                        <h4 class="font-extrabold text-slate-900 dark:text-white text-xs">Suara Greeting & Beep Scanner</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Mainkan audio "Selamat Pagi, [Nama Siswa], Kehadiran Berhasil" pada perangkat kiosk scanner.</p>
                    </div>
                </div>

                <!-- 3. Presensi Mandiri Siswa -->
                <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-start space-x-4 bg-slate-50/50 dark:bg-slate-900/30">
                    <label class="relative inline-flex items-center cursor-pointer mt-0.5">
                        <input type="checkbox" name="attendance_allow_self_checkin" value="1" {{ Setting::get('attendance_allow_self_checkin', '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                    </label>
                    <div>
                        <h4 class="font-extrabold text-slate-900 dark:text-white text-xs">Presensi Mandiri HP Siswa</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Izinkan siswa melakukan check-in lokasi GPS mandiri melalui akun dashboard siswa.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Sticky Floating Save Bar -->
        <div class="fixed bottom-6 left-6 right-6 md:left-72 md:right-8 z-40">
            <div class="p-4 rounded-2xl bg-slate-950/90 backdrop-blur-xl border border-slate-800 shadow-2xl flex items-center justify-between max-w-7xl mx-auto">
                <div class="hidden sm:flex items-center space-x-3 text-slate-300 text-xs font-bold">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Perubahan konfigurasi metode presensi akan langsung diterapkan ke seluruh terminal scanner.</span>
                </div>
                <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                    <button type="reset" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition border border-slate-700">
                        Reset
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-extrabold transition shadow-lg shadow-indigo-600/30 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Konfigurasi</span>
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection
