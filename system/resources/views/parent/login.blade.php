<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Orang Tua - {{ Setting::get('school_name', 'Sekolah') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ \App\Models\Setting::getFaviconUrl() }}">
    <link rel="shortcut icon" type="image/png" href="{{ \App\Models\Setting::getFaviconUrl() }}">
    <link rel="apple-touch-icon" href="{{ \App\Models\Setting::getFaviconUrl() }}">
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '{{ Setting::get('primary_color', '#3C50E0') }}',
                        secondary: '{{ Setting::get('secondary_color', '#2563eb') }}',
                        indigo: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#3C50E0') }}',
                            600: '{{ Setting::get('primary_color', '#3C50E0') }}',
                            700: '{{ Setting::get('secondary_color', '#2563eb') }}',
                            800: '{{ Setting::get('secondary_color', '#2563eb') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#2563eb') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#2563eb') }} 95%, black)',
                        },
                        blue: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#3C50E0') }}',
                            600: '{{ Setting::get('primary_color', '#3C50E0') }}',
                            700: '{{ Setting::get('secondary_color', '#2563eb') }}',
                            800: '{{ Setting::get('secondary_color', '#2563eb') }}',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#2563eb') }} 95%, black)',
                        },
                        emerald: {
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 30%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 70%, white)',
                            500: '{{ Setting::get('primary_color', '#3C50E0') }}',
                            950: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3C50E0') }} 95%, black)',
                        },
                        teal: {
                            200: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#2563eb') }} 30%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#2563eb') }} 50%, white)',
                            450: '{{ Setting::get('secondary_color', '#2563eb') }}',
                            500: '{{ Setting::get('secondary_color', '#2563eb') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#2563eb') }} 90%, black)',
                        }
                    }
                }
            }
        };
    </script>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .bg-grid-pattern {
            background-image: radial-gradient(rgba(99, 102, 241, 0.12) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 relative overflow-x-hidden flex flex-col antialiased">
    
    <div class="flex-1 flex flex-col lg:flex-row min-h-screen">
        
        <!-- LEFT SIDE: Information & School Hero Panel (Desktop) -->
        <div class="hidden lg:flex lg:w-5/12 xl:w-5/12 relative overflow-hidden bg-gradient-to-br from-emerald-950 via-teal-900 to-slate-950 text-white p-10 xl:p-14 flex-col justify-between border-r border-teal-800/40">
            <!-- Ambient Glow Background -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Brand Header -->
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3.5 group">
                    <div class="relative shrink-0">
                        @if(Setting::get('logo_path'))
                            <img src="{{ asset(Setting::get('logo_path')) }}" alt="Logo" class="h-11 w-11 rounded-2xl shadow-md object-contain bg-white/10 p-1 border border-white/20 group-hover:scale-105 transition-transform">
                        @else
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-11 w-11 rounded-2xl shadow-md object-cover border border-white/20 group-hover:scale-105 transition-transform">
                        @endif
                    </div>
                    <div>
                        <h1 class="text-base font-extrabold text-white tracking-tight leading-snug group-hover:text-emerald-200 transition-colors">{{ Setting::get('school_name', 'Sekolah') }}</h1>
                        <p class="text-[10px] text-emerald-300 font-extrabold tracking-widest uppercase">PORTAL WALI SISWA</p>
                    </div>
                </a>
            </div>

            <!-- Content Showcase -->
            <div class="relative z-10 my-auto py-8">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-xs font-bold text-emerald-200 mb-5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    MONITORING PERKEMBANGAN ANAK
                </span>
                
                <h2 class="text-3xl xl:text-4xl font-black text-white leading-tight tracking-tight mb-6">
                    Pantau Perkembangan <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-200 via-teal-200 to-sky-200">Belajar & Presensi</span> <br>
                    Putra-Putri Anda.
                </h2>
                
                <div class="space-y-3.5 max-w-md">
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center shrink-0 shadow-inner">
                            <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-extrabold text-xs">Laporan Presensi Real-Time</h3>
                            <p class="text-emerald-200/80 text-[11px] leading-relaxed mt-0.5">Dapatkan notifikasi kehadiran harian putra-putri Anda secara transparan.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10">
                        <div class="w-10 h-10 rounded-xl bg-teal-500/20 border border-teal-400/30 flex items-center justify-center shrink-0 shadow-inner">
                            <svg class="w-5 h-5 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-extrabold text-xs">Informasi Pembayaran SPP</h3>
                            <p class="text-emerald-200/80 text-[11px] leading-relaxed mt-0.5">Cek rincian tagihan sekolah dan riwayat transaksi secara praktis online.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Left -->
            <div class="relative z-10">
                <p class="text-[11px] text-emerald-300/70">© {{ date('Y') }} {{ Setting::get('school_name', 'Sekolah') }}. Seluruh hak cipta dilindungi.</p>
            </div>
        </div>

        <!-- RIGHT SIDE: Light & Modern Login Form Container -->
        <div class="flex-1 flex flex-col justify-center items-center p-4 sm:p-8 lg:p-12 relative bg-grid-pattern min-h-screen lg:min-h-0">
            
            <div class="w-full max-w-md my-auto">
                
                <!-- Mobile Header Logo -->
                <div class="lg:hidden w-full text-center mb-6">
                    <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-2 group">
                        @if(Setting::get('logo_path'))
                            <img src="{{ asset(Setting::get('logo_path')) }}" alt="Logo" class="h-12 w-12 rounded-2xl shadow-md object-contain bg-white p-1 border border-slate-200 group-hover:scale-105 transition-transform">
                        @else
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-12 w-12 rounded-2xl shadow-md object-cover border border-slate-200 group-hover:scale-105 transition-transform">
                        @endif
                        <h1 class="text-base font-extrabold text-slate-900 tracking-tight">{{ Setting::get('school_name', 'Sekolah') }}</h1>
                    </a>
                </div>

                <!-- Modern Role Switcher Segment Control Tabs -->
                <div class="bg-slate-200/70 p-1.5 rounded-2xl mb-4 flex items-center gap-1 shadow-inner">
                    <a href="{{ route('login') }}" 
                       class="flex-1 text-center py-2 px-3 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition-all flex items-center justify-center gap-1.5">
                        <svg class="hidden sm:block w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        <span>Siswa & Staf</span>
                    </a>
                    <a href="{{ route('parent.login') }}" 
                       class="flex-1 text-center py-2 px-3 rounded-xl text-xs font-extrabold transition-all bg-indigo-600 text-white shadow-md flex items-center justify-center gap-1.5">
                        <svg class="hidden sm:block w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Orang Tua</span>
                    </a>
                </div>

                <!-- Clean Crisp White Login Card -->
                <div class="bg-white rounded-3xl p-4 xs:p-6 sm:p-8 shadow-xl shadow-slate-200/70 border border-slate-200/80">
                    
                    <div class="mb-6">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Portal Orang Tua / Wali</h2>
                        <p class="text-slate-500 text-xs font-medium mt-1">Masukkan NISN dan Tanggal Lahir putra-putri Anda.</p>
                    </div>

                    <!-- DISMISSIBLE ALERT ERRORS -->
                    @if ($errors->any())
                        <div x-data="{ showAlert: true }" x-show="showAlert" x-transition class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-start justify-between gap-3 shadow-xs">
                            <div class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div class="space-y-1">
                                    @foreach ($errors->all() as $error)
                                        @if (!\Illuminate\Support\Str::startsWith($error, 'data:image') && $error !== '1' && $error !== 'true')
                                            <p class="leading-relaxed">{{ $error }}</p>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" @click="showAlert = false" class="text-rose-400 hover:text-rose-700 transition-colors shrink-0" title="Tutup">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endif

                    <!-- DISMISSIBLE ALERT SUCCESS -->
                    @if(session('success'))
                        <div x-data="{ showAlert: true }" x-show="showAlert" x-transition class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-start justify-between gap-3 shadow-xs">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="leading-relaxed">{{ session('success') }}</p>
                            </div>
                            <button type="button" @click="showAlert = false" class="text-emerald-400 hover:text-emerald-700 transition-colors shrink-0" title="Tutup">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endif

                    @if(session('parent_otp') && session('parent_user_id'))
                        <!-- FORM VERIFIKASI OTP ORTU -->
                        <form method="POST" action="{{ route('parent.verify-otp') }}" x-data="{ verifying: false }" @submit="verifying = true">
                            @csrf
                            <div class="space-y-4">
                                <div class="space-y-1.5">
                                    <label for="otp_code" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kode OTP (6 Digit)</label>
                                    <input type="text" id="otp_code" name="otp_code" required maxlength="6" autofocus placeholder="123456"
                                           class="w-full text-center py-3.5 rounded-xl font-mono text-xl font-black tracking-widest text-indigo-600 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 transition-all">
                                    <p class="text-[11px] text-slate-400 mt-1">Kode OTP telah dikirimkan ke WhatsApp Orang Tua.</p>
                                </div>

                                <button type="submit" :disabled="verifying"
                                        class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer border border-indigo-500/30">
                                    <span x-show="!verifying" class="flex items-center gap-2">
                                        Verifikasi & Masuk Portal
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                    <span x-show="verifying" class="flex items-center gap-2" x-cloak>
                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Verifikasi...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @else
                        <!-- FORM UTAMA ORTU -->
                        <form method="POST" action="{{ route('parent.login.submit') }}" x-data="{ loading: false, isMobile: window.innerWidth < 480 }" @resize.window="isMobile = window.innerWidth < 480" @submit="loading = true">
                            @csrf
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="nisn" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NISN</label>
                                    <div class="relative group">
                                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/></svg>
                                        </span>
                                        <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required autofocus
                                               class="w-full pl-10 pr-4 py-3 bg-slate-50/80 border border-slate-200 text-slate-900 text-sm font-bold font-mono rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 transition-all placeholder-slate-400" 
                                               :placeholder="isMobile ? 'NISN' : 'Contoh: 0012345678'">
                                    </div>
                                </div>

                                <div x-data>
                                    <label for="birth_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Lahir Siswa</label>
                                    <div class="relative group cursor-pointer" @click="$refs.birthDateInput.showPicker()">
                                        <button type="button" @click.stop="$refs.birthDateInput.showPicker()" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors focus:outline-none cursor-pointer" title="Pilih Tanggal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </button>
                                        <input type="date" id="birth_date" name="birth_date" x-ref="birthDateInput" value="{{ old('birth_date') }}" required 
                                               @click="$el.showPicker()"
                                               class="w-full pl-10 pr-4 py-3 bg-slate-50/80 border border-slate-200 text-slate-900 text-sm font-semibold rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 transition-all cursor-pointer">
                                    </div>
                                </div>

                                @if((isset($showCaptcha) && $showCaptcha) || session('show_captcha'))
                                <div x-data="{ captchaSvg: '{{ session('captcha_svg', $captchaSvg ?? '') }}', refreshing: false }" 
                                     class="p-3 sm:p-4 bg-gradient-to-br from-slate-50 via-indigo-50/30 to-blue-50/30 dark:from-slate-800/80 dark:to-slate-900 border border-indigo-100 dark:border-slate-700 rounded-2xl shadow-xs space-y-2.5 sm:space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5 text-[11px] sm:text-xs font-black text-indigo-950 dark:text-indigo-200 uppercase tracking-wider">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            <span>Verifikasi CAPTCHA</span>
                                        </div>
                                        <button type="button" @click="refreshing = true; fetch('{{ route('captcha.refresh') }}').then(r=>r.json()).then(d=>{ captchaSvg = d.captcha_svg; refreshing = false; })" 
                                                class="px-2 py-0.5 sm:px-2.5 sm:py-1 text-[10px] sm:text-[11px] font-bold text-indigo-700 hover:text-indigo-900 bg-indigo-100/70 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-300 rounded-lg transition-all flex items-center gap-1 shrink-0 shadow-2xs">
                                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 transition-transform duration-300" :class="{ 'animate-spin': refreshing }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            <span>Acak Soal</span>
                                        </button>
                                    </div>
                                    <div class="flex flex-row items-center gap-2 sm:gap-3">
                                        <div class="shrink-0 flex items-center justify-center rounded-xl overflow-hidden border border-slate-200/90 dark:border-slate-700 bg-white p-1 shadow-xs max-w-[110px] sm:max-w-[140px]">
                                            <img :src="captchaSvg" alt="CAPTCHA" class="h-9 sm:h-10 w-full object-contain rounded-lg">
                                        </div>
                                        <div class="relative flex-1 min-w-0 group">
                                            <span class="absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </span>
                                            <input type="text" id="captcha_code" name="captcha_code" required placeholder="Jawaban..."
                                                   class="w-full pl-8 sm:pl-9 pr-2.5 sm:pr-3 py-2 sm:py-2.5 bg-white border border-slate-200 text-slate-900 font-mono text-xs sm:text-sm font-bold rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 transition-all placeholder-slate-400">
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <button type="submit" :disabled="loading"
                                        class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer border border-indigo-500/30 mt-2">
                                    <span x-show="!loading" class="flex items-center gap-2">
                                        Masuk Portal Orang Tua
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                    <span x-show="loading" class="flex items-center gap-2" x-cloak>
                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Memproses...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @endif

                    <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <a href="{{ route('home') }}" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-800 transition-colors font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</body>
</html>
