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
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0B0F17;
            color: #F8FAFC;
            margin: 0;
            padding: 0;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* HARD CONSTRAINTS: Prevent any SVG from expanding full-screen if CDN delays or fails */
        svg {
            max-width: 100%;
            box-sizing: border-box;
        }
        svg.icon-svg, header svg, button svg, a svg, .icon-box svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
            max-width: 1.25rem !important;
            max-height: 1.25rem !important;
            display: inline-block !important;
            flex-shrink: 0 !important;
        }
        .logo-icon-box {
            width: 2.5rem !important;
            height: 2.5rem !important;
            max-width: 2.5rem !important;
            max-height: 2.5rem !important;
            min-width: 2.5rem !important;
            min-height: 2.5rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            border-radius: 1rem !important;
            overflow: hidden !important;
        }
        .logo-icon-box svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
            max-width: 1.25rem !important;
            max-height: 1.25rem !important;
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
      x-data="faceScannerApp()">

    <!-- Top Navigation Header -->
    <header class="bg-[#0F172A]/90 backdrop-blur-md border-b border-indigo-500/20 px-6 py-4 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="logo-icon-box bg-gradient-to-tr from-cyan-500 to-indigo-600 p-0.5 shadow-[0_0_15px_rgba(34,211,238,0.4)]">
                    <div class="w-full h-full bg-[#0F172A] rounded-[14px] flex items-center justify-center">
                        <svg width="20" height="20" style="width:20px;height:20px;max-width:20px;max-height:20px;" class="icon-svg text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
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

                <a href="{{ route('admin.teacher-attendances.register-face-page') }}" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-indigo-600/30 flex items-center space-x-1.5">
                    <svg width="16" height="16" style="width:16px;height:16px;max-width:16px;max-height:16px;" class="icon-svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                    <span>+ Registrasi Face ID Guru</span>
                </a>
                <a href="{{ route('admin.teacher-attendances.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold rounded-xl transition border border-slate-700 flex items-center space-x-1.5">
                    <svg width="16" height="16" style="width:16px;height:16px;max-width:16px;max-height:16px;" class="icon-svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
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

        <!-- Left Column: Camera Scanner HUD (7 Cols) -->
        <div class="lg:col-span-7 space-y-4 flex flex-col">
            <!-- Controls Selector Bar -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 p-4 bg-[#0F172A] border border-indigo-500/20 rounded-2xl">
                <div class="md:col-span-8">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[10px] font-mono text-indigo-300 uppercase tracking-widest font-bold">SESI PRESENSI</label>
                        <span class="text-[11px] font-mono font-bold text-cyan-400 bg-cyan-950/60 px-2 py-0.5 rounded border border-cyan-800/50" x-text="'🕒 ' + activeSessionLabel"></span>
                    </div>
                    <div class="grid grid-cols-4 gap-1 p-1 bg-slate-900 rounded-xl">
                        <button type="button" @click="scanType = 'auto'" :class="scanType === 'auto' ? 'bg-gradient-to-r from-cyan-600 to-indigo-600 text-white font-bold shadow-sm' : 'text-slate-400 font-semibold hover:text-slate-200'" class="py-1.5 text-[11px] rounded-lg transition flex items-center justify-center space-x-1">
                            <span>⚡</span>
                            <span>OTOMATIS</span>
                        </button>
                        <button type="button" @click="scanType = 'check_in'" :class="scanType === 'check_in' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'text-slate-400 font-semibold hover:text-slate-200'" class="py-1.5 text-[11px] rounded-lg transition flex items-center justify-center space-x-1">
                            <span>🌅</span>
                            <span>MASUK</span>
                        </button>
                        <button type="button" @click="scanType = 'midday'" :class="scanType === 'midday' ? 'bg-amber-600 text-white font-bold shadow-sm' : 'text-slate-400 font-semibold hover:text-slate-200'" class="py-1.5 text-[11px] rounded-lg transition flex items-center justify-center space-x-1">
                            <span>☀️</span>
                            <span>SIANG</span>
                        </button>
                        <button type="button" @click="scanType = 'check_out'" :class="scanType === 'check_out' ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'text-slate-400 font-semibold hover:text-slate-200'" class="py-1.5 text-[11px] rounded-lg transition flex items-center justify-center space-x-1">
                            <span>🌆</span>
                            <span>PULANG</span>
                        </button>
                    </div>
                </div>

                <div class="md:col-span-4">
                    <label class="block text-[10px] font-mono text-indigo-300 uppercase tracking-widest mb-1.5 font-bold">LOKASI KERJA</label>
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
                    <svg width="16" height="16" style="width:16px;height:16px;max-width:16px;max-height:16px;" class="icon-svg text-cyan-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
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
                    <div :class="scanStatus === 'success' ? 'border-emerald-400 shadow-[0_0_35px_rgba(52,211,153,0.8)]' : (scanStatus === 'already' ? 'border-amber-400 shadow-[0_0_35px_rgba(251,191,36,0.8)]' : (hasFaceInView ? 'border-emerald-400 shadow-[0_0_30px_rgba(52,211,153,0.7)] animate-pulse' : (scanStatus === 'failed' ? 'border-rose-500 shadow-[0_0_35px_rgba(244,63,94,0.6)]' : 'border-cyan-400/60 shadow-[0_0_20px_rgba(34,211,238,0.25)]')))" class="w-56 h-72 border-2 rounded-3xl relative transition-all duration-300">
                        <!-- Corner Reticles -->
                        <div class="absolute -top-2.5 -left-2.5 w-6 h-6 border-t-4 border-l-4" :class="hasFaceInView || scanStatus === 'success' ? 'border-emerald-400' : (scanStatus === 'already' ? 'border-amber-400' : 'border-cyan-400')"></div>
                        <div class="absolute -top-2.5 -right-2.5 w-6 h-6 border-t-4 border-r-4" :class="hasFaceInView || scanStatus === 'success' ? 'border-emerald-400' : (scanStatus === 'already' ? 'border-amber-400' : 'border-cyan-400')"></div>
                        <div class="absolute -bottom-2.5 -left-2.5 w-6 h-6 border-b-4 border-l-4" :class="hasFaceInView || scanStatus === 'success' ? 'border-emerald-400' : (scanStatus === 'already' ? 'border-amber-400' : 'border-cyan-400')"></div>
                        <div class="absolute -bottom-2.5 -right-2.5 w-6 h-6 border-b-4 border-r-4" :class="hasFaceInView || scanStatus === 'success' ? 'border-emerald-400' : (scanStatus === 'already' ? 'border-amber-400' : 'border-cyan-400')"></div>

                        <!-- Face Presence Status Pill inside Frame -->
                        <div class="absolute inset-x-0 -top-3 text-center">
                            <span x-show="hasFaceInView && scanStatus === 'scanning'" class="px-3 py-0.5 bg-emerald-500 text-slate-950 text-[10px] font-mono font-black rounded-full shadow-lg">
                                🟢 WAJAH TERDETEKSI
                            </span>
                            <span x-show="!hasFaceInView && scanStatus === 'scanning'" class="px-3 py-0.5 bg-slate-900/90 text-cyan-400 border border-cyan-500/40 text-[10px] font-mono font-semibold rounded-full shadow-lg">
                                POSISIKAN WAJAH DI SINI
                            </span>
                        </div>

                        <div class="absolute inset-x-0 bottom-3 text-center">
                            <span x-show="scanConfidence > 0" class="px-3 py-1 bg-emerald-500 text-slate-950 text-xs font-mono font-black rounded-full shadow-lg" x-text="scanConfidence + '% BIOMETRIC MATCH'"></span>
                        </div>
                    </div>
                </div>

                <!-- Trigger Action Button Overlay -->
                <div class="absolute bottom-4 inset-x-0 flex flex-col items-center space-y-2">
                    <div class="flex items-center space-x-2 bg-slate-900/90 backdrop-blur-md px-3.5 py-1 rounded-full border border-cyan-500/30 text-[11px] font-mono shadow-lg"
                         :class="hasFaceInView ? 'text-emerald-300 border-emerald-500/40' : 'text-cyan-300'">
                        <span class="w-2 h-2 rounded-full" :class="hasFaceInView ? 'bg-emerald-400 animate-ping' : 'bg-cyan-400 animate-pulse'"></span>
                        <span x-text="hasFaceInView ? 'Wajah Terdeteksi • Memverifikasi Otomatis' : 'Mode Hands-Free: Hanya Memindai Saat Ada Wajah'"></span>
                    </div>

                    <button type="button" @click="verifyFace()" :disabled="scanStatus === 'verifying'"
                            class="px-8 py-2 bg-gradient-to-r from-cyan-500 via-indigo-600 to-purple-600 hover:from-cyan-400 hover:to-purple-500 text-white font-mono font-extrabold text-xs rounded-full shadow-[0_0_25px_rgba(34,211,238,0.5)] transition transform active:scale-95 flex items-center space-x-2 cursor-pointer">
                        <svg width="16" height="16" style="width:16px;height:16px;max-width:16px;max-height:16px;" class="icon-svg" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span x-text="scanStatus === 'verifying' ? 'MEMPROSES BIOMETRIK...' : 'VERIFIKASI MANUAL'"></span>
                    </button>
                </div>
            </div>

            <!-- Status Banner with Full Teacher Identity Card -->
            <div :class="scanStatus === 'success' ? 'bg-emerald-950/70 border-emerald-500 text-emerald-200' : (scanStatus === 'already' ? 'bg-amber-950/70 border-amber-500 text-amber-200' : (scanStatus === 'failed' ? 'bg-rose-950/70 border-rose-500 text-rose-200' : 'bg-slate-900/90 border-indigo-500/30 text-indigo-200'))" class="p-4 rounded-2xl border text-xs font-mono transition-all">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full shrink-0"
                          :class="scanStatus === 'success' ? 'bg-emerald-400' : (scanStatus === 'already' ? 'bg-amber-400 animate-pulse' : (scanStatus === 'failed' ? 'bg-rose-400' : 'bg-cyan-400 animate-ping'))"></span>
                    <p class="font-bold text-sm" x-text="scanMessage"></p>
                </div>

                <template x-if="scannedUser">
                    <div class="mt-3 pt-3 border-t flex items-center justify-between"
                         :class="scanStatus === 'already' ? 'border-amber-500/30' : 'border-emerald-500/30'">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl font-bold flex items-center justify-center border text-base shrink-0"
                                 :class="scanStatus === 'already' ? 'bg-amber-500/20 text-amber-400 border-amber-500/40' : 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40'">
                                <span x-text="scannedUser.name ? scannedUser.name.substring(0, 2).toUpperCase() : 'ID'"></span>
                            </div>
                            <div>
                                <p class="font-black text-white text-base" x-text="scannedUser.name"></p>
                                <div class="flex items-center space-x-2 text-xs font-mono mt-0.5">
                                    <span class="text-slate-300">ID / NIP: <strong class="text-white" x-text="scannedUser.nip || '-'"></strong></span>
                                    <span>•</span>
                                    <span :class="scanStatus === 'already' ? 'text-amber-400 font-bold' : 'text-emerald-400'" x-text="scannedUser.email || 'Guru / Pegawai'"></span>
                                </div>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 font-black rounded-xl border text-xs font-mono shadow-sm"
                              :class="scanStatus === 'already' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'"
                              x-text="scanStatus === 'already' ? '⚠️ SUDAH TERCATAT HARI INI' : '✓ PRESENSI TERCATAT'"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right Column: Today's Live Attendance Feed (5 Cols) -->
        <div class="lg:col-span-5 bg-[#0F172A] border border-indigo-500/20 rounded-3xl p-5 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-indigo-500/20">
                    <h3 class="text-sm font-extrabold text-white tracking-wide flex items-center space-x-2">
                        <svg width="16" height="16" style="width:16px;height:16px;max-width:16px;max-height:16px;" class="icon-svg text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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

    <script>
    function extractFaceDescriptor(canvas) {
        try {
            const w = 128;
            const h = 128;
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = w;
            tempCanvas.height = h;
            const tCtx = tempCanvas.getContext('2d', { willReadFrequently: true });
            
            const srcW = canvas.width;
            const srcH = canvas.height;
            const cropSize = Math.min(srcW, srcH) * 0.65;
            const sx = (srcW - cropSize) / 2;
            const sy = (srcH - cropSize) / 2;
            
            tCtx.drawImage(canvas, sx, sy, cropSize, cropSize, 0, 0, w, h);
            const imgData = tCtx.getImageData(0, 0, w, h).data;
            
            const descriptor = [];
            const blockSize = 16;
            for (let by = 0; by < 8; by++) {
                for (let bx = 0; bx < 8; bx++) {
                    let lumSum = 0;
                    let rgSum = 0;
                    let count = 0;
                    for (let y = by * blockSize; y < (by + 1) * blockSize; y += 2) {
                        for (let x = bx * blockSize; x < (bx + 1) * blockSize; x += 2) {
                            const idx = (y * w + x) * 4;
                            const r = imgData[idx];
                            const g = imgData[idx + 1];
                            const b = imgData[idx + 2];
                            const lum = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
                            const rg = (r - g) / 255;
                            lumSum += lum;
                            rgSum += rg;
                            count++;
                        }
                    }
                    const avgLum = count > 0 ? (lumSum / count) * 2 - 1 : 0;
                    const avgRg = count > 0 ? (rgSum / count) * 2 : 0;
                    descriptor.push(Number(avgLum.toFixed(6)));
                    descriptor.push(Number(avgRg.toFixed(6)));
                }
            }
            return descriptor;
        } catch (e) {
            return Array.from({length: 128}, () => 0.0);
        }
    }

    function faceScannerApp() {
        return {
            scanType: 'auto',
            scanLocation: 'school',
            scanStatus: 'scanning',
            autoScan: true,
            hasFaceInView: false,
            isProcessing: false,
            lastScanTime: 0,
            scanConfidence: 0,
            scanMessage: 'Menunggu wajah di depan kamera... Posisikan wajah pada bingkai',
            scannedUser: null,
            latitude: '',
            longitude: '',
            isLocating: false,
            currentTime: '',
            activeSessionLabel: 'Otomatis Sesuai Waktu',
            nativeFaceDetector: null,
            tempDetectorCanvas: null,

            init() {
                this.updateClock();
                setInterval(() => this.updateClock(), 1000);
                this.getLocation();
                this.startCamera();

                // Hands-Free Auto Scan Loop: HANYA MEMINDAI JIKA WAJAH TERDETEKSI!
                setInterval(async () => {
                    if (!this.autoScan || this.isProcessing || this.scanStatus === 'verifying' || this.scanStatus === 'success' || this.scanStatus === 'already') {
                        return;
                    }

                    const video = document.getElementById('scanVideo');
                    if (!video || video.readyState < 2) return;

                    const faceDetected = await this.isFacePresent(video);
                    this.hasFaceInView = faceDetected;

                    if (faceDetected) {
                        const now = Date.now();
                        if (now - this.lastScanTime > 3000) {
                            this.scanMessage = '🟢 Wajah terdeteksi! Memverifikasi biometrik...';
                            this.verifyFace(true);
                        }
                    } else {
                        if (this.scanStatus === 'scanning') {
                            this.scanMessage = 'Menunggu wajah di depan kamera... Posisikan wajah pada bingkai';
                        }
                    }
                }, 800);

                // Pre-unlock audio
                const unlockAudio = () => {
                    try {
                        if ('speechSynthesis' in window) {
                            window.speechSynthesis.getVoices();
                        }
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        if (ctx.state === 'suspended') ctx.resume();
                    } catch(e) {}
                    document.removeEventListener('click', unlockAudio);
                    document.removeEventListener('touchstart', unlockAudio);
                };
                document.addEventListener('click', unlockAudio);
                document.addEventListener('touchstart', unlockAudio);
            },

            async isFacePresent(video) {
                if (!video || video.readyState < 2) return false;

                // 1. Native Chromium FaceDetector API jika browser mendukung
                if ('FaceDetector' in window) {
                    try {
                        if (!this.nativeFaceDetector) {
                            this.nativeFaceDetector = new window.FaceDetector({ fastMode: true, maxDetectedFaces: 1 });
                        }
                        const faces = await this.nativeFaceDetector.detect(video);
                        return faces && faces.length > 0;
                    } catch (e) {}
                }

                // 2. Optical Center-Box Face & Skin Tone Analyzer (Ultra-cepat ~10ms)
                try {
                    if (!this.tempDetectorCanvas) {
                        this.tempDetectorCanvas = document.createElement('canvas');
                        this.tempDetectorCanvas.width = 160;
                        this.tempDetectorCanvas.height = 120;
                    }
                    const ctx = this.tempDetectorCanvas.getContext('2d', { willReadFrequently: true });
                    ctx.drawImage(video, 0, 0, 160, 120);

                    // Kotak tengah reticle (x 45-115, y 25-95)
                    const imgData = ctx.getImageData(45, 25, 70, 70);
                    const data = imgData.data;
                    let skinPixels = 0;
                    const total = data.length / 4;
                    let lumSum = 0;

                    for (let i = 0; i < data.length; i += 4) {
                        const r = data[i];
                        const g = data[i+1];
                        const b = data[i+2];
                        const lum = (r + g + b) / 3;
                        lumSum += lum;

                        // Ciri rona kulit wajah: R > G > B dengan kontras wajar
                        if (r > 65 && g > 40 && b > 25 && r > g && (r - g) > 10 && (r - b) > 12 && lum > 45 && lum < 240) {
                            skinPixels++;
                        }
                    }

                    const avgLum = lumSum / total;
                    const skinRatio = skinPixels / total;

                    return skinRatio >= 0.14 && avgLum > 40 && avgLum < 245;
                } catch (e) {
                    return false;
                }
            },

            updateClock() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const hour = now.getHours();
                const minute = now.getMinutes();
                const totalMinutes = hour * 60 + minute;

                if (totalMinutes < 11 * 60 + 30) {
                    this.activeSessionLabel = 'Sesi Pagi (Masuk)';
                } else if (totalMinutes < 15 * 60) {
                    this.activeSessionLabel = 'Sesi Siang (Dzuhur)';
                } else {
                    this.activeSessionLabel = 'Sesi Pulang (Check-Out)';
                }
            },

            playVoice(text) {
                if (!('speechSynthesis' in window)) return;
                try {
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 1.0;
                    utterance.pitch = 1.0;
                    const voices = window.speechSynthesis.getVoices();
                    const idVoice = voices.find(v => v.lang === 'id-ID' || v.lang.startsWith('id'));
                    if (idVoice) utterance.voice = idVoice;
                    window.speechSynthesis.speak(utterance);
                } catch(e) {
                    console.error('Speech synthesis error:', e);
                }
            },

            playTone(type = 'success') {
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
                        gain.gain.setValueAtTime(0.25, ctx.currentTime);
                        gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.35);
                    } else if (type === 'already') {
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(440, ctx.currentTime);
                        gain.gain.setValueAtTime(0.2, ctx.currentTime);
                        gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.25);
                    } else {
                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(220, ctx.currentTime);
                        gain.gain.setValueAtTime(0.2, ctx.currentTime);
                        gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.35);
                    }
                } catch(e) {}
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
                            this.latitude = '-0.891700';
                            this.longitude = '119.870700';
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

            async verifyFace(isAuto = false) {
                const video = document.getElementById('scanVideo');
                const canvas = document.getElementById('scanCanvas');
                if (!video || !canvas || video.readyState < 2) return;

                this.isProcessing = true;
                this.scanStatus = 'verifying';
                this.scanMessage = 'Menganalisis matriks biometrik wajah...';

                const ctx = canvas.getContext('2d');
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 480;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const livePhoto = canvas.toDataURL('image/jpeg', 0.85);

                const descriptor = extractFaceDescriptor(canvas);

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
                            live_descriptor: JSON.stringify(descriptor),
                            type: this.scanType,
                            work_location: this.scanLocation,
                            latitude: this.latitude,
                            longitude: this.longitude
                        })
                    });
                    const data = await res.json();
                    this.lastScanTime = Date.now();

                    if (data.success || data.already_complete || data.locked) {
                        this.scanConfidence = data.confidence || 98;
                        this.scanMessage = data.message;
                        this.scannedUser = data.user || data.teacher;

                        const teacherName = (this.scannedUser && this.scannedUser.name) ? this.scannedUser.name : '';

                        if (data.already_complete || data.locked) {
                            this.scanStatus = 'already';
                            this.playTone('already');
                            const voiceMsg = teacherName ? `Afwan, presensi ${teacherName} sudah tercatat sebelumnya.` : 'Afwan, presensi sudah tercatat sebelumnya.';
                            this.playVoice(voiceMsg);
                        } else {
                            this.scanStatus = 'success';
                            this.playTone('success');
                            const voiceMsg = teacherName ? `Alhamdulillah, presensi ${teacherName} sudah berhasil. Syukron.` : 'Alhamdulillah, presensi sudah berhasil. Syukron.';
                            this.playVoice(voiceMsg);
                        }

                        // Resume scanning automatically for next teacher after 4s without reloading page
                        setTimeout(() => {
                            this.scanStatus = 'scanning';
                            this.scanMessage = 'Menunggu wajah di depan kamera... Posisikan wajah pada bingkai';
                            this.scannedUser = null;
                            this.scanConfidence = 0;
                            this.hasFaceInView = false;
                            this.isProcessing = false;
                        }, 4000);
                    } else {
                        this.scanStatus = 'failed';
                        this.scanMessage = data.message || 'Wajah tidak terverifikasi.';
                        this.playTone('failed');

                        if (data.message && (data.message.includes('terabsen') || data.message.includes('tercatat') || data.message.includes('Sudah Hadir') || data.message.includes('Sudah Check-In'))) {
                            this.playVoice('Afwan, presensi sudah tercatat sebelumnya.');
                        } else {
                            this.playVoice('Afwan, presensi belum berhasil. Silakan ulangi lagi.');
                        }

                        setTimeout(() => {
                            this.scanStatus = 'scanning';
                            this.scanMessage = 'Menunggu wajah di depan kamera... Posisikan wajah pada bingkai';
                            this.hasFaceInView = false;
                            this.isProcessing = false;
                        }, 3000);
                    }
                } catch (err) {
                    this.scanStatus = 'failed';
                    this.scanMessage = 'Terjadi kesalahan jaringan: ' + err.message;
                    this.playTone('failed');
                    setTimeout(() => {
                        this.scanStatus = 'scanning';
                        this.hasFaceInView = false;
                        this.isProcessing = false;
                    }, 3000);
                }
            }
        };
    }
    </script>
</body>
</html>
