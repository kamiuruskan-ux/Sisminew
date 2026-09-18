<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Tanda Siswa (KTS) - {{ $student->user->name ?? 'Siswa' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="apple-touch-icon" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            padding: 30px 15px;
            min-height: 100vh;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 30px auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 16px 24px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .no-print-bar, .no-print {
                display: none !important;
            }

            .page-break {
                page-break-after: always;
                break-after: page;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Non Printable) -->
    <div class="no-print-bar">
        <div>
            <h1 class="text-slate-900 font-bold text-base">Kartu Tanda Siswa (KTS) Digital - Depan & Belakang</h1>
            <p class="text-slate-500 text-xs mt-0.5">{{ Setting::get('school_name', 'Sekolah Indonesia') }} &bull; {{ Setting::get('student_card_orientation', 'landscape') === 'portrait' ? 'Standar Ukuran CR-80 Portrait (54mm x 85.6mm)' : 'Standar Ukuran CR-80 Landscape (85.6mm x 54mm)' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.student-cards.settings') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-xl border border-slate-200 transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Pengaturan KTS</span>
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center space-x-2 cursor-pointer">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Kartu Tanda Siswa</span>
            </button>
        </div>
    </div>

    <!-- Render Reusable Front & Back Card Component / Helper -->
    <div class="max-w-5xl mx-auto flex flex-col items-center justify-center">
        <x-student-card :student="$student" :qrCode="$qrCode ?? null" :showLabel="true" />
    </div>

</body>
</html>
