<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Kinerja Guru & Pegawai - {{ $user->name }} - {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 9.5pt;
            color: #0f172a;
            background-color: #f1f5f9;
            line-height: 1.35;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 15mm;
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
            margin-bottom: 10px;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
        }
        .kop-logo { width: 75px; text-align: center; vertical-align: middle; }
        .kop-logo img { width: 70px; max-height: 70px; object-fit: contain; }
        .kop-text { text-align: center; vertical-align: middle; padding: 0 10px; }
        .kop-foundation { font-size: 11pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-title { font-size: 14pt; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #1e293b; }
        .kop-sub { font-size: 8pt; color: #475569; margin-top: 2px; }

        /* Document Title */
        .report-title {
            text-align: center;
            font-size: 11pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 10px 0 8px 0;
            color: #0f172a;
        }

        /* Identity Box */
        .identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        .identity-table td { padding: 2.5px 4px; vertical-align: top; }
        .identity-table td.label { width: 130px; font-weight: 700; color: #334155; }
        .identity-table td.sep { width: 10px; text-align: center; }

        /* Summary Score Box */
        .summary-box {
            display: flex;
            border: 1.5px solid #0f172a;
            border-radius: 8px;
            margin-bottom: 12px;
            overflow: hidden;
            background: #f8fafc;
        }
        .summary-score {
            width: 140px;
            background: #0f172a;
            color: #fff;
            padding: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .summary-score .big-num { font-size: 20pt; font-weight: 800; color: #fde047; }
        .summary-score .pred-badge { font-size: 9pt; font-weight: 800; background: #10b981; color: #fff; padding: 2px 8px; border-radius: 4px; margin-top: 3px; }
        .summary-pillars {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            padding: 8px;
            gap: 6px;
            text-align: center;
            font-size: 8pt;
        }
        .pillar-card {
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 4px;
        }
        .pillar-card .p-title { font-size: 7pt; font-weight: 700; color: #64748b; text-transform: uppercase; }
        .pillar-card .p-score { font-size: 11pt; font-weight: 800; color: #1e293b; margin-top: 2px; }
        .pillar-card .p-weight { font-size: 6.5pt; color: #94a3b8; }

        /* Detail Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 8pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #94a3b8;
            padding: 4px 6px;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: 800;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .pillar-row {
            background-color: #e2e8f0;
            font-weight: 800;
            font-size: 8pt;
            color: #0f172a;
        }

        /* Feedback Boxes */
        .feedback-container {
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 8pt;
        }
        .feedback-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 8px;
            background: #f8fafc;
        }
        .feedback-title { font-weight: 800; color: #1e293b; text-transform: uppercase; font-size: 7.5pt; margin-bottom: 3px; }

        /* Signatures */
        .signature-section {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
            font-size: 8.5pt;
        }
        .signature-box {
            width: 220px;
            text-align: center;
        }
        .signature-space { height: 50px; }
        .signature-name { font-weight: 800; text-decoration: underline; }
        .signature-nip { font-size: 7.5pt; color: #475569; }
    </style>
</head>
<body>

    <!-- Action Bar for Screen -->
    <div class="action-bar no-print">
        <a href="{{ route('admin.kpi.raport', ['userId' => $user->id, 'year' => $year, 'month' => $month]) }}" class="action-btn btn-back">
            Kembali
        </a>
        <button onclick="window.print()" class="action-btn btn-print">
            Cetak PDF / Print (A4)
        </button>
    </div>

    <div class="page">
        <!-- Kop Surat -->
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="Logo">
                </td>
                <td class="kop-text">
                    <div class="kop-foundation">{{ \App\Models\Setting::get('foundation_name', 'YAYASAN PENDIDIKAN ISLAM TERPADU') }}</div>
                    <div class="kop-title">{{ \App\Models\Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}</div>
                    <div class="kop-sub">
                        {{ \App\Models\Setting::get('school_address', 'Jl. Trans Sulawesi No. 88, Kota Palu, Sulawesi Tengah') }} 
                        • Telp: {{ \App\Models\Setting::get('school_phone', '(0451) 481234') }} 
                        • Email: {{ \App\Models\Setting::get('school_email', 'info@sekolah.sch.id') }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Document Title -->
        <div class="report-title">
            RAPOR PENILAIAN KINERJA GURU &amp; PEGAWAI (KPI)
        </div>

        <!-- Identity Details -->
        <table class="identity-table">
            <tr>
                <td class="label">Nama Pegawai</td>
                <td class="sep">:</td>
                <td style="font-weight: 800;">{{ $user->name }}</td>
                <td class="label">Periode Penilaian</td>
                <td class="sep">:</td>
                <td style="font-weight: 800;">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</td>
            </tr>
            <tr>
                <td class="label">NIP / ID Pegawai</td>
                <td class="sep">:</td>
                <td>{{ $user->nip ?? '-' }}</td>
                <td class="label">Tingkat Kehadiran</td>
                <td class="sep">:</td>
                <td>
                    <strong>{{ $kpi['attendance_percentage'] }}%</strong> 
                    ({{ $kpi['gate_passed'] ? 'Memenuhi Gate Minimum >= 85%' : 'Di Bawah Gate Minimum' }})
                </td>
            </tr>
            <tr>
                <td class="label">Jabatan / Amanah</td>
                <td class="sep">:</td>
                <td>{{ $user->jabatan ?? ($user->roles->first()->name ?? 'Guru') }}</td>
                <td class="label">Hari Kerja Efektif</td>
                <td class="sep">:</td>
                <td>{{ $kpi['effective_workdays'] }} hari kerja ({{ $kpi['excused_days'] }} hari izin resmi/cuti)</td>
            </tr>
        </table>

        <!-- Summary Score & 5 Pillars Box -->
        <div class="summary-box">
            <div class="summary-score">
                <span style="font-size: 7pt; text-transform: uppercase; font-weight: 700; color: #cbd5e1;">Skor Akhir KPI</span>
                <div class="big-num">{{ $kpi['final_score'] }}</div>
                <div class="pred-badge">PREDIKAT {{ $kpi['predicate'] }}</div>
                <span style="font-size: 6.5pt; margin-top: 2px; color: #94a3b8;">{{ $kpi['predicate_label'] }}</span>
            </div>
            <div class="summary-pillars">
                <div class="pillar-card">
                    <div class="p-title">1. Disiplin</div>
                    <div class="p-score">{{ $kpi['score_comp_1'] }}</div>
                    <div class="p-weight">Bobot: {{ $kpi['weights']['comp_1'] }}%</div>
                </div>
                <div class="pillar-card">
                    <div class="p-title">2. Pedagogik</div>
                    <div class="p-score">{{ $kpi['score_comp_2'] }}</div>
                    <div class="p-weight">Bobot: {{ $kpi['weights']['comp_2'] }}%</div>
                </div>
                <div class="pillar-card">
                    <div class="p-title">3. Profesional</div>
                    <div class="p-score">{{ $kpi['score_comp_3'] }}</div>
                    <div class="p-weight">Bobot: {{ $kpi['weights']['comp_3'] }}%</div>
                </div>
                <div class="pillar-card">
                    <div class="p-title">4. Tarbiyah</div>
                    <div class="p-score">{{ $kpi['score_comp_4'] }}</div>
                    <div class="p-weight">Bobot: {{ $kpi['weights']['comp_4'] }}%</div>
                </div>
                <div class="pillar-card">
                    <div class="p-title">5. Sosial</div>
                    <div class="p-score">{{ $kpi['score_comp_5'] }}</div>
                    <div class="p-weight">Bobot: {{ $kpi['weights']['comp_5'] }}%</div>
                </div>
            </div>
        </div>

        <!-- Detail Table of 23 Indicators -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 35px;">Kode</th>
                    <th>Indikator Penilaian Standar Rapor Terpadu</th>
                    <th style="width: 75px;">Tipe</th>
                    <th>Sumber Bukti &amp; Data Audit</th>
                    <th style="width: 50px;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($definitions as $pillarKey => $pillar)
                    <tr class="pillar-row">
                        <td colspan="4">{{ strtoupper($pillar['name']) }} (BOBOT: {{ $kpi['weights'][$pillarKey] }}%)</td>
                        <td style="text-align: center; font-weight: 800;">{{ $kpi['score_' . $pillarKey] }}</td>
                    </tr>
                    @foreach($pillar['indicators'] as $indCode => $indDef)
                    <tr>
                        <td style="text-align: center; font-weight: 700;">{{ $indCode }}</td>
                        <td>
                            <strong>{{ $indDef['name'] }}</strong>
                        </td>
                        <td style="text-align: center; font-size: 7pt;">
                            @if($indDef['type'] === 'auto')
                                [Sistem]
                            @elseif($indDef['type'] === 'hybrid')
                                [Survei/Log]
                            @else
                                [Supervisi]
                            @endif
                        </td>
                        <td style="color: #334155; font-size: 7.5pt;">
                            {{ $kpi['items'][$indCode]['source_detail'] ?? ($kpi['items'][$indCode]['notes'] ?? 'Terverifikasi sesuai standar operasional') }}
                        </td>
                        <td style="text-align: center; font-weight: 800;">
                            {{ $kpi['items'][$indCode]['score'] ?? 0 }}
                        </td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <!-- Qualitative Feedback -->
        <div class="feedback-container">
            <div class="feedback-box">
                <div class="feedback-title">Apresiasi &amp; Keunggulan Pendidik:</div>
                <p>{{ $kpi['feedback_appreciation'] }}</p>
            </div>
            <div class="feedback-box">
                <div class="feedback-title">Rekomendasi Perbaikan &amp; Pembinaan:</div>
                <p>{{ $kpi['feedback_improvement'] }}</p>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Guru / Pegawai Yang Dinilai,</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $user->name }}</p>
                <p class="signature-nip">NIP: {{ $user->nip ?? '-' }}</p>
            </div>

            <div class="signature-box">
                <p>{{ \App\Models\Setting::get('school_city', 'Palu') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p>Kepala Sekolah / Penilai,</p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $principal ? $principal->name : 'Kepala Sekolah' }}</p>
                <p class="signature-nip">NIP: {{ $principal->nip ?? '-' }}</p>
            </div>
        </div>
    </div>

</body>
</html>
