@php
    $primaryColor = Setting::get('primary_color', '#3C50E0');
    $secondaryColor = Setting::get('secondary_color', '#2563eb');
    $schoolName = Setting::get('school_name', config('app.name', 'SDIT AL-FAHMI PALU'));
    $timezoneLabel = Setting::get('school_timezone_label', 'WITA');

    $hour = (int) now()->hour;
    if ($hour >= 4 && $hour < 11) {
        $timeGreeting = 'Selamat Pagi';
        $timeIcon = '🌅';
    } elseif ($hour >= 11 && $hour < 15) {
        $timeGreeting = 'Selamat Siang';
        $timeIcon = '☀️';
    } elseif ($hour >= 15 && $hour < 18) {
        $timeGreeting = 'Selamat Sore';
        $timeIcon = '🌇';
    } else {
        $timeGreeting = 'Selamat Malam';
        $timeIcon = '🌙';
    }

    $isTeacherOrStaff = auth()->user()->hasRole('guru') || auth()->user()->hasRole('staff') || auth()->user()->hasRole('kepala-sekolah');
@endphp

<!-- TailAdmin Unified Global Adaptive Hero Banner -->
<div class="relative overflow-hidden rounded-3xl text-white shadow-xl p-6 sm:p-8 transition-all duration-300 border border-white/10"
     style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);">

    <!-- Ambient Glowing Blurs (Adapts to primary accent) -->
    <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-12 -top-12 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute right-6 top-6 text-white/10 pointer-events-none select-none">
        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" opacity="0.35"/>
        </svg>
    </div>

    <div class="relative z-10 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
        
        <!-- Left: Islamic & User Greeting -->
        <div class="space-y-3 max-w-2xl">
            <!-- Badges Row -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex items-center space-x-1.5 px-3 py-1 bg-white/20 backdrop-blur-md rounded-xl text-xs font-bold text-white border border-white/25 shadow-2xs">
                    <span>🕌</span>
                    <span>Assalamu'alaikum Warahmatullah</span>
                </div>
                <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-black/20 backdrop-blur-md rounded-xl text-[11px] font-bold text-white/90 border border-white/15 font-mono">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>{{ ucfirst($defaultTab ?? 'Admin') }} View &bull; {{ $timezoneLabel }}</span>
                </div>
            </div>

            <!-- Title & User Name -->
            <div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white leading-tight drop-shadow-xs">
                    {{ $timeGreeting }} {{ $timeIcon }}, {{ auth()->user()->name }}
                </h1>
                <p class="text-xs sm:text-sm text-white/85 font-medium leading-relaxed mt-1">
                    @if(auth()->user()->hasRole('guru'))
                        Portal Asatidzah terpadu. Pantau presensi mandiri GPS, jadwal mengajar, materi ajar, dan tugas harian secara realtime.
                    @elseif(auth()->user()->hasRole('bendahara') || auth()->user()->hasRole('operator'))
                        Pantau ringkasan arus kas keuangan, rekapitulasi operasional sekolah, dan status pembayaran SPP.
                    @elseif(auth()->user()->hasRole('bk') || auth()->user()->hasRole('guru-bk'))
                        Pantau layanan bimbingan konseling santri, poin kedisiplinan, dan permohonan izin siswa.
                    @else
                        Pantau ringkasan operasional akademik, status pendaftaran SPMB, presensi guru &amp; siswa, serta aktivitas publikasi sekolah.
                    @endif
                </p>
            </div>

            <!-- Date & Time Info Pills -->
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-md rounded-xl text-xs font-semibold text-white/95 border border-white/20">
                    <span>📅</span>
                    <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
                @if(isset($todayAttendance) && $todayAttendance)
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/30 backdrop-blur-md rounded-xl text-xs font-bold text-emerald-100 border border-emerald-300/40">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-ping"></span>
                        <span>Presensi Hari Ini: {{ $todayAttendance->check_in ?? 'Hadir' }} ({{ ucfirst($todayAttendance->status) }})</span>
                    </div>
                @endif
            </div>

            <!-- Multi-Sesi Attendance Pills (If Teacher/Staff) -->
            @if($isTeacherOrStaff)
            <div class="pt-2">
                <p class="text-[11px] font-bold text-white/80 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <span>⏱️</span> Sesi Presensi Mandiri Hari Ini:
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                    <!-- 1. Pagi Masuk -->
                    <div class="p-2.5 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 text-center">
                        <span class="text-[9px] font-extrabold uppercase tracking-wider block text-white/75">1. Pagi Masuk</span>
                        <span class="text-xs font-black block mt-0.5">
                            @if(isset($todayAttendance) && $todayAttendance->check_in)
                                <span class="text-emerald-300">✓ {{ substr($todayAttendance->check_in, 0, 5) }}</span>
                            @else
                                <span class="text-white/90">{{ $sessionSettings['morning_open'] ?? '06:00' }} - {{ $sessionSettings['morning_close'] ?? '11:59' }}</span>
                            @endif
                        </span>
                    </div>

                    <!-- 2. Briefing Pagi -->
                    <div class="p-2.5 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 text-center">
                        <span class="text-[9px] font-extrabold uppercase tracking-wider block text-white/75">2. Briefing Guru</span>
                        <span class="text-xs font-black block mt-0.5">
                            @if(isset($hasAttendedBriefing) && $hasAttendedBriefing)
                                <span class="text-emerald-300">✓ Hadir</span>
                            @elseif(isset($briefingSession) && ($briefingSession['active'] ?? false))
                                <span class="text-amber-300 animate-pulse">⚡ Buka Sekarang</span>
                            @else
                                <span class="text-white/70">-</span>
                            @endif
                        </span>
                    </div>

                    <!-- 3. Sesi Siang -->
                    <div class="p-2.5 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 text-center">
                        <span class="text-[9px] font-extrabold uppercase tracking-wider block text-white/75">3. Dzuhur</span>
                        <span class="text-xs font-black block mt-0.5">
                            @if(isset($hasAttendedAfternoon) && $hasAttendedAfternoon)
                                <span class="text-emerald-300">✓ Hadir</span>
                            @else
                                <span class="text-white/90">{{ $sessionSettings['afternoon_open'] ?? '12:30' }}</span>
                            @endif
                        </span>
                    </div>

                    <!-- 4. Sesi Sore -->
                    <div class="p-2.5 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 text-center">
                        <span class="text-[9px] font-extrabold uppercase tracking-wider block text-white/75">4. Ashar</span>
                        <span class="text-xs font-black block mt-0.5">
                            <span class="text-white/90">{{ $sessionSettings['evening_open'] ?? '16:00' }}</span>
                        </span>
                    </div>

                    <!-- 5. Check Out / Pulang -->
                    <div class="p-2.5 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 text-center col-span-2 sm:col-span-1">
                        <span class="text-[9px] font-extrabold uppercase tracking-wider block text-white/75">5. Pulang</span>
                        <span class="text-xs font-black block mt-0.5">
                            @if(isset($todayAttendance) && $todayAttendance->check_out)
                                <span class="text-emerald-300">✓ {{ substr($todayAttendance->check_out, 0, 5) }}</span>
                            @else
                                <span class="text-white/70">KBM Selesai</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Action Badges & Buttons -->
        <div class="flex flex-col sm:flex-row xl:flex-col gap-3 shrink-0">
            <!-- Button: Presensi Mandiri (GPS Satelit) Trigger -->
            <button type="button" @click="openPresensiModal = true"
                    class="px-5 py-3.5 rounded-2xl bg-white text-slate-900 font-extrabold text-xs sm:text-sm shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                <span class="w-7 h-7 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black">📍</span>
                <span>Absen Mandiri GPS</span>
            </button>

            <!-- Button: Input Izin Siswa -->
            <button type="button" @click="createPermitModal = true"
                    class="px-5 py-3.5 rounded-2xl bg-white/20 hover:bg-white/30 text-white font-extrabold text-xs sm:text-sm backdrop-blur-md border border-white/30 shadow-md transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                <span>📝</span>
                <span>Input Izin Siswa</span>
            </button>

            <!-- Button: Terminal Presensi Kiosk Scan (New Tab) -->
            <a href="{{ route('admin.qr-attendance.scan') }}" target="_blank" rel="noopener"
               class="px-5 py-3 rounded-2xl bg-black/20 hover:bg-black/30 text-white/90 hover:text-white font-bold text-xs backdrop-blur-md border border-white/15 transition-all flex items-center justify-center gap-2 text-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Terminal Kiosk Presensi ↗</span>
            </a>
        </div>

    </div>

</div>
