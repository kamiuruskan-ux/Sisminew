<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal Scanner Sidik Jari Guru (HID DigitalPersona 4500) - {{ config('app.name', 'Sekolah') }}</title>

    <!-- Google Fonts Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- DigitalPersona Web SDK Official Bundles -->
    <script src="{{ file_exists(public_path('vendor/digitalpersona/websdk.client.bundle.min.js')) ? asset('vendor/digitalpersona/websdk.client.bundle.min.js') : asset('system/public/vendor/digitalpersona/websdk.client.bundle.min.js') }}" onerror="if(!this.dataset.retry){this.dataset.retry=1;this.src='{{ asset('system/public/vendor/digitalpersona/websdk.client.bundle.min.js') }}';}"></script>
    <script>
        if (typeof window !== 'undefined' && window.WebSdk && typeof window.WebSdkCore === 'undefined') {
            window.WebSdkCore = window.WebSdk;
        }
    </script>
    <script src="{{ file_exists(public_path('vendor/digitalpersona/dp.core.umd.min.js')) ? asset('vendor/digitalpersona/dp.core.umd.min.js') : asset('system/public/vendor/digitalpersona/dp.core.umd.min.js') }}" onerror="if(!this.dataset.retry){this.dataset.retry=1;this.src='{{ asset('system/public/vendor/digitalpersona/dp.core.umd.min.js') }}';}"></script>
    <script src="{{ file_exists(public_path('vendor/digitalpersona/dp.devices.umd.min.js')) ? asset('vendor/digitalpersona/dp.devices.umd.min.js') : asset('system/public/vendor/digitalpersona/dp.devices.umd.min.js') }}" onerror="if(!this.dataset.retry){this.dataset.retry=1;this.src='{{ asset('system/public/vendor/digitalpersona/dp.devices.umd.min.js') }}';}"></script>

    <!-- Attendance Modular Services -->
    <script src="{{ file_exists(public_path('js/attendance/LoggerService.js')) ? asset('js/attendance/LoggerService.js') : asset('system/public/js/attendance/LoggerService.js') }}" onerror="if(!this.dataset.retry){this.dataset.retry=1;this.src='{{ asset('system/public/js/attendance/LoggerService.js') }}';}"></script>
    <script src="{{ file_exists(public_path('js/attendance/FingerprintService.js')) ? asset('js/attendance/FingerprintService.js') : asset('system/public/js/attendance/FingerprintService.js') }}" onerror="if(!this.dataset.retry){this.dataset.retry=1;this.src='{{ asset('system/public/js/attendance/FingerprintService.js') }}';}"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #070B11;
            color: #F8FAFC;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @keyframes fingerprintPulse {
            0% { transform: scale(0.97); opacity: 0.7; box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); }
            50% { transform: scale(1.03); opacity: 1; box-shadow: 0 0 45px rgba(16, 185, 129, 0.5); }
            100% { transform: scale(0.97); opacity: 0.7; box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); }
        }
        .animate-finger-pulse {
            animation: fingerprintPulse 2.4s infinite ease-in-out;
        }
        @keyframes scanBeam {
            0% { top: 10%; opacity: 0.2; }
            50% { top: 85%; opacity: 0.9; }
            100% { top: 10%; opacity: 0.2; }
        }
        .animate-scan-beam {
            animation: scanBeam 1.8s infinite ease-in-out;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased selection:bg-emerald-500 selection:text-white"
      x-data="{
    activeTab: 'standby', // 'standby' or 'enroll'
    
    // Hardware State Indicator (HID DigitalPersona 4500)
    deviceConnected: false,
    sslUnauthorized: false,
    deviceName: 'HID DigitalPersona U.are.U 4500',
    deviceStatus: 'Disconnected', // 'Ready', 'Busy', 'Capturing Fingerprint', 'Disconnected', 'Error', 'Timeout'
    sensorArmed: false,
    activeFormatName: 'Intermediate (Format 2)',
    dpDeviceUid: null,
    webSocket: null,
    reconnectTimer: null,

    // Real Fingerprint Capture Progression
    // 'idle' -> 'finger_detected' -> 'capturing' -> 'extracting' -> 'success' -> 'error'
    scanStage: 'idle',
    scanMessage: 'Menunggu jari ditempelkan pada scanner USB...',
    qualityScore: null,
    scannedTeacher: null,
    scannedTime: '',
    scannedAction: '',
    scannedSession: '',

    // Production Enrollment State (3 Scans Required)
    enrollTeacherId: '{{ $selectedTeacherId ?? '' }}',
    enrollStep: 0, // 0, 1, 2, 3
    enrollSamples: [],
    enrollStatus: 'idle',
    enrollMessage: 'Pilih guru dan tempelkan jari 3 kali pada scanner untuk merekam template.',

    // Live Clock
    currentTime: '',
    currentDate: '',

    // Live SSE Event Stream
    sseSource: null,

    init() {
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);
        this.initDigitalPersonaSdk();
        this.initLiveAttendanceStream();
    },

    async initDigitalPersonaSdk() {
        if (window.AttendanceFingerprintService) {
            window.AttendanceFingerprintService.on('statusChange', (state) => {
                this.deviceConnected = (state.status !== 'device_disconnected' && state.status !== 'service_unavailable' && state.status !== 'ssl_unauthorized' && state.status !== 'error');
                this.deviceStatus = state.badge;
                this.sslUnauthorized = (state.status === 'ssl_unauthorized');
                this.deviceName = window.AttendanceFingerprintService.deviceName || 'HID DigitalPersona U.are.U 4500';
                this.sensorArmed = window.AttendanceFingerprintService.isAcquiring || (state.status === 'waiting_finger' || state.status === 'reading');
                
                const fmt = window.AttendanceFingerprintService.workingFormat || state.format;
                this.activeFormatName = fmt === 2 ? 'Intermediate (Format 2)' : (fmt === 1 ? 'Raw Sensor (1)' : (fmt === 5 ? 'PNG Image (5)' : 'Intermediate (Format 2)'));

                if (state.status === 'device_connected') {
                    this.scanStage = 'idle';
                    this.sslUnauthorized = false;
                    if (this.activeTab === 'standby') {
                        this.scanMessage = '🟢 Scanner terhubung. Klik sensor atau Tes Sensor untuk mengaktifkan.';
                    }
                } else if (state.status === 'waiting_finger') {
                    this.scanStage = 'idle';
                    this.sslUnauthorized = false;
                    if (this.activeTab === 'standby') {
                        this.scanMessage = '🟡 Sensor optik aktif. Tempelkan jari pada kaca scanner...';
                    }
                } else if (state.status === 'reading') {
                    this.scanStage = 'finger_detected';
                    this.scanMessage = '🔵 Sedang membaca sidik jari...';
                } else if (state.status === 'sample_acquired') {
                    this.scanStage = 'capturing';
                    this.scanMessage = '✅ Fingerprint berhasil dibaca! Memverifikasi...';
                } else if (state.status === 'ssl_unauthorized') {
                    this.scanStage = 'error';
                    this.sslUnauthorized = true;
                    this.scanMessage = '⚠️ Izin browser diperlukan: Buka port 127.0.0.1 di tab baru (1 kali saja).';
                } else if (state.status === 'service_unavailable') {
                    this.scanStage = 'error';
                    this.sslUnauthorized = true;
                    this.scanMessage = '🔴 Service DigitalPersona belum diizinkan atau tidak aktif pada https://127.0.0.1:52181.';
                } else if (state.status === 'device_disconnected') {
                    this.scanStage = 'idle';
                    this.sslUnauthorized = false;
                    this.scanMessage = '🔴 Scanner tidak ditemukan. Silakan sambungkan kabel USB scanner ke PC.';
                } else if (state.status === 'error') {
                    this.scanStage = 'error';
                    this.scanMessage = state.text;
                }
            });

            window.AttendanceFingerprintService.on('sampleCaptured', (sampleData) => {
                this.onHardwareSampleCaptured(sampleData);
            });

            await window.AttendanceFingerprintService.init();
        }
    },

    async rearmSensor() {
        if (window.AttendanceFingerprintService) {
            this.scanMessage = '🔄 Mengaktifkan sensor optik scanner...';
            const ok = await window.AttendanceFingerprintService.startCapture(true);
            if (ok) {
                this.sensorArmed = true;
                this.playAudio('success');
                if (this.activeTab === 'standby') {
                    this.scanMessage = '🟡 Sensor optik aktif. Tempelkan jari pada kaca scanner...';
                } else {
                    this.enrollMessage = this.enrollTeacherId 
                        ? `Guru dipilih! Silakan tempelkan jari pada scanner untuk Scan ${this.enrollStep + 1}/3.`
                        : 'Pilih guru di dropdown terlebih dahulu.';
                }
            } else {
                this.sensorArmed = false;
                this.scanMessage = 'Sensor scanner belum siap. Klik sensor pad atau Deteksi USB untuk mencoba lagi.';
            }
            return ok;
        }
        return false;
    },

    async stopSensor() {
        if (window.AttendanceFingerprintService) {
            await window.AttendanceFingerprintService.stopCapture();
            this.sensorArmed = false;
            this.scanMessage = 'Sensor optik dinonaktifkan sementara. Klik Tes Sensor untuk mengaktifkan kembali.';
        }
    },

    openBrowserSslApproval() {
        if (window.AttendanceFingerprintService) {
            window.AttendanceFingerprintService.openSslAuthorization();
        } else {
            window.open('https://127.0.0.1:52181/get_connection', '_blank');
        }
    },

    async pairUsbScanner() {
        this.scanMessage = 'Memeriksa scanner DigitalPersona pada 127.0.0.1:52181...';
        if (window.AttendanceFingerprintService) {
            const ok = await window.AttendanceFingerprintService.refreshScanner();
            if (ok) {
                this.sslUnauthorized = false;
                this.sensorArmed = true;
                this.playAudio('success');
                return;
            }
        }
    },

    updateClock() {
        const now = new Date();
        this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    },

    playAudio(type = 'success') {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);

            if (type === 'success') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.1);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            } else if (type === 'already') {
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(440, ctx.currentTime);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
                osc.start();
                osc.stop(ctx.currentTime + 0.25);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, ctx.currentTime);
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                osc.start();
                osc.stop(ctx.currentTime + 0.4);
            }
        } catch(e) {}
    },

    // 1. DigitalPersona Scanner lifecycle managed via AttendanceFingerprintService (@digitalpersona/devices)

    // 2. Real Fingerprint Capture Processing Pipeline
    onHardwareSampleCaptured(sampleData) {
        if (this.activeTab === 'standby') {
            this.scanStage = 'capturing';
            this.deviceStatus = 'Capturing Fingerprint';
            this.scanMessage = 'Capturing...';

            setTimeout(() => {
                this.scanStage = 'extracting';
                this.scanMessage = 'Extracting Template & Mencocokkan...';
                this.verifyFingerprintWithServer(sampleData);
            }, 250);
        } else if (this.activeTab === 'enroll') {
            this.recordEnrollmentSample(sampleData);
        }
    },

    // 3. Attendance Verification via Backend Unified Pipeline
    async verifyFingerprintWithServer(sampleData) {
        this.deviceStatus = 'Busy';

        try {
            const res = await fetch('{{ route('admin.teacher-attendances.fingerprint.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    fingerprint_sample: sampleData,
                    device_name: this.deviceName
                })
            });

            const data = await res.json();

            if (data.success) {
                this.scannedTeacher = data.teacher;
                this.scannedTime = new Date().toLocaleTimeString('id-ID');
                this.scannedSession = data.session_name || data.action_type || 'Presensi';
                this.qualityScore = data.confidence || 96;

                if (data.already_complete) {
                    this.scanStage = 'already';
                    this.deviceStatus = 'Ready';
                    this.scanMessage = data.message;
                    this.playAudio('already');
                } else {
                    this.scanStage = 'success';
                    this.deviceStatus = 'Ready';
                    this.scannedAction = data.session_name || (data.action_type === 'check_in' ? 'MASUK' : 'PULANG');
                    this.scanMessage = 'Fingerprint Captured Successfully! ' + data.message;
                    this.playAudio('success');

                    this.prependLiveAttendance({
                        name: data.teacher.name,
                        time: this.scannedTime,
                        action: this.scannedAction,
                        method: 'Sidik Jari (HID 4500)'
                    });
                }

                setTimeout(() => {
                    this.scanStage = 'idle';
                    this.scanMessage = 'Scanner Siap. Tempelkan jari guru berikutnya...';
                    this.deviceStatus = 'Ready';
                }, 4500);
            } else {
                this.scanStage = 'error';
                this.deviceStatus = 'Ready';
                this.scanMessage = data.message || 'Sidik jari tidak dikenali.';
                this.playAudio('error');

                setTimeout(() => {
                    this.scanStage = 'idle';
                    this.scanMessage = 'Scanner Siap. Tempelkan jari guru berikutnya...';
                }, 3500);
            }
        } catch(err) {
            this.scanStage = 'error';
            this.deviceStatus = 'Error';
            this.scanMessage = 'Terjadi kesalahan komunikasi dengan server.';
            this.playAudio('error');

            setTimeout(() => {
                this.scanStage = 'idle';
                this.scanMessage = 'Scanner Siap. Tempelkan jari guru...';
                this.deviceStatus = 'Ready';
            }, 3500);
        }
    },

    switchTab(tab) {
        this.activeTab = tab;
        setTimeout(() => {
            this.rearmSensor();
        }, 200);
    },

    onTeacherSelected() {
        if (this.enrollTeacherId) {
            this.enrollStep = 0;
            this.enrollSamples = [];
            this.enrollStatus = 'idle';
            this.enrollMessage = 'Guru dipilih! Silakan tempelkan jari pada scanner untuk Scan 1/3.';
            setTimeout(() => {
                this.rearmSensor();
            }, 250);
        } else {
            this.enrollMessage = 'Pilih guru dan tempelkan jari 3 kali pada scanner untuk merekam template.';
        }
    },

    // 4. Production Enrollment: 3 Real Physical Scans
    recordEnrollmentSample(sampleData) {
        if (!this.enrollTeacherId) {
            this.enrollStatus = 'error';
            this.enrollMessage = '⚠️ Pilih nama guru terlebih dahulu di dropdown sebelum menempelkan jari!';
            this.playAudio('error');
            return;
        }

        this.enrollSamples.push(sampleData);
        this.enrollStep = this.enrollSamples.length;
        this.playAudio('success');

        if (this.enrollStep < 3) {
            this.enrollStatus = 'scanning';
            this.enrollMessage = `Scan ${this.enrollStep}/3 berhasil! Angkat dan tempelkan jari yang sama sekali lagi...`;
        } else {
            this.enrollStatus = 'saving';
            this.enrollMessage = '3 Scan selesai! Mengekstrak & memverifikasi konsistensi template...';
            this.submitEnrollmentToServer();
        }
    },

    async submitEnrollmentToServer() {
        try {
            const res = await fetch('{{ route('admin.teacher-attendances.fingerprint.register') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: this.enrollTeacherId,
                    samples: this.enrollSamples
                })
            });

            const data = await res.json();

            if (data.success) {
                this.enrollStatus = 'done';
                this.enrollMessage = data.message;
                this.playAudio('success');
                this.enrollSamples = [];

                setTimeout(() => {
                    this.enrollStep = 0;
                    this.enrollStatus = 'idle';
                    this.activeTab = 'standby';
                }, 3000);
            } else {
                this.enrollStatus = 'error';
                this.enrollMessage = data.message || 'Perekaman gagal.';
                this.playAudio('error');
                // Allow retry if mismatch
                this.enrollSamples = [];
                this.enrollStep = 0;
            }
        } catch(err) {
            this.enrollStatus = 'error';
            this.enrollMessage = 'Gagal menyimpan template biometrik ke server.';
            this.playAudio('error');
            this.enrollSamples = [];
            this.enrollStep = 0;
        }
    },

    // 5. Server-Sent Events (SSE) Live Broadcast Stream
    initLiveAttendanceStream() {
        try {
            if (window.EventSource) {
                this.sseSource = new EventSource('{{ route('admin.teacher-attendances.stream') }}');

                this.sseSource.addEventListener('attendance_recorded', (e) => {
                    const eventData = JSON.parse(e.data);
                    if (eventData) {
                        this.prependLiveAttendance({
                            name: eventData.teacher_name,
                            time: eventData.time,
                            action: eventData.session || eventData.status_label,
                            method: eventData.method_label
                        });
                    }
                });

                this.sseSource.onerror = () => {
                    // Auto reconnects natively in browser EventSource
                };
            }
        } catch(e) {}
    },

    // Log hardware telemetry event to server
    async logHardwareEvent(event, details = {}) {
        try {
            await fetch('{{ route('admin.teacher-attendances.fingerprint.device-event') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    event: event,
                    device_name: this.deviceName,
                    details: details
                })
            });
        } catch(e) {}
    },

    prependLiveAttendance(item) {
        const container = document.getElementById('liveAttendanceList');
        if (container) {
            const div = document.createElement('div');
            div.className = 'p-3 rounded-2xl bg-emerald-950/30 border border-emerald-500/30 flex items-center justify-between text-xs transition-all animate-pulse';
            div.innerHTML = `
                <div class='flex items-center space-x-3'>
                    <div class='w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs'>
                        ✓
                    </div>
                    <div>
                        <div class='font-bold text-white'>${item.name}</div>
                        <div class='text-[10px] text-emerald-400 font-mono'>${item.method} • ${item.action}</div>
                    </div>
                </div>
                <span class='font-mono font-bold text-white bg-slate-800 px-2 py-1 rounded-lg border border-slate-700'>${item.time}</span>
            `;
            container.prepend(div);
        }
    }
}">

    <!-- Top Navigation Header -->
    <header class="bg-slate-900/80 backdrop-blur-xl border-b border-slate-800 px-6 py-4 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.teacher-attendances.index') }}" class="p-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700" title="Kembali">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full" :class="deviceConnected ? 'bg-emerald-500 animate-ping' : 'bg-rose-500'"></span>
                        <h1 class="text-lg font-black tracking-tight text-white flex items-center space-x-2">
                            <span>TERMINAL SCANNER SIDIK JARI</span>
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold tracking-wider rounded-md border"
                                  :class="deviceConnected ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20'"
                                  x-text="deviceName"></span>
                        </h1>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Sistem Presensi Biometrik Hardware Meja Piket Terpadu</p>
                </div>
            </div>

            <!-- Device Connection Indicator & Live Clock -->
            <div class="flex items-center space-x-4">
                <!-- Device Status Indicator Badge & Pair Button -->
                <div class="flex items-center gap-2">
                    <div class="flex items-center space-x-2.5 px-3.5 py-1.5 rounded-xl border text-xs font-semibold transition-all"
                         :class="deviceConnected ? 'bg-emerald-950/40 border-emerald-500/30 text-emerald-300' : (sslUnauthorized ? 'bg-amber-950/40 border-amber-500/30 text-amber-300' : 'bg-rose-950/40 border-rose-500/30 text-rose-300')">
                        <span class="w-2 h-2 rounded-full" :class="deviceConnected ? 'bg-emerald-400 animate-pulse' : (sslUnauthorized ? 'bg-amber-400 animate-ping' : 'bg-rose-400')"></span>
                        <span x-text="deviceConnected ? '🟢 ' + deviceName + ' (' + deviceStatus + ')' : (sslUnauthorized ? '⚠️ Perlu Izin Browser' : '🔴 Scanner Belum Terbaca')"></span>
                    </div>

                    <button type="button" @click="sslUnauthorized ? openBrowserSslApproval() : pairUsbScanner()"
                            class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 hover:text-white border border-slate-700 transition flex items-center gap-1.5 cursor-pointer"
                            :title="sslUnauthorized ? 'Buka Izin Port 127.0.0.1' : 'Hubungkan / Deteksi Scanner USB'">
                        <span x-text="sslUnauthorized ? '🚀' : '🔌'"></span>
                        <span class="hidden md:inline" x-text="sslUnauthorized ? 'Buka Izin Browser' : 'Deteksi USB'"></span>
                    </button>
                </div>

                <!-- Clock -->
                <div class="text-right hidden sm:block">
                    <div class="text-base font-black font-mono tracking-wider text-emerald-400" x-text="currentTime"></div>
                    <div class="text-[11px] text-slate-400 font-medium" x-text="currentDate"></div>
                </div>

                <!-- Fullscreen Toggle -->
                <button type="button" @click="document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()"
                        class="p-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700" title="Layar Penuh">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1v4m0-4h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Side: Terminal Scanner Hardware Area (7 Cols) -->
        <section class="lg:col-span-7 space-y-6">
            
            <!-- Navigation Tab: Standby vs Enroll -->
            <div class="flex items-center p-1.5 bg-slate-900/90 rounded-2xl border border-slate-800">
                <button type="button" @click="switchTab('standby')" 
                        :class="activeTab === 'standby' ? 'bg-emerald-600 text-white shadow-lg font-bold' : 'text-slate-400 hover:text-white font-semibold'"
                        class="flex-1 py-2.5 rounded-xl text-xs flex items-center justify-center space-x-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/></svg>
                    <span>Mode Presensi Aktif</span>
                </button>
                <button type="button" @click="switchTab('enroll')" 
                        :class="activeTab === 'enroll' ? 'bg-indigo-600 text-white shadow-lg font-bold' : 'text-slate-400 hover:text-white font-semibold'"
                        class="flex-1 py-2.5 rounded-xl text-xs flex items-center justify-center space-x-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Perekaman Sidik Jari (Enroll 3 Scan)</span>
                </button>
            </div>

            <!-- TAB 1: MODE STANDBY SCANNER -->
            <div x-show="activeTab === 'standby'" class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 sm:p-8 relative overflow-hidden backdrop-blur-md shadow-2xl">
                
                <!-- Background Accent Glow -->
                <div class="absolute -top-24 -left-24 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Animated Sensor Pad -->
                <div class="flex flex-col items-center justify-center py-6 text-center">
                    
                    <!-- Hardware Optical Sensor Glass Pad (Clickable to Re-arm) -->
                    <div @click="rearmSensor()"
                         title="Klik untuk mengaktifkan / memicu ulang sensor scanner"
                         style="min-height: 288px; height: 288px; width: 240px;"
                         class="relative w-60 h-72 rounded-3xl bg-slate-950 border-2 transition-all duration-500 flex flex-col items-center justify-center select-none shadow-2xl cursor-pointer group"
                         :class="{
                             'border-emerald-500/60 shadow-emerald-500/30 ring-2 ring-emerald-500/20': deviceConnected && sensorArmed && scanStage === 'idle',
                             'border-amber-400/80 shadow-amber-500/30': deviceConnected && !sensorArmed,
                             'border-amber-400 shadow-amber-500/40': scanStage === 'finger_detected',
                             'border-cyan-400 shadow-cyan-500/50 animate-pulse': scanStage === 'capturing' || scanStage === 'extracting',
                             'border-emerald-400 bg-emerald-950/40 shadow-emerald-500/50': scanStage === 'success',
                             'border-rose-500 bg-rose-950/40 shadow-rose-500/50': !deviceConnected || scanStage === 'error'
                         }">
                        
                        <!-- Realtime Sensor Arm Status Pill -->
                        <div class="absolute top-4 inset-x-0 text-center pointer-events-none">
                            <span class="text-[9px] font-mono font-black uppercase px-2.5 py-0.5 rounded-full border transition-all inline-flex items-center gap-1 shadow-sm"
                                  :class="sensorArmed ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 animate-pulse' : 'bg-amber-500/20 text-amber-300 border-amber-500/40'">
                                <span class="w-1.5 h-1.5 rounded-full" :class="sensorArmed ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                                <span x-text="sensorArmed ? 'SENSOR OPTIK AKTIF' : 'KLIK UNTUK AKTIFKAN'"></span>
                            </span>
                        </div>

                        <!-- Scanning Laser Beam -->
                        <div x-show="scanStage === 'capturing' || scanStage === 'extracting'" class="absolute inset-x-2 h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent rounded-full animate-scan-beam z-10 shadow-[0_0_15px_#22d3ee]"></div>

                        <!-- Fingerprint Vector Graphic with Concentric Rings -->
                        <div class="relative flex items-center justify-center my-auto">
                            <div class="absolute w-36 h-36 rounded-full border"
                                 :class="sensorArmed ? 'border-emerald-500/30 animate-finger-pulse' : 'border-slate-800'"></div>
                            <div class="absolute w-28 h-28 rounded-full border"
                                 :class="sensorArmed ? 'border-emerald-500/50' : 'border-slate-800'"></div>

                            <svg class="w-24 h-24 transition-colors duration-300 group-hover:scale-105 transform"
                                 :class="{
                                     'text-emerald-400/90': deviceConnected && sensorArmed && scanStage === 'idle',
                                     'text-amber-400/70': deviceConnected && !sensorArmed,
                                     'text-amber-400 animate-pulse': scanStage === 'finger_detected',
                                     'text-cyan-400 animate-pulse': scanStage === 'capturing' || scanStage === 'extracting',
                                     'text-emerald-400': scanStage === 'success',
                                     'text-rose-400': !deviceConnected || scanStage === 'error'
                                 }"
                                 fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/>
                            </svg>
                        </div>

                        <!-- Hardware Logo Caption & Format -->
                        <div class="absolute bottom-4 inset-x-0 text-center">
                            <span class="text-[9px] font-mono tracking-widest uppercase font-bold block"
                                  :class="deviceConnected ? 'text-slate-400' : 'text-rose-500/80'"
                                  x-text="deviceConnected ? deviceName : 'SCANNER DISCONNECTED'"></span>
                            <span class="text-[8px] text-slate-500 font-mono mt-0.5" x-show="deviceConnected" x-text="'Mode Format: ' + activeFormatName"></span>
                        </div>
                    </div>

                    <!-- Step Progression Status Display Message -->
                    <div class="mt-6 max-w-md w-full">
                        <div class="text-xs uppercase tracking-widest font-mono font-bold mb-1"
                             :class="{
                                 'text-emerald-400': scanStage === 'success' || (deviceConnected && sensorArmed && scanStage === 'idle'),
                                 'text-amber-400': scanStage === 'finger_detected' || (deviceConnected && !sensorArmed),
                                 'text-cyan-400': scanStage === 'capturing' || scanStage === 'extracting',
                                 'text-rose-400': !deviceConnected || scanStage === 'error'
                             }"
                             x-text="
                                !deviceConnected ? (sslUnauthorized ? '⚠️ PERLU IZIN BROWSER' : '🔴 SCANNER BELUM TERHUBUNG') :
                                scanStage === 'finger_detected' ? 'FINGER DETECTED' :
                                scanStage === 'capturing' ? 'CAPTURING...' :
                                scanStage === 'extracting' ? 'EXTRACTING TEMPLATE...' :
                                scanStage === 'success' ? 'FINGERPRINT CAPTURED SUCCESSFULLY' :
                                scanStage === 'error' ? 'PEMINDAIAN GAGAL' :
                                (sensorArmed ? 'READY • TEMPELKAN JARI DI KACA SCANNER' : 'SENSOR BELUM AKTIF • KLIK UNTUK AKTIFKAN')
                             "></div>
                        <p class="text-xs text-slate-400" x-text="deviceConnected ? scanMessage : (sslUnauthorized ? 'Browser memerlukan otorisasi untuk berkomunikasi dengan driver scanner USB.' : 'Pastikan kabel scanner USB terpasang ke komputer piket.')"></p>
                        
                        <!-- SSL / Browser Bridge Authorization Assistant -->
                        <div x-show="sslUnauthorized" class="mt-4 p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-left space-y-4 shadow-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2 text-amber-400 text-xs font-black uppercase tracking-wider">
                                    <span class="text-base">⚙️</span>
                                    <span>Aktifkan Izin Scanner di Chrome / Edge (Langkah Terakhir)</span>
                                </div>
                                <span class="text-[10px] bg-emerald-500/20 text-emerald-300 font-mono px-2 py-0.5 rounded-full border border-emerald-500/30 font-bold">Port 52181 Terhubung</span>
                            </div>

                            <p class="text-xs text-slate-300 leading-relaxed">
                                Endpoint scanner lokal <strong>127.0.0.1:52181</strong> berhasil dibuka di tab Anda. Agar Chrome mengizinkan komunikasi WebSocket ke hardware scanner, aktifkan flag localhost:
                            </p>

                            <!-- Step Guide with Copy Button -->
                            <div class="bg-slate-950/80 p-4 rounded-xl border border-slate-800 space-y-3 text-xs">
                                <div class="space-y-1.5">
                                    <div class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">1. Salin alamat flag berikut & buka di tab baru:</div>
                                    <div class="flex items-center gap-2">
                                        <input type="text" readonly value="chrome://flags/#allow-insecure-localhost" id="chromeFlagInput"
                                               class="flex-1 bg-slate-900 text-emerald-400 font-mono text-xs px-3 py-2 rounded-lg border border-slate-700 select-all cursor-text focus:outline-none">
                                        <button type="button" @click="navigator.clipboard.writeText('chrome://flags/#allow-insecure-localhost'); alert('Alamat disalin! Tempelkan (Ctrl+V) pada tab baru Chrome lalu tekan Enter.');"
                                                class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer flex-shrink-0">
                                            <span>📋</span>
                                            <span>Salin</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="space-y-1.5 pt-1 border-t border-slate-800">
                                    <div class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">2. Ubah dari \"Default\" menjadi \"Enabled\"</div>
                                    <p class="text-[11px] text-slate-400">Cari baris <em>\"Allow invalid certificates for resources loaded from localhost\"</em> lalu pilih <strong>Enabled</strong>.</p>
                                </div>

                                <div class="space-y-1.5 pt-1 border-t border-slate-800">
                                    <div class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">3. Klik tombol \"Relaunch\" di pojok kanan bawah Chrome</div>
                                    <p class="text-[11px] text-slate-400">Chrome akan restart sebentar dan scanner langsung 🟢 Terhubung otomatis!</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 pt-1">
                                <button type="button" @click="pairUsbScanner()"
                                        class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs rounded-xl shadow-lg transition flex items-center gap-2 cursor-pointer">
                                    <span>🔄</span>
                                    <span>Cek Koneksi Ulang</span>
                                </button>
                                <button type="button" @click="openBrowserSslApproval()"
                                        class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5 cursor-pointer">
                                    <span>🔗</span>
                                    <span>Buka 127.0.0.1:52181</span>
                                </button>
                            </div>
                        </div>

                        <div x-show="!deviceConnected && !sslUnauthorized" class="mt-3">
                            <button type="button" @click="pairUsbScanner()"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-2 mx-auto cursor-pointer">
                                <span>🔌</span>
                                <span>Deteksi Ulang Scanner USB</span>
                            </button>
                        </div>
                    </div>

                    <!-- Result Notification Banner (When Success / Already) -->
                    <template x-if="scannedTeacher">
                        <div class="mt-6 w-full max-w-md p-4 rounded-2xl border transition-all duration-300 flex items-center space-x-4 text-left"
                             :class="{
                                 'bg-emerald-950/50 border-emerald-500/40 text-emerald-200': scanStage === 'success',
                                 'bg-amber-950/50 border-amber-500/40 text-amber-200': scanStage === 'already',
                                 'bg-slate-800/60 border-slate-700 text-slate-300': scanStage === 'idle'
                             }">
                            <div class="w-14 h-14 rounded-2xl overflow-hidden bg-slate-800 flex-shrink-0 border border-white/10 flex items-center justify-center">
                                <template x-if="scannedTeacher.avatar">
                                    <img :src="scannedTeacher.avatar" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!scannedTeacher.avatar">
                                    <span class="text-xl font-bold text-slate-400 font-mono" x-text="scannedTeacher.name.charAt(0)"></span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-extrabold text-white truncate" x-text="scannedTeacher.name"></span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30" x-text="scannedAction"></span>
                                </div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">NIP: <span x-text="scannedTeacher.nip || '-'"></span></div>
                                <div class="text-[11px] text-slate-400 mt-1">Waktu: <span class="font-mono font-bold text-white" x-text="scannedTime"></span> • <span class="text-emerald-400 font-semibold" x-text="scannedSession"></span></div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Hardware Device Diagnostics Bar (No Fake Simulation) -->
                <div class="mt-6 pt-5 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="flex items-center space-x-2 text-slate-400">
                        <span class="w-2 h-2 rounded-full" :class="deviceConnected ? (sensorArmed ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400') : (sslUnauthorized ? 'bg-amber-400' : 'bg-rose-500')"></span>
                        <span>Perangkat: <strong class="text-white" x-text="deviceName"></strong></span>
                    </div>
                    <div class="flex items-center space-x-3 text-slate-400 font-mono text-[11px]">
                        <span>Status: <strong :class="sensorArmed ? 'text-emerald-400' : 'text-amber-400'" x-text="deviceStatus"></strong></span>
                        <span>Format: <strong class="text-indigo-400" x-text="activeFormatName"></strong></span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="rearmSensor()" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-emerald-500/30 rounded-lg text-[10px] font-bold flex items-center gap-1 cursor-pointer" title="Picukan ulang sensor">
                                <span>⚡</span>
                                <span>Tes Sensor</span>
                            </button>
                            <button type="button" @click="stopSensor()" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-rose-400 border border-rose-500/30 rounded-lg text-[10px] font-bold flex items-center gap-1 cursor-pointer" title="Hentikan sensor sementara">
                                <span>⏹️</span>
                                <span>Stop</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: MODE PEREKAMAN SIDIK JARI (ENROLLMENT 3 SCANS) -->
            <div x-show="activeTab === 'enroll'" class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 sm:p-8 backdrop-blur-md shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-white">Perekaman Biometrik Guru Baru</h3>
                        <p class="text-xs text-slate-400">Pindai sidik jari sebanyak 3 kali berturut-turut pada scanner fisik</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">3-Scan Enrollment</span>
                </div>

                <div class="mt-6 space-y-5">
                    <!-- Warning Notice If Teacher Not Selected -->
                    <div x-show="!enrollTeacherId" class="p-3.5 bg-amber-500/10 border border-amber-500/30 rounded-2xl text-amber-300 text-xs flex items-center gap-3">
                        <span class="text-lg flex-shrink-0">⚠️</span>
                        <div class="leading-relaxed">
                            <strong class="font-black">Langkah 1:</strong> Silakan pilih <strong>Nama Guru</strong> di dropdown bawah ini terlebih dahulu sebelum menempelkan jari ke scanner agar template dapat tersimpan.
                        </div>
                    </div>

                    <!-- Guru Selector -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Pilih Guru / Tenaga Kependidikan</label>
                        <select x-model="enrollTeacherId" @change="onTeacherSelected()" class="w-full bg-slate-800 text-white text-sm rounded-2xl px-4 py-3 border border-slate-700 focus:outline-none focus:border-indigo-500">
                            <option value="">-- Pilih Nama Guru --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->name }} (NIP: {{ $t->nip ?? '-' }}) {{ $t->fingerprint_registered_at ? '✓ [Terdaftar]' : '✕ [Belum]' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3 Steps Progress Bar Indicator -->
                    <div>
                        <div class="flex justify-between text-xs font-bold text-slate-300 mb-2">
                            <span>Progres Perekaman Jari Fisik:</span>
                            <span class="font-mono text-indigo-400" x-text="'Scan ' + enrollStep + '/3'"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="i in 3" :key="i">
                                <div class="h-3 rounded-full transition-all duration-300"
                                     :class="enrollStep >= i ? 'bg-indigo-500 shadow-md shadow-indigo-500/40' : 'bg-slate-800 border border-slate-700'"></div>
                            </template>
                        </div>
                    </div>

                    <!-- Sensor Instructions for Enrollment (Clickable to Re-arm) -->
                    <div @click="rearmSensor()"
                         title="Klik untuk mengaktifkan / memicu ulang sensor scanner"
                         class="p-6 rounded-2xl bg-slate-950 border text-center flex flex-col items-center justify-center transition cursor-pointer select-none group"
                         :class="deviceConnected ? (sensorArmed ? 'border-indigo-500/50 ring-2 ring-indigo-500/20' : 'border-amber-500/40') : (sslUnauthorized ? 'border-amber-500/40' : 'border-rose-500/40')">
                        
                        <!-- Status Pill in Enrollment -->
                        <div class="mb-3">
                            <span class="text-[9px] font-mono font-black uppercase px-2.5 py-0.5 rounded-full border transition-all inline-flex items-center gap-1 shadow-sm"
                                  :class="sensorArmed ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40 animate-pulse' : 'bg-amber-500/20 text-amber-300 border-amber-500/40'">
                                <span class="w-1.5 h-1.5 rounded-full" :class="sensorArmed ? 'bg-indigo-400' : 'bg-amber-400'"></span>
                                <span x-text="sensorArmed ? 'SENSOR OPTIK AKTIF' : 'KLIK UNTUK AKTIFKAN'"></span>
                            </span>
                        </div>

                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-3 group-hover:scale-105 transition transform"
                             :class="deviceConnected ? 'bg-indigo-500/10 text-indigo-400' : (sslUnauthorized ? 'bg-amber-500/10 text-amber-400' : 'bg-rose-500/10 text-rose-400')">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/></svg>
                        </div>
                        <div class="font-bold text-white text-sm" x-text="deviceConnected ? (enrollStep === 0 ? 'Tempelkan Jari Guru ke Kaca Scanner USB' : 'Angkat & Tempelkan Jari Sekali Lagi') : (sslUnauthorized ? '⚠️ Perlu Izin Browser Chrome' : 'Scanner Belum Terhubung')"></div>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm" x-text="deviceConnected ? enrollMessage : (sslUnauthorized ? 'Aktifkan flag localhost di Chrome untuk mengizinkan komunikasi scanner.' : 'Pastikan kabel USB terpasang ke komputer.')"></p>

                        <div x-show="!deviceConnected" class="mt-4 flex flex-wrap gap-2 justify-center">
                            <button type="button" @click.stop="switchTab('standby')"
                                    class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-1.5 cursor-pointer">
                                <span>⚙️</span>
                                <span>Lihat Petunjuk Aktivasi Scanner</span>
                            </button>
                            <button type="button" @click.stop="pairUsbScanner()"
                                    class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5 cursor-pointer">
                                <span>🔄</span>
                                <span>Deteksi Ulang</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Right Side: Live Feed & Statistik Hari Ini (5 Cols) -->
        <section class="lg:col-span-5 space-y-6">
            
            <!-- Statistik Singkat Card -->
            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800">
                    <div class="text-xs text-slate-400 font-semibold">Total Guru & Tendik</div>
                    <div class="text-2xl font-black font-mono text-white mt-1">{{ $teachers->count() }}</div>
                </div>
                <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800">
                    <div class="text-xs text-emerald-400 font-semibold">Hadir Hari Ini</div>
                    <div class="text-2xl font-black font-mono text-emerald-400 mt-1">{{ $todayAttendances->count() }}</div>
                </div>
            </div>

            <!-- Daftar Presensi Hari Ini (Live Stream SSE) -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 backdrop-blur-md shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-sm font-extrabold text-white flex items-center space-x-2">
                        <span>Aktivitas Presensi Hari Ini</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </h3>
                    <span class="text-xs font-mono text-slate-400">{{ count($todayAttendances) }} Guru</span>
                </div>

                <div id="liveAttendanceList" class="space-y-2.5 max-h-[480px] overflow-y-auto pr-1">
                    @forelse($todayAttendances as $att)
                        <div class="p-3 rounded-2xl bg-slate-800/40 border border-slate-700/60 flex items-center justify-between text-xs hover:bg-slate-800/70 transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-full bg-slate-700 text-slate-300 flex items-center justify-center font-bold text-xs border border-slate-600 flex-shrink-0">
                                    {{ substr($att->user->name ?? 'G', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-white leading-snug">{{ $att->user->name ?? 'Guru' }}</div>
                                    <div class="text-[10px] text-slate-400 flex items-center space-x-1.5 mt-0.5">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $att->method === 'fingerprint' ? 'bg-emerald-500/20 text-emerald-400' : ($att->method === 'mobile_gps' ? 'bg-cyan-500/20 text-cyan-400' : 'bg-slate-700 text-slate-300') }}">
                                            {{ $att->method_label }}
                                        </span>
                                        @if($att->session_name)
                                            <span class="text-slate-500">• {{ $att->session_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="font-mono font-bold text-emerald-400">{{ $att->check_in ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">Pulang: {{ $att->check_out ?? '-' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-500 text-xs">
                            Belum ada aktivitas presensi guru hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Panduan Meja Piket Card -->
            <div class="p-4 rounded-2xl bg-emerald-950/20 border border-emerald-500/20 text-xs text-slate-300 space-y-1.5">
                <div class="font-bold text-emerald-400 flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Info Sinkronisasi Otomatis</span>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Setiap kali guru melakukan tap sidik jari pada scanner USB ini, presensi dicatat ke dalam sesi yang sesuai (Pagi / Dzuhur / Pulang) dan otomatis mengunci tombol di HP guru secara realtime.
                </p>
            </div>
        </section>
    </main>

    <!-- Footer Bar -->
    <footer class="border-t border-slate-800/80 px-6 py-3 text-center text-[11px] text-slate-500">
        {{ config('app.name', 'Sekolah') }} • Terminal Presensi Biometrik Hardware HID DigitalPersona 4500 USB
    </footer>
</body>
</html>
