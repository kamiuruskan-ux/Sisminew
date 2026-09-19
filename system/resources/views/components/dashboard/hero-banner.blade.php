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

    // Calculate Hijri date dynamically with robust fallback
    $hijriDate = '';
    try {
        if (class_exists('\IntlDateFormatter')) {
            $hijriFmt = new \IntlDateFormatter(
                'id_ID@calendar=islamic-umalqura',
                \IntlDateFormatter::FULL,
                \IntlDateFormatter::NONE,
                'Asia/Makassar',
                \IntlDateFormatter::TRADITIONAL,
                'd MMMM y'
            );
            $hijriDate = $hijriFmt ? ($hijriFmt->format(time()) . ' H') : '';
        }
    } catch (\Throwable $e) {
        $hijriDate = '';
    }
    if (empty($hijriDate)) {
        $hijriDate = '8 Rabiul Awwal 1448 H';
    }
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

            <!-- Date & Time Info Pills: Kalender Masehi & Kalender Hijriyah di Bawahnya -->
            <div class="flex flex-col gap-1.5 pt-1">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-md rounded-xl text-xs font-semibold text-white/95 border border-white/20 w-fit">
                    <span>📅</span>
                    <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md rounded-xl text-xs font-bold text-amber-200 border border-white/25 w-fit">
                    <span>🌙</span>
                    <span>{{ $hijriDate }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Action Badges & Buttons -->
        <div class="flex flex-col gap-2.5 shrink-0 w-full sm:w-auto">
            <!-- Button: Presensi Mandiri (GPS Satelit) Popup Trigger -->
            <button type="button" @click.stop="openPresensiModal = true"
               class="px-5 py-3.5 rounded-2xl bg-white text-slate-900 font-extrabold text-xs sm:text-sm shadow-xl hover:shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-base shadow-xs">📍</span>
                <span>Absen Mandiri GPS</span>
            </button>

            <!-- Button: Pengajuan Izin Pegawai Popup Trigger (Tepat di bawah tombol absensi mandiri) -->
            <button type="button" @click.stop="openEmployeePermitModal = true"
               class="px-5 py-3 rounded-2xl bg-white/20 hover:bg-white/30 text-white font-extrabold text-xs sm:text-sm shadow-lg hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] backdrop-blur-md border border-white/30 hover:border-white/50 transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                <span class="w-8 h-8 rounded-xl bg-white/25 text-white flex items-center justify-center font-bold text-base shadow-xs">📝</span>
                <span>Pengajuan Izin Pegawai</span>
            </button>
        </div>

    </div>

</div>
