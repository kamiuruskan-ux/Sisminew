<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembayaran Siswa - {{ $student->name }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ \App\Models\Setting::get('favicon_path') ? asset(\App\Models\Setting::get('favicon_path')) : ((\App\Models\Setting::get('school_logo') && \App\Models\Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(\App\Models\Setting::get('school_logo'), 'img/') ? asset(\App\Models\Setting::get('school_logo')) : asset('img/' . \App\Models\Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ \App\Models\Setting::get('favicon_path') ? asset(\App\Models\Setting::get('favicon_path')) : ((\App\Models\Setting::get('school_logo') && \App\Models\Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(\App\Models\Setting::get('school_logo'), 'img/') ? asset(\App\Models\Setting::get('school_logo')) : asset('img/' . \App\Models\Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .print-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 p-6 md:p-8 min-h-screen flex flex-col items-center" onload="window.print()">

    <!-- Action Bar (Non-Printable) -->
    <div class="no-print w-full max-w-5xl mb-6 bg-slate-900 text-white p-4 rounded-xl shadow-md flex items-center justify-between">
        <div>
            <h1 class="font-extrabold text-xs">Preview Riwayat Pembayaran Siswa</h1>
            <p class="text-[10px] text-slate-400">Ukuran Lebar & Minimalis (A4 Horizontal / Vertikal)</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold rounded-lg transition flex items-center space-x-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Laporan</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold rounded-lg transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>

    <!-- Printable Report Container -->
    <div class="print-card w-full max-w-5xl bg-white p-4 md:p-6 text-slate-900">
        <!-- Kop Surat / Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-900">
            <div class="flex items-center space-x-4">
                <img src="{{ Setting::getLogoUrl() }}" alt="Logo" class="w-12 h-12 object-contain">
                <div>
                    <h2 class="text-base font-black uppercase tracking-tight text-slate-950">{{ $schoolName }}</h2>
                    <p class="text-[10px] font-medium text-slate-600">{{ $schoolAddress }}</p>
                    <p class="text-[9px] text-slate-500">Telp: {{ $schoolPhone }} | Email: {{ $schoolEmail }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">REKAPITULASI PEMBAYARAN</span>
                <span class="text-xs font-black text-slate-950 block mt-0.5">LAPORAN TRANSAKSI SISWA</span>
                <span class="text-[9px] text-slate-400 block">Dicetak: {{ date('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="text-center my-6">
            <h1 class="text-sm font-black tracking-wider uppercase text-slate-950">LAPORAN RIWAYAT TRANSAKSI PEMBAYARAN</h1>
            <p class="text-xs text-slate-600 mt-1">Periode: <strong>{{ $startDate->translatedFormat('d F Y') }}</strong> s.d. <strong>{{ $endDate->translatedFormat('d F Y') }}</strong></p>
        </div>

        <!-- Student Information Grid -->
        <div class="grid grid-cols-2 gap-4 py-3 border-y border-slate-200 text-[11px] my-5 bg-transparent rounded-none px-0">
            <div class="space-y-1">
                <div class="flex"><span class="text-slate-500 w-24">Nama Siswa</span><span class="text-slate-900 font-extrabold">: {{ $student->name }}</span></div>
                <div class="flex"><span class="text-slate-500 w-24">NISN</span><span class="text-slate-700 font-bold font-mono">: {{ $student->nisn }}</span></div>
            </div>
            <div class="space-y-1">
                <div class="flex"><span class="text-slate-500 w-24">Kelas</span><span class="text-slate-700 font-bold">: {{ $student->schoolClass ? $student->schoolClass->name : '-' }}</span></div>
                <div class="flex"><span class="text-slate-500 w-24">Status Siswa</span><span class="text-emerald-600 font-extrabold">: Aktif</span></div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="my-5">
            <table class="w-full text-[11px] text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-900 text-slate-800 font-bold uppercase tracking-wider">
                        <th class="py-2.5 w-10 text-center">No</th>
                        <th class="py-2.5 w-24">Tanggal</th>
                        <th class="py-2.5 w-32">No. Transaksi</th>
                        <th class="py-2.5">Keterangan / Uraian</th>
                        <th class="py-2.5 text-right w-36">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-800">
                    @forelse($transactions as $index => $tx)
                        <tr>
                            <td class="py-2.5 text-center text-slate-500 font-bold">{{ $index + 1 }}</td>
                            <td class="py-2.5 text-slate-600">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                            <td class="py-2.5 font-mono text-indigo-600 font-bold">{{ $tx->transaction_number }}</td>
                            <td class="py-2.5 pr-4 leading-relaxed">{{ $tx->description }}</td>
                            <td class="py-2.5 text-right font-bold text-slate-950">
                                Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-400">Tidak ada transaksi pembayaran di periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t border-slate-900 font-black text-slate-950">
                        <td colspan="4" class="py-3 text-right uppercase tracking-wider">Total Pembayaran Masuk:</td>
                        <td class="py-3 text-right text-xs text-emerald-600 font-black">
                            Rp {{ number_format($transactions->sum('amount'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Signature -->
        <div class="mt-12 flex justify-between items-start text-[10px] pt-6 border-t border-slate-200">
            <div class="text-slate-400 max-w-sm italic">
                * Dokumen ini dicetak otomatis secara resmi oleh sistem keuangan sekolah.
            </div>
            <div class="text-center min-w-[200px]">
                <p class="text-slate-500 font-semibold mb-12">Bendahara Keuangan Sekolah,</p>
                <p class="font-bold text-slate-900 uppercase border-b border-slate-900 pb-0.5 inline-block">STAFF BENDAHARA</p>
                <p class="text-[9px] text-slate-400 mt-1 block">NIP / NPY: -</p>
            </div>
        </div>
    </div>

</body>
</html>
