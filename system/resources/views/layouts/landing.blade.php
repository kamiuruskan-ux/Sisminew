<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden antialiased">
<head>
    @php
        $seoTitle       = Setting::get('seo_title', Setting::get('school_name', 'Portal Sekolah') . ' - ' . Setting::get('school_tagline', 'Berkarakter • Berprestasi • Mendunia'));
        $seoDesc        = Setting::get('seo_description', Setting::get('school_description', ''));
        $seoKeywords    = Setting::get('seo_keywords', '');
        $seoCanonical   = Setting::get('seo_canonical_url', url('/'));
        $seoRobots      = Setting::get('seo_robots', 'index, follow');
        $googleVerif    = Setting::get('google_site_verification', '');
        $ogTitle        = Setting::get('og_title', $seoTitle);
        $ogDesc         = Setting::get('og_description', $seoDesc);
        $schoolLogo     = Setting::get('school_logo');
        $schoolLogoUrl  = Setting::getLogoUrl();
        $ogImageSetting = Setting::get('og_image');
        $ogImage        = $ogImageSetting ? (\Illuminate\Support\Str::startsWith($ogImageSetting, 'img/') ? asset($ogImageSetting) : asset('img/' . $ogImageSetting)) : $schoolLogoUrl;
        $ogUrl          = Setting::get('og_url', url('/'));
        $ogType         = Setting::get('og_type', 'website');
        $fbAppId        = Setting::get('fb_app_id', '');
        $twCard         = Setting::get('twitter_card', 'summary_large_image');
        $twSite         = Setting::get('twitter_site', '');
        $twTitle        = Setting::get('twitter_title', $seoTitle);
        $twDesc         = Setting::get('twitter_description', $seoDesc);
        $schoolName     = Setting::get('school_name', 'Sekolah');
        $logoUrl        = $schoolLogoUrl;
        $faviconUrl     = Setting::getFaviconUrl();
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ===== SEO DASAR ===== --}}
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDesc }}">
    @if($seoKeywords)
    <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    <meta name="robots" content="{{ $seoRobots }}">
    <link rel="canonical" href="{{ $seoCanonical }}">
    @if($googleVerif)
    <meta name="google-site-verification" content="{{ $googleVerif }}">
    @endif

    {{-- ===== OPEN GRAPH (Facebook / WhatsApp / Telegram / LinkedIn) ===== --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDesc }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ $ogUrl }}">
    <meta property="og:site_name" content="{{ $schoolName }}">
    <meta property="og:locale" content="id_ID">
    @if($fbAppId)
    <meta property="fb:app_id" content="{{ $fbAppId }}">
    @endif

    {{-- ===== TWITTER / X CARD ===== --}}
    <meta name="twitter:card" content="{{ $twCard }}">
    <meta name="twitter:title" content="{{ $twTitle }}">
    <meta name="twitter:description" content="{{ $twDesc }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    @if($twSite)
    <meta name="twitter:site" content="{{ $twSite }}">
    @endif

    {{-- ===== FAVICON ===== --}}
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '{{ Setting::get('primary_color', '#6366f1') }}',
                        secondary: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                        navy: {
                            800: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 18%, #080d1a)',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 12%, #050914)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 6%, #03050b)',
                        },
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
                        }
                    },
                    boxShadow: {
                        card: '0 10px 30px -5px rgba(14, 30, 75, 0.08)',
                        'card-hover': '0 20px 40px -10px rgba(14, 30, 75, 0.14)',
                        glow: '0 0 25px {{ Setting::get('primary_color', '#6366f1') }}59',
                    }
                }
            }
        };
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: {{ Setting::get('primary_color', '#6366f1') }};
            --secondary-color: {{ Setting::get('secondary_color', '#4f46e5') }};
            --font-main: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        body { font-family: var(--font-main); background: #ffffff; color: #0f172a; overflow-x: hidden; }
        [x-cloak] { display: none !important; }

        .container-edunova { max-width: 82rem; margin: 0 auto; padding-inline: 1.25rem; }
        @media (min-width: 640px) { .container-edunova { padding-inline: 2rem; } }

        .nav-link {
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            padding: 0.5rem 0.85rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 9999px;
        }
        .nav-link:hover {
            color: var(--primary-color);
            background-color: rgba(15, 23, 42, 0.04);
        }
        .nav-link.active {
            color: var(--primary-color);
            font-weight: 700;
            background-color: rgba(15, 23, 42, 0.06);
        }

        /* Hide scrollbar completely for scrollbar-none and testiSlider */
        .scrollbar-none::-webkit-scrollbar,
        .no-scrollbar::-webkit-scrollbar,
        [x-ref="testiSlider"]::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            background: transparent !important;
        }

        .scrollbar-none,
        .no-scrollbar,
        [x-ref="testiSlider"] {
            -ms-overflow-style: none !important;  /* IE & Edge */
            scrollbar-width: none !important;  /* Firefox */
        }
    </style>

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
<body x-data="{
        mobileOpen: false,
        scrolled: false,
        showTop: false,
        profileOpen: false,
        akademikOpen: false,
        touchStartX: 0,
        init(){
            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 20;
                this.showTop = window.scrollY > 400;
            });
        }
    }" x-init="init()">

    <!-- Header / Navigasi (Sleek Modern Solid Header - Bright Theme) -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white/95 backdrop-blur-md shadow-sm border-b border-slate-200/80" :class="scrolled ? 'shadow-md py-3' : 'py-4'">
        <div class="container-edunova">
            <div class="flex items-center justify-between">
                
                <!-- Logo Sekolah & Tagline -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary via-secondary to-primary flex items-center justify-center text-white shadow-glow border border-white/20 group-hover:scale-105 transition-transform duration-300 overflow-hidden">
                        <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="Logo" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <span class="text-base font-extrabold tracking-tight text-slate-800 block leading-none">{{ Setting::get('school_name', 'SMA NUSANTARA') }}</span>
                        <span class="text-[7.5px] sm:text-[9px] font-semibold text-indigo-600 block mt-1 tracking-wider uppercase">{{ Setting::get('school_tagline', 'Berkarakter • Berprestasi • Mendunia') }}</span>
                    </div>
                </a>

                <!-- Navigasi Utama (Desktop) -->
                <nav class="hidden lg:flex items-center gap-1 bg-slate-100/80 p-1 rounded-full border border-slate-200/80 backdrop-blur-md">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
                    
                    <!-- Dropdown Profil -->
                    <div class="relative" @click.away="profileOpen = false">
                        <button @click="profileOpen = !profileOpen" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">
                            Profil
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="profileOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="profileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-cloak class="absolute top-full left-0 mt-2 w-52 bg-white border border-slate-200/80 rounded-2xl shadow-xl py-2 z-50 text-xs font-semibold backdrop-blur-xl">
                            <a href="{{ route('about') }}" class="block px-4 py-2.5 text-slate-600 hover:text-primary hover:bg-primary/5 transition-colors">Tentang Kami</a>
                            <a href="{{ route('about') }}#visi-misi" class="block px-4 py-2.5 text-slate-600 hover:text-primary hover:bg-primary/5 transition-colors">Visi & Misi</a>
                            <a href="{{ route('about') }}#sambutan" class="block px-4 py-2.5 text-slate-600 hover:text-primary hover:bg-primary/5 transition-colors">Sambutan Kepala Sekolah</a>
                        </div>
                    </div>

                    <!-- Dropdown Akademik -->
                    <div class="relative" @click.away="akademikOpen = false">
                        <button @click="akademikOpen = !akademikOpen" class="nav-link">
                            Akademik
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="akademikOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="akademikOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-cloak class="absolute top-full left-0 mt-2 w-56 bg-white border border-slate-200/80 rounded-2xl shadow-xl py-2 z-50 text-xs font-semibold backdrop-blur-xl">
                            <a href="{{ route('curriculum') }}" class="block px-4 py-2.5 text-slate-600 hover:text-primary hover:bg-primary/5 transition-colors">Kurikulum & Program</a>
                            @if(Setting::get('is_vocational', '1') == '1')
                                <a href="{{ route('majors') }}" class="block px-4 py-2.5 text-slate-600 hover:text-primary hover:bg-primary/5 transition-colors">Jurusan Unggulan</a>
                            @endif
                            <a href="{{ route('extracurricular') }}" class="block px-4 py-2.5 text-slate-600 hover:text-primary hover:bg-primary/5 transition-colors">Ekstrakurikuler</a>
                        </div>
                    </div>

                    @if(Setting::get('spmb_enabled', '1') == '1')
                        <a href="{{ route('spmb.info') }}" class="nav-link {{ request()->routeIs('spmb.*') ? 'active' : '' }}">SPMB</a>
                    @endif
                    <a href="{{ route('events') }}" class="nav-link {{ request()->routeIs('events') ? 'active' : '' }}">Kegiatan</a>
                    <a href="{{ route('blog') }}" class="nav-link {{ request()->routeIs('blog') || request()->routeIs('blog.show') ? 'active' : '' }}">Berita</a>
                    <a href="{{ route('gallery') }}" class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}">Galeri</a>
                    <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Kontak</a>
                </nav>

                <!-- Tombol Aksi Kanan Header (Proporsional & Presisi untuk PC Screen) -->
                <div class="hidden lg:flex items-center space-x-3 shrink-0">
                    @auth
                        @if(auth()->user()->hasRole('student'))
                            <a href="{{ route('student.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs border border-slate-200/80 transition-all flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Portal Siswa</span>
                            </a>
                        @elseif(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs shadow-sm transition-all flex items-center gap-2 border border-slate-200/80">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Admin Panel</span>
                            </a>
                        @elseif(auth()->user()->hasRole('calon-siswa'))
                            <a href="{{ route('spmb.dashboard.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs shadow-sm transition-all flex items-center gap-2 border border-slate-200/80">
                                <span>Dashboard SPMB</span>
                            </a>
                        @elseif(auth()->user()->hasRole('kantin'))
                            <a href="{{ route('canteen.vendor.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs border border-slate-200/80 transition-all flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>Dashboard Kantin</span>
                            </a>
                        @elseif(auth()->user()->hasRole('guru') || auth()->user()->hasRole('teacher'))
                            <a href="{{ route('teacher.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs border border-slate-200/80 transition-all flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                                <span>Portal Guru</span>
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs border border-slate-200/80 transition-all flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Dashboard</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="group px-4 py-2.5 rounded-xl bg-primary/10 hover:bg-primary/20 text-primary border border-primary/25 hover:border-primary/40 transition-all duration-300 flex items-center gap-2 shadow-xs transform hover:-translate-y-0.5 shrink-0 font-extrabold text-xs">
                            <svg class="w-4 h-4 text-primary group-hover:scale-110 shrink-0 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span>Login</span>
                        </a>
                    @endauth

                    @if(Setting::get('spmb_enabled', '1') == '1')
                        <a href="{{ route('spmb.register') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary via-secondary to-primary hover:brightness-110 text-white font-extrabold text-xs shadow-lg shadow-primary/30 transition-all flex items-center gap-2 border border-white/20 transform hover:-translate-y-0.5 shrink-0">
                            <span>Daftar SPMB</span>
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endif
                </div>

                <!-- Mobile Actions & Toggle Menu -->
                <div class="flex lg:hidden items-center gap-2">
                    @auth
                        @if(auth()->user()->hasRole('student'))
                            <a href="{{ route('student.dashboard') }}" class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 border border-slate-200/80 hover:bg-slate-200 transition-all flex items-center justify-center shadow-sm active:scale-95" title="Portal Siswa">
                                <svg class="w-4.5 h-4.5 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            </a>
                        @elseif(auth()->user()->hasRole('kantin'))
                            <a href="{{ route('canteen.vendor.dashboard') }}" class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 border border-slate-200/80 hover:bg-slate-200 transition-all flex items-center justify-center shadow-sm active:scale-95" title="Dashboard Kantin">
                                <svg class="w-4.5 h-4.5 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </a>
                        @elseif(auth()->user()->hasRole('guru') || auth()->user()->hasRole('teacher'))
                            <a href="{{ route('teacher.dashboard') }}" class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 border border-slate-200/80 hover:bg-slate-200 transition-all flex items-center justify-center shadow-sm active:scale-95" title="Portal Guru">
                                <svg class="w-4.5 h-4.5 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 border border-slate-200/80 hover:bg-slate-200 transition-all flex items-center justify-center shadow-sm active:scale-95" title="Dashboard Admin">
                                <svg class="w-4.5 h-4.5 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="h-9 px-3.5 rounded-xl bg-primary text-white hover:brightness-110 transition-all duration-300 flex items-center gap-1 shadow-md shadow-primary/20 text-[11px] font-bold active:scale-95" title="Login">
                            <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg><span>Login</span>
                        </a>
                    @endauth
                    
                    <button @click="mobileOpen = !mobileOpen" 
                        class="group flex h-9 w-9 rounded-xl focus:outline-none items-center justify-center active:scale-95 shrink-0 transition-all duration-300"
                        :class="mobileOpen ? 'bg-primary border border-transparent shadow-md shadow-primary/20' : 'bg-gradient-to-r from-primary/10 via-primary/5 to-secondary/10 border border-primary/25 hover:border-transparent hover:from-primary hover:to-secondary shadow-xs'"
                        title="Buka Menu">
                        <div class="w-5 h-4 flex items-center justify-center relative">
                            <span class="absolute w-5 h-0.5 rounded-full transition-all duration-300" :class="mobileOpen ? 'rotate-45 bg-white' : '-translate-y-1.5 bg-primary group-hover:bg-white'"></span>
                            <span class="absolute w-5 h-0.5 rounded-full transition-all duration-300" :class="mobileOpen ? 'opacity-0 bg-white' : 'bg-primary group-hover:bg-white'"></span>
                            <span class="absolute w-5 h-0.5 rounded-full transition-all duration-300" :class="mobileOpen ? '-rotate-45 bg-white' : 'translate-y-1.5 bg-primary group-hover:bg-white'"></span>
                        </div>
                    </button>
                </div>

            </div>
        </div>
    </header>

    <!-- ==========================================
         MOBILE LEFT SLIDE-IN OFF-CANVAS DRAWER WITH TOUCH SWIPE
         ========================================== -->
    <div class="lg:hidden" x-cloak>
        <!-- Dark Backdrop Overlay -->
        <div x-show="mobileOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileOpen = false"
             class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50"></div>

        <!-- Left Off-Canvas Sidebar Container -->
        <div x-show="mobileOpen" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-250 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             @touchstart="touchStartX = $event.touches[0].clientX"
             @touchmove="if (touchStartX - $event.touches[0].clientX > 60) { mobileOpen = false; }"
             class="fixed top-0 left-0 bottom-0 w-[310px] max-w-[88vw] bg-white z-50 flex flex-col shadow-2xl border-r border-slate-200 overflow-y-auto scrollbar-none">
            
            <!-- Drawer Header (Logo & Close Button) -->
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-white/95 sticky top-0 z-10 backdrop-blur-md">
                <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary via-secondary to-primary flex items-center justify-center text-white shadow-glow border border-white/20 overflow-hidden shrink-0">
                        <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="Logo" class="w-full h-full object-cover">
                    </div>
                    <div class="overflow-hidden min-w-0">
                        <span class="text-sm font-extrabold tracking-tight text-slate-800 block truncate leading-tight">{{ Setting::get('school_name', 'SMA NUSANTARA') }}</span>
                        <span class="text-[9px] font-bold text-indigo-600 block tracking-wider uppercase truncate mt-0.5">{{ Setting::get('school_tagline', 'Berkarakter • Berprestasi • Mendunia') }}</span>
                    </div>
                </a>
                <button @click="mobileOpen = false" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:text-slate-900 hover:bg-slate-200 border border-slate-200 transition-all flex items-center justify-center focus:outline-none shrink-0 active:scale-95 ml-2" title="Tutup Menu">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation Links by Group -->
            <div class="p-4 space-y-5 text-sm flex-1">
                
                <!-- Group 1: Utama -->
                <div>
                    <div class="px-2 mb-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">NAVIGASI UTAMA</span>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('home') }}" 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group border {{ request()->routeIs('home') ? 'bg-primary/10 text-primary border-primary/20 shadow-xs font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent font-medium' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ request()->routeIs('home') ? 'bg-primary text-white border-primary/20 shadow-xs' : 'bg-slate-100 text-slate-500 border-slate-200 group-hover:bg-primary/10 group-hover:text-primary' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                </div>
                                <span>Beranda Utama</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <a href="{{ route('about') }}" 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group border {{ request()->routeIs('about') ? 'bg-primary/10 text-primary border-primary/20 shadow-xs font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent font-medium' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ request()->routeIs('about') ? 'bg-primary text-white border-primary/20 shadow-xs' : 'bg-slate-100 text-slate-500 border-slate-200 group-hover:bg-primary/10 group-hover:text-primary' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <span>Profil Sekolah</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Group 2: Akademik & Program -->
                <div>
                    <div class="px-2 mb-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-505 text-slate-500">AKADEMIK & PROGRAM</span>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('curriculum') }}" 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group border {{ request()->routeIs('curriculum') ? 'bg-primary/10 text-primary border-primary/20 shadow-xs font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent font-medium' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ request()->routeIs('curriculum') ? 'bg-primary text-white border-primary/20 shadow-xs' : 'bg-slate-100 text-slate-500 border-slate-200 group-hover:bg-primary/10 group-hover:text-primary' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <span>Kurikulum & Program</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        @if(Setting::get('is_vocational', '1') == '1')
                            <a href="{{ route('majors') }}" 
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group border {{ request()->routeIs('majors') ? 'bg-primary/10 text-primary border-primary/20 shadow-xs font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent font-medium' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ request()->routeIs('majors') ? 'bg-primary text-white border-primary/20 shadow-xs' : 'bg-slate-100 text-slate-500 border-slate-200 group-hover:bg-primary/10 group-hover:text-primary' }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <span>Jurusan Unggulan</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endif

                        <a href="{{ route('extracurricular') }}" 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group border {{ request()->routeIs('extracurricular') ? 'bg-primary/10 text-primary border-primary/20 shadow-xs font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent font-medium' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ request()->routeIs('extracurricular') ? 'bg-primary text-white border-primary/20 shadow-xs' : 'bg-slate-100 text-slate-500 border-slate-200 group-hover:bg-primary/10 group-hover:text-primary' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span>Ekstrakurikuler</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Group 3: Informasi & SPMB -->
                <div>
                    <div class="px-2 mb-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">PENDAFTARAN & AGENDA</span>
                    </div>
                    <div class="space-y-1">
                        @if(Setting::get('spmb_enabled', '1') == '1')
                            <a href="{{ route('spmb.info') }}" 
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group border {{ request()->routeIs('spmb.info') ? 'bg-primary/10 text-primary border-primary/20 shadow-xs font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent font-medium' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ request()->routeIs('spmb.info') ? 'bg-amber-500 text-white border-amber-400/30 shadow-xs' : 'bg-amber-50 text-amber-600 border-amber-200 group-hover:bg-amber-500/10 group-hover:text-amber-600' }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h18"/></svg>
                                    </div>
                                    <span>Informasi SPMB</span>
                                </div>
                                <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200 uppercase">BUKA</span>
                            </a>
                        @endif

                        <a href="{{ route('events') }}" 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group border {{ request()->routeIs('events') ? 'bg-primary/10 text-primary border-primary/20 shadow-xs font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent font-medium' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ request()->routeIs('events') ? 'bg-primary text-white border-primary/20 shadow-xs' : 'bg-slate-100 text-slate-500 border-slate-200 group-hover:bg-primary/10 group-hover:text-primary' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span>Kegiatan & Agenda</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Group 4: Publikasi & Kontak -->
                <div>
                    <div class="px-2 mb-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">PUBLIKASI & KONTAK</span>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('blog') }}" 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group border {{ request()->routeIs('blog') ? 'bg-primary/10 text-primary border-primary/20 shadow-xs font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent font-medium' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ request()->routeIs('blog') ? 'bg-primary text-white border-primary/20 shadow-xs' : 'bg-slate-100 text-slate-500 border-slate-200 group-hover:bg-primary/10 group-hover:text-primary' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                </div>
                                <span>Berita & Kabar</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <a href="{{ route('gallery') }}" 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group border {{ request()->routeIs('gallery') ? 'bg-primary/10 text-primary border-primary/20 shadow-xs font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent font-medium' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ request()->routeIs('gallery') ? 'bg-primary text-white border-primary/20 shadow-xs' : 'bg-slate-100 text-slate-500 border-slate-200 group-hover:bg-primary/10 group-hover:text-primary' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span>Galeri Momen</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <a href="{{ route('contact') }}" 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group border {{ request()->routeIs('contact') ? 'bg-primary/10 text-primary border-primary/20 shadow-xs font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 border-transparent font-medium' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border transition-all {{ request()->routeIs('contact') ? 'bg-primary text-white border-primary/20 shadow-xs' : 'bg-slate-100 text-slate-500 border-slate-200 group-hover:bg-primary/10 group-hover:text-primary' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <span>Kontak Kami</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-700 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Drawer Footer Action Buttons -->
            <div class="p-4 border-t border-slate-100 bg-white sticky bottom-0 flex flex-col gap-2.5 backdrop-blur-md">
                @auth
                    @if(auth()->user()->hasRole('student'))
                        <a href="{{ route('student.dashboard') }}" class="w-full text-center py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 border border-slate-950 shadow-sm transition-all">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Masuk Portal Siswa</span>
                        </a>
                    @elseif(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
                        <a href="{{ route('admin.dashboard') }}" class="w-full text-center py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 border border-slate-950 shadow-sm transition-all">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Buka Admin Panel</span>
                        </a>
                    @elseif(auth()->user()->hasRole('calon-siswa'))
                        <a href="{{ route('spmb.dashboard.index') }}" class="w-full text-center py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 border border-slate-950 shadow-sm transition-all">
                            <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Dashboard SPMB</span>
                        </a>
                    @elseif(auth()->user()->hasRole('kantin'))
                        <a href="{{ route('canteen.vendor.dashboard') }}" class="w-full text-center py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 border border-slate-950 shadow-sm transition-all">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>Dashboard Kantin</span>
                        </a>
                    @elseif(auth()->user()->hasRole('guru') || auth()->user()->hasRole('teacher'))
                        <a href="{{ route('teacher.dashboard') }}" class="w-full text-center py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 border border-slate-950 shadow-sm transition-all">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                            <span>Portal Guru</span>
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="w-full text-center py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-2 border border-slate-950 shadow-sm transition-all">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Dashboard</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="group w-full text-center py-3 rounded-xl bg-primary/10 hover:bg-primary/20 text-primary border border-primary/25 hover:border-primary/40 transition-all duration-300 flex items-center justify-center gap-2 shadow-xs font-extrabold text-xs">
                        <svg class="w-4 h-4 text-primary group-hover:scale-110 shrink-0 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Login Siswa & Staf</span>
                    </a>
                @endauth

                @if(Setting::get('spmb_enabled', '1') == '1')
                    <a href="{{ route('spmb.register') }}" class="w-full text-center py-3 rounded-xl bg-gradient-to-r from-primary via-secondary to-primary hover:opacity-95 text-white font-extrabold text-xs shadow-lg shadow-primary/30 flex items-center justify-center gap-2 border border-white/20 transition-all active:scale-[0.98]">
                        <span>Daftar SPMB Online</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
    </header>

    <!-- Slot Konten Utama -->
    <main>
        @yield('content')
    </main>

    <!-- Footer (Modern Clean SaaS Dark Theme) -->
    <footer id="kontak" class="bg-navy-950 text-slate-400 pt-16 pb-12 border-t border-slate-800/80 relative">
        <div class="container-edunova">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-slate-800/80">
                
                <!-- Info Brand (Col 1) -->
                <div class="lg:col-span-1 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white shadow-lg border border-white/20 overflow-hidden">
                            @if(Setting::get('logo_path'))
                                <img src="{{ asset(Setting::get('logo_path')) }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <span class="text-base font-extrabold text-white block leading-none">{{ Setting::get('school_name', 'SMA NUSANTARA') }}</span>
                            <span class="text-[9px] font-semibold text-slate-400 block mt-1 tracking-wider uppercase">Berkarakter • Berprestasi • Mendunia</span>
                        </div>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        {{ Setting::get('school_description', 'Mewujudkan generasi cerdas, unggul, berkarakter, dan berdaya saing global.') }}
                    </p>
                    
                    <!-- Social Media Links -->
                    <div class="flex items-center gap-2 pt-1">
                        @if(Setting::get('facebook_url'))
                        <a href="{{ Setting::get('facebook_url') }}" target="_blank" class="w-8 h-8 rounded-full bg-slate-900 border border-slate-800 hover:bg-blue-600 hover:border-blue-500 flex items-center justify-center text-slate-400 hover:text-white transition-all" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        @endif
                        @if(Setting::get('instagram_url'))
                        <a href="{{ Setting::get('instagram_url') }}" target="_blank" class="w-8 h-8 rounded-full bg-slate-900 border border-slate-800 hover:bg-blue-600 hover:border-blue-500 flex items-center justify-center text-slate-400 hover:text-white transition-all" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        @endif
                        @if(Setting::get('youtube_url'))
                        <a href="{{ Setting::get('youtube_url') }}" target="_blank" class="w-8 h-8 rounded-full bg-slate-900 border border-slate-800 hover:bg-blue-600 hover:border-blue-500 flex items-center justify-center text-slate-400 hover:text-white transition-all" aria-label="YouTube">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Menu Utama -->
                <div>
                    <h5 class="text-white font-bold text-xs uppercase tracking-wider mb-4">Menu Utama</h5>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">Profil Sekolah</a></li>
                        <li><a href="{{ route('curriculum') }}" class="hover:text-white transition-colors">Program Akademik</a></li>
                        @if(Setting::get('spmb_enabled', '1') == '1')
                            <li><a href="{{ route('spmb.info') }}" class="hover:text-white transition-colors">Info SPMB</a></li>
                        @endif
                        <li><a href="{{ route('blog') }}" class="hover:text-white transition-colors">Berita & Artikel</a></li>
                        <li><a href="{{ route('gallery') }}" class="hover:text-white transition-colors">Galeri Momen</a></li>
                    </ul>
                </div>

                <!-- Informasi & Portal -->
                <div>
                    <h5 class="text-white font-bold text-xs uppercase tracking-wider mb-4">Informasi & Portal</h5>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('extracurricular') }}" class="hover:text-white transition-colors">Ekstrakurikuler</a></li>
                        @if(Setting::get('is_vocational', '1') == '1')
                            <li><a href="{{ route('majors') }}" class="hover:text-white transition-colors">Jurusan Unggulan</a></li>
                        @endif
                        <li><a href="{{ route('privacy.policy') }}" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="{{ route('terms.conditions') }}" class="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Kontak Kami</a></li>
                    </ul>
                </div>

                <!-- Kontak Kami -->
                <div>
                    <h5 class="text-white font-bold text-xs uppercase tracking-wider mb-4">Kontak Kami</h5>
                    <ul class="space-y-2.5 text-xs">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-blue-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ Setting::get('school_address', 'Jl. Pendidikan No. 10, Jakarta Selatan') }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>{{ Setting::get('school_phone', '(021) 1234 5678') }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>{{ Setting::get('school_email', 'info@smanusantara.sch.id') }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h5 class="text-white font-bold text-xs uppercase tracking-wider mb-2">Newsletter</h5>
                    <p class="text-xs text-slate-400 mb-3 leading-relaxed">Dapatkan informasi terbaru & event sekolah langsung di inbox Anda.</p>
                    <form onsubmit="event.preventDefault();" class="flex gap-2">
                        <input type="email" placeholder="Masukkan email Anda" class="w-full bg-slate-900/90 border border-slate-800 text-white text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-blue-500 transition-colors">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-3.5 py-2.5 rounded-xl flex items-center justify-center shrink-0 shadow-md transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>

            </div>

            <!-- Copyright -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-3 text-center sm:text-left">
                <p>&copy; {{ date('Y') }} {{ Setting::get('school_name', 'SMA Nusantara') }}. All rights reserved.</p>
                <div class="flex items-center gap-5 flex-wrap justify-center sm:justify-end">
                    <a href="{{ route('privacy.policy') }}" class="hover:text-slate-300 transition-colors">Kebijakan Privasi</a>
                    <a href="{{ route('terms.conditions') }}" class="hover:text-slate-300 transition-colors">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Tombol Kembali ke Atas -->
    <button x-show="showTop" x-transition @click="window.scrollTo({ top: 0, behavior: 'smooth' })" 
            class="fixed bottom-6 right-6 z-50 flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white shadow-xl transition-all hover:bg-blue-500 border border-white/20" 
            aria-label="Kembali ke atas" x-cloak>
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
    </button>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: true,
                offset: 60,
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
