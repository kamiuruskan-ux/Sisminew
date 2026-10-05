<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syahadah Tasmi' - {{ $exam->student->user->name ?? 'Santri' }}</title>
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
            padding: 14mm;
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
            border: 1px solid #b45309;
            pointer-events: none;
        }
        .bismillah {
            font-family: 'Amiri', serif;
            font-size: 26px;
            color: #065f46;
            text-align: center;
            margin-bottom: 4px;
        }
        .title-syahadah {
            font-family: 'Cinzel', serif;
            font-size: 28px;
            font-weight: 900;
            color: #065f46;
            text-align: center;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .sub-title {
            font-family: 'Cinzel', serif;
            font-size: 14px;
            font-weight: 700;
            color: #b45309;
            text-align: center;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }
        .cert-number {
            font-size: 11px;
            color: #64748b;
            text-align: center;
            font-family: monospace;
            margin-bottom: 16px;
        }
        .statement {
            font-size: 13px;
            color: #334155;
            text-align: center;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        .student-name {
            font-family: 'Cinzel', serif;
            font-size: 26px;
            font-weight: 900;
            color: #047857;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #d97706;
            display: inline-block;
            padding: 0 30px 4px 30px;
            margin: 0 auto 10px auto;
        }
        .nisn-class {
            font-size: 12px;
            color: #475569;
            text-align: center;
            font-weight: 600;
            margin-bottom: 14px;
        }
        .scores-grid {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 16px;
        }
        .score-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 18px;
            text-align: center;
        }
        .score-box .label {
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .score-box .val {
            font-size: 18px;
            font-weight: 900;
            color: #047857;
        }
        .predicate-banner {
            text-align: center;
            margin-bottom: 16px;
        }
        .predicate-badge {
            display: inline-block;
            background: linear-gradient(135deg, #059669, #0d9488);
            color: white;
            font-size: 14px;
            font-weight: 800;
            padding: 6px 24px;
            border-radius: 9999px;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
            margin-top: 10px;
        }
        .sig-block {
            text-align: center;
            width: 220px;
        }
        .sig-title {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 50px;
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

    <button class="print-btn" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>

    <div class="cert-container">
        <div class="outer-border"></div>
        <div class="inner-border"></div>

        <div>
            <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
            <h1 class="title-syahadah">SYAHADAH TASMI' AL-QUR'AN</h1>
            <p class="sub-title">1 JUZ SEKALI DUDUK (BIL-GHOIB)</p>
            <p class="cert-number">NOMOR: {{ $exam->certificate_number }}</p>
        </div>

        <div style="text-align: center;">
            <p class="statement">
                Dewan Penguji Halaqah Al-Qur'an menerangkan bahwa santri:
            </p>
            <h2 class="student-name">{{ $exam->student->user->name ?? 'Santri' }}</h2>
            <p class="nisn-class">
                NISN: {{ $exam->student->nisn ?? $exam->student->nis ?? '-' }} &bull; Kelas: {{ $exam->student->class->name ?? '-' }}
            </p>

            <p class="statement" style="max-w: 600px; margin: 0 auto 12px auto;">
                Telah berhasil menyelesaikan Ujian Tasmi' Al-Qur'an bil-ghoib (hafalan tanpa melihat mushaf) untuk materi:
                <br>
                <strong style="color: #047857; font-size: 15px;">{{ strtoupper($exam->juz_tested) }}</strong>
                @if($exam->surah_range) ({{ $exam->surah_range }}) @endif
                pada tanggal {{ $exam->exam_date->translatedFormat('d F Y') }}.
            </p>

            <div class="scores-grid">
                <div class="score-box">
                    <div class="label">Tajwid &amp; Makhraj</div>
                    <div class="val">{{ $exam->score_tajwid }}</div>
                </div>
                <div class="score-box">
                    <div class="label">Kelancaran</div>
                    <div class="val">{{ $exam->score_kelancaran }}</div>
                </div>
                <div class="score-box">
                    <div class="label">Fashohah &amp; Adab</div>
                    <div class="val">{{ $exam->score_fashohah }}</div>
                </div>
                <div class="score-box" style="background: #ecfdf5; border-color: #a7f3d0;">
                    <div class="label" style="color: #047857;">Nilai Akhir</div>
                    <div class="val" style="color: #047857; font-size: 20px;">{{ $exam->score_final }}</div>
                </div>
            </div>

            <div class="predicate-banner">
                <span class="predicate-badge">PREDIKAT: {{ strtoupper($exam->predicate) }}</span>
            </div>
        </div>

        <div class="signatures">
            <div class="sig-block">
                <p class="sig-title">Musyrif / Penguji Tasmi'</p>
                <p class="sig-name">Ust. {{ $exam->teacher->name ?? 'Penguji' }}</p>
                <p class="sig-role">Guru Halaqah Al-Qur'an</p>
            </div>

            <div class="sig-block">
                <p class="sig-title">{{ config('app.school_city', 'Indonesia') }}, {{ $exam->exam_date->translatedFormat('d F Y') }}<br>Kepala Sekolah</p>
                <p class="sig-name">{{ \App\Models\Setting::get('school_principal', 'Kepala Sekolah') }}</p>
                <p class="sig-role">NIP. {{ \App\Models\Setting::get('school_principal_nip', '-') }}</p>
            </div>
        </div>
    </div>

</body>
</html>
