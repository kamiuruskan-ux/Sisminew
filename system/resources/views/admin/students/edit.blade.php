@extends('layouts.admin')

@section('title', 'Edit Data Siswa')
@section('page_title', 'Formulir Edit Siswa')

@section('content')
<div class="w-full space-y-6" x-data="{ photoPreview: '{{ $student->photo ? (\Illuminate\Support\Str::startsWith($student->photo, 'img/') ? asset($student->photo) : asset('img/students/' . $student->photo)) : '' }}' }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">Edit Data Siswa</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Perbarui profil dan informasi akademik siswa: <strong class="text-slate-800 dark:text-slate-200">{{ $student->name }}</strong>.</p>
            </div>
        </div>
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center justify-center space-x-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-2xl border border-slate-200 dark:border-slate-700 transition">
            <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Data Siswa</span>
        </a>
    </div>

    <!-- Main Form Grid (Proportional 8-Col Main Content + 4-Col Sidebar) -->
    <form action="{{ route('admin.students.update', encode_id($student->id)) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN: Main Form Data (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Card 1: Akun & Identitas Utama -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-5">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold border border-indigo-100 dark:border-indigo-900 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Akun & Identitas Utama Siswa</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Informasi autentikasi login dan nomor identitas resmi.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Nama Lengkap Siswa <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $student->name) }}" placeholder="Masukkan nama lengkap siswa" required
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                            @error('name') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email & Password -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Email Akun <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $student->user->email ?? '') }}" placeholder="siswa@sekolah.sch.id" required
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('email') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Password Baru (Opsional)
                                </label>
                                <input type="password" name="password" placeholder="Kosongkan jika tak diubah" minlength="8"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('password') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- NIS, NISN, NIK -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">NIS (Lokal/Sekolah)</label>
                                <input type="text" name="nis" value="{{ old('nis', $student->nis) }}" placeholder="No. Induk Sekolah"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('nis') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">NISN (Nasional)</label>
                                <input type="text" name="nisn" value="{{ old('nisn', $student->nisn) }}" placeholder="10 digit NISN"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('nisn') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">NIK (KTP/KK)</label>
                                <input type="text" name="nik" value="{{ old('nik', $student->nik) }}" placeholder="16 digit NIK"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('nik') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Jenis Kelamin & Agama -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Jenis Kelamin <span class="text-rose-500">*</span>
                                </label>
                                <select name="gender" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-semibold rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                    <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Agama</label>
                                <select name="religion" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-semibold rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                    <option value="">-- Pilih Agama --</option>
                                    <option value="Islam" {{ old('religion', $student->religion) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Kristen" {{ old('religion', $student->religion) == 'Kristen' ? 'selected' : '' }}>Kristen Protestan</option>
                                    <option value="Katolik" {{ old('religion', $student->religion) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                    <option value="Hindu" {{ old('religion', $student->religion) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Buddha" {{ old('religion', $student->religion) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                    <option value="Khonghucu" {{ old('religion', $student->religion) == 'Khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                                </select>
                                @error('religion') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Akademik & Status Siswa -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-5">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold border border-blue-100 dark:border-blue-900 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Data Akademik & Penempatan</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Penempatan kelas, jurusan, angkatan, dan status siswa.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Kelas & Jurusan (Jurusan dahulu jika Kejuruan Aktif) -->
                        <x-major-class-select :majors="$majors" :classes="$classes" :selected-major="$student->major_id" :selected-class="$student->class_id" />

                        <!-- Status Siswa & Angkatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Status Keaktifan Siswa</label>
                                <select name="student_status" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-semibold rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                    <option value="active" {{ old('student_status', $student->student_status ?? 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="alumni" {{ old('student_status', $student->student_status) == 'alumni' ? 'selected' : '' }}>Alumni / Lulus</option>
                                    <option value="moved" {{ old('student_status', $student->student_status) == 'moved' ? 'selected' : '' }}>Mutasi / Pindah</option>
                                    <option value="dropped" {{ old('student_status', $student->student_status) == 'dropped' ? 'selected' : '' }}>Keluar / DO</option>
                                </select>
                                @error('student_status') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tahun Masuk / Angkatan</label>
                                <input type="text" name="entry_year" value="{{ old('entry_year', $student->entry_year) }}" placeholder="Misal: 2024"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('entry_year') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Biodata Diri & Kontak Tempat Tinggal -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-5">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold border border-emerald-100 dark:border-emerald-900 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Kelahiran & Tempat Tinggal</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Tempat lahir, tanggal lahir, kontak HP, dan alamat rumah.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Tempat & Tanggal Lahir -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tempat Lahir</label>
                                <input type="text" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}" placeholder="Kota tempat lahir"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('birth_place') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('birth_date') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- No HP Siswa -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">No. HP / WhatsApp Siswa</label>
                            <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" placeholder="08xxxxxxxxxx"
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                            @error('phone') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Alamat Lengkap -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Alamat Tempat Tinggal Lengkap Siswa</label>
                            <textarea name="address" rows="3" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten..."
                                      class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">{{ old('address', $student->address) }}</textarea>
                            @error('address') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 4: Informasi Orang Tua / Wali -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-5">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold border border-purple-100 dark:border-purple-900 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Data Orang Tua / Wali Siswa</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Nama Ayah, Nama Ibu, Wali, Kontak, dan Pekerjaan.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Nama Ayah & Nama Ibu -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Ayah Kandung</label>
                                <input type="text" name="father_name" value="{{ old('father_name', $student->father_name) }}" placeholder="Nama lengkap Ayah"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('father_name') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Ibu Kandung</label>
                                <input type="text" name="mother_name" value="{{ old('mother_name', $student->mother_name) }}" placeholder="Nama lengkap Ibu"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('mother_name') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Nama Wali & Kontak HP -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Wali Utama / Penanggungjawab</label>
                                <input type="text" name="parent_name" value="{{ old('parent_name', $student->parent_name) }}" placeholder="Nama Ayah/Ibu/Wali"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('parent_name') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">No. HP / WhatsApp Orang Tua / Wali</label>
                                <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}" placeholder="08xxxxxxxxxx"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                                @error('parent_phone') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Pekerjaan Orang Tua / Wali -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Pekerjaan Orang Tua / Wali</label>
                            <input type="text" name="parent_job" value="{{ old('parent_job', $student->parent_job) }}" placeholder="PNS, Swasta, Wiraswasta, Buruh, Dll."
                                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">
                            @error('parent_job') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Alamat Orang Tua / Wali -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Alamat Tempat Tinggal Orang Tua / Wali</label>
                            <textarea name="parent_address" rows="2" placeholder="Kosongkan jika sama dengan alamat siswa"
                                      class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 transition">{{ old('parent_address', $student->parent_address) }}</textarea>
                            @error('parent_address') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Sidebar (Media, Beasiswa & Submit) (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Pas Foto Siswa -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-4 text-center">
                    <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800 text-left">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold border border-amber-100 dark:border-amber-900 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Pas Foto Siswa</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Untuk KTS & Raport.</p>
                        </div>
                    </div>

                    <!-- 3x4 Frame Preview -->
                    <div class="w-28 h-36 mx-auto rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex items-center justify-center overflow-hidden shrink-0 relative shadow-inner">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!photoPreview">
                            <div class="text-center p-2 text-slate-400">
                                <svg class="w-8 h-8 mx-auto mb-1 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-[10px] font-bold block text-slate-400">Pas Foto 3x4</span>
                            </div>
                        </template>
                    </div>

                    <input type="file" name="photo" accept="image/*" id="photo-input" class="hidden"
                           @change="const file = $event.target.files[0]; if (file) { photoPreview = URL.createObjectURL(file); }">
                    <label for="photo-input" class="w-full py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-2xl border border-slate-200 dark:border-slate-700 cursor-pointer transition flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span>Ganti Berkas Foto</span>
                    </label>
                    <p class="text-[10px] text-slate-400">Format: JPG, PNG. Maksimal 2 MB.</p>
                    @error('photo') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Keringanan SPP / Beasiswa -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-4">
                    <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold border border-emerald-100 dark:border-emerald-900 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">Beasiswa / Potongan SPP</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Keringanan biaya bulanan.</p>
                        </div>
                    </div>

                    <div>
                        <x-rupiah-input name="spp_discount" label="Potongan Bulanan (Rp)" :value="old('spp_discount', $student->spp_discount ?? 0)" show-terbilang />
                        @error('spp_discount') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Alasan Keringanan</label>
                        <input type="text" name="discount_description" value="{{ old('discount_description', $student->discount_description) }}" placeholder="Misal: Beasiswa Prestasi"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition">
                        @error('discount_description') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- PIN Keamanan QRpay -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs space-y-4">
                    <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold border border-purple-100 dark:border-purple-900 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm">PIN QRpay (Opsional)</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Security PIN 6 Digit.</p>
                        </div>
                    </div>

                    <div>
                        <input type="password" name="pin" maxlength="6" placeholder="Kosongkan jika tak ingin diubah"
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium rounded-2xl px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition">
                        <p class="text-[10px] text-slate-400 mt-1">Dapat diatur nanti oleh siswa.</p>
                        @error('pin') <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Submit Action Panel -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-xs space-y-3">
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl shadow-md transition flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Perbarui Data Siswa</span>
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="w-full py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-2xl border border-slate-200 dark:border-slate-700 transition flex items-center justify-center">
                        Batal
                    </a>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection
