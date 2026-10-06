<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syahadah Kenaikan Jilid - {{ $exam->student->user->name ?? 'Santri' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@600;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .cert-container {
            width: 297mm;
            height: 210mm;
            background: #fff;
            position: relative;
            padding: 13mm 14mm;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
        .outer-border {
            position: absolute;
            top: 7mm;
            left: 7mm;
            right: 7mm;
            bottom: 7mm;
            border: 3px double #d97706;
            pointer-events: none;
        }
        .inner-border {
            position: absolute;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 1px solid #059669;
            pointer-events: none;
        }
        .corner-ornament {
            position: absolute;
            width: 32px;
            height: 32px;
            border: 2px solid #d97706;
        }
        .corner-tl { top: 12mm; left: 12mm; border-right: none; border-bottom: none; }
        .corner-tr { top: 12mm; right: 12mm; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 12mm; left: 12mm; border-right: none; border-top: none; }
        .corner-br { bottom: 12mm; right: 12mm; border-left: none; border-top: none; }

        .bismillah {
            font-family: 'Amiri', serif;
            font-size: 26px;
            color: #065f46;
            text-align: center;
            margin-bottom: 2px;
        }
        .title-syahadah {
            font-family: 'Cinzel', serif;
            font-size: 27px;
            font-weight: 900;
            color: #065f46;
            text-align: center;
            letter-spacing: 2.5px;
            text-transform: uppercase;
        }
        .sub-title {
            font-family: 'Cinzel', serif;
            font-size: 13px;
            font-weight: 700;
            color: #b45309;
            text-align: center;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .cert-number {
            font-size: 11px;
            color: #64748b;
            text-align: center;
            font-family: monospace;
            margin-bottom: 14px;
        }
        .statement {
            font-size: 13px;
            color: #334155;
            text-align: center;
            line-height: 1.5;
            margin-bottom: 6px;
        }
        .student-name {
            font-family: 'Cinzel', serif;
            font-size: 25px;
            font-weight: 900;
            color: #047857;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #d97706;
            display: inline-block;
            padding: 0 30px 4px 30px;
            margin: 0 auto 8px auto;
        }
        .nisn-class {
            font-size: 12px;
            color: #475569;
            text-align: center;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .achievement-highlight {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border: 1px dashed #059669;
            border-radius: 10px;
            padding: 10px 20px;
            max-width: 680px;
            margin: 0 auto 14px auto;
            text-align: center;
        }
        .achievement-text {
            font-size: 13px;
            color: #1e293b;
            line-height: 1.5;
        }
        .target-jilid-badge {
            display: inline-block;
            background: #047857;
            color: white;
            font-weight: 800;
            padding: 2px 10px;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }
        .scores-grid {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-bottom: 14px;
        }
        .score-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 16px;
            text-align: center;
            min-width: 140px;
        }
        .score-box .label {
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .score-box .weight {
            font-size: 9px;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .score-box .val {
            font-size: 18px;
            font-weight: 900;
            color: #047857;
        }
        .predicate-banner {
            text-align: center;
            margin-bottom: 14px;
        }
        .predicate-badge {
            display: inline-block;
            background: linear-gradient(135deg, #059669, #0d9488);
            color: white;
            font-size: 13px;
            font-weight: 800;
            padding: 5px 22px;
            border-radius: 9999px;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .predicate-badge.perbaikan {
            background: linear-gradient(135deg, #d97706, #b45309);
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
            margin-top: 6px;
        }
        .sig-block {
            text-align: center;
            width: 220px;
        }
        .sig-title {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 46px;
        }
        .sig-name {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            border-bottom: 1px solid #94a3b8;
            padding-bottom: 2px;
        }
        .sig-role {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .print-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: #059669;
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 14px;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .print-btn:hover {
            background: #047857;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .cert-container {
                box-shadow: none;
                width: 100vw;
                height: 100vh;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">
        <span>🖨️ Cetak / Simpan PDF</span>
    </button>

    <div class="cert-container">
        <div class="outer-border"></div>
        <div class="inner-border"></div>
        <div class="corner-ornament corner-tl"></div>
        <div class="corner-ornament corner-tr"></div>
        <div class="corner-ornament corner-bl"></div>
        <div class="corner-ornament corner-br"></div>

        <div>
            <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
            <h1 class="title-syahadah">SYAHADAH KENAIKAN JILID TAHSIN</h1>
            <p class="sub-title">PEMBELAJARAN AL-QUR'AN &amp; TAHSINUL QIRA'AH</p>
            <p class="cert-number">NOMOR: {{ $exam->certificate_number }}</p>
        </div>

        <div style="text-align: center;">
            <p class="statement">
                Dewan Penguji Halaqah Al-Qur'an menerangkan dengan sebenarnya bahwa santri:
            </p>
            <h2 class="student-name">{{ $exam->student->user->name ?? 'Santri' }}</h2>
            <p class="nisn-class">
                NISN: {{ $exam->student->nisn ?? $exam->student->nis ?? '-' }} &bull; Kelas: {{ $exam->student->class->name ?? '-' }}
            </p>

            <div class="achievement-highlight">
                <p class="achievement-text">
                    Telah mengikuti <strong>Munaqasyah / Ujian Kenaikan {{ $exam->current_jilid }}</strong>
                    @if($exam->page_tested) (Halaman {{ $exam->page_tested }}) @endif
                    pada tanggal <strong>{{ $exam->exam_date->translatedFormat('d F Y') }}</strong> dan dinyatakan:
                    <br>
                    @if($exam->isLulus())
                        <span style="font-weight: 800; color: #047857; font-size: 14px;">LULUS</span> dengan hak melanjutkan ke pembelajaran 
                        <span class="target-jilid-badge">{{ strtoupper($exam->target_jilid) }}</span>
                    @else
                        <span style="font-weight: 800; color: #b45309; font-size: 14px;">PERBAIKAN</span> (Melanjutkan pemantapan {{ $exam->current_jilid }})
                    @endif
                </p>
            </div>

            <div class="scores-grid">
                <div class="score-box">
                    <div class="label">Makharijul Huruf</div>
                    <div class="weight">Bobot 35%</div>
                    <div class="val">{{ $exam->score_makhraj }}</div>
                </div>
                <div class="score-box">
                    <div class="label">Ketepatan Mad</div>
                    <div class="weight">Bobot 35%</div>
                    <div class="val">{{ $exam->score_mad }}</div>
                </div>
                <div class="score-box">
                    <div class="label">Kelancaran &amp; Adab</div>
                    <div class="weight">Bobot 30%</div>
                    <div class="val">{{ $exam->score_kelancaran }}</div>
                </div>
                <div class="score-box" style="background: #ecfdf5; border-color: #a7f3d0;">
                    <div class="label" style="color: #047857;">Nilai Akhir</div>
                    <div class="weight" style="color: #059669;">Rata-rata Terbobot</div>
                    <div class="val" style="color: #047857; font-size: 20px;">{{ $exam->score_final }}</div>
                </div>
            </div>

            <div class="predicate-banner">
                <span class="predicate-badge {{ $exam->isLulus() ? '' : 'perbaikan' }}">
                    PREDIKAT: {{ strtoupper($exam->predicate) }} ({{ $exam->isLulus() ? 'NAIK JILID' : 'PERBAIKAN' }})
                </span>
            </div>
        </div>

        <div class="signatures">
            <div class="sig-block">
                <p class="sig-title">Penguji / Musyrif Tahsin</p>
                <p class="sig-name">Ust. {{ $exam->teacher->name ?? 'Penguji' }}</p>
                <p class="sig-role">Guru Halaqah Al-Qur'an</p>
            </div>

            <div class="sig-block">
                <p class="sig-title">{{ config('app.school_city', 'Palu') }}, {{ $exam->exam_date->translatedFormat('d F Y') }}<br>Kepala Sekolah</p>
                <p class="sig-name">{{ \App\Models\Setting::get('school_principal', 'Kepala Sekolah') }}</p>
                <p class="sig-role">NIP. {{ \App\Models\Setting::get('school_principal_nip', '-') }}</p>
            </div>
        </div>
    </div>

</body>
</html>
