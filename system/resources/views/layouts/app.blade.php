<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'School')) - {{ Setting::get('school_name', 'School') }}</title>

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
    <link rel="icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <!-- PWA Install Support -->
    @include('components.pwa-install')

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '{{ Setting::get('primary_color', '#6366f1') }}',
                        secondary: '{{ Setting::get('secondary_color', '#4f46e5') }}',
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
                        brand: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 20%, white)',
                            500: '{{ Setting::get('primary_color', '#6366f1') }}',
                            600: '{{ Setting::get('primary_color', '#6366f1') }}',
                            700: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            800: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                        },
                        amber: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#6366f1') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#6366f1') }}',
                            600: '{{ Setting::get('primary_color', '#6366f1') }}',
                            700: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                        },
                        orange: {
                            50: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#4f46e5') }} 75%, white)',
                            500: '{{ Setting::get('secondary_color', '#4f46e5') }}',
                            600: '{{ Setting::get('secondary_color', '#4f46e5') }}',
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
    
    <!-- AlpineJS CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>

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

    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-slate-950 text-gray-900 dark:text-slate-100 transition-colors duration-200">
    @yield('content')

    @stack('scripts')
</body>
</html>
