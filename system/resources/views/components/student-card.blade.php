@props(['student', 'qrCode' => null, 'showLabel' => true, 'orientation' => null])

@php
    $qrData = $student->nisn ?? $student->qr_code ?? $student->user->email;
    $qrSvg = $qrCode ?? (\Class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode') 
        ? \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->generate($qrData) 
        : null);

    $bgFront = Setting::get('student_card_bg_front_path');
    $bgBack = Setting::get('student_card_bg_back_path');
    $stampPath = Setting::get('student_card_stamp_path') ?? Setting::get('raport_stamp_path');
    $signaturePath = Setting::get('student_card_signature_path') ?? Setting::get('raport_signature_path');
    $headmasterName = Setting::get('school_principal_name', 'Dr. H. Ahmad Wijaya, M.Pd.');
    $accentColor = Setting::get('student_card_accent_color', '#4f46e5');
    $accentColorEnd = Setting::get('student_card_accent_color_end', $accentColor);
    $accentBg = ($accentColor === $accentColorEnd) ? $accentColor : "linear-gradient(135deg, {$accentColor}, {$accentColorEnd})";
    $subtitle = Setting::get('student_card_header_subtitle', 'KARTU TANDA SISWA RESMI (KTS)');
    $cardOrientation = $orientation ?? Setting::get('student_card_orientation', 'landscape');

    $rulesRaw = Setting::get('student_card_rules', "1. Kartu ini adalah identitas resmi siswa sekolah.\n2. Wajib dibawa & ditunjukkan saat presensi QR.\n3. Kartu tidak dapat dipindahtangankan.");
    $rulesList = array_filter(explode("\n", str_replace("\r", "", $rulesRaw)));
@endphp

