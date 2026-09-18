<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Tanda Siswa (KTS) - {{ $selectedClass ? 'Kelas ' . $selectedClass->name : 'Massal' }}</title>

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
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-after: always;
                break-after: page;
            }
        }
    </style>
</head>
<body class="p-6 sm:p-10 min-h-screen">

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="no-print max-w-5xl mx-auto mb-8 bg-white text-slate-900 p-4 sm:p-6 rounded-3xl shadow-xl flex items-center justify-between gap-4 border border-slate-200">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-emerald-500/10 text-emerald-600 rounded-2xl flex items-center justify-center font-bold border border-emerald-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            </div>
            <div>
                <h2 class="font-extrabold text-sm text-slate-900">Preview Kartu Tanda Siswa (KTS) - Depan & Belakang</h2>
                <p class="text-xs text-slate-500 font-medium">Siap dicetak: Total <strong class="text-emerald-600 font-bold">{{ $students->count() }} Kartu Siswa</strong> {{ $selectedClass ? '(' . $selectedClass->name . ')' : '' }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition-all flex items-center space-x-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Sekarang (Ctrl+P)</span>
            </button>
        </div>
    </div>

    <!-- Main Printable Cards Container -->
    <div class="max-w-5xl mx-auto space-y-8">
        @foreach($students as $student)
            <div class="page-break-inside-avoid">
                <x-student-card :student="$student" :showLabel="true" />
            </div>
        @endforeach
    </div>

</body>
</html>
