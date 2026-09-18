<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal Orang Tua - {{ $student->user->name }} | {{ Setting::get('school_name', 'Sekolah') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="apple-touch-icon" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">

    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
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
                            705: '{{ Setting::get('secondary_color', '#2563eb') }}',
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
                            505: '{{ Setting::get('primary_color', '#3C50E0') }}',
                            500: '{{ Setting::get('primary_color', '#3C50E0') }}',
                            600: '{{ Setting::get('primary_color', '#3C50E0') }}',
                            700: '{{ Setting::get('secondary_color', '#2563eb') }}',
                            800: '{{ Setting::get('secondary_color', '#2563eb') }}',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#2563eb') }} 95%, black)',
                        }
                    }
                }
            }
        }
    </script>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased selection:bg-indigo-500 selection:text-white" 
      x-data="{ activeTab: 'dashboard', showCardModal: false, selectedScheduleDay: '{{ Carbon\Carbon::today()->format('l') }}' }">

    <!-- Desktop Left Sidebar (Visible on Desktop PC lg:flex) -->
    <aside class="hidden lg:flex flex-col w-64 fixed inset-y-0 left-0 bg-white dark:bg-slate-900 border-r border-slate-200/80 dark:border-slate-800/80 z-40 no-print transition-colors">
        <!-- Sidebar Brand Header -->
        <div class="p-5 border-b border-slate-100 dark:border-slate-800/80 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center font-black text-lg shadow-md shadow-indigo-500/20 shrink-0">
                @if(Setting::get('logo_path'))
                    <img src="{{ asset(Setting::get('logo_path')) }}" alt="Logo" class="w-full h-full object-contain p-1">
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01-6.824 2.998L12 14z"/>
                    </svg>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="text-sm font-black text-slate-900 dark:text-white leading-tight truncate">
                    Portal Orang Tua
                </h1>
                <div class="flex items-center space-x-1.5 mt-0.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                        {{ Setting::get('school_name', 'Sekolah Indonesia') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Student Identity Brief Card -->
        <div class="p-4 mx-4 mt-4 rounded-2xl bg-indigo-50/80 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/60 flex items-center space-x-3">
            @if($student?->photo_url)
                <img src="{{ $student->photo_url }}" alt="{{ $student->user->name ?? '' }}" class="w-10 h-10 rounded-xl object-cover border border-indigo-200 shadow-xs shrink-0">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->user->name) }}&background=6366f1&color=ffffff&size=120" alt="{{ $student->user->name }}" class="w-10 h-10 rounded-xl object-cover border border-indigo-200 shadow-xs shrink-0">
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-xs font-black text-slate-900 dark:text-white truncate">{{ $student->user->name }}</p>
                <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 truncate">{{ $student->class?->name ?? 'Siswa' }} &bull; NISN: {{ $student->nisn ?? '-' }}</p>
            </div>
        </div>

        <!-- Sidebar Navigation List -->
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-1 scrollbar-none">
            <p class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">Navigasi Monitoring</p>

            <button type="button" @click="activeTab = 'dashboard'" 
                    :class="activeTab === 'dashboard' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-bold'" 
                    class="w-full flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs transition-all cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Ringkasan Dashboard</span>
            </button>

            <button type="button" @click="activeTab = 'attendance'" 
                    :class="activeTab === 'attendance' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-bold'" 
                    class="w-full flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs transition-all cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Presensi Kehadiran</span>
            </button>

            <button type="button" @click="activeTab = 'grades'" 
                    :class="activeTab === 'grades' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-bold'" 
                    class="w-full flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs transition-all cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Nilai Akademis</span>
            </button>

            <button type="button" @click="activeTab = 'payments'" 
                    :class="activeTab === 'payments' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-bold'" 
                    class="w-full flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs transition-all cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Tagihan & Keuangan</span>
            </button>

            <button type="button" @click="activeTab = 'schedules'" 
                    :class="activeTab === 'schedules' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-bold'" 
                    class="w-full flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs transition-all cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Jadwal Pelajaran</span>
            </button>

            <button type="button" @click="activeTab = 'announcements'" 
                    :class="activeTab === 'announcements' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white font-bold'" 
                    class="w-full flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs transition-all cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>Pengumuman Sekolah</span>
            </button>

            <p class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 pt-4 mb-2">Identitas & Fitur</p>

            <button type="button" @click="showCardModal = true" 
                    class="w-full flex items-center space-x-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 transition-all cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3m-3 3h3m-3 3h3M4 6h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm2 4a2 2 0 100 4 2 2 0 000-4zm-1 6a3 3 0 016 0H5z"/></svg>
                <span>Kartu Tanda Siswa (KTS)</span>
            </button>
        </div>

        <!-- Sidebar Footer Action (WhatsApp Contact & Logout) -->
        <div class="p-4 border-t border-slate-100 dark:border-slate-800/80 space-y-3">
            <a href="https://wa.me/{{ Setting::get('phone', '628123456789') }}?text=Halo%20Admin%20Sekolah,%20saya%20orang%20tua%20dari%20{{ urlencode($student->user->name) }}" target="_blank" 
               class="w-full py-2.5 px-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 font-extrabold text-xs flex items-center justify-center space-x-2 border border-emerald-200/60 dark:border-emerald-800/40 transition-all shadow-2xs">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.705 1.754zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                <span>Kontak Sekolah via WA</span>
            </a>

            <form method="POST" action="{{ route('parent.logout') }}">
                @csrf
                <button type="submit" class="w-full py-2 px-3 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 text-xs font-bold transition-all flex items-center justify-center space-x-2" title="Keluar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar Akun Portal</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper (With Desktop Sidebar Margin lg:ml-64) -->
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen min-w-0">

        <!-- Top App Header Bar -->
        <header class="sticky top-0 z-30 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 shadow-xs no-print transition-colors">
            <div class="w-full px-3.5 sm:px-6 lg:px-8 h-14 sm:h-16 flex items-center justify-between gap-2">
                
                <!-- Left Branding / Mobile Header -->
                <div class="flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <div class="lg:hidden w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 text-white flex items-center justify-center font-black text-sm shadow-md shadow-indigo-500/20 shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xs sm:text-base font-extrabold text-slate-900 dark:text-white leading-tight truncate">
                            Portal Orang Tua - <span class="text-indigo-600 dark:text-indigo-400">{{ $student->user->name }}</span>
                        </h1>
                        <div class="flex items-center space-x-1.5 mt-0.5">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                            <span class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                                {{ Setting::get('school_name', 'Sekolah Indonesia') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-1.5 sm:space-x-2 shrink-0 ml-auto">
                    <!-- Theme Switcher -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light')" 
                            type="button" 
                            class="p-2 sm:p-2.5 rounded-2xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all border border-slate-200/60 dark:border-slate-800/60"
                            title="Mode Gelap/Terang">
                        <svg x-show="!darkMode" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="w-4 h-4 sm:w-5 sm:h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>

                    <!-- Mobile Logout Button -->
                    <form method="POST" action="{{ route('parent.logout') }}" class="lg:hidden">
                        @csrf
                        <button type="submit" 
                                class="p-2 sm:p-2.5 rounded-2xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-slate-200/60 dark:border-slate-800/60 transition-all flex items-center space-x-1" 
                                title="Keluar Akun">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Body Content Area -->
        <main class="flex-1 w-full p-3.5 sm:p-6 lg:p-8 pb-28 lg:pb-8">
            
            <!-- TAB 0: DASHBOARD / RINGKASAN VIEW (Visible ONLY when activeTab === 'dashboard') -->
            <div x-show="activeTab === 'dashboard'" class="space-y-6">
                <!-- 2-Column Responsive Layout on Desktop PC -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
                    
                    <!-- Left Profile Summary & Saku Cards (lg:col-span-4) -->
                    <div class="lg:col-span-4 space-y-4 sm:space-y-5">
                        
                        <!-- Student Hero Banner Card -->
                        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-5 sm:p-6 border border-slate-800 shadow-xl space-y-4 sm:space-y-5">
                            <div class="absolute -top-16 -right-16 w-64 h-64 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

                            <div class="relative z-10 flex flex-col items-center text-center space-y-3">
                                <div class="relative shrink-0">
                                    @if($student?->photo_url)
                                        <img src="{{ $student->photo_url }}" alt="{{ $student->user->name ?? '' }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl object-cover border-4 border-white/30 shadow-2xl bg-slate-800">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->user->name) }}&background=6366f1&color=ffffff&size=250" alt="{{ $student->user->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl object-cover border-4 border-white/30 shadow-2xl">
                                    @endif
                                    <span class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 border-2 border-slate-900 rounded-full flex items-center justify-center shadow-md" title="Siswa Aktif">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </div>

                                <div class="space-y-1.5 w-full">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 text-[10px] font-extrabold uppercase tracking-widest rounded-full border border-emerald-400/30">
                                            Siswa Terdaftar
                                        </span>

                                        <!-- Today's Attendance Live Pill Badge -->
                                        @if($todayAttendance)
                                            @if($todayAttendance->status === 'present')
                                                <span class="px-2.5 py-0.5 bg-emerald-500/30 border border-emerald-400/50 text-emerald-200 text-[10px] font-bold rounded-full flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                                    <span>Hadir ({{ $todayAttendance->time_in ?? '07:00' }})</span>
                                                </span>
                                            @elseif($todayAttendance->status === 'late')
                                                <span class="px-2.5 py-0.5 bg-amber-500/30 border border-amber-400/50 text-amber-200 text-[10px] font-bold rounded-full">
                                                    Terlambat ({{ $todayAttendance->time_in ?? '-' }})
                                                </span>
                                            @else
                                                <span class="px-2.5 py-0.5 bg-blue-500/30 border border-blue-400/50 text-blue-200 text-[10px] font-bold rounded-full">
                                                    {{ $todayAttendance->status === 'sick' ? 'Sakit' : 'Izin' }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="px-2.5 py-0.5 bg-slate-500/20 border border-slate-400/30 text-slate-300 text-[10px] font-semibold rounded-full">
                                                Belum Absen Hari Ini
                                            </span>
                                        @endif
                                    </div>

                                    <h2 class="text-lg sm:text-xl font-black text-white leading-tight truncate">{{ $student->user->name }}</h2>
                                    
                                    <div class="flex items-center justify-center gap-2 text-xs text-indigo-200 font-medium flex-wrap">
                                        <span>NISN: <strong class="text-white font-mono font-bold">{{ $student->nisn ?? '-' }}</strong></span>
                                        <span>&bull;</span>
                                        <span>NIK: <strong class="text-white font-mono font-bold">{{ $student->nik ?? '-' }}</strong></span>
                                    </div>

                                    <div class="flex items-center justify-center gap-1.5 pt-1.5 flex-wrap text-xs">
                                        <span class="px-2.5 py-1 bg-white/10 rounded-xl border border-white/15 font-bold text-slate-100 shadow-2xs">
                                            Kelas: {{ $student->class?->name ?? 'Belum ada kelas' }}
                                        </span>
                                        @if($student->major)
                                            <span class="px-2.5 py-1 bg-white/10 rounded-xl border border-white/15 font-bold text-slate-100 shadow-2xs">
                                                Jurusan: {{ $student->major->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button Row -->
                            <div class="relative z-10 pt-3 border-t border-white/10 grid grid-cols-2 gap-2">
                                <button type="button" @click="showCardModal = true" class="inline-flex items-center justify-center px-3 py-2 bg-indigo-600/90 hover:bg-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-all gap-1.5 border border-indigo-400/40 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3m-3 3h3m-3 3h3M4 6h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm2 4a2 2 0 100 4 2 2 0 000-4zm-1 6a3 3 0 016 0H5z"/></svg>
                                    <span>KTS Digital</span>
                                </button>

                                <a href="https://wa.me/{{ Setting::get('phone', '628123456789') }}?text=Halo%20Admin%20Sekolah,%20saya%20orang%20tua%20dari%20{{ urlencode($student->user->name) }}" target="_blank" 
                                   class="inline-flex items-center justify-center px-3 py-2 bg-emerald-600/90 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl shadow-md transition-all gap-1.5 border border-emerald-400/40 cursor-pointer">
                                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.705 1.754zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                    <span>WhatsApp WA</span>
                                </a>
                            </div>
                        </div>

                        <!-- Saldo Tabungan / Saku Card -->
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-9 h-9 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-extrabold text-slate-900 dark:text-white">Saldo Tabungan / Saku</h3>
                                        <p class="text-[10px] text-slate-400">Dompet Digital Siswa</p>
                                    </div>
                                </div>
                                <span class="text-base sm:text-lg font-black text-emerald-600 dark:text-emerald-400 font-mono">
                                    Rp {{ number_format($student->savings_balance ?? 0, 0, ',', '.') }}
                                </span>
                            </div>

                            @if(isset($savingsTransactions) && $savingsTransactions->count() > 0)
                                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Riwayat Saku Terakhir</span>
                                    <div class="space-y-1.5 max-h-44 overflow-y-auto">
                                        @foreach($savingsTransactions->take(5) as $trx)
                                            <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                                                <div class="min-w-0 pr-2">
                                                    <p class="font-bold text-slate-800 dark:text-slate-200 truncate text-[11px]">{{ $trx->description ?? 'Transaksi Saku' }}</p>
                                                    <p class="text-[9px] text-slate-400">{{ $trx->created_at ? $trx->created_at->format('d/m H:i') : '-' }}</p>
                                                </div>
                                                <span class="font-mono font-bold text-xs shrink-0 {{ $trx->type === 'credit' || $trx->type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                    {{ $trx->type === 'credit' || $trx->type === 'deposit' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Quick Menu Grid Box (Menu Kotak-Kotak Navigasi Parent) -->
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3.5">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <h3 class="font-black text-slate-900 dark:text-white text-xs sm:text-sm uppercase tracking-wider">Menu Utama Portal</h3>
                                    <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium">Akses cepat menu data siswa</p>
                                </div>
                                <span class="px-2.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 text-[10px] font-extrabold rounded-full border border-indigo-200/60 dark:border-indigo-800/40">
                                    Menu Navigasi
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-2 sm:gap-2.5">
                                <!-- 1. Presensi -->
                                <button type="button" @click="activeTab = 'attendance'" class="flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30 transition-all cursor-pointer group text-center">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform shadow-2xs border border-emerald-200/60 dark:border-emerald-800/40">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Presensi</span>
                                </button>

                                <!-- 2. Nilai -->
                                <button type="button" @click="activeTab = 'grades'" class="flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/30 transition-all cursor-pointer group text-center">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-indigo-100/80 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform shadow-2xs border border-indigo-200/60 dark:border-indigo-800/40">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Nilai</span>
                                </button>

                                <!-- 3. Tagihan -->
                                <button type="button" @click="activeTab = 'payments'" class="flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/50 dark:hover:bg-amber-950/30 transition-all cursor-pointer group text-center">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-amber-100/80 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform shadow-2xs border border-amber-200/60 dark:border-amber-800/40">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Tagihan</span>
                                </button>

                                <!-- 4. Jadwal -->
                                <button type="button" @click="activeTab = 'schedules'" class="flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 hover:border-purple-400 dark:hover:border-purple-500 hover:bg-purple-50/50 dark:hover:bg-purple-950/30 transition-all cursor-pointer group text-center">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-purple-100/80 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform shadow-2xs border border-purple-200/60 dark:border-purple-800/40">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Jadwal</span>
                                </button>

                                <!-- 5. Pengumuman -->
                                <button type="button" @click="activeTab = 'announcements'" class="flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50/50 dark:hover:bg-blue-950/30 transition-all cursor-pointer group text-center">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-blue-100/80 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform shadow-2xs border border-blue-200/60 dark:border-blue-800/40">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Informasi</span>
                                </button>

                                <!-- 6. KTS Digital -->
                                <button type="button" @click="showCardModal = true" class="flex flex-col items-center justify-center p-2.5 sm:p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-800 hover:border-rose-400 dark:hover:border-rose-500 hover:bg-rose-50/50 dark:hover:bg-rose-950/30 transition-all cursor-pointer group text-center">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-rose-100/80 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform shadow-2xs border border-rose-200/60 dark:border-rose-800/40">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3m-3 3h3m-3 3h3M4 6h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm2 4a2 2 0 100 4 2 2 0 000-4zm-1 6a3 3 0 016 0H5z"/></svg>
                                    </div>
                                    <span class="text-[11px] font-extrabold text-slate-800 dark:text-slate-200 group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">Kartu KTS</span>
                                </button>
                            </div>
                        </div>

                        <!-- Kontak Informasi Sekolah -->
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
                            <h3 class="text-xs font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Layanan Kontak Sekolah</span>
                            </h3>

                            <div class="space-y-2 text-xs">
                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-300">
                                    <span class="text-slate-400">Telepon:</span>
                                    <span class="font-bold">{{ Setting::get('phone', '-') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-300">
                                    <span class="text-slate-400">Email:</span>
                                    <span class="font-bold">{{ Setting::get('email', '-') }}</span>
                                </div>
                                <div class="flex items-start justify-between text-slate-600 dark:text-slate-300">
                                    <span class="text-slate-400 shrink-0">Alamat:</span>
                                    <span class="font-medium text-right ml-2 text-[11px]">{{ Setting::get('address', 'Sekolah') }}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Content Section (lg:col-span-8) -->
                    <div class="lg:col-span-8 space-y-5">

                        <!-- 4 Overview Metric Cards (2x2 Grid on Mobile HP, 4-Cols on PC Desktop) -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                            <!-- Presensi -->
                            <div @click="activeTab = 'attendance'" class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between gap-2 cursor-pointer hover:shadow-md transition-all">
                                <div class="min-w-0">
                                    <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Kehadiran</p>
                                    <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mt-0.5 font-mono">{{ $attendanceStats['percentage'] }}%</h3>
                                    <p class="text-[9px] sm:text-[10px] text-emerald-600 dark:text-emerald-400 font-extrabold truncate mt-0.5">{{ $attendanceStats['present'] }} Hari Hadir</p>
                                </div>
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>

                            <!-- Nilai Rapor -->
                            <div @click="activeTab = 'grades'" class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between gap-2 cursor-pointer hover:shadow-md transition-all">
                                <div class="min-w-0">
                                    <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Rata-Rata Nilai</p>
                                    <h3 class="text-lg sm:text-xl font-black text-indigo-600 dark:text-indigo-400 mt-0.5 font-mono">{{ $gradeStats['average'] }}</h3>
                                    <p class="text-[9px] sm:text-[10px] text-slate-500 dark:text-slate-400 font-semibold truncate mt-0.5">{{ $gradeStats['total'] }} Mapel</p>
                                </div>
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            </div>

                            <!-- Tunggakan Tagihan -->
                            <div @click="activeTab = 'payments'" class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between gap-2 cursor-pointer hover:shadow-md transition-all">
                                <div class="min-w-0">
                                    <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Tunggakan</p>
                                    <h3 class="text-xs sm:text-base font-black {{ $unpaidTotal > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }} mt-0.5 truncate font-mono">
                                        Rp {{ number_format($unpaidTotal, 0, ',', '.') }}
                                    </h3>
                                    <p class="text-[9px] sm:text-[10px] {{ $unpaidTotal > 0 ? 'text-amber-600 font-extrabold' : 'text-emerald-600 font-extrabold' }} truncate mt-0.5">
                                        {{ $unpaidTotal > 0 ? 'Perlu Dilunasi' : 'Lunas Semua' }}
                                    </p>
                                </div>
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-2xl {{ $unpaidTotal > 0 ? 'bg-amber-50 dark:bg-amber-950 text-amber-600' : 'bg-emerald-50 dark:bg-emerald-950 text-emerald-600' }} flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                            </div>

                            <!-- Jadwal Pelajaran Hari Ini -->
                            <div @click="activeTab = 'schedules'" class="bg-white dark:bg-slate-900 rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex items-center justify-between gap-2 cursor-pointer hover:shadow-md transition-all">
                                <div class="min-w-0">
                                    <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Jadwal Hari Ini</p>
                                    <h3 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white mt-0.5 font-mono">{{ $todaySchedules->count() }} Mapel</h3>
                                    <p class="text-[9px] sm:text-[10px] text-slate-500 dark:text-slate-400 font-semibold truncate mt-0.5">
                                        {{ isset($days[Carbon\Carbon::now()->format('l')]) ? $days[Carbon\Carbon::now()->format('l')] : date('l') }}
                                    </p>
                                </div>
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-2xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Aktivitas Terkini Card -->
                        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                                <h3 class="font-black text-slate-900 dark:text-white text-sm sm:text-base">Ringkasan Aktivitas Terkini</h3>
                                <span class="text-[11px] sm:text-xs font-bold text-indigo-600 dark:text-indigo-400">Status Update Real-time</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                                <!-- Status Presensi Hari Ini Card -->
                                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 space-y-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Presensi Hari Ini</span>
                                    @if($todayAttendance)
                                        <div class="flex items-center space-x-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                                            <span class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase">{{ $todayAttendance->status === 'present' ? 'Hadir Tempat Waktu' : $todayAttendance->status }}</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Jam Masuk Terdata: <strong class="text-slate-900 dark:text-white font-mono">{{ $todayAttendance->time_in ?? '-' }}</strong></p>
                                    @else
                                        <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum ada catatan presensi hari ini.</p>
                                    @endif
                                </div>

                                <!-- Status Tagihan SPP Card -->
                                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 space-y-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Tagihan Sekolah</span>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">Total Tunggakan:</span>
                                        <span class="text-xs sm:text-sm font-mono font-black {{ $unpaidTotal > 0 ? 'text-amber-600' : 'text-emerald-600' }}">Rp {{ number_format($unpaidTotal, 0, ',', '.') }}</span>
                                    </div>
                                    <button type="button" @click="activeTab = 'payments'" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Rincian Tagihan &rarr;</button>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- TAB 1: PRESENSI KEHADIRAN ONLY -->
            <div x-show="activeTab === 'attendance'" class="w-full space-y-5" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-100 dark:border-emerald-900 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-sm sm:text-base">Riwayat Presensi Harian Siswa</h3>
                                <p class="text-[11px] sm:text-xs text-slate-400 font-medium">Catatan kehadiran dan status absen siswa terbaru</p>
                            </div>
                        </div>

                        <!-- Stats Summary Pills -->
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl text-[11px] sm:text-xs font-extrabold">Hadir: {{ $attendanceStats['present'] }}</span>
                            <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-xl text-[11px] sm:text-xs font-extrabold">Telat: {{ $attendanceStats['late'] }}</span>
                            <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-xl text-[11px] sm:text-xs font-extrabold">Izin/Sakit: {{ $attendanceStats['permission'] }}</span>
                            <span class="px-2.5 py-1 bg-rose-50 dark:bg-rose-950 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800 rounded-xl text-[11px] sm:text-xs font-extrabold">Alpa: {{ $attendanceStats['absent'] }}</span>
                        </div>
                    </div>

                    @if($attendances->count() > 0)
                        <!-- Mobile Timeline View (HP Viewport lg:hidden) -->
                        <div class="lg:hidden space-y-2.5">
                            @foreach($attendances as $att)
                                <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/70 dark:border-slate-800 flex items-center justify-between gap-3">
                                    <div class="min-w-0 space-y-0.5">
                                        <p class="font-mono font-bold text-xs text-slate-900 dark:text-white">
                                            {{ $att->date ? $att->date->format('d/m/Y') : '-' }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">
                                            Jam Masuk: <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $att->time_in ?? '-' }}</strong>
                                            @if($att->notes) &bull; {{ $att->notes }} @endif
                                        </p>
                                    </div>
                                    <div class="shrink-0">
                                        @if($att->status === 'present')
                                            <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 text-[10px] font-black rounded-full uppercase">Hadir</span>
                                        @elseif($att->status === 'late')
                                            <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 text-[10px] font-black rounded-full uppercase">Terlambat</span>
                                        @elseif(in_array($att->status, ['permission', 'sick']))
                                            <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 text-[10px] font-black rounded-full uppercase">{{ $att->status === 'sick' ? 'Sakit' : 'Izin' }}</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-300 text-[10px] font-black rounded-full uppercase">Alpa</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Data Table View (PC Viewport hidden lg:block) -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-extrabold border-b border-slate-200/80 dark:border-slate-800">
                                        <th class="py-3 px-4 rounded-l-xl">Tanggal</th>
                                        <th class="py-3 px-4">Jam Masuk</th>
                                        <th class="py-3 px-4">Status Presensi</th>
                                        <th class="py-3 px-4 rounded-r-xl">Keterangan Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-semibold text-slate-700 dark:text-slate-300">
                                    @foreach($attendances as $att)
                                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/60 transition-colors">
                                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                                {{ $att->date ? $att->date->format('d/m/Y') : '-' }}
                                            </td>
                                            <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-400 font-bold">
                                                {{ $att->time_in ?? '-' }}
                                            </td>
                                            <td class="py-3.5 px-4">
                                                @if($att->status === 'present')
                                                    <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-[10px] font-black rounded-full uppercase">
                                                        Hadir
                                                    </span>
                                                @elseif($att->status === 'late')
                                                    <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 text-[10px] font-black rounded-full uppercase">
                                                        Terlambat
                                                    </span>
                                                @elseif(in_array($att->status, ['permission', 'sick']))
                                                    <span class="px-3 py-1 bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 text-[10px] font-black rounded-full uppercase">
                                                        {{ $att->status === 'sick' ? 'Sakit' : 'Izin' }}
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 bg-rose-50 dark:bg-rose-950 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-[10px] font-black rounded-full uppercase">
                                                        Alpa / Tanpa Keterangan
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 text-xs">
                                                {{ $att->notes ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-10 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 space-y-2">
                            <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold">Belum ada catatan presensi harian untuk siswa ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 2: NILAI AKADEMIS ONLY -->
            <div x-show="activeTab === 'grades'" class="w-full space-y-5" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950 border border-indigo-100 dark:border-indigo-900 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-sm sm:text-base">Hasil & Transkrip Nilai Akademis</h3>
                                <p class="text-[11px] sm:text-xs text-slate-400 font-medium">Ringkasan perolehan nilai mata pelajaran siswa</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 text-[11px] sm:text-xs font-bold">
                            <a href="{{ route('parent.raport.print') }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Cetak Raport Anak (PDF)</span>
                            </a>
                            <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-xl">Tertinggi: {{ $gradeStats['highest'] }}</span>
                            <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl">Rata-rata: {{ $gradeStats['average'] }}</span>
                        </div>
                    </div>

                    @if($grades->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                            @foreach($grades as $grade)
                                <div class="p-3.5 sm:p-4 bg-slate-50/80 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3 shadow-2xs hover:border-indigo-300 transition-all">
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white truncate">{{ $grade->subject ?? $grade->subject_name ?? 'Mata Pelajaran' }}</h4>
                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                            <span class="px-2 py-0.5 text-[9px] sm:text-[10px] font-bold rounded-md {{ $grade->type === 'daily' ? 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300' : ($grade->type === 'mid_term' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300' : ($grade->type === 'final_term' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-slate-200 text-slate-700')) }}">
                                                {{ $grade->type === 'daily' ? 'Harian' : ($grade->type === 'mid_term' ? 'UTS' : ($grade->type === 'final_term' ? 'UAS' : 'Ujian')) }}
                                            </span>
                                            @if($grade->notes)
                                                <span class="text-[9px] sm:text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate max-w-[120px] sm:max-w-[150px]">&bull; {{ $grade->notes }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="px-3 py-1.5 {{ $grade->score >= 75 ? 'bg-emerald-600' : ($grade->score >= 60 ? 'bg-amber-500' : 'bg-rose-600') }} text-white font-mono font-black text-xs sm:text-sm rounded-xl shrink-0 shadow-2xs">
                                        {{ number_format($grade->score, 1) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 space-y-2">
                            <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold">Belum ada data nilai mata pelajaran yang diinputkan.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 3: TAGIHAN & KEUANGAN ONLY -->
            <div x-show="activeTab === 'payments'" class="w-full space-y-5" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-amber-50 dark:bg-amber-950 border border-amber-100 dark:border-amber-900 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-sm sm:text-base">Rincian Tagihan & Biaya Sekolah</h3>
                                <p class="text-[11px] sm:text-xs text-slate-400 font-medium">Status kewajiban dan histori pembayaran</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 text-[11px] sm:text-xs font-extrabold flex-wrap">
                            <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-xl">Belum Lunas: Rp {{ number_format($unpaidTotal, 0, ',', '.') }}</span>
                            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl">Sudah Lunas: Rp {{ number_format($paidTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($paymentBills->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                            @foreach($paymentBills as $bill)
                                @php
                                    $remaining = max(0, (float)$bill->total_amount - (float)$bill->paid_amount);
                                @endphp
                                <div class="p-3.5 sm:p-4 bg-slate-50/80 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between gap-3 shadow-2xs hover:border-amber-300 transition-all" x-data="{ showDetails: false }">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white">
                                                {{ $bill->paymentBill?->name ?? $bill->paymentBill?->paymentPost?->name ?? 'Tagihan Biaya Sekolah' }}
                                            </h4>
                                            <p class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5">
                                                Dibuat: {{ $bill->created_at ? $bill->created_at->format('d M Y') : '-' }}
                                            </p>
                                        </div>
                                        @if($bill->status === 'paid')
                                            <span class="px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-[10px] font-black rounded-full uppercase shrink-0">Lunas</span>
                                        @elseif($bill->paid_amount > 0)
                                            <span class="px-2.5 py-0.5 bg-blue-100 dark:bg-blue-950 border border-blue-300 dark:border-blue-800 text-blue-800 dark:text-blue-300 text-[10px] font-black rounded-full uppercase shrink-0">Cicil</span>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-amber-100 dark:bg-amber-950 border border-amber-300 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-[10px] font-black rounded-full uppercase shrink-0">Belum Lunas</span>
                                        @endif
                                    </div>

                                    <div class="pt-2 border-t border-slate-200/80 dark:border-slate-800 space-y-1">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-bold text-slate-500 dark:text-slate-400">Total Tagihan:</span>
                                            <span class="font-mono font-black text-slate-900 dark:text-white">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-bold text-slate-500 dark:text-slate-400">Sudah Dibayar:</span>
                                            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</span>
                                        </div>
                                        @if($remaining > 0)
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="font-bold text-slate-500 dark:text-slate-400">Sisa Pembayaran:</span>
                                                <span class="font-mono font-extrabold text-amber-600 dark:text-amber-400">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($bill->details && $bill->details->count() > 0)
                                        <div class="pt-2 border-t border-slate-200/80 dark:border-slate-800">
                                            <button type="button" @click="showDetails = !showDetails" class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:underline font-bold flex items-center gap-1 cursor-pointer">
                                                <span x-text="showDetails ? 'Sembunyikan Rincian Bulanan' : 'Lihat Rincian Bulanan (' + {{ $bill->details->count() }} + ' Bulan)'"></span>
                                                <svg class="w-3.5 h-3.5 transition-transform" :class="showDetails ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>

                                            <div x-show="showDetails" x-transition class="mt-2 space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                                @foreach($bill->details as $det)
                                                    <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between text-[11px]">
                                                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $det->month_name }}</span>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-mono text-slate-600 dark:text-slate-400">Rp {{ number_format($det->amount, 0, ',', '.') }}</span>
                                                            @if($det->status === 'paid')
                                                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[9px] font-bold">Lunas</span>
                                                            @else
                                                                <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-bold">Belum</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 space-y-2">
                            <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold">Tidak ada rincian tagihan yang aktif.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 4: JADWAL PELAJARAN ONLY -->
            <div x-show="activeTab === 'schedules'" class="w-full space-y-5" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-purple-50 dark:bg-purple-950 border border-purple-100 dark:border-purple-900 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-sm sm:text-base">Jadwal Pelajaran KBM</h3>
                                <p class="text-[11px] sm:text-xs text-slate-400 font-medium">Pilih hari untuk melihat alokasi jam mata pelajaran</p>
                            </div>
                        </div>

                        <!-- Interactive Day Selector Pills -->
                        <div class="flex items-center space-x-1 overflow-x-auto scrollbar-none py-1">
                            @foreach(['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'] as $dayKey => $dayLabel)
                                <button type="button" @click="selectedScheduleDay = '{{ $dayKey }}'" 
                                        :class="selectedScheduleDay === '{{ $dayKey }}' ? 'bg-purple-600 text-white shadow-xs font-extrabold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold'" 
                                        class="px-3 py-1 rounded-xl text-xs transition-all shrink-0 cursor-pointer">
                                    {{ $dayLabel }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if(isset($allSchedules) && $allSchedules->count() > 0)
                        <div class="space-y-2.5">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $dayKey)
                                @php
                                    $daySchedules = $allSchedules->where('day', $dayKey);
                                @endphp
                                <div x-show="selectedScheduleDay === '{{ $dayKey }}'" class="space-y-2.5">
                                    @forelse($daySchedules as $sch)
                                        <div class="p-3.5 sm:p-4 bg-slate-50/90 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3 hover:border-purple-300 transition-all">
                                            <div class="flex items-center space-x-3 min-w-0">
                                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-purple-600 text-white font-mono font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                    {{ strtoupper(substr($sch->subject ?? $sch->name ?? 'MP', 0, 3)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white truncate">{{ $sch->subject ?? $sch->name ?? 'Mata Pelajaran' }}</h4>
                                                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate">Pengampu: {{ $sch->teacher ?? 'Guru Pengampu' }}</p>
                                                </div>
                                            </div>

                                            <div class="text-right shrink-0 font-mono">
                                                <span class="px-2.5 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-purple-700 dark:text-purple-300 font-black text-[11px] sm:text-xs rounded-xl shadow-2xs">
                                                    {{ is_string($sch->start_time) ? $sch->start_time : ($sch->start_time ? $sch->start_time->format('H:i') : '') }} - {{ is_string($sch->end_time) ? $sch->end_time : ($sch->end_time ? $sch->end_time->format('H:i') : '') }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 space-y-1">
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold">Tidak ada kegiatan KBM / jadwal pelajaran pada hari ini.</p>
                                        </div>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 space-y-2">
                            <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold">Tidak ada kegiatan KBM / mata pelajaran yang diinputkan.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 5: PENGUMUMAN SEKOLAH ONLY -->
            <div x-show="activeTab === 'announcements'" class="w-full space-y-5" x-cloak>
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-blue-50 dark:bg-blue-950 border border-blue-100 dark:border-blue-900 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-slate-900 dark:text-white text-sm sm:text-base">Pengumuman & Informasi Sekolah</h3>
                            <p class="text-[11px] sm:text-xs text-slate-400 font-medium">Berita resmi dan pemberitahuan penting untuk orang tua siswa</p>
                        </div>
                    </div>

                    @if($announcements->count() > 0)
                        <div class="space-y-3.5">
                            @foreach($announcements as $ann)
                                <div class="p-3.5 sm:p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white">{{ $ann->title }}</h4>
                                        <span class="text-[10px] font-bold text-slate-400 shrink-0">{{ $ann->created_at ? $ann->created_at->format('d M Y') : '' }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-medium">{!! $ann->content !!}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 space-y-2">
                            <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold">Belum ada pengumuman sekolah terbaru.</p>
                        </div>
                    @endif
                </div>
            </div>

        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar (Visible on Mobile lg:hidden) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-t border-slate-200 dark:border-slate-800 z-40 shadow-lg no-print transition-colors">
        <div class="max-w-md mx-auto grid grid-cols-5 py-1 px-1 text-[10px] font-bold text-slate-500 dark:text-slate-400 text-center items-end">
            
            <button @click="activeTab = 'dashboard'" :class="activeTab === 'dashboard' ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : 'hover:text-slate-900 dark:hover:text-white'" class="flex flex-col items-center py-1 px-1 transition-colors cursor-pointer">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Beranda</span>
            </button>

            <button @click="activeTab = 'attendance'" :class="activeTab === 'attendance' ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : 'hover:text-slate-900 dark:hover:text-white'" class="flex flex-col items-center py-1 px-1 transition-colors cursor-pointer">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Presensi</span>
            </button>

            <!-- Floating KTS Button -->
            <div class="flex flex-col items-center -mt-5">
                <button @click="showCardModal = true" type="button" class="w-12 h-12 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center shadow-lg shadow-indigo-600/30 ring-4 ring-white dark:ring-slate-900 transform active:scale-90 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3m-3 3h3m-3 3h3M4 6h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm2 4a2 2 0 100 4 2 2 0 000-4zm-1 6a3 3 0 016 0H5z"/></svg>
                </button>
                <span class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mt-0.5">KTS</span>
            </div>

            <button @click="activeTab = 'grades'" :class="activeTab === 'grades' ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : 'hover:text-slate-900 dark:hover:text-white'" class="flex flex-col items-center py-1 px-1 transition-colors cursor-pointer">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Nilai</span>
            </button>

            <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : 'hover:text-slate-900 dark:hover:text-white'" class="flex flex-col items-center py-1 px-1 transition-colors cursor-pointer">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Tagihan</span>
            </button>

        </div>
    </nav>

    <!-- Student ID Card Preview Modal -->
    <div x-show="showCardModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-6 overflow-y-auto" 
         x-cloak>
        <div @click.away="showCardModal = false" class="bg-slate-900 text-white rounded-3xl max-w-2xl w-full p-4 sm:p-7 space-y-4 sm:space-y-6 border border-white/20 shadow-2xl relative my-auto max-h-[92vh] flex flex-col justify-between overflow-y-auto scrollbar-none">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-800 pb-3 sm:pb-4 shrink-0">
                <div>
                    <h3 class="text-sm sm:text-base font-black text-white">Kartu Tanda Siswa Resmi (KTS)</h3>
                    <p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">Identitas digital resmi &amp; QR Kode Absensi Siswa</p>
                </div>
                <button type="button" @click="showCardModal = false" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-colors cursor-pointer shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Card Render Container (Proportional mobile scaling) -->
            <div class="flex flex-col items-center justify-center py-1 w-full overflow-hidden shrink-0">
                <div class="scale-[0.68] min-[400px]:scale-[0.78] sm:scale-90 md:scale-100 transform origin-center transition-transform -my-14 min-[400px]:-my-8 sm:my-0">
                    <x-student-card :student="$student" :showLabel="true" />
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 sm:pt-4 border-t border-slate-800 flex flex-wrap items-center justify-between gap-2.5 shrink-0">
                <span class="text-[10px] sm:text-xs text-slate-400 font-medium">Standar ISO CR-80 (85.6mm x 54mm)</span>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('parent.student-card.print') }}" target="_blank" class="px-3.5 sm:px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs rounded-xl transition-all cursor-pointer flex items-center space-x-1.5 shadow-md border border-emerald-500/40">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Simpan PDF / Cetak</span>
                    </a>
                    <button type="button" @click="showCardModal = false" class="px-4 sm:px-5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition-all cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
