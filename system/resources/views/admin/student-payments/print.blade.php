<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran - {{ $transaction->transaction_number }}</title>
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
<body class="bg-slate-100 p-6 md:p-12 min-h-screen flex flex-col items-center" onload="window.print()">

    <!-- Action Bar (Non-Printable) -->
    <div class="no-print w-full max-w-3xl mb-6 bg-slate-900 text-white p-4 rounded-2xl shadow-xl flex items-center justify-between">
        <div>
            <h1 class="font-extrabold text-sm">Preview Kuitansi Pembayaran</h1>
            <p class="text-xs text-slate-400">Siap dicetak atau disimpan sebagai PDF</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak (Ctrl+P)</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Printable Receipt Card -->
    <div class="print-card w-full max-w-3xl bg-white border border-slate-300 shadow-xl rounded-3xl p-8 md:p-12">
        <!-- Kop Surat / Header -->
        <div class="flex items-center justify-between pb-6 border-b-2 border-slate-800">
            <div class="flex items-center space-x-4">
                <img src="{{ Setting::getLogoUrl() }}" alt="Logo" class="w-16 h-16 object-contain">
                <div>
                    <h2 class="text-xl font-black uppercase text-slate-900 tracking-tight">{{ Setting::get('school_name', 'SEKOLAH INDONESIA') }}</h2>
                    <p class="text-xs font-semibold text-slate-600">{{ Setting::get('school_address', 'Jl. Pendidikan No. 1') }}</p>
                    <p class="text-[11px] text-slate-500">Telp: {{ Setting::get('school_phone', '-') }} &bull; Email: {{ Setting::get('school_email', '-') }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-black text-slate-400 uppercase tracking-widest block">KUITANSI PEMBAYARAN</span>
                <span class="text-base font-black font-mono text-indigo-600">{{ $transaction->transaction_number }}</span>
                <span class="text-xs text-slate-500 block mt-1">{{ $transaction->transaction_date->format('d/m/Y') }}</span>
            </div>
        </div>

        <!-- Student & Payment Info -->
        <div class="grid grid-cols-2 gap-6 py-6 text-xs border-b border-slate-200">
            <div>
                <span class="text-slate-400 font-bold block uppercase tracking-wider">Telah Diterima Dari</span>
                <span class="text-base font-extrabold text-slate-900 block mt-0.5">{{ $student ? $student->name : $transaction->recipient_or_payee }}</span>
                @if($student)
                    <span class="text-slate-600 block mt-0.5 font-medium">NISN: {{ $student->nisn }} &bull; Kelas: {{ $student->schoolClass ? $student->schoolClass->name : '-' }}</span>
                @endif
            </div>
            <div class="text-right">
                <span class="text-slate-400 font-bold block uppercase tracking-wider">Metode / Rekening</span>
                <span class="text-sm font-bold text-slate-800 block mt-0.5">{{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_name }}</span>
                <span class="text-slate-500 block mt-0.5 font-medium">Petugas: {{ $transaction->creator ? $transaction->creator->name : 'Kasir Bendahara' }}</span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="my-6 border rounded-2xl overflow-hidden border-slate-200 shadow-sm">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700 uppercase">
                    <tr>
                        <th class="p-4 w-12 text-center">No</th>
                        <th class="p-4">Uraian Pembayaran</th>
                        <th class="p-4 text-right w-44">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-800 font-medium">
                    @forelse($paidDetails as $index => $detail)
                        <tr>
                            <td class="p-4 text-center font-semibold text-slate-500">{{ $index + 1 }}</td>
                            <td class="p-4 font-bold text-slate-900">
                                {{ $detail->studentPaymentBill->paymentBill->name }}
                                @if($detail->month_name)
                                    ({{ $detail->month_name }})
                                @endif
                            </td>
                            <td class="p-4 text-right font-black text-slate-950">
                                Rp {{ number_format($detail->paid_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-4 text-center font-semibold text-slate-500">1</td>
                            <td class="p-4 font-bold text-slate-900">{{ $transaction->description }}</td>
                            <td class="p-4 text-right font-black text-slate-950">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-200 font-black text-slate-900">
                    <tr>
                        <td colspan="2" class="p-4 text-right text-xs uppercase tracking-wider">TOTAL DIBAYAR:</td>
                        <td class="p-4 text-right text-sm text-emerald-600 font-black">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footnotes & Signatures -->
        <div class="mt-8 flex flex-col md:flex-row justify-between items-start text-xs pt-6 border-t border-slate-200 gap-6">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-500 font-medium italic max-w-sm leading-relaxed">
                Catatan: Kuitansi ini adalah bukti pembayaran yang sah dan dicetak secara otomatis oleh sistem Keuangan Sekolah.
            </div>

            <div class="text-center min-w-[200px] ml-auto shrink-0">
                <p class="text-slate-500 font-semibold mb-16">Bendahara Sekolah,</p>
                <p class="font-black text-slate-900 uppercase border-b border-slate-900 pb-0.5 inline-block">{{ $transaction->creator ? $transaction->creator->name : 'BENDAHARA' }}</p>
                <p class="text-[10px] text-slate-400 mt-1">NIP / NPY: -</p>
            </div>
        </div>
    </div>

</body>
</html>
