<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport BPI - {{ $student->user?->name ?? 'Santri' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 11pt;
            line-height: 1.35;
        }
        .container {
            width: 100%;
            max-width: 780px;
            margin: 0 auto;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-logo {
            width: 80px;
            text-align: center;
            vertical-align: middle;
        }
        .header-logo img {
            max-width: 75px;
            max-height: 75px;
            object-fit: contain;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
        }
        .header-text h2 {
            margin: 0;
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-text h3 {
            margin: 2px 0 0 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-text p {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
            color: #374151;
        }
        .report-title {
            text-align: center;
            margin: 12px 0;
        }
        .report-title h1 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }
        .report-title p {
            margin: 2px 0 0 0;
            font-size: 9.5pt;
            font-style: italic;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }
        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }
        .content-table th, .content-table td {
            border: 1px solid #1f2937;
            padding: 5px 6px;
        }
        .content-table th {
            background-color: #f3f4f6;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .bg-light { background-color: #f9fafb; font-weight: bold; }
        
        .box-notes {
            border: 1px solid #1f2937;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 9.5pt;
            border-radius: 4px;
            background: #fafafa;
        }
        .box-notes-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
            margin-bottom: 4px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 2px;
        }

        .sign-table {
            width: 100%;
            margin-top: 18px;
            font-size: 9.5pt;
            page-break-inside: avoid;
        }
        .sign-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            padding: 0 5px;
        }
        .sign-space {
            height: 60px;
        }

        .btn-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #0d9488;
            color: white;
            border: none;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 999;
        }
        .btn-print:hover {
            background-color: #0f766e;
        }

        @media print {
            .btn-print { display: none; }
            body { font-size: 10.5pt; }
            .container { max-width: 100%; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print">
        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Cetak / Simpan PDF
    </button>

    <div class="container">
        <!-- KOP SURAT SEKOLAH -->
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if($schoolLogo)
                        <img src="{{ get_public_file_url($schoolLogo, 'img') }}" alt="Logo Sekolah">
                    @else
                        <div style="font-size: 24pt; font-weight: bold; color: #0d9488;">۞</div>
                    @endif
                </td>
                <td class="header-text">
                    <h2>YAYASAN AL-FAHMI PALU</h2>
                    <h3>{{ $schoolName }}</h3>
                    <p>{{ $schoolAddress }}</p>
                    <p>Website: sdit.alfahmi-palu.com | Email: sdit@alfahmi-palu.com | Kota: {{ $schoolCity }}</p>
                </td>
            </tr>
        </table>

        <!-- JUDUL RAPORT -->
        <div class="report-title">
            <h1>RAPOR CAPAIAN BINA PRIBADI ISLAM (BPI)</h1>
            <p>Evaluasi Mutaba'ah Ibadah Yaumiyah & Pembinaan Karakter Islami</p>
        </div>

        <!-- IDENTITAS SANTRI & KELOMPOK -->
        <table class="meta-table">
            <tr>
                <td style="width: 18%;">Nama Santri</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%; font-weight: bold; text-transform: uppercase;">{{ $student->user?->name ?? '-' }}</td>
                <td style="width: 18%;">Tahun Ajaran</td>
                <td style="width: 2%;">:</td>
                <td style="width: 22%;">{{ $activeYear?->name ?? '2024/2025' }}</td>
            </tr>
            <tr>
                <td>NISN / NIS</td>
                <td>:</td>
                <td>{{ $student->nisn ?? $student->nis ?? '-' }}</td>
                <td>Semester</td>
                <td>:</td>
                <td>{{ ucfirst($semester) }} ({{ $semester == '1' || strtolower($semester) == 'ganjil' ? 'Satu' : 'Dua' }})</td>
            </tr>
            <tr>
                <td>Kelas / Rombel</td>
                <td>:</td>
                <td>{{ $className ?? ($student->class?->name ?? '-') }}</td>
                <td>Kelompok BPI</td>
                <td>:</td>
                <td>{{ $group?->name ?? 'Kelompok Tarbiyah' }}</td>
            </tr>
            <tr>
                <td>Murabbi / Pembina</td>
                <td>:</td>
                <td>{{ $group?->mentor?->name ?? '-' }}</td>
                <td>Kategori</td>
                <td>:</td>
                <td>{{ ucfirst($group?->gender ?? 'Ikhwan') }}</td>
            </tr>
        </table>

        <!-- I. MUTABA'AH IBADAH YAUMIYAH -->
        <table class="content-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th>Aspek Mutaba'ah Ibadah Yaumiyah</th>
                    <th style="width: 80px;">Predikat</th>
                    <th>Keterangan / Kriteria Capaian</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sholatFardhu = $report?->sholat_fardhu ?? 'A';
                    $sholatSunnah = $report?->sholat_sunnah ?? 'A';
                    $tilawah = $report?->tilawah_quran ?? 'A';
                    $dzikir = $report?->dzikir_doa ?? 'A';
                    $puasa = $report?->puasa_sunnah ?? 'B';

                    $descMap = [
                        'A' => 'Sangat Baik / Tertib & Istiqomah',
                        'B' => 'Baik / Berusaha Istiqomah',
                        'C' => 'Cukup / Perlu Peningkatan Bimbingan',
                    ];
                @endphp
                <tr>
                    <td class="text-center">1</td>
                    <td><strong>Sholat Wajib 5 Waktu</strong><br><span style="font-size: 8.5pt; color: #4b5563;">Tertib di awal waktu & diutamakan berjamaah di masjid (Ikhwan)</span></td>
                    <td class="text-center text-bold">{{ $sholatFardhu }}</td>
                    <td>{{ $descMap[$sholatFardhu] ?? 'Sangat Baik' }}</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td><strong>Sholat Sunnah (Dhuha & Rawatib)</strong><br><span style="font-size: 8.5pt; color: #4b5563;">Melaksanakan sholat dhuha di sekolah & rawatib muakkadah</span></td>
                    <td class="text-center text-bold">{{ $sholatSunnah }}</td>
                    <td>{{ $descMap[$sholatSunnah] ?? 'Sangat Baik' }}</td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td><strong>Tilawah & Tadarus Al-Qur'an</strong><br><span style="font-size: 8.5pt; color: #4b5563;">Menjaga bacaan harian dan target lembar tilawah sepekan</span></td>
                    <td class="text-center text-bold">{{ $tilawah }}</td>
                    <td>{{ $descMap[$tilawah] ?? 'Sangat Baik' }} (Tercatat: {{ $totalTilawahPages }} lembar)</td>
                </tr>
                <tr>
                    <td class="text-center">4</td>
                    <td><strong>Dzikir Al-Ma'tsurat & Doa Harian</strong><br><span style="font-size: 8.5pt; color: #4b5563;">Membaca dzikir pagi/petang serta adab doa harian</span></td>
                    <td class="text-center text-bold">{{ $dzikir }}</td>
                    <td>{{ $descMap[$dzikir] ?? 'Sangat Baik' }}</td>
                </tr>
                <tr>
                    <td class="text-center">5</td>
                    <td><strong>Puasa Sunnah</strong><br><span style="font-size: 8.5pt; color: #4b5563;">Latihan puasa sunnah Senin-Kamis atau Ayyamul Bidh</span></td>
                    <td class="text-center text-bold">{{ $puasa }}</td>
                    <td>{{ $descMap[$puasa] ?? 'Baik' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- II. ADAB & PERKEMBANGAN AKHLAK ISLAMI -->
        <table class="content-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th>Dimensi Adab & Karakter Islami</th>
                    <th style="width: 80px;">Predikat</th>
                    <th>Catatan Observasi Karakter</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $adab = $report?->adab_guru_ortu ?? 'A';
                    $disiplin = $report?->kedisiplinan_kebersihan ?? 'A';
                    $ukhuwah = $report?->ukhuwah_sosial ?? 'A';
                    $keaktifan = $report?->keaktifan_halaqah ?? 'A';
                @endphp
                <tr>
                    <td class="text-center">1</td>
                    <td><strong>Adab Kepada Guru & Orang Tua</strong></td>
                    <td class="text-center text-bold">{{ $adab }}</td>
                    <td>Menunjukkan sikap tawadhu, tutur kata santun, dan taat terhadap nasihat.</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td><strong>Kedisiplinan & Kebersihan Diri</strong></td>
                    <td class="text-center text-bold">{{ $disiplin }}</td>
                    <td>Menjaga kebersihan pakaian, kerapian perlengkapan ibadah, dan tepat waktu.</td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td><strong>Ukhuwah & Kepedulian Sosial</strong></td>
                    <td class="text-center text-bold">{{ $ukhuwah }}</td>
                    <td>Saling tolong-menolong sesama teman, menghindari perkataan tercela/bullying.</td>
                </tr>
                <tr>
                    <td class="text-center">4</td>
                    <td><strong>Keaktifan Dalam Halaqah BPI</strong></td>
                    <td class="text-center text-bold">{{ $keaktifan }}</td>
                    <td>Antusias menyimak materi pembinaan dan aktif berinteraksi dengan pembina.</td>
                </tr>
            </tbody>
        </table>

        <!-- III. REKAP KEHADIRAN & PREDIKAT AKHIR -->
        <table class="content-table" style="margin-bottom: 8px;">
            <tr class="bg-light">
                <td style="width: 50%;">Kehadiran Pertemuan Mentoring BPI:</td>
                <td style="width: 50%;" class="text-bold">
                    {{ $attendedMeetings }} dari {{ $totalMeetings > 0 ? $totalMeetings : 12 }} Pertemuan 
                    ({{ $totalMeetings > 0 ? round(($attendedMeetings / $totalMeetings) * 100) : 100 }}%)
                </td>
            </tr>
            <tr class="bg-light">
                <td>Predikat Akhir Pembinaan BPI:</td>
                <td class="text-bold" style="font-size: 11pt; color: #0d9488;">
                    {{ \App\Models\BpiReport::getPredicateLabel($report?->final_predicate ?? 'A') }}
                </td>
            </tr>
        </table>

        <!-- CATATAN PEMBINA & TARGET -->
        <div class="box-notes">
            <div class="box-notes-title">Catatan & Motivasi Pembina (Murabbi):</div>
            <div>{{ $report?->mentor_notes ?? 'Alhamdulillah ananda menunjukkan komitmen ibadah dan adab islami yang sangat baik. Semoga istiqomah dalam mengamalkan nilai-nilai tarbiyah di rumah maupun di sekolah.' }}</div>
        </div>

        @if($report?->target_improvement)
            <div class="box-notes" style="margin-top: -6px;">
                <div class="box-notes-title">Target Pembinaan Semester Depan:</div>
                <div>{{ $report->target_improvement }}</div>
            </div>
        @endif

        <!-- TANDA TANGAN FORMAL -->
        <table class="sign-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    Orang Tua / Wali Santri,
                    <div class="sign-space"></div>
                    <strong>( .................................................... )</strong>
                </td>
                <td>
                    {{ $schoolCity }}, {{ date('d F Y') }}<br>
                    Murabbi / Pembina BPI,
                    <div class="sign-space"></div>
                    <strong><u>{{ $group?->mentor?->name ?? 'Pembina BPI' }}</u></strong>
                </td>
                <td>
                    Mengetahui,<br>
                    Kepala Sekolah,
                    <div class="sign-space"></div>
                    <strong><u>{{ $headmasterName }}</u></strong>
                    @if($headmasterNip)
                        <br><span style="font-size: 8.5pt;">NIP: {{ $headmasterNip }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
