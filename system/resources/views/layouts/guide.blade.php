<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Panduan Pengguna Admin') - {{ Setting::get('school_name', 'School') }}</title>
    
    <!-- Anti-FOUC Dark Mode Initializer -->
    <script>
        (function() {
            var savedTheme = localStorage.getItem('guide_theme_mode') || localStorage.getItem('theme_mode');
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
                        darkbg: '#0F172A',
                        darkcard: '#1E293B',
                    }
                }
            }
        }
    </script>
    
    <!-- AlpineJS CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #0F172A;
            -webkit-font-smoothing: antialiased;
        }

        html.dark body {
            background-color: #0F172A;
            color: #F1F5F9;
        }

        /* Custom Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        html.dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3C50E0;
        }

        /* Print Media Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #FFFFFF !important;
                color: #000000 !important;
            }
            .print-full-width {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .card-shadow {
                box-shadow: none !important;
                border: 1px solid #E2E8F0 !important;
            }
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased transition-colors duration-200"
      x-data="guideApp()">

    <!-- Header Navigation Bar (No Print) -->
    <header class="no-print sticky top-0 z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 transition-colors shadow-xs">
        <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-10">
            <div class="flex items-center justify-between h-16 sm:h-20 gap-4">
                
                <!-- Brand / Logo & Title -->
                <div class="flex items-center space-x-3 shrink-0">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-blue-500 flex items-center justify-center p-1.5 shadow-md shrink-0 border border-white/20 overflow-hidden">
                            <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="{{ Setting::get('school_name', 'School') }}" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h1 class="font-extrabold text-slate-900 dark:text-white text-base sm:text-lg tracking-tight group-hover:text-primary transition-colors">
                                    Panduan Pengguna
                                </h1>
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-extrabold text-[10px] uppercase rounded-full tracking-wider border border-indigo-200 dark:border-indigo-800">
                                    V2.5 Full Edition
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate max-w-[200px] sm:max-w-xs">
                                {{ Setting::get('school_name', 'Sistem Informasi Sekolah') }}
                            </p>
                        </div>
                    </a>
                </div>

                <!-- Global Search Bar -->
                <div class="flex-1 max-w-md hidden md:block">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               x-model="searchQuery" 
                               @input="filterSections()"
                               placeholder="Cari fitur, menu, atau kata kunci panduan..." 
                               class="w-full pl-9 pr-8 py-2 text-xs rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary focus:outline-none transition-all">
                        <button x-show="searchQuery.length > 0" 
                                @click="searchQuery = ''; filterSections();" 
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Right Quick Actions -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    
                    <!-- Print Button -->
                    <button @click="window.print()" 
                            title="Cetak atau Simpan PDF Dokumentasi"
                            class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition-all shadow-2xs">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span class="hidden sm:inline">Cetak PDF</span>
                    </button>

                    <!-- Theme Toggle -->
                    <button @click="toggleTheme()" 
                            title="Beralih Mode Gelap/Terang"
                            class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors border border-slate-200 dark:border-slate-700">
                        <svg x-show="!isDarkMode" class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg x-show="isDarkMode" class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <!-- Back to Admin Panel Button -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-primary text-white hover:opacity-95 transition-all shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span class="hidden sm:inline">Panel Admin</span>
                    </a>
                </div>

            </div>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="flex-1 max-w-[1700px] w-full mx-auto px-4 sm:px-6 lg:px-8 xl:px-10 py-6 sm:py-8 print-full-width">
        @yield('content')
    </main>

    <!-- Footer Documentation (No Print) -->
    <footer class="no-print bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-6 transition-colors mt-auto">
        <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-10 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400">
            <div class="flex items-center space-x-2">
                <span class="font-bold text-slate-700 dark:text-slate-300">{{ Setting::get('school_name', 'Sekolah') }}</span>
                <span>&bull;</span>
                <span>Dokumentasi Resmi Panduan Pengguna &amp; Sistem Manajemen Terpadu</span>
            </div>
            <div class="flex items-center space-x-4">
                <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="hover:text-primary transition-colors flex items-center space-x-1 font-semibold">
                    <span>Kembali ke Atas</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </button>
            </div>
        </div>
    </footer>

    <!-- Cool Circular Floating Scroll To Top Widget with Progress Bar (No Print) -->
    <div x-data="{ show: false, progress: 0, circumference: 138.23 }"
         x-init="window.addEventListener('scroll', () => {
             show = window.scrollY > 200;
             const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
             const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
             const pct = height > 0 ? (winScroll / height) : 0;
             progress = Math.min(Math.max(pct, 0), 1);
         })"
         x-show="show"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-6 scale-75"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-75"
         class="no-print fixed bottom-6 right-6 z-50 flex items-center space-x-3 group">
        
        <!-- Floating Tooltip Badge on Hover -->
        <span class="hidden sm:inline-block px-3 py-1.5 rounded-full bg-slate-900/90 dark:bg-slate-800/90 text-white text-[11px] font-extrabold shadow-lg border border-slate-700/60 backdrop-blur-md opacity-0 group-hover:opacity-100 transition-all duration-200 -translate-x-2 group-hover:translate-x-0">
            Kembali ke Atas (<span x-text="Math.round(progress * 100) + '%'"></span>)
        </span>

        <!-- Circular Glassmorphism Progress Button -->
        <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                title="Kembali ke Atas"
                class="relative w-14 h-14 rounded-full bg-white/90 dark:bg-slate-900/90 backdrop-blur-md text-indigo-600 dark:text-indigo-400 shadow-2xl shadow-indigo-500/30 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-center transition-all duration-300 hover:scale-110 active:scale-95 group-hover:shadow-indigo-500/50">
            
            <!-- SVG Circular Progress Ring Bar -->
            <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none p-0.5" viewBox="0 0 52 52">
                <!-- Track Ring -->
                <circle cx="26" cy="26" r="22" 
                        class="text-slate-200 dark:text-slate-800 stroke-current" 
                        stroke-width="3" 
                        fill="none" />
                <!-- Progress Ring -->
                <circle cx="26" cy="26" r="22" 
                        class="text-indigo-600 dark:text-indigo-400 stroke-current transition-all duration-150 ease-out" 
                        stroke-width="3.5" 
                        stroke-linecap="round" 
                        fill="none" 
                        :stroke-dasharray="circumference" 
                        :stroke-dashoffset="circumference - (progress * circumference)" />
            </svg>

            <!-- Up Arrow Icon -->
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 group-hover:-translate-y-1 transition-transform duration-200 relative z-10" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
            </svg>
        </button>
    </div>

    <!-- AlpineJS Controller Script -->
    <script>
        function guideApp() {
            return {
                isDarkMode: document.documentElement.classList.contains('dark'),
                searchQuery: '',
                activeSection: 'menu-dashboard',
                
                init() {
                    this.$nextTick(() => {
                        this.setupScrollSpy();
                    });
                },

                setupScrollSpy() {
                    const sections = document.querySelectorAll('section[id]');
                    if (!sections.length) return;

                    const observerOptions = {
                        root: null,
                        rootMargin: '-15% 0px -50% 0px',
                        threshold: 0
                    };

                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                this.activeSection = entry.target.id;
                                const activeLink = document.querySelector(`nav a[href="#${entry.target.id}"]`);
                                if (activeLink) {
                                    activeLink.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                                }
                            }
                        });
                    }, observerOptions);

                    sections.forEach(section => observer.observe(section));
                },

                toggleTheme() {
                    this.isDarkMode = !this.isDarkMode;
                    if (this.isDarkMode) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('guide_theme_mode', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('guide_theme_mode', 'light');
                    }
                },

                filterSections() {
                    const q = this.searchQuery.toLowerCase().trim();
                    const cards = document.querySelectorAll('.guide-card');
                    
                    cards.forEach(card => {
                        if (!q) {
                            card.style.display = 'block';
                        } else {
                            const text = card.innerText.toLowerCase();
                            if (text.includes(q)) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        }
                    });
                }
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
