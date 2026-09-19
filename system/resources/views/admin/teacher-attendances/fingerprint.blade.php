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
    deviceName: 'HID DigitalPersona 4500',
    deviceStatus: 'Disconnected', // 'Ready', 'Busy', 'Capturing Fingerprint', 'Disconnected', 'Error', 'Timeout'
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
        this.initUsbHardwareDetection();
        this.connectDigitalPersonaService();
        this.initLiveAttendanceStream();
    },

    // 0. Native WebUSB & WebHID Real Hardware Detection
    async initUsbHardwareDetection() {
        if (navigator.usb) {
            try {
                const devices = await navigator.usb.getDevices();
                if (devices && devices.length > 0) {
                    const dev = devices[0];
                    this.deviceConnected = true;
                    this.deviceName = dev.productName || (dev.manufacturerName ? `${dev.manufacturerName} Optical Reader` : 'USB Optical Scanner');
                    this.deviceStatus = 'Ready';
                    this.scanMessage = `Scanner USB (${this.deviceName}) Terdeteksi. Menunggu jari ditempelkan...`;
                }
            } catch(e) {}

            navigator.usb.addEventListener('connect', (event) => {
                this.deviceConnected = true;
                this.deviceName = event.device.productName || 'USB Fingerprint Scanner';
                this.deviceStatus = 'Ready';
                this.scanMessage = `Scanner USB (${this.deviceName}) Terhubung. Siap digunakan.`;
                this.playAudio('success');
                this.logHardwareEvent('device_connected', { device: this.deviceName });
            });

            navigator.usb.addEventListener('disconnect', (event) => {
                this.deviceConnected = false;
                this.deviceStatus = 'Disconnected';
                this.scanMessage = 'Scanner USB terputus. Silakan hubungkan kembali kabel scanner.';
                this.playAudio('error');
                this.logHardwareEvent('device_removed');
            });
        }

        if (navigator.hid && !this.deviceConnected) {
            try {
                const hidDevices = await navigator.hid.getDevices();
                if (hidDevices && hidDevices.length > 0) {
                    const hdev = hidDevices[0];
                    this.deviceConnected = true;
                    this.deviceName = hdev.productName || 'HID Biometric Device';
                    this.deviceStatus = 'Ready';
                    this.scanMessage = `Scanner HID (${this.deviceName}) Terdeteksi.`;
                }
            } catch(e) {}
        }
    },

    async pairUsbScanner() {
        if (navigator.usb) {
            try {
                const device = await navigator.usb.requestDevice({ filters: [] });
                if (device) {
                    this.deviceConnected = true;
                    this.deviceName = device.productName || (device.manufacturerName ? `${device.manufacturerName} Scanner` : 'USB Fingerprint Scanner');
                    this.deviceStatus = 'Ready';
                    this.scanMessage = `Scanner (${this.deviceName}) berhasil dipasangkan. Silakan tempelkan jari.`;
                    this.playAudio('success');
                    this.logHardwareEvent('device_paired', { device: this.deviceName });
                    return;
                }
            } catch(e) {}
        }

        if (navigator.hid) {
            try {
                const hidDevices = await navigator.hid.requestDevice({ filters: [] });
                if (hidDevices && hidDevices.length > 0) {
                    this.deviceConnected = true;
                    this.deviceName = hidDevices[0].productName || 'HID Fingerprint Reader';
                    this.deviceStatus = 'Ready';
                    this.scanMessage = `Scanner (${this.deviceName}) terhubung.`;
                    this.playAudio('success');
                    return;
                }
            } catch(e) {}
        }

        alert('Tidak ada scanner USB yang dipilih atau browser tidak mengizinkan akses USB.');
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

    // 1. Continuous Real USB Device Detection (HID DigitalPersona WebSDK)
    connectDigitalPersonaService() {
        const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const wsUrl = wsProtocol + '//127.0.0.1:52181';

        try {
            if (this.webSocket) {
                try { this.webSocket.close(); } catch(e) {}
            }

            this.webSocket = new WebSocket(wsUrl);

            this.webSocket.onopen = () => {
                this.deviceConnected = true;
                this.deviceStatus = 'Ready';
                this.scanMessage = 'Scanner Siap. Tempelkan jari guru pada sensor...';
                console.log('[HID DigitalPersona] Desktop Service Connected on port 52181');

                this.logHardwareEvent('device_connected', { port: 52181 });

                // Enumerate connected optical hardware readers
                try {
                    this.webSocket.send(JSON.stringify({ command: 'EnumerateDevices' }));
                    this.webSocket.send(JSON.stringify({ command: 'StartAcquisition', SampleFormat: 'PngBiometric' }));
                } catch(e) {}
            };

            this.webSocket.onmessage = (event) => {
                try {
                    const msg = JSON.parse(event.data);

                    // A. Hardware Device Detected / Description
                    if (msg.event === 'DeviceConnected' || msg.devices || msg.DeviceDescription) {
                        this.deviceConnected = true;
                        this.deviceName = msg.DeviceDescription || (msg.devices && msg.devices[0]?.name) || 'HID DigitalPersona 4500';
                        this.deviceStatus = 'Ready';
                        if (msg.DeviceUid) {
                            this.dpDeviceUid = msg.DeviceUid;
                        }
                    }

                    // B. Finger Placed on Sensor
                    if (msg.event === 'FingerDetected' || msg.status === 'touch') {
                        this.scanStage = 'finger_detected';
                        this.deviceStatus = 'Capturing Fingerprint';
                        this.scanMessage = 'Jari terdeteksi pada sensor optik...';
                    }

                    // C. Biometric Capture in Progress
                    if (msg.event === 'CaptureStarted') {
                        this.scanStage = 'capturing';
                        this.deviceStatus = 'Busy';
                        this.scanMessage = 'Memindai kontur sidik jari...';
                    }

                    // D. Biometric Samples Acquired from Real Hardware
                    if (msg.event === 'SamplesAcquired' || msg.samples || msg.sample) {
                        const sampleData = (msg.samples && msg.samples[0]) || msg.sample || msg.data;
                        this.onHardwareSampleCaptured(sampleData);
                    }

                    // E. Sensor Quality Feedback
                    if (msg.event === 'QualityReported') {
                        this.qualityScore = msg.quality ?? 85;
                        if (msg.quality > 0) {
                            this.scanMessage = 'Kualitas sensor: tekan jari lebih mantap dan bersihkan prisma sensor.';
                        }
                    }

                    // F. Device Disconnected / Unplugged
                    if (msg.event === 'DeviceDisconnected') {
                        this.deviceConnected = false;
                        this.deviceStatus = 'Disconnected';
                        this.scanStage = 'idle';
                        this.scanMessage = 'Scanner USB terputus. Silakan hubungkan kembali scanner.';
                        this.logHardwareEvent('device_removed');
                    }
                } catch(err) {}
            };

            this.webSocket.onerror = () => {
                this.deviceConnected = false;
                this.deviceStatus = 'Disconnected';
                this.scanStage = 'idle';
            };

            this.webSocket.onclose = () => {
                this.deviceConnected = false;
                this.deviceStatus = 'Disconnected';
                this.scanStage = 'idle';
                
                // Continuous background reconnect retry without page reload
                clearTimeout(this.reconnectTimer);
                this.reconnectTimer = setTimeout(() => {
                    this.connectDigitalPersonaService();
                }, 3000);
            };
        } catch(e) {
            this.deviceConnected = false;
            this.deviceStatus = 'Disconnected';
        }
    },

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

    // 4. Production Enrollment: 3 Real Physical Scans
    recordEnrollmentSample(sampleData) {
        if (!this.enrollTeacherId) {
            alert('Pilih nama guru terlebih dahulu sebelum menempelkan jari!');
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
                         :class="deviceConnected ? 'bg-emerald-950/40 border-emerald-500/30 text-emerald-300' : 'bg-rose-950/40 border-rose-500/30 text-rose-300'">
                        <span class="w-2 h-2 rounded-full" :class="deviceConnected ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400'"></span>
                        <span x-text="deviceConnected ? '🟢 ' + deviceName + ' (' + deviceStatus + ')' : '🔴 Scanner Not Connected'"></span>
                    </div>

                    <button type="button" @click="pairUsbScanner()"
                            class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 hover:text-white border border-slate-700 transition flex items-center gap-1.5 cursor-pointer"
                            title="Hubungkan / Deteksi Scanner USB">
                        <span>🔌</span>
                        <span class="hidden md:inline">Deteksi USB</span>
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
                <button type="button" @click="activeTab = 'standby'" 
                        :class="activeTab === 'standby' ? 'bg-emerald-600 text-white shadow-lg font-bold' : 'text-slate-400 hover:text-white font-semibold'"
                        class="flex-1 py-2.5 rounded-xl text-xs flex items-center justify-center space-x-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/></svg>
                    <span>Mode Presensi Aktif</span>
                </button>
                <button type="button" @click="activeTab = 'enroll'" 
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
                    
                    <!-- Hardware Optical Sensor Glass Pad -->
                    <div class="relative w-52 h-64 rounded-3xl bg-slate-950 border-2 transition-all duration-500 flex flex-col items-center justify-center select-none shadow-2xl"
                         :class="{
                             'border-emerald-500/40 shadow-emerald-500/20': deviceConnected && scanStage === 'idle',
                             'border-amber-400 shadow-amber-500/30': scanStage === 'finger_detected',
                             'border-cyan-400 shadow-cyan-500/50 animate-pulse': scanStage === 'capturing' || scanStage === 'extracting',
                             'border-emerald-400 bg-emerald-950/40 shadow-emerald-500/50': scanStage === 'success',
                             'border-rose-500 bg-rose-950/40 shadow-rose-500/50': !deviceConnected || scanStage === 'error'
                         }">
                        
                        <!-- Scanning Laser Beam -->
                        <div x-show="scanStage === 'capturing' || scanStage === 'extracting'" class="absolute inset-x-2 h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent rounded-full animate-scan-beam z-10 shadow-[0_0_15px_#22d3ee]"></div>

                        <!-- Fingerprint Vector Graphic with Concentric Rings -->
                        <div class="relative flex items-center justify-center">
                            <div class="absolute w-36 h-36 rounded-full border"
                                 :class="deviceConnected ? 'border-emerald-500/20 animate-finger-pulse' : 'border-rose-500/20'"></div>
                            <div class="absolute w-28 h-28 rounded-full border"
                                 :class="deviceConnected ? 'border-emerald-500/40' : 'border-rose-500/30'"></div>

                            <svg class="w-24 h-24 transition-colors duration-300"
                                 :class="{
                                     'text-emerald-400/80': deviceConnected && scanStage === 'idle',
                                     'text-amber-400 animate-pulse': scanStage === 'finger_detected',
                                     'text-cyan-400 animate-pulse': scanStage === 'capturing' || scanStage === 'extracting',
                                     'text-emerald-400': scanStage === 'success',
                                     'text-rose-400': !deviceConnected || scanStage === 'error'
                                 }"
                                 fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/>
                            </svg>
                        </div>

                        <!-- Hardware Logo Caption -->
                        <div class="absolute bottom-4 inset-x-0 text-center">
                            <span class="text-[9px] font-mono tracking-widest uppercase font-bold"
                                  :class="deviceConnected ? 'text-slate-500' : 'text-rose-500/80'"
                                  x-text="deviceConnected ? deviceName : 'SCANNER DISCONNECTED'"></span>
                        </div>
                    </div>

                    <!-- Step Progression Status Display Message -->
                    <div class="mt-6 max-w-md">
                        <div class="text-xs uppercase tracking-widest font-mono font-bold mb-1"
                             :class="{
                                 'text-emerald-400': scanStage === 'success' || (deviceConnected && scanStage === 'idle'),
                                 'text-amber-400': scanStage === 'finger_detected',
                                 'text-cyan-400': scanStage === 'capturing' || scanStage === 'extracting',
                                 'text-rose-400': !deviceConnected || scanStage === 'error'
                             }"
                             x-text="
                                !deviceConnected ? '🔴 SCANNER NOT CONNECTED' :
                                scanStage === 'finger_detected' ? 'FINGER DETECTED' :
                                scanStage === 'capturing' ? 'CAPTURING...' :
                                scanStage === 'extracting' ? 'EXTRACTING TEMPLATE...' :
                                scanStage === 'success' ? 'FINGERPRINT CAPTURED SUCCESSFULLY' :
                                scanStage === 'error' ? 'PEMINDAIAN GAGAL' :
                                'WAITING FINGER...'
                             "></div>
                        <p class="text-xs text-slate-400" x-text="deviceConnected ? scanMessage : 'Pastikan kabel scanner USB terpasang ke komputer piket.'"></p>
                        
                        <div x-show="!deviceConnected" class="mt-3">
                            <button type="button" @click="pairUsbScanner()"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-2 mx-auto cursor-pointer">
                                <span>🔌</span>
                                <span>Hubungkan / Deteksi Scanner USB</span>
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
                        <span class="w-2 h-2 rounded-full" :class="deviceConnected ? 'bg-emerald-400' : 'bg-rose-500'"></span>
                        <span>Perangkat: <strong class="text-white" x-text="deviceName"></strong></span>
                    </div>
                    <div class="flex items-center space-x-4 text-slate-400 font-mono text-[11px]">
                        <span>Status: <strong :class="deviceConnected ? 'text-emerald-400' : 'text-rose-400'" x-text="deviceStatus"></strong></span>
                        <span>Port: <strong>52181</strong></span>
                        <span x-show="qualityScore">Quality: <strong class="text-cyan-400" x-text="qualityScore + '%'"></strong></span>
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
                    <!-- Guru Selector -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Pilih Guru / Tenaga Kependidikan</label>
                        <select x-model="enrollTeacherId" class="w-full bg-slate-800 text-white text-sm rounded-2xl px-4 py-3 border border-slate-700 focus:outline-none focus:border-indigo-500">
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

                    <!-- Sensor Instructions for Enrollment -->
                    <div class="p-6 rounded-2xl bg-slate-950 border text-center flex flex-col items-center justify-center transition"
                         :class="deviceConnected ? 'border-indigo-500/40' : 'border-rose-500/40'">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/></svg>
                        </div>
                        <div class="font-bold text-white text-sm" x-text="deviceConnected ? (enrollStep === 0 ? 'Tempelkan Jari Guru ke Kaca Scanner USB' : 'Angkat & Tempelkan Jari Sekali Lagi') : 'Scanner Belum Terhubung'"></div>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm" x-text="enrollMessage"></p>
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
