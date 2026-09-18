<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ Setting::get('school_name', 'School') }}</title>

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
                        primary: '{{ Setting::get('primary_color', '#3B82F6') }}',
                        secondary: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                        brand: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            600: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            700: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                            800: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 95%, black)',
                        },
                        indigo: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            600: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            700: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                            800: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 95%, black)',
                        },
                        amber: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 20%, white)',
                            500: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            600: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            700: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                        },
                        orange: {
                            50: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 20%, white)',
                            500: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                            700: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 90%, black)',
                        },
                        blue: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            600: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            700: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                            800: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 95%, black)',
                        },
                        purple: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 20%, white)',
                            200: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 35%, white)',
                            300: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 50%, white)',
                            400: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 75%, white)',
                            500: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            600: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            700: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                            800: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                            900: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 80%, black)',
                            950: 'color-mix(in srgb, {{ Setting::get('secondary_color', '#1E40AF') }} 95%, black)',
                        },
                        violet: {
                            50: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 10%, white)',
                            100: 'color-mix(in srgb, {{ Setting::get('primary_color', '#3B82F6') }} 20%, white)',
                            500: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            600: '{{ Setting::get('primary_color', '#3B82F6') }}',
                            700: '{{ Setting::get('secondary_color', '#1E40AF') }}',
                        }
                    },
                    screens: {
                        'xs': '400px',
                        'sm': '640px',
                        'md': '768px',
                        'lg': '1024px',
                        'xl': '1280px',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.7s ease-out',
                        'scale-up': 'scaleUp 0.5s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(40px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        scaleUp: {
                            '0%': { transform: 'scale(0.9)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- AlpineJS CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts - Modern & Readable -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Modern Readable Font Stack */
        :root {
            --font-heading: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --font-body: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        body { 
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
        }

        /* Glass Morphism */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Mesh Gradient Background */
        .mesh-gradient {
            background-color: #f3f4f6;
            background-image:
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(147, 51, 234, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(34, 197, 94, 0.15) 0px, transparent 50%);
        }

        /* Better Input Styles for Mobile */
        input, button {
            font-size: 16px; /* Prevents zoom on iOS */
        }

        /* Touch-friendly buttons */
        @media (max-width: 640px) {
            button, a {
                min-height: 44px;
            }
        }
    </style>
</head>
<body class="mesh-gradient min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute top-10 right-4 sm:top-20 sm:right-20 w-48 h-48 sm:w-72 sm:h-72 bg-primary/20 rounded-full blur-3xl animate-float"></div>
    <div class="absolute bottom-10 left-4 sm:bottom-20 sm:left-20 w-64 h-64 sm:w-96 sm:h-96 bg-secondary/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] sm:w-[600px] h-[400px] sm:h-[600px] bg-purple-500/10 rounded-full blur-3xl animate-pulse"></div>

    <div class="w-full max-w-[22rem] xs:max-w-[26rem] sm:max-w-md relative z-10 animate-scale-up mx-auto">
        <!-- Logo Card -->
        <div class="glass rounded-xl xs:rounded-2xl sm:rounded-3xl shadow-2xl p-4 xs:p-5 sm:p-6 md:p-8 lg:p-10 border border-white/20">
            <!-- Header with Logo -->
            <div class="text-center mb-5 xs:mb-6 sm:mb-8">
                <div class="relative inline-block mb-3 xs:mb-4">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary to-secondary rounded-lg xs:rounded-xl sm:rounded-2xl blur-lg opacity-50"></div>
                    @php
                        $authLogo = Setting::get('logo_path') ?? Setting::get('school_logo', 'img/logo.png');
                        $authLogoUrl = \Illuminate\Support\Str::startsWith($authLogo, ['http://', 'https://', 'img/']) ? asset($authLogo) : asset('img/' . $authLogo);
                    @endphp
                    <img src="{{ $authLogoUrl }}" alt="{{ Setting::get('school_name', 'School') }}" class="h-12 w-12 xs:h-14 xs:w-14 sm:h-16 sm:w-16 md:h-20 md:w-20 relative z-10 rounded-lg xs:rounded-xl sm:rounded-2xl shadow-lg mx-auto object-contain bg-white/10 p-1">
                </div>
                <h1 class="text-lg xs:text-xl sm:text-2xl md:text-2xl font-bold text-gray-900 leading-tight px-2">{{ Setting::get('school_name', 'School') }}</h1>
                <p class="text-gray-500 mt-1 xs:mt-1.5 sm:mt-2 text-xs font-medium">Portal Login</p>
            </div>

            @if ($errors->any())
                <div x-data="{ show: true }"
                     x-show="show"
                     x-init="setTimeout(() => show = true, 50)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mb-5 bg-gradient-to-r from-red-500 to-rose-500 text-white px-4 py-3.5 rounded-xl shadow-lg shadow-red-500/30">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm font-medium leading-relaxed">{{ $error }}</p>
                            @endforeach
                        </div>
                        <button @click="show = false" class="flex-shrink-0 w-6 h-6 rounded-full hover:bg-white/20 flex items-center justify-center transition -mr-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div x-data="{ show: true }" 
                     x-show="show"
                     x-init="setTimeout(() => show = true, 50)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mb-4 sm:mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-3 sm:px-4 py-3 sm:py-4 rounded-xl flex items-center justify-between shadow-lg">
                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm font-medium truncate">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="ml-2 sm:ml-4 text-green-600 hover:text-green-800 font-bold w-7 h-7 sm:w-8 sm:h-8 rounded-full hover:bg-green-100 flex items-center justify-center flex-shrink-0 transition text-lg">&times;</button>
                </div>
            @endif

            @if(session('info'))
                <div id="info-alert" class="mb-4 sm:mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 text-blue-800 px-3 sm:px-4 py-3 sm:py-4 rounded-xl flex items-center justify-between shadow-lg">
                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm font-medium truncate">{{ session('info') }}</span>
                    </div>
                    <button onclick="document.getElementById('info-alert').style.display='none'" class="ml-2 sm:ml-4 text-blue-600 hover:text-blue-800 font-bold w-7 h-7 sm:w-8 sm:h-8 rounded-full hover:bg-blue-100 flex items-center justify-center flex-shrink-0 transition text-lg">&times;</button>
                </div>
                <script>
                    setTimeout(function() {
                        var alert = document.getElementById('info-alert');
                        if(alert) {
                            alert.style.transition = 'all 0.3s ease';
                            alert.style.opacity = '0';
                            alert.style.transform = 'translateY(-8px)';
                            setTimeout(function() {
                                alert.style.display = 'none';
                            }, 300);
                        }
                    }, 5000);
                </script>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" 
                     x-show="show"
                     x-init="setTimeout(() => show = true, 50)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mb-4 sm:mb-6 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-3 sm:px-4 py-3 sm:py-4 rounded-xl flex items-center justify-between shadow-lg">
                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-red-500 to-rose-500 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm font-medium truncate">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="ml-2 sm:ml-4 text-red-600 hover:text-red-800 font-bold w-7 h-7 sm:w-8 sm:h-8 rounded-full hover:bg-red-100 flex items-center justify-center flex-shrink-0 transition text-lg">&times;</button>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" 
                  x-data="{ loading: false }" 
                  @submit="loading = true"
                  class="space-y-4 sm:space-y-5 md:space-y-6">
                @csrf

                <div class="space-y-4 sm:space-y-5 md:space-y-6">
                    <!-- Email -->
                    <div>
                        <label for="email" class="flex items-center space-x-2 text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>Email</span>
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               placeholder="nama@email.com"
                               inputmode="email"
                               class="w-full px-3 sm:px-4 py-3 sm:py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('email') border-red-500 @enderror hover:border-gray-400 text-sm sm:text-base">
                        @error('email')
                            <p class="mt-2 text-xs sm:text-sm text-red-600 flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="flex items-center space-x-2 text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Password</span>
                        </label>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               placeholder="••••••••"
                               class="w-full px-3 sm:px-4 py-3 sm:py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('password') border-red-500 @enderror hover:border-gray-400 text-sm sm:text-base">
                        @error('password')
                            <p class="mt-2 text-xs sm:text-sm text-red-600 flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox"
                               id="remember"
                               name="remember"
                               class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary cursor-pointer">
                        <label for="remember" class="ml-2 text-xs sm:text-sm text-gray-600 cursor-pointer select-none">Ingat saya</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            :disabled="loading"
                            class="group w-full bg-gradient-to-r from-primary to-secondary text-white font-semibold py-3 sm:py-3.5 md:py-4 rounded-xl transition-all duration-300 hover:shadow-2xl hover:shadow-primary/40 hover:scale-[1.02] disabled:scale-100 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed disabled:shadow-none flex items-center justify-center space-x-2">
                        <svg x-show="!loading" class="w-4 h-4 sm:w-5 sm:h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <svg x-show="loading" class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Memproses...' : 'Login'"></span>
                    </button>
                </div>
            </form>

            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                <a href="{{ route('home') }}" class="group flex items-center justify-center space-x-2 text-xs sm:text-sm text-gray-600 hover:text-primary transition-colors py-2">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-xs sm:text-sm mt-4 sm:mt-6 font-medium px-4">
            &copy; {{ date('Y') }} {{ Setting::get('school_name', 'School') }}
        </p>
    </div>
</body>
</html>
