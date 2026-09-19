@php
    $schoolLat = (float) Setting::get('school_latitude', -0.8917);
    $schoolLong = (float) Setting::get('school_longitude', 119.8707);
    $schoolRadius = (int) Setting::get('school_attendance_radius', 100);
    $timezoneLabel = Setting::get('school_timezone_label', 'WITA');
    $schoolName = Setting::get('school_name', config('app.name', 'SDIT AL-FAHMI PALU'));
@endphp

<!-- Scripts Loader for Modular Attendance Services (Guard against double-loading) -->
<script>
    (function() {
        const scripts = [
            '{{ asset('js/attendance/LoggerService.js') }}',
            '{{ asset('js/attendance/PermissionService.js') }}',
            '{{ asset('js/attendance/RadiusService.js') }}',
            '{{ asset('js/attendance/GPSService.js') }}',
            '{{ asset('js/attendance/ScheduleService.js') }}',
            '{{ asset('js/attendance/AttendanceService.js') }}'
        ];
        scripts.forEach(src => {
            if (!document.querySelector(`script[src="${src}"]`)) {
                const s = document.createElement('script');
                s.src = src;
                document.head.appendChild(s);
            }
        });
    })();
</script>

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
            <button type="button" @click="openPresensiModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-[#24303F] text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Digital Clock & Dates Header -->
        <div class="rounded-2xl p-4 text-white text-center shadow-md relative overflow-hidden"
             style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#3C50E0') }} 0%, {{ Setting::get('secondary_color', '#2563eb') }} 100%);">
            <div class="text-xs font-bold text-white/80 uppercase tracking-widest mb-1 flex items-center justify-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Waktu Server Presensi</span>
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
                        class="py-2 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <span>🏛️</span>
                    <span>WFO (Di Sekolah)</span>
                </button>
                <button type="button" @click="attendanceMode = 'dinas_luar'"
                        :class="attendanceMode === 'dinas_luar' ? 'bg-white dark:bg-[#1A222C] text-purple-600 dark:text-purple-400 shadow-sm font-black' : 'text-slate-600 dark:text-slate-400 font-semibold'"
                        class="py-2 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <span>💼</span>
                    <span>Dinas Luar</span>
                </button>
            </div>
        </div>

        <!-- Dinas Luar Active Session Banner (Requirement 4) -->
        <div x-show="attendanceMode === 'dinas_luar'" x-cloak class="p-3.5 rounded-2xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-purple-600 dark:text-purple-400">Moda Dinas Luar</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-200 dark:bg-purple-900/60 text-purple-800 dark:text-purple-300">Bebas Geofence</span>
            </div>
            <p class="text-xs text-purple-900 dark:text-purple-100 font-medium">
                Anda sedang melakukan: <strong class="font-extrabold text-purple-700 dark:text-purple-300" x-text="dinasSessionInfo.dinasLabel">Absen Masuk (Dinas Luar)</strong>
            </p>
            <p class="text-[10px] text-purple-600 dark:text-purple-400 leading-relaxed">
                Sesi ditentukan otomatis berdasarkan waktu server aktif. Tidak perlu memilih sesi sendiri.
            </p>
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
                    <span class="w-2.5 h-2.5 rounded-full" :class="gpsLoading ? 'bg-amber-400 animate-ping' : (inRadius || attendanceMode === 'dinas_luar' ? 'bg-emerald-500' : 'bg-rose-500')"></span>
                    Radar Lokasi GPS Satelit
                </span>
                <button type="button" @click="refreshGps()" class="text-[#3C50E0] dark:text-indigo-400 font-bold text-xs hover:underline flex items-center gap-1 cursor-pointer">
                    <span :class="gpsLoading ? 'animate-spin' : ''">🔄</span> Refresh GPS
                </button>
            </div>

            <template x-if="gpsLoading">
                <p class="text-xs text-amber-600 dark:text-amber-400 animate-pulse font-medium">📡 Mendeteksi koordinat satelit presisi tinggi (15s timeout)...</p>
            </template>

            <template x-if="gpsError && !gpsLoading">
                <div class="p-3 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-800 space-y-2">
                    <p class="text-xs text-rose-700 dark:text-rose-300 font-semibold" x-text="gpsError"></p>
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <button type="button" @click="refreshGps()" class="px-3 py-1 rounded-lg bg-rose-600 text-white font-bold text-xs hover:bg-rose-700 cursor-pointer">
                            🔄 Coba Lagi
                        </button>
                        <button type="button" @click="attendanceMode = 'dinas_luar'" class="px-3 py-1 rounded-lg bg-purple-600 text-white font-bold text-xs hover:bg-purple-700 cursor-pointer">
                            💼 Beralih Mode Dinas Luar
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="!gpsLoading && !gpsError">
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Jarak ke Gerbang Sekolah:</span>
                        <span class="font-mono font-extrabold" :class="inRadius || attendanceMode === 'dinas_luar' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                              x-text="distanceMeters !== null ? distanceMeters + ' Meter (Maks ' + schoolRadius + 'm)' : '-'"></span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span>Koordinat Anda:</span>
                        <span class="font-mono" x-text="userLat ? userLat.toFixed(5) + ', ' + userLong.toFixed(5) : '-'"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Action Submit Button: Unified Intelligent Session Button (Requirement 4 & 5) -->
        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-[#2E3A47]">
            <button type="button" @click="submitAttendance()"
                    :disabled="isSubmitDisabled()"
                    class="w-full py-3.5 px-4 rounded-2xl font-extrabold text-xs sm:text-sm text-white shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="getSubmitButtonGradientClass()">
                <span x-show="!submitting" x-text="getSubmitIcon()"></span>
                <span x-show="submitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span x-text="getSubmitButtonLabel()">Simpan Presensi</span>
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
        schoolName: '{{ $schoolName }}',
        userLat: null,
        userLong: null,
        distanceMeters: null,
        inRadius: false,
        gpsLoading: false,
        gpsError: null,
        attendanceMode: 'reguler', // 'reguler' | 'dinas_luar'
        dinasNotes: '',
        submitting: false,
        feedbackMsg: '',
        feedbackSuccess: false,
        activeSession: null,
        dinasSessionInfo: {
            sessionType: 'check_in',
            sessionName: 'Sesi Pagi',
            dinasLabel: 'Absen Masuk (Dinas Luar)',
            isActive: true
        },

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);

            // Listen to ScheduleService
            if (window.AttendanceScheduleService) {
                AttendanceScheduleService.on('sessionChange', (session) => {
                    this.activeSession = session;
                    this.dinasSessionInfo = AttendanceScheduleService.getDinasLuarSessionInfo();
                });
                this.activeSession = AttendanceScheduleService.resolveCurrentSession();
                this.dinasSessionInfo = AttendanceScheduleService.getDinasLuarSessionInfo();
            }

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

            if (window.AttendanceScheduleService) {
                this.dinasSessionInfo = AttendanceScheduleService.getDinasLuarSessionInfo();
            }
        },

        async detectGps() {
            this.gpsLoading = true;
            this.gpsError = null;

            if (window.AttendanceGPSService) {
                try {
                    const coords = await AttendanceGPSService.init();
                    this.userLat = coords.latitude;
                    this.userLong = coords.longitude;
                    this.calculateDistance();
                    this.gpsLoading = false;
                } catch (err) {
                    this.gpsLoading = false;
                    this.gpsError = err.message || 'Gagal mendeteksi lokasi satelit GPS.';
                }
            } else {
                this.gpsLoading = false;
            }
        },

        async refreshGps() {
            this.gpsLoading = true;
            this.gpsError = null;

            if (window.AttendanceGPSService) {
                try {
                    const coords = await AttendanceGPSService.refreshLocation();
                    this.userLat = coords.latitude;
                    this.userLong = coords.longitude;
                    this.calculateDistance();
                } catch (err) {
                    this.gpsError = err.message || 'Gagal memperbarui lokasi satelit GPS.';
                } finally {
                    this.gpsLoading = false;
                }
            }
        },

        calculateDistance() {
            if (!this.userLat || !this.userLong) return;
            if (window.AttendanceRadiusService) {
                this.distanceMeters = AttendanceRadiusService.calculateDistance(
                    this.schoolLat,
                    this.schoolLong,
                    this.userLat,
                    this.userLong
                );
                this.inRadius = AttendanceRadiusService.isWithinRadius(this.distanceMeters, this.schoolRadius);
            }
        },

        isSubmitDisabled() {
            if (this.submitting) return true;
            if (this.attendanceMode === 'reguler' && !this.inRadius) return true;
            if (this.attendanceMode === 'dinas_luar' && !this.dinasNotes.trim()) return true;
            return false;
        },

        getSubmitIcon() {
            if (this.attendanceMode === 'dinas_luar') return '💼';
            const sess = this.activeSession?.type;
            if (sess === 'check_in') return '🌅';
            if (sess === 'midday') return '☀️';
            if (sess === 'check_out') return '🌇';
            return '📍';
        },

        getSubmitButtonGradientClass() {
            if (this.isSubmitDisabled()) return 'bg-slate-400 opacity-70 cursor-not-allowed';
            if (this.attendanceMode === 'dinas_luar') {
                return 'bg-gradient-to-r from-purple-600 to-indigo-600 shadow-purple-600/30';
            }
            const sess = this.activeSession?.type;
            if (sess === 'check_in') return 'bg-gradient-to-r from-emerald-600 to-teal-600 shadow-emerald-600/30';
            if (sess === 'midday') return 'bg-gradient-to-r from-amber-500 to-orange-500 shadow-amber-500/30';
            if (sess === 'check_out') return 'bg-gradient-to-r from-indigo-600 to-violet-600 shadow-indigo-600/30';
            return 'bg-gradient-to-r from-indigo-600 to-teal-600 shadow-indigo-600/30';
        },

        getSubmitButtonLabel() {
            if (this.submitting) return 'Memproses Presensi ke Server...';
            if (this.attendanceMode === 'dinas_luar') {
                return this.dinasSessionInfo.dinasLabel || 'Simpan Presensi Dinas Luar';
            }
            const sess = this.activeSession;
            if (sess?.type === 'check_in') return 'Presensi Masuk (Check-In)';
            if (sess?.type === 'midday') return 'Konfirmasi Presensi Dzuhur';
            if (sess?.type === 'check_out') return 'Presensi Pulang (Check-Out)';
            return sess?.action_label || 'Simpan Presensi';
        },

        async submitAttendance() {
            if (this.isSubmitDisabled()) return;

            this.submitting = true;
            this.feedbackMsg = '';

            try {
                if (window.AttendanceUnifiedService) {
                    const result = await AttendanceUnifiedService.submitGpsAttendance({
                        mode: this.attendanceMode,
                        session: this.activeSession || AttendanceScheduleService.resolveCurrentSession(),
                        coords: {
                            latitude: this.userLat,
                            longitude: this.userLong,
                            accuracy: 10
                        },
                        dinasNotes: this.dinasNotes
                    });

                    this.feedbackSuccess = result.success;
                    this.feedbackMsg = result.message;

                    if (result.success) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    }
                }
            } catch (e) {
                this.feedbackSuccess = false;
                this.feedbackMsg = 'Terjadi kendala saat mengirim presensi ke server.';
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
