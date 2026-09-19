<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal Scanner Sidik Jari Guru (Digital Persona USB) - {{ config('app.name', 'Sekolah') }}</title>

    <!-- Google Fonts Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Digital Persona WebSDK Core Client (Optional External Fallback) -->
    <script src="https://cdn.jsdelivr.net/npm/es6-shim@0.35.6/es6-shim.min.js"></script>

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
            animation: scanBeam 2s infinite ease-in-out;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased selection:bg-emerald-500 selection:text-white"
      x-data="{
    activeTab: 'standby', // 'standby' or 'enroll'
    scannerConnected: true,
    scannerStatusText: 'Digital Persona U.are.U 4500 Siap',
    scanState: 'idle', // 'idle', 'scanning', 'success', 'already', 'error'
    scanMessage: 'Tempelkan jari guru pada scanner USB di samping laptop...',
    scannedTeacher: null,
    scannedTime: '',
    scannedAction: '',

    // Enrollment state
    enrollTeacherId: '{{ $selectedTeacherId ?? '' }}',
    enrollStep: 0,
    enrollStatus: 'idle',
    enrollMessage: 'Pilih guru dan tempelkan jari 4 kali untuk merekam template.',

    // Clock
    currentTime: '',
    currentDate: '',

    init() {
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);
        this.initWebSdk();
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
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.1); // A5
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
        } catch(e) {
            console.log('Audio error:', e);
        }
    },

    webSocket: null,
    dpDeviceUid: null,
    reconnectTimer: null,
    enrollSamples: [],

    initWebSdk() {
        this.connectDigitalPersonaService();
    },

    connectDigitalPersonaService() {
        // HID DigitalPersona WebSDK local service loopback ports (default 52181)
        const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const wsUrl = wsProtocol + '//127.0.0.1:52181';

        try {
            if (this.webSocket) {
                try { this.webSocket.close(); } catch(e) {}
            }

            this.webSocket = new WebSocket(wsUrl);

            this.webSocket.onopen = () => {
                this.scannerConnected = true;
                this.scannerStatusText = 'Digital Persona USB Online (Port 52181)';
                console.log('Connected to HID DigitalPersona Desktop Service');

                // Send device enumeration command
                try {
                    this.webSocket.send(JSON.stringify({
                        command: 'EnumerateDevices'
                    }));
                    // Send acquisition start request (PngBiometric or Raw FMD)
                    this.webSocket.send(JSON.stringify({
                        command: 'StartAcquisition',
                        SampleFormat: 'PngBiometric'
                    }));
                } catch(e) {
                    console.log('Error sending DP handshake:', e);
                }
            };

            this.webSocket.onmessage = (event) => {
                try {
                    const msg = JSON.parse(event.data);

                    // 1. Device Detected / Connected
                    if (msg.event === 'DeviceConnected' || msg.devices || msg.DeviceDescription) {
                        this.scannerConnected = true;
                        const deviceName = msg.DeviceDescription || (msg.devices && msg.devices[0]?.name) || 'Digital Persona U.are.U 4500';
                        this.scannerStatusText = deviceName + ' Siap';
                        if (msg.DeviceUid) {
                            this.dpDeviceUid = msg.DeviceUid;
                        }
                    }

                    // 2. Real Biometric Fingerprint Sample Acquired
                    if (msg.event === 'SamplesAcquired' || msg.samples || msg.sample) {
                        const sampleData = (msg.samples && msg.samples[0]) || msg.sample || msg.data;
                        this.onHardwareSampleAcquired(sampleData);
                    }

                    // 3. Quality feedback from optical sensor
                    if (msg.event === 'QualityReported' && msg.quality > 0) {
                        this.scanMessage = 'Kualitas sensor: tekan jari lebih mantap dan bersihkan permukaan sensor.';
                    }
                } catch(err) {
                    console.log('DP message parse:', event.data);
                }
            };

            this.webSocket.onerror = () => {
                this.scannerConnected = false;
                this.scannerStatusText = 'Layanan USB Offline (Port 52181)';
            };

            this.webSocket.onclose = () => {
                this.scannerConnected = false;
                this.scannerStatusText = 'Menghubungkan Scanner (Port 52181)...';
                // Automatic background polling / retry every 5s
                clearTimeout(this.reconnectTimer);
                this.reconnectTimer = setTimeout(() => {
                    this.connectDigitalPersonaService();
                }, 5000);
            };
        } catch(e) {
            this.scannerConnected = false;
            this.scannerStatusText = 'Scanner Standby (Mode Uji / Port 52181)';
        }
    },

    // Handle real physical touch on DigitalPersona sensor
    onHardwareSampleAcquired(sampleData) {
        if (this.activeTab === 'standby') {
            // Instant verification from hardware sensor
            this.submitVerification(null, sampleData);
        } else if (this.activeTab === 'enroll') {
            // Hardware step acquisition for enrollment
            this.recordEnrollSampleFromHardware(sampleData);
        }
    },

    // Record sample in 4-step enrollment flow
    recordEnrollSampleFromHardware(sampleData) {
        if (!this.enrollTeacherId) {
            alert('Pilih nama guru terlebih dahulu sebelum menempelkan jari!');
            return;
        }

        this.enrollSamples.push(sampleData);

        if (this.enrollStep < 3) {
            this.enrollStep++;
            this.enrollStatus = 'scanning';
            this.enrollMessage = `Perekaman ${this.enrollStep}/4 berhasil! Angkat dan tempelkan jari yang sama sekali lagi...`;
            this.playAudio('success');
        } else {
            this.enrollStep = 4;
            this.saveEnrollment(this.enrollSamples.join('::'));
        }
    },

    // Kirim Verifikasi Sidik Jari ke Server
    async submitVerification(teacherId = null, sample = null) {
        this.scanState = 'scanning';
        this.scanMessage = 'Membaca sidik jari & mencocokkan biometrik...';

        try {
            const res = await fetch('{{ route('admin.teacher-attendances.fingerprint.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: teacherId,
                    fingerprint_sample: sample || ('DP_OPTICAL_SAMPLE_' + Date.now()),
                    device_name: this.scannerStatusText || 'Digital Persona U.are.U 4500 USB (Admin Desk)'
                })
            });

            const data = await res.json();

            if (data.success) {
                this.scannedTeacher = data.teacher;
                this.scannedTime = new Date().toLocaleTimeString('id-ID');
                
                if (data.already_complete) {
                    this.scanState = 'already';
                    this.scanMessage = data.message;
                    this.playAudio('already');
                } else {
                    this.scanState = 'success';
                    this.scannedAction = data.action_type === 'check_in' ? 'MASUK' : 'PULANG';
                    this.scanMessage = data.message;
                    this.playAudio('success');

                    // Tambahkan ke live list di tabel kanan secara instan
                    this.prependLiveAttendance({
                        name: data.teacher.name,
                        time: this.scannedTime,
                        action: this.scannedAction,
                        method: 'Sidik Jari (Admin)'
                    });
                }

                setTimeout(() => {
                    this.scanState = 'idle';
                    this.scanMessage = 'Tempelkan jari guru pada scanner USB di samping laptop...';
                }, 5000);
            } else {
                this.scanState = 'error';
                this.scanMessage = data.message || 'Sidik jari tidak dikenali.';
                this.playAudio('error');
                setTimeout(() => {
                    this.scanState = 'idle';
                    this.scanMessage = 'Tempelkan jari guru pada scanner USB di samping laptop...';
                }, 4000);
            }
        } catch(err) {
            console.error('Scan error:', err);
            this.scanState = 'error';
            this.scanMessage = 'Terjadi kesalahan komunikasi dengan server.';
            this.playAudio('error');
            setTimeout(() => {
                this.scanState = 'idle';
                this.scanMessage = 'Tempelkan jari guru pada scanner USB di samping laptop...';
            }, 4000);
        }
    },

    // Enrolment Perekaman Jari Guru Baru
    tapEnrollStep() {
        if (!this.enrollTeacherId) {
            alert('Pilih nama guru terlebih dahulu!');
            return;
        }

        const simulatedSample = 'DP_ENROLL_RAW_' + Date.now();
        this.recordEnrollSampleFromHardware(simulatedSample);
    },

    async saveEnrollment(collectedFmd = null) {
        this.enrollStatus = 'saving';
        this.enrollMessage = 'Membentuk template biometrik terenkripsi & menyimpan ke database...';

        const finalTemplate = collectedFmd || ('DP_FMD_' + btoa(this.enrollTeacherId + '_' + Date.now()));

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
                    fingerprint_template: finalTemplate
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
                this.enrollMessage = data.message || 'Gagal merekam sidik jari.';
            }
        } catch(err) {
            this.enrollStatus = 'error';
            this.enrollMessage = 'Gagal menyimpan ke server.';
        }
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
                <a href="{{ route('admin.teacher-attendances.index') }}" class="p-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <h1 class="text-lg font-black tracking-tight text-white flex items-center space-x-2">
                            <span>TERMINAL SCANNER SIDIK JARI</span>
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold tracking-wider rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">USB Digital Persona 4500</span>
                        </h1>
                    </div>
                    <p class="text-xs text-slate-400 font-medium">Sistem Presensi Meja Piket & Meja Admin Terintegrasi</p>
                </div>
            </div>

            <!-- Device Connection Indicator & Live Clock -->
            <div class="flex items-center space-x-4">
                <!-- Device Status Badge -->
                <div class="flex items-center space-x-2 px-3.5 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-xs">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span class="text-slate-300 font-semibold" x-text="scannerStatusText"></span>
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
        
        <!-- Left Side: Terminal Pemindai Sidik Jari (7 Cols) -->
        <section class="lg:col-span-7 space-y-6">
            
            <!-- Navigation Tab: Standby vs Enroll -->
            <div class="flex items-center p-1.5 bg-slate-900/90 rounded-2xl border border-slate-800">
                <button type="button" @click="activeTab = 'standby'" 
                        :class="activeTab === 'standby' ? 'bg-emerald-600 text-white shadow-lg font-bold' : 'text-slate-400 hover:text-white font-semibold'"
                        class="flex-1 py-2.5 rounded-xl text-xs flex items-center justify-center space-x-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/></svg>
                    <span>Mode Standby Presensi (Piket)</span>
                </button>
                <button type="button" @click="activeTab = 'enroll'" 
                        :class="activeTab === 'enroll' ? 'bg-indigo-600 text-white shadow-lg font-bold' : 'text-slate-400 hover:text-white font-semibold'"
                        class="flex-1 py-2.5 rounded-xl text-xs flex items-center justify-center space-x-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Perekaman Sidik Jari Guru (Enroll)</span>
                </button>
            </div>

            <!-- TAB 1: MODE STANDBY SCANNER -->
            <div x-show="activeTab === 'standby'" class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 sm:p-8 relative overflow-hidden backdrop-blur-md shadow-2xl">
                
                <!-- Background Accent Glow -->
                <div class="absolute -top-24 -left-24 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Animated Sensor Pad -->
                <div class="flex flex-col items-center justify-center py-6 text-center">
                    
                    <!-- Scanner Glass Pad Container -->
                    <div class="relative w-52 h-64 rounded-3xl bg-slate-950 border-2 transition-all duration-500 flex flex-col items-center justify-center cursor-pointer select-none shadow-2xl group"
                         :class="{
                             'border-emerald-500/50 shadow-emerald-500/20': scanState === 'idle',
                             'border-cyan-400 shadow-cyan-500/40 animate-pulse': scanState === 'scanning',
                             'border-emerald-400 bg-emerald-950/40 shadow-emerald-500/50': scanState === 'success',
                             'border-amber-400 bg-amber-950/40 shadow-amber-500/50': scanState === 'already',
                             'border-rose-500 bg-rose-950/40 shadow-rose-500/50': scanState === 'error'
                         }"
                         @click="submitVerification()">
                        
                        <!-- Scanning Laser Beam -->
                        <div x-show="scanState === 'scanning'" class="absolute inset-x-2 h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent rounded-full animate-scan-beam z-10 shadow-[0_0_15px_#22d3ee]"></div>

                        <!-- Fingerprint Icon with Concentric Rings -->
                        <div class="relative flex items-center justify-center">
                            <div class="absolute w-36 h-36 rounded-full border border-emerald-500/20 animate-finger-pulse"></div>
                            <div class="absolute w-28 h-28 rounded-full border border-emerald-500/40"></div>

                            <!-- SVG Fingerprint Optical Vector -->
                            <svg class="w-24 h-24 transition-colors duration-300"
                                 :class="{
                                     'text-emerald-400/80 group-hover:text-emerald-300': scanState === 'idle',
                                     'text-cyan-400 animate-pulse': scanState === 'scanning',
                                     'text-emerald-400': scanState === 'success',
                                     'text-amber-400': scanState === 'already',
                                     'text-rose-400': scanState === 'error'
                                 }"
                                 fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/>
                            </svg>
                        </div>

                        <!-- Hardware Logo Caption -->
                        <div class="absolute bottom-4 inset-x-0 text-center">
                            <span class="text-[9px] font-mono tracking-widest text-slate-500 uppercase font-bold">DIGITAL PERSONA OPTICAL</span>
                        </div>
                    </div>

                    <!-- Status Display Message -->
                    <div class="mt-6 max-w-md">
                        <h3 class="text-base font-bold text-white tracking-tight" x-text="
                            scanState === 'scanning' ? 'Memverifikasi Sidik Jari...' :
                            scanState === 'success' ? 'Presensi Berhasil Dicatat!' :
                            scanState === 'already' ? 'Status Kehadiran Lengkap' :
                            scanState === 'error' ? 'Pemindaian Gagal' :
                            'Scanner Standby & Siap Digunakan'
                        "></h3>
                        <p class="text-xs text-slate-400 mt-1" x-text="scanMessage"></p>
                    </div>

                    <!-- Result Notification Banner (When Success / Already) -->
                    <template x-if="scannedTeacher">
                        <div class="mt-6 w-full max-w-md p-4 rounded-2xl border transition-all duration-300 flex items-center space-x-4 text-left"
                             :class="{
                                 'bg-emerald-950/50 border-emerald-500/40 text-emerald-200': scanState === 'success',
                                 'bg-amber-950/50 border-amber-500/40 text-amber-200': scanState === 'already',
                                 'bg-slate-800/60 border-slate-700 text-slate-300': scanState === 'idle'
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
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30" x-text="scannedAction || 'HADIR'"></span>
                                </div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">NIP: <span x-text="scannedTeacher.nip || '-'"></span></div>
                                <div class="text-[11px] text-slate-400 mt-1">Waktu: <span class="font-mono font-bold text-white" x-text="scannedTime"></span> via Sidik Jari Admin</div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Simulation & Testing Dropdown (Untuk Testing Cepat Tanpa Hardware) -->
                <div class="mt-6 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="text-slate-400 flex items-center space-x-1.5">
                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        <span>Mode Uji Coba Cepat (Pilih Guru untuk simulasi tap jari):</span>
                    </div>
                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <select id="simulatedTeacherSelect" class="bg-slate-800 text-white text-xs rounded-xl px-3 py-2 border border-slate-700 focus:outline-none focus:border-emerald-500 w-full sm:w-56">
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->nip ?? 'No NIP' }})</option>
                            @endforeach
                        </select>
                        <button type="button" @click="submitVerification(document.getElementById('simulatedTeacherSelect').value)"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition shadow-lg flex-shrink-0">
                            Simulasi Tap
                        </button>
                    </div>
                </div>
            </div>

            <!-- TAB 2: MODE PEREKAMAN SIDIK JARI (ENROLLMENT) -->
            <div x-show="activeTab === 'enroll'" class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 sm:p-8 backdrop-blur-md shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div>
                        <h3 class="text-base font-extrabold text-white">Perekaman Biometrik Guru Baru</h3>
                        <p class="text-xs text-slate-400">Pindai sidik jari 4 kali untuk menghasilkan template Digital Persona</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Enrollment</span>
                </div>

                <div class="mt-6 space-y-5">
                    <!-- Guru Selector -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Pilih Guru / Tenaga Kependidikan</label>
                        <select x-model="enrollTeacherId" class="w-full bg-slate-800 text-white text-sm rounded-2xl px-4 py-3 border border-slate-700 focus:outline-none focus:border-indigo-500">
                            <option value="">-- Pilih Nama Guru --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->name }} (NIP: {{ $t->nip ?? '-' }}) {{ $t->fingerprint_registered_at ? '✓ [Sudah Terdaftar]' : '✕ [Belum Terdaftar]' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4 Steps Progress Bar Indicator -->
                    <div>
                        <div class="flex justify-between text-xs font-bold text-slate-300 mb-2">
                            <span>Progres Perekaman Sampel Jari:</span>
                            <span class="font-mono text-indigo-400" x-text="enrollStep + '/4'"></span>
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="i in 4" :key="i">
                                <div class="h-3 rounded-full transition-all duration-300"
                                     :class="enrollStep >= i ? 'bg-indigo-500 shadow-md shadow-indigo-500/40' : 'bg-slate-800 border border-slate-700'"></div>
                            </template>
                        </div>
                    </div>

                    <!-- Scanner Touch Pad for Enrollment -->
                    <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 text-center flex flex-col items-center justify-center cursor-pointer hover:border-indigo-500/50 transition"
                         @click="tapEnrollStep()">
                        <div class="w-20 h-20 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-3">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/></svg>
                        </div>
                        <div class="font-bold text-white text-sm" x-text="enrollStep === 0 ? 'Klik di sini atau Tempelkan Jari Guru ke Scanner' : 'Tempelkan Jari Sekali Lagi'"></div>
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

            <!-- Daftar Presensi Hari Ini (Live Stream) -->
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
                    Setiap kali guru melakukan tap sidik jari di laptop admin ini, sistem akan otomatis mencatat presensi masuk/pulang dan **mengunci tombol presensi di HP guru secara realtime**. Guru tidak perlu lagi melakukan absen manual di ponselnya.
                </p>
            </div>
        </section>
    </main>

    <!-- Footer Bar -->
    <footer class="border-t border-slate-800/80 px-6 py-3 text-center text-[11px] text-slate-500">
        {{ config('app.name', 'Sekolah') }} • Sistem Presensi Biometrik Digital Persona U.are.U 4500 USB & Lock GPS Mobile
    </footer>
</body>
</html>
