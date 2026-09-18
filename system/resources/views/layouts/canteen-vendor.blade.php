<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Vendor Kantin') - {{ Setting::get('school_name', 'Sekolah') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="apple-touch-icon" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>


    <!-- TailwindCSS CDN for dynamic theme colors override -->
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
                        }
                    }
                }
            }
        }
    </script>


    <!-- AlpineJS & Custom Styles -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-tap-highlight-color: transparent;
            background:
                radial-gradient(900px 360px at 8% -8%, color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 12%, transparent), transparent 60%),
                radial-gradient(800px 320px at 95% -12%, color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 10%, transparent), transparent 60%),
                #f8fafc;
        }
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
        }

        html.dark body {
            background:
                radial-gradient(900px 360px at 8% -8%, color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 18%, transparent), transparent 60%),
                radial-gradient(800px 320px at 95% -12%, color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 15%, transparent), transparent 60%),
                #030712 !important;
            color: #f8fafc;
        }

        /* Comprehensive Dark Mode Overrides for Canteen Pages */
        html.dark .bg-white {
            background-color: #0F172A !important;
            border-color: #1E293B !important;
            color: #F8FAFC !important;
        }

        html.dark .bg-slate-50,
        html.dark .bg-slate-100 {
            background-color: #1E293B !important;
            border-color: #334155 !important;
            color: #F8FAFC !important;
        }

        html.dark .text-slate-900,
        html.dark .text-slate-800,
        html.dark .text-slate-700 {
            color: #F8FAFC !important;
        }

        html.dark .text-slate-600,
        html.dark .text-slate-500,
        html.dark .text-slate-400 {
            color: #94A3B8 !important;
        }

        html.dark .border-slate-100,
        html.dark .border-slate-200,
        html.dark .border-slate-300 {
            border-color: #1E293B !important;
        }
    </style>
