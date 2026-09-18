<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Admin / Staf - {{ Setting::get('school_name', 'Sekolah') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ \App\Models\Setting::getFaviconUrl() }}">
    <link rel="shortcut icon" type="image/png" href="{{ \App\Models\Setting::getFaviconUrl() }}">
    <link rel="apple-touch-icon" href="{{ \App\Models\Setting::getFaviconUrl() }}">
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ Setting::get('primary_color', '#6366f1') }}',
                        secondary: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                        brand: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#6366f1') }}',
                            600: '{{ Setting::get('primary_color', '#6366f1') }}',
                            700: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            800: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 95%, black)',
                        },
                        indigo: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#6366f1') }}',
                            600: '{{ Setting::get('primary_color', '#6366f1') }}',
                            700: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            800: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 95%, black)',
                        },
                        amber: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 20%, white)',
                            500: '{{ Setting::get('primary_color', '#6366f1') }}',
                            600: '{{ Setting::get('primary_color', '#6366f1') }}',
                            700: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                        },
                        orange: {
                            50: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 20%, white)',
                            500: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            700: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 90%, black)',
                        },
                        blue: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#6366f1') }}',
                            600: '{{ Setting::get('primary_color', '#6366f1') }}',
                            700: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            800: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 95%, black)',
                        },
                        purple: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#6366f1') }}',
                            600: '{{ Setting::get('primary_color', '#6366f1') }}',
                            700: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            800: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 95%, black)',
                        },
                        violet: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 20%, white)',
                            500: '{{ Setting::get('primary_color', '#6366f1') }}',
                            600: '{{ Setting::get('primary_color', '#6366f1') }}',
                            700: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                        }
                    }
                }
            }
        }
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
        <div class="hidden lg:flex lg:w-5/12 xl:w-5/12 relative overflow-hidden bg-gradient-to-br from-primary via-secondary to-slate-950 text-white p-10 xl:p-14 flex-col justify-between border-r border-primary/20">
            <!-- Ambient Glow Background -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-secondary/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Brand Header -->
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3.5 group">
                    <div class="relative shrink-0">
                        @php
                            $adminLogo = Setting::get('logo_path') ?? Setting::get('school_logo');
                        @endphp
                        @if($adminLogo)
                            <img src="{{ \Illuminate\Support\Str::startsWith($adminLogo, ['http://', 'https://', 'img/']) ? asset($adminLogo) : asset('img/' . $adminLogo) }}" alt="Logo" class="h-11 w-11 rounded-2xl shadow-md object-contain bg-white/10 p-1 border border-white/20 group-hover:scale-105 transition-transform">
                        @else
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-11 w-11 rounded-2xl shadow-md object-cover border border-white/20 group-hover:scale-105 transition-transform">
                        @endif
                    </div>
                    <div>
                        <h1 class="text-base font-extrabold text-white tracking-tight leading-snug group-hover:text-primary transition-colors">{{ Setting::get('school_name', 'Sekolah') }}</h1>
                        <p class="text-[10px] text-white/70 font-extrabold tracking-widest uppercase">PANEL MANAJEMEN SEKOLAH</p>
                    </div>
                </a>
            </div>

            <!-- Content Showcase -->
            <div class="relative z-10 my-auto py-8">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-white mb-5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    AKSES PORTAL ADMINISTRATOR & GURU
                </span>
                
                <h2 class="text-3xl xl:text-4xl font-black text-white leading-tight tracking-tight mb-6">
                    Sistem Tata Kelola <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-white/90 to-white/75">Akademik & Keuangan</span> <br>
                    Terintegrasi Aman.
                </h2>
                
                <div class="space-y-3.5 max-w-md">
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10">
                        <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0 shadow-inner">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-extrabold text-xs">Proteksi Autentikasi 2-Faktor</h3>
                            <p class="text-white/80 text-[11px] leading-relaxed mt-0.5">Mendukung verifikasi OTP WhatsApp resmi untuk keamanan data administrator dan pengajar.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10">
                        <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0 shadow-inner">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-extrabold text-xs">Modul Terpusat</h3>
                            <p class="text-white/80 text-[11px] leading-relaxed mt-0.5">Kelola data siswa, jadwal, nilai, tagihan SPP, broadcast WA, dan ujian online CBT.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Left -->
            <div class="relative z-10">
                <p class="text-[11px] text-white/60">© {{ date('Y') }} {{ Setting::get('school_name', 'Sekolah') }}. Seluruh hak cipta dilindungi.</p>
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
                       class="flex-1 text-center py-2 px-1.5 xs:px-3 rounded-xl text-[10px] xs:text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition-all flex items-center justify-center gap-1 sm:gap-1.5">
                        <svg class="hidden sm:block w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        <span>Siswa</span>
                    </a>
                    <a href="{{ route('parent.login') }}" 
                       class="flex-1 text-center py-2 px-1.5 xs:px-3 rounded-xl text-[10px] xs:text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition-all flex items-center justify-center gap-1 sm:gap-1.5">
                        <svg class="hidden sm:block w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Orang Tua</span>
                    </a>
                    <a href="{{ route('admin.login') }}" 
                       class="flex-1 text-center py-2 px-1.5 xs:px-3 rounded-xl text-[10px] xs:text-xs font-extrabold transition-all bg-indigo-600 text-white shadow-md flex items-center justify-center gap-1 sm:gap-1.5">
                        <svg class="hidden sm:block w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Admin / Staf</span>
                    </a>
                </div>

                <!-- Clean Crisp White Login Card -->
                <div class="bg-white rounded-3xl p-4 xs:p-6 sm:p-8 shadow-xl shadow-slate-200/70 border border-slate-200/80">
                    
                    <div class="mb-6">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Login Staf & Admin</h2>
                        <p class="text-slate-500 text-xs font-medium mt-1">Masukkan Email dan Password akun administrator / guru Anda.</p>
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
                                        <p class="leading-relaxed">{{ $error }}</p>
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

                    @if(session('admin_otp') && session('admin_otp_user_id'))
                        <!-- FORM VERIFIKASI OTP ADMIN -->
                        <form method="POST" action="{{ route('admin.verify-otp.submit') }}" x-data="{ verifying: false }" @submit="verifying = true">
                            @csrf
                            <div class="space-y-4">
                                <div class="space-y-1.5">
                                    <label for="otp_code" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kode OTP Admin (6 Digit)</label>
                                    <input type="text" id="otp_code" name="otp_code" required maxlength="6" autofocus placeholder="123456"
                                           class="w-full text-center py-3.5 rounded-xl font-mono text-xl font-black tracking-widest text-indigo-600 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 transition-all">
                                    <p class="text-[11px] text-slate-400 mt-1">Kode OTP telah dikirimkan ke WhatsApp Anda.</p>
                                </div>

                                <button type="submit" :disabled="verifying"
                                        class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer border border-indigo-500/30">
                                    <span x-show="!verifying" class="flex items-center gap-2">
                                        Verifikasi & Masuk Dashboard
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
                        <!-- FORM UTAMA ADMIN -->
                        <form method="POST" action="{{ route('admin.login.submit') }}" x-data="{ loading: false, showPassword: false, isMobile: window.innerWidth < 480 }" @resize.window="isMobile = window.innerWidth < 480" @submit="loading = true">
                            @csrf
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Administrator / Guru</label>
                                    <div class="relative group">
                                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                        </span>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                               class="w-full pl-10 pr-4 py-3 bg-slate-50/80 border border-slate-200 text-slate-900 text-sm font-semibold rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 transition-all placeholder-slate-400" 
                                               :placeholder="isMobile ? 'Email Admin / Guru' : 'admin@sekolah.sch.id'">
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between items-center mb-1.5">
                                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                                    </div>
                                    <div class="relative group">
                                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </span>
                                        <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                               class="w-full pl-10 pr-10 py-3 bg-slate-50/80 border border-slate-200 text-slate-900 text-sm font-semibold rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 transition-all placeholder-slate-400" 
                                               placeholder="••••••••">
                                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none" title="Lihat Password">
                                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-1">
                                    <label class="flex items-center cursor-pointer select-none">
                                        <input type="checkbox" id="remember" name="remember" 
                                               class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 transition-colors cursor-pointer">
                                        <span class="ml-2 text-xs font-semibold text-slate-600">Tetap masuk</span>
                                    </label>
                                </div>

                                <button type="submit" :disabled="loading"
                                        class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-indigo-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer border border-indigo-500/30 mt-2">
                                    <span x-show="!loading" class="flex items-center gap-2">
                                        Masuk ke Dashboard Admin
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
