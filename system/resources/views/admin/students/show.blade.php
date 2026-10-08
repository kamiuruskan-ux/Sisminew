@extends('layouts.admin')

@section('title', 'Detail Siswa - ' . ($student->user->name ?? 'Siswa'))
@section('page_title', 'Detail Data Siswa')

@section('content')
<div class="space-y-6 w-full pb-16"
     x-data="{
         showPasswordModal: false,
         showCredentialsModal: false,
         newPassword: '',
         newPin: '',
         generatePassword() {
             const chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
             let res = 'SISWA';
             for (let i = 0; i < 4; i++) {
                 res += chars.charAt(Math.floor(Math.random() * chars.length));
             }
             this.newPassword = res;
         },
         generatePin() {
             let pin = '';
             for (let i = 0; i < 6; i++) {
                 pin += Math.floor(Math.random() * 10);
             }
             this.newPin = pin;
         }
     }"
     @if(session('open_wa_url'))
     x-init="setTimeout(() => window.open('{{ session('open_wa_url') }}', '_blank'), 300)"
     @endif
>
    <!-- Header Navigation & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#1A222C] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.students.index') }}" 
               class="p-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-black text-[#1C2434] dark:text-white tracking-tight">{{ $student->user->name ?? 'Siswa' }}</h1>
                <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">
                    NISN: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $student->nisn ?? '-' }}</span> | 
                    NIK: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $student->nik ?? '-' }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.students.print-card', encode_id($student->id)) }}" target="_blank"
               class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 00-2 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Kartu</span>
            </a>
            <a href="{{ route('admin.students.edit', encode_id($student->id)) }}"
               class="inline-flex items-center space-x-2 px-4 py-2.5 bg-[#3C50E0] hover:bg-[#3C50E0]/90 text-white font-bold text-xs rounded-xl transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Data</span>
            </a>
        </div>
    </div>

    <!-- Main Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="bg-white dark:bg-[#1A222C] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
            <div class="flex flex-col items-center text-center">
                <div class="relative w-28 h-28 mb-4">
                    @if($student->photo)
                        <img src="{{ \Illuminate\Support\Str::startsWith($student->photo, 'img/') ? asset($student->photo) : asset('img/students/' . $student->photo) }}" 
                             alt="{{ $student->user->name ?? 'Foto Siswa' }}" 
                             class="w-28 h-28 rounded-2xl object-cover border-2 border-slate-100 dark:border-slate-700 shadow-sm">
                    @else
                        <div class="w-28 h-28 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 border-2 border-indigo-100 dark:border-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black text-3xl">
                            {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                </div>

                <h2 class="text-lg font-bold text-[#1C2434] dark:text-white">{{ $student->user->name ?? '-' }}</h2>
                <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">{{ $student->user->email ?? '-' }}</p>

                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-lg border border-indigo-100 dark:border-indigo-900/50">
                        {{ $student->class->name ?? 'Tanpa Kelas' }}
                    </span>
                    @if($student->major)
                        <span class="px-3 py-1 bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 text-xs font-bold rounded-lg border border-purple-100 dark:border-purple-900/50">
                            {{ $student->major->name }}
                        </span>
                    @endif
                    <span class="px-3 py-1 {{ $student->gender === 'male' ? 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400 border-blue-100 dark:border-blue-900/50' : 'bg-pink-50 text-pink-600 dark:bg-pink-950/50 dark:text-pink-400 border-pink-100 dark:border-pink-900/50' }} text-xs font-bold rounded-lg border">
                        {{ $student->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>
            </div>

            <hr class="border-slate-100 dark:border-slate-800">

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        <span>Akun & Kredensial Login</span>
                    </h3>
                    @if($student->user?->isLockedOut())
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-800 animate-pulse">
                            Terkunci
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50">
                            Aktif
                        </span>
                    @endif
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/50">
                        <span class="text-[#64748B] dark:text-[#8A99AD]">Email / Username:</span>
                        <span class="font-mono font-bold text-[#1C2434] dark:text-white truncate max-w-[150px]">{{ $student->user->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/50">
                        <span class="text-[#64748B] dark:text-[#8A99AD]">No. HP Siswa:</span>
                        <span class="font-bold text-[#1C2434] dark:text-white">{{ $student->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/50">
                        <span class="text-[#64748B] dark:text-[#8A99AD]">No. HP Wali:</span>
                        <span class="font-bold text-[#1C2434] dark:text-white">{{ $student->parent_phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/50">
                        <span class="text-[#64748B] dark:text-[#8A99AD]">PIN Transaksi:</span>
                        <span class="font-bold text-[#1C2434] dark:text-white">
                            {{ $student->pin ? 'Sudah Diatur' : 'Belum Diatur' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-[#64748B] dark:text-[#8A99AD]">Terakhir Login:</span>
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $student->user?->last_login_at ? $student->user->last_login_at->diffForHumans() : 'Belum pernah' }}</span>
                    </div>
                </div>

                @if($student->user?->isLockedOut())
                    <form action="{{ route('admin.students.unlock', encode_id($student->id)) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                            <span>Buka Kunci Akun Siswa</span>
                        </button>
                    </form>
                @endif

                <div class="pt-2 flex flex-col gap-2">
                    <button type="button" @click="showPasswordModal = true"
                            class="w-full py-2.5 px-3 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white dark:bg-indigo-950/40 dark:text-indigo-300 dark:hover:bg-indigo-600 dark:hover:text-white border border-indigo-200/80 dark:border-indigo-800 rounded-xl transition-all font-bold text-xs flex items-center justify-center space-x-1.5 shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        <span>Reset Password & PIN</span>
                    </button>
                    <button type="button" @click="showCredentialsModal = true"
                            class="w-full py-2.5 px-3 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white dark:bg-blue-950/40 dark:text-blue-300 dark:hover:bg-blue-600 dark:hover:text-white border border-blue-200/80 dark:border-blue-800 rounded-xl transition-all font-bold text-xs flex items-center justify-center space-x-1.5 shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Ubah Kredensial (Email/No HP)</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Detail Data Tabs/Sections -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Pribadi & Akademik -->
            <div class="bg-white dark:bg-[#1A222C] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-[#1C2434] dark:text-white flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Informasi Pribadi & Akademik</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-slate-50 dark:bg-[#24303F] rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <p class="text-slate-400 font-medium">NISN</p>
                        <p class="font-bold text-[#1C2434] dark:text-white mt-1 text-sm">{{ $student->nisn ?? '-' }}</p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-[#24303F] rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <p class="text-slate-400 font-medium">NIK</p>
                        <p class="font-bold text-[#1C2434] dark:text-white mt-1 text-sm">{{ $student->nik ?? '-' }}</p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-[#24303F] rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <p class="text-slate-400 font-medium">Tempat, Tanggal Lahir</p>
                        <p class="font-bold text-[#1C2434] dark:text-white mt-1">
                            {{ $student->birth_place ?? '-' }}{{ $student->birth_date ? ', ' . $student->birth_date->translatedFormat('d F Y') : '' }}
                        </p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-[#24303F] rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <p class="text-slate-400 font-medium">Kelas / Rombel</p>
                        <p class="font-bold text-[#1C2434] dark:text-white mt-1">{{ $student->class->name ?? '-' }}</p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-[#24303F] rounded-xl border border-slate-100 dark:border-slate-700/50 md:col-span-2">
                        <p class="text-slate-400 font-medium">Alamat Tempat Tinggal</p>
                        <p class="font-bold text-[#1C2434] dark:text-white mt-1 leading-relaxed">{{ $student->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Orang Tua / Wali & Keuangan -->
            <div class="bg-white dark:bg-[#1A222C] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-[#1C2434] dark:text-white flex items-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Informasi Orang Tua & Beasiswa/SPP</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-slate-50 dark:bg-[#24303F] rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <p class="text-slate-400 font-medium">Nama Orang Tua / Wali</p>
                        <p class="font-bold text-[#1C2434] dark:text-white mt-1">{{ $student->parent_name ?? '-' }}</p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-[#24303F] rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <p class="text-slate-400 font-medium">No. HP Orang Tua / Wali</p>
                        <p class="font-bold text-[#1C2434] dark:text-white mt-1">{{ $student->parent_phone ?? '-' }}</p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-[#24303F] rounded-xl border border-slate-100 dark:border-slate-700/50">
                        <p class="text-slate-400 font-medium">Potongan SPP</p>
                        <p class="font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                            Rp {{ number_format($student->spp_discount ?? 0, 0, ',', '.') }}
                        </p>
                    </div>

            <!-- Data Riwayat Capaian Al-Qur'an (Tahsin & Tahfidz) -->
            <div class="bg-white dark:bg-[#1A222C] p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-[#1C2434] dark:text-white flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Riwayat Capaian Halaqah &amp; Tahfidz Al-Qur'an</span>
                    </h3>
                    <a href="{{ route('admin.quran-raport.print', ['student_id' => encrypt_id($student->id)]) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold text-xs rounded-xl border border-emerald-200 dark:border-emerald-800 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Raport Al-Qur'an</span>
                    </a>
                </div>

                @php
                    $halaqahItems = $student->halaqahRecords()->take(5)->get();
                @endphp

                @if($halaqahItems->isNotEmpty())
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($halaqahItems as $item)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-800 dark:text-white">{{ $item->assessment_date->format('d/m/Y') }}</span>
                                <span class="mx-1.5 text-slate-300">•</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item->program_type === 'tahfidz' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400' : 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400' }}">
                                    {{ strtoupper($item->program_type) }}
                                </span>
                                <p class="text-slate-500 dark:text-slate-400 mt-0.5">{{ $item->material_summary }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-black text-slate-900 dark:text-white">{{ $item->score_cognitive }}</span>
                                <span class="block text-[10px] font-bold text-emerald-600 dark:text-emerald-400">{{ $item->predicate }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 bg-slate-50 dark:bg-[#24303F] rounded-xl text-center text-xs text-slate-400">
                        Belum ada catatan evaluasi halaqah untuk santri ini.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Reset Password & PIN Siswa -->
    <div x-show="showPasswordModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" @click="showPasswordModal = false"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-md transform rounded-3xl bg-white dark:bg-boxdark p-6 sm:p-8 text-left shadow-2xl transition-all border border-slate-200 dark:border-strokedark"
                 @click.away="showPasswordModal = false"
                 x-data="{ showPassPlain: false }">

                <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-2xl border border-indigo-100 dark:border-indigo-800">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>

                <h3 class="text-lg font-extrabold text-center text-slate-900 dark:text-white mb-1">Reset Password & PIN Siswa</h3>
                <p class="text-xs text-center text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">Atur ulang kata sandi login santri: <strong class="text-slate-800 dark:text-slate-200">{{ $student->user->name }}</strong></p>

                <form action="{{ route('admin.students.reset-password', encode_id($student->id)) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Password Baru <span class="text-rose-500">*</span></label>
                            <button type="button" @click="generatePassword()" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Acak Password</span>
                            </button>
                        </div>
                        <div class="relative">
                            <input :type="showPassPlain ? 'text' : 'password'" 
                                   name="password" 
                                   x-model="newPassword"
                                   required minlength="8"
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-boxdark-2 border border-slate-200 dark:border-strokedark rounded-xl text-center font-mono text-sm font-bold text-slate-800 dark:text-slate-200 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition"
                                   placeholder="Minimal 8 karakter">
                            <button type="button" @click="showPassPlain = !showPassPlain" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-[11px] font-bold">
                                <span x-text="showPassPlain ? 'Tutup' : 'Lihat'"></span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">PIN Transaksi/Absen (Opsional)</label>
                            <button type="button" @click="generatePin()" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 cursor-pointer">
                                <span>Acak 6 Digit</span>
                            </button>
                        </div>
                        <input type="text" 
                               name="pin" 
                               x-model="newPin"
                               maxlength="6"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-boxdark-2 border border-slate-200 dark:border-strokedark rounded-xl text-center font-mono text-sm font-bold text-emerald-700 dark:text-emerald-300 tracking-widest focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition"
                               placeholder="6 digit angka (opsional)">
                        <p class="text-[10px] text-slate-400 mt-1 italic">Kosongkan jika tidak ingin mengubah PIN transaksi santri saat ini.</p>
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-boxdark-2 rounded-xl border border-slate-100 dark:border-strokedark space-y-1.5 text-xs">
                        <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="send_wa" value="1" checked class="w-3.5 h-3.5 rounded text-indigo-600">
                            <span>Kirim kredensial baru ke WhatsApp Siswa / Wali</span>
                        </label>
                        <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="open_wa" value="1" class="w-3.5 h-3.5 rounded text-indigo-600">
                            <span>Buka chat WhatsApp langsung setelah menyimpan</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button type="button" @click="showPasswordModal = false"
                                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-strokedark text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md transition-all cursor-pointer">
                            Simpan Password Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kredensial Siswa (Email / No HP / Nama) -->
    <div x-show="showCredentialsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" @click="showCredentialsModal = false"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-lg transform rounded-3xl bg-white dark:bg-boxdark p-6 sm:p-8 text-left shadow-2xl transition-all border border-slate-200 dark:border-strokedark"
                 @click.away="showCredentialsModal = false">
                
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900 dark:text-white">Ubah Kredensial Akun Siswa</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui email login, nama pengguna, dan nomor telepon</p>
                        </div>
                    </div>
                    <button type="button" @click="showCredentialsModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admin.students.update-credentials', encode_id($student->id)) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Nama Lengkap Siswa</label>
                        <input type="text" name="name" value="{{ old('name', $student->user->name) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-boxdark-2 border border-slate-200 dark:border-strokedark rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Email (Username Login Siswa) <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $student->user->email) }}" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-boxdark-2 border border-slate-200 dark:border-strokedark rounded-xl text-xs font-mono font-bold text-slate-800 dark:text-slate-200 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Nomor Handphone / WhatsApp Siswa</label>
                        <input type="text" name="phone" value="{{ old('phone', $student->phone ?? $student->user->phone) }}"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-boxdark-2 border border-slate-200 dark:border-strokedark rounded-xl text-xs font-mono font-bold text-slate-800 dark:text-slate-200 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition"
                               placeholder="08123456789">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Password Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" minlength="8"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-boxdark-2 border border-slate-200 dark:border-strokedark rounded-xl text-xs font-mono text-slate-800 dark:text-slate-200 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition"
                               placeholder="Minimal 8 karakter (opsional)">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">PIN Transaksi Baru (Opsional, 6 digit)</label>
                        <input type="text" name="pin" maxlength="6"
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-boxdark-2 border border-slate-200 dark:border-strokedark rounded-xl text-xs font-mono text-slate-800 dark:text-slate-200 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition"
                               placeholder="6 digit angka (opsional)">
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-boxdark-2 rounded-xl border border-slate-100 dark:border-strokedark space-y-1.5 text-xs">
                        <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="send_wa" value="1" checked class="w-3.5 h-3.5 rounded text-blue-600">
                            <span>Kirim notifikasi pembaruan ke WhatsApp Siswa / Wali</span>
                        </label>
                        <label class="flex items-center gap-2 text-slate-700 dark:text-slate-300 cursor-pointer">
                            <input type="checkbox" name="open_wa" value="1" class="w-3.5 h-3.5 rounded text-blue-600">
                            <span>Buka chat WhatsApp langsung setelah menyimpan</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button type="button" @click="showCredentialsModal = false"
                                class="px-4 py-2 rounded-xl border border-slate-200 dark:border-strokedark text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition-all cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
