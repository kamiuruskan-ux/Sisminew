<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Presensi Siswa - {{ $raportSettings['school_name'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 10pt;
            color: #0f172a;
            background-color: #f1f5f9;
            line-height: 1.4;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            margin: 10px auto;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
        }
        @media print {
            body {
                background: white;
            }
            .page {
                margin: 0;
                box-shadow: none;
                width: 100%;
                min-height: auto;
                padding: 10mm 12mm;
            }
            .no-print {
                display: none !important;
            }
        }
        /* Action Bar */
        .action-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            gap: 10px;
            background: #0f172a;
            padding: 10px 16px;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .action-btn {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .action-btn:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }
        .action-btn-secondary {
            background: #334155;
        }
        .action-btn-secondary:hover {
            background: #475569;
        }

        /* Kop Surat Header */
        .kop-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            border-bottom: 3.5px double #0f172a;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .kop-logo-box {
            width: 75px;
            height: 75px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .kop-logo {
            max-width: 75px;
            max-height: 75px;
            object-fit: contain;
        }
        .kop-details {
            flex: 1;
            text-align: center;
        }
        .kop-details .kop-header-top {
            font-size: 8.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e293b;
            margin: 0 0 1px 0;
            line-height: 1.2;
        }
        .kop-details .kop-sub {
            font-size: 9.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin: 0 0 2px 0;
            line-height: 1.2;
        }
        .kop-details h1 {
            font-size: 14pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #020617;
            font-family: Georgia, "Times New Roman", serif;
            font-variant-numeric: lining-nums;
            margin: 0 0 3px 0;
            line-height: 1.1;
        }
        .kop-details p {
            font-size: 8.5pt;
            color: #334155;
            font-weight: 500;
            margin: 0;
            line-height: 1.35;
        }

        /* Document Title Bar */
        .doc-title-container {
            text-align: center;
            margin-bottom: 15px;
        }
        .doc-title {
            font-size: 13pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        .doc-subtitle {
            font-size: 9pt;
            font-weight: 600;
            color: #475569;
            margin-top: 3px;
        }

        /* Meta Filter Grid */
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 8.5pt;
        }
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        .meta-label {
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .meta-val {
            font-weight: 800;
            color: #0f172a;
        }

        /* Tables */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 20px;
        }
        table.report-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #0f172a;
            text-align: center;
            font-size: 8pt;
        }
        table.report-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            color: #1e293b;
        }
        table.report-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .font-extrabold { font-weight: 800; }

        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 7.5pt;
            text-transform: uppercase;
        }
        .badge-present { background: #dcfce7; color: #166534; }
        .badge-late { background: #fef3c7; color: #92400e; }
        .badge-excused { background: #e0e7ff; color: #3730a3; }
        .badge-absent { background: #ffe4e6; color: #9f1239; }

        /* Signatures Section */
        .signatures-container {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 9pt;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 220px;
            text-align: center;
        }
        .sig-space {
            height: 60px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sig-stamp {
            position: absolute;
            max-height: 70px;
            opacity: 0.8;
            left: 20px;
        }
        .sig-img {
            max-height: 55px;
            z-index: 2;
        }
        .sig-name {
            font-weight: 800;
            text-decoration: underline;
            color: #0f172a;
        }
    </style>
</head>
<body>

    <!-- Floating Print Action Bar -->
    <div class="action-bar no-print">
        <button onclick="window.print()" class="action-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-4 0h-4v4h8v-4z"/></svg>
            <span>Cetak PDF / Print</span>
        </button>
        <button onclick="window.close()" class="action-btn action-btn-secondary">
            <span>Tutup</span>
        </button>
    </div>

    <div class="page">
        <!-- Header Kop Surat -->
        <div class="kop-container">
            <div class="kop-logo-box">
                @php
                    $attendanceLogo = !empty($raportSettings['school_logo']) ? $raportSettings['school_logo'] : Setting::getLogoUrl();
                @endphp
                <img src="{{ $attendanceLogo }}" alt="Logo" class="kop-logo">
            </div>

            <div class="kop-details">
                @if(!empty($raportSettings['letterhead_header_top']))
                    <p class="kop-header-top">{{ $raportSettings['letterhead_header_top'] }}</p>
                @endif
                @if(!empty($raportSettings['letterhead_sub']))
                    <p class="kop-sub">{{ $raportSettings['letterhead_sub'] }}</p>
                @endif
                <h1>{{ $raportSettings['school_name'] }}</h1>
                <p>
                    {{ $raportSettings['school_address'] }}
                    @if(!empty($raportSettings['school_city']))
                        , {{ $raportSettings['school_city'] }}
                    @endif
                    @if(!empty($raportSettings['school_postal_code']))
                        {{ $raportSettings['school_postal_code'] }}
                    @endif
                </p>
                <p>
                    Telp: {{ $raportSettings['school_phone'] }} | Email: {{ $raportSettings['school_email'] }} | Web: {{ $raportSettings['school_website'] }}
                </p>
            </div>

            <div class="kop-logo-box">
                @if(!empty($raportSettings['school_logo_right']))
                    <img src="{{ $raportSettings['school_logo_right'] }}" alt="Logo Kanan" class="kop-logo">
                @endif
            </div>
        </div>

        <!-- Document Title -->
        <div class="doc-title-container">
            <h2 class="doc-title">REKAPITULASI PRESENSI KEHADIRAN SISWA</h2>
            <p class="doc-subtitle">Laporan Hasil Presensi Harian Siswa Sekolah</p>
        </div>

        <!-- Meta Information Filter -->
        <div class="meta-grid">
            <div class="meta-item">
                <span class="meta-label">Periode Tanggal</span>
                <span class="meta-val">
                    @if($startDate === $endDate)
                        {{ date('d/m/Y', strtotime($startDate)) }}
                    @else
                        {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}
                    @endif
                </span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Kelas Rombel</span>
                <span class="meta-val">{{ $selectedClass->name ?? 'Semua Kelas' }}</span>
            </div>
            @if(\App\Models\Setting::get('is_vocational', '1') == '1')
            <div class="meta-item">
                <span class="meta-label">Jurusan / Major</span>
                <span class="meta-val">{{ $selectedMajor->name ?? ($selectedClass->major->name ?? 'Semua Jurusan') }}</span>
            </div>
            @endif
            <div class="meta-item">
                <span class="meta-label">Status Presensi</span>
                <span class="meta-val">{{ $status ? strtoupper($status) : 'SEMUA STATUS' }}</span>
            </div>
        </div>

        <!-- Summary Per Student Table -->
        @if(($type ?? 'all') === 'summary' || ($type ?? 'all') === 'all')
            @if($studentSummaries->count() > 0)
                <h3 style="font-size: 9.5pt; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; color: #0f172a;">
                    Ringkasan Kehadiran Per Siswa
                </h3>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th width="4%">No</th>
                            <th width="14%">NISN</th>
                            <th width="28%">Nama Siswa</th>
                            <th width="14%">Kelas</th>
                            <th width="8%">Hadir</th>
                            <th width="8%">Telat</th>
                            <th width="8%">Izin</th>
                            <th width="8%">Alpha</th>
                            <th width="8%">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentSummaries as $index => $summary)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center font-bold">{{ $summary['student']->nisn ?? '-' }}</td>
                                <td class="font-bold">{{ $summary['student']->user->name ?? $summary['student']->name ?? 'Siswa' }}</td>
                                <td class="text-center">{{ $summary['student']->class->name ?? 'Tanpa Kelas' }}</td>
                                <td class="text-center font-bold text-emerald-700">{{ $summary['present'] }}</td>
                                <td class="text-center font-bold text-amber-700">{{ $summary['late'] }}</td>
                                <td class="text-center font-bold text-indigo-700">{{ $summary['excused'] }}</td>
                                <td class="text-center font-bold text-rose-700">{{ $summary['absent'] }}</td>
                                <td class="text-center font-extrabold">{{ $summary['percentage'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif

        <!-- Detailed Log Table -->
        @if(($type ?? 'all') === 'detail' || ($type ?? 'all') === 'all')
            <h3 style="font-size: 9.5pt; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; color: #0f172a;">
                Log Presensi Detail (Total {{ $attendances->count() }} Data)
            </h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="14%">NISN</th>
                        <th width="24%">Nama Siswa</th>
                        <th width="12%">Kelas</th>
                        <th width="12%">Status</th>
                        <th width="22%">Catatan / Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ date('d/m/Y', strtotime($item->date)) }}</td>
                            <td class="text-center font-bold">{{ $item->student->nisn ?? '-' }}</td>
                            <td class="font-bold">{{ $item->student->user->name ?? $item->student->name ?? 'Siswa' }}</td>
                            <td class="text-center">{{ $item->class->name ?? $item->student->class->name ?? '-' }}</td>
                            <td class="text-center">
                                @if($item->status === 'present')
                                    <span class="badge badge-present">Hadir</span>
                                @elseif($item->status === 'late')
                                    <span class="badge badge-late">Terlambat</span>
                                @elseif($item->status === 'excused')
                                    <span class="badge badge-excused">Izin/Sakit</span>
                                @else
                                    <span class="badge badge-absent">Alpha</span>
                                @endif
                            </td>
                            <td>{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 15px; color: #64748b;">
                                Tidak ada data presensi yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        <!-- Signatures Footer -->
        <div class="signatures-container">
            <div class="sig-box">
                <p>Mengetahui,</p>
                <p class="font-bold">Wali Kelas / Guru Piket</p>
                <div class="sig-space"></div>
                <p class="sig-name">( ........................................ )</p>
                <p style="font-size: 8pt; color: #64748b;">NIP. -</p>
            </div>

            <div class="sig-box">
                <p>{{ $raportSettings['city'] }}, {{ $raportSettings['date'] }}</p>
                <p class="font-bold">Kepala Sekolah</p>
                <div class="sig-space">
                    @if($raportSettings['stamp_path'])
                        <img src="{{ $raportSettings['stamp_path'] }}" alt="Stempel" class="sig-stamp">
                    @endif
                    @if($raportSettings['signature_path'])
                        <img src="{{ $raportSettings['signature_path'] }}" alt="Tanda Tangan" class="sig-img">
                    @endif
                </div>
                <p class="sig-name">{{ $raportSettings['principal_name'] }}</p>
                <p style="font-size: 8pt; color: #64748b;">NIP. {{ $raportSettings['principal_nip'] }}</p>
            </div>
        </div>
    </div>

</body>
</html>