<div class="card-row flex flex-wrap gap-8 justify-center items-start page-break-inside-avoid my-4">
@if($cardOrientation === 'portrait')
    <!-- PORTRAIT FRONT CARD (COMPACT GRID LAYOUT) -->
    <div class="card-wrapper flex flex-col items-center gap-2">
        @if($showLabel)
            <span class="card-label text-[11px] font-black uppercase tracking-widest text-slate-600 no-print">Tampilan Depan (Portrait)</span>
        @endif
        <div class="kts-card w-[275px] h-[440px] bg-white rounded-2xl relative overflow-hidden shadow-2xl border border-slate-200 flex flex-col justify-between"
             style="{{ $bgFront ? 'background: url(' . asset($bgFront) . ') center/cover no-repeat;' : '' }}">
            
            <!-- Header -->
            <div class="kts-header text-white px-3 py-2 flex items-center justify-between z-10 shrink-0 shadow-xs"
                 style="background: {{ $accentBg }}">
                <div class="kts-header-brand flex items-center space-x-2 overflow-hidden">
                    <div class="kts-logo-icon w-[22px] h-[22px] rounded-md bg-white/15 border border-white/20 flex items-center justify-center text-white shrink-0 overflow-hidden">
                        <img src="{{ Setting::getLogoUrl() }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                    </div>
                    <div class="kts-header-titles overflow-hidden">
                        <h2 class="text-[10px] font-black uppercase tracking-wider text-white leading-tight truncate">{{ Setting::get('school_name', 'SEKOLAH INDONESIA') }}</h2>
                        <p class="text-[7.5px] font-medium text-white/80 truncate">{{ $subtitle }}</p>
                    </div>
                </div>
                <span class="kts-tag bg-white/20 border border-white/30 text-white text-[7px] font-extrabold px-1.5 py-0.5 rounded uppercase shrink-0">KTS</span>
            </div>

            <!-- Body Portrait (Clean Full-Width Layout) -->
            <div class="kts-body flex-1 p-3.5 flex flex-col justify-between items-center text-center {{ $bgFront ? 'bg-white/85 backdrop-blur-xs' : 'bg-gradient-to-br from-slate-50 to-white' }} z-10 overflow-hidden text-slate-900">
                
                <!-- Foto & Nama di Atas -->
                <div class="flex flex-col items-center w-full my-auto shrink-0">
                    <div class="kts-photo-frame w-[92px] h-[118px] rounded-xl overflow-hidden border-2 border-slate-300 shadow-md bg-slate-100 flex items-center justify-center shrink-0 mb-2">
                        @if($student?->photo_url)
                            <img src="{{ $student->photo_url }}" alt="{{ $student->user->name ?? '' }}" class="w-full h-full object-cover" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                        @endif
                        <div class="kts-photo-fallback w-full h-full bg-slate-200 text-slate-600 font-extrabold text-2xl flex items-center justify-center" style="{{ $student?->photo_url ? 'display:none;' : '' }}">
                            {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                        </div>
                    </div>

                    <h3 class="font-black text-xs text-slate-900 tracking-wide uppercase text-center w-full truncate border-b border-slate-200/80 pb-1 mb-1 leading-tight">{{ $student->user->name ?? '-' }}</h3>
                </div>

                <!-- Clean Full-Width Details List -->
                <div class="w-full space-y-1 text-left my-auto px-1">
                    <div class="kts-detail-row grid grid-cols-[60px_1fr] text-[9.5px] leading-tight py-1 border-b border-slate-100">
                        <span class="kts-detail-label font-bold text-slate-400 uppercase text-[8px] tracking-wider">NISN</span>
                        <span class="kts-detail-val font-mono font-extrabold" style="color: {{ $accentColor }}">{{ $student->nisn ?? '-' }}</span>
                    </div>

                    <div class="kts-detail-row grid grid-cols-[60px_1fr] text-[9.5px] leading-tight py-1 border-b border-slate-100">
                        <span class="kts-detail-label font-bold text-slate-400 uppercase text-[8px] tracking-wider">NIK</span>
                        <span class="kts-detail-val font-bold text-slate-800">{{ $student->nik ?? '-' }}</span>
                    </div>

                    <div class="kts-detail-row grid grid-cols-[60px_1fr] text-[9.5px] leading-tight py-1 border-b border-slate-100">
                        <span class="kts-detail-label font-bold text-slate-400 uppercase text-[8px] tracking-wider">Kelas</span>
                        <span class="kts-detail-val font-extrabold text-slate-900">{{ $student->class?->name ?? '-' }}</span>
                    </div>

                    @if($student->major)
                        <div class="kts-detail-row grid grid-cols-[60px_1fr] text-[9.5px] leading-tight py-1 border-b border-slate-100">
                            <span class="kts-detail-label font-bold text-slate-400 uppercase text-[8px] tracking-wider">Jurusan</span>
                            <span class="kts-detail-val font-bold text-slate-800 truncate">{{ $student->major->name }}</span>
                        </div>
                    @endif

                    <div class="kts-detail-row grid grid-cols-[60px_1fr] text-[9.5px] leading-tight py-1">
                        <span class="kts-detail-label font-bold text-slate-400 uppercase text-[8px] tracking-wider">Gender</span>
                        <span class="kts-detail-val font-bold text-slate-800">{{ $student->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="kts-footer {{ $bgFront ? 'bg-slate-950/85 text-slate-300' : 'bg-slate-50 text-slate-500 border-t border-slate-200' }} px-3 py-1 flex items-center justify-between text-[7.5px] font-semibold z-10 shrink-0">
                <span>Berlaku Selama Menjadi Siswa</span>
                <span class="kts-footer-nisn font-mono font-bold">ID: {{ $student->nisn ?? $student->id }}</span>
            </div>
        </div>
    </div>

    <!-- PORTRAIT BACK CARD (COMPACT GRID LAYOUT) -->
    <div class="card-wrapper flex flex-col items-center gap-2">
        @if($showLabel)
            <span class="card-label text-[11px] font-black uppercase tracking-widest text-slate-600 no-print">Tampilan Belakang (Portrait)</span>
        @endif
        <div class="kts-card w-[275px] h-[440px] bg-white rounded-2xl relative overflow-hidden shadow-2xl border border-slate-200 flex flex-col justify-between"
             style="{{ $bgBack ? 'background: url(' . asset($bgBack) . ') center/cover no-repeat;' : '' }}">
            
            <!-- Header -->
            <div class="kts-header text-white px-3 py-2 flex items-center justify-between z-10 shrink-0 shadow-xs"
                 style="background: {{ $accentBg }}">
                <div class="kts-header-brand flex items-center space-x-2 overflow-hidden">
                    <div class="kts-logo-icon w-[22px] h-[22px] rounded-md bg-white/15 border border-white/20 flex items-center justify-center text-white shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <div class="kts-header-titles overflow-hidden">
                        <h2 class="text-[10px] font-black uppercase tracking-wider text-white leading-tight truncate">QR ABSENSI DIGITAL</h2>
                        <p class="text-[7.5px] font-medium text-white/80 truncate">TATA TERTIB & KETENTUAN</p>
                    </div>
                </div>
                <span class="kts-tag bg-emerald-500/30 border border-emerald-400/40 text-emerald-200 text-[7px] font-extrabold px-1.5 py-0.5 rounded uppercase shrink-0">SCAN</span>
            </div>

            <!-- Body Back Portrait (Compact Grid) -->
            <div class="kts-back-body flex-1 p-3 flex flex-col justify-between text-left {{ $bgBack ? 'bg-white/85 backdrop-blur-xs' : 'bg-white' }} z-10 overflow-hidden">
                
                <!-- Upper Grid: QR Code Kiri & Description Kanan -->
                <div class="grid grid-cols-[82px_1fr] gap-2.5 items-center bg-white/70 p-2 rounded-xl border border-slate-200/80 shadow-2xs">
                    <div class="kts-qr-box w-[82px] h-[82px] p-1 bg-white border-2 border-indigo-600 rounded-xl shadow-sm flex items-center justify-center overflow-hidden shrink-0 [&_svg]:w-full [&_svg]:h-full [&_svg]:max-w-full [&_svg]:max-h-full [&_svg]:object-contain">
                        @if($qrSvg)
                            {!! $qrSvg !!}
                        @else
                            <img src="https://quickchart.io/qr?text={{ urlencode($qrData) }}&size=150" alt="QR Code" class="w-full h-full object-contain">
                        @endif
                    </div>

                    <div class="space-y-1 overflow-hidden">
                        <h4 class="text-[9.5px] font-black text-slate-900 uppercase tracking-wider leading-tight">QR PRESENSI</h4>
                        <p class="text-[7.5px] font-medium text-slate-500 leading-tight">Tunjukkan kode QR ini pada mesin scanner presensi sekolah.</p>
                        <span class="font-mono font-extrabold text-[8.5px] text-indigo-700 block mt-1">NISN: {{ $student->nisn ?? '-' }}</span>
                    </div>
                </div>

                <!-- Middle Section: Rules Box -->
                <div class="kts-rules-box w-full space-y-1.5 text-left bg-slate-50/90 p-2.5 rounded-xl border border-slate-200/80 my-auto">
                    <div class="kts-rules-title text-[8.5px] font-black text-slate-900 uppercase tracking-wider">KETENTUAN PENGGUNAAN:</div>
                    <div class="space-y-1 max-h-[110px] overflow-hidden">
                        @foreach($rulesList as $idx => $rule)
                            <div class="kts-rule-item flex items-center space-x-1.5 text-[8px] text-slate-700 font-semibold leading-tight">
                                <span class="kts-rule-num w-3.5 h-3.5 bg-indigo-100 text-indigo-800 font-black rounded-full flex items-center justify-center text-[7.5px] shrink-0 leading-none shadow-2xs">{{ $idx + 1 }}</span>
                                <span class="line-clamp-2">{{ trim(preg_replace('/^\d+\.\s*/', '', $rule)) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Lower Section: Sign & Stamp Overlay Box -->
                <div class="kts-sign-box flex flex-col items-center text-center self-end ml-auto text-[7.5px] text-slate-700 leading-tight relative shrink-0 pt-0.5">
                    <p class="font-semibold text-slate-500">{{ Setting::get('school_city', 'Jakarta') }}, {{ date('Y') }}</p>
                    <p class="font-bold text-slate-700">Kepala Sekolah,</p>
                    
                    <div class="kts-sign-space h-7 w-full relative flex items-center justify-center my-0.5">
                        @if($stampPath)
                            <img src="{{ asset($stampPath) }}" alt="Stempel" class="absolute left-1/2 -translate-x-1/2 -top-2 h-10 object-contain opacity-55 mix-blend-multiply pointer-events-none z-0">
                        @endif
                        @if($signaturePath)
                            <img src="{{ asset($signaturePath) }}" alt="TTD" class="absolute inset-0 m-auto h-7 object-contain pointer-events-none z-10">
                        @endif
                    </div>
                    
                    <p class="kts-sign-name font-extrabold text-slate-900 underline text-center whitespace-nowrap">{{ $headmasterName }}</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="kts-footer {{ $bgBack ? 'bg-slate-950/85 text-slate-300' : 'bg-slate-50 text-slate-500 border-t border-slate-200' }} px-3 py-1 flex items-center justify-between text-[7.5px] font-semibold z-10 shrink-0">
                <span class="truncate max-w-[120px]">{{ Setting::get('school_name', 'Sekolah Indonesia') }}</span>
                <span>Jika menemukan harap kembalikan</span>
            </div>
        </div>
    </div>
@else
    <!-- LANDSCAPE FRONT CARD (KARTU TAMPAKAN DEPAN) -->
    <div class="card-wrapper flex flex-col items-center gap-2">
        @if($showLabel)
            <span class="card-label text-[11px] font-black uppercase tracking-widest text-slate-600 no-print">Tampilan Depan</span>
        @endif
        <div class="kts-card w-[440px] h-[275px] bg-white rounded-2xl relative overflow-hidden shadow-2xl border border-slate-200 flex flex-col justify-between"
             style="{{ $bgFront ? 'background: url(' . asset($bgFront) . ') center/cover no-repeat;' : '' }}">
            
            <!-- Custom BG Overlay if set or Default Header -->
            <div class="kts-header text-white px-4 py-2.5 flex items-center justify-between z-10 shrink-0 shadow-xs"
                 style="background: {{ $accentBg }}">
                <div class="kts-header-brand flex items-center space-x-2.5">
                    <div class="kts-logo-icon w-6 h-6 rounded-md bg-white/10 border border-white/20 flex items-center justify-center text-indigo-300 shrink-0 overflow-hidden">
                        <img src="{{ Setting::getLogoUrl() }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                    </div>
                    <div class="kts-header-titles">
                        <h2 class="text-xs font-black uppercase tracking-wider text-white leading-tight drop-shadow-sm">{{ Setting::get('school_name', 'SEKOLAH INDONESIA') }}</h2>
                        <p class="text-[9px] font-semibold text-slate-300">{{ $subtitle }}</p>
                    </div>
                </div>
                <span class="kts-tag bg-indigo-500/30 border border-indigo-400/40 text-indigo-200 text-[8px] font-extrabold px-2 py-0.5 rounded-md uppercase tracking-wider">KTS</span>
            </div>

            <!-- Card Body Front -->
            <div class="kts-body flex-1 p-3.5 grid grid-cols-[96px_1fr] gap-3.5 items-center {{ $bgFront ? 'bg-white/80 backdrop-blur-xs' : 'bg-gradient-to-br from-slate-50 to-white' }} z-10">
                <!-- Photo Box -->
                <div class="kts-photo-box flex flex-col items-center space-y-1.5">
                    <div class="kts-photo-frame w-[92px] h-[118px] rounded-xl overflow-hidden border-2 border-slate-300 shadow-md bg-slate-100 flex items-center justify-center shrink-0">
                        @if($student?->photo_url)
                            <img src="{{ $student->photo_url }}" alt="{{ $student->user->name ?? '' }}" class="w-full h-full object-cover" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                        @endif
                        <div class="kts-photo-fallback w-full h-full bg-slate-200 text-slate-600 font-extrabold text-2xl flex items-center justify-center" style="{{ $student?->photo_url ? 'display:none;' : '' }}">
                            {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                        </div>
                    </div>
                </div>

                <!-- Student Details List -->
                <div class="kts-details flex flex-col space-y-1.5">
                    <div class="kts-student-name font-black text-sm text-slate-900 border-b border-slate-200/90 pb-1 leading-tight truncate">
                        {{ $student->user->name ?? '-' }}
                    </div>

                    <div class="kts-detail-row grid grid-cols-[70px_1fr] text-[10px] leading-tight">
                        <span class="kts-detail-label font-bold text-slate-500">NISN</span>
                        <span class="kts-detail-val font-mono font-extrabold" style="color: {{ $accentColor }}">{{ $student->nisn ?? '-' }}</span>
                    </div>

                    <div class="kts-detail-row grid grid-cols-[70px_1fr] text-[10px] leading-tight">
                        <span class="kts-detail-label font-bold text-slate-500">NIK</span>
                        <span class="kts-detail-val font-bold text-slate-800">{{ $student->nik ?? '-' }}</span>
                    </div>

                    <div class="kts-detail-row grid grid-cols-[70px_1fr] text-[10px] leading-tight">
                        <span class="kts-detail-label font-bold text-slate-500">Kelas</span>
                        <span class="kts-detail-val font-extrabold text-slate-900">{{ $student->class?->name ?? '-' }}</span>
                    </div>

                    @if($student->major)
                        <div class="kts-detail-row grid grid-cols-[70px_1fr] text-[10px] leading-tight">
                            <span class="kts-detail-label font-bold text-slate-500">Jurusan</span>
                            <span class="kts-detail-val font-bold text-slate-800 truncate">{{ $student->major->name }}</span>
                        </div>
                    @endif

                    <div class="kts-detail-row grid grid-cols-[70px_1fr] text-[10px] leading-tight">
                        <span class="kts-detail-label font-bold text-slate-500">Gender</span>
                        <span class="kts-detail-val font-bold text-slate-800">{{ $student->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer Front -->
            <div class="kts-footer {{ $bgFront ? 'bg-slate-950/80 text-slate-300' : 'bg-slate-50 text-slate-500 border-t border-slate-200' }} px-4 py-1.5 flex items-center justify-between text-[8px] font-semibold z-10 shrink-0">
                <span>Berlaku Selama Menjadi Siswa</span>
                <span class="kts-footer-nisn font-mono font-bold">ID: {{ $student->nisn ?? $student->id }}</span>
            </div>
        </div>
    </div>

    <!-- LANDSCAPE BACK CARD (KARTU TAMPAKAN BELAKANG) -->
    <div class="card-wrapper flex flex-col items-center gap-2">
        @if($showLabel)
            <span class="card-label text-[11px] font-black uppercase tracking-widest text-slate-600 no-print">Tampilan Belakang</span>
        @endif
        <div class="kts-card w-[440px] h-[275px] bg-white rounded-2xl relative overflow-hidden shadow-2xl border border-slate-200 flex flex-col justify-between"
             style="{{ $bgBack ? 'background: url(' . asset($bgBack) . ') center/cover no-repeat;' : '' }}">
            
            <!-- Card Header Back -->
            <div class="kts-header text-white px-4 py-2.5 flex items-center justify-between z-10 shrink-0 shadow-xs"
                 style="background: {{ $accentBg }}">
                <div class="kts-header-brand flex items-center space-x-2.5">
                    <div class="kts-logo-icon w-6 h-6 rounded-md bg-white/10 border border-white/20 flex items-center justify-center text-indigo-300 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <div class="kts-header-titles">
                        <h2 class="text-xs font-black uppercase tracking-wider text-white leading-tight">QR ABSENSI DIGITAL</h2>
                        <p class="text-[9px] font-semibold text-slate-300">TATA TERTIB & KETENTUAN KARTU</p>
                    </div>
                </div>
                <span class="kts-tag bg-emerald-500/30 border border-emerald-400/40 text-emerald-300 text-[8px] font-extrabold px-2 py-0.5 rounded-md uppercase tracking-wider">SCAN</span>
            </div>

            <!-- Card Body Back (Balanced & Proportional Layout) -->
            <div class="kts-back-body flex-1 p-3.5 grid grid-cols-[105px_1fr] gap-4 items-stretch {{ $bgBack ? 'bg-white/85 backdrop-blur-xs' : 'bg-white' }} z-10 overflow-hidden">
                <!-- QR Code Column -->
                <div class="kts-qr-container flex flex-col items-center justify-center text-center space-y-1.5 my-auto shrink-0">
                    <div class="kts-qr-box w-[92px] h-[92px] p-1.5 bg-white border-2 border-indigo-600 rounded-2xl shadow-md flex items-center justify-center overflow-hidden shrink-0 [&_svg]:w-full [&_svg]:h-full [&_svg]:max-w-full [&_svg]:max-h-full [&_svg]:object-contain">
                        @if($qrSvg)
                            {!! $qrSvg !!}
                        @else
                            <img src="https://quickchart.io/qr?text={{ urlencode($qrData) }}&size=150" alt="QR Code" class="w-full h-full object-contain">
                        @endif
                    </div>
                    <span class="kts-qr-caption font-mono font-extrabold text-[8.5px] text-indigo-700 uppercase tracking-wide">NISN: {{ $student->nisn ?? '-' }}</span>
                </div>

                <!-- Rules & Signature Column -->
                <div class="kts-rules-box flex flex-col justify-between h-full text-left relative overflow-hidden py-0.5">
                    <div class="space-y-1.5">
                        <div class="kts-rules-title text-[9px] font-black text-slate-900 uppercase tracking-wider">KETENTUAN PENGGUNAAN:</div>
                        <div class="space-y-1.5 max-h-[110px] overflow-hidden">
                            @foreach($rulesList as $idx => $rule)
                                <div class="kts-rule-item flex items-center space-x-2 text-[9px] text-slate-700 font-semibold leading-tight">
                                    <span class="kts-rule-num w-4 h-4 bg-indigo-100 text-indigo-800 font-black rounded-full flex items-center justify-center text-[8px] shrink-0 leading-none shadow-2xs">{{ $idx + 1 }}</span>
                                    <span class="line-clamp-2">{{ trim(preg_replace('/^\d+\.\s*/', '', $rule)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sign & Stamp Overlay Box -->
                    <div class="kts-sign-box flex flex-col items-center text-center self-end ml-auto text-[7.5px] text-slate-700 leading-tight relative mt-auto pt-1">
                        <p class="font-semibold text-slate-500">{{ Setting::get('school_city', 'Jakarta') }}, {{ date('Y') }}</p>
                        <p class="font-bold text-slate-700">Kepala Sekolah,</p>
                        
                        <div class="kts-sign-space h-7 w-full relative flex items-center justify-center my-0.5">
                            @if($stampPath)
                                <img src="{{ asset($stampPath) }}" alt="Stempel" class="absolute left-1/2 -translate-x-1/2 -top-2 h-11 object-contain opacity-55 mix-blend-multiply pointer-events-none z-0">
                            @endif
                            @if($signaturePath)
                                <img src="{{ asset($signaturePath) }}" alt="TTD" class="absolute inset-0 m-auto h-8 object-contain pointer-events-none z-10">
                            @endif
                        </div>
                        
                        <p class="kts-sign-name font-extrabold text-slate-900 underline text-center whitespace-nowrap">{{ $headmasterName }}</p>
                    </div>
                </div>
            </div>

            <!-- Card Footer Back -->
            <div class="kts-footer {{ $bgBack ? 'bg-slate-950/80 text-slate-300' : 'bg-slate-50 text-slate-500 border-t border-slate-200' }} px-4 py-1.5 flex items-center justify-between text-[8px] font-semibold z-10 shrink-0">
                <span>{{ Setting::get('school_name', 'Sekolah Indonesia') }}</span>
                <span>Jika menemukan harap kembalikan ke sekolah</span>
            </div>
        </div>
    </div>
@endif
</div>
