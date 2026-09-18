@extends('layouts.admin')

@section('title', 'Detail Siswa - ' . ($student->user->name ?? 'Siswa'))
@section('page_title', 'Detail Data Siswa')

@section('content')
<div class="space-y-6 w-full pb-16">
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

            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kontak & Akses</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1">
                        <span class="text-[#64748B] dark:text-[#8A99AD]">No. Handphone / WA</span>
                        <span class="font-bold text-[#1C2434] dark:text-white">{{ $student->phone ?? $student->user->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-[#64748B] dark:text-[#8A99AD]">Status Akun</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50">
                            Aktif
                        </span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-[#64748B] dark:text-[#8A99AD]">PIN Akses</span>
                        <span class="font-bold text-[#1C2434] dark:text-white">
                            {{ $student->pin ? 'Tersedia (Encrypted)' : 'Belum diatur' }}
                        </span>
                    </div>
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
</div>
@endsection
