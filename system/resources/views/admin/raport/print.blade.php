<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Raport Siswa - {{ $raportSettings['school_name'] }}</title>
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
            transition: all 0.2s ease;
        }
        .action-btn:hover {
            background: #4338ca;
        }
        .action-btn.btn-close {
            background: #334155;
        }
        .action-btn.btn-close:hover {
            background: #475569;
        }

        /* Header Kop Sekolah */
        .kop-header {
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
            font-size: 14.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #020617;
            font-family: Georgia, "Times New Roman", serif;
            font-variant-numeric: lining-nums;
            margin: 0 0 2px 0;
            line-height: 1.2;
        }
        .kop-details p {
            font-size: 8pt;
            font-weight: 500;
            color: #334155;
            margin: 1px 0;
            line-height: 1.25;
        }

        /* Raport Title */
        .raport-title {
            text-align: center;
            margin-bottom: 15px;
        }
        .raport-title h3 {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 1px;
            color: #0f172a;
        }
        .raport-title p {
            font-size: 9.5pt;
            font-weight: 600;
            color: #475569;
            margin-top: 2px;
        }

        /* Student Metadata Table */
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .meta-table td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .meta-table td.label {
            font-weight: 700;
            color: #334155;
            width: 15%;
        }
        .meta-table td.colon {
            width: 2%;
            font-weight: 700;
        }
        .meta-table td.value {
            width: 33%;
            color: #0f172a;
        }

        /* Content Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #94a3b8;
            padding: 6px 8px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 8.5pt;
            text-align: center;
        }
        .data-table td.text-center { text-align: center; }
        .data-table td.text-right { text-align: right; }
        .data-table td.font-bold { font-weight: 700; }

        .section-header {
            font-size: 10pt;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Attendance & Notes Container */
        .info-grid {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .attendance-box {
            width: 45%;
        }
        .notes-box {
            width: 55%;
        }
        .box-container {
            border: 1px solid #94a3b8;
            border-radius: 4px;
            padding: 8px 12px;
            background: #fafafa;
            min-height: 80px;
            font-size: 9pt;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 75px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
            font-size: 9.5pt;
        }
        .sig-block {
            width: 30%;
            text-align: center;
            position: relative;
        }
        .sig-space {
            height: 70px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sig-image {
            max-height: 60px;
            max-width: 130px;
            object-fit: contain;
        }
        .stamp-image {
            position: absolute;
            top: -10px;
            left: 15px;
            max-height: 80px;
            opacity: 0.85;
            pointer-events: none;
        }
        .sig-name {
            font-weight: 800;
            text-decoration: underline;
            color: #0f172a;
        }
        .sig-nip {
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
            Tutup Window
        </button>
    </div>

    @foreach($raportData as $data)
    @php
        $student = $data['student'];
    @endphp
    <div class="page {{ !$loop->last ? 'page-break' : '' }}">
        
        {{-- Kop Sekolah --}}
        <div class="kop-header">
            <div class="kop-logo-box">
                @php
                    $raportLogo = !empty($raportSettings['school_logo']) ? $raportSettings['school_logo'] : Setting::getLogoUrl();
                @endphp
                <img src="{{ $raportLogo }}" class="kop-logo" alt="Logo Kiri">
            </div>

            <div class="kop-details">
                @if(!empty($raportSettings['letterhead_header_top']))
                    <div class="kop-header-top">{{ $raportSettings['letterhead_header_top'] }}</div>
                @endif
                @if(!empty($raportSettings['letterhead_sub']))
                    <div class="kop-sub">{{ $raportSettings['letterhead_sub'] }}</div>
                @endif

                <h1>{{ $raportSettings['school_name'] }}</h1>

                @php
                    $addressParts = array_filter([
                        $raportSettings['school_address'] ?? null,
                        $raportSettings['school_city'] ?? null,
                        $raportSettings['school_province'] ?? null,
                        $raportSettings['school_postal_code'] ?? null,
                    ]);
                    $contactParts = array_filter([
                        !empty($raportSettings['school_phone']) ? 'Telp: ' . $raportSettings['school_phone'] : null,
                        !empty($raportSettings['school_email']) ? 'Email: ' . $raportSettings['school_email'] : null,
                        !empty($raportSettings['school_website']) ? 'Website: ' . $raportSettings['school_website'] : null,
                    ]);
                @endphp

                @if(!empty($addressParts))
                    <p>{{ implode(', ', $addressParts) }}</p>
                @endif
                @if(!empty($contactParts))
                    <p>{{ implode(' | ', $contactParts) }}</p>
                @endif
            </div>

            <div class="kop-logo-box">
                @if(!empty($raportSettings['school_logo_right']))
                    <img src="{{ $raportSettings['school_logo_right'] }}" class="kop-logo" alt="Logo Kanan">
                @endif
            </div>
        </div>

        {{-- Judul Raport --}}
        @php
            $academicYearLabel = $academicYear ? ($academicYear->start_year && $academicYear->end_year ? $academicYear->start_year . '/' . $academicYear->end_year : trim(explode('-', $academicYear->name)[0])) : '2026/2027';
        @endphp
        <div class="raport-title">
            <h3>{{ $raportSettings['header_title'] ?? 'LAPORAN HASIL BELAJAR SISWA' }}</h3>
            <p>Tahun Ajaran {{ $academicYearLabel }} - Semester {{ $semester }}</p>
        </div>

        {{-- Metadata Siswa --}}
        <table class="meta-table">
            <tr>
                <td class="label">Nama Siswa</td>
                <td class="colon">:</td>
                <td class="value"><strong>{{ $student->user->name ?? $student->name }}</strong></td>

                <td class="label">Kelas</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->class->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">
                    @if($student->nis && $student->nisn) NIS / NISN
                    @elseif($student->nis) NIS
                    @elseif($student->nisn) NISN
                    @else NIS / NISN @endif
                </td>
                <td class="colon">:</td>
                <td class="value">
                    @if($student->nis && $student->nisn)
                        {{ $student->nis }} / {{ $student->nisn }}
                    @elseif($student->nis)
                        {{ $student->nis }}
                    @elseif($student->nisn)
                        {{ $student->nisn }}
                    @else
                        -
                    @endif
                </td>

                @if(\App\Models\Setting::get('is_vocational', '1') == '1')
                <td class="label">Jurusan</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->major->name ?? 'Umum / Reguler' }}</td>
                @else
                <td class="label">Tahun Ajaran</td>
                <td class="colon">:</td>
                <td class="value">{{ $academicYearLabel }}</td>
                @endif
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>

                <td class="label">Semester</td>
                <td class="colon">:</td>
                <td class="value">Semester {{ $semester }}</td>
            </tr>
        </table>

        {{-- Tabel Nilai Akademis --}}
        <div class="section-header">A. CAPAIAN NILAI AKADEMIK</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 26%;">Mata Pelajaran</th>
                    <th style="width: 8%;">Harian</th>
                    <th style="width: 8%;">UTS</th>
                    <th style="width: 8%;">UAS</th>
                    <th style="width: 10%;">Nilai Akhir</th>
                    <th style="width: 8%;">Predikat</th>
                    <th style="width: 28%;">Capaian Kompetensi &amp; Catatan Belajar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['subjects'] as $idx => $subj)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $subj['subject'] }}</td>
                    <td class="text-center">{{ $subj['daily'] }}</td>
                    <td class="text-center">{{ $subj['mid_term'] }}</td>
                    <td class="text-center">{{ $subj['final_term'] }}</td>
                    <td class="text-center font-bold" style="font-size: 9.5pt;">{{ $subj['final_score'] }}</td>
                    <td class="text-center font-bold">{{ $subj['letter'] }}</td>
                    <td style="font-size: 8.5pt;">{{ $subj['description'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">
                        Belum ada data nilai mata pelajaran yang tercatat untuk siswa ini.
                    </td>
                </tr>
                @endforelse
                @if(count($data['subjects']) > 0)
                <tr style="background-color: #f8fafc; font-weight: 800;">
                    <td colspan="5" class="text-right" style="padding-right: 15px;">RATA-RATA KESELURUHAN</td>
                    <td class="text-center" style="font-size: 10pt; color: #0f172a;">{{ $data['overall_average'] }}</td>
                    <td colspan="2" class="text-left" style="font-size: 8.5pt; color: #475569; padding-left: 10px;">
                        Predikat Rata-Rata: 
                        <strong>
                            @if($data['overall_average'] >= 88) A (Sangat Baik)
                            @elseif($data['overall_average'] >= 78) B (Baik)
                            @elseif($data['overall_average'] >= 68) C (Cukup)
                            @else D (Perlu Bimbingan) @endif
                        </strong>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        {{-- Rekapitulasi Presensi & Catatan Wali Kelas --}}
        <div class="info-grid">
            <div class="attendance-box">
                <div class="section-header">B. REKAPITULASI KEHADIRAN</div>
                <table class="data-table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Keterangan</th>
                            <th style="width: 50%;">Jumlah Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sakit</td>
                            <td class="text-center font-bold">{{ $data['attendance']['sick'] }} hari</td>
                        </tr>
                        <tr>
                            <td>Izin</td>
                            <td class="text-center font-bold">{{ $data['attendance']['permit'] }} hari</td>
                        </tr>
                        <tr>
                            <td>Tanpa Keterangan (Alpa)</td>
                            <td class="text-center font-bold">{{ $data['attendance']['absent'] }} hari</td>
                        </tr>
                        <tr style="background-color: #f8fafc;">
                            <td class="font-bold">Hadir / Mengikuti KBM</td>
                            <td class="text-center font-bold" style="color: #16a34a;">{{ $data['attendance']['present'] }} hari</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="notes-box">
                <div class="section-header">C. CATATAN PERKEMBANGAN WALI KELAS</div>
                <div class="box-container">
                    <p style="font-style: italic; color: #334155;">
                        "{{ $data['teacher_note'] }}"
                    </p>
                </div>
            </div>
        </div>

        {{-- Tanggal Cetak Raport --}}
        <div style="display: flex; justify-content: flex-end; margin-top: 55px; margin-bottom: 8px; font-size: 9.5pt;">
            <div style="width: 30%; text-align: center;">
                <p>{{ $raportSettings['city'] }}, {{ $raportSettings['date'] }}</p>
            </div>
        </div>

        {{-- Blok Tanda Tangan --}}
        <div class="signature-section" style="margin-top: 0;">
            <div class="sig-block">
                <p>Orang Tua / Wali Siswa,</p>
                <div class="sig-space"></div>
                <p class="sig-name">( {{ $student->parent_name ?? $student->father_name ?? '.........................................' }} )</p>
            </div>

            <div class="sig-block">
                <p>Wali Kelas,</p>
                <div class="sig-space">
                    @if($raportSettings['signature_path'])
                        <img src="{{ $raportSettings['signature_path'] }}" class="sig-image" alt="TTD Wali Kelas">
                    @endif
                </div>
                <p class="sig-name">{{ $student->class->teacher->name ?? 'Wali Kelas, S.Pd.' }}</p>
                <p class="sig-nip">NIP. {{ $student->class->teacher->nip ?? '-' }}</p>
            </div>

            <div class="sig-block">
                <p>Kepala Sekolah,</p>
                <div class="sig-space">
                    @if($raportSettings['signature_path'])
                        <img src="{{ $raportSettings['signature_path'] }}" class="sig-image" alt="TTD Kepala Sekolah">
                    @endif
                    @if($raportSettings['stamp_path'])
                        <img src="{{ $raportSettings['stamp_path'] }}" class="stamp-image" alt="Stempel Sekolah">
                    @endif
                </div>
                <p class="sig-name">{{ $raportSettings['principal_name'] }}</p>
                <p class="sig-nip">NIP. {{ $raportSettings['principal_nip'] }}</p>
            </div>
        </div>

    </div>
    @endforeach

</body>
</html>