</head>
<body class="text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Desktop Left Sidebar (Visible on Desktop PC lg:flex) -->
    <aside class="hidden lg:flex flex-col w-64 fixed inset-y-0 left-0 bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800/80 z-40 no-print transition-colors">
        <!-- Sidebar Brand Header -->
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center space-x-3">
            <a href="{{ route('canteen.vendor.dashboard') }}" class="w-10 h-10 rounded-2xl bg-primary text-white flex items-center justify-center font-black text-lg shadow-md shadow-primary/20 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </a>
            <div class="min-w-0 flex-1">
                <h1 class="text-sm font-extrabold text-slate-900 dark:text-white leading-tight truncate">
                    Vendor Kantin
                </h1>
                <div class="flex items-center space-x-1.5 mt-0.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                        {{ Setting::get('school_name', 'E-Kantin Sekolah') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation List -->
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-1 scrollbar-none">
            <p class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Menu Utama</p>
            
            <a href="{{ route('canteen.vendor.dashboard') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('canteen.vendor.dashboard') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('canteen.vendor.dashboard') ? '' : 'text-sky-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('canteen.vendor.orders') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('canteen.vendor.orders*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('canteen.vendor.orders*') ? '' : 'text-rose-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="flex-1">Kelola Pesanan</span>
                @if(!empty($vendorPendingOrderCount) && $vendorPendingOrderCount > 0)
                    <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[9px] font-black shadow-sm animate-pulse {{ request()->routeIs('canteen.vendor.orders*') ? 'bg-white/30 text-white' : '' }}">
                        {{ $vendorPendingOrderCount > 99 ? '99+' : $vendorPendingOrderCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('canteen.vendor.pos') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('canteen.vendor.pos*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('canteen.vendor.pos*') ? '' : 'text-emerald-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Kasir POS</span>
            </a>

            <p class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 pt-4 mb-2">Katalog &amp; Stok</p>

            <a href="{{ route('canteen.vendor.products') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('canteen.vendor.products*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('canteen.vendor.products*') ? '' : 'text-yellow-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span>Produk Menu</span>
            </a>

            <a href="{{ route('canteen.vendor.categories') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('canteen.vendor.categories*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('canteen.vendor.categories*') ? '' : 'text-fuchsia-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 11h.01M7 15h.01M11 7h8M11 11h8M11 15h8"/></svg>
                <span>Kategori Menu</span>
            </a>

            <a href="{{ route('canteen.vendor.stock') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('canteen.vendor.stock*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('canteen.vendor.stock*') ? '' : 'text-cyan-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Manajemen Stok</span>
            </a>

            <p class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 pt-4 mb-2">Laporan &amp; Profil</p>

            <a href="{{ route('canteen.vendor.reports') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('canteen.vendor.reports*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('canteen.vendor.reports*') ? '' : 'text-teal-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Laporan Penjualan</span>
            </a>

            <a href="{{ route('canteen.vendor.finance') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('canteen.vendor.finance*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('canteen.vendor.finance*') ? '' : 'text-emerald-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Keuangan &amp; Saldo</span>
            </a>

            <a href="{{ route('canteen.vendor.profile') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('canteen.vendor.profile*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('canteen.vendor.profile*') ? '' : 'text-pink-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Profil Stand Toko</span>
            </a>
        </div>

        <!-- Sidebar Footer Action (Quick QR Scan & User Profile) -->
        <div class="p-4 border-t border-slate-100 dark:border-slate-800/80 space-y-3">
            <button onclick="window.dispatchEvent(new CustomEvent('open-vendor-qr-modal'))" type="button" 
                    class="w-full py-2.5 px-4 rounded-2xl bg-primary/10 hover:bg-primary/20 text-primary font-extrabold text-xs flex items-center justify-center space-x-2 border border-primary/20 transition-all shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span>Scan QR Pesanan</span>
            </button>

            <div class="pt-2 flex items-center justify-between">
                <div class="flex items-center space-x-2 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-extrabold text-xs flex items-center justify-center shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'V', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Vendor' }}</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Penjual Kantin</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition-all" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper (With Desktop Sidebar Margin lg:ml-64) -->
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen min-w-0">
        
        <!-- Top App Header Bar -->
        <header class="sticky top-0 z-30 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 shadow-xs no-print transition-colors">
            <div class="w-full px-4 sm:px-6 lg:px-8 h-14 sm:h-16 flex items-center justify-between gap-2">
                
                <!-- Left Mobile Branding / Page Title -->
                <div class="flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <a href="{{ route('canteen.vendor.dashboard') }}" class="lg:hidden w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-primary text-white flex items-center justify-center font-black text-base shadow-md shadow-primary/20 transform active:scale-95 transition-all shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </a>
                    <div class="min-w-0">
                        <h1 class="text-xs sm:text-base font-extrabold text-slate-900 dark:text-white leading-tight truncate">
                            @yield('header_title', 'Panel Vendor Kantin')
                        </h1>
                        <div class="flex items-center space-x-1.5 mt-0.5">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                            <span class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                                {{ Setting::get('school_name', 'E-Kantin Sekolah') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Actions (Theme Switcher, Desktop Scan Button & Logout) -->
                <div class="flex items-center space-x-1.5 sm:space-x-2 shrink-0 ml-auto">
                    <!-- Desktop Quick Scan Button -->
                    <button onclick="window.dispatchEvent(new CustomEvent('open-vendor-qr-modal'))" type="button" 
                            class="hidden lg:flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-primary/10 hover:bg-primary/20 text-primary text-xs font-extrabold transition-all border border-primary/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        <span>Scan QR</span>
                    </button>

                    <!-- Theme Switcher -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" 
                            type="button" 
                            class="p-2 sm:p-2.5 rounded-2xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all border border-slate-200/60 dark:border-slate-800/60"
                            title="Mode Gelap/Terang">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>


                    <!-- Mobile Logout Button -->
                    <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                        @csrf
                        <button type="submit" 
                                class="p-2 sm:p-2.5 rounded-2xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-slate-200/60 dark:border-slate-800/60 transition-all flex items-center space-x-1" 
                                title="Keluar Akun">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="hidden sm:inline text-xs font-bold">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Mobile & Desktop Content Area -->
        <main class="flex-1 w-full p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
            
            <!-- Flash Alert Banner -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition.out.opacity.duration.300ms class="mb-5 p-4 rounded-2xl bg-emerald-500/10 dark:bg-emerald-950/60 border border-emerald-500/30 text-emerald-800 dark:text-emerald-200 text-xs font-bold flex items-center justify-between shadow-xs">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 font-bold text-base">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition.out.opacity.duration.300ms class="mb-5 p-4 rounded-2xl bg-rose-500/10 dark:bg-rose-950/60 border border-rose-500/30 text-rose-800 dark:text-rose-200 text-xs font-bold flex items-center justify-between shadow-xs">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 font-bold text-base">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar (Visible on Mobile lg:hidden) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-t border-slate-200 dark:border-slate-800 z-40 shadow-lg no-print transition-colors">
        <div class="max-w-md mx-auto grid grid-cols-5 py-1 px-1 text-[10px] font-bold text-slate-500 dark:text-slate-400 text-center items-end">
            
            <!-- Dashboard Tab -->
            <a href="{{ route('canteen.vendor.dashboard') }}" 
               class="flex flex-col items-center py-1 px-1 transition-colors {{ request()->routeIs('canteen.vendor.dashboard') ? 'text-primary font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-5 h-5 mb-0.5 {{ request()->routeIs('canteen.vendor.dashboard') ? '' : 'text-sky-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Beranda</span>
            </a>

            <!-- Orders Tab -->
            <a href="{{ route('canteen.vendor.orders') }}" 
               class="relative flex flex-col items-center py-1 px-1 transition-colors {{ request()->routeIs('canteen.vendor.orders*') ? 'text-primary font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="relative">
                    <svg class="w-5 h-5 mb-0.5 {{ request()->routeIs('canteen.vendor.orders*') ? '' : 'text-rose-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    @if(!empty($vendorPendingOrderCount) && $vendorPendingOrderCount > 0)
                        <span class="absolute -top-1 -right-1.5 inline-flex items-center justify-center min-w-[14px] h-[14px] px-0.5 rounded-full bg-rose-500 text-white text-[8px] font-black shadow animate-pulse">
                            {{ $vendorPendingOrderCount > 9 ? '9+' : $vendorPendingOrderCount }}
                        </span>
                    @endif
                </div>
                <span>Pesanan</span>
            </a>

            <!-- Middle Floating QR Scanner Action Button -->
            <div class="flex flex-col items-center -mt-5">
                <button onclick="window.dispatchEvent(new CustomEvent('open-vendor-qr-modal'))" type="button" 
                        class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/30 ring-4 ring-white dark:ring-slate-900 transform active:scale-90 transition-all hover:scale-105">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </button>
                <span class="text-[9px] font-black text-primary uppercase tracking-wider mt-0.5">Scan QR</span>
            </div>

            <!-- Products Tab -->
            <a href="{{ route('canteen.vendor.products') }}" 
               class="flex flex-col items-center py-1 px-1 transition-colors {{ request()->routeIs('canteen.vendor.products*') ? 'text-primary font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-5 h-5 mb-0.5 {{ request()->routeIs('canteen.vendor.products*') ? '' : 'text-yellow-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span>Produk</span>
            </a>

            <!-- Stall Profile Tab -->
            <a href="{{ route('canteen.vendor.profile') }}" 
               class="flex flex-col items-center py-1 px-1 transition-colors {{ request()->routeIs('canteen.vendor.profile*') ? 'text-primary font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-5 h-5 mb-0.5 {{ request()->routeIs('canteen.vendor.profile*') ? '' : 'text-pink-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Toko</span>
            </a>

        </div>
    </nav>

    <!-- Global Vendor QR Code Scanner Hub Modal -->
    <div x-data="{
            showModal: false,
            activeTab: 'scan', // 'scan', 'input'
            manualQrInput: '',
            isScanning: false,
            scannedOrder: null,
            html5QrCode: null,
            processing: false,
            errorMessage: '',
            
            init() {
                window.addEventListener('open-vendor-qr-modal', () => {
                    this.showModal = true;
                    this.errorMessage = '';
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
                const element = document.getElementById('vendor-qr-camera-reader');
                if (!element) return;

                if (!this.html5QrCode) {
                    this.html5QrCode = new Html5Qrcode('vendor-qr-camera-reader');
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
                this.scannedOrder = null;

                fetch('{{ route("canteen.vendor.verify-qr") }}', {
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
                        this.scannedOrder = data.order;
                    } else {
                        this.errorMessage = data.message || 'QR Code tidak dapat diverifikasi.';
                    }
                })
                .catch(err => {
                    this.processing = false;
                    this.errorMessage = 'Terjadi kesalahan koneksi server.';
                });
            },
            updateStatus(newStatus) {
                if (!this.scannedOrder) return;
                this.processing = true;

                fetch('{{ url('canteen-vendor/orders') }}/' + this.scannedOrder.id + '/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ order_status: newStatus })
                })
                .then(res => res.json())
                .then(data => {
                    this.processing = false;
                    if (data.success) {
                        this.handleDecodedQr(this.scannedOrder.order_number);
                    }
                })
                .catch(err => {
                    this.processing = false;
                });
            }
        }"
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
        role="dialog"
        aria-modal="true">

        <!-- Modal Backdrop -->
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md transition-opacity" @click="closeModal()"></div>

        <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 text-left align-middle shadow-2xl transition-all w-full max-w-md mx-4 sm:mx-auto border border-slate-200 dark:border-slate-800 my-auto z-10 max-h-[90vh] overflow-y-auto scrollbar-none">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 text-white flex items-center justify-between relative overflow-hidden" style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#6366f1') }} 0%, {{ Setting::get('secondary_color', '#4f46e5') }} 100%);">
                    <!-- Background Pattern -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-8 translate-x-8 blur-sm pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full translate-y-6 -translate-x-6 pointer-events-none"></div>

                    <div class="relative z-10 flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur border border-white/25 flex items-center justify-center text-white shadow-inner">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black tracking-tight leading-none mb-1">Scanner QR Code Vendor</h3>
                            <p class="text-[10px] text-white/80 font-medium">Verifikasi & Konfirmasi Pesanan Siswa</p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()" class="relative z-10 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
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
                </div>

                <!-- Modal Body Content -->
                <div class="p-6">
                    <!-- Loading overlay -->
                    <div x-show="processing" class="py-8 text-center">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Memproses Verifikasi QR Code...</p>
                    </div>

                    <!-- Error Alert -->
                    <div x-show="errorMessage && !processing" class="mb-4 p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="errorMessage"></span>
                    </div>

                    <!-- Tab 1: Scan Kamera -->
                    <div x-show="activeTab === 'scan' && !processing" class="space-y-4 text-center">
                        <div class="overflow-hidden rounded-2xl border-2 border-indigo-500/50 bg-slate-950 p-2 shadow-inner">
                            <div id="vendor-qr-camera-reader" class="w-full"></div>
                        </div>
                        <p class="text-[11px] text-slate-400">Arahkan kamera ke QR Code pesanan pada smartphone siswa.</p>
                    </div>

                    <!-- Tab 2: Input Kode Manual -->
                    <div x-show="activeTab === 'input' && !processing" class="space-y-4">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Nomor Pesanan / Kode QR</label>
                            <input type="text" x-model="manualQrInput" @keydown.enter.prevent="submitManual()" placeholder="Contoh: KTNQR-xxx atau KTN-xxx" class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 font-mono font-bold text-xs text-center text-slate-900 dark:text-white">
                        </div>
                        <button type="button" @click="submitManual()" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl text-xs shadow-md transition-all">
                            Verifikasi Kode
                        </button>
                    </div>

                    <!-- Verification Order Details Result Card -->
                    <div x-show="scannedOrder && !processing" class="mt-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-3">
                        <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200 dark:border-slate-700">
                            <span class="font-mono font-extrabold text-indigo-600 dark:text-indigo-400" x-text="scannedOrder?.order_number"></span>
                            <div x-html="scannedOrder?.order_status_badge"></div>
                        </div>

                        <div class="text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pemesan:</span>
                                <span class="font-bold text-slate-900 dark:text-white" x-text="scannedOrder?.student_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Kelas:</span>
                                <span class="font-bold text-slate-900 dark:text-white" x-text="scannedOrder?.student_class"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Nominal:</span>
                                <span class="font-black text-emerald-600 dark:text-emerald-400" x-text="scannedOrder?.total_formatted"></span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200 dark:border-slate-700">
                            <template x-if="scannedOrder?.order_status === 'pending'">
                                <button type="button" @click="updateStatus('processing')" class="w-full py-2.5 bg-amber-500 text-white font-bold text-xs rounded-xl shadow-xs">
                                    Mulai Proses Pesanan
                                </button>
                            </template>

                            <template x-if="scannedOrder?.order_status === 'processing'">
                                <button type="button" @click="updateStatus('ready')" class="w-full py-2.5 bg-blue-600 text-white font-bold text-xs rounded-xl shadow-xs">
                                    Tandai Siap Diambil
                                </button>
                            </template>

                            <template x-if="scannedOrder?.order_status === 'ready'">
                                <button type="button" @click="updateStatus('completed')" class="w-full py-2.5 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow-xs">
                                    Konfirmasi & Serahkan Makanan (Selesai)
                                </button>
                            </template>

                            <template x-if="scannedOrder?.order_status === 'completed'">
                                <div class="p-2 bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-center font-extrabold text-xs rounded-xl">
                                    ✓ Pesanan Selesai & Lunas
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
