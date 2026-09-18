@extends('layouts.student-mobile')

@section('title', 'Profil Saya')
@section('header_title', 'Profil Saya')

@php
    use Illuminate\Support\Facades\File;
    $user = auth()->user();
    $student = $user->student;
    $photoDoc = null;
@endphp

@section('content')
<div class="space-y-4 sm:space-y-6" x-data="{ 
    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    },
    photoSelected: false,
    checkPhoto(event) {
        this.photoSelected = event.target.files.length > 0;
        this.previewImage(event);
    }
}">



    <!-- Main Container: Asymmetric 1/3 (Avatar & Digital Card) + 2/3 (Form Fields) on Desktop (PC) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

        <!-- LEFT SIDEBAR COLUMN: Profile Card & Digital Student ID Card (1/3 width on PC) -->
        <div class="lg:col-span-1 space-y-4 sm:space-y-6">
            
            <!-- Kartu Tanda Siswa / Kartu Nama Digital + QR Code -->
            <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xl relative overflow-hidden border border-indigo-500/30">
                <!-- Background Glows -->
                <div class="absolute -top-10 -right-10 w-36 h-36 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-10 -left-10 w-36 h-36 bg-blue-500/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10">
                    <!-- Header Kartu -->
                    <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-white/10">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            @if(Setting::get('logo_path'))
                                <img src="{{ asset(Setting::get('logo_path')) }}" alt="Logo" class="h-7 w-7 sm:h-8 sm:w-8 rounded-lg object-contain bg-white/10 p-0.5 border border-white/20 shrink-0">
                            @else
                                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-7 w-7 sm:h-8 sm:w-8 rounded-lg object-cover bg-white/10 p-0.5 border border-white/20 shrink-0">
                            @endif
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-white tracking-tight leading-tight truncate">{{ Setting::get('school_name', 'School') }}</h4>
                                <p class="text-[9px] text-indigo-300 font-extrabold uppercase tracking-widest truncate">KARTU TANDA SISWA</p>
                            </div>
                        </div>
                        <span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 flex items-center gap-1 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Aktif
                        </span>
                    </div>

                    <!-- Body Kartu: QR Code + Bio Info -->
                    <div class="flex items-center gap-3 bg-white/5 backdrop-blur-md rounded-2xl p-3 border border-white/10 mb-3.5">
                        <!-- QR Code Box (Click to Enlarge) -->
                        <div class="bg-white p-1.5 sm:p-2 rounded-xl shrink-0 cursor-pointer shadow-md group relative overflow-hidden flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20" onclick="openQrModal()" title="Klik untuk memperbesar QR Code">
                            @if(isset($qrCode) && $qrCode)
                                <div class="w-full h-full flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                                    {!! $qrCode !!}
                                </div>
                            @elseif($student?->qr_code_path)
                                <img src="{{ \Illuminate\Support\Str::startsWith($student->qr_code_path, 'img/') ? asset($student->qr_code_path) : asset('img/' . $student->qr_code_path) }}" alt="QR Code" class="w-full h-full object-contain group-hover:scale-105 transition-transform">
                            @else
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($student?->nisn ?? $user->email) }}" alt="QR Code" class="w-full h-full object-contain group-hover:scale-105 transition-transform">
                            @endif
                            <div class="absolute inset-0 bg-indigo-950/50 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>

                        <!-- Data Identitas Siswa -->
                        <div class="min-w-0 flex-1 space-y-0.5">
                            <h3 class="font-extrabold text-xs sm:text-sm text-white truncate">{{ $user->name }}</h3>
                            <p class="text-[10px] sm:text-[11px] text-indigo-200 font-medium truncate">NISN: <span class="font-mono text-white font-bold">{{ $student?->nisn ?? '-' }}</span></p>
                            <p class="text-[10px] sm:text-[11px] text-slate-300 truncate">Kelas: <span class="font-bold text-white">{{ $student?->class?->name ?? 'Belum ada kelas' }}</span></p>
                            <p class="text-[9px] sm:text-[10px] text-slate-400 truncate">{{ $user->email }}</p>
                        </div>
                    </div>

                    <!-- Tombol Aksi Perbesar QR Code -->
                    <button type="button" onclick="openQrModal()" class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center space-x-2 border border-white/20 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        <span>Perbesar QR Code Absensi</span>
                    </button>
                </div>
            </div>

            <!-- Profile Photo & Summary Card -->
            <div class="bg-white dark:bg-slate-800/90 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-xs border border-slate-200 dark:border-slate-700">
                <div class="text-center">
                    
                    <!-- Profile Photo Form -->
                    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="flex flex-col items-center">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        
                        <div class="relative mb-3.5 group">
                            @if($student?->photo_url)
                                <img id="profile-preview" src="{{ $student->photo_url }}" alt="Profile" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl sm:rounded-3xl object-cover border-4 border-slate-100 dark:border-slate-700 shadow-md">
                            @else
                                <img id="profile-preview" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=ffffff&size=256" alt="Profile" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl sm:rounded-3xl object-cover border-4 border-slate-100 dark:border-slate-700 shadow-md">
                            @endif
                            
                            <label for="profile-photo" class="absolute -bottom-1.5 -right-1.5 w-8 h-8 sm:w-9 sm:h-9 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl flex items-center justify-center cursor-pointer shadow-md transition-all border-2 border-white dark:border-slate-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </label>
                            <input type="file" id="profile-photo" name="photo" accept="image/*,.jfif" class="hidden" @change="checkPhoto($event)">
                        </div>

                        <button type="submit" 
                                x-show="photoSelected" 
                                x-cloak
                                class="mb-3 px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Foto Baru</span>
                        </button>
                    </form>

                    <!-- Student Summary Info -->
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-base sm:text-lg leading-snug truncate">{{ $user->name }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $user->email }}</p>

                    <div class="flex flex-wrap justify-center gap-1.5 sm:gap-2 mt-3">
                        <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 rounded-full text-[11px] sm:text-xs font-bold">
                            Kelas {{ $student?->class?->name ?? 'Belum ada kelas' }}
                        </span>
                        <span class="px-2.5 py-0.5 sm:px-3 sm:py-1 bg-slate-100 dark:bg-slate-700/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-full text-[11px] sm:text-xs font-bold font-mono">
                            NISN: {{ $student?->nisn ?? 'Belum diisi' }}
                        </span>
                    </div>

                    <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-700 space-y-2 text-left text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-700/50">
                            <span class="text-slate-400 dark:text-slate-400">Status Akun:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">Aktif</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-700/50">
                            <span class="text-slate-400 dark:text-slate-400">Nomor Telepon:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $student?->phone ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-400 dark:text-slate-400">Jenis Kelamin:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ ($student?->gender ?? '') === 'male' ? 'Laki-laki' : (($student?->gender ?? '') === 'female' ? 'Perempuan' : '-') }}</span>
                        </div>
                    </div>

                    <!-- Logout Button under Profile Card -->
                    <form id="profile-logout-form" method="POST" action="{{ route('logout') }}" class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-700">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 font-bold text-xs rounded-xl transition-all border border-rose-200 dark:border-rose-800 flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Keluar dari Akun (Logout)</span>
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <!-- RIGHT FORM COLUMN: Detail Data Inputs (2/3 width on PC) -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="space-y-4 sm:space-y-6" x-data="{ loading: false }">
                @csrf
                @method('PUT')

                <!-- 1. Informasi Pribadi Card -->
                <div class="bg-white dark:bg-slate-800/90 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center mb-4 sm:mb-5 pb-2.5 sm:pb-3 border-b border-slate-100 dark:border-slate-700">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center mr-3 border border-indigo-100 dark:border-indigo-800 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm">Informasi Pribadi Siswa</h3>
                            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-400">Data utama identitas siswa terdaftar</p>
                        </div>
                    </div>

                    <div class="space-y-3 sm:space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                            </div>
                            <div>
                                <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">Alamat Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">NISN (Nomor Induk Siswa)</label>
                                <input type="text" name="nisn" value="{{ old('nisn', $student?->nisn ?? '') }}" placeholder="Nomor NISN" class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                            </div>
                            <div>
                                <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">NIK (KTP / KK)</label>
                                <input type="text" name="nik" value="{{ old('nik', $student?->nik ?? '') }}" placeholder="Nomor NIK KTP/KK" class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Kontak & Domisili Card -->
                <div class="bg-white dark:bg-slate-800/90 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center mb-4 sm:mb-5 pb-2.5 sm:pb-3 border-b border-slate-100 dark:border-slate-700">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center mr-3 border border-blue-100 dark:border-blue-800 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm">Kontak & Alamat Domisili</h3>
                            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-400">Nomor kontak pribadi dan alamat tinggal</p>
                        </div>
                    </div>

                    <div class="space-y-3 sm:space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">No. Telepon / WA Siswa</label>
                                <input type="text" name="phone" value="{{ old('phone', $student?->phone ?? $user->phone ?? '') }}" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                            </div>
                            <div>
                                <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">Jenis Kelamin</label>
                                <select name="gender" class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="male" {{ ($student?->gender ?? '') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ ($student?->gender ?? '') === 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <div>
                                <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">Tempat Lahir</label>
                                <input type="text" name="birth_place" value="{{ old('birth_place', $student?->birth_place ?? '') }}" placeholder="Kota/Kabupaten" class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                            </div>
                            <div>
                                <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">Tanggal Lahir</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date', $student?->birth_date?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">Alamat Lengkap Tempat Tinggal</label>
                            <textarea name="address" rows="3" placeholder="Masukkan alamat lengkap rumah/domisili" class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all resize-none">{{ old('address', $student?->address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 3. Informasi Orang Tua Card -->
                <div class="bg-white dark:bg-slate-800/90 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center mb-4 sm:mb-5 pb-2.5 sm:pb-3 border-b border-slate-100 dark:border-slate-700">
                        <div class="w-8 h-8 sm:w-9 sm:h-9 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center mr-3 border border-emerald-100 dark:border-emerald-800 shrink-0">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm">Informasi Orang Tua / Wali</h3>
                            <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-400">Kontak darurat dan nama orang tua/wali siswa</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">Nama Orang Tua / Wali</label>
                            <input type="text" name="parent_name" value="{{ old('parent_name', $student?->parent_name ?? '') }}" placeholder="Nama lengkap orang tua" class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] sm:text-xs font-bold text-slate-600 dark:text-slate-300 mb-1 uppercase tracking-wider">No. Telepon / WA Orang Tua</label>
                            <input type="text" name="parent_phone" value="{{ old('parent_phone', $student?->parent_phone ?? '') }}" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Submit & Logout Button Bar -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 pt-2">
                    <button type="button" 
                            onclick="document.getElementById('profile-logout-form').submit()" 
                            class="w-full sm:w-auto px-5 py-3 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 font-bold text-xs rounded-xl sm:rounded-2xl transition-all border border-rose-200 dark:border-rose-800 flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Keluar dari Akun</span>
                    </button>

                    <button type="submit" 
                            @click="setTimeout(() => loading = true, 500)" 
                            :disabled="loading" 
                            class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl sm:rounded-2xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer border border-indigo-400/30">
                        <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="loading ? 'Memproses...' : 'Simpan Perubahan Profil'"></span>
                    </button>
                </div>

            </form>
        </div>

    </div>

</div>

<!-- QR Code Full Screen Modal (Kartu Tanda Siswa / Name Card Digital Lengkap) -->
<div id="qrModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 hidden flex items-center justify-center p-4 transition-all duration-300" onclick="closeQrModalOutside(event)">
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col transform transition-all border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100" onclick="event.stopPropagation()">
        
        <!-- Header Kartu Pelajar (Header Kop Sekolah) -->
        <div class="bg-gradient-to-r from-indigo-900 via-indigo-850 to-slate-900 text-white p-4 sm:p-5 relative overflow-hidden shrink-0">
            <!-- Background Glow & Pattern -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
            
            <!-- Close Button -->
            <button type="button" onclick="closeQrModal()" class="absolute top-3.5 right-3.5 z-20 w-8 h-8 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-all border border-white/20 backdrop-blur-sm cursor-pointer" title="Tutup">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="flex items-center space-x-3 pr-8">
                @if(Setting::get('logo_path'))
                    <img src="{{ asset(Setting::get('logo_path')) }}" alt="Logo" class="h-10 w-10 sm:h-11 sm:w-11 rounded-xl object-contain bg-white/10 p-1 border border-white/20 shrink-0 shadow-md">
                @else
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="h-10 w-10 sm:h-11 sm:w-11 rounded-xl object-cover bg-white/10 p-1 border border-white/20 shrink-0 shadow-md">
                @endif
                <div class="min-w-0">
                    <h3 class="font-extrabold text-xs sm:text-sm text-white tracking-tight leading-snug truncate">{{ Setting::get('school_name', 'Sekolah') }}</h3>
                    <p class="text-[9px] text-indigo-200 font-extrabold uppercase tracking-widest mt-0.5">KARTU TANDA SISWA DIGITAL</p>
                </div>
            </div>
        </div>

        <!-- Body Kartu Nama Siswa -->
        <div class="p-4 sm:p-5 text-center space-y-3.5 sm:space-y-4">
            
            <!-- Profil Foto + Informasi Identitas Siswa -->
            <div class="bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-3.5 sm:p-4 flex items-center gap-3 text-left shadow-xs">
                <div class="relative shrink-0">
                    @if($student?->photo_url)
                        <img src="{{ $student->photo_url }}" alt="Foto Siswa" class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl object-cover border-2 border-white dark:border-slate-700 shadow-md">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=ffffff&size=160" alt="Foto Siswa" class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl object-cover border-2 border-white dark:border-slate-700 shadow-md">
                    @endif
                    <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 sm:w-4 sm:h-4 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full flex items-center justify-center" title="Status Aktif"></span>
                </div>

                <div class="min-w-0 flex-1 space-y-0.5">
                    <h4 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm truncate leading-tight">{{ $user->name }}</h4>
                    <p class="text-[10px] sm:text-[11px] text-indigo-600 dark:text-indigo-400 font-bold font-mono">NISN: {{ $student?->nisn ?? '-' }}</p>
                    <div class="flex items-center gap-1.5 pt-0.5 flex-wrap">
                        <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 text-[9px] sm:text-[10px] font-bold rounded-md">
                            {{ $student?->class?->name ?? 'Tanpa Kelas' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Box QR Code Presensi Vector -->
            <div class="bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 rounded-2xl p-3.5 sm:p-4 inline-block shadow-xs w-full">
                <div class="bg-white dark:bg-slate-950 p-2.5 sm:p-3 rounded-xl border border-slate-200 dark:border-slate-800 inline-block shadow-sm">
                    @if(isset($qrCode) && $qrCode)
                        <div class="w-40 h-40 sm:w-48 sm:h-48 mx-auto flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:mx-auto">
                            {!! $qrCode !!}
                        </div>
                    @elseif($student?->qr_code_path)
                        <img src="{{ \Illuminate\Support\Str::startsWith($student->qr_code_path, 'img/') ? asset($student->qr_code_path) : asset('img/' . $student->qr_code_path) }}" alt="QR Code Absensi" class="w-40 h-40 sm:w-48 sm:h-48 object-contain mx-auto">
                    @else
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($student?->nisn ?? $user->email) }}" alt="QR Code Absensi" class="w-40 h-40 sm:w-48 sm:h-48 object-contain mx-auto">
                    @endif
                </div>

                <div class="mt-2">
                    <span class="inline-block px-3 py-0.5 sm:py-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-mono font-bold text-[11px] sm:text-xs rounded-full shadow-xs">
                        NISN: {{ $student?->nisn ?? '-' }}
                    </span>
                    <p class="text-[9px] sm:text-[10px] text-slate-400 font-medium mt-1">Scan QR Code ini untuk Presensi Kehadiran</p>
                </div>
            </div>

            <!-- Footer Badge Validitas Kartu -->
            <div class="flex items-center justify-between text-[9px] sm:text-[10px] text-slate-400 px-1 pt-1 border-t border-slate-100 dark:border-slate-800">
                <span class="flex items-center gap-1 font-semibold text-slate-500 dark:text-slate-400">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Kartu Pelajar Sah & Aktif
                </span>
                <span class="font-mono text-slate-400">T.A {{ date('Y') }}/{{ date('Y')+1 }}</span>
            </div>

        </div>

        <!-- Footer Action Button -->
        <div class="px-4 sm:px-5 pb-4 sm:pb-5 pt-0">
            <button type="button" onclick="closeQrModal()" class="w-full py-2.5 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-sm cursor-pointer border border-slate-700">
                Tutup Kartu
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function openQrModal() {
        document.getElementById('qrModal').classList.remove('hidden');
    }
    function closeQrModal() {
        document.getElementById('qrModal').classList.add('hidden');
    }
    function closeQrModalOutside(e) {
        if (e.target.id === 'qrModal') {
            closeQrModal();
        }
    }
</script>
@endpush
@endsection
