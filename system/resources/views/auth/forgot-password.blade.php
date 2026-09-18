<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lupa Password - {{ Setting::get('school_name', 'Sekolah') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="apple-touch-icon" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    
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
                        @if(Setting::get('logo_path'))
                            <img src="{{ asset(Setting::get('logo_path')) }}" alt="Logo" class="h-11 w-11 rounded-2xl shadow-md object-contain bg-white/10 p-1 border border-white/20 group-hover:scale-105 transition-transform">
                        @else
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-11 w-11 rounded-2xl shadow-md object-cover border border-white/20 group-hover:scale-105 transition-transform">
                        @endif
                    </div>
                    <div>
                        <h1 class="text-base font-extrabold text-white tracking-tight leading-snug group-hover:text-primary transition-colors">{{ Setting::get('school_name', 'Sekolah') }}</h1>
                        <p class="text-[10px] text-white/70 font-extrabold tracking-widest uppercase">PORTAL AKADEMIK DIGITAL</p>
                    </div>
                </a>
            </div>

            <!-- Content Showcase -->
            <div class="relative z-10 my-auto py-8">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-white mb-5">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    PORTAL SISWA & STAF TERPADU
                </span>
                
                <h2 class="text-3xl xl:text-4xl font-black text-white leading-tight tracking-tight mb-6">
                    Akses Pembelajaran & <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-white/90 to-white/75">Laporan Akademik</span> <br>
                    Secara Real-Time.
                </h2>
                
                <div class="space-y-3.5 max-w-md">
                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10">
                        <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0 shadow-inner">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-extrabold text-xs">Materi & Penugasan Online</h3>
                            <p class="text-white/80 text-[11px] leading-relaxed mt-0.5">Unduh modul pelajaran, kumpulkan tugas sekolah, dan pantau status kelulusan KKM.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10">
                        <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0 shadow-inner">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-extrabold text-xs">Jadwal & Presensi QR Code</h3>
                            <p class="text-white/80 text-[11px] leading-relaxed mt-0.5">Cek jadwal harian dan gunakan QR Code digital untuk presensi kehadiran siswa.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Left -->
            <div class="relative z-10">
                <p class="text-[11px] text-white/60">© {{ date('Y') }} {{ Setting::get('school_name', 'Sekolah') }}. Seluruh hak cipta dilindungi.</p>
            </div>
        </div>

        <!-- RIGHT SIDE: Light & Modern Forgot Password Form Container -->
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

                <div class="bg-white/80 backdrop-blur-md rounded-3xl p-6 sm:p-10 border border-slate-200/80 shadow-[0_20px_50px_-20px_rgba(15,30,75,0.05)] w-full">
                    
                    <div class="mb-8">
                        <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Lupa Password</h2>
                        <p class="text-slate-500 text-xs sm:text-sm font-medium">Masukkan alamat email terdaftar Anda untuk menerima tautan pemulihan password.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200/60 text-red-700 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p class="font-semibold">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="mb-6 bg-emerald-50 border border-emerald-200/60 text-emerald-700 px-4 py-3 rounded-2xl text-xs sm:text-sm flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-semibold">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('forgot-password.submit') }}" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        
                        <div class="space-y-6">
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                                <div class="relative group">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                    </span>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                           class="w-full pl-10 pr-4 py-3 bg-slate-50/80 border border-slate-200 text-slate-900 text-sm font-semibold rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 transition-all placeholder-slate-400" 
                                           placeholder="nama@email.sekolah.id">
                                </div>
                            </div>

                            <button type="submit" :disabled="loading"
                                    class="w-full py-3.5 px-5 text-white bg-gradient-to-r from-primary to-secondary hover:brightness-110 active:scale-[0.98] transition-all duration-150 font-bold rounded-xl shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                                <span x-show="!loading" class="flex items-center gap-2 justify-center">
                                    <span>Kirim Link Reset Password</span>
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </span>
                                <span x-show="loading" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-row items-center justify-between gap-4">
                        <a href="{{ route('login') }}" class="flex items-center gap-2 text-slate-500 hover:text-slate-900 transition-colors text-xs sm:text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Kembali ke Login</span>
                        </a>
                        <a href="{{ route('home') }}" class="text-primary hover:text-secondary font-bold text-xs sm:text-sm transition-colors">Beranda Utama</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
