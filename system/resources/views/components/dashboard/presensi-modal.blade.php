@php
    $schoolLat = (float) Setting::get('school_latitude', -0.8917);
    $schoolLong = (float) Setting::get('school_longitude', 119.8707);
    $schoolRadius = (int) Setting::get('school_attendance_radius', 100);
    $timezoneLabel = Setting::get('school_timezone_label', 'WITA');
    $schoolName = Setting::get('school_name', config('app.name', 'SDIT AL-FAHMI PALU'));
    $attendanceSettings = app(\App\Services\AttendanceSessionService::class)->getScheduleSettings();
    $user = auth()->user();
@endphp

<!-- Ensure Required Modular Attendance Services are Loaded -->
<script src="{{ asset('js/attendance/LoggerService.js') }}"></script>
<script src="{{ asset('js/attendance/PermissionService.js') }}"></script>
<script src="{{ asset('js/attendance/RadiusService.js') }}"></script>
<script src="{{ asset('js/attendance/GPSService.js') }}"></script>
<script src="{{ asset('js/attendance/ScheduleService.js') }}"></script>
<script src="{{ asset('js/attendance/AttendanceService.js') }}"></script>

<!-- ============================================================ -->
<!-- UNIVERSAL PRESENSI MANDIRI GPS MODAL (RESPONSIVE POPUP) -->
<!-- ============================================================ -->
<div x-show="openPresensiModal" x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95">

    <div class="bg-white dark:bg-[#1A222C] rounded-3xl max-w-lg w-full p-5 sm:p-7 shadow-2xl border border-slate-200 dark:border-[#2E3A47] space-y-4 my-auto max-h-[92vh] overflow-y-auto" 
         @click.outside="openPresensiModal = false"
         x-data="presensiGpsModalApp()">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-[#2E3A47]">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-[#3C50E0] dark:text-indigo-400 flex items-center justify-center font-bold text-lg shadow-xs shrink-0">
                    📍
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white">Presensi Mandiri GPS</h3>
                    <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD]">{{ $schoolName }} &bull; {{ $timezoneLabel }}</p>
                </div>
            </div>
            <button type="button" @click="openPresensiModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-[#24303F] text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Live Server Clock & Dates Card (WITA Synchronized) -->
        <div class="rounded-2xl p-4 text-white text-center shadow-md relative overflow-hidden"
             style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#3C50E0') }} 0%, {{ Setting::get('secondary_color', '#2563eb') }} 100%);">
            
            <div class="text-[11px] font-bold text-white/85 uppercase tracking-widest mb-1 flex items-center justify-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Waktu Server ({{ $timezoneLabel }})</span>
            </div>
            
            <!-- Realtime live ticking clock -->
            <div class="text-3xl sm:text-4xl font-black font-mono tracking-wider text-white">
                <span x-text="currentTime">00:00:00</span>
                <span class="text-xs font-sans text-white/80" x-text="timezoneLabel">{{ $timezoneLabel }}</span>
            </div>

            <!-- Gregorian & Hijri Dates Stash -->
            <div class="flex flex-col items-center justify-center gap-1 mt-2 text-xs text-white/95 font-medium">
                <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-black/15">
                    <span>📅</span>
                    <span x-text="currentDateFormatted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-black/20 text-amber-200 font-bold text-[11px]">
                    <span>🌙</span>
                    <span x-text="hijriDate">8 Rabiul Awwal 1448 H</span>
                </div>
            </div>
        </div>

        <!-- Mode Selector: Reguler vs Dinas Luar -->
        <div class="space-y-1.5">
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

        <!-- Dinas Luar Note Input (if Dinas Luar active) -->
        <div x-show="attendanceMode === 'dinas_luar'" x-cloak class="p-3.5 rounded-2xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-purple-600 dark:text-purple-400">Moda Dinas Luar</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-200 dark:bg-purple-900/60 text-purple-800 dark:text-purple-300">Bebas Radius</span>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-purple-700 dark:text-purple-300 mb-1">Catatan / Tugas Dinas Luar *</label>
                <input type="text" x-model="dinasNotes" placeholder="Tuliskan tugas atau instansi tujuan..."
                       class="w-full text-xs px-3 py-2 rounded-xl border border-purple-300 dark:border-purple-700 bg-white dark:bg-[#24303F] text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 outline-none">
            </div>
        </div>

        <!-- Real-time GPS Radar Card (Robust Multi-Tier Detection) -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#24303F]/60 border border-slate-200/80 dark:border-[#2E3A47] space-y-2.5">
            <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full" :class="gpsLoading ? 'bg-amber-400 animate-ping' : (inRadius || attendanceMode === 'dinas_luar' ? 'bg-emerald-500' : 'bg-rose-500')"></span>
                    <span>Status Radar GPS</span>
                </span>
                <button type="button" @click="refreshGps()" class="text-[#3C50E0] dark:text-indigo-400 font-bold text-xs hover:underline flex items-center gap-1 cursor-pointer">
                    <span :class="gpsLoading ? 'animate-spin' : ''">🔄</span> Refresh GPS
                </button>
            <!-- Loading State -->
            <template x-if="gpsLoading">
                <div class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400">
                    <span class="w-4 h-4 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></span>
                    <span class="font-medium animate-pulse">Menghubungkan sensor GPS perangkat...</span>
                </div>
            </template>

            <!-- Error State -->
            <template x-if="gpsError && !gpsLoading">
                <div class="p-3.5 bg-rose-50 dark:bg-rose-950/40 rounded-2xl border border-rose-200 dark:border-rose-800 space-y-2">
                    <div class="flex items-start gap-2">
                        <span class="text-rose-600 text-sm mt-0.5">⚠️</span>
                        <p class="text-xs text-rose-700 dark:text-rose-300 font-semibold leading-relaxed" x-text="gpsError"></p>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <button type="button" @click="refreshGps()"
                                class="py-2 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-1 cursor-pointer">
                            <span>🔄</span>
                            <span>Coba Deteksi Ulang GPS</span>
                        </button>
                        <button type="button" @click="attendanceMode = 'dinas_luar'"
                                class="py-2 px-3 rounded-xl bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 font-bold text-xs hover:bg-purple-200 transition flex items-center justify-center gap-1 cursor-pointer">
                            <span>💼</span>
                            <span>Mode Dinas Luar</span>
                        </button>
                    </div>
                </div>
            </template>

            <!-- Success / Coords Connected State -->
            <template x-if="!gpsLoading && !gpsError">
                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">Jarak ke Sekolah:</span>
                        <div class="flex items-center gap-1.5 font-mono font-extrabold">
                            <span :class="inRadius || attendanceMode === 'dinas_luar' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                                  x-text="distanceMeters !== null ? distanceMeters + ' m (Maks {{ $schoolRadius }}m)' : '-'"></span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-sans"
                                  :class="inRadius || attendanceMode === 'dinas_luar' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300'"
                                  x-text="attendanceMode === 'dinas_luar' ? 'Bebas Radius' : (inRadius ? '✓ Dalam Radius' : 'Di Luar Radius')"></span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span>Koordinat Perangkat:</span>
                        <span class="font-mono" x-text="userLat ? userLat.toFixed(6) + ', ' + userLong.toFixed(6) : '-'"></span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400" x-show="accuracy">
                        <span>Akurasi Sensor:</span>
                        <span class="font-mono" x-text="accuracy ? '± ' + accuracy + ' meter' : '-'"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Session Selection Option (Simplified) -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">Sesi Presensi</label>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="selectedSessionType = 'check_in'"
                        :class="selectedSessionType === 'check_in' ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-extrabold ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'"
                        class="p-2.5 rounded-xl border text-center text-xs transition cursor-pointer">
                    <span class="block text-sm">🌅</span>
                    <span class="text-[11px] font-bold block mt-0.5">Pagi Masuk</span>
                </button>
                <button type="button" @click="selectedSessionType = 'midday'"
                        :class="selectedSessionType === 'midday' ? 'border-amber-500 bg-amber-50/60 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 font-extrabold ring-2 ring-amber-500/20' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'"
                        class="p-2.5 rounded-xl border text-center text-xs transition cursor-pointer">
                    <span class="block text-sm">☀️</span>
                    <span class="text-[11px] font-bold block mt-0.5">Dzuhur</span>
                </button>
                <button type="button" @click="selectedSessionType = 'check_out'"
                        :class="selectedSessionType === 'check_out' ? 'border-indigo-500 bg-indigo-50/60 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 font-extrabold ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'"
                        class="p-2.5 rounded-xl border text-center text-xs transition cursor-pointer">
                    <span class="block text-sm">🌇</span>
                    <span class="text-[11px] font-bold block mt-0.5">Sore Pulang</span>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="button" @click="submitAttendance()"
                    :disabled="isSubmitDisabled()"
                    :class="getSubmitButtonGradientClass()"
                    class="w-full py-3.5 px-4 rounded-2xl text-white font-black text-xs sm:text-sm shadow-lg hover:shadow-xl hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span x-show="!submitting" x-text="getSubmitIcon()">📍</span>
                <span x-show="submitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span x-text="getSubmitButtonLabel()">Simpan Presensi</span>
            </button>
        </div>

        <!-- Result Feedback Message -->
        <div x-show="feedbackMsg" x-cloak class="p-3 rounded-2xl text-xs font-bold transition-all"
             :class="feedbackSuccess ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 text-rose-800 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800'">
            <p x-text="feedbackMsg"></p>
        </div>

    </div>
