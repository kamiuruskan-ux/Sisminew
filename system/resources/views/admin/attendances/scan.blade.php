@extends('layouts.app')

@section('title', 'Sistem Scanner Presensi QR Code')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 p-4 md:p-6 flex flex-col justify-between space-y-6 select-none relative overflow-x-hidden">
    <!-- Standalone Kiosk Header Bar -->
    <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-4 md:p-5 shadow-2xl backdrop-blur-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 text-center sm:text-left">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 border border-emerald-400/30 flex items-center justify-center text-white shrink-0 shadow-lg shadow-emerald-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1 w-full">
                <div class="flex flex-col sm:flex-row items-center sm:items-center gap-1.5 sm:gap-2">
                    <h1 class="text-lg sm:text-xl font-black tracking-tight text-white break-words">{{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}</h1>
                    <span class="inline-block w-fit px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 uppercase tracking-widest shrink-0">Presensi Real-Time</span>
                </div>
                <p class="text-slate-400 text-xs font-medium mt-1">Pencatatan Kehadiran Otomatis Berdasarkan NISN & QR Code Siswa.</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full md:w-auto">
            <!-- Live Digital Clock -->
            <div class="bg-slate-950/80 border border-slate-800 px-4 py-2 rounded-xl flex items-center justify-center space-x-3 text-xs font-mono w-full sm:w-auto">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span id="liveClock" class="font-black text-white text-sm">00:00:00 WIB</span>
                <span class="text-slate-500">|</span>
                <span class="text-slate-400 font-semibold" id="liveDate">{{ date('d M Y') }}</span>
            </div>

            <!-- Fullscreen Button -->
            <button onclick="toggleFullScreen()" id="fullScreenBtn" class="bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs px-4 py-2.5 rounded-xl transition-all flex items-center justify-center space-x-2 shadow-lg shadow-indigo-600/30 active:scale-95 w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
                <span id="fullScreenBtnText">Layar Penuh</span>
            </button>

            <!-- Real-Time History Modal Trigger -->
            <button onclick="openHistoryModal()" class="bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs px-4 py-2.5 rounded-xl transition-all flex items-center justify-center space-x-2 shadow-lg shadow-emerald-600/30 active:scale-95 w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Riwayat Scan</span>
            </button>
        </div>
    </div>

    @php
        $activeMethod = Setting::get('attendance_active_method', Setting::get('attendance_default_method', 'qr'));
        $isCameraMethod = in_array($activeMethod, ['qr', 'face']);
    @endphp

    <!-- Attendance Settings & Schedule Banner -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-xl p-3 px-4 shadow-lg backdrop-blur-md flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-slate-400 font-bold">Metode Presensi Aktif:</span>
            @if($activeMethod === 'rfid')
                <span class="px-2.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[11px] font-extrabold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                    <span>NFC / RFID Card</span>
                </span>
            @elseif($activeMethod === 'finger')
                <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[11px] font-extrabold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Fingerprint (Sidik Jari)</span>
                </span>
            @elseif($activeMethod === 'face')
                <span class="px-2.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[11px] font-extrabold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Scan Wajah (Face ID)</span>
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-[11px] font-extrabold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span>Scan QR Code (KTS)</span>
                </span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-3 text-slate-300 text-[11px] font-mono">
            <div>Jam Masuk: <span class="font-extrabold text-emerald-400">{{ Setting::get('attendance_entry_time', '07:00') }}</span></div>
            <div class="text-slate-600">|</div>
            <div>Batas Terlambat: <span class="font-extrabold text-amber-400">{{ Setting::get('attendance_late_time', '07:15') }}</span></div>
            <div class="text-slate-600">|</div>
            <div>Jam Pulang: <span class="font-extrabold text-cyan-400">{{ Setting::get('attendance_exit_time', '15:00') }}</span></div>
        </div>
    </div>

    <!-- Main Scanner Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1">

        <!-- Left: Active Method Terminal (2 Cols) -->
        <div class="lg:col-span-2 space-y-5 flex flex-col">
            @if($isCameraMethod)
            <!-- CAMERA TERMINAL PANEL (QR Code or Face ID) -->
            <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-2xl flex-1 flex flex-col justify-between relative overflow-hidden backdrop-blur-xl">
                <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-800/80">
                    <h3 class="font-extrabold text-white text-xs sm:text-sm flex items-center min-w-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span class="truncate">Scanner Kamera ({{ $activeMethod === 'qr' ? 'QR Code KTS' : 'Scan Wajah' }})</span>
                    </h3>
                    <div class="flex items-center space-x-3 shrink-0">
                        <span class="text-[10px] sm:text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2 sm:px-3 py-0.5 sm:py-1 rounded-full border border-emerald-500/20 flex items-center">
                            <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-400 mr-1.5 sm:mr-2 animate-ping"></span>
                            <span>Kamera Ready</span>
                        </span>
                    </div>
                </div>

                <!-- Video Viewfinder Frame -->
                <div class="relative bg-black rounded-2xl overflow-hidden shadow-2xl my-4 flex-1 min-h-[260px] sm:min-h-[360px] md:min-h-[420px] border border-slate-800">
                    <video id="video" class="w-full h-full object-cover" autoplay playsinline></video>
                    
                    <!-- Scanner Frame Corner Accent Overlay -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="relative">
                            <div class="w-56 h-56 sm:w-72 sm:h-72 border-2 border-white/20 rounded-3xl relative shadow-[0_0_50px_rgba(16,185,129,0.15)]">
                                <div class="absolute -top-1 -left-1 w-8 h-8 sm:w-10 sm:h-10 border-t-4 border-l-4 border-emerald-400 rounded-tl-2xl"></div>
                                <div class="absolute -top-1 -right-1 w-8 h-8 sm:w-10 sm:h-10 border-t-4 border-r-4 border-emerald-400 rounded-tr-2xl"></div>
                                <div class="absolute -bottom-1 -left-1 w-8 h-8 sm:w-10 sm:h-10 border-b-4 border-l-4 border-emerald-400 rounded-bl-2xl"></div>
                                <div class="absolute -bottom-1 -right-1 w-8 h-8 sm:w-10 sm:h-10 border-b-4 border-r-4 border-emerald-400 rounded-br-2xl"></div>
                                
                                <!-- Futuristic Scanning Animation Overlay -->
                                <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none scan-grid-bg">
                                    <div class="absolute left-2 right-2 h-1 bg-gradient-to-r from-transparent via-emerald-400 to-transparent opacity-95 rounded-full laser-scan-line"></div>
                                    <div class="w-full h-1/2 bg-gradient-to-b from-emerald-500/10 to-transparent absolute left-0 laser-sweep-area"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Guideline Badge -->
                    <div class="absolute bottom-5 left-0 right-0 text-center pointer-events-none w-full flex justify-center">
                        <p class="text-white text-[10px] sm:text-xs bg-slate-950/80 backdrop-blur-md inline-flex items-center space-x-2 px-4 sm:px-6 py-2 sm:py-2.5 rounded-full font-bold border border-white/10 shadow-2xl max-w-[90%] mx-auto justify-center">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>{{ $activeMethod === 'qr' ? 'Arahkan QR Code Kartu Siswa Ke Dalam Kotak Scanner' : 'Posisikan Wajah Siswa Ke Dalam Kotak Scanner' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Camera Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button id="startCamera" class="w-full sm:flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-5 py-3.5 rounded-xl font-extrabold text-xs transition-all active:scale-95 flex items-center justify-center space-x-2 shadow-lg shadow-emerald-600/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span>Nyalakan Kamera Scanner</span>
                    </button>
                    <button id="stopCamera" class="w-full sm:flex-1 bg-rose-600 hover:bg-rose-500 text-white px-5 py-3.5 rounded-xl font-extrabold text-xs transition-all active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center space-x-2" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Matikan Kamera</span>
                    </button>
                </div>
            </div>

            @elseif($activeMethod === 'rfid')
            <!-- RFID READER TERMINAL PANEL -->
            <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-6 sm:p-8 shadow-2xl flex-1 flex flex-col items-center justify-center text-center space-y-6 backdrop-blur-xl relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-indigo-500/5 pointer-events-none"></div>

                <div class="relative">
                    <div class="w-24 h-24 rounded-3xl bg-purple-500/10 border-2 border-purple-500/30 text-purple-400 flex items-center justify-center shadow-2xl shadow-purple-500/20 animate-pulse">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                <div class="space-y-2 max-w-lg z-10">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 bg-purple-500/10 text-purple-300 rounded-full border border-purple-500/20 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-purple-400 animate-ping"></span>
                        <span>Mode Presensi: NFC / RFID Reader</span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-white">Tempelkan Kartu RFID / NFC Siswa</h3>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed font-medium">
                        Silakan tempelkan (Tap) Kartu Tanda Siswa (KTS) pada scanner RFID Reader. Data kehadiran akan otomatis tercatat secara real-time.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-slate-300 font-mono flex items-center space-x-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-ping"></span>
                    <span>Status Reader: Terhubung &amp; Siap Membaca Tag Card...</span>
                </div>
            </div>

            @elseif($activeMethod === 'finger')
            <!-- FINGERPRINT BIOMETRIC TERMINAL PANEL -->
            <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-6 sm:p-8 shadow-2xl flex-1 flex flex-col items-center justify-center text-center space-y-6 backdrop-blur-xl relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 via-transparent to-teal-500/5 pointer-events-none"></div>

                <div class="relative">
                    <div class="w-24 h-24 rounded-3xl bg-emerald-500/10 border-2 border-emerald-500/30 text-emerald-400 flex items-center justify-center shadow-2xl shadow-emerald-500/20 animate-pulse">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 4.12a21.959 21.959 0 01-4.329 2.07M15 15.5a8.38 8.38 0 001.76-2.5m1.523-2.15a13.96 13.96 0 00.717-3.85C19 4.9 15.866 2 12 2g"/></svg>
                    </div>
                </div>

                <div class="space-y-2 max-w-lg z-10">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 bg-emerald-500/10 text-emerald-300 rounded-full border border-emerald-500/20 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>Mode Presensi: Mesin Sidik Jari (Fingerprint)</span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-white">Posisikan Jari Siswa Pada Sensor Mesin</h3>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed font-medium">
                        Tempelkan sidik jari siswa pada sensor mesin biometrik sekolah. Data kehadiran akan otomatis disinkronkan ke server secara langsung.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-slate-300 font-mono flex items-center space-x-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Status Mesin: Terhubung ke IP {{ Setting::get('attendance_finger_device_ip', '192.168.1.201') }}:{{ Setting::get('attendance_finger_device_port', '4370') }}</span>
                </div>
            </div>
            @endif

            <!-- Manual NISN & Barcode Scanner Input Card -->
            <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xl backdrop-blur-xl">
                <form id="manualForm" onsubmit="event.preventDefault(); submitManualScan();" class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <div class="relative flex-1">
                        <input type="text" id="manualQrInput" autofocus placeholder="Scan Barcode / Input NISN..."
                            class="w-full pl-10 pr-4 py-3.5 bg-slate-950 border border-slate-800 rounded-xl focus:ring-2 focus:ring-emerald-500 text-xs font-bold text-white placeholder-slate-500 transition-all">
                        <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <button type="submit" id="submitManual" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3.5 rounded-xl font-extrabold text-xs shadow-lg transition-all active:scale-95 shrink-0">
                        Proses Manual
                    </button>
                </form>
            </div>
        </div>

        <!-- Right: Recent Scans & Stats Feed (1 Col) -->
        <div class="space-y-5 flex flex-col justify-between">
            <!-- Status & Settings -->
            <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xl space-y-4 backdrop-blur-xl">
                <h3 class="font-extrabold text-white text-sm flex items-center pb-3 border-b border-slate-800/80">
                    <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    </svg>
                    Set Status Presensi
                </h3>

                <div class="grid grid-cols-3 gap-2">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="status" value="auto" checked class="sr-only peer">
                        <div class="px-2.5 py-2.5 border-2 border-slate-800 rounded-xl text-center font-extrabold text-[11px] text-slate-400 peer-checked:border-indigo-500 peer-checked:bg-indigo-500/20 peer-checked:text-indigo-400 transition-all">
                            Otomatis
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="status" value="present" class="sr-only peer">
                        <div class="px-2.5 py-2.5 border-2 border-slate-800 rounded-xl text-center font-extrabold text-[11px] text-slate-400 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/20 peer-checked:text-emerald-400 transition-all">
                            Hadir
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="status" value="late" class="sr-only peer">
                        <div class="px-2.5 py-2.5 border-2 border-slate-800 rounded-xl text-center font-extrabold text-[11px] text-slate-400 peer-checked:border-amber-500 peer-checked:bg-amber-500/20 peer-checked:text-amber-400 transition-all">
                            Terlambat
                        </div>
                    </label>
                </div>
            </div>

            <!-- Stats KPI Card -->
            <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xl backdrop-blur-xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 mb-4">
                    <h3 class="font-extrabold text-white text-sm flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Rekapitulasi Presensi Hari Ini
                    </h3>
                </div>

                <div class="bg-emerald-950/40 rounded-xl p-4 border border-emerald-500/30 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] text-emerald-400 font-extrabold uppercase tracking-widest">Total Siswa Terabsen</p>
                        <p class="text-3xl font-black text-white mt-0.5" id="totalToday">0</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-500/20 border border-emerald-500/40 rounded-xl flex items-center justify-center text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Recent Scan Feed Log Card -->
            <div class="bg-slate-900/90 border border-slate-800/80 rounded-2xl p-4 sm:p-5 shadow-xl flex-1 flex flex-col justify-between backdrop-blur-xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 mb-3">
                    <h3 class="font-extrabold text-white text-xs uppercase tracking-wider flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pemindaian Terakhir
                    </h3>
                    <button onclick="openHistoryModal()" class="text-[10px] font-bold text-emerald-400 hover:underline">Lihat Semua</button>
                </div>

                <div id="recentScansList" class="space-y-2.5 overflow-y-auto max-h-[220px] pr-1">
                    <div class="text-center py-8 text-slate-500 text-xs font-semibold">
                        Belum ada pemindaian presensi hari ini.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REAL-TIME RIWAYAT SCAN MODAL / DRAWER -->
<div id="historyModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-md flex justify-end transition-opacity duration-300">
    <div class="w-full max-w-2xl bg-slate-900 border-l border-slate-800 h-full flex flex-col justify-between shadow-2xl">
        <!-- Modal Header -->
        <div class="p-5 border-b border-slate-800 bg-slate-900 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-black text-white">Riwayat Presensi QR Hari Ini</h2>
                    <p class="text-slate-400 text-xs font-medium">Data Terupdate Secara Real-Time Tanpa Reload</p>
                </div>
            </div>
            <button onclick="closeHistoryModal()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Filter Search Bar -->
        <div class="p-4 border-b border-slate-800/80 bg-slate-950/60 flex space-x-3">
            <div class="relative flex-1">
                <input type="text" id="historySearchInput" oninput="filterHistoryList()" placeholder="Cari nama siswa atau NISN..."
                    class="w-full pl-9 pr-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs font-bold text-white placeholder-slate-500 focus:ring-2 focus:ring-emerald-500">
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button onclick="loadRealtimeHistory()" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-emerald-400 rounded-xl font-bold text-xs flex items-center space-x-1.5 border border-slate-700 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>

        <!-- History Records Table List -->
        <div class="p-5 flex-1 overflow-y-auto space-y-3" id="historyModalBody">
            <div class="text-center py-12 text-slate-500 text-xs">
                Memuat data riwayat...
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-950 flex items-center justify-between text-xs text-slate-400">
            <span>Total Records: <strong class="text-white" id="modalTotalRecords">0</strong> Siswa</span>
            <button onclick="closeHistoryModal()" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-bold">Tutup</button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 space-y-3"></div>

<script src="https://unpkg.com/jsqr/dist/jsQR.js"></script>
<script>
    let video = document.getElementById('video');
    let startCameraBtn = document.getElementById('startCamera');
    let stopCameraBtn = document.getElementById('stopCamera');
    let manualQrInput = document.getElementById('manualQrInput');
    let stream = null;
    let scanning = false;
    let lastScanTime = 0;
    let recentScans = [];
    let allHistoryRecords = [];

    // Live Clock Update
    function updateClock() {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('liveClock').textContent = `${hrs}:${mins}:${secs} WIB`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    if (startCameraBtn) startCameraBtn.addEventListener('click', startCamera);
    if (stopCameraBtn) stopCameraBtn.addEventListener('click', stopCamera);

    // Fullscreen Toggle
    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                showMessage('Layar penuh tidak didukung di browser ini', 'error');
            });
            document.getElementById('fullScreenBtnText').textContent = 'Keluar Layar Penuh';
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
            document.getElementById('fullScreenBtnText').textContent = 'Layar Penuh';
        }
    }

    // Modal Control & Realtime History Loading
    function openHistoryModal() {
        document.getElementById('historyModal').classList.remove('hidden');
        loadRealtimeHistory();
    }

    function closeHistoryModal() {
        document.getElementById('historyModal').classList.add('hidden');
    }

    function loadRealtimeHistory() {
        fetch('{{ route("admin.qr-attendance.summary") }}')
            .then(res => res.json())
            .then(data => {
                if (data.attendances) {
                    allHistoryRecords = data.attendances;
                    renderHistoryList(allHistoryRecords);
                }
            });
    }

    function renderHistoryList(records) {
        const body = document.getElementById('historyModalBody');
        document.getElementById('modalTotalRecords').textContent = records.length;

        if (records.length === 0) {
            body.innerHTML = `
                <div class="text-center py-12 text-slate-500 text-xs font-semibold">
                    Belum ada data presensi hari ini.
                </div>
            `;
            return;
        }

        body.innerHTML = records.map(att => {
            const studentName = att.student?.user?.name || 'Siswa';
            const studentNisn = att.student?.nisn || '-';
            const className = att.student?.class?.name || '-';
            const statusLabel = att.status === 'present' ? 'Hadir' : (att.status === 'late' ? 'Terlambat' : att.status);
            const statusBg = att.status === 'present' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border-amber-500/30';

            return `
                <div class="p-3.5 rounded-xl bg-slate-950/80 border border-slate-800/80 flex items-center justify-between shadow-sm">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 text-white flex items-center justify-center font-black text-sm shrink-0">
                            ${studentName.charAt(0)}
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-xs leading-snug">${studentName}</h4>
                            <p class="text-[11px] text-slate-400 font-semibold mt-0.5">NISN: ${studentNisn} • Kelas ${className}</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-black border uppercase tracking-wider ${statusBg}">
                            ${statusLabel}
                        </span>
                    </div>
                </div>
            `;
        }).join('');
    }

    function filterHistoryList() {
        const query = document.getElementById('historySearchInput').value.toLowerCase();
        const filtered = allHistoryRecords.filter(att => {
            const name = (att.student?.user?.name || '').toLowerCase();
            const nisn = (att.student?.nisn || '').toLowerCase();
            return name.includes(query) || nisn.includes(query);
        });
        renderHistoryList(filtered);
    }

    function startCamera() {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function(s) {
                stream = s;
                video.srcObject = stream;
                video.setAttribute('playsinline', true);
                video.play();
                scanning = true;
                startCameraBtn.disabled = true;
                stopCameraBtn.disabled = false;
                requestAnimationFrame(tick);
            })
            .catch(function(err) {
                showMessage('Gagal mengakses kamera: ' + err.message, 'error');
            });
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            video.srcObject = null;
        }
        scanning = false;
        startCameraBtn.disabled = false;
        stopCameraBtn.disabled = true;
    }

    function tick() {
        if (video.readyState === video.HAVE_ENOUGH_DATA && scanning) {
            let canvas = document.createElement('canvas');
            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            let ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            let code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: 'dontInvert',
            });

            if (code && code.data) {
                processScan(code.data);
            }
        }
        if (scanning) {
            requestAnimationFrame(tick);
        }
    }

    function submitManualScan() {
        const val = manualQrInput.value.trim();
        if (!val) {
            showMessage('Masukkan NISN atau kode QR terlebih dahulu', 'error');
            return;
        }
        processScan(val);
        manualQrInput.value = '';
    }

    function processScan(qrData) {
        const now = Date.now();
        if (now - lastScanTime < 2500) return;
        lastScanTime = now;

        const status = document.querySelector('input[name="status"]:checked').value;

        fetch('{{ route("admin.qr-attendance.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_data: qrData, status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(`${data.message} - ${data.student.name} (Kelas ${data.student.class})`, 'success');
                playSuccessSound(data.student.name);
                addRecentScan(data.student, data.attendance);
                // Update history if modal open
                if (!document.getElementById('historyModal').classList.contains('hidden')) {
                    loadRealtimeHistory();
                }
            } else {
                showMessage(data.message, 'error');
                playWarningSound();
            }
            loadTodaySummary();
        })
        .catch(err => {
            showMessage('Terjadi kesalahan koneksi', 'error');
        });
    }

    function addRecentScan(student, attendance) {
        recentScans.unshift({
            name: student.name,
            nisn: student.nisn,
            class: student.class,
            time: attendance.time,
            status: attendance.status
        });
        if (recentScans.length > 5) recentScans.pop();
        renderRecentScans();
    }

    function renderRecentScans() {
        const container = document.getElementById('recentScansList');
        if (recentScans.length === 0) return;

        container.innerHTML = recentScans.map(item => `
            <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg ${item.status === 'present' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30'} flex items-center justify-center font-extrabold text-xs shrink-0">
                        ${item.name.charAt(0)}
                    </div>
                    <div>
                        <p class="font-extrabold text-white text-xs leading-snug line-clamp-1">${item.name}</p>
                        <p class="text-[10px] text-slate-400 font-semibold">NISN: ${item.nisn} • Kelas ${item.class}</p>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md ${item.status === 'present' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400'}">
                        ${item.time}
                    </span>
                </div>
            </div>
        `).join('');
    }

    function loadTodaySummary() {
        fetch('{{ route("admin.qr-attendance.summary") }}')
            .then(res => res.json())
            .then(data => {
                if (data.summary) {
                    document.getElementById('totalToday').textContent = data.summary.qr_scans || data.summary.total || 0;
                }
            });
    }

    function showMessage(msg, type) {
        const toast = document.createElement('div');
        toast.className = `px-5 py-3.5 rounded-xl font-extrabold text-xs text-white shadow-2xl transition-all duration-300 ${type === 'success' ? 'bg-emerald-600 border border-emerald-400/40' : 'bg-rose-600 border border-rose-400/40'}`;
        toast.textContent = msg;
        document.getElementById('toastContainer').appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    function playSuccessSound(studentName) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.15);
        } catch(e){}

        @if(Setting::get('attendance_sound_feedback', '1') == '1')
        if ('speechSynthesis' in window && studentName) {
            const utterance = new SpeechSynthesisUtterance("Selamat Pagi, " + studentName + ". Kehadiran berhasil dicatat.");
            utterance.lang = 'id-ID';
            window.speechSynthesis.speak(utterance);
        }
        @endif
    }

    function playWarningSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.setValueAtTime(440, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(300, ctx.currentTime + 0.2);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.2);
        } catch(e){}
    }

    loadTodaySummary();
</script>

@push('styles')
<style>
    /* Custom High-Tech Scanner Animation */
    @keyframes scan-laser {
        0% { top: 2%; opacity: 0.3; }
        50% { top: 98%; opacity: 1; }
        100% { top: 2%; opacity: 0.3; }
    }
    .laser-scan-line {
        position: absolute;
        animation: scan-laser 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        box-shadow: 0 0 15px 4px rgba(16, 185, 129, 0.6), 0 0 6px 1px rgba(255, 255, 255, 0.9);
    }
    
    @keyframes scan-sweep {
        0% { transform: translateY(-100%); }
        50% { transform: translateY(100%); }
        100% { transform: translateY(-100%); }
    }
    .laser-sweep-area {
        animation: scan-sweep 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }

    .scan-grid-bg {
        background-size: 24px 24px;
        background-image: 
            linear-gradient(to right, rgba(16, 185, 129, 0.06) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(16, 185, 129, 0.06) 1px, transparent 1px);
    }
</style>
@endpush
@endsection
