<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Resmi - {{ Setting::get('school_name', 'SEKOLAH') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="apple-touch-icon" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #0f172a;
            margin: 0;
            padding: 25px;
            background: #fff;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .school-title {
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            margin: 0;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .school-subtitle {
            font-size: 11px;
            color: #475569;
            margin-top: 4px;
        }
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .report-period {
            text-align: center;
            font-size: 11px;
            color: #475569;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f1f5f9;
            font-weight: 800;
            text-transform: uppercase;
            color: #334155;
            font-size: 9px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .font-mono { font-family: monospace; }
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
        }
        .summary-card {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #f8fafc;
        }
        .summary-label {
            font-size: 9px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
        }
        .summary-val {
            font-size: 13px;
            font-weight: 900;
            margin-top: 3px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            min-width: 200px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #0f172a; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 12px;">Cetak Dokumen Resmi</button>
    </div>

    <!-- Header Kop Surat Sekolah -->
    <div class="header">
        <div style="display: flex; align-items: center; gap: 14px;">
            <img src="{{ Setting::getLogoUrl() }}" alt="Logo" style="height: 52px; width: 52px; object-fit: contain;">
            <div>
                <h1 class="school-title">{{ Setting::get('school_name', 'SEKOLAH INDONESIA') }}</h1>
                <div class="school-subtitle">{{ Setting::get('school_address', 'Jl. Pendidikan No. 1') }} &bull; Telp: {{ Setting::get('school_phone', '-') }} &bull; Email: {{ Setting::get('school_email', '-') }}</div>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-weight: 900; font-size: 11px; text-transform: uppercase;">BUKU KAS UMUM (BKU)</div>
            <div style="font-size: 10px; color: #64748b; margin-top: 3px;">Tgl Cetak: {{ date('d/m/Y H:i') }}</div>
        </div>
    </div>

    <!-- Judul Laporan -->
    <div class="report-title">
        @if($reportType === 'cashbook') LAPORAN MUTASI BUKU KAS UMUM
        @elseif($reportType === 'income') LAPORAN PEMASUKAN KAS (KAS MASUK)
        @elseif($reportType === 'expense') LAPORAN PENGELUARAN KAS (KAS KELUAR)
        @else LAPORAN REKAPITULASI TAGIHAN & TUNGGAKAN SISWA
        @endif
    </div>
    <div class="report-period">
        Periode: {{ date('d F Y', strtotime($startDate)) }} s/d {{ date('d F Y', strtotime($endDate)) }}
        @if($bankAccount) &bull; Kas/Rekening: {{ $bankAccount->bank_name }} ({{ $bankAccount->account_name }}) @endif
        @if($category) &bull; Kategori: {{ $category->name }} @endif
        @if($schoolClass) &bull; Kelas: {{ $schoolClass->name }} @endif
    </div>

    <!-- Summary Box -->
    @if($reportType !== 'student_bills')
        <div class="summary-box">
            <div class="summary-card">
                <div class="summary-label">Saldo Awal Periode</div>
                <div class="summary-val" style="color: #475569;">Rp {{ number_format($openingBalance, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Pemasukan</div>
                <div class="summary-val" style="color: #059669;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Pengeluaran</div>
                <div class="summary-val" style="color: #e11d48;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Saldo Akhir Periode</div>
                <div class="summary-val" style="color: #4f46e5;">Rp {{ number_format($endingBalance, 0, ',', '.') }}</div>
            </div>
        </div>
    @else
        <div class="summary-box">
            <div class="summary-card">
                <div class="summary-label">Total Tagihan</div>
                <div class="summary-val" style="color: #4f46e5;">Rp {{ number_format($studentBills->sum('total_amount'), 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Terbayar</div>
                <div class="summary-val" style="color: #059669;">Rp {{ number_format($studentBills->sum('paid_amount'), 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Sisa Tunggakan</div>
                <div class="summary-val" style="color: #e11d48;">Rp {{ number_format($studentBills->sum('total_amount') - $studentBills->sum('paid_amount'), 0, ',', '.') }}</div>
            </div>
        </div>
    @endif

    <!-- Table Details -->
    @if($reportType !== 'student_bills')
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th style="width: 110px;">No. Transaksi</th>
                    <th style="width: 70px;">Tanggal</th>
                    <th style="width: 120px;">Kategori</th>
                    <th style="width: 100px;">Kas / Rekening</th>
                    <th>Uraian Transaksi</th>
                    <th class="text-right" style="width: 100px;">Kas Masuk (Rp)</th>
                    <th class="text-right" style="width: 100px;">Kas Keluar (Rp)</th>
                    <th class="text-right" style="width: 110px;">Saldo (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Saldo Awal Row -->
                <tr style="background-color: #f8fafc; font-weight: bold;">
                    <td class="text-center">-</td>
                    <td class="font-mono">SALDO-AWAL</td>
                    <td>{{ date('d/m/Y', strtotime($startDate)) }}</td>
                    <td>Saldo Awal</td>
                    <td>-</td>
                    <td>Saldo Awal Kas & Rekening Bank sebelum {{ date('d/m/Y', strtotime($startDate)) }}</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right font-bold">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                </tr>

                @forelse($transactions as $index => $tx)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="font-mono">{{ $tx->transaction_number }}</td>
                        <td>{{ $tx->transaction_date->format('d/m/Y') }}</td>
                        <td>{{ $tx->financialCategory->name ?? '-' }}</td>
                        <td>{{ $tx->bankAccount->bank_name ?? '-' }}</td>
                        <td>{{ $tx->description }}</td>
                        <td class="text-right font-bold" style="color: #059669;">
                            {{ $tx->type === 'pemasukan' ? number_format($tx->amount, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right font-bold" style="color: #e11d48;">
                            {{ $tx->type === 'pengeluaran' ? number_format($tx->amount, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-right font-bold">
                            Rp {{ number_format($tx->running_balance, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: #94a3b8; padding: 15px;">Tidak ada transaksi dalam periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #f1f5f9; font-weight: bold;">
                    <td colspan="6" class="text-right">TOTAL PERIODE INI:</td>
                    <td class="text-right" style="color: #059669;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #e11d48;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #4f46e5;">Rp {{ number_format($endingBalance, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Pos Tagihan</th>
                    <th>Tipe</th>
                    <th class="text-right">Total Tagihan</th>
                    <th class="text-right">Sudah Dibayar</th>
                    <th class="text-right">Sisa Tunggakan</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($studentBills as $index => $sb)
                    @php $sisa = $sb->total_amount - $sb->paid_amount; @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="font-bold">{{ $sb->student->name }}</td>
                        <td>{{ $sb->student->schoolClass ? $sb->student->schoolClass->name : '-' }}</td>
                        <td>{{ $sb->paymentBill->name }}</td>
                        <td style="text-transform: uppercase;">{{ $sb->paymentBill->type }}</td>
                        <td class="text-right">Rp {{ number_format($sb->total_amount, 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color: #059669;">Rp {{ number_format($sb->paid_amount, 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color: #e11d48;">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                        <td class="text-center font-bold" style="text-transform: uppercase;">{{ $sb->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; color: #94a3b8; padding: 15px;">Tidak ada data tagihan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <!-- Tanda Tangan Resmi -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p style="font-weight: bold;">Kepala Sekolah</p>
            <div style="height: 55px;"></div>
            <p style="font-weight: bold; text-decoration: underline;">_______________________</p>
            <p style="font-size: 9px; color: #64748b; margin-top: 2px;">NIP. ....................................</p>
        </div>
        <div class="signature-box">
            <p>{{ Setting::get('school_city', 'Kota') }}, {{ date('d F Y') }}</p>
            <p style="font-weight: bold;">Bendahara Sekolah</p>
            <div style="height: 55px;"></div>
            <p style="font-weight: bold; text-decoration: underline;">_______________________</p>
            <p style="font-size: 9px; color: #64748b; margin-top: 2px;">NIP. ....................................</p>
        </div>
    </div>
</body>
</html>
