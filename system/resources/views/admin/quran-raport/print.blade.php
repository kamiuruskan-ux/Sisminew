<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Raport Pembelajaran Al-Qur'an - {{ $raportSettings['school_name'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 11pt;
            color: #1e293b;
            background-color: #e2e8f0;
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
        .page-break {
            page-break-after: always;
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
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }
        .action-btn {
            background: #059669;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .action-btn:hover {
            background: #047857;
        }
        .btn-close {
            background: #334155;
        }
        .btn-close:hover {
            background: #475569;
        }

        /* Kop Surat */
        .kop-header {
            display: flex;
            align-items: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 10px;
            margin-bottom: 12px;
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
            color: #047857;
            margin-bottom: 1px;
        }
        .kop-details h1 {
            font-size: 13.5pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #020617;
            font-family: Georgia, "Times New Roman", serif;
            margin-bottom: 2px;
        }
        .kop-details p {
            font-size: 8pt;
            color: #334155;
        }

        /* Basmalah */
        .basmalah {
            text-align: center;
            font-family: 'Amiri', serif;
            font-size: 14pt;
            color: #047857;
            margin: 4px 0 10px 0;
        }

        /* Raport Title */
        .raport-title {
            text-align: center;
            margin-bottom: 14px;
        }
        .raport-title h3 {
            font-size: 12.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .raport-title p {
            font-size: 9pt;
            color: #64748b;
            font-weight: 600;
        }

        /* Biodata Grid */
        .student-info {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .student-info td {
            padding: 2.5px 4px;
            vertical-align: top;
        }
        .student-info .label {
            width: 17%;
            font-weight: 600;
            color: #475569;
        }
        .student-info .colon {
            width: 2%;
            text-align: center;
            font-weight: bold;
        }
        .student-info .value {
            width: 31%;
            font-weight: 700;
            color: #0f172a;
        }

        /* Section Header */
        .section-header {
            font-size: 9pt;
            font-weight: 800;
            color: #065f46;
            background: #ecfdf5;
            padding: 5px 10px;
            border-left: 4px solid #059669;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5.5px 8px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        /* Grid for Attendance & Notes */
        .info-grid {
            display: flex;
            gap: 12px;
            margin-bottom: 14px;
        }
        .attendance-box {
            width: 45%;
        }
        .notes-box {
            width: 55%;
        }
        .box-container {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 12px;
            background: #fafafa;
            min-height: 80px;
            font-size: 8.5pt;
        }

        /* Signatures */
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
            font-size: 9pt;
        }
        .sig-block {
            width: 30%;
            text-align: center;
            position: relative;
        }
        .sig-space {
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sig-name {
            font-weight: 800;
            text-decoration: underline;
            color: #0f172a;
        }
        .sig-title {
            font-size: 8.5pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    <div class="action-bar no-print">
        <button onclick="window.print()" class="action-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" class="action-btn btn-close">
            Tutup
        </button>
    </div>

    @foreach($raportData as $data)
    @php
        $student = $data['student'];
    @endphp
    <div class="page {{ !$loop->last ? 'page-break' : '' }}">
        
        {{-- Kop Lembaga --}}
        <div class="kop-header">
            <div class="kop-logo-box">
                @php
                    $schoolLogo = \App\Models\Setting::getLogoUrl();
                @endphp
                <img src="{{ $schoolLogo }}" class="kop-logo" alt="Logo">
            </div>
            <div class="kop-details">
                <p class="kop-header-top">LEMBAGA PENDIDIKAN DAN TAHFIDZ AL-QUR'AN</p>
                <h1>{{ $raportSettings['school_name'] }}</h1>
                <p>{{ $raportSettings['school_address'] }} • Telp: {{ $raportSettings['school_phone'] }} • Web: {{ $raportSettings['school_website'] }}</p>
            </div>
        </div>

        {{-- Basmalah --}}
        <div class="basmalah">
            بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ
        </div>

        {{-- Raport Title --}}
        <div class="raport-title">
            <h3>RAPORT CAPAIAN PEMBELAJARAN AL-QUR'AN</h3>
            <p>BIDANG STUDI: TAHSIN &amp; TAHFIDZUL QUR'AN</p>
        </div>

        {{-- Biodata Santri --}}
        <table class="student-info">
            <tr>
                <td class="label">Nama Santri</td>
                <td class="colon">:</td>
                <td class="value">{{ strtoupper($student->user->name ?? 'SANTRI') }}</td>

                <td class="label">Kelas / Halaqah</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->class->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NISN / NIS</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->nisn ?? $student->nis ?? '-' }}</td>

                <td class="label">Semester / TP</td>
                <td class="colon">:</td>
                <td class="value">Semester {{ $semester }} / {{ $academicYear ? ($academicYear->name ?? $academicYear->start_year) : now()->year }}</td>
            </tr>
            <tr>
                <td class="label">Musyrif Pembimbing</td>
                <td class="colon">:</td>
                <td class="value">{{ $data['teacher_name'] }}</td>

                <td class="label">Predikat Akhir</td>
                <td class="colon">:</td>
                <td class="value font-bold" style="color: #059669;">{{ $data['overall_predicate'] }} (Skor: {{ $data['overall_score'] }})</td>
            </tr>
        </table>

        {{-- BAGIAN A: CAPAIAN TAHSIN --}}
        <div class="section-header">A. CAPAIAN PROGRAM TAHSIN AL-QUR'AN</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Aspek Penilaian</th>
                    <th style="width: 25%;">Capaian Terakhir</th>
                    <th style="width: 15%;">Nilai (0-100)</th>
                    <th style="width: 20%;">Predikat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="font-bold">Standar Jilid &amp; Kelancaran Bacaan</td>
                    <td>{{ $data['tahsin']['last_jilid'] }} ({{ $data['tahsin']['last_pages'] }})</td>
                    <td class="text-center font-bold">{{ $data['tahsin']['score'] }}</td>
                    <td class="text-center font-bold" style="color: #059669;">{{ $data['tahsin']['predicate'] }}</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td class="font-bold">Makharijul Huruf &amp; Kaidah Tajwid</td>
                    <td>Sesuai Bimbingan Musyrif</td>
                    <td class="text-center font-bold">{{ $data['tahsin']['score'] }}</td>
                    <td class="text-center font-bold" style="color: #059669;">{{ $data['tahsin']['predicate'] }}</td>
                </tr>
            </tbody>
        </table>

        {{-- BAGIAN B: CAPAIAN TAHFIDZ --}}
        <div class="section-header">B. CAPAIAN PROGRAM TAHFIDZUL QUR'AN (HAFALAN)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Materi Setoran Hafalan</th>
                    <th style="width: 25%;">Juz &amp; Ayat Terakhir</th>
                    <th style="width: 15%;">Nilai Mutqin</th>
                    <th style="width: 20%;">Predikat</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="font-bold">{{ $data['tahfidz']['last_surah'] }}</td>
                    <td>{{ $data['tahfidz']['juz_list'] }} ({{ $data['tahfidz']['last_ayat'] }})</td>
                    <td class="text-center font-bold">{{ $data['tahfidz']['score'] }}</td>
                    <td class="text-center font-bold" style="color: #059669;">{{ $data['tahfidz']['predicate'] }}</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td class="font-bold">Kelancaran &amp; Fashohah Muroja'ah</td>
                    <td>Tercatat {{ $data['tahfidz']['total_setoran'] }} Kali Setoran</td>
                    <td class="text-center font-bold">{{ $data['tahfidz']['score'] }}</td>
                    <td class="text-center font-bold" style="color: #059669;">{{ $data['tahfidz']['predicate'] }}</td>
                </tr>
            </tbody>
        </table>

        {{-- REKAP KEHADIRAN & CATATAN MUSYRIF --}}
        <div class="info-grid">
            <div class="attendance-box">
                <div class="section-header">C. KEHADIRAN HALAQAH</div>
                <table class="data-table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th>Keterangan</th>
                            <th>Jumlah Pertemuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Hadir Mengikuti Halaqah</td>
                            <td class="text-center font-bold" style="color: #059669;">{{ $data['attendance']['hadir'] }} kali</td>
                        </tr>
                        <tr>
                            <td>Sakit</td>
                            <td class="text-center font-bold">{{ $data['attendance']['sakit'] }} kali</td>
                        </tr>
                        <tr>
                            <td>Izin Syar'i</td>
                            <td class="text-center font-bold">{{ $data['attendance']['izin'] }} kali</td>
                        </tr>
                        <tr>
                            <td>Tanpa Keterangan</td>
                            <td class="text-center font-bold" style="color: #e11d48;">{{ $data['attendance']['alpa'] }} kali</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="notes-box">
                <div class="section-header">D. PESAN &amp; CATATAN MUSYRIF HALAQAH</div>
                <div class="box-container">
                    <p style="font-style: italic; color: #334155; line-height: 1.5;">
                        "{{ $data['teacher_note'] }}"
                    </p>
                </div>
            </div>
        </div>

        {{-- Tanda Tangan Resmi --}}
        <div style="text-align: right; margin-top: 25px; margin-bottom: 5px; font-size: 9pt;">
            <p>{{ $raportSettings['city'] }}, {{ $raportSettings['date'] }}</p>
        </div>

        <div class="signature-section">
            <div class="sig-block">
                <p class="sig-title">Orang Tua / Wali Santri,</p>
                <div class="sig-space"></div>
                <p class="sig-name">( ............................................ )</p>
            </div>

            <div class="sig-block">
                <p class="sig-title">Musyrif Pembimbing Halaqah,</p>
                <div class="sig-space"></div>
                <p class="sig-name">{{ $data['teacher_name'] }}</p>
            </div>

            <div class="sig-block">
                <p class="sig-title">Kepala Pengasuhan / Sekolah,</p>
                <div class="sig-space">
                    @if(!empty($raportSettings['signature_path']))
                        <img src="{{ asset($raportSettings['signature_path']) }}" style="max-height: 55px;" alt="TTD">
                    @endif
                </div>
                <p class="sig-name">{{ $raportSettings['principal_name'] }}</p>
                <p class="sig-title">NIP: {{ $raportSettings['principal_nip'] }}</p>
            </div>
        </div>

    </div>
    @endforeach

</body>
</html>
