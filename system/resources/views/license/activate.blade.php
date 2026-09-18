<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aktivasi Lisensi - {{ \App\Models\Setting::get('school_name', 'Sistem Informasi Sekolah') }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3C50E0',
                        secondary: '#2563eb',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #F8FAFC;
            color: #1E293B;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 sm:p-6">

    <div class="w-full max-w-lg">
        <!-- Brand Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white border border-slate-200 shadow-sm p-2 mb-3">
                @php
                    $logoFile = \App\Models\Setting::get('logo_path') ?? \App\Models\Setting::get('school_logo', 'img/logo.png');
                    $logoUrl = \Illuminate\Support\Str::startsWith($logoFile, ['http://', 'https://', 'img/']) ? asset($logoFile) : asset('img/' . $logoFile);
                @endphp
                <img src="{{ $logoUrl }}" alt="Logo Sekolah" class="w-full h-full object-contain">
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                {{ \App\Models\Setting::get('school_name', 'Sistem Informasi Sekolah') }}
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Verifikasi & Aktivasi Lisensi Penggunaan Aplikasi</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
            
            <!-- Server Domain Badge -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <div>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Domain Server Aktif</p>
                        <p class="text-xs font-mono font-bold text-slate-800 mt-0.5">{{ $currentHost }}</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">
                    PROTECTED
                </span>
            </div>

            <!-- Error Alert -->
            @if(session('error') || $errorMessage)
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start space-x-3 text-rose-800 text-xs">
                    <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold">Status Lisensi Belum Aktif</p>
                        <p class="mt-0.5 text-rose-700 leading-relaxed">{{ session('error') ?: $errorMessage }}</p>
                    </div>
                </div>
            @endif

            <!-- License Form -->
            <form method="POST" action="{{ route('license.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="license_key" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kunci Lisensi Resmi (License Key)
                    </label>
                    <textarea 
                        id="license_key" 
                        name="license_key" 
                        rows="4" 
                        required 
                        placeholder="Contoh format: BHJ12F8A-FSDF123F-FDJK12D2-DK58DS6E-45D45D6R"
                        class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 text-xs font-mono focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all outline-none"
                    >{{ old('license_key', $activeLicense) }}</textarea>
                    <p class="text-[11px] text-slate-500 mt-1">
                        * Kunci lisensi terikat secara khusus untuk nama domain <strong class="text-slate-700 font-mono">{{ $currentHost }}</strong>.
                    </p>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-all duration-150 flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"/>
                    </svg>
                    <span>Simpan & Aktifkan Lisensi</span>
                </button>
            </form>

            <div class="border-t border-slate-100 pt-4 text-center">
                <p class="text-xs text-slate-500">
                    Bila membutuhkan bantuan atau pendaftaran lisensi domain baru, silakan hubungi tim administrator/pengembang.
                </p>
            </div>
        </div>

        <div class="text-center mt-6">
            <p class="text-[11px] text-slate-400">&copy; {{ date('Y') }} {{ \App\Models\Setting::get('school_name', 'Sekolah') }}. Hak cipta dilindungi undang-undang.</p>
        </div>
    </div>

</body>
</html>
