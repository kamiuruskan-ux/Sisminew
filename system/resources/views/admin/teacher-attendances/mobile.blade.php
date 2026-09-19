<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#2563EB">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Portal Mobile Asatidzah & Pegawai - {{ $schoolName ?? config('app.name', 'SDIT AL-FAHMI PALU') }}</title>

    <!-- Google Fonts: Inter, Plus Jakarta Sans, Amiri (Arabic), JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                        arabic: ['Amiri', 'Traditional Arabic', 'serif'],
                    },
                    colors: {
                        primary: {
                            50: '#EFF6FF',
                            100: '#DBEAFE',
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8',
                            800: '#1E40AF',
                            900: '#1E3A8A',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: #F1F5F9;
            color: #0F172A;
            -webkit-tap-highlight-color: transparent;
        }

        /* Cyber grid tech pattern for GPS card */
        .bg-cyber-grid {
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.12) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.12) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        /* Glow effects */
        .glow-fab {
            box-shadow: 0 10px 25px -3px rgba(37, 99, 235, 0.5), 0 4px 6px -2px rgba(37, 99, 235, 0.25);
        }

        /* Subtle scrollbars */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.5);
            border-radius: 9999px;
        }

        @keyframes pulseSoft {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.04); opacity: 0.92; }
        }
        .animate-pulse-soft {
            animation: pulseSoft 3s infinite ease-in-out;
        }
    </style>