</div>

<script>
function presensiGpsModalApp() {
    return {
        currentTime: '{{ now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('H:i:s') }}',
        currentDateFormatted: '{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}',
        hijriDate: '{{ $hijriDate ?? '' }}',
        timezoneLabel: '{{ $timezoneLabel }}',
        schoolLat: {{ $schoolLat }},
        schoolLong: {{ $schoolLong }},
        schoolRadius: {{ $schoolRadius }},
        schoolName: '{{ $schoolName }}',
        userLat: null,
        userLong: null,
        accuracy: null,
        distanceMeters: null,
        inRadius: false,
        gpsLoading: false,
        gpsError: null,
        attendanceMode: 'reguler', // 'reguler' | 'dinas_luar'
        dinasNotes: '',
        selectedSessionType: 'check_in',
        submitting: false,
        feedbackMsg: '',
        feedbackSuccess: false,
        clockTimer: null,

        init() {
            if (window.AttendanceScheduleService && typeof AttendanceScheduleService.init === 'function') {
                try {
                    AttendanceScheduleService.init({
                        timezoneLabel: this.timezoneLabel,
                        serverTime: '{{ now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('H:i:s') }}',
                        serverDate: '{{ now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('Y-m-d') }}'
                    });
                } catch (e) {}
            }

            this.updateClock();
            if (this.clockTimer) clearInterval(this.clockTimer);
            this.clockTimer = setInterval(() => this.updateClock(), 1000);

            // Auto-detect sensible initial session based on current hour
            const hour = new Date().getHours();
            if (hour >= 6 && hour < 12) {
                this.selectedSessionType = 'check_in';
            } else if (hour >= 12 && hour < 14) {
                this.selectedSessionType = 'midday';
            } else {
                this.selectedSessionType = 'check_out';
            }

            // Detect GPS on modal open
            this.detectGps();
        },

        updateClock() {
            if (window.AttendanceScheduleService && typeof AttendanceScheduleService.getServerTimeString === 'function') {
                const t = AttendanceScheduleService.getServerTimeString();
                if (t && t !== '00:00:00') {
                    this.currentTime = t;
                } else {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
                }
            } else {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
            }

            const now = new Date();
            this.currentDateFormatted = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Compute Hijri Date dynamically
            try {
                const formatter = new Intl.DateTimeFormat('id-TN-u-ca-islamic-umalqura', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                let hStr = formatter.format(now);
                if (!hStr.includes('H')) hStr += ' H';
                this.hijriDate = hStr;
            } catch (e) {
                this.hijriDate = '8 Rabiul Awwal 1448 H';
            }
        },

        async detectGps() {
            this.gpsLoading = true;
            this.gpsError = null;

            if (typeof navigator === 'undefined' || !navigator.geolocation) {
                this.gpsLoading = false;
                this.gpsError = 'Browser perangkat Anda tidak mendukung API geolokasi GPS.';
                return;
            }

            const getPos = (enableHigh) => {
                return new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: enableHigh,
                        timeout: 15000,
                        maximumAge: 0
                    });
                });
            };

            try {
                // Try High Accuracy GPS, with graceful fallback to network if timeout
                let pos = await getPos(true).catch(async (err) => {
                    if (err && (err.code === 2 || err.code === 3)) {
                        return await getPos(false);
                    }
                    throw err;
                });

                if (pos && pos.coords) {
                    this.userLat = pos.coords.latitude;
                    this.userLong = pos.coords.longitude;
                    this.accuracy = Math.round(pos.coords.accuracy || 0);
                    this.calculateDistance();
                    this.gpsLoading = false;
                }
            } catch (err) {
                this.gpsLoading = false;
                if (err && err.code === 1) {
                    this.gpsError = 'Izin lokasi belum diizinkan. Silakan klik ikon gembok/setelan di samping URL browser, pilih "Izinkan" (Allow) untuk Lokasi, lalu coba lagi.';
                } else if (err && err.code === 2) {
                    this.gpsError = 'Sensor lokasi tidak dapat menentukan posisi. Pastikan GPS/Layanan Lokasi perangkat aktif.';
                } else if (err && err.code === 3) {
                    this.gpsError = 'Waktu pencarian sinyal GPS habis. Silakan coba deteksi ulang atau pastikan perangkat berada di tempat dengan sinyal baik.';
                } else {
                    this.gpsError = err.message || 'Gagal membaca sensor lokasi perangkat.';
                }
            }
        },

        async refreshGps() {
            this.detectGps();
        },

        calculateDistance() {
            if (!this.userLat || !this.userLong) return;

            const R = 6371e3; // meters
            const toRad = (deg) => (deg * Math.PI) / 180;
            const φ1 = toRad(this.schoolLat);
            const φ2 = toRad(this.userLat);
            const Δφ = toRad(this.userLat - this.schoolLat);
            const Δλ = toRad(this.userLong - this.schoolLong);

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                      Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            this.distanceMeters = Math.round(R * c);

            this.inRadius = this.distanceMeters <= this.schoolRadius;
        },

        isSubmitDisabled() {
            if (this.submitting) return true;
            if (this.attendanceMode === 'reguler' && (!this.userLat || !this.inRadius)) return true;
            if (this.attendanceMode === 'dinas_luar' && (!this.userLat || !this.dinasNotes.trim())) return true;
            return false;
        },

        getSubmitIcon() {
            if (this.attendanceMode === 'dinas_luar') return '💼';
            if (this.selectedSessionType === 'check_in') return '🌅';
            if (this.selectedSessionType === 'midday') return '☀️';
            if (this.selectedSessionType === 'check_out') return '🌇';
            return '📍';
        },

        getSubmitButtonGradientClass() {
            if (this.isSubmitDisabled()) return 'bg-slate-400 opacity-70 cursor-not-allowed';
            if (this.attendanceMode === 'dinas_luar') {
                return 'bg-gradient-to-r from-purple-600 to-indigo-600 shadow-purple-600/30';
            }
            if (this.selectedSessionType === 'check_in') return 'bg-gradient-to-r from-emerald-600 to-teal-600 shadow-emerald-600/30';
            if (this.selectedSessionType === 'midday') return 'bg-gradient-to-r from-amber-500 to-orange-500 shadow-amber-500/30';
            if (this.selectedSessionType === 'check_out') return 'bg-gradient-to-r from-indigo-600 to-violet-600 shadow-indigo-600/30';
            return 'bg-gradient-to-r from-indigo-600 to-teal-600 shadow-indigo-600/30';
        },

        getSubmitButtonLabel() {
            if (this.submitting) return 'Memproses Presensi ke Server...';
            if (this.attendanceMode === 'dinas_luar') {
                return 'Simpan Presensi Dinas Luar';
            }
            if (this.selectedSessionType === 'check_in') return 'Simpan Presensi Masuk (Check-In)';
            if (this.selectedSessionType === 'midday') return 'Konfirmasi Presensi Sesi Siang (Dzuhur)';
            if (this.selectedSessionType === 'check_out') return 'Simpan Presensi Pulang (Check-Out)';
            return 'Simpan Presensi';
        },

        async submitAttendance() {
            if (this.isSubmitDisabled()) return;

            this.submitting = true;
            this.feedbackMsg = '';

            const payload = {
                action_type: this.selectedSessionType,
                type: this.selectedSessionType,
                attendance_mode: this.attendanceMode,
                work_location: this.attendanceMode === 'dinas_luar' ? 'outstation' : 'school',
                latitude: this.userLat,
                longitude: this.userLong,
                distance: this.distanceMeters || 0,
                notes: this.attendanceMode === 'dinas_luar' ? this.dinasNotes : null,
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
                this.feedbackSuccess = res.ok && data.success;
                this.feedbackMsg = data.message || (res.ok ? 'Presensi berhasil dicatat!' : 'Gagal memproses presensi.');

                if (this.feedbackSuccess) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                }
            } catch (e) {
                this.feedbackSuccess = false;
                this.feedbackMsg = 'Terjadi kendala jaringan saat mengirim data presensi.';
            } finally {
                this.submitting = false;
            }
        }
    };
}
</script>
