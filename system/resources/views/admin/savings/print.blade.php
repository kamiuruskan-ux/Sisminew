<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mutasi Tabungan - {{ $student->name }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="apple-touch-icon" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .student-info {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .table-mutasi {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table-mutasi th, .table-mutasi td {
            border: 1px solid #666;
            padding: 6px 8px;
            text-align: left;
        }
        .table-mutasi th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .footer td {
            text-align: center;
            vertical-align: top;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Cetak Dokumen
        </button>
    </div>

    <div class="header" style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 20px;">
        <img src="{{ Setting::getLogoUrl() }}" alt="Logo" style="height: 60px; width: 60px; object-fit: contain;">
        <div style="text-align: center;">
            <h2 style="margin: 0; font-size: 18px; text-transform: uppercase;">{{ Setting::get('school_name', 'SEKOLAH') }}</h2>
            <p style="margin: 2px 0 0 0; font-size: 11px; color: #475569;">{{ Setting::get('school_address', 'Alamat Sekolah') }}</p>
            <p style="margin: 4px 0 0 0; font-size: 12px; font-weight: bold; text-transform: uppercase;">LAPORAN MUTASI TABUNGAN SISWA</p>
        </div>
    </div>

    <table class="student-info">
        <tr>
            <td width="15%"><strong>Nama Siswa</strong></td>
            <td width="35%">: {{ $student->name }}</td>
            <td width="15%"><strong>Saldo Tabungan</strong></td>
            <td width="35%">: <strong>Rp {{ number_format($student->savings_balance, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td><strong>NISN</strong></td>
            <td>: {{ $student->nisn }}</td>
            <td><strong>Kelas</strong></td>
            <td>: {{ $student->schoolClass->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Wali Murid</strong></td>
            <td>: {{ $student->parent_name ?? '-' }}</td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ date('d/m/Y H:i') }} WIB</td>
        </tr>
    </table>

    <table class="table-mutasi">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="20%">No. Referensi</th>
                <th width="15%">Jenis</th>
                <th>Keterangan</th>
                <th width="15%" class="text-right">Nominal (Rp)</th>
                <th width="15%" class="text-right">Saldo Akhir (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $t)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td><code>{{ $t->reference_no }}</code></td>
                    <td>{{ $t->type_label }}</td>
                    <td>{{ $t->notes ?? '-' }}</td>
                    <td class="text-right">{{ $t->formatted_amount }}</td>
                    <td class="text-right">{{ number_format($t->balance_after, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Belum ada data mutasi tabungan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer">
        <tr>
            <td width="50%">
                <br><br>
                Orang Tua / Wali Siswa
                <br><br><br><br>
                ( _____________________ )
            </td>
            <td width="50%">
                {{ Setting::get('school_city', 'Kota') }}, {{ date('d F Y') }}<br>
                Petugas / Bendahara Sekolah
                <br><br><br><br>
                ( _____________________ )
            </td>
        </tr>
    </table>

</body>
</html>
