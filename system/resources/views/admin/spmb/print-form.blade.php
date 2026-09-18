<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pendaftaran - {{ $spmb->registration_number }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <link rel="apple-touch-icon" href="{{ Setting::get('favicon_path') ? asset(Setting::get('favicon_path')) : ((Setting::get('school_logo') && Setting::get('school_logo') !== '') ? (\Illuminate\Support\Str::startsWith(Setting::get('school_logo'), 'img/') ? asset(Setting::get('school_logo')) : asset('img/' . Setting::get('school_logo'))) : asset('img/fav.png')) }}">
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
        }
        
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            border-bottom: 3.5px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-logo-box {
            width: 75px;
            height: 75px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header-logo-box img {
            max-width: 75px;
            max-height: 75px;
            object-fit: contain;
        }
        .header-text {
            flex: 1;
            text-align: center;
        }
        .header-text .header-top {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #111;
            margin-bottom: 2px;
            line-height: 1.2;
        }
        .header-text .header-sub {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000;
            margin-bottom: 2px;
            line-height: 1.2;
        }
        .header-text h1 {
            font-size: 14.5pt;
            font-weight: bold;
            text-transform: uppercase;
            font-family: Georgia, 'Times New Roman', serif;
            font-variant-numeric: lining-nums;
            margin-bottom: 2px;
            line-height: 1.2;
        }
        .header-text p {
            font-size: 8pt;
            color: #222;
            margin-bottom: 1px;
            line-height: 1.25;
        }
        
        .title {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .title h3 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        
        .title p {
            font-size: 11pt;
            color: #333;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px 12px;
            border: 1px solid #000;
            margin-bottom: 10px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table td {
            padding: 6px 10px;
            vertical-align: top;
        }
        
        .data-table .label {
            width: 180px;
            font-weight: normal;
        }
        
        .data-table .separator {
            width: 20px;
            text-align: center;
        }
        
        .data-table .value {
            flex: 1;
        }
        
        .data-table .colon {
            width: 10px;
        }
        
        .barcode {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #000;
            display: inline-block;
        }
        
        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 3px;
        }
        
        .photo-box {
            width: 100px;
            height: 130px;
            border: 1px solid #000;
            display: inline-block;
            vertical-align: top;
            margin-left: 20px;
            text-align: center;
            padding-top: 50px;
            font-size: 9pt;
            color: #666;
        }
        
        .documents-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .documents-table th,
        .documents-table td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: left;
        }
        
        .documents-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .documents-table .check {
            width: 30px;
            text-align: center;
        }
        
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
        }
        
        .signature-name {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            display: inline-block;
            min-width: 200px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-verified {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #065f46;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #92400e;
        }
        
        .status-accepted {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #1e40af;
        }
        
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #991b1b;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #000;
            font-size: 9pt;
            text-align: center;
            color: #666;
        }
        
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(3, 1fr);
            justify-items: center;
            align-items: center;
            gap: 60px;
            opacity: 0.05;
        }
        
        .watermark-item {
            transform: rotate(-45deg);
        }
        
        .watermark-item img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    @php
        $logoPath = Setting::get('school_logo');
        $watermarkLogo = '';
        
        if ($logoPath) {
            if (file_exists(public_path('img/' . $logoPath))) {
                $watermarkLogo = asset('img/' . $logoPath);
            } elseif (file_exists(public_path($logoPath))) {
                $watermarkLogo = asset($logoPath);
            }
        }
        
        if (!$watermarkLogo) {
            $watermarkLogo = Setting::getLogoUrl();
        }
    @endphp
    
    @if($watermarkLogo)
        <div class="watermark">
            @for($i = 0; $i < 6; $i++)
                <div class="watermark-item">
                    <img src="{{ $watermarkLogo }}" alt="">
                </div>
            @endfor
        </div>
    @else
        <div class="watermark">
            @for($i = 0; $i < 6; $i++)
                <div class="watermark-item" style="font-size: 60pt; font-weight: bold;">
                    {{ $schoolShortName }}
                </div>
            @endfor
        </div>
    @endif
    
    <!-- Header Kop Surat -->
    <div class="header">
        <div class="header-logo-box">
            @if($letterheadLogoLeftUrl)
                <img src="{{ $letterheadLogoLeftUrl }}" alt="Logo Kiri">
            @elseif($watermarkLogo)
                <img src="{{ $watermarkLogo }}" alt="Logo">
            @endif
        </div>

        <div class="header-text">
            @if(!empty($letterheadHeaderTop))
                <div class="header-top">{{ $letterheadHeaderTop }}</div>
            @endif
            @if(!empty($letterheadSub))
                <div class="header-sub">{{ $letterheadSub }}</div>
            @endif

            <h1>{{ $schoolName }}</h1>

            @php
                $addressParts = array_filter([
                    $schoolAddress,
                    $schoolCity,
                    $schoolProvince,
                    $schoolPostalCode,
                ]);
                $contactParts = array_filter([
                    !empty($schoolPhone) ? 'Telp: ' . $schoolPhone : null,
                    !empty($schoolEmail) ? 'Email: ' . $schoolEmail : null,
                    !empty($schoolWebsite) ? 'Website: ' . $schoolWebsite : null,
                ]);
            @endphp

            @if(!empty($addressParts))
                <p>{{ implode(', ', $addressParts) }}</p>
            @endif
            @if(!empty($contactParts))
                <p>{{ implode(' | ', $contactParts) }}</p>
            @endif
        </div>

        <div class="header-logo-box">
            @if($letterheadLogoRightUrl)
                <img src="{{ $letterheadLogoRightUrl }}" alt="Logo Kanan">
            @endif
        </div>
    </div>
    
    <!-- Title -->
    <div class="title">
        <h3>FORMULIR PENDAFTARAN</h3>
        <p>Tahun Ajaran {{ $spmb->wave?->year ?? '-' }}</p>
    </div>
    
    <!-- Registration Info -->
    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
        <table class="data-table" style="margin-bottom: 0;">
            <tr>
                <td class="label" style="font-weight: bold; white-space: nowrap;">Nomor Pendaftaran</td>
                <td class="colon" style="white-space: nowrap;">:</td>
                <td style="font-family: 'Courier New', monospace; font-weight: bold; font-size: 13pt; letter-spacing: 1px; white-space: nowrap;">{{ $spmb->registration_number }}</td>
                <td class="separator"></td>
                <td class="label" style="font-weight: bold; white-space: nowrap;">Gelombang</td>
                <td class="colon" style="white-space: nowrap;">:</td>
                <td style="white-space: nowrap;">{{ $spmb->wave?->name ?? '-' }} @if($spmb->wave?->year)({{ $spmb->wave?->year }})@endif</td>
            </tr>
            <tr>
                <td class="label" style="font-weight: bold; white-space: nowrap;">Tanggal Daftar</td>
                <td class="colon" style="white-space: nowrap;">:</td>
                <td style="white-space: nowrap;">{{ $spmb->created_at->translatedFormat('d F Y') }}</td>
                <td class="separator"></td>
                <td class="label" style="font-weight: bold; white-space: nowrap;">Status</td>
                <td class="colon" style="white-space: nowrap;">:</td>
                <td style="white-space: nowrap;">
                    <span class="status-badge status-{{ $spmb->status }}">
                        {{ ucfirst($spmb->status) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Personal Data -->
    <div class="section">
        <div class="section-title">A. DATA PRIBADI</div>
        <table class="data-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="colon">:</td>
                <td><strong>{{ $spmb->full_name }}</strong></td>
                <td rowspan="6" class="photo-box">
                    Foto 3x4
                </td>
            </tr>
            <tr>
                <td class="label">NISN</td>
                <td class="colon">:</td>
                <td>{{ $spmb->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td class="colon">:</td>
                <td>{{ $spmb->nik }}</td>
            </tr>
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="colon">:</td>
                <td>{{ $spmb->birth_place }}, {{ \Carbon\Carbon::parse($spmb->birth_date)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td>{{ $spmb->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td class="label">Agama</td>
                <td class="colon">:</td>
                <td>{{ $spmb->religion ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Lengkap</td>
                <td class="colon">:</td>
                <td colspan="2">{{ $spmb->address }}</td>
            </tr>
            <tr>
                <td class="label">No. Telepon/HP</td>
                <td class="colon">:</td>
                <td>{{ $spmb->phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="colon">:</td>
                <td>{{ $spmb->email }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Parent Data -->
    <div class="section">
        <div class="section-title">B. DATA ORANG TUA/WALI</div>
        <table class="data-table">
            <tr>
                <td class="label">Nama Orang Tua/Wali</td>
                <td class="colon">:</td>
                <td>{{ $spmb->parent_name }}</td>
            </tr>
            <tr>
                <td class="label">No. Telepon Orang Tua/Wali</td>
                <td class="colon">:</td>
                <td>{{ $spmb->parent_phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Pekerjaan Orang Tua/Wali</td>
                <td class="colon">:</td>
                <td>{{ $spmb->parent_occupation ?? '-' }}</td>
            </tr>
        </table>
    </div>
    
    <!-- School Preference -->
    <div class="section">
        <div class="section-title">C. PILIHAN JURUSAN/KELAS</div>
        <table class="data-table">
            <tr>
                <td class="label">Pilihan Jurusan</td>
                <td class="colon">:</td>
                <td>{{ $spmb->major?->name ?? 'Tidak Ada Jurusan' }}</td>
            </tr>
            <tr>
                <td class="label">Pilihan Kelas</td>
                <td class="colon">:</td>
                <td>{{ $spmb->class?->name ?? '-' }}</td>
            </tr>
        </table>
    </div>
    
    <!-- Documents -->
    <div class="section">
        <div class="section-title">D. DOKUMEN PERSYARATAN</div>
        <table class="documents-table">
            <thead>
                <tr>
                    <th class="check">V</th>
                    <th>Dokumen</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $documentsList = [
                        'kk' => 'Kartu Keluarga (KK)',
                        'akte_kelahiran' => 'Akta Kelahiran',
                        'foto' => 'Pas Foto 3x4',
                        'rapor' => 'Rapor/SKL',
                        'sertifikat' => 'Sertifikat Prestasi (jika ada)',
                    ];
                    $uploadedDocs = $spmb->documents->pluck('type')->toArray();
                @endphp
                @foreach($documentsList as $key => $label)
                <tr>
                    <td class="check">
                        @if(in_array($key, $uploadedDocs))
                            ✓
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $label }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Verification Info -->
    @if($spmb->verified_by || $spmb->verification_notes)
    <div class="section">
        <div class="section-title">E. KETERANGAN VERIFIKASI</div>
        <table class="data-table">
            @if($spmb->verified_by)
            <tr>
                <td class="label">Diverifikasi Oleh</td>
                <td class="colon">:</td>
                <td>{{ $spmb->verifier?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Verifikasi</td>
                <td class="colon">:</td>
                <td>{{ $spmb->verified_at ? \Carbon\Carbon::parse($spmb->verified_at)->format('d F Y H:i') : '-' }}</td>
            </tr>
            @endif
            @if($spmb->verification_notes)
            <tr>
                <td class="label">Catatan</td>
                <td class="colon">:</td>
                <td>{{ $spmb->verification_notes }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif
    
    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">Orang Tua/Wali</div>
            <div class="signature-name">( ___________________ )</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">Panitia PPDB</div>
            @if($spmb->verified_by)
                <div class="signature-name">{{ $spmb->verifier?->name ?? '___________________' }}</div>
            @else
                <div class="signature-name">( ___________________ )</div>
            @endif
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dan sah tanpa tanda tangan.</p>
        <p>{{ $schoolName }} - {{ now()->format('Y') }}</p>
    </div>
    
    <!-- Print Button (Hidden when printing) -->
    <div class="no-print" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
        <button onclick="window.print()" style="background-color: #3B82F6; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            Print / Simpan PDF
        </button>
        <button onclick="window.close()" style="background-color: #EF4444; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-left: 10px;">
            Tutup
        </button>
    </div>
</body>
</html>