</head>
<body class="antialiased selection:bg-blue-600 selection:text-white"
      x-data="mobilePortalApp()"
      x-init="init()">

    <!-- App Mobile Viewport Container -->
    <div class="max-w-md mx-auto min-h-screen bg-slate-50 shadow-2xl relative flex flex-col justify-between overflow-x-hidden pb-28">

        <!-- ============================================================ -->
        <!-- TAB 1: BERANDA (HOME) -->
        <!-- ============================================================ -->
        <div x-show="activeTab === 'beranda'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 p-4">
            
            <!-- Header User Greeting & Notif -->
            <div class="flex items-center justify-between pt-1 pb-1">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-blue-700 to-cyan-500 text-white font-extrabold flex items-center justify-center text-base shadow-md border-2 border-white">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-500 tracking-wide uppercase">Assalamu'alaikum,</p>
                        <h2 class="text-base font-extrabold text-slate-900 leading-tight truncate max-w-[200px]">
                            {{ $user->name ?? 'Asatidzah' }}
                        </h2>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($isPrincipal)
                    <button @click="openPrincipalModal = true" class="px-2.5 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold text-[11px] shadow-sm flex items-center gap-1 active:scale-95 transition-all">
                        <span>👑</span>
                        <span>Kontrol Sesi</span>
                    </button>
                    @endif
                    <!-- Notif Bell -->
                    <button @click="openNotificationModal = true" class="relative w-10 h-10 rounded-full bg-white border border-slate-200/80 shadow-sm flex items-center justify-center text-slate-600 hover:text-blue-600 active:scale-95 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full shadow-sm">9+</span>
                    </button>
                </div>
            </div>

            <!-- Hero Welcome Islamic Card -->
            <div class="rounded-3xl bg-gradient-to-br from-blue-600 via-blue-600 to-cyan-500 p-5 text-white shadow-xl shadow-blue-500/20 relative overflow-hidden">
                <!-- Background decorative shapes -->
                <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                <div class="absolute right-4 top-4 text-white/20">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" opacity="0.4"/>
                    </svg>
                </div>

                <div class="relative z-10 space-y-3">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-blue-100 font-medium" x-text="timeGreeting"></p>
                        <span class="text-xl">👋</span>
                    </div>
                    
                    <h1 class="text-2xl font-black tracking-tight" x-text="'{{ explode(' ', $user->name ?? 'Guru')[0] }}!'"></h1>
                    <p class="text-xs text-blue-50/90 font-medium">Jurnal KBM & Absensi Mandiri Pegawai siap diisi!</p>

                    <!-- Date and Time Badges (Pills) -->
                    <div class="pt-2 space-y-1.5">
                        <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 py-1.5 rounded-xl text-xs font-semibold text-white/95 border border-white/20">
                            <span>📅</span>
                            <span x-text="currentDateFormatted">Sabtu, 19 September 2026</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/15 backdrop-blur-md px-3 py-1.5 rounded-xl text-xs font-semibold text-white/95 border border-white/20">
                            <span>🌙</span>
                            <span x-text="hijriDate">8 Rabiul Akhir 1448 H</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/20 backdrop-blur-md px-3 py-1.5 rounded-xl text-xs font-bold text-white border border-white/25">
                            <span>🕒</span>
                            <span class="font-mono tracking-wider" x-text="currentTime + ' ' + timezoneLabel">00:15:18 WITA</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LIVE BRIEFING KEPALA SEKOLAH CARD (Real-time Broadcast) -->
            <div x-show="briefingSession && briefingSession.active"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 class="rounded-3xl bg-gradient-to-br from-amber-500 via-orange-600 to-rose-600 p-5 text-white shadow-xl shadow-orange-500/25 relative overflow-hidden animate-pulse-soft border border-orange-300/30">
                <div class="flex items-center justify-between gap-2 mb-2.5">
                    <span class="px-3 py-1 rounded-full bg-white/25 backdrop-blur-md text-[10px] font-mono font-black tracking-wider uppercase flex items-center gap-1.5 border border-white/30">
                        <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                        <span>📢 SESI BRIEFING KEPALA SEKOLAH</span>
                    </span>
                    <span class="text-[11px] font-bold text-orange-100 font-mono" x-text="'Dibuka ' + (briefingSession.opened_at || '')"></span>
                </div>

                <h3 class="text-base font-black text-white leading-snug mb-1 drop-shadow-sm" x-text="briefingSession.title"></h3>
                <p class="text-xs text-orange-100/95 font-medium leading-relaxed mb-4" x-text="briefingSession.content"></p>

                <!-- State: Sudah Hadir vs Hadir Sekarang -->
                <template x-if="hasAttendedBriefing">
                    <div class="w-full py-2.5 px-3 rounded-2xl bg-emerald-950/40 border border-emerald-300/50 text-emerald-200 font-extrabold text-xs flex items-center justify-center gap-2 shadow-inner">
                        <span class="text-base">✅</span>
                        <span>ALHAMDULILLAH, ANDA TELAH HADIR BRIEFING</span>
                    </div>
                </template>

                <template x-if="!hasAttendedBriefing">
                    <button @click="submitBriefingAttendance()"
                            :disabled="isSubmitting || (attendanceMode === 'reguler' && !inRadius)"
                            class="w-full py-3 px-4 rounded-2xl bg-white text-orange-700 hover:bg-orange-50 font-black text-xs shadow-lg active:scale-98 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:pointer-events-none cursor-pointer">
                        <span x-show="!isSubmitting">👉 HADIR BRIEFING SEKARANG (GPS)</span>
                        <span x-show="isSubmitting" class="animate-spin">⏳</span>
                    </button>
                </template>
            </div>

            <!-- Daily Attendance Progress Status -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80">
                <div class="flex items-center justify-between text-xs font-bold mb-2">
                    <div class="flex items-center gap-3">
                        <span class="text-emerald-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="hasCheckedIn && hasCheckedOut ? '2 Selesai' : (hasCheckedIn ? '1 Selesai' : '0 Selesai')">0 Selesai</span>
                        </span>
                        <span class="text-amber-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="hasCheckedIn && hasCheckedOut ? '0 Belum' : (hasCheckedIn ? '1 Belum' : '2 Belum')">1 Belum</span>
                        </span>
                    </div>
                    <span class="text-blue-600 font-extrabold" x-text="hasCheckedIn && hasCheckedOut ? '100%' : (hasCheckedIn ? '50%' : '0%')">0%</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full transition-all duration-500"
                         :style="`width: ${hasCheckedIn && hasCheckedOut ? '100%' : (hasCheckedIn ? '50%' : '0%')}`"></div>
                </div>
            </div>

            <!-- Checklist & Tugas Harian Pegawai Banner -->
            <button @click="activeTab = 'laporan'" class="w-full text-left rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-4 text-white shadow-md hover:shadow-lg transition-all flex items-center justify-between group active:scale-[0.99]">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm group-hover:scale-105 transition-transform text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7m-5-4v2m-6 0v2m-3 4h16M4 21h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-black tracking-wide">Checklist & Tugas Harian Pegawai</h4>
                        <p class="text-[11px] text-blue-100 mt-0.5">Lihat & selesaikan instruksi / daftar tugas harian Anda</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-rose-500 text-white font-black text-xs flex items-center justify-center shadow">1</span>
                    <svg class="w-5 h-5 text-white/80 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </button>

            <!-- Syi'ar & Informasi Sekolah Section -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-wider">Syi'ar & Informasi Sekolah</h3>
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                        <span class="w-4 h-2 rounded-full bg-emerald-500"></span>
                    </div>
                </div>

                <!-- Featured Image Card -->
                <div class="rounded-2xl overflow-hidden relative shadow-md bg-slate-900 border border-slate-200">
                    <div class="h-44 bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=800&auto=format&fit=crop');">
                        <!-- Dark gradient overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
                        <div class="absolute top-3 left-3">
                            <span class="px-2.5 py-1 rounded-md bg-amber-400 text-slate-950 font-black text-[10px] tracking-wider uppercase">GURU</span>
                        </div>
                        <div class="absolute bottom-3 left-3 right-3 text-white space-y-1">
                            <h4 class="text-sm font-black leading-snug">Kajian Akbar Bulanan Guru & Karyawan</h4>
                            <p class="text-[11px] text-slate-200 line-clamp-2 leading-relaxed">
                                Menumbuhkan Keikhlasan Berkhidmat di Lembaga Pendidikan Islam. Bersama Pengasuh & Pembina Yayasan SDIT Al-Fahmi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengumuman Terkini Section -->
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-wider">Pengumuman Terkini</h3>
                    <span class="text-[11px] font-bold text-blue-600">Terbaru</span>
                </div>

                <div class="space-y-2">
                    @forelse($announcements as $announcement)
                        <div class="bg-white rounded-2xl p-3.5 shadow-sm border border-slate-200/80 space-y-1">
                            <span class="text-[10px] font-semibold text-slate-400">{{ $announcement->created_at->format('Y-m-d') }}</span>
                            <h4 class="text-xs font-bold text-slate-900 leading-snug">{{ $announcement->title }}</h4>
                            <p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">{{ \Illuminate\Support\Str::limit($announcement->content, 110) }}</p>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl p-3.5 shadow-sm border border-slate-200/80 space-y-1">
                            <span class="text-[10px] font-semibold text-slate-400">2026-09-18</span>
                            <h4 class="text-xs font-bold text-slate-900 leading-snug">Ujian Tasmi' Al-Qur'an Pertengahan Semester</h4>
                            <p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">Untuk seluruh asatidzah halaqah Tahfidz dan Tahsin, jadwal ujian komprehensif pekan depan.</p>
                        </div>
                        <div class="bg-white rounded-2xl p-3.5 shadow-sm border border-slate-200/80 space-y-1">
                            <span class="text-[10px] font-semibold text-slate-400">2026-09-15</span>
                            <h4 class="text-xs font-bold text-slate-900 leading-snug">Sosialisasi Aplikasi Presensi Mobile GPS Sismi</h4>
                            <p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">Presensi mandiri guru kini dapat dilakukan langsung melalui smartphone saat berada dalam radius resmi sekolah.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Hikmah & Mutiara Hari Ini Card -->
            <div class="rounded-2xl bg-gradient-to-br from-[#064E3B] via-[#065F46] to-[#047857] p-5 text-white shadow-lg space-y-3 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 bg-emerald-900/50 backdrop-blur-sm px-2.5 py-1 rounded-full border border-emerald-400/20 text-[10px] font-bold text-emerald-200">
                        <span>✨</span>
                        <span>HIKMAH & MUTIARA HARI INI</span>
                    </div>
                    <span class="text-[10px] font-black text-emerald-300 tracking-wider uppercase">{{ $hadithToday['category'] ?? 'HADITS' }}</span>
                </div>

                <!-- Arabic Text -->
                <p class="font-arabic text-xl font-bold text-emerald-50 leading-loose text-right dir-rtl tracking-wide pt-1">
                    {{ $hadithToday['arabic'] }}
                </p>

                <!-- Indonesian Translation -->
                <p class="text-xs text-emerald-100 italic leading-relaxed pt-1">
                    "{{ $hadithToday['translation'] }}"
                </p>
                <p class="text-[10px] font-bold text-emerald-300 text-right">
                    — {{ $hadithToday['narrator'] }}
                </p>
            </div>

        </div>


        <!-- ============================================================ -->
        <!-- TAB 2: JADWAL (KALENDER & PRESENSI KEGIATAN) -->
        <!-- ============================================================ -->
        <div x-show="activeTab === 'jadwal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 p-4">
            
            <!-- Program Kegiatan Header -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-1">
                <span class="text-[10px] font-black text-blue-600 tracking-wider uppercase">PROGRAM KEGIATAN & SYI'AR</span>
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>📅</span> Kalender & Presensi Kegiatan
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Daftar agenda kajian, rapat asatidzah, dan milad santri. Lakukan check-in absen terikat koordinat lokasi secara real-time.
                </p>
            </div>

            <!-- Kalender Bulanan Interaktif -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                        <span>🗓️</span> KALENDER AGENDA BULANAN
                    </span>
                    <span class="text-[11px] text-slate-400">Pilih tanggal untuk filter</span>
                </div>

                <!-- Month Switcher -->
                <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2 border border-slate-200/60">
                    <button class="p-1 hover:bg-slate-200 rounded-lg text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <span class="text-xs font-extrabold text-slate-900 uppercase tracking-wider" x-text="calendarMonthLabel">SEPTEMBER 2026</span>
                    <button class="p-1 hover:bg-slate-200 rounded-lg text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <!-- Days Table -->
                <div class="grid grid-cols-7 gap-1 text-center">
                    <span class="text-[10px] font-extrabold text-rose-500 py-1">MIN</span>
                    <span class="text-[10px] font-bold text-slate-500 py-1">SEN</span>
                    <span class="text-[10px] font-bold text-slate-500 py-1">SEL</span>
                    <span class="text-[10px] font-bold text-slate-500 py-1">RAB</span>
                    <span class="text-[10px] font-bold text-slate-500 py-1">KAM</span>
                    <span class="text-[10px] font-bold text-slate-500 py-1">JUM</span>
                    <span class="text-[10px] font-bold text-slate-500 py-1">SAB</span>

                    <!-- Empty slots for start of month if any -->
                    <span></span><span></span>
                    
                    <template x-for="day in 30" :key="day">
                        <button @click="selectedDate = day"
                                :class="selectedDate === day ? 'bg-blue-600 text-white font-black shadow-md scale-105' : 'text-slate-700 hover:bg-slate-100 font-semibold'"
                                class="w-9 h-9 mx-auto rounded-xl flex items-center justify-center text-xs transition-all">
                            <span x-text="day"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Filter Pills -->
            <div class="flex items-center gap-2">
                <button @click="agendaFilter = 'SEMUA'" :class="agendaFilter === 'SEMUA' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200'" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all">
                    SEMUA
                </button>
                <button @click="agendaFilter = 'UMUM'" :class="agendaFilter === 'UMUM' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200'" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all">
                    UMUM
                </button>
                <button @click="agendaFilter = 'GURU'" :class="agendaFilter === 'GURU' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200'" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all">
                    GURU
                </button>
            </div>

            <!-- Agenda List -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <span>🕒</span> AGENDA KEGIATAN SEBARAN PARTISIPAN ({{ count($agendas) }})
                    </h3>
                </div>

                @foreach($agendas as $agenda)
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-2.5">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-bold text-[10px]">📅 {{ $agenda['date'] }}</span>
                            <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold text-[10px]">{{ $agenda['audience'] }}</span>
                            @if($agenda['is_gps_lock'])
                                <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-600 font-black text-[10px]">GPS LOCK</span>
                            @endif
                        </div>
                        <span class="px-2 py-0.5 rounded-md {{ $agenda['status'] === 'SELESAI' ? 'bg-slate-100 text-slate-500' : 'bg-emerald-100 text-emerald-700' }} font-black text-[10px]">
                            🔒 {{ $agenda['status'] }}
                        </span>
                    </div>

                    <h4 class="text-sm font-extrabold text-slate-900 leading-snug">{{ $agenda['title'] }}</h4>
                    <p class="text-xs text-slate-500">{{ $agenda['description'] }}</p>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                        <span class="flex items-center gap-1">📍 {{ $agenda['location'] }}</span>
                        <span class="flex items-center gap-1 font-semibold text-blue-600">👥 {{ $agenda['attended_count'] }} Hadir</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Rekapitulasi Hadir Terkini -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <span>📊</span> REKAPITULASI HADIR TERKINI ({{ $latestSchoolAttendances->count() }})
                    </h3>
                </div>

                <div class="space-y-2">
                    @forelse($latestSchoolAttendances as $log)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ $log->user->name ?? 'Guru / Asatidz' }}</p>
                            <p class="text-[10px] text-slate-400">{{ $log->notes ?? 'Presensi Mandiri' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-mono font-bold text-slate-700">{{ $log->check_in ?? '-' }}</p>
                            <span class="text-[10px] font-bold text-amber-600">Tervalidasi GPS</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-xs text-slate-400">
                        Belum ada data hadir kegiatan hari ini
                    </div>
                    @endforelse
                </div>
            </div>

        </div>


        <!-- ============================================================ -->
        <!-- TAB 3: ABSEN (PRESENSI GPS & MULTI-SESI) -->
        <!-- ============================================================ -->
        <div x-show="activeTab === 'absen'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 p-4">
            
            <!-- Tech Geofence Digital Clock Card -->
            <div class="rounded-3xl bg-gradient-to-br from-[#1E3A8A] via-[#2563EB] to-[#0284C7] p-5 text-white shadow-xl shadow-blue-600/25 relative overflow-hidden bg-cyber-grid">
                <!-- Badge Encrypted Geofence -->
                <div class="flex items-center justify-center mb-2">
                    <span class="px-3 py-1 rounded-full bg-black/30 backdrop-blur-md border border-white/20 text-[10px] font-mono font-black text-cyan-300 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span x-text="timezoneLabel + ' TERENKRIPSI OTOMATIS (GPS GEOFENCE)'">WITA TERENKRIPSI OTOMATIS (GPS GEOFENCE)</span>
                    </span>
                </div>

                <!-- Large Digital Clock -->
                <div class="text-center py-2">
                    <div class="text-3xl sm:text-4xl font-black font-mono tracking-wider drop-shadow-md text-white">
                        <span x-text="currentTime">00:16:24</span>
                        <span class="text-cyan-300 text-lg sm:text-xl font-sans" x-text="timezoneLabel">WITA</span>
                    </div>
                </div>

                <!-- Dates Sub-Pills -->
                <div class="flex items-center justify-center gap-2 pt-1 text-[11px] font-semibold text-blue-100 flex-wrap">
                    <span x-text="currentDateFormatted">Sabtu, 19 September 2026</span>
                    <span>•</span>
                    <span x-text="hijriDate">8 Rabiul Akhir 1448 H</span>
                </div>
            </div>

            <!-- Pilihan Moda Presensi -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs font-black text-slate-800 tracking-wide uppercase">Pilihan Moda Presensi</span>
                </div>

                <!-- Segmented Control Button -->
                <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1.5 rounded-xl border border-slate-200/60">
                    <button @click="attendanceMode = 'reguler'"
                            :class="attendanceMode === 'reguler' ? 'bg-white text-blue-700 shadow-sm font-black' : 'text-slate-600 font-semibold'"
                            class="py-2.5 px-2 rounded-lg text-xs transition-all flex items-center justify-center gap-1.5 text-center leading-tight">
                        <span>🏛️</span>
                        <span>Presensi Reguler<br><span class="text-[10px] opacity-75">(Sekolah)</span></span>
                    </button>
                    <button @click="attendanceMode = 'dinas_luar'"
                            :class="attendanceMode === 'dinas_luar' ? 'bg-white text-purple-700 shadow-sm font-black' : 'text-slate-600 font-semibold'"
                            class="py-2.5 px-2 rounded-lg text-xs transition-all flex items-center justify-center gap-1.5 text-center leading-tight">
                        <span>💼</span>
                        <span>Dinas / Tugas Luar<br><span class="text-[10px] opacity-75">(Bebas Radius)</span></span>
                    </button>
                </div>

                <!-- Dynamic Notice depending on Mode -->
                <div x-show="attendanceMode === 'reguler'" class="bg-blue-50/80 rounded-xl p-3 border border-blue-200/60 flex items-start gap-2.5 text-[11px] text-blue-900 leading-relaxed">
                    <span class="text-base mt-0.5">ℹ️</span>
                    <div>
                        <span class="font-bold">Moda Standar:</span> Lokasi GPS wajib berada di dalam radius resmi area {{ $schoolName ?? 'SDIT Al-Fahmi Kota Palu' }} (Maks. <span x-text="schoolRadius">150</span> meter).
                    </div>
                </div>

                <!-- Dinas Luar Note Input -->
                <div x-show="attendanceMode === 'dinas_luar'" class="space-y-2 pt-1" style="display: none;">
                    <div class="bg-purple-50/80 rounded-xl p-3 border border-purple-200/60 flex items-start gap-2.5 text-[11px] text-purple-900 leading-relaxed">
                        <span class="text-base mt-0.5">📝</span>
                        <div>
                            <span class="font-bold">Moda Tugas Luar:</span> Anda diizinkan presensi di luar radius sekolah. Tuliskan lokasi/keperluan dinas Anda di bawah.
                        </div>
                    </div>
                    <input type="text" x-model="dinasNotes" placeholder="Contoh: Menghadiri MGMP Asatidzah di Dinas Pendidikan..."
                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-200 outline-none transition-all">
                </div>

                <!-- Real-time GPS Radar Card -->
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full" :class="gpsLoading ? 'bg-amber-400 animate-ping' : (inRadius ? 'bg-emerald-500' : 'bg-rose-500')"></span>
                            Radar Lokasi GPS Satelit
                        </span>
                        <button @click="detectGps()" class="text-blue-600 font-bold text-[11px] hover:underline flex items-center gap-1">
                            <span>🔄</span> Refresh GPS
                        </button>
                    </div>

                    <template x-if="gpsLoading">
                        <p class="text-[11px] text-amber-600 animate-pulse">📡 Mendeteksi koordinat lintang & bujur perangkat Anda...</p>
                    </template>
                    <template x-if="gpsError">
                        <div class="p-3 bg-rose-50 rounded-xl border border-rose-200 space-y-2">
                            <p class="text-[11px] text-rose-700 font-semibold" x-text="gpsError"></p>
                            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                <button type="button" @click="detectGps()" class="px-3 py-1 rounded-lg bg-rose-600 text-white font-bold text-[10px] hover:bg-rose-700 flex items-center gap-1">
                                    <span>🔄</span> Minta Ulang Izin
                                </button>
                                <button type="button" @click="attendanceMode = 'dinas_luar'" class="px-3 py-1 rounded-lg bg-indigo-600 text-white font-bold text-[10px] hover:bg-indigo-700 flex items-center gap-1">
                                    <span>✈️</span> Mode Dinas Luar
                                </button>
                                <button type="button" @click="enableGpsFallback()" class="px-3 py-1 rounded-lg bg-slate-200 text-slate-800 font-bold text-[10px] hover:bg-slate-300">
                                    Bypass Darurat (Lokasi Sekolah)
                                </button>
                            </div>
                        </div>
                    </template>
                    <template x-if="!gpsLoading && !gpsError">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-600">Jarak ke Gerbang Sekolah:</span>
                            <span class="font-bold font-mono" :class="inRadius ? 'text-emerald-600' : 'text-rose-600'" x-text="distanceMeters !== null ? distanceMeters + ' Meter' : '-'"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Manual Override Notice Banner (If active) -->
            <template x-if="sessionSettings.manual_override">
                <div class="rounded-2xl bg-amber-500/10 border border-amber-500/30 p-3.5 text-amber-900 flex items-start gap-2.5 text-xs">
                    <span class="text-base leading-none">⚡</span>
                    <div>
                        <span class="font-extrabold uppercase">Mode Buka Paksa (Manual Override):</span>
                        <p class="text-[11px] text-amber-800 mt-0.5">Semua sesi presensi saat ini dibuka langsung oleh Kepala Sekolah tanpa pembatasan jam.</p>
                    </div>
                </div>
            </template>

            <!-- SESI BRIEFING KEPALA SEKOLAH CARD (Tab Absen) -->
            <div x-show="briefingSession && briefingSession.active"
                 class="rounded-3xl bg-gradient-to-br from-amber-500 via-orange-600 to-rose-600 p-5 text-white shadow-xl shadow-orange-500/25 relative overflow-hidden animate-pulse-soft border border-orange-300/30">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="px-3 py-1 rounded-full bg-white/25 backdrop-blur-md text-[10px] font-mono font-black tracking-wider uppercase flex items-center gap-1.5 border border-white/30">
                        <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                        <span>📢 BRIEFING KEPALA SEKOLAH</span>
                    </span>
                    <span class="text-[11px] font-bold text-orange-100 font-mono" x-text="'Dibuka ' + (briefingSession.opened_at || '')"></span>
                </div>

                <h3 class="text-sm font-black text-white leading-snug mb-1" x-text="briefingSession.title"></h3>
                <p class="text-xs text-orange-100/95 font-medium leading-relaxed mb-3.5" x-text="briefingSession.content"></p>

                <template x-if="hasAttendedBriefing">
                    <div class="w-full py-2.5 px-3 rounded-2xl bg-emerald-950/40 border border-emerald-300/50 text-emerald-200 font-extrabold text-xs flex items-center justify-center gap-2">
                        <span>✅</span>
                        <span>ALHAMDULILLAH, ANDA TELAH HADIR BRIEFING</span>
                    </div>
                </template>

                <template x-if="!hasAttendedBriefing">
                    <button @click="submitBriefingAttendance()"
                            :disabled="isSubmitting || (attendanceMode === 'reguler' && !inRadius)"
                            class="w-full py-3 px-4 rounded-2xl bg-white text-orange-700 hover:bg-orange-50 font-black text-xs shadow-lg active:scale-98 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:pointer-events-none cursor-pointer">
                        <span x-show="!isSubmitting">👉 HADIR BRIEFING SEKARANG (GPS)</span>
                        <span x-show="isSubmitting" class="animate-spin">⏳</span>
                    </button>
                </template>
            </div>

            <!-- Shift Sesi Presensi Cards -->
            <div class="space-y-3">

                <!-- SESI 1 / PAGI (PRESENSI MASUK) -->
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-black text-slate-900 uppercase">SESI 1 / PAGI (MASUK)</h4>
                            <p class="text-[11px] text-slate-500">
                                Buka: <span x-text="sessionSettings.morning_open">06:00</span> | 
                                Batas: <span x-text="sessionSettings.morning_late">07:30</span> |
                                Tutup: <span x-text="sessionSettings.morning_close">11:59</span> <span x-text="timezoneLabel">WITA</span>
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-extrabold text-[10px]" x-text="'Buka ' + sessionSettings.morning_open">
                            Buka 06:00
                        </span>
                    </div>

                    <!-- Action State Container -->
                    <template x-if="!hasCheckedIn">
                        <div>
                            <template x-if="isMorningOpen()">
                                <button @click="submitAttendance('check_in')"
                                        :disabled="isSubmitting || (attendanceMode === 'reguler' && !inRadius)"
                                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-extrabold text-xs shadow-md active:scale-98 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
                                    <span x-show="!isSubmitting">👉 PRESENSI MASUK SEKARANG (GPS)</span>
                                    <span x-show="isSubmitting" class="animate-spin">⏳</span>
                                </button>
                            </template>
                            <template x-if="!isMorningOpen()">
                                <div class="w-full py-3 px-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center text-xs font-bold text-slate-400 flex items-center justify-center gap-2">
                                    <span>🔒</span>
                                    <span>BELUM DIBUKA (Jadwal: <span x-text="sessionSettings.morning_open + ' - ' + sessionSettings.morning_close">06:00 - 11:59</span> <span x-text="timezoneLabel">WITA</span>)</span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="hasCheckedIn">
                        <div class="w-full py-3 px-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-extrabold text-xs flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="text-emerald-600 text-base">✅</span>
                                <span>SUDAH PRESENSI MASUK</span>
                            </span>
                            <span class="font-mono text-[11px]" x-text="checkInTime"></span>
                        </div>
                    </template>
                </div>

                <!-- SESI 2 / SIANG (DZUHUR / ISTIRAHAT) -->
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-black text-slate-900 uppercase">SESI 2 / SIANG (DZUHUR)</h4>
                            <p class="text-[11px] text-slate-500">
                                Waktu: <span x-text="sessionSettings.afternoon_open + ' - ' + sessionSettings.afternoon_close">12:30 - 13:30</span> <span x-text="timezoneLabel">WITA</span>
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 font-extrabold text-[10px]" x-text="'Buka ' + sessionSettings.afternoon_open">
                            Buka 12:30
                        </span>
                    </div>

                    <template x-if="!hasAttendedAfternoon">
                        <div>
                            <template x-if="isAfternoonOpen()">
                                <button @click="submitAttendance('afternoon')"
                                        :disabled="isSubmitting || (attendanceMode === 'reguler' && !inRadius)"
                                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold text-xs shadow-md active:scale-98 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
                                    <span x-show="!isSubmitting">👉 PRESENSI SIANG (DZUHUR) SEKARANG</span>
                                    <span x-show="isSubmitting" class="animate-spin">⏳</span>
                                </button>
                            </template>
                            <template x-if="!isAfternoonOpen()">
                                <div class="w-full py-3 px-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center text-xs font-bold text-slate-400 flex items-center justify-center gap-2">
                                    <span>🔒</span>
                                    <span>BELUM DIBUKA (Jadwal: <span x-text="sessionSettings.afternoon_open + ' - ' + sessionSettings.afternoon_close">12:30 - 13:30</span> <span x-text="timezoneLabel">WITA</span>)</span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="hasAttendedAfternoon">
                        <div class="w-full py-3 px-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-extrabold text-xs flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="text-emerald-600 text-base">✅</span>
                                <span>SUDAH PRESENSI SESI SIANG</span>
                            </span>
                            <span class="font-mono text-[11px]">TERVERIFIKASI</span>
                        </div>
                    </template>
                </div>

                <!-- SESI 3 / SORE (PRESENSI PULANG) -->
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-black text-slate-900 uppercase">SESI 3 / SORE (PULANG)</h4>
                            <p class="text-[11px] text-slate-500">
                                Waktu: <span x-text="sessionSettings.evening_open + ' s.d ' + sessionSettings.evening_close">16:00 s.d 23:59</span> <span x-text="timezoneLabel">WITA</span>
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-extrabold text-[10px]" x-text="'Buka ' + sessionSettings.evening_open">
                            Buka 16:00
                        </span>
                    </div>

                    <template x-if="!hasCheckedOut">
                        <div>
                            <template x-if="isEveningOpen()">
                                <button @click="submitAttendance('check_out')"
                                        :disabled="isSubmitting || !hasCheckedIn || (attendanceMode === 'reguler' && !inRadius)"
                                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-xs shadow-md active:scale-98 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:pointer-events-none cursor-pointer">
                                    <span x-show="!isSubmitting">👉 PRESENSI PULANG SEKARANG (GPS)</span>
                                    <span x-show="isSubmitting" class="animate-spin">⏳</span>
                                </button>
                            </template>
                            <template x-if="!isEveningOpen()">
                                <div class="w-full py-3 px-4 rounded-xl bg-rose-50/60 border border-rose-200/50 text-center text-xs font-bold text-rose-700 flex items-center justify-center gap-2">
                                    <span>🔒</span>
                                    <span>BELUM DIBUKA (Dibuka Pukul <span x-text="sessionSettings.evening_open">16:00</span> <span x-text="timezoneLabel">WITA</span>)</span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="hasCheckedOut">
                        <div class="w-full py-3 px-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-extrabold text-xs flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="text-emerald-600 text-base">✅</span>
                                <span>SUDAH PRESENSI PULANG</span>
                            </span>
                            <span class="font-mono text-[11px]" x-text="checkOutTime"></span>
                        </div>
                    </template>
                </div>

            </div>

            <!-- Tombol Riwayat Log Saya -->
            <button @click="openLogModal = true" class="w-full py-3 px-4 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs border border-indigo-200/80 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>LIHAT RIWAYAT & LOG ABSENSI SAYA</span>
            </button>

            <!-- Alert Toast Feedback -->
            <template x-if="alertMsg">
                <div class="p-3.5 rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-2"
                     :class="alertType === 'success' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'">
                    <span x-text="alertType === 'success' ? '✅' : '⚠️'"></span>
                    <span x-text="alertMsg"></span>
                </div>
            </template>

        </div>


        <!-- ============================================================ -->
        <!-- TAB 4: LAPORAN & AKSES CEPAT (PORTAL ASATIDZAH) -->
        <!-- ============================================================ -->
        <div x-show="activeTab === 'laporan'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 p-4">
            
            <!-- Akses Cepat Grid -->
            <div class="space-y-3">
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Akses Cepat</h3>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Pelajaran & Nilai -->
                    <a href="{{ route('admin.dashboard') }}" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 text-center hover:shadow-md transition-all space-y-2 group">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800">Pelajaran & Nilai</h4>
                    </a>

                    <!-- Kegiatan -->
                    <button @click="activeTab = 'jadwal'" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 text-center hover:shadow-md transition-all space-y-2 group">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800">Kegiatan</h4>
                    </button>

                    <!-- Halaqah Qur'an -->
                    <a href="{{ route('admin.halaqah.index') }}" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 text-center hover:shadow-md transition-all space-y-2 group">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800">Halaqah Qur'an</h4>
                    </a>

                    <!-- Ekskul -->
                    <a href="{{ route('admin.dashboard') }}" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 text-center hover:shadow-md transition-all space-y-2 group">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800">Ekskul</h4>
                    </a>

                    <!-- Izin Syar'i -->
                    <a href="{{ route('admin.dashboard') }}" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 text-center hover:shadow-md transition-all space-y-2 group">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800">Izin Syar'i</h4>
                    </a>

                    <!-- Kritik & Saran -->
                    <a href="{{ route('admin.dashboard') }}" class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 text-center hover:shadow-md transition-all space-y-2 group">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800">Kritik & Saran</h4>
                    </a>
                </div>
            </div>

            <!-- Raport Kinerja (KPI) Bulanan -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-rose-500 text-lg">⭐</span>
                        <div>
                            <h4 class="text-xs font-extrabold text-slate-900">Raport Kinerja (KPI)</h4>
                            <p class="text-[10px] text-slate-400 font-semibold">EVALUASI BULANAN</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">September 2026</span>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block">TOTAL KPI</span>
                        <span class="text-2xl font-black text-blue-700">83%</span>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 block">ABSENSI</span>
                        <span class="text-2xl font-black text-emerald-600" x-text="attendancePercentage + '%'">100%</span>
                    </div>
                </div>

                <button class="w-full py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition-all flex items-center justify-center gap-1.5">
                    <span>LIHAT DETAIL & HISTORI KPI</span>
                    <span>></span>
                </button>
            </div>

            <!-- Navigasi Cepat Portal Asatidzah -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <span>⚙️</span> NAVIGASI CEPAT PORTAL ASATIDZAH
                    </h4>
                </div>

                <div class="space-y-2.5">
                    <!-- Presensi Presisi GPS Satelit -->
                    <button @click="activeTab = 'absen'" class="w-full text-left p-3 rounded-xl hover:bg-slate-50 border border-slate-100 flex items-start gap-3 transition-all">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            📍
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-slate-900">Presensi Presisi GPS Satelit</h5>
                            <p class="text-[11px] text-slate-500">Validasi radius geofence dan koordinat kehadiran sekolah</p>
                        </div>
                    </button>

                    <!-- Skor Halaqah Harian -->
                    <a href="{{ route('admin.halaqah.index') }}" class="w-full text-left p-3 rounded-xl hover:bg-slate-50 border border-slate-100 flex items-start gap-3 transition-all">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-base">
                            📖
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-slate-900">Skor Halaqah Harian</h5>
                            <p class="text-[11px] text-slate-500">Input penilaian harian Tahsin &amp; Tahfidz santri bimbingan</p>
                        </div>
                    </a>

                    <!-- Laporan Progres Santri -->
                    <a href="{{ route('admin.halaqah.index', ['tab' => 'reports']) }}" class="w-full text-left p-3 rounded-xl hover:bg-slate-50 border border-slate-100 flex items-start gap-3 transition-all">
                        <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 text-base">
                            📊
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-slate-900">Laporan Progres &amp; Statistik</h5>
                            <p class="text-[11px] text-slate-500">Pantau statistik sebaran jilid Tahsin dan setoran hafalan santri</p>
                        </div>
                    </a>
                </div>
            </div>

        </div>


        <!-- ============================================================ -->
        <!-- TAB 5: PROFIL (AKUN & STATISTIK GURU) -->
        <!-- ============================================================ -->
        <div x-show="activeTab === 'profil'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
            
            <!-- Hero Gradient Profile Card -->
            <div class="rounded-b-3xl bg-gradient-to-b from-blue-700 via-blue-600 to-indigo-700 p-6 text-white text-center shadow-xl space-y-3 relative overflow-hidden">
                <div class="w-20 h-20 mx-auto rounded-full bg-white/20 backdrop-blur-md border-2 border-white flex items-center justify-center text-2xl font-black shadow-lg">
                    {{ strtoupper(substr($user->name ?? 'A', 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-lg font-black leading-tight">{{ $user->name ?? 'Akun Guru' }}</h3>
                    <p class="text-xs text-blue-200 font-medium mt-0.5">{{ $user->email ?? 'asatidz@sekolah.sch.id' }}</p>
                </div>

                <!-- Badges -->
                <div class="flex items-center justify-center gap-2 pt-1 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-[10px] font-extrabold text-white border border-white/20 uppercase tracking-wider">
                        GURU KELAS
                    </span>
                    <span class="px-3 py-1 rounded-full bg-emerald-400/20 backdrop-blur-md text-[10px] font-extrabold text-emerald-200 border border-emerald-400/30 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        STATUS AKTIF
                    </span>
                </div>

                <div class="pt-2">
                    <a href="{{ route('admin.profile') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur-md text-xs font-bold text-white border border-white/20 transition-all">
                        <span>✏️</span> Edit Biodata & Profil Akun
                    </a>
                </div>
            </div>

            <div class="p-4 space-y-4">
                
                <!-- Quick Badge Trio Bar -->
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-white rounded-2xl p-3 border border-slate-200/80 shadow-sm">
                        <div class="text-blue-600 text-base mb-1">🛡️</div>
                        <span class="text-[10px] font-bold text-slate-700 block">Terverifikasi</span>
                        <span class="text-[9px] text-slate-400">Sistem Sekolah</span>
                    </div>
                    <div class="bg-white rounded-2xl p-3 border border-slate-200/80 shadow-sm">
                        <div class="text-indigo-600 text-base mb-1">👤</div>
                        <span class="text-[10px] font-bold text-slate-700 block">Guru Kelas</span>
                        <span class="text-[9px] text-slate-400">Tahun Ajaran Aktif</span>
                    </div>
                    <div class="bg-white rounded-2xl p-3 border border-slate-200/80 shadow-sm">
                        <div class="text-cyan-600 text-base mb-1">🏛️</div>
                        <span class="text-[10px] font-bold text-slate-700 block truncate">{{ $schoolName ?? 'SDIT AL-FAHMI' }}</span>
                        <span class="text-[9px] text-slate-400">Palu</span>
                    </div>
                </div>

                <!-- Laporan Statistik Kehadiran -->
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-500 font-bold">📈</span>
                            <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wide">Laporan Statistik Kehadiran</h4>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="bg-slate-100 px-3 py-1.5 rounded-lg text-slate-700 font-bold">September 2026 ▾</span>
                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full text-xs font-black" x-text="attendancePercentage + '% Kehadiran'">100% Kehadiran</span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-500">
                            <span>Persentase Kehadiran</span>
                            <span class="text-emerald-600 font-black" x-text="attendancePercentage + '%'">100%</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" :style="`width: ${attendancePercentage}%`"></div>
                        </div>
                    </div>

                    <!-- 4 Grid Stat Cards -->
                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                        <!-- Tepat Waktu -->
                        <div class="bg-emerald-50/80 rounded-xl p-3 border border-emerald-100">
                            <span class="text-[10px] font-bold text-emerald-800 uppercase block">TEPAT WAKTU</span>
                            <span class="text-xl font-black text-emerald-700">{{ $onTimeCount }} <span class="text-xs font-semibold">Hari</span></span>
                        </div>
                        <!-- Terlambat -->
                        <div class="bg-amber-50/80 rounded-xl p-3 border border-amber-100">
                            <span class="text-[10px] font-bold text-amber-800 uppercase block">TERLAMBAT</span>
                            <span class="text-xl font-black text-amber-700">{{ $lateCount }} <span class="text-xs font-semibold">Hari</span></span>
                        </div>
                        <!-- Izin / Sakit / Cuti -->
                        <div class="bg-cyan-50/80 rounded-xl p-3 border border-cyan-100">
                            <span class="text-[10px] font-bold text-cyan-800 uppercase block">IZIN / SAKIT / CUTI</span>
                            <span class="text-xl font-black text-cyan-700">{{ $permitCount }} <span class="text-xs font-semibold">Hari</span></span>
                        </div>
                        <!-- Total Hari Evaluasi -->
                        <div class="bg-indigo-50/80 rounded-xl p-3 border border-indigo-100">
                            <span class="text-[10px] font-bold text-indigo-800 uppercase block">TOTAL HARI EVALUASI</span>
                            <span class="text-xl font-black text-indigo-700">{{ $totalDays }} <span class="text-xs font-semibold">Hari</span></span>
                        </div>
                    </div>

                    <!-- Button Log Absensi -->
                    <button @click="openLogModal = true" class="w-full py-2.5 px-4 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white font-extrabold text-xs transition-all flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span>🕒</span>
                            <span>Log Absensi ({{ $totalDays }} Catatan)</span>
                        </span>
                        <span>Buka Log Absensi ></span>
                    </button>
                </div>

                <!-- Informasi Biodata & Peran -->
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                            <span>👤</span> INFORMASI BIODATA & PERAN
                        </h4>
                        <a href="{{ route('admin.profile') }}" class="text-blue-600 font-bold text-xs hover:underline">Edit Biodata</a>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400">🛡️</span>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">NIP / NRH PEGAWAI</p>
                                <p class="font-extrabold text-slate-900 font-mono">{{ $user->nip ?? '102917391' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400">📱</span>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">NO. WHATSAPP / HP</p>
                                <p class="font-bold text-slate-700">{{ $user->phone ?? '0812-3456-7890' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400">👤</span>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">JENIS KELAMIN</p>
                                <p class="font-bold text-slate-700">Laki-laki / Perempuan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400">🔑</span>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">USERNAME LOGIN</p>
                                <p class="font-mono text-slate-700 font-bold">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Utilities Actions -->
                <div class="space-y-2">
                    <!-- Install PWA -->
                    <button @click="installPwa()" class="w-full p-3.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-extrabold text-xs shadow-sm flex items-center justify-between hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">📲</span>
                            <div class="text-left">
                                <p class="font-bold leading-snug">Pasang Aplikasi (PWA)</p>
                                <p class="text-[10px] text-blue-100 font-normal">Instal ke Layar Utama HP / PC</p>
                            </div>
                        </div>
                        <span>></span>
                    </button>

                    <!-- Notifikasi -->
                    <button @click="openNotificationModal = true" class="w-full p-3.5 rounded-2xl bg-white border border-slate-200/80 text-slate-800 font-bold text-xs shadow-sm flex items-center justify-between hover:bg-slate-50 transition-all">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">🔔</span>
                            <div class="text-left">
                                <p class="font-extrabold leading-snug">Pusat Notifikasi</p>
                                <p class="text-[10px] text-slate-400 font-normal">Riwayat Alarm & Peringatan</p>
                            </div>
                        </div>
                        <span>></span>
                    </button>

                    <!-- Keluar dari Portal Akun -->
                    <form action="{{ route('logout') }}" method="POST" class="pt-2">
                        @csrf
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar dari Portal Akun?')" class="w-full py-3.5 px-4 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-md transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>KELUAR DARI PORTAL AKUN</span>
                        </button>
                    </form>
                </div>

            </div>

        </div>

        <!-- ============================================================ -->
        <!-- FOOTER BRANDING SEKOLAH -->
        <!-- ============================================================ -->
        <div class="text-center py-6 px-4 space-y-1">
            <h5 class="text-xs font-black text-slate-700">{{ $schoolName ?? 'Sistem Manajemen Informasi SDIT AL-FAHMI PALU' }}</h5>
            <p class="text-[11px] text-slate-400 italic">{{ $schoolMotto ?? 'Sekolahnya Calon Pemimpin Peradaban' }}</p>
            <div class="flex items-center justify-center gap-2 pt-1 text-[10px] font-bold text-slate-400">
                <span>© 2026 {{ $schoolName ?? 'SDIT AL-FAHMI PALU' }}</span>
                <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 text-[9px]">PALU</span>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- FLOATING BOTTOM NAVIGATION BAR -->
        <!-- ============================================================ -->
        <div class="fixed bottom-3 left-0 right-0 max-w-md mx-auto px-4 z-40">
            <nav class="bg-white/95 backdrop-blur-xl rounded-full border border-slate-200/90 shadow-2xl px-3 py-2 flex items-center justify-between">
                
                <!-- Tab Beranda -->
                <button @click="activeTab = 'beranda'"
                        :class="activeTab === 'beranda' ? 'text-primary font-extrabold' : 'text-slate-400 hover:text-slate-600 font-semibold'"
                        class="flex-1 flex flex-col items-center gap-1 transition-all py-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-[10px]">Beranda</span>
                </button>

                <!-- Menu Tugas & Checklist -->
                <a href="{{ route('admin.employee-tasks.index') }}"
                   class="flex-1 flex flex-col items-center gap-1 transition-all py-1 text-slate-400 hover:text-primary font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="text-[10px]">Tugas</span>
                </a>

                <!-- Prominent Center Floating FAB Button: ABSEN -->
                <div class="relative -top-5 flex flex-col items-center">
                    <button @click="activeTab = 'absen'"
                            :class="activeTab === 'absen' ? 'ring-4 ring-primary/30 scale-105' : ''"
                            class="w-14 h-14 rounded-full bg-gradient-to-tr from-[#3C50E0] via-primary to-indigo-600 text-white flex items-center justify-center glow-fab active:scale-95 transition-all shadow-lg border-2 border-white">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                    <span class="text-[10px] font-black mt-1" :class="activeTab === 'absen' ? 'text-primary' : 'text-slate-500'">Absen</span>
                </div>

                <!-- Menu Halaqah Qur'an -->
                <a href="{{ route('admin.halaqah.index') }}"
                   class="flex-1 flex flex-col items-center gap-1 transition-all py-1 text-slate-400 hover:text-primary font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="text-[10px]">Halaqah</span>
                </a>

                <!-- Menu Profil Akun -->
                <a href="{{ route('admin.profile') }}"
                   class="flex-1 flex flex-col items-center gap-1 transition-all py-1 text-slate-400 hover:text-primary font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-[10px]">Profil</span>
                </a>

            </nav>
        </div>

        <!-- ============================================================ -->
        <!-- MODAL 1: RIWAYAT & LOG ABSENSI SAYA -->
        <!-- ============================================================ -->
        <div x-show="openLogModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="display: none;">
            
            <div @click.away="openLogModal = false"
                 class="bg-white w-full max-w-md rounded-t-3xl sm:rounded-3xl p-5 space-y-4 max-h-[85vh] flex flex-col shadow-2xl">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <span>📋</span> Log Riwayat Presensi Mandiri
                    </h3>
                    <button @click="openLogModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
                </div>

                <div class="overflow-y-auto space-y-2.5 flex-1 pr-1">
                    @forelse($recentLogs as $log)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 block">{{ $log->date }}</span>
                            <span class="text-xs font-bold text-slate-800">
                                Masuk: {{ $log->check_in ?? '-' }} | Pulang: {{ $log->check_out ?? '-' }}
                            </span>
                            <span class="text-[10px] text-slate-500 block">Via {{ $log->method_label ?? 'Mobile GPS' }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-black {{ $log->status === 'present' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $log->status_label }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-8 text-xs text-slate-400">
                        Belum ada riwayat catatan absensi
                    </div>
                    @endforelse
                </div>

                <button @click="openLogModal = false" class="w-full py-3 rounded-xl bg-slate-100 font-bold text-xs text-slate-700 hover:bg-slate-200 transition-all">
                    Tutup Riwayat
                </button>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODAL 2: NOTIFIKASI PUSAT -->
        <!-- ============================================================ -->
        <div x-show="openNotificationModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="display: none;">
            
            <div @click.away="openNotificationModal = false"
                 class="bg-white w-full max-w-md rounded-t-3xl sm:rounded-3xl p-5 space-y-4 max-h-[85vh] flex flex-col shadow-2xl">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <span>🔔</span> Pusat Notifikasi & Pengingat
                    </h3>
                    <button @click="openNotificationModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
                </div>

                <div class="space-y-3 overflow-y-auto flex-1 pr-1">
                    <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200/60 space-y-1">
                        <span class="text-[10px] font-black text-blue-600 uppercase">PENGINGAT PRESENSI PAGI</span>
                        <h4 class="text-xs font-bold text-slate-900">Jadwal Sesi 1 Buka Pukul 06:00 WITA</h4>
                        <p class="text-[11px] text-slate-600">Pastikan GPS smartphone aktif saat tiba di radius resmi sekolah untuk presensi masuk.</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/60 space-y-1">
                        <span class="text-[10px] font-black text-emerald-600 uppercase">INFORMASI SISTEM</span>
                        <h4 class="text-xs font-bold text-slate-900">PWA Portal Asatidzah Telah Siap</h4>
                        <p class="text-[11px] text-slate-600">Anda dapat memasang aplikasi langsung ke layar utama smartphone untuk akses cepat satu klik.</p>
                    </div>
                </div>

                <button @click="openNotificationModal = false" class="w-full py-3 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition-all">
                    Saya Mengerti
                </button>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODAL 3: KONTROL SESI & LIVE BRIEFING KEPALA SEKOLAH -->
        <!-- ============================================================ -->
        @if($isPrincipal)
        <div x-show="openPrincipalModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4"
             style="display: none;">
            
            <div @click.away="openPrincipalModal = false"
                 class="bg-white w-full max-w-md rounded-t-3xl sm:rounded-3xl p-5 space-y-4 max-h-[90vh] flex flex-col shadow-2xl">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold">👑</span>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Kontrol Sesi & Briefing</h3>
                            <p class="text-[10px] text-slate-500">Panel Khusus Kepala Sekolah & Admin</p>
                        </div>
                    </div>
                    <button @click="openPrincipalModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
                </div>

                <!-- Sub-tab switcher inside modal -->
                <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1 rounded-xl text-xs font-bold" x-data="{ principalTab: 'briefing' }">
                    <button @click="principalTab = 'briefing'"
                            :class="principalTab === 'briefing' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600'"
                            class="py-2 rounded-lg transition-all flex items-center justify-center gap-1.5">
                        <span>📢</span>
                        <span>Sesi Briefing</span>
                    </button>
                    <button @click="principalTab = 'sesi'"
                            :class="principalTab === 'sesi' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600'"
                            class="py-2 rounded-lg transition-all flex items-center justify-center gap-1.5">
                        <span>⏰</span>
                        <span>Jam Presensi</span>
                    </button>

                    <!-- TAB A: KONTROL SESI BRIEFING -->
                    <div x-show="principalTab === 'briefing'" class="col-span-2 space-y-3 pt-2">
                        <!-- Toggle Sesi Status -->
                        <div class="p-3 rounded-xl border flex items-center justify-between"
                             :class="briefingSession.active ? 'bg-emerald-50/80 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider block"
                                      :class="briefingSession.active ? 'text-emerald-700' : 'text-slate-500'">Status Sesi Briefing</span>
                                <span class="text-xs font-extrabold"
                                      :class="briefingSession.active ? 'text-emerald-800' : 'text-slate-700'"
                                      x-text="briefingSession.active ? '🟢 SEDANG AKTIF / DIBUKA' : '🔴 SEDANG TUTUP / NONAKTIF'"></span>
                            </div>
                            <button type="button"
                                    @click="toggleBriefingSession(!briefingSession.active)"
                                    :disabled="briefingForm.isSaving"
                                    :class="briefingSession.active ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white'"
                                    class="py-2 px-3.5 rounded-xl font-extrabold text-xs shadow-sm transition-all active:scale-95 disabled:opacity-50">
                                <span x-text="briefingSession.active ? 'Tutup Sesi' : 'Buka Sesi Live'"></span>
                            </button>
                        </div>

                        <!-- Judul Briefing Input -->
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-slate-700 block">Judul / Topik Briefing</label>
                            <input type="text" x-model="briefingForm.title"
                                   placeholder="Contoh: Briefing Pagi Kedisiplinan & KBM Santri"
                                   class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none">
                        </div>

                        <!-- Isi Briefing Input -->
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-slate-700 block">Isi Arahan / Ringkasan Briefing</label>
                            <textarea x-model="briefingForm.content" rows="3"
                                      placeholder="Tuliskan poin-poin penting arahan kepala sekolah kepada seluruh asatidzah..."
                                      class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none"></textarea>
                        </div>

                        <!-- Simpan & Update Briefing -->
                        <button type="button" @click="saveBriefingContent()"
                                :disabled="briefingForm.isSaving"
                                class="w-full py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white font-extrabold text-xs shadow-md active:scale-98 transition-all flex items-center justify-center gap-1.5 disabled:opacity-50">
                            <span x-show="!briefingForm.isSaving">💾 Simpan Perubahan Briefing</span>
                            <span x-show="briefingForm.isSaving" class="animate-spin">⏳</span>
                        </button>
                    </div>

                    <!-- TAB B: PENGATURAN JAM SESI PRESENSI -->
                    <div x-show="principalTab === 'sesi'" class="col-span-2 space-y-3 pt-2 max-h-[55vh] overflow-y-auto pr-1">
                        
                        <!-- Manual Override Toggle -->
                        <div class="p-3 rounded-xl bg-amber-50/90 border border-amber-200 space-y-1.5">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="sessionsForm.manual_override" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-400">
                                <span class="text-xs font-black text-amber-900">Buka Paksa Semua Sesi (Override)</span>
                            </label>
                            <p class="text-[10px] text-amber-800 leading-snug">
                                Jika dicentang, semua tombol presensi (Pagi, Siang, Pulang) dapat langsung ditekan tanpa dibatasi jam buka/tutup.
                            </p>
                        </div>

                        <!-- Sesi Pagi -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                <span>🌅</span> SESI 1 (PAGI / MASUK)
                            </span>
                            <div class="grid grid-cols-3 gap-2 text-[11px]">
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold">Jam Buka</span>
                                    <input type="time" x-model="sessionsForm.morning_open" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-xs font-mono font-bold">
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold text-rose-600">Batas Telat</span>
                                    <input type="time" x-model="sessionsForm.morning_late" class="w-full px-2 py-1.5 rounded-lg border border-rose-300 text-xs font-mono font-bold text-rose-700">
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold">Jam Tutup</span>
                                    <input type="time" x-model="sessionsForm.morning_close" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-xs font-mono font-bold">
                                </div>
                            </div>
                        </div>

                        <!-- Sesi Siang -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                <span>☀️</span> SESI 2 (SIANG / DZUHUR)
                            </span>
                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold">Jam Buka</span>
                                    <input type="time" x-model="sessionsForm.afternoon_open" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-xs font-mono font-bold">
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold">Jam Tutup</span>
                                    <input type="time" x-model="sessionsForm.afternoon_close" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-xs font-mono font-bold">
                                </div>
                            </div>
                        </div>

                        <!-- Sesi Pulang -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                <span>🌇</span> SESI 3 (SORE / PULANG)
                            </span>
                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold">Jam Buka</span>
                                    <input type="time" x-model="sessionsForm.evening_open" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-xs font-mono font-bold">
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold">Jam Tutup</span>
                                    <input type="time" x-model="sessionsForm.evening_close" class="w-full px-2 py-1.5 rounded-lg border border-slate-300 text-xs font-mono font-bold">
                                </div>
                            </div>
                        </div>

                        <!-- Simpan Jam Sesi -->
                        <button type="button" @click="saveSessionTimes()"
                                :disabled="sessionsForm.isSaving"
                                class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md active:scale-98 transition-all flex items-center justify-center gap-1.5 disabled:opacity-50">
                            <span x-show="!sessionsForm.isSaving">💾 Simpan Jadwal Jam Sesi</span>
                            <span x-show="sessionsForm.isSaving" class="animate-spin">⏳</span>
                        </button>
                    </div>
                </div>

                <button @click="openPrincipalModal = false" class="w-full py-2.5 rounded-xl bg-slate-100 font-bold text-xs text-slate-700 hover:bg-slate-200 transition-all">
                    Tutup Panel
                </button>
            </div>
        </div>
        @endif

    </div>

    <!-- Application Script Logic -->
    <script>
        window.mobilePortalApp = function() {
            return {
                // Tab state: 'beranda' | 'jadwal' | 'absen' | 'laporan' | 'profil'
                activeTab: 'beranda',

                // Modals
                openLogModal: false,
                openNotificationModal: false,
                openPrincipalModal: false,

                // School coordinates & config
                schoolLat: {{ (float) $schoolLat }},
                schoolLong: {{ (float) $schoolLong }},
                schoolRadius: {{ (int) $schoolRadius }},
                timezoneLabel: '{{ $timezoneLabel ?? "WITA" }}',
                attendancePercentage: {{ $attendancePercentage }},

                // Real-time clock & dates
                currentTime: '',
                currentDateFormatted: '',
                hijriDate: '',
                timeGreeting: '',
                calendarMonthLabel: 'SEPTEMBER 2026',
                selectedDate: {{ (int) date('j') }},
                agendaFilter: 'SEMUA',

                // GPS & Location states
                userLat: null,
                userLong: null,
                distanceMeters: null,
                inRadius: false,
                gpsLoading: true,
                gpsError: '',
                gpsBypass: false,

                // Attendance Mode: 'reguler' vs 'dinas_luar'
                attendanceMode: 'reguler',
                dinasNotes: '',

                // Attendance status (Database synchronization)
                hasCheckedIn: {{ !empty($todayAttendance?->check_in) ? 'true' : 'false' }},
                checkInTime: '{{ $todayAttendance?->check_in ?? "" }}',
                checkInMethod: '{{ $todayAttendance?->method_label ?? "Mobile GPS" }}',
                hasCheckedOut: {{ !empty($todayAttendance?->check_out) ? 'true' : 'false' }},
                checkOutTime: '{{ $todayAttendance?->check_out ?? "" }}',

                // Sesi Briefing & Siang Attendance states
                hasAttendedBriefing: {{ $hasAttendedBriefing ? 'true' : 'false' }},
                hasAttendedAfternoon: {{ $hasAttendedAfternoon ? 'true' : 'false' }},

                // Sesi Settings & Briefing Settings from Server
                sessionSettings: @json($sessionSettings),
                briefingSession: @json($briefingSession),
                isPrincipal: {{ $isPrincipal ? 'true' : 'false' }},

                // Principal Form models
                briefingForm: {
                    title: '{{ addslashes($briefingSession['title'] ?? '') }}',
                    content: '{{ addslashes($briefingSession['content'] ?? '') }}',
                    isSaving: false
                },
                sessionsForm: {
                    morning_open: '{{ $sessionSettings['morning_open'] ?? '06:00' }}',
                    morning_late: '{{ $sessionSettings['morning_late'] ?? '07:30' }}',
                    morning_close: '{{ $sessionSettings['morning_close'] ?? '11:59' }}',
                    afternoon_open: '{{ $sessionSettings['afternoon_open'] ?? '12:30' }}',
                    afternoon_close: '{{ $sessionSettings['afternoon_close'] ?? '13:30' }}',
                    evening_open: '{{ $sessionSettings['evening_open'] ?? '16:00' }}',
                    evening_close: '{{ $sessionSettings['evening_close'] ?? '23:59' }}',
                    manual_override: {{ $sessionSettings['manual_override'] ? 'true' : 'false' }},
                    isSaving: false
                },

                // Submission state
                isSubmitting: false,
                alertMsg: '',
                alertType: '',

                init() {
                    this.updateClockAndGreetings();
                    setInterval(() => this.updateClockAndGreetings(), 1000);
                    this.detectGps();

                    // Polling sinkronisasi status setiap 10 detik
                    setInterval(() => {
                        this.pollStatus();
                    }, 10000);
                },

                updateClockAndGreetings() {
                    const now = new Date();

                    // Clock format: HH.mm.ss
                    const h = String(now.getHours()).padStart(2, '0');
                    const m = String(now.getMinutes()).padStart(2, '0');
                    const s = String(now.getSeconds()).padStart(2, '0');
                    this.currentTime = `${h}.${m}.${s}`;

                    // Indonesian formatted date
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    this.currentDateFormatted = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
                    this.calendarMonthLabel = `${months[now.getMonth()].toUpperCase()} ${now.getFullYear()}`;

                    // Dynamic Islamic greeting based on time of day
                    const hour = now.getHours();
                    if (hour >= 3 && hour < 6) {
                        this.timeGreeting = "Assalamu'alaikum, Shobahul Khair (Selamat Subuh)";
                    } else if (hour >= 6 && hour < 11) {
                        this.timeGreeting = "Assalamu'alaikum, Shobahul Khair (Selamat Pagi)";
                    } else if (hour >= 11 && hour < 15) {
                        this.timeGreeting = "Assalamu'alaikum, Naharukum Sa'id (Selamat Siang)";
                    } else if (hour >= 15 && hour < 18) {
                        this.timeGreeting = "Assalamu'alaikum, Masa'ul Khair (Selamat Sore)";
                    } else {
                        this.timeGreeting = "Assalamu'alaikum, Lailatakum Sa'idah (Selamat Malam)";
                    }

                    // Compute Hijri Date dynamically
                    try {
                        const formatter = new Intl.DateTimeFormat('id-TN-u-ca-islamic-umalqura', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                        this.hijriDate = formatter.format(now) + ' H';
                    } catch (e) {
                        this.hijriDate = '8 Rabiul Akhir 1448 H';
                    }
                },

                parseTimeToMinutes(timeStr) {
                    if (!timeStr || typeof timeStr !== 'string' || !timeStr.includes(':')) return 0;
                    const parts = timeStr.split(':');
                    return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
                },

                isTimeInRange(openStr, closeStr) {
                    if (this.sessionSettings && this.sessionSettings.manual_override) return true;
                    const now = new Date();
                    const currentMinutes = now.getHours() * 60 + now.getMinutes();
                    const openMin = this.parseTimeToMinutes(openStr);
                    const closeMin = this.parseTimeToMinutes(closeStr);
                    return currentMinutes >= openMin && currentMinutes <= closeMin;
                },

                isMorningOpen() {
                    return this.isTimeInRange(this.sessionSettings.morning_open, this.sessionSettings.morning_close);
                },

                isAfternoonOpen() {
                    return this.isTimeInRange(this.sessionSettings.afternoon_open, this.sessionSettings.afternoon_close);
                },

                isEveningOpen() {
                    return this.isTimeInRange(this.sessionSettings.evening_open, this.sessionSettings.evening_close);
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
                                this.gpsError = 'Izin akses GPS ditolak! Aktifkan izin lokasi HP Anda untuk absensi presisi.';
                            } else {
                                this.gpsError = 'Gagal mendeteksi koordinat lokasi GPS. Pastikan GPS aktif.';
                            }
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                },

                enableGpsFallback() {
                    this.userLat = this.schoolLat;
                    this.userLong = this.schoolLong;
                    this.distanceMeters = 0;
                    this.inRadius = true;
                    this.gpsBypass = true;
                    this.gpsError = '';
                    this.showAlert('success', 'Lokasi acuan sekolah terpasang sebagai fallback darurat.');
                },

                calculateDistance() {
                    if (!this.userLat || !this.userLong) return;

                    const R = 6371000; // Radius Bumi dalam meter
                    const dLat = (this.schoolLat - this.userLat) * Math.PI / 180;
                    const dLon = (this.schoolLong - this.userLong) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                              Math.cos(this.userLat * Math.PI / 180) * Math.cos(this.schoolLat * Math.PI / 180) *
                              Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    this.distanceMeters = Math.round(R * c);
                    this.inRadius = this.distanceMeters <= this.schoolRadius;
                },

                async pollStatus() {
                    try {
                        const res = await fetch('{{ route('admin.teacher-attendances.check-status') }}', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();

                        if (data.briefing_active !== undefined) {
                            this.briefingSession.active = data.briefing_active;
                        }
                        if (data.briefing_title) {
                            this.briefingSession.title = data.briefing_title;
                        }

                        if (data.has_record) {
                            if (!this.hasCheckedIn && data.has_checked_in) {
                                this.hasCheckedIn = true;
                                this.checkInTime = data.check_in_time;
                                this.checkInMethod = data.method_label;
                                this.showAlert('success', `Tersinkronisasi! Presensi Masuk tercatat (${data.check_in_time}).`);
                            }
                            if (!this.hasCheckedOut && data.has_checked_out) {
                                this.hasCheckedOut = true;
                                this.checkOutTime = data.check_out_time;
                                this.showAlert('success', `Tersinkronisasi! Presensi Pulang tercatat (${data.check_out_time}).`);
                            }
                            if (data.has_attended_briefing !== undefined) {
                                this.hasAttendedBriefing = data.has_attended_briefing;
                            }
                            if (data.has_attended_afternoon !== undefined) {
                                this.hasAttendedAfternoon = data.has_attended_afternoon;
                            }
                        }
                    } catch (e) {
                        // Ignore background polling errors
                    }
                },

                async submitAttendance(type) {
                    if (type === 'check_in' && this.hasCheckedIn) {
                        this.showAlert('error', 'Anda sudah melakukan presensi masuk hari ini!');
                        return;
                    }
                    if (type === 'check_out' && this.hasCheckedOut) {
                        this.showAlert('error', 'Anda sudah melakukan presensi pulang hari ini!');
                        return;
                    }
                    if (type === 'afternoon' && this.hasAttendedAfternoon) {
                        this.showAlert('error', 'Anda sudah melakukan presensi siang hari ini!');
                        return;
                    }
                    if (type === 'briefing' && this.hasAttendedBriefing) {
                        this.showAlert('error', 'Anda sudah mencatat kehadiran briefing hari ini!');
                        return;
                    }

                    // Check radius only for regular attendance if not bypassed or overridden
                    if (this.attendanceMode === 'reguler' && !this.inRadius && !this.gpsBypass && !this.sessionSettings?.manual_override) {
                        this.showAlert('error', `Lokasi di luar radius! Anda berjarak ${this.distanceMeters}m (Batas resmi: ${this.schoolRadius}m). Dekati gerbang sekolah atau pilih moda dinas luar.`);
                        return;
                    }

                    this.isSubmitting = true;
                    this.alertMsg = '';

                    const payload = {
                        type: type,
                        latitude: this.userLat || this.schoolLat,
                        longitude: this.userLong || this.schoolLong,
                        work_location: this.attendanceMode === 'dinas_luar' ? 'dinas_luar' : 'school',
                        notes: this.attendanceMode === 'dinas_luar' ? (this.dinasNotes || 'Dinas / Tugas Luar') : null
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

                        if (res.ok && data.success) {
                            if (type === 'check_in') {
                                this.hasCheckedIn = true;
                                this.checkInTime = data.attendance.check_in;
                                this.showAlert('success', 'Alhamdulillah! Presensi Masuk Berhasil Tercatat.');
                            } else if (type === 'check_out') {
                                this.hasCheckedOut = true;
                                this.checkOutTime = data.attendance.check_out;
                                this.showAlert('success', 'Alhamdulillah! Presensi Pulang Berhasil Tercatat.');
                            } else if (type === 'afternoon') {
                                this.hasAttendedAfternoon = true;
                                this.showAlert('success', 'Alhamdulillah! Presensi Sesi Siang Berhasil Dicatat.');
                            } else if (type === 'briefing') {
                                this.hasAttendedBriefing = true;
                                if (!this.hasCheckedIn && data.attendance && data.attendance.check_in) {
                                    this.hasCheckedIn = true;
                                    this.checkInTime = data.attendance.check_in;
                                }
                                this.showAlert('success', data.message || 'Alhamdulillah! Kehadiran Briefing Berhasil Dicatat.');
                            }
                        } else {
                            this.showAlert('error', data.message || 'Gagal melakukan presensi. Silakan coba lagi.');
                        }
                    } catch (e) {
                        this.showAlert('error', 'Terjadi kesalahan jaringan atau server. Pastikan koneksi internet stabil.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                submitBriefingAttendance() {
                    this.submitAttendance('briefing');
                },

                async toggleBriefingSession(activeState) {
                    this.briefingForm.isSaving = true;
                    try {
                        const res = await fetch('{{ route('admin.teacher-attendances.toggle-briefing') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                active: activeState ? 1 : 0,
                                title: this.briefingForm.title,
                                content: this.briefingForm.content
                            })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.briefingSession = data.briefing;
                            this.showAlert('success', data.message);
                        } else {
                            this.showAlert('error', data.message || 'Gagal mengubah status sesi briefing.');
                        }
                    } catch (err) {
                        this.showAlert('error', 'Gagal menghubungi server.');
                    } finally {
                        this.briefingForm.isSaving = false;
                    }
                },

                async saveBriefingContent() {
                    this.toggleBriefingSession(this.briefingSession.active);
                },

                async saveSessionTimes() {
                    this.sessionsForm.isSaving = true;
                    try {
                        const res = await fetch('{{ route('admin.teacher-attendances.update-session-times') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                morning_open: this.sessionsForm.morning_open,
                                morning_late: this.sessionsForm.morning_late,
                                morning_close: this.sessionsForm.morning_close,
                                afternoon_open: this.sessionsForm.afternoon_open,
                                afternoon_close: this.sessionsForm.afternoon_close,
                                evening_open: this.sessionsForm.evening_open,
                                evening_close: this.sessionsForm.evening_close,
                                manual_override: this.sessionsForm.manual_override ? 1 : 0,
                            })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.sessionSettings = data.sessions;
                            this.showAlert('success', data.message);
                            this.openPrincipalModal = false;
                        } else {
                            this.showAlert('error', data.message || 'Gagal menyimpan pengaturan sesi.');
                        }
                    } catch (err) {
                        this.showAlert('error', 'Gagal menghubungi server.');
                    } finally {
                        this.sessionsForm.isSaving = false;
                    }
                },

                showAlert(type, msg) {
                    this.alertType = type;
                    this.alertMsg = msg;
                    setTimeout(() => {
                        this.alertMsg = '';
                    }, 5000);
                },

                installPwa() {
                    alert('Untuk menginstal aplikasi ini ke layar utama smartphone:\n1. Buka menu browser (ikon titik tiga / tombol bagikan).\n2. Pilih "Tambahkan ke Layar Utama" / "Add to Home Screen".');
                }
            }
        };

        // Alpine Initialization Register
        document.addEventListener('alpine:init', () => {
            Alpine.data('mobilePortalApp', window.mobilePortalApp);
        });
        if (window.Alpine) {
            Alpine.data('mobilePortalApp', window.mobilePortalApp);
        }
    </script>
</body>
</html>
