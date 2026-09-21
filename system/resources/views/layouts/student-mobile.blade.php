<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>@yield('title', 'Dashboard') - {{ Setting::get('school_name', 'Portal Siswa') }}</title>
    
    <!-- Anti-FOUC Dark Mode & Theme Initializer -->
    <script>
        (function() {
            var savedTheme = localStorage.getItem('theme_mode');
            var defaultTheme = "{{ Setting::get('theme_mode_default', 'light') }}";
            var mode = savedTheme || defaultTheme;
            if (mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ \App\Models\Setting::getFaviconUrl() }}">
    <link rel="shortcut icon" type="image/png" href="{{ \App\Models\Setting::getFaviconUrl() }}">
    <link rel="apple-touch-icon" href="{{ \App\Models\Setting::getFaviconUrl() }}">

    <!-- TailwindCSS CDN (dengan filter suppress warning produksi) -->
    <script>
        (function() {
            var _warn = console.warn;
            console.warn = function() {
                if (arguments[0] && typeof arguments[0] === 'string' && arguments[0].indexOf('cdn.tailwindcss.com') !== -1) {
                    return;
                }
                _warn.apply(console, arguments);
            };
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '{{ Setting::get('primary_color', '#6366f1') }}',
                        secondary: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                        brand: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 20%, white)',
                            500: '{{ Setting::get('primary_color', '#6366f1') }}',
                            600: '{{ Setting::get('primary_color', '#6366f1') }}',
                            700: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            800: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                        },
                        slate: {
                            850: '#111827',
                            950: '#030712',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- AlpineJS & Collapse Plugin CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .bottom-nav { padding-bottom: env(safe-area-inset-bottom); }

        /* Hide scrollbars globally for no-scrollbar or scrollbar-hide classes */
        .no-scrollbar::-webkit-scrollbar,
        .scrollbar-hide::-webkit-scrollbar,
        .scrollbar-none::-webkit-scrollbar {
            display: none !important;
        }
        .no-scrollbar,
        .scrollbar-hide,
        .scrollbar-none {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }

        .desktop-shell {
            background:
                radial-gradient(900px 360px at 8% -8%, color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 12%, transparent), transparent 60%),
                radial-gradient(800px 320px at 95% -12%, color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 10%, transparent), transparent 60%),
                #f8fafc;
        }

        html.dark .desktop-shell {
            background:
                radial-gradient(900px 360px at 8% -8%, color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 18%, transparent), transparent 60%),
                radial-gradient(800px 320px at 95% -12%, color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 15%, transparent), transparent 60%),
                #030712;
            color: #f8fafc;
        }

        html.dark .desktop-topbar {
            background: rgba(15, 23, 42, 0.85);
            border-bottom-color: #1e293b;
        }

        .desktop-sidebar {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            box-shadow: 4px 0 24px -4px rgba(0, 0, 0, 0.04);
        }

        html.dark .desktop-sidebar {
            background: #0f172a;
            border-right-color: #1e293b;
            box-shadow: none;
        }

        .desktop-nav-link {
            border: 1px solid transparent;
            transition: all .2s ease;
            color: #64748b;
        }

        .desktop-nav-link:hover {
            color: {{ Setting::get('primary_color', '#6366f1') }};
            background: color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 6%, #ffffff);
            border-color: color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 10%, #ffffff);
        }

        .desktop-nav-link.active {
            border-color: {{ Setting::get('primary_color', '#6366f1') }}33;
            background: linear-gradient(90deg, color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 12%, #ffffff), color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 8%, #ffffff));
            color: {{ Setting::get('primary_color', '#6366f1') }};
            box-shadow: 0 4px 12px -2px color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 12%, transparent);
            font-weight: 700;
        }

        /* Dark mode overrides for nav links */
        html.dark .desktop-nav-link {
            color: #94a3b8;
        }

        html.dark .desktop-nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.12);
        }

        html.dark .desktop-nav-link.active {
            border-color: {{ Setting::get('primary_color', '#6366f1') }}66;
            background: linear-gradient(90deg, color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 35%, transparent), color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 25%, transparent));
            color: #ffffff;
            box-shadow: 0 10px 25px -10px {{ Setting::get('primary_color', '#6366f1') }}80;
        }

        .desktop-topbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .mobile-header-gradient {
            background: linear-gradient(135deg, color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 25%, #0f172a) 0%, color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 35%, #090d16) 100%);
        }

        /* Comprehensive Dark Mode Overrides for All Student Portal Pages */
        html.dark body {
            background-color: #030712 !important;
            color: #F8FAFC !important;
        }

        html.dark .bg-white {
            background-color: #0F172A !important;
            border-color: #1E293B !important;
            color: #F8FAFC !important;
        }

        html.dark .bg-slate-50,
        html.dark .bg-slate-100,
        html.dark .bg-slate-50\/50,
        html.dark .bg-slate-50\/80,
        html.dark .bg-slate-50\/60,
        html.dark .bg-slate-50\/40,
        html.dark .bg-gray-50,
        html.dark .bg-gray-100 {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            color: #F8FAFC !important;
        }

        html.dark .bg-emerald-50,
        html.dark .bg-green-50 {
            background-color: rgba(6, 78, 59, 0.25) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
            color: #6EE7B7 !important;
        }

        html.dark .bg-indigo-50 {
            background-color: rgba(49, 46, 129, 0.4) !important;
            border-color: rgba(99, 102, 241, 0.4) !important;
            color: #C7D2FE !important;
        }

        html.dark .bg-amber-50,
        html.dark .bg-amber-50\/50 {
            background-color: rgba(120, 53, 15, 0.25) !important;
            border-color: rgba(217, 119, 6, 0.4) !important;
            color: #FDE68A !important;
        }

        html.dark .bg-blue-50 {
            background-color: rgba(30, 58, 138, 0.3) !important;
            border-color: rgba(59, 130, 246, 0.4) !important;
            color: #93C5FD !important;
        }

        html.dark .bg-purple-50 {
            background-color: rgba(88, 28, 135, 0.3) !important;
            border-color: rgba(168, 85, 247, 0.4) !important;
            color: #E9D5FF !important;
        }

        html.dark .text-slate-900,
        html.dark .text-slate-800,
        html.dark .text-slate-700,
        html.dark .text-gray-900,
        html.dark .text-gray-800,
        html.dark .text-gray-700 {
            color: #F8FAFC !important;
        }

        html.dark .text-slate-600,
        html.dark .text-slate-500,
        html.dark .text-slate-400,
        html.dark .text-gray-600,
        html.dark .text-gray-500,
        html.dark .text-gray-400 {
            color: #94A3B8 !important;
        }

        html.dark .border-slate-100,
        html.dark .border-slate-200,
        html.dark .border-slate-300,
        html.dark .border-gray-100,
        html.dark .border-gray-200,
        html.dark .border-green-100,
        html.dark .border-green-200,
        html.dark .divide-slate-100,
        html.dark .divide-slate-200,
        html.dark .divide-slate-300,
        html.dark .divide-gray-100,
        html.dark .divide-gray-200 {
            border-color: #1E293B !important;
        }

        html.dark table,
        html.dark .modern-table {
            background-color: #0F172A !important;
            color: #F8FAFC !important;
        }

        html.dark table thead th {
            background-color: #1E293B !important;
            color: #94A3B8 !important;
            border-bottom-color: #334155 !important;
        }

        html.dark table tbody td {
            border-bottom-color: #1E293B !important;
            color: #F8FAFC !important;
        }

        html.dark table tbody tr:hover {
            background-color: rgba(30, 41, 59, 0.6) !important;
        }

        html.dark input[type="text"], 
        html.dark input[type="email"], 
        html.dark input[type="password"], 
        html.dark input[type="number"], 
        html.dark input[type="date"], 
        html.dark input[type="datetime-local"], 
        html.dark input[type="search"], 
        html.dark select, 
        html.dark textarea {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            color: #FFFFFF !important;
        }

        html.dark nav.bottom-nav {
            background-color: rgba(15, 23, 42, 0.95) !important;
            border-top-color: #1E293B !important;
            color: #94A3B8 !important;
        }

        html.dark nav.bottom-nav a {
            color: #94A3B8;
        }

        html.dark nav.bottom-nav a.text-indigo-600 {
            color: #818CF8 !important;
        }

        html.dark #qrModal .bg-white {
            background-color: #0F172A !important;
            border-color: #1E293B !important;
            color: #F8FAFC !important;
        }

        html.dark #qrModal .bg-slate-50 {
            background-color: #1E293B !important;
            border-color: #334155 !important;
        }

        @keyframes scan-line {
            0%, 100% { top: 8%; opacity: 0; }
            15% { opacity: 1; }
            85% { opacity: 1; }
            92% { top: 90%; opacity: 0; }
        }
        .animate-scan-line {
            animation: scan-line 2.5s ease-in-out infinite;
        }
    </style>

    @stack('styles')

    <!-- Global Image Auto Fallback Script -->
    <script>
        (function() {
            window.NO_IMAGE_FALLBACK = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="%23F1F5F9"/><g transform="translate(250, 130)"><rect x="10" y="10" width="80" height="60" rx="10" fill="%23E2E8F0" stroke="%2394A3B8" stroke-width="4"/><circle cx="35" cy="32" r="8" fill="%2394A3B8"/><path d="M15 62L38 42L58 58L72 48L85 62" stroke="%2394A3B8" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><line x1="5" y1="5" x2="95" y2="75" stroke="%23F43F5E" stroke-width="5" stroke-linecap="round"/></g><text x="300" y="235" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="700" fill="%2364748B" text-anchor="middle">GAMBAR TIDAK TERSEDIA</text></svg>`;

            window.addEventListener('error', function(e) {
                if (e.target && e.target.tagName === 'IMG') {
                    if (e.target.src !== window.NO_IMAGE_FALLBACK) {
                        e.target.onerror = null;
                        e.target.src = window.NO_IMAGE_FALLBACK;
                    }
                }
            }, true);

            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('img').forEach(function(img) {
                    const src = img.getAttribute('src');
                    if (!src || src.trim() === '' || src.endsWith('/img/') || src.endsWith('/img') || src.endsWith('/storage/') || src.endsWith('/storage') || src.includes('null') || src.includes('undefined')) {
                        img.src = window.NO_IMAGE_FALLBACK;
                    }
                });
            });
        })();
    </script>
</head>
<body class="bg-slate-50 dark:bg-[#030712] text-slate-800 dark:text-slate-100 antialiased lg:pb-0 pb-24">

    <!-- Overall Responsive Container -->
    <div class="desktop-shell min-h-screen flex flex-col lg:flex-row">

        <!-- Mobile Top Header Bar (HP Screen only) -->
        <header class="mobile-header-gradient text-white px-5 py-4 sticky top-0 z-40 lg:hidden shadow-md border-b border-white/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 min-w-0">
                    @if(auth()->user()->student?->photo_url)
                        <img src="{{ auth()->user()->student->photo_url }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-xl object-cover border border-white/20 shrink-0 shadow-sm">
                    @else
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-sm border border-white/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h1 class="text-sm font-bold text-white truncate">@yield('header_title', 'Dashboard')</h1>
                        <p class="text-[11px] text-slate-300 truncate">{{ auth()->user()->name }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2 shrink-0" x-data="{ 
                    isDark: document.documentElement.classList.contains('dark'),
                    toggleTheme() {
                        this.isDark = !this.isDark;
                        if (this.isDark) {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('theme_mode', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('theme_mode', 'light');
                        }
                    }
                }">
                    <button @click="toggleTheme()" type="button" class="p-2 text-slate-300 hover:text-white rounded-xl bg-white/10 border border-white/10 transition-colors" title="Toggle Light/Dark Mode">
                        <svg x-show="!isDark" class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                        <svg x-show="isDark" class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                    </button>
                    <a href="{{ route('student.announcements') }}" class="p-2 text-slate-300 hover:text-white rounded-xl bg-white/10 border border-white/10 transition-colors relative" title="Pengumuman Sekolah">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php
                            $headerAnnounceCount = \App\Models\Announcement::published()->count();
                        @endphp
                        @if($headerAnnounceCount > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white ring-2 ring-indigo-950 animate-pulse">
                                {{ $headerAnnounceCount > 9 ? '9+' : $headerAnnounceCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </header>

        <!-- Desktop Sidebar Navigation (PC Screen only) -->
        <aside class="desktop-sidebar sticky top-0 hidden h-screen w-72 flex-col p-6 text-slate-600 dark:text-slate-200 shrink-0 lg:flex">
            <!-- School Brand Header -->
            <a href="{{ route('home') }}" class="mb-8 flex items-center gap-3.5 group">
                @if(Setting::get('logo_path'))
                    <img src="{{ asset(Setting::get('logo_path')) }}" alt="{{ Setting::get('school_name', 'School') }}" class="h-11 w-11 rounded-2xl bg-slate-50 dark:bg-white/10 p-1 border border-slate-200 dark:border-white/20 shadow-md object-contain group-hover:scale-105 transition-transform">
                @else
                    <img src="{{ asset('img/logo.png') }}" alt="{{ Setting::get('school_name', 'School') }}" class="h-11 w-11 rounded-2xl bg-white p-1 shadow-md object-cover group-hover:scale-105 transition-transform">
                @endif
                <div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white tracking-tight leading-snug">{{ Setting::get('school_name', 'School') }}</h2>
                    <p class="text-[11px] text-indigo-600 dark:text-indigo-300/85 font-extrabold uppercase tracking-wider">Portal Siswa Digital</p>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="space-y-4 flex-1 overflow-y-auto pr-1">
                @php
                    $navGroups = [
                        [
                            'title' => 'Menu Utama',
                            'type' => 'flat',
                            'items' => [
                                ['route' => 'student.dashboard', 'match' => 'student.dashboard', 'label' => 'Dashboard Beranda', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'color' => 'sky'],
                                ['route' => 'student.canteen.index', 'match' => 'student.canteen.*', 'label' => 'E-Kantin Sekolah', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z', 'color' => 'yellow'],
                                ['route' => 'student.announcements', 'match' => 'student.announcements', 'label' => 'Pengumuman Sekolah', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z', 'color' => 'pink'],
                            ]
                        ],
                        [
                            'title' => 'Keuangan Siswa',
                            'type' => 'dropdown',
                            'label' => 'Keuangan & Tabungan',
                            'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            'color' => 'emerald',
                            'matches' => ['student.savings.*', 'student.payments.*'],
                            'items' => [
                                ['route' => 'student.savings.index', 'match' => 'student.savings.*', 'label' => 'Tabungan Siswa', 'icon' => 'M19 5H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zM16 11a2 2 0 1 0 0 4 2 2 0 0 0 0-4z', 'color' => 'teal'],
                                ['route' => 'student.payments.index', 'match' => 'student.payments.*', 'label' => 'Tagihan & SPP', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'rose'],
                            ]
                        ],
                        [
                            'title' => 'Akademik & LMS',
                            'type' => 'dropdown',
                            'label' => 'Ruang Belajar Digital',
                            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                            'color' => 'indigo',
                            'matches' => ['student.lms.*'],
                            'items' => [
                                ['route' => 'student.lms.index', 'match' => 'student.lms.index', 'label' => 'Modul Belajar LMS', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'indigo'],
                                ['route' => 'student.lms.gamification', 'match' => 'student.lms.gamification', 'label' => 'Leaderboard & XP', 'icon' => 'M5 3h14l-1.5 8a4.5 4.5 0 01-4.5 4.5h-2A4.5 4.5 0 016.5 11L5 3zM6 6H3v2a3 3 0 003 3M18 6h3v2a3 3 0 01-3 3M12 15.5V18M9 21h6', 'color' => 'amber'],
                            ]
                        ],
                        [
                            'title' => 'Pelajaran & Tugas',
                            'type' => 'dropdown',
                            'label' => 'Tugas & Jadwal',
                            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                            'color' => 'violet',
                            'matches' => ['student.schedules.*', 'student.materials.*', 'student.assignments.*'],
                            'items' => [
                                ['route' => 'student.schedules.index', 'match' => 'student.schedules.*', 'label' => 'Jadwal Pelajaran', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'emerald'],
                                ['route' => 'student.materials.index', 'match' => 'student.materials.*', 'label' => 'Materi & Modul', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'cyan'],
                                ['route' => 'student.assignments.index', 'match' => 'student.assignments.*', 'label' => 'Tugas & PR', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'lime'],
                            ]
                        ],
                        [
                            'title' => 'Ujian & E-Raport',
                            'type' => 'dropdown',
                            'label' => 'Ujian & Raport',
                            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'color' => 'fuchsia',
                            'matches' => ['student.exams.*', 'student.permits.*', 'student.grades.*', 'student.raport.*'],
                            'items' => [
                                ['route' => 'student.exams.index', 'match' => 'student.exams.*', 'label' => 'Ujian Online CBT', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'fuchsia'],
                                ['route' => 'student.raport.print', 'match' => 'student.raport.*', 'label' => 'E-Raport Siswa', 'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0v6m-6-3h12', 'color' => 'emerald', 'target' => '_blank'],
                                ['route' => 'student.grades.index', 'match' => 'student.grades.*', 'label' => 'Laporan Nilai Siswa', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'teal'],
                                ['route' => 'student.permits.index', 'match' => 'student.permits.*', 'label' => 'Pengajuan Izin / Sakit', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'indigo'],
                            ]
                        ],
                        [
                            'title' => 'Fasilitas & Akun',
                            'type' => 'flat',
                            'items' => [
                                ['route' => 'student.library.index', 'match' => 'student.library.*', 'label' => 'Perpustakaan Digital', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'rose'],
                                ['route' => 'student.profile', 'match' => 'student.profile', 'label' => 'Profil Siswa', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'sky'],
                            ]
                        ],
                    ];
                @endphp

                @foreach($navGroups as $group)
                    @if($group['type'] === 'flat')
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1.5 px-2.5">{{ $group['title'] }}</p>
                            <div class="space-y-1">
                                @foreach($group['items'] as $item)
                                    @php $isActive = request()->routeIs($item['match']); @endphp
                                    <a href="{{ route($item['route']) }}" target="{{ $item['target'] ?? '_self' }}" 
                                       class="desktop-nav-link {{ $isActive ? 'active' : '' }} flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all">
                                        <svg class="w-4 h-4 shrink-0 text-{{ $item['color'] }}-500 dark:text-{{ $item['color'] }}-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                                        </svg>
                                        <span class="truncate">{{ $item['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @elseif($group['type'] === 'dropdown')
                        @php
                            $isGroupActive = false;
                            foreach($group['matches'] as $pattern) {
                                if(request()->routeIs($pattern)) {
                                    $isGroupActive = true;
                                    break;
                                }
                            }
                        @endphp
                        <div x-data="{ open: {{ $isGroupActive ? 'true' : 'false' }} }" class="space-y-1">
                            <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1.5 px-2.5">{{ $group['title'] }}</p>
                            <button type="button" 
                                    @click="open = !open" 
                                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/60 cursor-pointer {{ $isGroupActive ? 'bg-indigo-50/70 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400' : '' }}">
                                <div class="flex items-center space-x-3 truncate">
                                    <svg class="w-4 h-4 shrink-0 text-{{ $group['color'] }}-500 dark:text-{{ $group['color'] }}-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $group['icon'] }}"/>
                                    </svg>
                                    <span class="truncate">{{ $group['label'] }}</span>
                                </div>
                                <svg class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                                 class="pl-3 ml-3 border-l-2 border-slate-200 dark:border-slate-700/80 space-y-1 pt-1">
                                @foreach($group['items'] as $item)
                                    @php $isActive = request()->routeIs($item['match']); @endphp
                                    <a href="{{ route($item['route']) }}" target="{{ $item['target'] ?? '_self' }}" 
                                       class="desktop-nav-link {{ $isActive ? 'active font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50/90 dark:bg-indigo-950/60' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/40' }} flex items-center space-x-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all">
                                        <div class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-indigo-600 dark:bg-indigo-400' : 'bg-slate-300 dark:bg-slate-600' }}"></div>
                                        <span class="truncate">{{ $item['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            <!-- Bottom User Card Sidebar -->
            <div class="mt-auto pt-4 border-t border-slate-200 dark:border-white/10">
                <div class="flex items-center space-x-3 p-2.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200/80 dark:border-white/10">
                    @if(auth()->user()->student?->photo_url)
                        <img src="{{ auth()->user()->student->photo_url }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-xl object-cover border border-indigo-200/20 dark:border-white/20 shrink-0 shadow-sm">
                    @else
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm border border-indigo-200/20 dark:border-white/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 shrink-0">
                        @csrf
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-200 dark:hover:text-rose-450 dark:hover:bg-white/10 rounded-lg transition-colors" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Desktop & Mobile Content Container -->
        <div class="min-w-0 flex-1 flex flex-col min-h-screen">
            <!-- Desktop Top Bar Header (PC Screen only) -->
            <header class="desktop-topbar sticky top-0 z-30 hidden lg:block border-b border-slate-200/80 dark:border-slate-800 px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">@yield('header_title', 'Dashboard')</h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Portal Informasi & Pembelajaran Siswa Resmi</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Desktop Light / Dark Mode Toggle Button -->
                        <div x-data="{ 
                            isDark: document.documentElement.classList.contains('dark'),
                            toggleTheme() {
                                this.isDark = !this.isDark;
                                if (this.isDark) {
                                    document.documentElement.classList.add('dark');
                                    localStorage.setItem('theme_mode', 'dark');
                                } else {
                                    document.documentElement.classList.remove('dark');
                                    localStorage.setItem('theme_mode', 'light');
                                }
                            }
                        }">
                            <button @click="toggleTheme()" type="button" class="p-2 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all border border-slate-200 dark:border-slate-800" title="Switch Light/Dark Mode">
                                <svg x-show="!isDark" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                                <svg x-show="isDark" class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                            </button>
                        </div>

                        <button onclick="window.dispatchEvent(new CustomEvent('open-qr-modal'))" type="button" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-xs font-bold rounded-xl transition-all shadow-md hover:shadow-indigo-500/20 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <span>Scan QR Pay</span>
                        </button>

                        <a href="{{ route('home') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
                            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Web Sekolah</span>
                        </a>

                        <div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="flex items-center space-x-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
                                @if(auth()->user()->student?->photo_url)
                                    <img src="{{ auth()->user()->student->photo_url }}" alt="{{ auth()->user()->name }}" class="w-7 h-7 rounded-lg object-cover shrink-0">
                                @else
                                    <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100 max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition x-cloak class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 z-50 text-xs font-semibold">
                                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700">
                                    <p class="font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('student.profile') }}" class="block px-4 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Profil Saya</a>
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors font-bold">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Single Main Content Area (Rendered Once) -->
            <main class="flex-1 p-4 lg:p-8 w-full max-w-full">
                <!-- Global Toast Alerts -->
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-2.5">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" @click="show = false" class="text-emerald-700 dark:text-emerald-300 hover:text-emerald-900 font-bold">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-semibold flex items-center justify-between shadow-xs">
                        <div class="flex items-center space-x-2.5">
                            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" @click="show = false" class="text-rose-700 dark:text-rose-300 hover:text-rose-900 font-bold">&times;</button>
                    </div>
                @endif

                @if($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-semibold flex items-start justify-between shadow-xs">
                        <div class="flex items-start space-x-2.5">
                            <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="space-y-1">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" @click="show = false" class="text-rose-700 dark:text-rose-300 hover:text-rose-900 font-bold ml-2">&times;</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- Mobile Bottom Navigation (HP Screen only) -->
        <nav class="bottom-nav fixed bottom-0 left-0 right-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-t border-slate-200 dark:border-slate-800 z-40 lg:hidden shadow-lg">
            <div class="grid grid-cols-5 py-1 px-1 text-[10px] font-bold text-slate-500 dark:text-slate-400 text-center items-end">
                
                <a href="{{ route('student.dashboard') }}" 
                   class="flex flex-col items-center py-1 px-1 transition-colors {{ request()->routeIs('student.dashboard') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Beranda</span>
                </a>
                
                <a href="{{ route('student.canteen.index') }}" 
                   class="flex flex-col items-center py-1 px-1 transition-colors relative {{ request()->routeIs('student.canteen.*') ? 'text-yellow-500 dark:text-yellow-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-yellow-500' }}"
                   x-data="{ 
                       cartCount: 0,
                       updateCount() {
                           let c = JSON.parse(localStorage.getItem('student_canteen_cart') || '[]');
                           this.cartCount = c.reduce((sum, item) => sum + (item.qty || 0), 0);
                       }
                   }"
                   x-init="
                       updateCount();
                       window.addEventListener('storage', () => updateCount());
                       setInterval(() => updateCount(), 1000);
                   ">
                    <div class="relative">
                        <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                        <span x-show="cartCount > 0" x-text="cartCount" class="absolute -top-1.5 -right-2.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[9px] font-extrabold leading-none rounded-full ring-2 ring-white dark:ring-slate-900 shadow-sm"></span>
                    </div>
                    <span>E-Kantin</span>
                </a>
 
                <!-- Middle Floating QR Code Scanner Action Button -->
                <div class="flex flex-col items-center -mt-5">
                    <button onclick="window.dispatchEvent(new CustomEvent('open-qr-modal'))" type="button" 
                            class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/40 ring-4 ring-white dark:ring-slate-900 transform active:scale-90 transition-all hover:scale-105">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </button>
                    <span class="text-[9px] font-black text-primary uppercase tracking-wider mt-0.5">QR Pay</span>
                </div>
 
                <a href="{{ route('student.schedules.index') }}" 
                   class="flex flex-col items-center py-1 px-1 transition-colors {{ request()->routeIs('student.schedules.*') || request()->routeIs('student.assignments.*') ? 'text-emerald-500 dark:text-emerald-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-emerald-500' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Jadwal</span>
                </a>
 
                <a href="{{ route('student.profile') }}" 
                   class="flex flex-col items-center py-1 px-1 transition-colors {{ request()->routeIs('student.profile') ? 'text-sky-500 dark:text-sky-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-sky-500' }}">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Profil</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Global QR Code Scanner & Wallet Modal Component -->
    <div x-data="{
            showModal: false,
            activeTab: 'scan', // 'scan', 'input', 'myqr'
            manualQrInput: '',
            isScanning: false,
            scanResult: null,
            scannedOrder: null,
            html5QrCode: null,
            processing: false,
            errorMessage: '',
            
            init() {
                window.addEventListener('open-qr-modal', () => {
                    this.showModal = true;
                    this.errorMessage = '';
                    this.scanResult = null;
                    this.scannedOrder = null;
                    if (this.activeTab === 'scan') {
                        this.$nextTick(() => this.startScanner());
                    }
                });
            },
            closeModal() {
                this.stopScanner();
                this.showModal = false;
            },
            switchTab(tab) {
                this.activeTab = tab;
                this.errorMessage = '';
                if (tab === 'scan') {
                    this.$nextTick(() => this.startScanner());
                } else {
                    this.stopScanner();
                }
            },
            startScanner() {
                if (this.isScanning) return;
                const element = document.getElementById('qr-camera-reader');
                if (!element) return;

                if (!this.html5QrCode) {
                    this.html5QrCode = new Html5Qrcode('qr-camera-reader');
                }

                const config = { fps: 10, qrbox: { width: 220, height: 220 } };

                this.html5QrCode.start(
                    { facingMode: 'environment' },
                    config,
                    (decodedText) => {
                        this.handleDecodedQr(decodedText);
                    },
                    (errorMessage) => {
                        // ignore frame scan errors
                    }
                ).then(() => {
                    this.isScanning = true;
                }).catch(err => {
                    console.warn('Camera access error:', err);
                    this.isScanning = false;
                    this.errorMessage = 'Tidak dapat membuka kamera. Gunakan fitur Input Kode QR atau berikan izin kamera pada browser.';
                });
            },
            stopScanner() {
                if (this.html5QrCode && this.isScanning) {
                    this.html5QrCode.stop().then(() => {
                        this.isScanning = false;
                    }).catch(err => console.log(err));
                }
            },
            submitManual() {
                if (!this.manualQrInput.trim()) return;
                this.handleDecodedQr(this.manualQrInput.trim());
            },
            handleDecodedQr(qrText) {
                this.stopScanner();
                this.processing = true;
                this.errorMessage = '';

                fetch('{{ route("student.canteen.process-qr") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ qr_data: qrText })
                })
                .then(res => res.json())
                .then(data => {
                    this.processing = false;
                    if (data.success) {
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else if (data.type === 'canteen_order') {
                            this.scannedOrder = data.order;
                        } else {
                            this.scanResult = data;
                        }
                    } else {
                        this.errorMessage = data.message || 'QR Code tidak dapat diproses.';
                    }
                })
                .catch(err => {
                    this.processing = false;
                    this.errorMessage = 'Terjadi kesalahan koneksi server.';
                });
            }
        }"
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true">

        <!-- Modal Backdrop -->
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md transition-opacity" @click="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 text-left align-middle shadow-2xl transition-all w-full max-w-md border border-slate-200 dark:border-slate-800">
                
                <!-- Modal Header -->
                <div class="mobile-header-gradient px-6 py-5 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-cyan-300 shadow-inner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold tracking-tight">Center QR Code Hub</h3>
                            <p class="text-xs text-indigo-200">Pembayaran Kantin & Sekolah Digital</p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        &times;
                    </button>
                </div>

                <!-- Tabs Selector -->
                <div class="flex border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850 p-1.5 gap-1 text-xs font-bold">
                    <button @click="switchTab('scan')" :class="activeTab === 'scan' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'" class="flex-1 py-2 rounded-xl transition-all">
                        Scan Kamera
                    </button>
                    <button @click="switchTab('input')" :class="activeTab === 'input' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'" class="flex-1 py-2 rounded-xl transition-all">
                        Input Kode
                    </button>
                    <button @click="switchTab('myqr')" :class="activeTab === 'myqr' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white'" class="flex-1 py-2 rounded-xl transition-all">
                        QR Saya
                    </button>
                </div>

                <!-- Modal Body Content -->
                <div class="p-6">
                    <!-- Loading overlay -->
                    <div x-show="processing" class="py-8 text-center">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Memproses data QR Code...</p>
                    </div>

                    <!-- Error Alert -->
                    <div x-show="errorMessage && !processing" class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="errorMessage"></span>
                    </div>

                    <!-- TAB 1: Scan Kamera -->
                    <div x-show="activeTab === 'scan' && !processing" class="space-y-4">
                        <div class="relative bg-slate-900 rounded-2xl overflow-hidden shadow-inner border border-slate-800 min-h-[240px] flex items-center justify-center">
                            <div id="qr-camera-reader" class="w-full text-white text-xs"></div>
                            
                            <!-- Scanner guide overlay -->
                            <div class="absolute inset-0 pointer-events-none border-2 border-indigo-500/30 rounded-2xl flex items-center justify-center">
                                <div class="w-48 h-48 border-2 border-dashed border-cyan-400/80 rounded-xl animate-pulse"></div>
                            </div>
                        </div>
                        <p class="text-[11px] text-center text-slate-500 dark:text-slate-400">Arahkan kamera ke QR Code transaksi kantin atau QR Code tagihan sekolah.</p>
                    </div>

                    <!-- TAB 2: Input Manual Kode QR -->
                    <div x-show="activeTab === 'input' && !processing" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kode QR / ID Transaksi</label>
                            <input type="text" x-model="manualQrInput" placeholder="Contoh: KTNQR-20260826..." 
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-indigo-500 font-mono">
                        </div>
                        <button type="button" @click="submitManual()" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-md transition-colors">
                            Proses Kode QR
                        </button>
                    </div>

                    <!-- TAB 3: QR Saya (Kartu Siswa & Saldo) -->
                    <div x-show="activeTab === 'myqr' && !processing" class="text-center space-y-4 py-2">
                        @php
                            $studentObj = auth()->user()->student;
                        @endphp

                        @if($studentObj)
                            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-3">
                                <div class="w-36 h-36 bg-white p-2.5 rounded-xl shadow-md mx-auto flex items-center justify-center border border-slate-200">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($studentObj->qr_code) }}" 
                                         alt="QR Code Siswa" class="w-full h-full object-contain">
                                </div>
                                <p class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 tracking-wider">{{ $studentObj->qr_code }}</p>
                                
                                <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Saldo Tabungan Siswa:</span>
                                    <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm">Rp {{ number_format($studentObj->savings_balance ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Tunjukkan QR Code ini kepada Kasir Kantin untuk pembayaran langsung dari Saldo Tabungan Anda.</p>
                        @else
                            <p class="text-xs text-slate-500">Data siswa tidak tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
    @include('components.pwa-install')
</body>
</html>

