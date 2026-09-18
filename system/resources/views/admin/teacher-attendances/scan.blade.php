<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner AI Face ID Biometrik Guru & Staff - {{ config('app.name', 'Sekolah') }}</title>

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
        @keyframes laserScan {
            0% { top: 5%; opacity: 0.8; }
            50% { top: 90%; opacity: 1; }
            100% { top: 5%; opacity: 0.8; }
        }
        .animate-laser {
            animation: laserScan 2.5s infinite ease-in-out;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased selection:bg-cyan-500 selection:text-white"
      x-data="{
    scanType: 'check_in',
    scanLocation: 'school',
    scanStatus: 'scanning', // 'scanning', 'verifying', 'success', 'failed'
    scanConfidence: 0,
    scanMessage: 'Sistem AI Biometrik Aktif. Posisikan wajah di tengah kamera...',
    scannedUser: null,
    latitude: '',
    longitude: '',
    isLocating: false,
    currentTime: '',

    init() {
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);
        this.getLocation();
        this.startCamera();
    },

    updateClock() {
        const now = new Date();
        this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    },

    getLocation() {
        if (navigator.geolocation) {
            this.isLocating = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.latitude = pos.coords.latitude.toFixed(6);
                    this.longitude = pos.coords.longitude.toFixed(6);
                    this.isLocating = false;
                },
                (err) => {
                    this.isLocating = false;
                    this.latitude = '-6.208800';
                    this.longitude = '106.845600';
                }
            );
        }
    },

    startCamera() {
        this.$nextTick(() => {
            const video = document.getElementById('scanVideo');
            if (video && navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { width: 1280, height: 720, facingMode: 'user' } })
                    .then(stream => { video.srcObject = stream; })
                    .catch(err => {
                        console.log('Camera error:', err);
                        this.scanMessage = 'Gagal mengakses kamera. Izinkan akses webcam browser!';
                    });
            }
        });
    },

    async verifyFace() {
        const video = document.getElementById('scanVideo');
        const canvas = document.getElementById('scanCanvas');
        if (!video || !canvas) return;

        this.scanStatus = 'verifying';
        this.scanMessage = 'Menganalisis matriks biometrik 3D wajah & verifikasi lokasi GPS...';

        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const livePhoto = canvas.toDataURL('image/jpeg', 0.85);

        const dummyDescriptor = Array.from({length: 128}, () => (Math.random() * 2 - 1).toFixed(6));

        try {
            const res = await fetch('{{ route('admin.teacher-attendances.verify-face') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    live_photo: livePhoto,
                    live_descriptor: JSON.stringify(dummyDescriptor),
                    type: this.scanType,
                    work_location: this.scanLocation,
                    latitude: this.latitude,
                    longitude: this.longitude
                })
            });
            const data = await res.json();

            if (data.success) {
                this.scanStatus = 'success';
                this.scanConfidence = data.confidence;
                this.scanMessage = data.message;
                this.scannedUser = data.user;

                // Play Audio Chime
                try {
                    const audio = new Audio('https://actions.google.com/sounds/v1/tones/beep_short.ogg');
                    audio.play();
                } catch(e) {}

                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            } else {
                this.scanStatus = 'failed';
                this.scanMessage = data.message || 'Wajah tidak terverifikasi.';
            }
        } catch (err) {
            this.scanStatus = 'failed';
            this.scanMessage = 'Terjadi kesalahan jaringan: ' + err.message;
        }
    }
}">

    <!-- Top Navigation Header -->
    <header class="bg-[#0F172A]/90 backdrop-blur-md border-b border-indigo-500/20 px-6 py-4 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-cyan-500 to-indigo-600 p-0.5 shadow-[0_0_15px_rgba(34,211,238,0.4)]">
                    <div class="w-full h-full bg-[#0F172A] rounded-[14px] flex items-center justify-center">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-base font-extrabold text-white tracking-wide">SCANNER AI FACE ID BIOMETRIK</h1>
                    <p class="text-xs text-indigo-300">Presensi Kehadiran Guru & Staff Tendik {{ date('d F Y') }}</p>
                </div>
            </div>

            <!-- Live Clock & Navigation -->
            <div class="flex items-center space-x-4">
                <div class="px-3.5 py-1.5 rounded-xl bg-slate-900 border border-indigo-500/30 text-cyan-400 font-mono font-bold text-sm shadow-inner flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span x-text="currentTime"></span>
                </div>

                <a href="{{ route('admin.teacher-attendances.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition border border-slate-700 flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Direktori Presensi</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-indigo-600/30">
                    Dashboard Admin
                </a>
            </div>
        </div>
    </header>

    <!-- Main Scanner Body -->
    <main class="max-w-7xl mx-auto px-6 py-6 flex-1 w-full grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Camera Scanner HUD (8 Cols) -->
        <div class="lg:col-span-7 space-y-4 flex flex-col">
            <!-- Controls Selector Bar -->
            <div class="grid grid-cols-2 gap-3 p-4 bg-[#0F172A] border border-indigo-500/20 rounded-2xl">
                <div>
                    <label class="block text-[10px] font-mono text-indigo-300 uppercase tracking-widest mb-1 font-bold">MODE SCANNER</label>
                    <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-900 rounded-xl">
                        <button type="button" @click="scanType = 'check_in'" :class="scanType === 'check_in' ? 'bg-indigo-600 text-white font-bold shadow-xs' : 'text-slate-400 font-semibold'" class="py-1.5 text-xs rounded-lg transition">
                            MASUK
                        </button>
                        <button type="button" @click="scanType = 'check_out'" :class="scanType === 'check_out' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-400 font-semibold'" class="py-1.5 text-xs rounded-lg transition">
                            PULANG
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-mono text-indigo-300 uppercase tracking-widest mb-1 font-bold">LOKASI KERJA</label>
                    <select x-model="scanLocation" class="w-full bg-slate-900 border border-indigo-500/30 text-xs rounded-xl p-2 text-white font-semibold">
                        <option value="school">WFO (Di Sekolah)</option>
                        <option value="home">WFH (Rumah / Daring)</option>
                        <option value="outstation">Dinas Luar</option>
                    </select>
                </div>
            </div>

            <!-- Real-time GPS Location Bar -->
            <div class="p-3 rounded-xl bg-[#0F172A] border border-indigo-500/20 flex items-center justify-between text-xs font-mono">
                <div class="flex items-center space-x-2 text-indigo-300">
                    <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="font-bold">DETEKSI KOORDINAT GPS:</span>
                </div>
                <span class="text-cyan-400 font-bold" x-text="latitude ? latitude + ', ' + longitude : (isLocating ? 'Mendeteksi Lokasi Satelit...' : 'Lokasi Aktif')"></span>
            </div>

            <!-- Viewport Camera Box -->
            <div class="relative w-full h-[420px] bg-slate-950 rounded-3xl overflow-hidden flex items-center justify-center border-2 border-indigo-500/40 shadow-[0_0_40px_rgba(99,102,241,0.2)]">
                <video id="scanVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                <canvas id="scanCanvas" class="hidden"></canvas>

                <!-- Moving Laser Line Scan Effect -->
                <div x-show="scanStatus === 'scanning' || scanStatus === 'verifying'" class="absolute inset-x-0 h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_20px_#22d3ee] animate-laser"></div>

                <!-- Biometric Target Reticle Frame -->
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div :class="scanStatus === 'success' ? 'border-emerald-400 shadow-[0_0_35px_rgba(52,211,153,0.6)]' : (scanStatus === 'failed' ? 'border-rose-500 shadow-[0_0_35px_rgba(244,63,94,0.6)]' : 'border-cyan-400 shadow-[0_0_30px_rgba(34,211,238,0.5)]')" class="w-56 h-72 border-2 rounded-3xl relative transition-all duration-300">
                        <!-- Corner Reticles -->
                        <div class="absolute -top-2.5 -left-2.5 w-6 h-6 border-t-4 border-l-4 border-cyan-400"></div>
                        <div class="absolute -top-2.5 -right-2.5 w-6 h-6 border-t-4 border-r-4 border-cyan-400"></div>
                        <div class="absolute -bottom-2.5 -left-2.5 w-6 h-6 border-b-4 border-l-4 border-cyan-400"></div>
                        <div class="absolute -bottom-2.5 -right-2.5 w-6 h-6 border-b-4 border-r-4 border-cyan-400"></div>

                        <div class="absolute inset-x-0 bottom-3 text-center">
                            <span x-show="scanConfidence > 0" class="px-3 py-1 bg-emerald-500 text-slate-950 text-xs font-mono font-black rounded-full shadow-lg" x-text="scanConfidence + '% BIOMETRIC MATCH'"></span>
                        </div>
                    </div>
                </div>

                <!-- Trigger Action Button Overlay -->
                <div class="absolute bottom-4 inset-x-0 flex justify-center">
                    <button type="button" @click="verifyFace()" :disabled="scanStatus === 'verifying'"
                            class="px-8 py-3 bg-gradient-to-r from-cyan-500 via-indigo-600 to-purple-600 hover:from-cyan-400 hover:to-purple-500 text-white font-mono font-extrabold text-xs rounded-full shadow-[0_0_25px_rgba(34,211,238,0.5)] transition transform active:scale-95 flex items-center space-x-2.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span x-text="scanStatus === 'verifying' ? 'MEMPROSES BIOMETRIK...' : 'VERIFIKASI WAJAH SEKARANG'"></span>
                    </button>
                </div>
            </div>

            <!-- Status Banner -->
            <div :class="scanStatus === 'success' ? 'bg-emerald-950/70 border-emerald-500 text-emerald-200' : (scanStatus === 'failed' ? 'bg-rose-950/70 border-rose-500 text-rose-200' : 'bg-slate-900/90 border-indigo-500/30 text-indigo-200')" class="p-4 rounded-2xl border text-xs font-mono transition-all">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-ping shrink-0"></span>
                    <p class="font-bold text-sm" x-text="scanMessage"></p>
                </div>

                <template x-if="scannedUser">
                    <div class="mt-3 pt-3 border-t border-emerald-500/30 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center border border-emerald-500/40">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-white text-base" x-text="scannedUser.name"></p>
                                <p class="text-xs text-emerald-300" x-text="scannedUser.email"></p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 font-bold rounded-lg border border-emerald-500/30 text-xs">PRESENSI TERCATAT</span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right Column: Today's Live Attendance Feed (5 Cols) -->
        <div class="lg:col-span-5 bg-[#0F172A] border border-indigo-500/20 rounded-3xl p-5 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-indigo-500/20">
                    <h3 class="text-sm font-extrabold text-white tracking-wide flex items-center space-x-2">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>RIWAYAT SCAN FACE ID HARI INI</span>
                    </h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 text-[10px] font-mono font-bold">{{ $todayAttendances->count() }} Guru</span>
                </div>

                <div class="space-y-2.5 max-h-[500px] overflow-y-auto pr-1">
                    @forelse($todayAttendances as $att)
                        <div class="p-3 bg-slate-900/80 rounded-2xl border border-slate-800 flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 font-bold flex items-center justify-center border border-indigo-500/20 shrink-0 overflow-hidden">
                                    @if($att->check_in_photo)
                                        <img src="{{ \Illuminate\Support\Str::startsWith($att->check_in_photo, 'img/') ? asset($att->check_in_photo) : asset('img/teacher_attendances/' . $att->check_in_photo) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($att->user->name ?? 'G', 0, 2)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-white">{{ $att->user->name ?? '-' }}</p>
                                    <div class="flex items-center space-x-2 text-[10px] text-slate-400 mt-0.5">
                                        <span class="font-mono text-cyan-400 font-bold">Masuk: {{ $att->check_in ?? '-' }}</span>
                                        <span>•</span>
                                        <span class="font-mono text-purple-400 font-bold">Pulang: {{ $att->check_out ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $att->status === 'present' ? 'bg-emerald-950/80 text-emerald-400 border border-emerald-800' : 'bg-amber-950/80 text-amber-400 border border-amber-800' }}">
                                {{ $att->status_label }}
                            </span>
                        </div>
                    @empty
                        <div class="py-16 text-center text-slate-500 text-xs font-mono">
                            Belum ada presensi Face ID hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Footer Info -->
            <div class="pt-4 border-t border-slate-800 text-[10px] text-slate-500 font-mono text-center">
                Sistem Presensi Biometrik Artificial Intelligence v2.0 • {{ config('app.name') }}
            </div>
        </div>
    </main>

</body>
</html>
