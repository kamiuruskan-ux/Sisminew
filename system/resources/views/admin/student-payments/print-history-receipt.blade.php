<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Gabungan Pembayaran - {{ $student->name }}</title>
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
            <h1 class="font-extrabold text-xs">Preview Kuitansi Gabungan</h1>
            <p class="text-[10px] text-slate-400">Ukuran Lebar & Minimalis (A4 Horizontal / Vertikal)</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold rounded-lg transition flex items-center space-x-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak (Ctrl+P)</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold rounded-lg transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>

    <!-- Printable Receipt Card -->
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
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">KUITANSI GABUNGAN</span>
                <span class="text-xs font-black text-slate-950 block mt-0.5">PEMBAYARAN SEKOLAH</span>
                <span class="text-[9px] text-slate-400 block">Dicetak: {{ date('d/m/Y H:i') }}</span>
            </div>
        </div>

        <!-- Student & Date Range Info -->
        <div class="grid grid-cols-2 gap-6 py-4 text-[11px] border-b border-slate-200">
            <div>
                <span class="text-slate-500 font-bold block uppercase tracking-wider text-[9px]">Telah Diterima Dari:</span>
                <span class="text-sm font-extrabold text-slate-950 block mt-0.5">{{ $student->name }}</span>
                <span class="text-slate-600 block mt-0.5 font-medium">NISN: {{ $student->nisn }} | Kelas: {{ $student->schoolClass ? $student->schoolClass->name : '-' }}</span>
            </div>
            <div class="text-right">
                <span class="text-slate-500 font-bold block uppercase tracking-wider text-[9px]">Periode Transaksi:</span>
                <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</span>
                <span class="text-slate-500 block mt-0.5 font-medium">Jenis: Kuitansi Gabungan Rekapitulasi</span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="my-5">
            <table class="w-full text-left text-[11px] border-collapse">
                <thead>
                    <tr class="border-b border-slate-900 text-slate-800 font-bold uppercase tracking-wider">
                        <th class="py-2.5 w-12 text-center">No</th>
                        <th class="py-2.5">Uraian / Deskripsi Pembayaran</th>
                        <th class="py-2.5 w-28 text-center">Tgl Bayar</th>
                        <th class="py-2.5 text-right w-44">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-800">
                    @forelse($paidDetails as $index => $detail)
                        <tr>
                            <td class="py-2.5 text-center font-bold text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-2.5 font-bold text-slate-900">
                                {{ $detail->studentPaymentBill->paymentBill->name }}
                                @if($detail->month_name)
                                    ({{ $detail->month_name }})
                                @endif
                            </td>
                            <td class="py-2.5 text-center text-slate-500 font-mono">{{ $detail->paid_at ? \Carbon\Carbon::parse($detail->paid_at)->format('d/m/Y') : '-' }}</td>
                            <td class="py-2.5 text-right font-bold text-slate-950">
                                Rp {{ number_format($detail->paid_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-slate-400">Tidak ada detail pembayaran di rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t border-slate-900 font-black text-slate-950">
                        <td colspan="3" class="py-3 text-right uppercase tracking-wider">TOTAL TERBAYAR:</td>
                        <td class="py-3 text-right text-xs text-emerald-600 font-black">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footnotes & Signatures -->
        <div class="mt-8 flex flex-col md:flex-row justify-between items-start text-[10px] pt-6 border-t border-slate-200 gap-6">
            <div class="text-slate-400 italic max-w-sm leading-relaxed">
                Catatan: Kuitansi Gabungan ini memuat rekap seluruh pos pembayaran terbayar dalam rentang tanggal yang dipilih.
            </div>

            <div class="text-center min-w-[200px] ml-auto shrink-0">
                <p class="text-slate-500 font-semibold mb-12">Bendahara Sekolah,</p>
                <p class="font-bold text-slate-900 uppercase border-b border-slate-900 pb-0.5 inline-block">STAFF BENDAHARA</p>
                <p class="text-[9px] text-slate-400 mt-1 block">NIP / NPY: -</p>
            </div>
        </div>
    </div>

</body>
</html>
