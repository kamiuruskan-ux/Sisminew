<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Kantin - {{ Setting::get('school_name', 'SEKOLAH') }}</title>
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
    <div class="no-print w-full max-w-4xl mb-6 bg-slate-900 text-white p-4 rounded-2xl shadow-xl flex items-center justify-between">
        <div>
            <h1 class="font-extrabold text-sm">Preview Laporan Penjualan Kantin</h1>
            <p class="text-xs text-slate-400">Laporan Keuangan E-Kantin Sekolah</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Laporan</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Printable Report Container -->
    <div class="print-card w-full max-w-4xl bg-white border border-slate-300 shadow-xl rounded-3xl p-8 md:p-12">
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
                <span class="text-xs font-black text-slate-400 uppercase tracking-widest block">LAPORAN E-KANTIN</span>
                <span class="text-sm font-extrabold text-slate-900 block mt-1">LAPORAN PENJUALAN</span>
                <span class="text-[11px] text-slate-500 block mt-1">Tanggal Cetak: {{ date('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="text-center my-6">
            <h1 class="text-lg font-black text-slate-900 uppercase tracking-tight">LAPORAN REKAPITULASI PENJUALAN KANTIN</h1>
            <p class="text-xs text-slate-500">Periode: {{ now()->translatedFormat('F Y') }}</p>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-3 gap-6 my-6">
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pendapatan Hari Ini</span>
                <span class="text-lg font-black text-slate-900 block mt-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-500 mt-1 block">{{ $todayOrders }} Transaksi Berhasil</span>
            </div>
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pendapatan Bulan Ini</span>
                <span class="text-lg font-black text-slate-900 block mt-1">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-500 mt-1 block">Akumulasi Bulan Ini</span>
            </div>
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Menu Paling Laris</span>
                <span class="text-sm font-extrabold text-indigo-600 block mt-1 truncate">{{ $topItems->first()?->item_name ?? '-' }}</span>
                <span class="text-[10px] text-slate-500 mt-1 block">{{ $topItems->first()?->total_qty ?? 0 }} Porsi Terjual</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 my-8">
            <!-- Top Selling Table -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">5 Menu Terlaris</h3>
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700 uppercase">
                            <tr>
                                <th class="p-3 w-10 text-center">No</th>
                                <th class="p-3">Nama Menu</th>
                                <th class="p-3 text-right">Terjual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-800 font-semibold">
                            @forelse($topItems as $idx => $ti)
                                <tr>
                                    <td class="p-3 text-center text-slate-500 font-bold">{{ $idx + 1 }}</td>
                                    <td class="p-3 font-bold text-slate-900">{{ $ti->item_name }}</td>
                                    <td class="p-3 text-right font-black text-slate-950">{{ $ti->total_qty }} Porsi</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-slate-400">Tidak ada data menu terjual.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">10 Transaksi Terakhir</h3>
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700 uppercase">
                            <tr>
                                <th class="p-3">No. Order</th>
                                <th class="p-3">Siswa</th>
                                <th class="p-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-800 font-semibold">
                            @forelse($recentPaidOrders as $ro)
                                <tr>
                                    <td class="p-3 text-slate-900 font-mono">{{ $ro->order_number }}</td>
                                    <td class="p-3 truncate max-w-[120px]">{{ $ro->student->user->name ?? 'Umum/Guest' }}</td>
                                    <td class="p-3 text-right font-black text-slate-950">Rp {{ number_format($ro->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-slate-400">Tidak ada transaksi baru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Signature -->
        <div class="mt-12 flex justify-between items-start text-xs pt-8 border-t border-slate-200">
            <div class="text-slate-400 max-w-sm">
                * Laporan ini ditarik secara real-time dari database penjualan E-Kantin.
            </div>
            <div class="text-center min-w-[200px]">
                <p class="text-slate-500 font-semibold mb-16">Pengelola Kantin Sekolah,</p>
                <p class="font-black text-slate-900 uppercase border-b border-slate-950 pb-0.5 inline-block">STAFF E-KANTIN</p>
                <p class="text-[10px] text-slate-400 mt-1">NIP / NPY: -</p>
            </div>
        </div>
    </div>

</body>
</html>
