<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>@yield('title', 'Dashboard') - {{ Setting::get('school_name', 'School') }}</title>

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
                        primary: '{{ Setting::get('primary_color', '#f59e0b') }}',
                        secondary: '{{ Setting::get('secondary_color', '#d97706') }}',
                        brand: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            600: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            700: '{{ Setting::get('secondary_color', '#d97706') }}',
                            800: '{{ Setting::get('secondary_color', '#d97706') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 95%, black)',
                        },
                        indigo: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            600: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            700: '{{ Setting::get('secondary_color', '#d97706') }}',
                            800: '{{ Setting::get('secondary_color', '#d97706') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 95%, black)',
                        },
                        amber: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            600: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            700: '{{ Setting::get('secondary_color', '#d97706') }}',
                            850: '#111827',
                            950: '#030712',
                        },
                        orange: {
                            50: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 75%, white)',
                            500: '{{ Setting::get('secondary_color', '#d97706') }}',
                            600: '{{ Setting::get('secondary_color', '#d97706') }}',
                            700: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 90%, black)',
                        },
                        blue: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            600: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            700: '{{ Setting::get('secondary_color', '#d97706') }}',
                            800: '{{ Setting::get('secondary_color', '#d97706') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 95%, black)',
                        },
                        purple: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            600: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            700: '{{ Setting::get('secondary_color', '#d97706') }}',
                            800: '{{ Setting::get('secondary_color', '#d97706') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 95%, black)',
                        },
                        violet: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 20%, white)',
                            500: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            600: '{{ Setting::get('primary_color', '#f59e0b') }}',
                            700: '{{ Setting::get('secondary_color', '#d97706') }}',
                        }
                    }
                }
            }
        }
    </script>

    <!-- AlpineJS CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        [x-cloak] { display: none !important; }
        .bottom-nav { padding-bottom: env(safe-area-inset-bottom); }

        .desktop-sidebar {
            background: linear-gradient(165deg, color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 16%, #090d16) 0%, #0f172a 50%, color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 22%, #090d16) 100%);
            box-shadow: inset -1px 0 0 rgba(255,255,255,.08);
        }

        .desktop-nav-link {
            border: 1px solid transparent;
            transition: all .2s ease;
        }

        .desktop-nav-link:hover {
            border-color: rgba(255,255,255,.12);
            background: rgba(255,255,255,.06);
        }

        .desktop-nav-link.active {
            border-color: {{ Setting::get('primary_color', '#f59e0b') }}66;
            background: linear-gradient(90deg, color-mix(in srgb, {{ Setting::get('primary_color', '#f59e0b') }} 35%, transparent), color-mix(in srgb, {{ Setting::get('secondary_color', '#d97706') }} 25%, transparent));
            box-shadow: 0 10px 25px -10px {{ Setting::get('primary_color', '#f59e0b') }}80;
        }

        .desktop-topbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100 text-slate-900 antialiased font-sans" x-data="{ activeTab: '{{ request()->route()->getName() }}' }">
    <!-- Layout Wrapper -->
    <div class="min-h-screen flex flex-col lg:flex-row bg-slate-50">
        
        <!-- Mobile Header (HP Screen only) -->
        <header class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-3.5 sticky top-0 z-40 lg:hidden shadow-md flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold">@yield('header_title', 'Dashboard')</h1>
                <p class="text-xs text-orange-100">{{ auth()->user()->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <button class="relative p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($notificationCount ?? 0 > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                </button>
            </div>
        </header>

        <!-- Desktop Sidebar Navigation (PC Screen only) -->
        <aside class="desktop-sidebar sticky top-0 hidden h-screen w-72 flex-col p-6 text-slate-100 shrink-0 lg:flex">
            <!-- School Brand Header -->
            <a href="{{ route('home') }}" class="mb-8 flex items-center gap-3.5 group">
                @if(Setting::get('school_logo') && Setting::get('school_logo') !== '')
                    <img src="{{ \Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo')) }}" alt="{{ Setting::get('school_name', 'School') }}" class="h-11 w-11 rounded-2xl bg-white p-1 border border-white/20 shadow-lg object-contain group-hover:scale-105 transition-transform">
                @else
                    <img src="{{ asset('img/logo.png') }}" alt="{{ Setting::get('school_name', 'School') }}" class="h-11 w-11 rounded-2xl bg-white p-1 shadow-lg object-cover group-hover:scale-105 transition-transform">
                @endif
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight leading-snug">{{ Setting::get('school_name', 'Sekolah') }}</h2>
                    <p class="text-[10px] text-amber-300/80 font-semibold uppercase tracking-wider">Dashboard Calon Siswa</p>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="space-y-6 flex-1 overflow-y-auto pr-1">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-2 px-2.5">Menu Utama</p>
                    <div class="space-y-1">
                        <a href="{{ route('spmb.dashboard.index') }}" 
                           class="desktop-nav-link {{ request()->routeIs('spmb.dashboard.index') ? 'active text-white' : 'text-slate-300 hover:text-white' }} flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('spmb.dashboard.index') ? 'text-amber-300' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="truncate">Beranda Dashboard</span>
                        </a>

                        <a href="{{ route('spmb.dashboard.edit') }}" 
                           class="desktop-nav-link {{ request()->routeIs('spmb.dashboard.edit') ? 'active text-white' : 'text-slate-300 hover:text-white' }} flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('spmb.dashboard.edit') ? 'text-amber-300' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span class="truncate">Lengkapi Data</span>
                        </a>

                        <a href="{{ route('spmb.dashboard.announcements') }}" 
                           class="desktop-nav-link {{ request()->routeIs('spmb.dashboard.announcements') ? 'active text-white' : 'text-slate-300 hover:text-white' }} flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('spmb.dashboard.announcements') ? 'text-amber-300' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                            <span class="truncate">Pengumuman</span>
                        </a>

                        <a href="{{ route('spmb.dashboard.account') }}" 
                           class="desktop-nav-link {{ request()->routeIs('spmb.dashboard.account') ? 'active text-white' : 'text-slate-300 hover:text-white' }} flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('spmb.dashboard.account') ? 'text-amber-300' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="truncate">Akun Saya</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Bottom User Card Sidebar -->
            <div class="mt-auto pt-4 border-t border-white/10">
                <div class="flex items-center space-x-3 p-2.5 rounded-2xl bg-white/5 border border-white/10">
                    <div class="w-9 h-9 rounded-xl bg-amber-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm border border-white/20">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 shrink-0">
                        @csrf
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-white/10 rounded-lg transition-colors" title="Logout">
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
            <header class="desktop-topbar sticky top-0 z-30 hidden lg:block border-b border-slate-200 px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">@yield('header_title', 'Dashboard')</h1>
                        <p class="text-xs text-slate-500 mt-0.5">Portal Penerimaan Siswa Baru Resmi</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('home') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Web Sekolah</span>
                        </a>

                        <div class="h-6 w-px bg-slate-200"></div>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="flex items-center space-x-2 px-3 py-1.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-xs">
                                <div class="w-7 h-7 rounded-lg bg-amber-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="text-xs font-bold text-slate-800 max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition x-cloak class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-50 text-xs font-semibold">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('spmb.dashboard.edit') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 transition-colors">Lengkapi Data</a>
                                <a href="{{ route('spmb.dashboard.account') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 transition-colors">Pengaturan Akun</a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 transition-colors font-bold">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content (Desktop & Mobile) -->
            <main class="flex-1 p-4 lg:p-8 w-full max-w-full pb-20 lg:pb-8">
                <!-- Global Alerts -->
                @if(session('success'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="mb-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-lg">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0 shadow-md">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium truncate">{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="ml-3 text-green-600 hover:text-green-800 font-bold w-7 h-7 rounded-full hover:bg-green-100 flex items-center justify-center flex-shrink-0 transition">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="mb-4 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-lg">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-rose-500 rounded-lg flex items-center justify-center flex-shrink-0 shadow-md">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium truncate">{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="ml-3 text-red-600 hover:text-red-800 font-bold w-7 h-7 rounded-full hover:bg-red-100 flex items-center justify-center flex-shrink-0 transition">&times;</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- Mobile Bottom Navigation (HP Screen only) -->
        <nav class="bottom-nav fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-50 lg:hidden shadow-lg">
            <div class="flex justify-around items-center py-2">
                <a href="{{ route('spmb.dashboard.index') }}"
                   class="flex flex-col items-center p-2 {{ request()->routeIs('spmb.dashboard.index') ? 'text-amber-600' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-xs mt-1">Home</span>
                </a>

                <a href="{{ route('spmb.dashboard.edit') }}"
                   class="flex flex-col items-center p-2 {{ request()->routeIs('spmb.dashboard.edit') ? 'text-amber-600' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span class="text-xs mt-1">Edit Data</span>
                </a>

                <a href="{{ route('spmb.dashboard.announcements') }}"
                   class="flex flex-col items-center p-2 {{ request()->routeIs('spmb.dashboard.announcements') ? 'text-amber-600' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    <span class="text-xs mt-1">Pengumuman</span>
                </a>

                <a href="{{ route('spmb.dashboard.account') }}"
                   class="flex flex-col items-center p-2 {{ request()->routeIs('spmb.dashboard.account') ? 'text-amber-600' : 'text-gray-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-xs mt-1">Akun</span>
                </a>
            </div>
        </nav>
    </div>

    @stack('scripts')
</body>
</html>
