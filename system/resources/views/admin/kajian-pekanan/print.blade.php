<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara & Rekap Presensi Kajian Pekanan Pegawai - {{ $session->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
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
            body { background: white; }
            .page {
                margin: 0;
                box-shadow: none;
                width: 100%;
                min-height: auto;
                padding: 10mm 12mm;
            }
            .no-print { display: none !important; }
        }
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
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-print { background: #10b981; color: #fff; }
        .btn-back { background: #334155; color: #fff; }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
        }
        .kop-logo { width: 80px; text-align: center; vertical-align: middle; }
        .kop-logo img { width: 75px; max-height: 75px; object-fit: contain; }
        .kop-text { text-align: center; vertical-align: middle; padding: 0 10px; }
        .kop-title { font-size: 14pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-sub { font-size: 9pt; color: #334155; margin-top: 2px; }

        /* Title */
        .report-title {
            text-align: center;
            font-size: 12pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 15px 0 12px 0;
            text-decoration: underline;
        }

        /* Detail Box */
        .detail-box {
            width: 100%;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        .detail-box td { padding: 3px 0; vertical-align: top; }
        .detail-box td.label { width: 140px; font-weight: 700; color: #334155; }
        .detail-box td.sep { width: 15px; text-align: center; }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #94a3b8;
            padding: 6px 8px;
        }
        .data-table th {
            background-color: #f8fafc;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-hadir { background: #dcfce7; color: #15803d; }
        .badge-izin { background: #e0e7ff; color: #4338ca; }
        .badge-sakit { background: #fef3c7; color: #b45309; }
        .badge-alpa { background: #ffe4e6; color: #be123c; }

        /* Signatures */
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 220px;
            text-align: center;
            font-size: 9.5pt;
        }
        .signature-space { height: 60px; }
    </style>
</head>
<body>

    <div class="action-bar no-print">
        <a href="{{ route('admin.kajian-pekanan.show', $session->id) }}" class="action-btn btn-back">
            &larr; Kembali
        </a>
        <button onclick="window.print()" class="action-btn btn-print">
            &#128438; Cetak Berita Acara
        </button>
    </div>

    <div class="page">
        <!-- Kop Surat -->
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    <img src="{{ $raportSettings['school_logo'] }}" alt="Logo">
                </td>
                <td class="kop-text">
                    <div class="kop-title">{{ $raportSettings['school_name'] }}</div>
                    <div class="kop-sub">{{ $raportSettings['school_address'] }} - Kota {{ $raportSettings['school_city'] }}</div>
                    <div class="kop-sub">Telp: {{ $raportSettings['school_phone'] }} | Website: sditalfahmi-palu.com</div>
                </td>
            </tr>
        </table>

        <div class="report-title">
            BERITA ACARA &amp; REKAPITULASI PRESENSI KAJIAN PEKANAN PEGAWAI
        </div>

        <table class="detail-box">
            <tr>
                <td class="label">Tema Kajian</td>
                <td class="sep">:</td>
                <td><strong>{{ $session->title }}</strong></td>
            </tr>
            <tr>
                <td class="label">Pemateri / Ustadz</td>
                <td class="sep">:</td>
                <td>{{ $session->speaker }}</td>
            </tr>
            <tr>
                <td class="label">Hari / Tanggal</td>
                <td class="sep">:</td>
                <td>{{ $session->date->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Waktu &amp; Tempat</td>
                <td class="sep">:</td>
                <td>{{ $session->time_start ?: '08:30' }} - {{ $session->time_end ?: 'Selesai' }} WITA | {{ $session->location }}</td>
            </tr>
            @if($session->material_summary)
            <tr>
                <td class="label">Ringkasan Materi</td>
                <td class="sep">:</td>
                <td>{{ Str::limit($session->material_summary, 200) }}</td>
            </tr>
            @endif
        </table>

        <!-- Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Pegawai / Guru</th>
                    <th style="width: 110px;">NIP</th>
                    <th style="width: 120px;">Jabatan</th>
                    <th style="width: 70px;">Status</th>
                    <th>Keterangan</th>
                    <th style="width: 70px;">Paraf</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $index => $att)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td><strong>{{ $att->user->name ?? '-' }}</strong></td>
                        <td style="text-align: center;">{{ $att->user->nip ?? '-' }}</td>
                        <td>{{ $att->user->roles->first()?->name ?? 'Pegawai' }}</td>
                        <td style="text-align: center;">
                            @if($att->status === 'hadir')
                                <span class="badge badge-hadir">Hadir</span>
                            @elseif($att->status === 'izin')
                                <span class="badge badge-izin">Izin</span>
                            @elseif($att->status === 'sakit')
                                <span class="badge badge-sakit">Sakit</span>
                            @else
                                <span class="badge badge-alpa">Alpa</span>
                            @endif
                        </td>
                        <td>{{ $att->notes ?: '-' }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div style="margin-top: 12px; font-size: 8.5pt; color: #475569;">
            <strong>Ringkasan Kehadiran:</strong>
            Total Pegawai: {{ $attendances->count() }} Orang | 
            Hadir: {{ $attendances->where('status', 'hadir')->count() }} | 
            Izin: {{ $attendances->where('status', 'izin')->count() }} | 
            Sakit: {{ $attendances->where('status', 'sakit')->count() }} | 
            Alpa: {{ $attendances->where('status', 'alpa')->count() }} | 
            Persentase Kehadiran: {{ $attendances->count() > 0 ? round(($attendances->where('status', 'hadir')->count() / $attendances->count()) * 100, 1) : 0 }}%
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Pemateri Kajian,</div>
                <div class="signature-space"></div>
                <div style="font-weight: 700; text-decoration: underline;">{{ $session->speaker }}</div>
            </div>

            <div class="signature-box">
                <div>{{ $raportSettings['school_city'] }}, {{ $session->date->translatedFormat('d F Y') }}</div>
                <div>Kepala Sekolah,</div>
                <div class="signature-space"></div>
                <div style="font-weight: 700; text-decoration: underline;">{{ $raportSettings['principal_name'] }}</div>
                <div style="font-size: 8.5pt;">NIP. {{ $raportSettings['principal_nip'] }}</div>
            </div>
        </div>
    </div>

</body>
</html>
