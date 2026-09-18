<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Presensi Mobile Mandiri Guru (Lock GPS) - {{ config('app.name', 'Sekolah') }}</title>

    <!-- Google Fonts Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0B0F17;
            color: #F8FAFC;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @keyframes pulseLocation {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .animate-loc-pulse {
            animation: pulseLocation 2s infinite ease-in-out;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased selection:bg-cyan-500 selection:text-white"
      x-data="{
    schoolLat: {{ $schoolLat }},
    schoolLong: {{ $schoolLong }},
    schoolRadius: {{ $schoolRadius }},

    // Geolocation state
    userLat: null,
    userLong: null,
    distanceMeters: null,
    inRadius: false,
    gpsLoading: true,
    gpsError: '',

    // Attendance State (Sinkronisasi Database)
    hasCheckedIn: {{ !empty($todayAttendance?->check_in) ? 'true' : 'false' }},
    checkInTime: '{{ $todayAttendance?->check_in ?? '' }}',
    checkInMethod: '{{ $todayAttendance?->method_label ?? 'Sidik Jari/GPS' }}',
    
    hasCheckedOut: {{ !empty($todayAttendance?->check_out) ? 'true' : 'false' }},
    checkOutTime: '{{ $todayAttendance?->check_out ?? '' }}',

    // Submitting State
    isSubmitting: false,
    alertMsg: '',
    alertType: '', // 'success', 'error'

    // Realtime Clock
    currentTime: '',
    currentDate: '',

    init() {
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);
        this.detectGps();
        
        // Background Polling setiap 10 detik untuk mendeteksi jika guru tap di scanner laptop admin
        setInterval(() => {
            this.pollStatus();
        }, 10000);
    },

    updateClock() {
        const now = new Date();
        this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    },

    detectGps() {
        this.gpsLoading = true;
        this.gpsError = '';

        if (!navigator.geolocation) {
            this.gpsLoading = false;
            this.gpsError = 'Browser perangkat Anda tidak mendukung Geolocation GPS.';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                this.userLat = pos.coords.latitude;
                this.userLong = pos.coords.longitude;
                this.calculateDistance();
                this.gpsLoading = false;
            },
            (err) => {
                this.gpsLoading = false;
                if (err.code === err.PERMISSION_DENIED) {
                    this.gpsError = 'Izin akses GPS ditolak! Aktifkan lokasi HP Anda untuk melakukan absensi.';
                } else {
                    this.gpsError = 'Gagal mendeteksi koordinat lokasi GPS. Pastikan GPS aktif.';
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    },

    calculateDistance() {
        if (!this.userLat || !this.userLong) return;
        
        const R = 6371000; // Radius Bumi dalam meter
        const dLat = (this.schoolLat - this.userLat) * Math.PI / 180;
        const dLon = (this.schoolLong - this.userLong) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(this.userLat * Math.PI / 180) * Math.cos(this.schoolLat * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        this.distanceMeters = Math.round(R * c);
        this.inRadius = this.distanceMeters <= this.schoolRadius;
    },

    // Background Polling Otomatis: Cek apakah sidik jari sudah ditap di meja admin
    async pollStatus() {
        try {
            const res = await fetch('{{ route('admin.teacher-attendances.check-status') }}', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            
            if (data.has_record) {
                // Jika sebelumnya belum masuk tapi sekarang sudah masuk (karena baru tap jari di admin desk)
                if (!this.hasCheckedIn && data.has_checked_in) {
                    this.hasCheckedIn = true;
                    this.checkInTime = data.check_in_time;
                    this.checkInMethod = data.method_label;
                    this.showAlert('success', `Tersinkronisasi! Presensi Masuk Anda telah tercatat via ${data.method_label} pada ${data.check_in_time}. Tombol masuk telah dikunci.`);
                }
                if (!this.hasCheckedOut && data.has_checked_out) {
                    this.hasCheckedOut = true;
                    this.checkOutTime = data.check_out_time;
                    this.showAlert('success', `Tersinkronisasi! Presensi Pulang Anda telah tercatat pada ${data.check_out_time}.`);
                }
            }
        } catch(e) {
            // Abaikan kesalahan polling latar belakang
        }
    },

    async submitAttendance(type) {
        if (type === 'check_in' && this.hasCheckedIn) {
            this.showAlert('error', 'Tombol Terkunci: Anda sudah melakukan presensi masuk hari ini!');
            return;
        }

        if (type === 'check_out' && this.hasCheckedOut) {
            this.showAlert('error', 'Tombol Terkunci: Anda sudah melakukan presensi pulang hari ini!');
            return;
        }

        if (!this.inRadius) {
            this.showAlert('error', `Lokasi di luar radius! Anda berjarak ${this.distanceMeters}m (Batas: ${this.schoolRadius}m). Dekati sekolah atau gunakan scanner sidik jari di meja admin.`);
            return;
        }

        this.isSubmitting = true;
        this.alertMsg = '';

        try {
            const res = await fetch('{{ route('admin.teacher-attendances.self-checkin') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    type: type,
                    latitude: this.userLat,
                    longitude: this.userLong,
                    work_location: 'school'
                })
            });

            const data = await res.json();

            if (data.success) {
                if (type === 'check_in') {
                    this.hasCheckedIn = true;
                    this.checkInTime = new Date().toLocaleTimeString('id-ID');
                    this.checkInMethod = 'Mobile HP (Lock GPS)';
                } else {
                    this.hasCheckedOut = true;
                    this.checkOutTime = new Date().toLocaleTimeString('id-ID');
                }
                this.showAlert('success', data.message);
            } else {
                this.showAlert('error', data.message || 'Presensi gagal.');
                if (data.locked) {
                    this.pollStatus();
                }
            }
        } catch(err) {
            this.showAlert('error', 'Terjadi gangguan koneksi internet. Silakan coba lagi.');
        } finally {
            this.isSubmitting = false;
        }
    },

    showAlert(type, msg) {
        this.alertType = type;
        this.alertMsg = msg;
        setTimeout(() => {
            // Auto dismiss alert jika success setelah 6 detik
            if (this.alertType === 'success') {
                this.alertMsg = '';
            }
        }, 6000);
    }
}">

    <!-- Top Mobile Header -->
    <header class="bg-slate-900/90 backdrop-blur-xl border-b border-slate-800 px-4 py-3.5 sticky top-0 z-30">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.teacher-attendances.index') }}" class="p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white border border-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-sm font-black text-white leading-tight">Presensi Mandiri Guru</h1>
                    <div class="flex items-center space-x-1.5 text-[10px] text-emerald-400 font-mono">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Lock GPS & Auto-Sync Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Reload GPS Button -->
            <button type="button" @click="detectGps()" :disabled="gpsLoading"
                    class="p-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white border border-slate-700 flex items-center space-x-1 text-xs">
                <svg class="w-4 h-4" :class="gpsLoading ? 'animate-spin text-cyan-400' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
        </div>
    </header>

    <!-- Main Container (Mobile Max Width 480px) -->
    <main class="flex-1 max-w-md w-full mx-auto p-4 space-y-4">

        <!-- User Profile Card -->
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center space-x-3.5 shadow-lg">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-600 to-indigo-600 flex items-center justify-center font-bold text-white text-lg flex-shrink-0 shadow-md">
                {{ substr(auth()->user()->name ?? 'G', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-extrabold text-white truncate">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-400 font-mono">NIP: {{ auth()->user()->nip ?? '-' }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5" x-text="currentDate"></div>
            </div>
            <div class="text-right flex-shrink-0">
                <div class="text-sm font-black font-mono text-emerald-400" x-text="currentTime"></div>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-800 border border-slate-700 text-slate-400">WIB</span>
            </div>
        </div>

        <!-- Alert Notification Box -->
        <template x-if="alertMsg">
            <div class="p-3.5 rounded-2xl text-xs font-semibold flex items-start space-x-2.5 transition-all shadow-lg"
                 :class="alertType === 'success' ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-500/30' : 'bg-rose-950/80 text-rose-300 border border-rose-500/30'">
                <span class="text-sm flex-shrink-0" x-text="alertType === 'success' ? '✓' : '✕'"></span>
                <div class="flex-1 leading-relaxed" x-text="alertMsg"></div>
                <button type="button" @click="alertMsg = ''" class="text-slate-400 hover:text-white">✕</button>
            </div>
        </template>

        <!-- GPS Geofencing Status Card -->
        <div class="p-4 rounded-2xl border transition-all duration-300 shadow-xl"
             :class="{
                 'bg-slate-900/90 border-slate-800': gpsLoading,
                 'bg-emerald-950/30 border-emerald-500/30 shadow-emerald-950/20': inRadius && !gpsLoading,
                 'bg-rose-950/30 border-rose-500/30 shadow-rose-950/20': !inRadius && !gpsLoading && !gpsError,
                 'bg-amber-950/30 border-amber-500/30': gpsError
             }">
            <div class="flex items-center justify-between pb-3 border-b border-white/5">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full"
                          :class="{
                              'bg-cyan-400 animate-ping': gpsLoading,
                              'bg-emerald-400 animate-loc-pulse': inRadius && !gpsLoading,
                              'bg-rose-500': !inRadius && !gpsLoading && !gpsError,
                              'bg-amber-400': gpsError
                          }"></span>
                    <span class="text-xs font-bold text-white uppercase tracking-wider">Status Lock GPS</span>
                </div>
                <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded-lg"
                      :class="inRadius ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30'"
                      x-text="gpsLoading ? 'Mencari...' : (inRadius ? 'DALAM RADIUS' : 'DI LUAR RADIUS')"></span>
            </div>

            <!-- GPS Details -->
            <div class="mt-3 space-y-2 text-xs">
                <template x-if="gpsLoading">
                    <div class="py-2 text-center text-slate-400 text-xs flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4 animate-spin text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Mendeteksi koordinat satelit GPS HP Anda...</span>
                    </div>
                </template>

                <template x-if="!gpsLoading && !gpsError">
                    <div>
                        <div class="flex justify-between items-center text-slate-300">
                            <span>Jarak ke Pusat Sekolah:</span>
                            <span class="font-mono font-extrabold text-sm"
                                  :class="inRadius ? 'text-emerald-400' : 'text-rose-400'"
                                  x-text="distanceMeters + ' meter'"></span>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-1 flex justify-between">
                            <span>Batas Toleransi Sekolah:</span>
                            <span class="font-mono font-bold text-slate-300" x-text="schoolRadius + ' meter'"></span>
                        </div>
                    </div>
                </template>

                <template x-if="gpsError">
                    <div class="text-rose-300 text-xs leading-relaxed" x-text="gpsError"></div>
                </template>
            </div>
        </div>

        <!-- Attendance Action Buttons & Locking Status -->
        <div class="space-y-3 pt-2">
            
            <!-- BUTTON 1: ABSEN MASUK -->
            <div class="p-4 rounded-2xl border transition-all"
                 :class="hasCheckedIn ? 'bg-slate-900/40 border-slate-800 opacity-90' : 'bg-slate-900 border-slate-800'">
                
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold"
                             :class="hasCheckedIn ? 'bg-emerald-500/20 text-emerald-400' : 'bg-indigo-500/20 text-indigo-400'">
                            {{ $todayAttendance?->check_in ? '✓' : '1' }}
                        </div>
                        <span class="text-xs font-extrabold text-white">Presensi Datang / Masuk</span>
                    </div>

                    <!-- Lock Badge -->
                    <template x-if="hasCheckedIn">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center space-x-1">
                            <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>TERKUNCI (SUDAH HADIR)</span>
                        </span>
                    </template>
                </div>

                <!-- Checked In Info -->
                <template x-if="hasCheckedIn">
                    <div class="p-3 rounded-xl bg-emerald-950/20 border border-emerald-500/20 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Pukul Masuk:</span>
                            <span class="font-mono font-black text-emerald-400 text-sm" x-text="checkInTime"></span>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-1 flex justify-between">
                            <span>Metode Tercatat:</span>
                            <span class="font-bold text-white" x-text="checkInMethod"></span>
                        </div>
                    </div>
                </template>

                <!-- Check In Action Button -->
                <template x-if="!hasCheckedIn">
                    <button type="button" @click="submitAttendance('check_in')"
                            :disabled="!inRadius || isSubmitting || gpsLoading"
                            class="w-full py-3.5 px-4 rounded-xl text-xs font-black uppercase tracking-wider flex items-center justify-center space-x-2 transition shadow-lg"
                            :class="inRadius && !gpsLoading ? 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-600/30 active:scale-98' : 'bg-slate-800 text-slate-500 cursor-not-allowed border border-slate-700'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span x-text="isSubmitting ? 'Mencatat...' : (inRadius ? 'Absen Masuk Sekarang (GPS)' : 'Mendekat ke Radius Sekolah')"></span>
                    </button>
                </template>
            </div>

            <!-- BUTTON 2: ABSEN PULANG -->
            <div class="p-4 rounded-2xl border transition-all"
                 :class="hasCheckedOut ? 'bg-slate-900/40 border-slate-800 opacity-90' : 'bg-slate-900 border-slate-800'">
                
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold"
                             :class="hasCheckedOut ? 'bg-cyan-500/20 text-cyan-400' : 'bg-slate-800 text-slate-400'">
                            {{ $todayAttendance?->check_out ? '✓' : '2' }}
                        </div>
                        <span class="text-xs font-extrabold text-white">Presensi Pulang / Selesai</span>
                    </div>

                    <!-- Lock Badge -->
                    <template x-if="hasCheckedOut">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center space-x-1">
                            <svg class="w-3 h-3 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>TERKUNCI (SELESAI)</span>
                        </span>
                    </template>
                </div>

                <!-- Checked Out Info -->
                <template x-if="hasCheckedOut">
                    <div class="p-3 rounded-xl bg-cyan-950/20 border border-cyan-500/20 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Pukul Pulang:</span>
                            <span class="font-mono font-black text-cyan-400 text-sm" x-text="checkOutTime"></span>
                        </div>
                        <div class="text-[10px] text-slate-400 mt-1 flex justify-between">
                            <span>Status Harian:</span>
                            <span class="font-bold text-emerald-400">Kehadiran Hari Ini Lengkap ✓</span>
                        </div>
                    </div>
                </template>

                <!-- Check Out Action Button -->
                <template x-if="!hasCheckedOut">
                    <button type="button" @click="submitAttendance('check_out')"
                            :disabled="!hasCheckedIn || !inRadius || isSubmitting || gpsLoading"
                            class="w-full py-3.5 px-4 rounded-xl text-xs font-black uppercase tracking-wider flex items-center justify-center space-x-2 transition shadow-lg"
                            :class="hasCheckedIn && inRadius && !gpsLoading ? 'bg-cyan-600 hover:bg-cyan-500 text-white shadow-cyan-600/30 active:scale-98' : 'bg-slate-800 text-slate-500 cursor-not-allowed border border-slate-700'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span x-text="isSubmitting ? 'Mencatat...' : (!hasCheckedIn ? 'Harap Absen Masuk Dulu' : (inRadius ? 'Absen Pulang Sekarang (GPS)' : 'Mendekat ke Radius Sekolah'))"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Info Sinkronisasi Otomatis Card -->
        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs text-slate-400 space-y-1.5">
            <div class="font-bold text-slate-200 flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Sinkronisasi Otomatis 2 Arah</span>
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Jika Anda menempelkan sidik jari di scanner laptop admin, halaman ponsel ini akan **otomatis terkunci dalam 10 detik** tanpa perlu me-refresh halaman. Begitu pula sebaliknya, jika sudah absen di HP ini, status di laptop admin langsung tercatat hadir.
            </p>
        </div>
    </main>

    <!-- Bottom Footer -->
    <footer class="p-4 text-center text-[10px] text-slate-500 border-t border-slate-800/80">
        {{ config('app.name', 'Sekolah') }} • Presensi Geofencing GPS Guru & Staff
    </footer>
</body>
</html>
