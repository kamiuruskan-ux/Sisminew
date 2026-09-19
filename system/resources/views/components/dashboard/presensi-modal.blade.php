@php
    $schoolLat = (float) Setting::get('school_latitude', -0.8917);
    $schoolLong = (float) Setting::get('school_longitude', 119.8707);
    $schoolRadius = (int) Setting::get('school_attendance_radius', 100);
    $timezoneLabel = Setting::get('school_timezone_label', 'WITA');
    $schoolName = Setting::get('school_name', config('app.name', 'SDIT AL-FAHMI PALU'));
@endphp

<!-- ============================================================ -->
<!-- UNIVERSAL PRESENSI MANDIRI GPS MODAL (GLOBAL THEME INTEGRATED) -->
<!-- ============================================================ -->
<div x-show="openPresensiModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
    <div class="bg-white dark:bg-[#1A222C] rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 dark:border-[#2E3A47] space-y-5 my-8" 
         @click.outside="openPresensiModal = false"
         x-data="presensiGpsApp()">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-[#2E3A47]">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-[#3C50E0] dark:text-indigo-400 flex items-center justify-center font-bold text-lg">
                    📍
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white">Presensi Mandiri (GPS Lock)</h3>
                    <p class="text-xs text-[#64748B] dark:text-[#8A99AD]">{{ $schoolName }} &bull; {{ $timezoneLabel }}</p>
                </div>
            </div>
            <button type="button" @click="openPresensiModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-[#24303F] text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Digital Clock & Dates Header -->
        <div class="rounded-2xl p-4 text-white text-center shadow-md relative overflow-hidden"
             style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#3C50E0') }} 0%, {{ Setting::get('secondary_color', '#2563eb') }} 100%);">
            <div class="text-xs font-bold text-white/80 uppercase tracking-widest mb-1 flex items-center justify-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Waktu Presensi Terenkripsi</span>
            </div>
            <div class="text-3xl font-black font-mono tracking-wider text-white">
                <span x-text="currentTime">00:00:00</span>
                <span class="text-xs font-sans text-white/80" x-text="timezoneLabel">WITA</span>
            </div>
            <p class="text-xs text-white/90 font-medium mt-1" x-text="currentDateFormatted"></p>
        </div>

        <!-- Mode Selector: Reguler vs Dinas Luar -->
        <div class="space-y-2">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Moda Kehadiran</label>
            <div class="grid grid-cols-2 gap-2 bg-slate-100 dark:bg-[#24303F] p-1.5 rounded-2xl border border-slate-200 dark:border-[#2E3A47]">
                <button type="button" @click="attendanceMode = 'reguler'"
                        :class="attendanceMode === 'reguler' ? 'bg-white dark:bg-[#1A222C] text-[#3C50E0] dark:text-indigo-400 shadow-sm font-black' : 'text-slate-600 dark:text-slate-400 font-semibold'"
                        class="py-2 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5">
                    <span>🏛️</span>
                    <span>Reguler (Sekolah)</span>
                </button>
                <button type="button" @click="attendanceMode = 'dinas_luar'"
                        :class="attendanceMode === 'dinas_luar' ? 'bg-white dark:bg-[#1A222C] text-purple-600 dark:text-purple-400 shadow-sm font-black' : 'text-slate-600 dark:text-slate-400 font-semibold'"
                        class="py-2 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5">
                    <span>💼</span>
                    <span>Dinas / Tugas Luar</span>
                </button>
            </div>
        </div>

        <!-- Dinas Luar Note Input -->
        <div x-show="attendanceMode === 'dinas_luar'" x-cloak class="space-y-1.5">
            <label class="block text-xs font-bold text-purple-700 dark:text-purple-300">Catatan / Surat Tugas Dinas Luar *</label>
            <input type="text" x-model="dinasNotes" placeholder="Contoh: Menghadiri MGMP Asatidzah di Dinas Pendidikan..."
                   class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-purple-200 dark:border-purple-800 dark:bg-[#24303F] dark:text-white focus:ring-2 focus:ring-purple-500 outline-none">
        </div>

        <!-- Real-time GPS Radar Card -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#24303F]/60 border border-slate-200/80 dark:border-[#2E3A47] space-y-2.5">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full" :class="gpsLoading ? 'bg-amber-400 animate-ping' : (inRadius ? 'bg-emerald-500' : 'bg-rose-500')"></span>
                    Radar Lokasi GPS Satelit
                </span>
                <button type="button" @click="detectGps()" class="text-[#3C50E0] dark:text-indigo-400 font-bold text-xs hover:underline flex items-center gap-1 cursor-pointer">
                    <span>🔄</span> Refresh GPS
                </button>
            </div>

            <template x-if="gpsLoading">
                <p class="text-xs text-amber-600 dark:text-amber-400 animate-pulse font-medium">📡 Mendeteksi koordinat lintang &amp; bujur perangkat Anda...</p>
            </template>

            <template x-if="gpsError">
                <div class="p-3 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-800 space-y-2">
                    <p class="text-xs text-rose-700 dark:text-rose-300 font-semibold" x-text="gpsError"></p>
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <button type="button" @click="detectGps()" class="px-3 py-1 rounded-lg bg-rose-600 text-white font-bold text-xs hover:bg-rose-700">
                            🔄 Coba Lagi
                        </button>
                        <button type="button" @click="attendanceMode = 'dinas_luar'" class="px-3 py-1 rounded-lg bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700">
                            ✈️ Mode Dinas Luar
                        </button>
                        <button type="button" @click="enableGpsFallback()" class="px-3 py-1 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs hover:bg-slate-300">
                            Bypass Darurat
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="!gpsLoading && !gpsError">
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Jarak ke Gerbang Sekolah:</span>
                        <span class="font-mono font-extrabold" :class="inRadius ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                              x-text="distanceMeters !== null ? distanceMeters + ' Meter (Maks ' + schoolRadius + 'm)' : '-'"></span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span>Koordinat Anda:</span>
                        <span class="font-mono" x-text="userLat ? userLat.toFixed(5) + ', ' + userLong.toFixed(5) : '-'"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Action Submit Buttons -->
        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-[#2E3A47]">
            <!-- Check In Pagi Button -->
            <button type="button" @click="submitAttendance('check_in')"
                    :disabled="submitting || (attendanceMode === 'reguler' && !inRadius && !gpsBypass)"
                    class="w-full py-3 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-extrabold text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2">
                <span x-show="!submitting">🟢</span>
                <span x-show="submitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span x-text="submitting ? 'Memproses Presensi...' : 'Simpan Presensi Masuk (Check-In)'"></span>
            </button>

            <!-- Sesi Siang / Dzuhur Button -->
            <button type="button" @click="submitAttendance('session_afternoon')"
                    :disabled="submitting || (attendanceMode === 'reguler' && !inRadius && !gpsBypass)"
                    class="w-full py-2.5 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-2">
                <span>🕌</span>
                <span>Konfirmasi Hadir Sesi Siang / Sholat Dzuhur</span>
            </button>

            <!-- Check Out Sore / Pulang Button -->
            <button type="button" @click="submitAttendance('check_out')"
                    :disabled="submitting || (attendanceMode === 'reguler' && !inRadius && !gpsBypass)"
                    class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs shadow-xs transition-all flex items-center justify-center gap-2">
                <span>🔴</span>
                <span>Presensi Pulang / Selesai KBM (Check-Out)</span>
            </button>
        </div>

        <!-- Result Feedback Message -->
        <div x-show="feedbackMsg" x-cloak class="p-3.5 rounded-2xl text-xs font-bold"
             :class="feedbackSuccess ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200' : 'bg-rose-50 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200'">
            <p x-text="feedbackMsg"></p>
        </div>

    </div>
</div>

<script>
function presensiGpsApp() {
    return {
        currentTime: '00:00:00',
        currentDateFormatted: '',
        timezoneLabel: '{{ $timezoneLabel }}',
        schoolLat: {{ $schoolLat }},
        schoolLong: {{ $schoolLong }},
        schoolRadius: {{ $schoolRadius }},
        userLat: null,
        userLong: null,
        distanceMeters: null,
        inRadius: false,
        gpsLoading: false,
        gpsError: null,
        gpsBypass: false,
        attendanceMode: 'reguler',
        dinasNotes: '',
        submitting: false,
        feedbackMsg: '',
        feedbackSuccess: false,

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            this.detectGps();
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
            this.currentDateFormatted = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        detectGps() {
            if (!navigator.geolocation) {
                this.gpsError = 'Browser Anda tidak mendukung geolokasi GPS.';
                return;
            }

            this.gpsLoading = true;
            this.gpsError = null;

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.userLat = pos.coords.latitude;
                    this.userLong = pos.coords.longitude;
                    this.calculateDistance();
                    this.gpsLoading = false;
                },
                (err) => {
                    this.gpsLoading = false;
                    switch (err.code) {
                        case err.PERMISSION_DENIED:
                            this.gpsError = 'Izin akses lokasi GPS ditolak oleh browser/perangkat Anda.';
                            break;
                        case err.POSITION_UNAVAILABLE:
                            this.gpsError = 'Informasi lokasi GPS satelit tidak tersedia.';
                            break;
                        case err.TIMEOUT:
                            this.gpsError = 'Permintaan lokasi GPS waktu habis (timeout).';
                            break;
                        default:
                            this.gpsError = 'Gagal mendeteksi lokasi GPS.';
                    }
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
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

        enableGpsFallback() {
            this.gpsBypass = true;
            this.inRadius = true;
            this.userLat = this.schoolLat;
            this.userLong = this.schoolLong;
            this.distanceMeters = 5;
            this.gpsError = null;
        },

        async submitAttendance(actionType) {
            this.submitting = true;
            this.feedbackMsg = '';

            const payload = {
                action_type: actionType,
                latitude: this.userLat || this.schoolLat,
                longitude: this.userLong || this.schoolLong,
                distance: this.distanceMeters || 0,
                attendance_mode: this.attendanceMode,
                dinas_notes: this.dinasNotes
            };

            try {
                const res = await fetch('{{ route('admin.teacher-attendances.self-checkin') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                this.feedbackSuccess = data.success ?? false;
                this.feedbackMsg = data.message || 'Presensi berhasil diproses.';

                if (data.success) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } catch (e) {
                this.feedbackSuccess = false;
                this.feedbackMsg = 'Terjadi kendala jaringan saat mengirim presensi. Silakan coba kembali.';
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
