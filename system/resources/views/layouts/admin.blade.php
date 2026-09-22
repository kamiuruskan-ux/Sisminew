<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ Setting::get('school_name', 'School') }}</title>
    
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

    <!-- PWA Install Support -->
    @include('components.pwa-install')
    @include('components.push-notification-client')

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    colors: {
                        primary: '{{ Setting::get('primary_color', '#3C50E0') }}',
                        secondary: '{{ Setting::get('secondary_color', '#2563eb') }}',
                        boxdark: {
                            DEFAULT: '#24303F',
                            2: '#1A222C',
                        },
                        strokedark: '#2E3A47',
                        stroke: '#E2E8F0',
                        whiten: '#F1F5F9',
                    }
                }
            }
        }
    </script>
    
    <!-- AlpineJS & Collapse Plugin CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- TomSelect CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

    <style>
        :root {
            --primary: {{ Setting::get('primary_color', '#3C50E0') }};
            --secondary: {{ Setting::get('secondary_color', '#2563eb') }};
        }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F1F5F9;
            color: #1C2434;
            -webkit-font-smoothing: antialiased;
        }

        html.dark body {
            background-color: #1A222C;
            color: #DEE4EE;
        }

        /* TomSelect (Select Search) Proportional Styling */
        .ts-wrapper {
            width: 100% !important;
        }
        .ts-wrapper .ts-control {
            min-height: 40px !important;
            height: 40px !important;
            padding: 0 14px !important;
            border-radius: 0.75rem !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            display: flex !important;
            align-items: center !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
            transition: all 0.15s ease-in-out !important;
        }
        html.dark .ts-wrapper .ts-control {
            border-color: #334155 !important;
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
        }
        .ts-wrapper.focus .ts-control {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
        }
        .ts-wrapper .ts-control input {
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            color: #0f172a !important;
        }
        html.dark .ts-wrapper .ts-control input {
            color: #f1f5f9 !important;
        }
        .ts-dropdown {
            border-radius: 1rem !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
            margin-top: 4px !important;
            overflow: hidden !important;
            z-index: 9999 !important;
            font-size: 0.75rem !important;
        }
        html.dark .ts-dropdown {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        .ts-dropdown .option {
            padding: 8px 14px !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
        }
        .ts-dropdown .option.active, .ts-dropdown .option:hover {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
        }
        html.dark .ts-dropdown .option.active, html.dark .ts-dropdown .option:hover {
            background-color: #6366f1 !important;
            color: #ffffff !important;
        }

        /* Admin Component Design System Overrides */
        .tailadmin-card, .school-card, .premium-card {
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 1rem;
            box-shadow: 0px 8px 13px -3px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease-in-out;
        }

        html.dark .tailadmin-card, html.dark .school-card, html.dark .premium-card {
            background-color: #24303F !important;
            border-color: #2E3A47 !important;
            color: #DEE4EE !important;
            box-shadow: 0px 8px 13px -3px rgba(0, 0, 0, 0.2);
        }

        .tailadmin-header, .school-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid #E2E8F0;
        }

        html.dark .tailadmin-header, html.dark .school-header {
            background: rgba(36, 48, 63, 0.95);
            border-bottom: 1px solid #2E3A47;
        }

        /* Admin Sidebar (Light mode = white bg, Dark mode = dark bg) */
        .tailadmin-sidebar, .school-sidebar {
            background-color: #FFFFFF;
            color: #64748B;
            border-right: 1px solid #E2E8F0;
        }

        html.dark .tailadmin-sidebar, html.dark .school-sidebar {
            background-color: #1C2434 !important;
            color: #8A99AD !important;
            border-right: 1px solid #2E3A47 !important;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.65rem 1rem;
            margin: 0.2rem 0.75rem;
            border-radius: 0.625rem;
            color: #64748B;
            font-size: 0.8125rem;
            font-weight: 600;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover {
            background-color: #F1F5F9;
            color: var(--primary);
        }

        html.dark .nav-link {
            color: #8A99AD;
        }

        html.dark .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: #FFFFFF;
        }

        .nav-link-active {
            background-color: var(--primary) !important;
            color: #FFFFFF !important;
            font-weight: 700;
            box-shadow: 0px 4px 12px color-mix(in srgb, var(--primary) 35%, transparent);
        }

        .nav-icon {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.5rem;
            flex-shrink: 0;
            opacity: 0.85;
            transition: transform 0.2s ease;
        }

        .nav-link:hover .nav-icon {
            opacity: 1;
            transform: scale(1.08);
        }

        .nav-link-active .nav-icon {
            color: #FFFFFF !important;
            opacity: 1;
            transform: scale(1.08);
        }

        .section-label {
            padding: 1.25rem 1.25rem 0.5rem;
            font-size: 0.6875rem;
            font-weight: 800;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        html.dark .section-label {
            color: #64748B;
        }

        /* Global Form Control Overrides for TailAdmin */
        input[type="text"], 
        input[type="email"], 
        input[type="password"], 
        input[type="number"], 
        input[type="date"], 
        input[type="datetime-local"], 
        input[type="search"], 
        select, 
        textarea {
            background-color: #F8FAFC;
            border-color: #E2E8F0;
            color: #1C2434;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
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
            background-color: #1A222C !important;
            border-color: #2E3A47 !important;
            color: #FFFFFF !important;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary) !important;
            outline: none !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 15%, transparent) !important;
        }

        /* Global Table Overrides for TailAdmin */
        .modern-table, table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modern-table thead th, table thead th {
            background-color: #F1F5F9;
            color: #64748B;
            font-weight: 700;
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid #E2E8F0;
        }

        html.dark .modern-table thead th, html.dark table thead th {
            background-color: #1A222C !important;
            color: #8A99AD !important;
            border-bottom-color: #2E3A47 !important;
        }

        .modern-table tbody td, table tbody td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #E2E8F0;
            color: #1C2434;
            font-size: 0.875rem;
        }

        html.dark .modern-table tbody td, html.dark table tbody td {
            border-bottom-color: #2E3A47 !important;
            color: #DEE4EE !important;
        }

        .modern-table tbody tr:hover, table tbody tr:hover {
            background-color: rgba(241, 245, 249, 0.6);
        }

        html.dark .modern-table tbody tr:hover, html.dark table tbody tr:hover {
            background-color: rgba(26, 34, 44, 0.5) !important;
        }

        /* Custom Modern Sleek Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
            transition: background 0.2s ease-in-out;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3C50E0;
        }

        html.dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }

        html.dark ::-webkit-scrollbar-thumb:hover {
            background: #3C50E0;
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: #CBD5E1 transparent;
        }

        html.dark * {
            scrollbar-color: #334155 transparent;
        }

        /* Global Button Design System for Admin Panel */
        .btn-primary,
        .btn-secondary,
        .btn-success,
        .btn-danger,
        .btn-warning {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            line-height: 1rem;
            font-weight: 700;
            border-radius: 0.75rem;
            border: 1px solid transparent;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            white-space: nowrap;
            height: auto;
        }

        .btn-primary, .bg-indigo-600 {
            background-color: var(--primary) !important;
            color: #FFFFFF !important;
        }

        .btn-primary:hover, .bg-indigo-600:hover {
            background-color: var(--secondary) !important;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--secondary) 25%, transparent);
        }

        .btn-primary:active, .bg-indigo-600:active {
            transform: scale(0.98);
        }

        .btn-secondary {
            background-color: #F1F5F9 !important;
            color: #334155 !important;
            border-color: #CBD5E1 !important;
        }

        .btn-secondary:hover {
            background-color: #E2E8F0 !important;
        }

        html.dark .btn-secondary {
            background-color: #334155 !important;
            color: #F8FAFC !important;
            border-color: #475569 !important;
        }

        /* High Contrast Guarantee for Buttons and Action Links */
        a.bg-white,
        button.bg-white,
        a.bg-slate-100,
        button.bg-slate-100,
        a.bg-slate-50,
        button.bg-slate-50 {
            font-weight: 700;
            border: 1px solid #CBD5E1;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        a.bg-white:hover,
        button.bg-white:hover,
        a.bg-slate-100:hover,
        button.bg-slate-100:hover {
            background-color: #F1F5F9;
            border-color: #94A3B8;
        }

        html.dark a.bg-white,
        html.dark button.bg-white,
        html.dark a.bg-slate-100,
        html.dark button.bg-slate-100,
        html.dark a.bg-slate-50,
        html.dark button.bg-slate-50 {
            background-color: #24303F !important;
            color: #F8FAFC !important;
            border-color: #475569 !important;
        }

        html.dark a.bg-white:hover,
        html.dark button.bg-white:hover,
        html.dark a.bg-slate-100:hover,
        html.dark button.bg-slate-100:hover {
            background-color: #334155 !important;
            color: #FFFFFF !important;
            border-color: #64748B !important;
        }

        /* TailAdmin Dark Mode Overrides for All Tables & Containers */
        html.dark table,
        html.dark .modern-table {
            background-color: #24303F !important;
            color: #DEE4EE !important;
        }

        html.dark table thead,
        html.dark table thead tr,
        html.dark table thead th,
        html.dark .bg-slate-50,
        html.dark .bg-gray-50,
        html.dark .bg-slate-100,
        html.dark .bg-gray-100,
        html.dark .bg-slate-50\/50,
        html.dark .bg-slate-50\/80,
        html.dark .bg-slate-50\/60,
        html.dark .bg-slate-50\/40 {
            background-color: #1A222C !important;
            color: #8A99AD !important;
            border-color: #2E3A47 !important;
        }

        html.dark table tbody tr,
        html.dark table tbody td {
            background-color: transparent !important;
            color: #DEE4EE !important;
            border-color: #2E3A47 !important;
        }

        html.dark table tbody tr:hover {
            background-color: rgba(26, 34, 44, 0.7) !important;
        }

        html.dark .text-slate-900,
        html.dark .text-gray-900,
        html.dark .text-slate-800,
        html.dark .text-gray-800,
        html.dark .text-slate-700,
        html.dark .text-gray-700 {
            color: #DEE4EE !important;
        }

        html.dark .text-slate-600:not(button):not(a),
        html.dark .text-gray-600:not(button):not(a),
        html.dark .text-slate-500:not(button):not(a),
        html.dark .text-gray-500:not(button):not(a),
        html.dark .text-slate-400:not(button):not(a),
        html.dark .text-gray-400:not(button):not(a) {
            color: #8A99AD !important;
        }

        html.dark .bg-white {
            background-color: #24303F !important;
            border-color: #2E3A47 !important;
            color: #DEE4EE !important;
        }

        html.dark .border-slate-100,
        html.dark .border-slate-200,
        html.dark .border-slate-300,
        html.dark .border-gray-100,
        html.dark .border-gray-200,
        html.dark .divide-slate-100,
        html.dark .divide-slate-200,
        html.dark .divide-slate-300,
        html.dark .divide-gray-100,
        html.dark .divide-gray-200 {
            border-color: #2E3A47 !important;
        }

        html.dark .shadow-xs, html.dark .shadow-sm, html.dark .shadow-md {
            box-shadow: 0px 8px 13px -3px rgba(0, 0, 0, 0.25) !important;
        }

        /* Scrollbar Styling */
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(200, 200, 200, 0.2);
            border-radius: 9999px;
        }
        .sidebar-nav:hover::-webkit-scrollbar-thumb {
            background: rgba(200, 200, 200, 0.4);
        }

        [x-cloak] { display: none !important; }
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
<body class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#1C2434] dark:text-[#DEE4EE] h-full flex flex-col transition-colors duration-200" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-xs z-40 lg:hidden"
             x-cloak>
        </div>

        <!-- TailAdmin Sidebar -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="tailadmin-sidebar fixed inset-y-0 left-0 z-50 w-72 -translate-x-full lg:translate-x-0 transform transition-transform duration-300 ease-in-out lg:static lg:inset-0 shadow-2xl lg:shadow-none flex flex-col"
        >
            <!-- TailAdmin Brand Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-[#E2E8F0] dark:border-[#2E3A47] bg-white dark:bg-[#1C2434]">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3.5 group">
                    <div class="w-10 h-10 bg-white/10 dark:bg-white/10 rounded-xl flex items-center justify-center p-1.5 shadow-md shrink-0 border border-white/20 overflow-hidden">
                        <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="{{ Setting::get('school_name', 'School') }}" class="w-full h-full object-contain">
                    </div>
                    <div class="overflow-hidden">
                        <p class="font-extrabold text-[#1C2434] dark:text-white text-base truncate tracking-tight group-hover:text-primary transition-colors">
                            {{ Setting::get('school_short_name', 'School') }}
                        </p>
                        <p class="text-[10px] text-[#64748B] dark:text-[#8A99AD] font-bold uppercase tracking-widest mt-0.5">ADMIN PANEL</p>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-[#64748B] dark:text-[#8A99AD] hover:text-primary focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Admin Navigation Links -->
            <nav @click.capture="if (window.innerWidth < 1024 && $event.target.closest('a')) sidebarOpen = false" class="sidebar-nav flex-1 overflow-y-auto py-5 space-y-3 px-3">
                <!-- 1. Menu Utama -->
                <div>
                    <p class="section-label">Utama</p>
                    <div class="space-y-0.5">
                        <a href="{{ route('admin.dashboard') }}"
                           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
                            <svg class="nav-icon text-primary dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="9" rx="1"></rect>
                                <rect x="14" y="3" width="7" height="5" rx="1"></rect>
                                <rect x="14" y="12" width="7" height="9" rx="1"></rect>
                                <rect x="3" y="16" width="7" height="5" rx="1"></rect>
                            </svg>
                            <span class="font-bold">Dashboard Utama</span>
                        </a>
                    </div>
                </div>

                <!-- 2. Master Data Sekolah -->
                @if(auth()->user()->hasPermission('view-users|view-students|view-alumni|view-student-cards|view-face-id|view-classes|view-majors|view-subjects|view-schedules|view-academic-years|view-library'))
                <div>
                    <p class="section-label">Master Data Sekolah</p>
                    <div class="space-y-1">
                        @if(auth()->user()->hasPermission('view-users'))
                            <a href="{{ route('admin.users.index') }}" 
                               class="nav-link {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : '' }}">
                                <svg class="nav-icon text-purple-500 dark:text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Data Guru &amp; Pegawai</span>
                            </a>
                        @endif

                        <!-- Dropdown Data Siswa & Kartu -->
                        @if(auth()->user()->hasPermission('view-students|view-alumni|view-student-cards|view-face-id'))
                        @php
                            $isStudentDropdownActive = request()->routeIs('admin.students.*') || request()->routeIs('admin.student-cards.*') || request()->routeIs('admin.alumni.*');
                        @endphp
                        <div x-data="{ open: {{ $isStudentDropdownActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                    class="nav-link w-full flex items-center justify-between transition-colors {{ $isStudentDropdownActive ? 'text-primary dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-blue-500 dark:text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <span>Data Siswa &amp; Kartu</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                @if(auth()->user()->hasPermission('view-students'))
                                    <a href="{{ route('admin.students.index') }}" 
                                       class="nav-link text-xs {{ request()->routeIs('admin.students.*') && !request()->routeIs('admin.students.face-id*') ? 'nav-link-active' : '' }}">
                                        <span>Data Siswa</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-alumni|view-students'))
                                    <a href="{{ route('admin.alumni.index') }}" 
                                       class="nav-link text-xs {{ request()->routeIs('admin.alumni.*') ? 'nav-link-active' : '' }}">
                                        <span class="flex items-center justify-between w-full">
                                            <span>Daftar Alumni</span>
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">Lulus</span>
                                        </span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-student-cards|view-students'))
                                    <a href="{{ route('admin.student-cards.index') }}" 
                                       class="nav-link text-xs {{ request()->routeIs('admin.student-cards.*') ? 'nav-link-active' : '' }}">
                                        <span>Cetak Kartu Siswa</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-face-id|view-students'))
                                    <a href="{{ route('admin.students.face-id.index') }}" 
                                       class="nav-link text-xs {{ request()->routeIs('admin.students.face-id*') ? 'nav-link-active' : '' }}">
                                        <span>Daftar Face ID Siswa</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Dropdown Struktur Akademik & Kelas -->
                        @if(auth()->user()->hasPermission('view-classes|view-majors|view-subjects|view-schedules|view-academic-years'))
                        @php
                            $isAcademicDropdownActive = request()->routeIs('admin.classes.*') || request()->routeIs('admin.subjects.*') || request()->routeIs('admin.majors.*') || request()->routeIs('admin.academic-years.*') || request()->routeIs('admin.schedules.*');
                        @endphp
                        <div x-data="{ open: {{ $isAcademicDropdownActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                    class="nav-link w-full flex items-center justify-between transition-colors {{ $isAcademicDropdownActive ? 'text-primary dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                    </svg>
                                    <span>Struktur Akademik</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                @if(auth()->user()->hasPermission('view-classes'))
                                    <a href="{{ route('admin.classes.index') }}"
                                       class="nav-link text-xs {{ request()->routeIs('admin.classes.*') ? 'nav-link-active' : '' }}">
                                        <span>Data Kelas</span>
                                    </a>
                                @endif
                                @if(Setting::get('is_vocational', '0') == '1' && auth()->user()->hasPermission('view-majors'))
                                    <a href="{{ route('admin.majors.index') }}"
                                       class="nav-link text-xs {{ request()->routeIs('admin.majors.*') ? 'nav-link-active' : '' }}">
                                        <span>Data Jurusan</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-subjects'))
                                    <a href="{{ route('admin.subjects.index') }}"
                                       class="nav-link text-xs {{ request()->routeIs('admin.subjects.*') ? 'nav-link-active' : '' }}">
                                        <span>Mata Pelajaran</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-schedules|view-learning'))
                                    <a href="{{ route('admin.schedules.index') }}"
                                       class="nav-link text-xs {{ request()->routeIs('admin.schedules.*') ? 'nav-link-active' : '' }}">
                                        <span>Jadwal Pelajaran</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-academic-years'))
                                    <a href="{{ route('admin.academic-years.index') }}"
                                       class="nav-link text-xs {{ request()->routeIs('admin.academic-years.*') ? 'nav-link-active' : '' }}">
                                        <span>Tahun Akademik</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Standalone Link Perpustakaan Digital -->
                        @if(auth()->user()->hasPermission('view-library|view-learning'))
                            <a href="{{ route('admin.books.index') }}"
                               class="nav-link {{ request()->routeIs('admin.books.*') ? 'nav-link-active' : '' }}">
                                <svg class="nav-icon text-purple-500 dark:text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span>Perpustakaan Digital</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- 3. Akademik & Pembelajaran -->
                @if(auth()->user()->hasPermission('view-learning|view-lms|view-materials|view-assignments|view-exams|manage-cbt-server|view-halaqah|view-raport|view-grades|view-quran-raport|manage-raport-settings'))
                <div>
                    <p class="section-label">Akademik &amp; Pembelajaran</p>
                    <div class="space-y-1">
                        <!-- LMS & CBT Dropdown -->
                        @if(auth()->user()->hasPermission('view-learning|view-lms|view-materials|view-assignments|view-exams|manage-cbt-server'))
                        @php
                            $isLearningActive = request()->routeIs('admin.lms.*') || request()->routeIs('admin.materials.*') || request()->routeIs('admin.assignments.*') || request()->routeIs('admin.exams.*') || request()->routeIs('admin.cbt-capacity.*');
                        @endphp
                        <div x-data="{ open: {{ $isLearningActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                     class="nav-link w-full flex items-center justify-between transition-colors {{ $isLearningActive ? 'text-primary dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-indigo-500 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                    <span>LMS &amp; CBT</span>
                                    @if(($notificationCounts['cbt'] ?? 0) > 0)
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping ml-2" title="Ada Koreksi CBT"></span>
                                    @endif
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                @if(auth()->user()->hasPermission('view-lms|view-learning'))
                                    <a href="{{ route('admin.lms.chapters.index') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.lms.*') ? 'nav-link-active' : '' }}">
                                        <span>LMS &amp; Live class</span>
                                        <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-extrabold text-[9px] rounded">Pro</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-materials|view-learning'))
                                    <a href="{{ route('admin.materials.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.materials.*') ? 'nav-link-active' : '' }}">Materi &amp; Modul Ajar</a>
                                @endif
                                @if(auth()->user()->hasPermission('view-assignments|view-learning'))
                                    <a href="{{ route('admin.assignments.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.assignments.*') ? 'nav-link-active' : '' }}">Tugas Siswa</a>
                                @endif
                                @if(auth()->user()->hasPermission('view-exams|view-learning'))
                                    <a href="{{ route('admin.exams.index') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.exams.*') ? 'nav-link-active' : '' }}">
                                        <span>Ujian CBT Online</span>
                                        @if(($notificationCounts['cbt'] ?? 0) > 0)
                                            <span class="px-2 py-0.5 bg-amber-500 text-white font-extrabold text-[9px] rounded-full animate-pulse shadow-xs">
                                                {{ $notificationCounts['cbt'] }} Koreksi
                                            </span>
                                        @endif
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('manage-cbt-server|view-learning'))
                                    <a href="{{ route('admin.cbt-capacity.index') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.cbt-capacity.*') ? 'nav-link-active' : '' }}">
                                        <span>Server CBT</span>
                                        <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-extrabold text-[9px] rounded">Cek</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Standalone Menu Halaqah Al-Qur'an (Tahsin & Tahfidz - Khusus Guru Al-Qur'an & Manajemen) -->
                        @if(auth()->user()->hasRole(['guru-quran', 'super-admin', 'admin', 'kepala-sekolah']))
                            <a href="{{ route('admin.halaqah.index') }}"
                               class="nav-link {{ request()->routeIs('admin.halaqah.*') ? 'nav-link-active' : '' }}">
                                <svg class="nav-icon text-emerald-500 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    <path d="M12 6v6"></path>
                                    <path d="M9 9h6"></path>
                                </svg>
                                <div class="flex items-center justify-between w-full">
                                    <span class="font-bold">Halaqah Al-Qur'an</span>
                                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-extrabold text-[9px] rounded">Qur'an</span>
                                </div>
                            </a>
                        @endif

                        <!-- Dropdown Menu E-Raport & Penilaian -->
                        @if(auth()->user()->hasPermission('view-raport|view-grades|view-quran-raport|manage-raport-settings|view-learning'))
                        @php
                            $isRaportActive = request()->routeIs('admin.raport.*') || request()->routeIs('admin.grades.*') || request()->routeIs('admin.quran-raport.*');
                        @endphp
                        <div x-data="{ open: {{ $isRaportActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                    class="nav-link w-full flex items-center justify-between transition-colors {{ $isRaportActive ? 'text-primary dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                    <span>E-Raport &amp; Penilaian</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                @if(auth()->user()->hasPermission('view-grades|view-learning'))
                                    <a href="{{ route('admin.grades.index') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.grades.*') ? 'nav-link-active' : '' }}">
                                        <span>Agenda &amp; Penilaian Mapel</span>
                                        <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 font-bold text-[9px] rounded">Guru</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-raport|view-learning'))
                                    <a href="{{ route('admin.raport.index') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.raport.index') || request()->routeIs('admin.raport.print*') ? 'nav-link-active' : '' }}">
                                        <span>Raport Mapel Umum</span>
                                        <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-extrabold text-[9px] rounded">A4/PDF</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasRole(['guru-quran', 'super-admin', 'admin', 'kepala-sekolah']))
                                    <a href="{{ route('admin.quran-raport.index') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.quran-raport.index') || request()->routeIs('admin.quran-raport.print') ? 'nav-link-active' : '' }}">
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">Raport Al-Qur'an</span>
                                        <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-extrabold text-[9px] rounded">Khusus</span>
                                    </a>
                                    <a href="{{ route('admin.quran-raport.settings') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.quran-raport.settings') ? 'nav-link-active' : '' }}">
                                        <span>Template Raport Qur'an</span>
                                        <span class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-bold text-[9px] rounded">Desain</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('manage-raport-settings'))
                                    <a href="{{ route('admin.raport.settings') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.raport.settings') ? 'nav-link-active' : '' }}">
                                        <span>Pengaturan Raport</span>
                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 font-extrabold text-[9px] rounded">Setting</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- 4. Kesiswaan & SPMB -->
                @if(auth()->user()->hasPermission('view-spmb|manage-spmb-waves|manage-spmb-settings|view-bk|view-bk-counseling|view-bk-violations|manage-bk-categories|view-bk-assessments|manage-extracurriculars|view-content') || auth()->user()->hasRole('super-admin|admin|guru-bk|bk|wakasek-kesiswaan'))
                <div>
                    <p class="section-label">Kesiswaan &amp; SPMB</p>
                    <div class="space-y-1">
                        <!-- Penerimaan Siswa (SPMB) Dropdown -->
                        @if(auth()->user()->hasPermission('view-spmb|manage-spmb-waves|manage-spmb-settings'))
                            @php
                                $isSpmbActive = request()->routeIs('admin.spmb.*') || request()->routeIs('admin.waves.*');
                            @endphp
                            <div x-data="{ open: {{ $isSpmbActive ? 'true' : 'false' }} }">
                                <button type="button" @click="open = !open" 
                                        class="nav-link w-full flex items-center justify-between transition-colors {{ $isSpmbActive ? 'text-[#3C50E0] dark:text-indigo-400 font-bold' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="nav-icon text-purple-500 dark:text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <polyline points="16 11 18 13 22 9"></polyline>
                                        </svg>
                                        <span>Penerimaan (SPMB)</span>
                                    </div>
                                    <div class="flex items-center space-x-1.5">
                                        @php
                                            try {
                                                $spmbPendingCount = \App\Models\SpmbRegistration::where('status', 'submitted')->count();
                                            } catch (\Throwable $e) {
                                                $spmbPendingCount = 0;
                                            }
                                        @endphp
                                        @if($spmbPendingCount > 0)
                                            <span class="bg-[#F87171] text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full shadow-xs">
                                                {{ $spmbPendingCount > 99 ? '99+' : $spmbPendingCount }}
                                            </span>
                                        @endif
                                        <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </button>

                                <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                    @if(auth()->user()->hasPermission('view-spmb'))
                                        <a href="{{ route('admin.spmb.index') }}" class="nav-link text-xs {{ (request()->routeIs('admin.spmb.index') || request()->routeIs('admin.spmb.show') || request()->routeIs('admin.spmb.print')) ? 'nav-link-active' : '' }}">Pendaftaran SPMB</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('manage-spmb-waves|view-spmb'))
                                        <a href="{{ route('admin.waves.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.waves.*') ? 'nav-link-active' : '' }}">Gelombang SPMB</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('manage-spmb-settings'))
                                        <a href="{{ route('admin.spmb.settings') }}" class="nav-link text-xs {{ request()->routeIs('admin.spmb.settings') ? 'nav-link-active' : '' }}">Pengaturan SPMB</a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Bimbingan & Konseling Dropdown -->
                        @if(auth()->user()->hasPermission('view-bk|view-bk-counseling|view-bk-violations|manage-bk-categories|view-bk-assessments') || auth()->user()->hasRole('guru-bk|bk'))
                            @php
                                $isBkActive = request()->routeIs('admin.bk.*');
                            @endphp
                            <div x-data="{ open: {{ $isBkActive ? 'true' : 'false' }} }">
                                <button type="button" @click="open = !open" 
                                        class="nav-link w-full flex items-center justify-between transition-colors {{ $isBkActive ? 'text-[#3C50E0] dark:text-indigo-400 font-bold' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="nav-icon text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        <span>Bimbingan &amp; Konseling</span>
                                    </div>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                    @if(auth()->user()->hasPermission('view-bk-counseling|view-bk') || auth()->user()->hasRole('guru-bk|bk'))
                                        <a href="{{ route('admin.bk.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.bk.index') || request()->routeIs('admin.bk.show') || request()->routeIs('admin.bk.create') ? 'nav-link-active font-bold text-[#3C50E0]' : '' }}">Sesi Konseling Siswa</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('view-bk-violations|view-bk') || auth()->user()->hasRole('guru-bk|bk'))
                                        <a href="{{ route('admin.bk.violations.index') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.bk.violations.index') || request()->routeIs('admin.bk.violations.show') || request()->routeIs('admin.bk.violations.create') || request()->routeIs('admin.bk.violations.edit') ? 'nav-link-active font-bold text-[#3C50E0]' : '' }}">
                                            <span>Pelanggaran Siswa</span>
                                            @php
                                                try {
                                                    $violationPendingCount = \App\Models\BkStudentViolation::whereIn('status', ['pending', 'processed', 'sp1', 'sp2', 'sp3'])->count();
                                                } catch (\Throwable $e) {
                                                    $violationPendingCount = 0;
                                                }
                                            @endphp
                                            @if($violationPendingCount > 0)
                                                <span class="bg-rose-500 text-white text-[9px] font-extrabold px-1.5 py-0.5 rounded-full shadow-xs">
                                                    {{ $violationPendingCount > 99 ? '99+' : $violationPendingCount }}
                                                </span>
                                            @endif
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('manage-bk-categories|view-bk') || auth()->user()->hasRole('guru-bk|bk'))
                                        <a href="{{ route('admin.bk.violations.categories') }}" class="nav-link text-xs {{ request()->routeIs('admin.bk.violations.categories') ? 'nav-link-active font-bold text-[#3C50E0]' : '' }}">Kategori &amp; Poin Pelanggaran</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('view-bk-assessments|view-bk') || auth()->user()->hasRole('guru-bk|bk'))
                                        <a href="{{ route('admin.bk.assessments') }}" class="nav-link text-xs {{ request()->routeIs('admin.bk.assessments') ? 'nav-link-active font-bold text-[#3C50E0]' : '' }}">Asesmen &amp; Minat Bakat</a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Ekstrakurikuler Siswa -->
                        @if(auth()->user()->hasPermission('manage-extracurriculars|view-content'))
                            <a href="{{ route('admin.extracurriculars.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.extracurriculars.*') ? 'nav-link-active' : '' }}">
                                <svg class="nav-icon text-sky-500 dark:text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polygon points="12 8 8 12 12 16 16 12 12 8"></polygon>
                                </svg>
                                <span class="flex items-center justify-between w-full">
                                    <span>Ekstrakurikuler Siswa</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300">Kegiatan</span>
                                </span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- 5. Presensi & Kehadiran -->
                <div>
                    <p class="section-label">Presensi &amp; Kehadiran</p>
                    <div class="space-y-1">
                        @if(auth()->user()->hasRole('admin|super-admin|operator|kepala-sekolah|wakasek-kurikulum|wakasek-kesiswaan') || auth()->user()->hasPermission('view-teacher-attendance|view-employee-permits|manage-attendance'))
                        @php
                            $isTeacherAttendanceActive = request()->routeIs('admin.teacher-attendances.*') || request()->routeIs('admin.employee-permits.*') || request()->routeIs('admin.employee-tasks.*');
                            $isPermitApprover = auth()->user()->hasRole('kepala-sekolah') || auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
                            $pendingEmployeePermitsCount = 0;
                            if ($isPermitApprover) {
                                try {
                                    if (\Illuminate\Support\Facades\Schema::hasTable('employee_permits')) {
                                        $pendingEmployeePermitsCount = \App\Models\EmployeePermit::where('status', 'pending')->count();
                                    }
                                } catch (\Throwable $e) {
                                    $pendingEmployeePermitsCount = 0;
                                }
                            }
                        @endphp
                        <!-- Dropdown Presensi Guru & Tendik -->
                        <div x-data="{ open: {{ $isTeacherAttendanceActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                    class="nav-link w-full flex items-center justify-between transition-colors {{ $isTeacherAttendanceActive ? 'text-[#3C50E0] dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-cyan-500 dark:text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <span class="flex items-center gap-1.5">
                                        <span>Presensi Guru &amp; Tendik</span>
                                        @if($isPermitApprover && $pendingEmployeePermitsCount > 0)
                                            <span class="relative flex h-2 w-2" title="{{ $pendingEmployeePermitsCount }} Izin Menunggu">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                            </span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center space-x-1.5">
                                    @if($isPermitApprover && $pendingEmployeePermitsCount > 0)
                                        <span class="bg-rose-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full shadow-xs animate-pulse">
                                            {{ $pendingEmployeePermitsCount > 99 ? '99+' : $pendingEmployeePermitsCount }}
                                        </span>
                                    @endif
                                    <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </div>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                @if(auth()->user()->hasRole(['super-admin', 'admin', 'operator', 'kepala-sekolah']))
                                    <a href="{{ route('admin.teacher-attendances.index') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.teacher-attendances.index') ? 'nav-link-active' : '' }}">
                                        <span>Manajemen Presensi</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-employee-permits|approve-employee-permits|manage-attendance') || auth()->user()->isTeacher() || auth()->user()->hasRole(['guru', 'teacher', 'guru-quran', 'staff', 'operator', 'tata-usaha']))
                                    <a href="{{ route('admin.employee-permits.index') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.employee-permits.*') ? 'nav-link-active font-bold text-[#3C50E0]' : '' }}">
                                        <span>Izin &amp; Cuti Pegawai</span>
                                        @if($isPermitApprover && $pendingEmployeePermitsCount > 0)
                                            <span class="px-2 py-0.5 bg-rose-500 text-white font-black text-[10px] rounded-full shadow-xs animate-pulse">
                                                {{ $pendingEmployeePermitsCount }} Baru
                                            </span>
                                        @endif
                                    </a>
                                @endif
                                @if(auth()->user()->hasRole(['super-admin', 'admin', 'operator', 'kepala-sekolah']))
                                    <a href="{{ route('admin.teacher-attendances.fingerprint') }}" class="nav-link text-xs {{ request()->routeIs('admin.teacher-attendances.fingerprint') ? 'nav-link-active' : '' }}">Scanner Sidik Jari USB</a>
                                    <a href="{{ route('admin.teacher-attendances.scan') }}" target="_blank" rel="noopener" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.teacher-attendances.scan') ? 'nav-link-active' : '' }}">
                                        <span>Scanner Face ID Guru</span>
                                        <svg class="w-3 h-3 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <a href="{{ route('admin.teacher-attendances.recap') }}" class="nav-link text-xs {{ request()->routeIs('admin.teacher-attendances.recap') ? 'nav-link-active' : '' }}">Rekap Bulanan Guru</a>
                                @endif
                                @if(auth()->user()->hasPermission('view-employee-tasks|view-teacher-attendance|manage-attendance'))
                                    <a href="{{ route('admin.employee-tasks.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.employee-tasks.*') ? 'nav-link-active' : '' }}">Tugas &amp; Checklist Pegawai</a>
                                @endif
                                @if(auth()->user()->hasRole(['super-admin', 'admin', 'operator']))
                                    <a href="{{ route('admin.teacher-attendances.settings') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.teacher-attendances.settings') ? 'nav-link-active font-bold text-[#3C50E0]' : '' }}">
                                        <span>Pengaturan Presensi Guru</span>
                                        <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-extrabold text-[9px] rounded">Config</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Dropdown Presensi Siswa -->
                        @if(auth()->user()->hasPermission('view-student-attendance|view-student-permits|manage-student-attendance-settings|view-terminal-attendance|manage-attendance'))
                        @php
                            $isStudentAttendanceActive = request()->routeIs('admin.attendances.*') || request()->routeIs('admin.student-permits.*') || request()->routeIs('admin.qr-attendance.*');
                        @endphp
                        <div x-data="{ open: {{ $isStudentAttendanceActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                    class="nav-link w-full flex items-center justify-between transition-colors {{ $isStudentAttendanceActive ? 'text-[#3C50E0] dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-teal-500 dark:text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <polyline points="16 11 18 13 22 9"></polyline>
                                    </svg>
                                    <span>Presensi Siswa</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                @if(auth()->user()->hasPermission('view-student-attendance|manage-attendance'))
                                    <a href="{{ route('admin.attendances.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.attendances.index') ? 'nav-link-active' : '' }}">Rekap Presensi Siswa</a>
                                @endif
                                @if(auth()->user()->hasPermission('view-student-permits|manage-attendance'))
                                    <a href="{{ route('admin.student-permits.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.student-permits.*') ? 'nav-link-active' : '' }}">Permohonan Izin Siswa</a>
                                @endif
                                @if(auth()->user()->hasRole(['super-admin', 'admin', 'operator', 'wakasek-kesiswaan']))
                                    <a href="{{ route('admin.qr-attendance.scan') }}" target="_blank" rel="noopener" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.qr-attendance.*') ? 'nav-link-active' : '' }}">
                                        <span>Terminal Presensi (QR)</span>
                                        <svg class="w-3 h-3 text-cyan-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <a href="{{ route('admin.attendances.settings') }}" class="nav-link text-xs {{ request()->routeIs('admin.attendances.settings') ? 'nav-link-active' : '' }}">Pengaturan Presensi Siswa</a>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endif

                        <!-- Direct Personal Attendance Links (Accessible for everyone) -->
                        <div class="pt-1 space-y-1 border-t border-slate-100 dark:border-slate-800/80">
                            <a href="{{ route('admin.teacher-attendances.my-attendance') }}" 
                               class="nav-link text-xs {{ request()->routeIs('admin.teacher-attendances.my-attendance') ? 'nav-link-active font-bold text-[#3C50E0] dark:text-indigo-400' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-emerald-500 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/>
                                    </svg>
                                    <span>Presensi Mandiri Saya</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Dedicated Standalone Menu: Kajian Pekanan Pegawai (Terpisah dari Presensi) -->
                <div>
                    <p class="section-label">Kajian &amp; Pembinaan Pegawai</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.kajian-pekanan.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.kajian-pekanan.*') ? 'nav-link-active' : '' }}">
                            <svg class="nav-icon text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span class="flex items-center justify-between w-full">
                                <span class="font-bold">Kajian Pekanan Pegawai</span>
                                <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-extrabold text-[9px] rounded">Kegiatan</span>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Kinerja & Mutabaah Pegawai -->
                @if(auth()->user()->hasRole(['super-admin', 'admin', 'operator', 'kepala-sekolah', 'wakasek-kurikulum', 'wakasek-kesiswaan', 'guru', 'teacher', 'guru-quran', 'staff', 'tata-usaha']))
                <div>
                    <p class="section-label">Kinerja &amp; Mutabaah Pegawai</p>
                    <div class="space-y-1">
                        <!-- KPI & Performance Engine -->
                        <a href="{{ route('admin.kpi.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.kpi.*') ? 'nav-link-active' : '' }}">
                            <svg class="nav-icon text-indigo-500 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20V10"></path>
                                <path d="M18 20V4"></path>
                                <path d="M6 20v-4"></path>
                            </svg>
                            <span class="flex items-center justify-between w-full">
                                <span class="font-bold">Penilaian Kinerja (KPI)</span>
                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-extrabold text-[9px] rounded">Engine</span>
                            </span>
                        </a>

                        <!-- Dropdown Mutabaah Pegawai -->
                        @php
                            $isMutabaahActive = request()->routeIs('admin.employee-mutabaah.*');
                        @endphp
                        <div x-data="{ open: {{ $isMutabaahActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                    class="nav-link w-full flex items-center justify-between transition-colors {{ $isMutabaahActive ? 'text-[#3C50E0] dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-emerald-500 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 11l3 3L22 4"></path>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                    <span>Mutabaah Ibadah</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                <a href="{{ route('admin.employee-mutabaah.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.employee-mutabaah.index') ? 'nav-link-active' : '' }}">
                                    <span>Mutabaah Harian Saya</span>
                                </a>
                                @if(auth()->user()->hasRole(['super-admin', 'admin', 'operator', 'kepala-sekolah']))
                                    <a href="{{ route('admin.employee-mutabaah.recap') }}" class="nav-link text-xs {{ request()->routeIs('admin.employee-mutabaah.recap') ? 'nav-link-active' : '' }}">
                                        <span>Rekap Mutabaah Guru</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif


                <!-- 6. Keuangan & Operasional -->
                @if(auth()->user()->hasPermission('view-financial|view-student-payments|view-savings|view-financial-transactions|manage-financial|manage-payment-bills|view-payment-tracking|view-financial-reports|view-canteen-admin|manage-canteen-admin'))
                <div>
                    <p class="section-label">Keuangan &amp; Operasional</p>
                    <div class="space-y-1">
                        <!-- Keuangan & Kas Sekolah Dropdown -->
                        @if(auth()->user()->hasPermission('view-financial|view-student-payments|view-savings|view-financial-transactions|manage-financial|manage-payment-bills|view-payment-tracking|view-financial-reports'))
                        @php
                            $isFinancialActive = request()->routeIs('admin.student-payments.*') || request()->routeIs('admin.savings.*') || request()->routeIs('admin.payment-posts.*') || request()->routeIs('admin.payment-bills.*') || request()->routeIs('admin.financial-categories.*') || request()->routeIs('admin.financial-transactions.*') || request()->routeIs('admin.financial-reports.*');
                            try {
                                $pendingManualSPPCount = \App\Models\PaymentTransaction::where('reference_type', 'spp')->where('payment_gateway', 'manual')->where('status', 'pending')->count();
                                $pendingManualSavingsCount = \App\Models\PaymentTransaction::where('reference_type', 'savings_deposit')->where('payment_gateway', 'manual')->where('status', 'pending')->count();
                            } catch (\Throwable $e) {
                                $pendingManualSPPCount = 0;
                                $pendingManualSavingsCount = 0;
                            }
                            $totalPendingCount = $pendingManualSPPCount + $pendingManualSavingsCount;
                        @endphp
                        <div x-data="{ open: {{ $isFinancialActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                    class="nav-link w-full flex items-center justify-between transition-colors {{ $isFinancialActive ? 'text-[#3C50E0] dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-emerald-500 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                                        <line x1="2" y1="10" x2="22" y2="10"></line>
                                    </svg>
                                    <span class="flex items-center space-x-1.5">
                                        <span>Keuangan &amp; Kas</span>
                                        @if($totalPendingCount > 0)
                                            <span x-show="!open" class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse shrink-0"></span>
                                        @endif
                                    </span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                @if(auth()->user()->hasPermission('view-student-payments|view-financial'))
                                    <a href="{{ route('admin.student-payments.index') }}" class="nav-link text-xs {{ (request()->routeIs('admin.student-payments.index') || request()->routeIs('admin.student-payments.manual-confirm') || request()->routeIs('admin.student-payments.pay')) ? 'nav-link-active' : '' }} w-full flex items-center justify-between">
                                        <span>Pembayaran Siswa (POS)</span>
                                        @if($pendingManualSPPCount > 0)
                                            <span class="px-1.5 py-0.5 rounded-full bg-rose-500 text-white text-[9px] font-black leading-none animate-pulse shrink-0">{{ $pendingManualSPPCount }}</span>
                                        @endif
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-savings|view-financial'))
                                    <a href="{{ route('admin.savings.index') }}" class="nav-link text-xs {{ (request()->routeIs('admin.savings.index') || request()->routeIs('admin.savings.manual-confirm') || request()->routeIs('admin.savings.show')) ? 'nav-link-active' : '' }} w-full flex items-center justify-between">
                                        <span>Tabungan Siswa</span>
                                        @if($pendingManualSavingsCount > 0)
                                            <span class="px-1.5 py-0.5 rounded-full bg-rose-500 text-white text-[9px] font-black leading-none animate-pulse shrink-0">{{ $pendingManualSavingsCount }}</span>
                                        @endif
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('manage-financial'))
                                    <a href="{{ route('admin.financial-transactions.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.financial-transactions.*') || request()->routeIs('admin.financial-categories.*') ? 'nav-link-active' : '' }}">Jurnal &amp; Kas Sekolah</a>
                                    <a href="{{ route('admin.payment-bills.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.payment-bills.*') || request()->routeIs('admin.payment-posts.*') ? 'nav-link-active' : '' }}">Setting Tarif Tagihan</a>
                                @endif
                                @if(auth()->user()->hasPermission('view-payment-tracking|view-financial'))
                                    <a href="{{ route('admin.student-payments.tracking') }}" class="nav-link text-xs {{ request()->routeIs('admin.student-payments.tracking') ? 'nav-link-active' : '' }}">Tracking Tunggakan</a>
                                @endif
                                @if(auth()->user()->hasPermission('view-financial-reports|view-financial'))
                                    <a href="{{ route('admin.financial-reports.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.financial-reports.*') ? 'nav-link-active' : '' }}">Laporan Keuangan</a>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Digital E-Kantin Dropdown -->
                        @if(auth()->user()->hasPermission('view-canteen-admin|manage-canteen-admin'))
                        @php
                            $isCanteenActive = request()->routeIs('admin.canteen.*');
                            try {
                                $pendingWithdrawalsCount = \App\Models\CanteenWithdrawal::where('status', 'pending')->count();
                            } catch (\Throwable $e) {
                                $pendingWithdrawalsCount = 0;
                            }
                        @endphp
                        <div x-data="{ open: {{ $isCanteenActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                    class="nav-link w-full flex items-center justify-between transition-colors {{ $isCanteenActive ? 'text-[#3C50E0] dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="flex items-center space-x-1.5">
                                        <span>Digital E-Kantin</span>
                                        @if($pendingWithdrawalsCount > 0)
                                            <span class="px-1.5 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-full leading-none">{{ $pendingWithdrawalsCount }}</span>
                                        @endif
                                    </span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                <a href="{{ route('admin.canteen.orders') }}" class="nav-link text-xs {{ request()->routeIs('admin.canteen.orders') ? 'nav-link-active' : '' }}">Daftar Pesanan Siswa</a>
                                <a href="{{ route('admin.canteen.items') }}" class="nav-link text-xs {{ request()->routeIs('admin.canteen.items') ? 'nav-link-active' : '' }}">Menu &amp; Produk Kantin</a>
                                <a href="{{ route('admin.canteen.users') }}" class="nav-link text-xs {{ request()->routeIs('admin.canteen.users*') ? 'nav-link-active' : '' }}">Pengguna / Akun Kantin</a>
                                <a href="{{ route('admin.canteen.withdrawals') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.canteen.withdrawals*') ? 'nav-link-active' : '' }}">
                                    <span>Pencairan Saldo (Withdraw)</span>
                                    @if($pendingWithdrawalsCount > 0)
                                        <span class="px-1.5 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-full leading-none shrink-0">{{ $pendingWithdrawalsCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('admin.canteen.reports') }}" class="nav-link text-xs {{ request()->routeIs('admin.canteen.reports') ? 'nav-link-active' : '' }}">Laporan &amp; Analytics</a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- 7. Publikasi & Informasi -->
                @if(auth()->user()->hasPermission('view-posts|manage-posts|view-announcements|manage-announcements|view-gallery|manage-gallery|manage-sliders|manage-curriculum|view-broadcast|view-notifications|view-content'))
                <div>
                    <p class="section-label">Publikasi &amp; Informasi</p>
                    <div class="space-y-0.5">
                        <!-- Konten & Website Dropdown -->
                        @if(auth()->user()->hasPermission('view-posts|view-announcements|view-gallery|manage-sliders|manage-curriculum|view-content'))
                        @php
                            $isContentActive = request()->routeIs('admin.posts.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.gallery.*') || request()->routeIs('admin.announcements.*') || request()->routeIs('admin.sliders.*') || request()->routeIs('admin.curriculum.*');
                        @endphp
                        <div x-data="{ open: {{ $isContentActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                    class="nav-link w-full flex items-center justify-between transition-colors {{ $isContentActive ? 'text-[#3C50E0] dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                    <span>Konten &amp; Website</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                @if(auth()->user()->hasPermission('view-posts'))
                                    <a href="{{ route('admin.posts.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.posts.*') || request()->routeIs('admin.categories.*') ? 'nav-link-active' : '' }}">Berita &amp; Artikel</a>
                                @endif
                                @if(auth()->user()->hasPermission('view-announcements'))
                                    <a href="{{ route('admin.announcements.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.announcements.*') ? 'nav-link-active' : '' }}">Kegiatan &amp; Pengumuman</a>
                                @endif
                                @if(auth()->user()->hasPermission('view-gallery'))
                                    <a href="{{ route('admin.gallery.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.gallery.*') ? 'nav-link-active' : '' }}">Galeri Foto</a>
                                @endif
                                @if(auth()->user()->hasPermission('manage-sliders|view-content'))
                                    <a href="{{ route('admin.sliders.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.sliders.*') ? 'nav-link-active' : '' }}">Hero Banner Slider</a>
                                @endif
                                @if(auth()->user()->hasPermission('manage-curriculum|view-content'))
                                    <a href="{{ route('admin.curriculum.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.curriculum.*') ? 'nav-link-active' : '' }}">Kurikulum &amp; Program</a>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(auth()->user()->hasPermission('view-broadcast'))
                        <a href="{{ route('admin.wa-broadcasts.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.wa-broadcasts.*') ? 'nav-link-active' : '' }}">
                            <svg class="nav-icon text-emerald-500 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <span>Broadcast WhatsApp</span>
                        </a>
                        @endif
                        @if(auth()->user()->hasPermission('view-notifications'))
                        <a href="{{ route('admin.notifications.broadcast') }}" 
                           class="nav-link {{ request()->routeIs('admin.notifications.broadcast') ? 'nav-link-active' : '' }}">
                            <svg class="nav-icon text-indigo-500 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                            <span>Siaran Notifikasi (Push)</span>
                        </a>
                        <a href="{{ route('admin.notifications.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.notifications.index') ? 'nav-link-active' : '' }}">
                            <svg class="nav-icon text-amber-500 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            <span>Pusat Notifikasi</span>
                        </a>
                        @endif

                        <!-- Kritik & Saran Sekolah -->
                        <a href="{{ route('admin.feedback.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.feedback.*') ? 'nav-link-active font-bold text-indigo-600 dark:text-indigo-400' : '' }}">
                            <svg class="nav-icon text-teal-500 dark:text-teal-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <span>Kritik &amp; Saran Sekolah</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- 8. Sistem & Pengaturan -->
                @if(auth()->user()->hasPermission('view-roles|manage-roles|view-settings|edit-settings|view-security|manage-database-maintenance') || auth()->user()->hasRole('super-admin|admin'))
                <div>
                    <p class="section-label">Sistem &amp; Pengaturan</p>
                    <div class="space-y-0.5">
                        @php
                            $isSystemActive = request()->routeIs('admin.roles.*') || request()->routeIs('admin.profile-settings') || request()->routeIs('admin.settings') || request()->routeIs('admin.security.*');
                        @endphp
                        <div x-data="{ open: {{ $isSystemActive ? 'true' : 'false' }} }">
                            <button type="button" @click="open = !open" 
                                     class="nav-link w-full flex items-center justify-between transition-colors {{ $isSystemActive ? 'text-primary dark:text-indigo-400 font-bold' : '' }}">
                                <div class="flex items-center">
                                    <svg class="nav-icon text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                    </svg>
                                    <span>Pengaturan Sistem</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200 shrink-0 text-slate-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            <div x-show="open" x-collapse class="pl-4 space-y-1 mt-1 border-l-2 border-slate-200 dark:border-slate-800 ml-4">
                                @if(auth()->user()->hasPermission('view-roles|manage-roles') || auth()->user()->hasRole('super-admin|admin'))
                                    <a href="{{ route('admin.roles.index') }}" class="nav-link text-xs {{ request()->routeIs('admin.roles.*') ? 'nav-link-active' : '' }}">Role &amp; Hak Akses</a>
                                @endif
                                @if(auth()->user()->hasPermission('view-settings|edit-settings') || auth()->user()->hasRole('super-admin|admin'))
                                    <a href="{{ route('admin.profile-settings') }}" class="nav-link text-xs {{ request()->routeIs('admin.profile-settings') ? 'nav-link-active' : '' }}">Profil Website</a>
                                @endif
                                @if(auth()->user()->hasPermission('view-security') || auth()->user()->hasRole('super-admin|admin'))
                                    <a href="{{ route('admin.security.index') }}" class="nav-link text-xs flex items-center justify-between {{ request()->routeIs('admin.security.*') ? 'nav-link-active' : '' }}">
                                        <span>Keamanan Sistem</span>
                                        <span class="px-1.5 py-0.5 bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 font-extrabold text-[9px] rounded">Pro</span>
                                    </a>
                                @endif
                                @if(auth()->user()->hasPermission('view-settings|edit-settings') || auth()->user()->hasRole('super-admin|admin'))
                                    <a href="{{ route('admin.settings') }}" class="nav-link text-xs {{ request()->routeIs('admin.settings') ? 'nav-link-active' : '' }}">Konfigurasi Web</a>
                                @endif
                            </div>
                        </div>

                        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin') || auth()->user()->hasPermission('manage-database-maintenance'))
                        @php
                            $pendingMigrationsCount = 0;
                            try {
                                $files = \Illuminate\Support\Facades\File::exists(database_path('migrations')) ? \Illuminate\Support\Facades\File::files(database_path('migrations')) : [];
                                $executed = \Illuminate\Support\Facades\DB::table('migrations')->pluck('migration')->toArray();
                                foreach($files as $f) {
                                    if(!in_array(pathinfo($f->getFilename(), PATHINFO_FILENAME), $executed)) {
                                        $pendingMigrationsCount++;
                                    }
                                }
                            } catch(\Throwable $e) {}
                        @endphp
                        <a href="{{ route('admin.database-maintenance.index') }}" 
                           class="nav-link flex items-center justify-between {{ request()->routeIs('admin.database-maintenance.*') ? 'nav-link-active font-bold' : '' }}">
                            <div class="flex items-center">
                                <svg class="nav-icon text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                                </svg>
                                <span>Database &amp; Backup</span>
                            </div>
                            @if($pendingMigrationsCount > 0)
                                <span class="px-1.5 py-0.5 rounded-full bg-rose-500 text-white text-[9px] font-black leading-none animate-pulse shrink-0">
                                    {{ $pendingMigrationsCount }}
                                </span>
                            @endif
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- 9. Bantuan & Panduan -->
                <div>
                    <p class="section-label">Bantuan &amp; Panduan</p>
                    <div class="space-y-0.5">
                        <a href="{{ route('admin.guide') }}" target="_blank" rel="noopener"
                           class="nav-link flex items-center justify-between {{ request()->routeIs('admin.guide') ? 'nav-link-active font-bold' : '' }}">
                            <div class="flex items-center">
                                <svg class="nav-icon text-indigo-500 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <span class="font-bold">Panduan Pengguna</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- TailAdmin User Profile Card Footer -->
            <div class="p-4 border-t border-[#E2E8F0] dark:border-[#2E3A47] bg-[#F8FAFC] dark:bg-[#1C2434]">
                <div class="flex items-center space-x-3 p-2 rounded-xl bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] mb-3">
                    <div class="relative">
                        <img src="{{ auth()->user()->avatar ? asset('img/avatars/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=' . str_replace('#', '', Setting::get('primary_color', '3C50E0')) . '&color=fff' }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-9 h-9 rounded-xl object-cover ring-2 ring-primary/50">
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full ring-2 ring-white dark:ring-[#1C2434]"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-extrabold text-[#1C2434] dark:text-white text-xs truncate tracking-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-[#64748B] dark:text-[#8A99AD] font-bold truncate tracking-wider">{{ auth()->user()->roles->first()->name ?? 'Administrator' }}</p>
                    </div>
                </div>

                <!-- Install App Sidebar Button -->
                <button type="button" 
                        onclick="triggerPwaInstall()" 
                        class="w-full flex items-center justify-center space-x-2 px-3 py-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-600 hover:text-white rounded-xl transition-all duration-200 border border-indigo-200 dark:border-indigo-800 font-bold text-xs group shadow-2xs mb-2 cursor-pointer">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Install Aplikasi</span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-3 py-2.5 bg-rose-500/10 text-rose-600 dark:text-rose-300 hover:bg-rose-600 hover:text-white rounded-xl transition-all duration-200 border border-rose-500/20 font-bold text-[10px] uppercase tracking-widest group shadow-xs">
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- TailAdmin Main Workspace -->
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">
            <!-- TailAdmin Top Header -->
            <header class="h-20 tailadmin-header flex items-center justify-between px-6 lg:px-8 sticky top-0 z-30">
                <div class="flex items-center space-x-4">
                    <!-- Mobile Sidebar Toggle Button -->
                    <button @click="sidebarOpen = true" class="lg:hidden text-[#64748B] dark:text-[#8A99AD] hover:text-[#1C2434] dark:hover:text-white p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-[#1A222C] transition-colors focus:outline-none">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>

                    <!-- TailAdmin Breadcrumb Header -->
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-extrabold text-[#1C2434] dark:text-white tracking-tight">@yield('page_title', 'Dashboard')</h1>
                        <nav class="flex items-center space-x-2 text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mt-0.5">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">Admin</a>
                            <span>/</span>
                            <span class="text-primary">@yield('title', 'Dashboard')</span>
                        </nav>
                    </div>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <!-- TailAdmin Dark / Light Mode Switcher Pill -->
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
                        <button @click="toggleTheme()" type="button" class="relative inline-flex h-8 w-14 items-center rounded-full bg-[#E2E8F0] dark:bg-[#2E3A47] p-1 transition-colors duration-300 focus:outline-none" title="Switch Light/Dark Mode">
                            <span :class="isDark ? 'translate-x-6 bg-primary' : 'translate-x-0 bg-white'" class="inline-block h-6 w-6 transform rounded-full shadow-md transition-transform duration-300 flex items-center justify-center">
                                <svg x-show="!isDark" class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                                </svg>
                                <svg x-show="isDark" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                                </svg>
                            </span>
                        </button>
                    </div>

                    <!-- Front Website Link Button -->
                    <a href="{{ url('/') }}" target="_blank" class="hidden sm:inline-flex items-center space-x-1.5 px-3.5 py-2 bg-[#F8FAFC] dark:bg-[#1A222C] hover:bg-[#E2E8F0] dark:hover:bg-[#2E3A47] text-[#1C2434] dark:text-white rounded-xl text-xs font-bold transition-all border border-[#E2E8F0] dark:border-[#2E3A47] shadow-2xs" title="Buka Website Portal">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                        <span>Website</span>
                    </a>

                    <!-- Install App Button -->
                    <button type="button" 
                            onclick="triggerPwaInstall()" 
                            class="inline-flex items-center space-x-1.5 px-3.5 py-2 bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-600 hover:text-white text-indigo-700 dark:text-indigo-300 rounded-xl text-xs font-bold transition-all border border-indigo-200 dark:border-indigo-800 shadow-2xs cursor-pointer group" 
                            title="Pasang aplikasi di HP atau Komputer Anda">
                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span class="hidden md:inline">Install App</span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false">
                        <button @click="notifOpen = !notifOpen" class="relative p-2.5 text-[#64748B] dark:text-[#8A99AD] hover:text-primary hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition-all duration-200 focus:outline-none" title="Notifikasi Sistem">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                            @if(($notificationCount ?? 0) > 0)
                                <span class="absolute top-2 right-2 flex h-2.5 w-2.5">
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500 ring-2 ring-white dark:ring-[#24303F]"></span>
                                </span>
                            @endif
                        </button>

                        <div x-show="notifOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                             class="fixed left-4 right-4 top-16 sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-3 w-auto sm:w-96 bg-white dark:bg-[#24303F] rounded-2xl shadow-2xl border border-[#E2E8F0] dark:border-[#2E3A47] py-3 z-50 overflow-hidden"
                             x-cloak>
                            
                            <div class="px-4 py-2.5 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <h3 class="font-bold text-[#1C2434] dark:text-white text-xs uppercase tracking-wider">Notifikasi Sistem</h3>
                                    @if(($notificationCount ?? 0) > 0)
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold bg-primary/10 text-primary rounded-md border border-primary/20">
                                            {{ $notificationCount }} Baru
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] max-h-80 sm:max-h-96 overflow-y-auto">
                                @forelse(($recentNotifications ?? []) as $notif)
                                    <a href="{{ $notif['url'] }}" class="p-3.5 hover:bg-[#F1F5F9] dark:hover:bg-[#1A222C] flex items-start space-x-3 transition-colors block">
                                        <div class="w-8 h-8 rounded-lg {{ $notif['icon_bg'] }} flex items-center justify-center shrink-0 mt-0.5">
                                            @if($notif['category'] === 'spmb')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            @elseif($notif['category'] === 'payment')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @elseif($notif['category'] === 'cbt')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            @elseif($notif['category'] === 'permit')
                                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1">
                                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded border {{ $notif['badge_class'] }} shrink-0">
                                                    {{ $notif['badge'] }}
                                                </span>
                                                <span class="text-[10px] text-[#64748B] dark:text-[#8A99AD] shrink-0">
                                                    {{ $notif['created_at'] ? $notif['created_at']->diffForHumans() : '-' }}
                                                </span>
                                            </div>
                                            <h4 class="text-xs font-semibold text-[#1C2434] dark:text-white mt-1 truncate">{{ $notif['title'] }}</h4>
                                            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5 line-clamp-2 leading-relaxed break-words">{{ $notif['message'] }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-6 text-center text-[#64748B] dark:text-[#8A99AD] text-xs">
                                        Tidak ada notifikasi saat ini.
                                    </div>
                                @endforelse
                            </div>

                            <div class="px-4 pt-3 pb-1 border-t border-[#E2E8F0] dark:border-[#2E3A47] text-center">
                                <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center justify-center text-xs font-bold text-primary hover:underline py-1 w-full">
                                    <span>Lihat Semua Notifikasi</span>
                                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile Dropdown Pill -->
                    <div class="relative" x-data="{ profileOpen: false }" @click.outside="profileOpen = false">
                        <button @click="profileOpen = !profileOpen" class="flex items-center space-x-3 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-[#1A222C] transition-all duration-200 focus:outline-none group">
                            <img src="{{ auth()->user()->avatar ? asset('img/avatars/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=' . str_replace('#', '', Setting::get('primary_color', '3C50E0')) . '&color=fff' }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="w-9 h-9 rounded-full object-cover ring-2 ring-primary/30 shadow-xs">
                            <div class="hidden md:flex flex-col items-start text-left">
                                <span class="text-xs font-extrabold text-[#1C2434] dark:text-white leading-tight group-hover:text-primary transition-colors">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-[#64748B] dark:text-[#8A99AD] font-semibold uppercase tracking-wider">{{ auth()->user()->roles->first()->name ?? 'Administrator' }}</span>
                            </div>
                            <svg class="w-4 h-4 text-[#64748B] dark:text-[#8A99AD] transition-transform duration-200" :class="profileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="profileOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                             class="absolute right-0 mt-2 w-56 bg-white dark:bg-[#24303F] rounded-2xl shadow-xl border border-[#E2E8F0] dark:border-[#2E3A47] py-2 z-50 overflow-hidden"
                             x-cloak>
                            <div class="px-4 py-3 border-b border-[#E2E8F0] dark:border-[#2E3A47] bg-slate-50/50 dark:bg-[#1A222C]/50">
                                <p class="text-xs font-bold text-[#1C2434] dark:text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] font-medium truncate mt-0.5">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-1">
                                @if(auth()->user()->hasPermission('view-settings'))
                                    <a href="{{ route('admin.settings') }}" class="flex items-center space-x-2.5 px-4 py-2.5 text-xs font-bold text-[#1C2434] dark:text-white hover:bg-primary/10 hover:text-primary transition-colors">
                                        <svg class="w-4 h-4 text-[#64748B] dark:text-[#8A99AD]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                        </svg>
                                        <span>Konfigurasi Web</span>
                                    </a>
                                @endif
                                <a href="{{ url('/') }}" target="_blank" class="flex items-center space-x-2.5 px-4 py-2.5 text-xs font-bold text-[#1C2434] dark:text-white hover:bg-primary/10 hover:text-primary transition-colors">
                                    <svg class="w-4 h-4 text-[#64748B] dark:text-[#8A99AD]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                    <span>Buka Website</span>
                                </a>
                            </div>
                            <div class="border-t border-[#E2E8F0] dark:border-[#2E3A47] p-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center space-x-2.5 px-3 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl text-xs font-bold transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                        <span>Keluar Sistem</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- TailAdmin Main Content Area -->
            @hasSection('no_footer')
                <main class="flex-1 overflow-hidden p-4 lg:p-5 bg-[#F1F5F9] dark:bg-[#1A222C] flex flex-col justify-between">
                    <div class="h-full flex flex-col min-h-0">
                        @if(session('success'))
                            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mb-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 px-5 py-4 rounded-2xl flex items-center justify-between shadow-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-emerald-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">Berhasil</h4>
                                        <p class="text-sm font-medium text-emerald-950 dark:text-emerald-100 mt-0.5">{{ session('success') }}</p>
                                    </div>
                                </div>
                                <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 font-bold p-1 rounded-lg hover:bg-emerald-100/50 transition-colors">&times;</button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mb-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 px-5 py-4 rounded-2xl flex items-center justify-between shadow-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-rose-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-800 dark:text-rose-400">Perhatian</h4>
                                        <p class="text-sm font-medium text-rose-950 dark:text-rose-100 mt-0.5">{{ session('error') }}</p>
                                    </div>
                                </div>
                                <button @click="show = false" class="text-rose-500 hover:text-rose-800 font-bold p-1 rounded-lg hover:bg-rose-100/50 transition-colors">&times;</button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mb-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 px-5 py-4 rounded-2xl flex items-start justify-between shadow-xs">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-rose-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs mt-0.5">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-800 dark:text-rose-400">Ada Kesalahan Form</h4>
                                        <ul class="text-xs font-medium text-rose-950 dark:text-rose-100 mt-1 list-disc list-inside space-y-0.5">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <button @click="show = false" class="text-rose-500 hover:text-rose-800 font-bold p-1 rounded-lg hover:bg-rose-100/50 transition-colors">&times;</button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </main>
            @else
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8 bg-[#F1F5F9] dark:bg-[#1A222C] flex flex-col justify-between">
                    <div>
                        @if(session('success'))
                            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mb-6 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 px-5 py-4 rounded-2xl flex items-center justify-between shadow-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-emerald-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">Berhasil</h4>
                                        <p class="text-sm font-medium text-emerald-950 dark:text-emerald-100 mt-0.5">{{ session('success') }}</p>
                                    </div>
                                </div>
                                <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 font-bold p-1 rounded-lg hover:bg-emerald-100/50 transition-colors">&times;</button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mb-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 px-5 py-4 rounded-2xl flex items-center justify-between shadow-xs">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-rose-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-800 dark:text-rose-400">Perhatian</h4>
                                        <p class="text-sm font-medium text-rose-950 dark:text-rose-100 mt-0.5">{{ session('error') }}</p>
                                    </div>
                                </div>
                                <button @click="show = false" class="text-rose-500 hover:text-rose-800 font-bold p-1 rounded-lg hover:bg-rose-100/50 transition-colors">&times;</button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mb-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200 px-5 py-4 rounded-2xl flex items-start justify-between shadow-xs">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-rose-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs mt-0.5">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-rose-800 dark:text-rose-400">Ada Kesalahan Form</h4>
                                        <ul class="text-xs font-medium text-rose-950 dark:text-rose-100 mt-1 list-disc list-inside space-y-0.5">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <button @click="show = false" class="text-rose-500 hover:text-rose-800 font-bold p-1 rounded-lg hover:bg-rose-100/50 transition-colors">&times;</button>
                            </div>
                        @endif

                        @yield('content')
                    </div>

                    <footer class="mt-12 pt-6 border-t border-[#E2E8F0] dark:border-[#2E3A47] flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-semibold text-[#64748B] dark:text-[#8A99AD]">
                        <div class="flex items-center space-x-2">
                            <span>&copy; {{ date('Y') }} <strong class="text-[#1C2434] dark:text-white font-extrabold">{{ Setting::get('school_name', 'Sekolah') }}</strong>. All rights reserved.</span>
                        </div>
                    </footer>
                </main>
            @endif
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- UNIVERSAL MOBILE BOTTOM NAVIGATION BAR (lg:hidden) -->
    <!-- ============================================================ -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-[#1A222C]/95 backdrop-blur-md border-t border-slate-200 dark:border-[#2E3A47] px-2 py-1.5 shadow-lg shadow-black/5">
        <div class="grid grid-cols-5 gap-1 items-center max-w-md mx-auto">
            
            <!-- Beranda -->
            <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('admin.dashboard') ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[10px] tracking-tight mt-0.5">Beranda</span>
            </a>

            <!-- Tugas & Checklist -->
            <a href="{{ route('admin.employee-tasks.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ request()->routeIs('admin.employee-tasks.*') ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('admin.employee-tasks.*') ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span class="text-[10px] tracking-tight mt-0.5">Tugas</span>
            </a>

            <!-- Presensi (Pusat Aksi) -->
            <a href="{{ auth()->user()->hasRole('admin|super-admin|operator') ? route('admin.teacher-attendances.index') : route('admin.teacher-attendances.my-attendance') }}" class="flex flex-col items-center justify-center py-1 px-1 transition-all group">
                <div class="w-10 h-10 rounded-2xl text-white flex items-center justify-center shadow-md shadow-primary/30 transform -translate-y-2 group-hover:scale-105 transition-transform" style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#3C50E0') }} 0%, {{ Setting::get('secondary_color', '#2563eb') }} 100%);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[10px] font-extrabold tracking-tight -mt-1.5 {{ request()->routeIs('admin.teacher-attendances.*') ? 'text-primary' : 'text-slate-600 dark:text-slate-300' }}">Presensi</span>
            </a>

            @php
                $currUser = auth()->user();
                $isQuranGuru = $currUser->hasRole(['guru-quran', 'guru_quran', 'guru-qur-an']);
                $isMapelGuru = $currUser->hasRole(['guru', 'teacher', 'guru-mapel', 'guru-kelas']) || ($currUser->isTeacher() && !$isQuranGuru);
                $isKepsek = $currUser->hasRole(['kepala-sekolah', 'kepsek']);
                $isBkUser = $currUser->hasRole(['bk', 'guru-bk', 'konselor']);
                $isTreasurer = $currUser->hasRole(['bendahara', 'bendahara-sekolah']);
                $isOperatorOrAdmin = $currUser->hasRole(['super-admin', 'admin', 'operator']);
                $isStaffTendik = $currUser->hasRole(['staff', 'tata-usaha', 'tu']);
            @endphp

            @if($isQuranGuru)
                <!-- 1. Guru Al-Qur'an: Halaqah -->
                <a href="{{ route('admin.halaqah.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ request()->routeIs('admin.halaqah.*') ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('admin.halaqah.*') ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span class="text-[10px] tracking-tight mt-0.5">Halaqah</span>
                </a>
            @elseif($isMapelGuru)
                <!-- 2. Guru Bidang Studi / Mapel / Guru Kelas: Agenda & Penilaian Mapel -->
                <a href="{{ route('admin.grades.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ request()->routeIs('admin.grades.*') ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('admin.grades.*') ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-[10px] tracking-tight mt-0.5">Agenda &amp; Nilai</span>
                </a>
            @elseif($isKepsek)
                <!-- 3. Kepala Sekolah: Approval Izin Pegawai -->
                @php
                    $pendingPermitsCount = 0;
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('employee_permits')) {
                            $pendingPermitsCount = \App\Models\EmployeePermit::where('status', 'pending')->count();
                        }
                    } catch (\Throwable $e) {}
                @endphp
                <a href="{{ route('admin.employee-permits.index') }}" class="relative flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ request()->routeIs('admin.employee-permits.*') ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                    <div class="relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('admin.employee-permits.*') ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @if($pendingPermitsCount > 0)
                            <span class="absolute -top-1 -right-1.5 flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] tracking-tight mt-0.5">Approval Izin</span>
                </a>
            @elseif($isBkUser)
                <!-- 4. Guru BK: Konseling Siswa -->
                <a href="{{ route('admin.bk.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ request()->routeIs('admin.bk.*') ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('admin.bk.*') ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[10px] tracking-tight mt-0.5">Konseling BK</span>
                </a>
            @elseif($isTreasurer)
                <!-- 5. Bendahara: Kas & SPP -->
                <a href="{{ route('admin.student-payments.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ (request()->routeIs('admin.student-payments.*') || request()->routeIs('admin.financial*')) ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ (request()->routeIs('admin.student-payments.*') || request()->routeIs('admin.financial*')) ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-[10px] tracking-tight mt-0.5">Kas &amp; SPP</span>
                </a>
            @elseif($isOperatorOrAdmin)
                <!-- 6. Admin / Operator: Direktori Data Siswa -->
                <a href="{{ route('admin.students.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ request()->routeIs('admin.students.*') ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('admin.students.*') ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="text-[10px] tracking-tight mt-0.5">Data Siswa</span>
                </a>
            @elseif($isStaffTendik)
                <!-- 7. Staff Tendik (Non-Guru): Mutabaah Ibadah Harian -->
                <a href="{{ route('admin.employee-mutabaah.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ request()->routeIs('admin.employee-mutabaah.*') ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('admin.employee-mutabaah.*') ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    <span class="text-[10px] tracking-tight mt-0.5">Mutabaah</span>
                </a>
            @else
                <!-- 8. Default Fallback -->
                <a href="{{ route('admin.grades.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ request()->routeIs('admin.grades.*') ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('admin.grades.*') ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-[10px] tracking-tight mt-0.5">Agenda &amp; Nilai</span>
                </a>
            @endif

            <!-- Profil Akun -->
            <a href="{{ route('admin.profile') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition-all {{ request()->routeIs('admin.profile') ? 'text-[#3C50E0] dark:text-indigo-400 font-extrabold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="{{ request()->routeIs('admin.profile') ? '2.5' : '2' }}"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] tracking-tight mt-0.5">Profil</span>
            </a>

        </div>
    </nav>

    @yield('scripts')
    @stack('scripts')

    <!-- TomSelect JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
</body>
</html>
