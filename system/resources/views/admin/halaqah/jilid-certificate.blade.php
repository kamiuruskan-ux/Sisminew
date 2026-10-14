<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Syahadah Kenaikan Jilid - {{ $exam->student->user->name ?? 'Santri' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
            background: #0f172a;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 30px 20px 80px 20px;
            color: #1e293b;
        }
        .cert-container {
            width: 297mm;
            height: 210mm;
            background: #ffffff;
            position: relative;
            padding: 9mm 15mm 9mm 15mm;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.45);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            border-radius: 4px;
        }

        /* Border Frames */
        .outer-border {
            position: absolute;
            top: 7mm;
            left: 7mm;
            right: 7mm;
            bottom: 7mm;
            border: 3px double #d97706;
            pointer-events: none;
            z-index: 2;
        }
        .inner-border {
            position: absolute;
            top: 9.5mm;
            left: 9.5mm;
            right: 9.5mm;
            bottom: 9.5mm;
            border: 1px solid #059669;
            pointer-events: none;
            z-index: 2;
        }

        /* Islamic Corner Ornaments */
        .corner-ornament {
            position: absolute;
            width: 24px;
            height: 24px;
            border: 2px solid #d97706;
            pointer-events: none;
            z-index: 3;
        }
        .corner-tl { top: 11mm; left: 11mm; border-right: none; border-bottom: none; }
        .corner-tr { top: 11mm; right: 11mm; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 11mm; left: 11mm; border-right: none; border-top: none; }
        .corner-br { bottom: 11mm; right: 11mm; border-left: none; border-top: none; }

        /* Subtle Watermark */
        .cert-watermark {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 280px;
            height: 280px;
            opacity: 0.038;
            pointer-events: none;
            z-index: 1;
            object-fit: contain;
        }

        .cert-content {
            position: relative;
            z-index: 5;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        /* School Identity Header */
        .school-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-top: 1mm;
            margin-bottom: 2px;
        }
        .school-logo {
            height: 42px;
            max-width: 70px;
            object-fit: contain;
        }
        .school-text {
            text-align: left;
        }
        .school-name {
            font-family: 'Cinzel', serif;
            font-size: 15px;
            font-weight: 800;
            color: #065f46;
            letter-spacing: 2.5px;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .school-unit {
            font-size: 8.5px;
            font-weight: 700;
            color: #b45309;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Gold Ornamental Divider */
        .gold-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 4px auto 4px auto;
            width: 320px;
        }
        .gold-divider .line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #d97706, transparent);
        }
        .gold-divider .symbol {
            color: #d97706;
            font-size: 10px;
        }

        .bismillah {
            font-family: 'Amiri', serif;
            font-size: 24px;
            color: #065f46;
            text-align: center;
            margin-bottom: 1px;
            line-height: 1.2;
        }
        .title-syahadah {
            font-family: 'Cinzel', serif;
            font-size: 23px;
            font-weight: 900;
            color: #065f46;
            text-align: center;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .sub-title {
            font-family: 'Cinzel', serif;
            font-size: 11.5px;
            font-weight: 700;
            color: #b45309;
            text-align: center;
            letter-spacing: 2px;
            margin-top: 2px;
            margin-bottom: 2px;
        }
        .cert-number {
            font-size: 10px;
            color: #64748b;
            text-align: center;
            font-family: monospace;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .statement {
            font-size: 11.5px;
            color: #334155;
            text-align: center;
            line-height: 1.5;
            margin-bottom: 3px;
        }
        .student-name {
            font-family: 'Cinzel', serif;
            font-size: 23px;
            font-weight: 900;
            color: #047857;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #d97706;
            display: inline-block;
            padding: 0 24px 2px 24px;
            margin: 0 auto 5px auto;
        }
        .nisn-class {
            font-size: 10.5px;
            color: #475569;
            text-align: center;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .achievement-highlight {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 6px 16px;
            display: inline-block;
            margin: 0 auto 8px auto;
            max-width: 680px;
        }
        .achievement-text {
            font-size: 11px;
            color: #166534;
            line-height: 1.5;
        }
        .target-jilid-badge {
            display: inline-block;
            background: #047857;
            color: white;
            font-weight: 800;
            font-size: 10px;
            padding: 2px 10px;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }

        .scores-grid {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 6px;
        }
        .score-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 5px 12px;
            text-align: center;
            min-width: 125px;
        }
        .score-box .label {
            font-size: 9px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .score-box .weight {
            font-size: 8px;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 1px;
        }
        .score-box .val {
            font-size: 16px;
            font-weight: 900;
            color: #047857;
        }

        .predicate-banner {
            text-align: center;
            margin-bottom: 6px;
        }
        .predicate-badge {
            display: inline-block;
            background: linear-gradient(135deg, #059669, #0d9488);
            color: white;
            font-size: 11.5px;
            font-weight: 800;
            padding: 4px 20px;
            border-radius: 9999px;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
        }
        .predicate-badge.perbaikan {
            background: linear-gradient(135deg, #d97706, #b45309);
        }

        /* Signature Section: Kepala Sekolah di Kiri, Penguji di Kanan */
        .signatures {
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
            margin-top: 2px;
            margin-bottom: 2mm;
        }
        .sig-block {
            text-align: center;
            width: 250px;
        }
        .sig-title {
            font-size: 10.5px;
            font-weight: 700;
            color: #334155;
            line-height: 1.4;
            min-height: 30px;
        }
        .sig-space {
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 2px auto;
        }
        .sig-image {
            max-height: 54px;
            max-width: 150px;
            object-fit: contain;
            mix-blend-mode: multiply;
            user-select: none;
            pointer-events: none;
        }
        .sig-name {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            border-bottom: 1px solid #94a3b8;
            padding-bottom: 2px;
            display: inline-block;
            min-width: 170px;
        }
        .sig-role {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
            font-weight: 500;
        }

        /* Floating Action Bar */
        .action-bar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.94);
            backdrop-filter: blur(12px);
            padding: 10px 20px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 30px;
            font-size: 12.5px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-print {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.45);
        }
        .btn-signature {
            background: linear-gradient(135deg, #d97706, #f59e0b);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
        }
        .btn-signature:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.45);
        }
        .toggle-group {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 8px;
            border-left: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
            font-size: 11px;
            font-weight: 600;
        }
        .toggle-group label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            user-select: none;
        }
        .toggle-group input[type="checkbox"] {
            accent-color: #10b981;
            width: 14px;
            height: 14px;
            cursor: pointer;
        }

        /* Modal Settings Signatures */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(6px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-card {
            background: #ffffff;
            border-radius: 20px;
            width: 100%;
            max-width: 680px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            padding: 24px;
            position: relative;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 14px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 18px;
        }
        .modal-title {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .modal-close {
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            color: #64748b;
            font-weight: bold;
            transition: all 0.2s;
        }
        .modal-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .sig-editor-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }
        .sig-tab-btn {
            flex: 1;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }
        .sig-tab-btn.active {
            background: #ecfdf5;
            border-color: #10b981;
            color: #047857;
        }

        .sig-editor-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .canvas-container {
            position: relative;
            background: #ffffff;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
            touch-action: none;
            height: 140px;
        }
        .canvas-container canvas {
            display: block;
            width: 100%;
            height: 100%;
            cursor: crosshair;
        }
        .canvas-tools {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }

        /* Print Mode */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
            }
            .cert-container {
                box-shadow: none !important;
                width: 100vw !important;
                height: 100vh !important;
                border-radius: 0 !important;
                padding: 9mm 15mm !important;
            }
            .action-bar, .modal-overlay {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Toolbar (Hanya Tampil di Layar, Tersembunyi saat Cetak) -->
    <div class="action-bar">
        <button class="btn-action btn-print" onclick="window.print()">
            🖨️ Cetak / Simpan PDF
        </button>

        <button class="btn-action btn-signature" onclick="openSignatureModal()">
            ✍️ Kelola Tanda Tangan
        </button>

        <div class="toggle-group">
            <label title="Centang untuk menampilkan tanda tangan Kepala Sekolah">
                <input type="checkbox" id="chkShowPrincipal" checked onchange="toggleSignature('principal', this.checked)">
                TTD Kepsek
            </label>
            <label title="Centang untuk menampilkan tanda tangan Penguji">
                <input type="checkbox" id="chkShowExaminer" checked onchange="toggleSignature('examiner', this.checked)">
                TTD Penguji
            </label>
        </div>
    </div>

    <!-- Sertifikat Utama (A4 Landscape) -->
    <div class="cert-container" id="syahadahCertificate">
        <!-- Borders & Frame Ornaments -->
        <div class="outer-border"></div>
        <div class="inner-border"></div>
        <div class="corner-ornament corner-tl"></div>
        <div class="corner-ornament corner-tr"></div>
        <div class="corner-ornament corner-bl"></div>
        <div class="corner-ornament corner-br"></div>

        <!-- Subtle Watermark Logo Sekolah -->
        <img src="{{ $schoolLogoUrl }}" alt="Watermark" class="cert-watermark">

        <div class="cert-content">
            <!-- Bagian Atas: Logo & Nama Sekolah, Bismillah, Judul Syahadah -->
            <div>
                <!-- Identitas Sekolah (Logo & Nama) -->
                <div class="school-header">
                    <img src="{{ $schoolLogoUrl }}" alt="Logo Sekolah" class="school-logo">
                    <div class="school-text">
                        <h3 class="school-name">{{ $schoolName }}</h3>
                        <p class="school-unit">LEMBAGA PENDIDIKAN &amp; PEMBELAJARAN AL-QUR'AN &bull; TAHSINUL QIRA'AH</p>
                    </div>
                </div>

                <!-- Pembatas Emas Ornamen -->
                <div class="gold-divider">
                    <span class="line"></span>
                    <span class="symbol">❖</span>
                    <span class="line"></span>
                </div>

                <!-- Bismillah & Judul Syahadah -->
                <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                <h1 class="title-syahadah">SYAHADAH KENAIKAN JILID TAHSIN</h1>
                <p class="sub-title">PEMBELAJARAN AL-QUR'AN &amp; TAHSINUL QIRA'AH</p>
                <p class="cert-number">NOMOR: {{ $exam->certificate_number }}</p>
            </div>

            <!-- Bagian Tengah: Data Santri, Pencapaian Kenaikan Jilid & Nilai -->
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
                            <span style="font-weight: 800; color: #047857; font-size: 13px;">LULUS</span> dengan hak melanjutkan ke pembelajaran 
                            <span class="target-jilid-badge">{{ strtoupper($exam->target_jilid) }}</span>
                        @else
                            <span style="font-weight: 800; color: #b45309; font-size: 13px;">PERBAIKAN</span> (Melanjutkan pemantapan {{ $exam->current_jilid }})
                        @endif
                    </p>
                </div>

                <!-- Grid Komponen Nilai Kenaikan Jilid -->
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
                        <div class="val" style="color: #047857; font-size: 18px;">{{ $exam->score_final }}</div>
                    </div>
                </div>

                <div class="predicate-banner">
                    <span class="predicate-badge {{ $exam->isLulus() ? '' : 'perbaikan' }}">
                        PREDIKAT: {{ strtoupper($exam->predicate) }} ({{ $exam->isLulus() ? 'NAIK JILID' : 'PERBAIKAN' }})
                    </span>
                </div>
            </div>

            <!-- Bagian Bawah: Tanda Tangan (Kepala Sekolah di Kiri, Penguji di Kanan) -->
            <div class="signatures">
                <!-- SISI KIRI: Kepala Sekolah -->
                <div class="sig-block">
                    <p class="sig-title">
                        Mengetahui,<br>
                        {{ $principalTitle }}
                    </p>
                    <div class="sig-space" id="principalSigBox">
                        @if(!empty($principalSigUrl))
                            <img src="{{ $principalSigUrl }}" class="sig-image" id="principalSigImg" alt="TTD Kepala Sekolah">
                        @else
                            <img src="" class="sig-image" id="principalSigImg" alt="TTD Kepala Sekolah" style="display: none;">
                        @endif
                    </div>
                    <p class="sig-name">{{ $principalName }}</p>
                    @if(!empty($principalNip) && $principalNip !== '0' && $principalNip !== '-')
                        <p class="sig-role">NIP. {{ $principalNip }}</p>
                    @endif
                </div>

                <!-- SISI KANAN: Penguji Tahsin & Tanggal Kota -->
                <div class="sig-block">
                    <p class="sig-title">
                        {{ $schoolCity }}, {{ $exam->exam_date->translatedFormat('d F Y') }}<br>
                        Penguji / Musyrif Tahsin
                    </p>
                    <div class="sig-space" id="examinerSigBox">
                        @if(!empty($examinerSigUrl))
                            <img src="{{ $examinerSigUrl }}" class="sig-image" id="examinerSigImg" alt="TTD Penguji">
                        @else
                            <img src="" class="sig-image" id="examinerSigImg" alt="TTD Penguji" style="display: none;">
                        @endif
                    </div>
                    <p class="sig-name">Ust. {{ $exam->teacher->name ?? 'Penguji' }}</p>
                    <p class="sig-role">Guru Halaqah Al-Qur'an</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kelola & Tambah Tanda Tangan -->
    <div class="modal-overlay" id="sigModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title">
                    <span>✍️</span> Pengaturan Tanda Tangan Syahadah
                </h3>
                <button type="button" class="modal-close" onclick="closeSignatureModal()">&times;</button>
            </div>

            <!-- Tab Pemilihan Pihak: Penguji vs Kepala Sekolah -->
            <div class="sig-editor-tabs">
                <button type="button" class="sig-tab-btn active" id="tabBtnExaminer" onclick="switchSigTab('examiner')">
                    Tanda Tangan Penguji (Ust. {{ $exam->teacher->name ?? 'Penguji' }})
                </button>
                <button type="button" class="sig-tab-btn" id="tabBtnPrincipal" onclick="switchSigTab('principal')">
                    Tanda Tangan Kepala Sekolah ({{ $principalName }})
                </button>
            </div>

            <div class="sig-editor-section">
                <!-- Info Status TTD Saat Ini -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div>
                        <h4 style="font-size: 13px; font-weight: 800; color: #1e293b;" id="sigSectionTitle">
                            Tanda Tangan Penguji
                        </h4>
                        <p style="font-size: 11px; color: #64748b;" id="sigSectionSub">
                            Unggah gambar transparan (PNG) atau goreskan tanda tangan langsung di bawah.
                        </p>
                    </div>
                    <button type="button" id="btnDeleteSig" onclick="deleteCurrentSignature()" style="font-size: 11px; color: #dc2626; background: #fee2e2; border: none; padding: 5px 12px; border-radius: 8px; font-weight: 700; cursor: pointer;">
                        🗑️ Hapus TTD
                    </button>
                </div>

                <!-- Opsi 1: Tanda Tangan Langsung di Layar (Canvas Pad) -->
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">
                        Goreskan Tanda Tangan Langsung (Touchscreen / Mouse):
                    </label>
                    <div class="canvas-container">
                        <canvas id="sigCanvas"></canvas>
                    </div>
                    <div class="canvas-tools">
                        <button type="button" onclick="clearCanvas()" style="font-size: 11px; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            Hapus Goresan
                        </button>
                        <span style="font-size: 10px; color: #94a3b8;">Gunakan jari/stylus di HP/tablet atau klik & tarik mouse.</span>
                    </div>
                </div>

                <!-- Opsi 2: Upload File Tanda Tangan PNG -->
                <div style="padding-top: 10px; border-top: 1px dashed #cbd5e1;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">
                        Atau Upload Gambar Tanda Tangan (PNG Transparan / JPG):
                    </label>
                    <input type="file" id="sigFileInput" accept="image/png,image/jpeg,image/webp,image/svg+xml" style="font-size: 12px; width: 100%;">
                </div>

                <!-- Pilihan Simpan Default -->
                <div style="margin-top: 14px; padding-top: 10px; border-top: 1px solid #e2e8f0;">
                    <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 11.5px; color: #334155; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" id="chkSaveDefault" checked>
                        <span>Jadikan tanda tangan default untuk sertifikat syahadah lainnya</span>
                    </label>
                </div>
            </div>

            <!-- Tombol Aksi Simpan -->
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeSignatureModal()" style="padding: 9px 18px; border-radius: 10px; border: 1px solid #cbd5e1; background: white; color: #475569; font-size: 12px; font-weight: 700; cursor: pointer;">
                    Batal
                </button>
                <button type="button" id="btnSaveSig" onclick="saveSignature()" style="padding: 9px 22px; border-radius: 10px; border: none; background: #059669; color: white; font-size: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);">
                    Simpan &amp; Terapkan
                </button>
            </div>
        </div>
    </div>

    <script>
        const EXAM_ID = {{ $exam->id }};
        const EXAM_TYPE = 'jilid';
        const UPLOAD_URL = "{{ route('admin.halaqah.certificate.upload-signature') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let currentTab = 'examiner'; // 'examiner' or 'principal'
        let canvas, ctx;
        let isDrawing = false;
        let hasDrawn = false;

        document.addEventListener('DOMContentLoaded', function () {
            initCanvas();
        });

        function toggleSignature(type, isVisible) {
            const imgId = type === 'principal' ? 'principalSigImg' : 'examinerSigImg';
            const img = document.getElementById(imgId);
            if (img && img.getAttribute('src')) {
                img.style.visibility = isVisible ? 'visible' : 'hidden';
            }
        }

        function openSignatureModal() {
            document.getElementById('sigModal').classList.add('active');
            switchSigTab('examiner');
            setTimeout(resizeCanvas, 50);
        }

        function closeSignatureModal() {
            document.getElementById('sigModal').classList.remove('active');
        }

        function switchSigTab(type) {
            currentTab = type;
            const btnEx = document.getElementById('tabBtnExaminer');
            const btnPr = document.getElementById('tabBtnPrincipal');
            const title = document.getElementById('sigSectionTitle');
            const sub = document.getElementById('sigSectionSub');

            if (type === 'examiner') {
                btnEx.classList.add('active');
                btnPr.classList.remove('active');
                title.innerText = "Tanda Tangan Penguji (Ust. {{ $exam->teacher->name ?? 'Penguji' }})";
                sub.innerText = "Tanda tangan ini akan muncul di kolom kanan penguji.";
            } else {
                btnPr.classList.add('active');
                btnEx.classList.remove('active');
                title.innerText = "Tanda Tangan Kepala Sekolah ({{ $principalName }})";
                sub.innerText = "Tanda tangan ini akan muncul di kolom kiri Kepala Sekolah.";
            }
            clearCanvas();
            document.getElementById('sigFileInput').value = '';
        }

        function initCanvas() {
            canvas = document.getElementById('sigCanvas');
            ctx = canvas.getContext('2d');

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: (clientX - rect.left) * (canvas.width / rect.width),
                    y: (clientY - rect.top) * (canvas.height / rect.height)
                };
            }

            function startDraw(e) {
                e.preventDefault();
                isDrawing = true;
                hasDrawn = true;
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            }

            function draw(e) {
                if (!isDrawing) return;
                e.preventDefault();
                const pos = getPos(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.stroke();
            }

            function stopDraw(e) {
                if (isDrawing) {
                    isDrawing = false;
                }
            }

            canvas.addEventListener('mousedown', startDraw);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDraw);
            canvas.addEventListener('mouseleave', stopDraw);

            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            canvas.addEventListener('touchend', stopDraw);

            window.addEventListener('resize', resizeCanvas);
        }

        function resizeCanvas() {
            if (!canvas) return;
            const container = canvas.parentElement;
            canvas.width = container.clientWidth * 2;
            canvas.height = container.clientHeight * 2;
            ctx.scale(1, 1);
        }

        function clearCanvas() {
            if (!ctx) return;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawn = false;
        }

        function saveSignature() {
            const fileInput = document.getElementById('sigFileInput');
            const saveDefault = document.getElementById('chkSaveDefault').checked;
            const btnSave = document.getElementById('btnSaveSig');

            const formData = new FormData();
            formData.append('_token', CSRF_TOKEN);
            formData.append('type', currentTab);
            formData.append('exam_id', EXAM_ID);
            formData.append('exam_type', EXAM_TYPE);
            formData.append('save_as_default', saveDefault ? '1' : '0');

            if (fileInput.files.length > 0) {
                formData.append('signature_file', fileInput.files[0]);
            } else if (hasDrawn) {
                const dataUrl = canvas.toDataURL('image/png');
                formData.append('signature_base64', dataUrl);
            } else {
                alert('Silakan goreskan tanda tangan pada area canvas atau pilih file gambar tanda tangan terlebih dahulu.');
                return;
            }

            btnSave.disabled = true;
            btnSave.innerText = 'Menyimpan...';

            fetch(UPLOAD_URL, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnSave.disabled = false;
                btnSave.innerText = 'Simpan & Terapkan';

                if (data.success) {
                    const imgId = currentTab === 'principal' ? 'principalSigImg' : 'examinerSigImg';
                    const img = document.getElementById(imgId);
                    img.src = data.url;
                    img.style.display = 'block';
                    img.style.visibility = 'visible';

                    if (currentTab === 'principal') {
                        document.getElementById('chkShowPrincipal').checked = true;
                    } else {
                        document.getElementById('chkShowExaminer').checked = true;
                    }

                    closeSignatureModal();
                    alert(data.message || 'Tanda tangan berhasil diterapkan!');
                } else {
                    alert(data.message || 'Terjadi kesalahan saat menyimpan.');
                }
            })
            .catch(err => {
                btnSave.disabled = false;
                btnSave.innerText = 'Simpan & Terapkan';
                console.error(err);
                alert('Gagal mengirim data tanda tangan. Silakan coba lagi.');
            });
        }

        function deleteCurrentSignature() {
            if (!confirm(`Hapus tanda tangan ${currentTab === 'principal' ? 'Kepala Sekolah' : 'Penguji'} dari sertifikat ini?`)) {
                return;
            }

            const saveDefault = document.getElementById('chkSaveDefault').checked;
            const formData = new FormData();
            formData.append('_token', CSRF_TOKEN);
            formData.append('type', currentTab);
            formData.append('exam_id', EXAM_ID);
            formData.append('exam_type', EXAM_TYPE);
            formData.append('delete', '1');
            formData.append('save_as_default', saveDefault ? '1' : '0');

            fetch(UPLOAD_URL, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const imgId = currentTab === 'principal' ? 'principalSigImg' : 'examinerSigImg';
                    const img = document.getElementById(imgId);
                    img.src = '';
                    img.style.display = 'none';

                    closeSignatureModal();
                    alert(data.message || 'Tanda tangan berhasil dihapus.');
                } else {
                    alert(data.message || 'Gagal menghapus tanda tangan.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memproses penghapusan tanda tangan.');
            });
        }
    </script>
</body>
</html>
