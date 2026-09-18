@extends('layouts.spmb-mobile')

@section('title', 'Edit Data Pendaftaran')
@section('header_title', 'Edit Data')

@section('content')
<div class="space-y-4">
    <!-- Success Message -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = true, 100)"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
             class="fixed top-4 left-4 right-4 z-50 mx-auto max-w-md">
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-5 py-4 rounded-2xl shadow-2xl shadow-green-500/30 border border-green-400/30">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-sm mb-0.5">Berhasil!</h4>
                        <p class="text-sm text-green-50">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 w-8 h-8 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div x-data="{ show: true }" 
             x-show="show"
             x-init="setTimeout(() => show = true, 100)"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
             class="fixed top-4 left-4 right-4 z-50 mx-auto max-w-md">
            <div class="bg-gradient-to-r from-red-500 to-rose-500 text-white px-5 py-4 rounded-2xl shadow-2xl shadow-red-500/30 border border-red-400/30">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-sm mb-1.5">Terjadi Kesalahan</h4>
                        <ul class="text-sm text-red-50 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="flex-shrink-0 w-8 h-8 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center space-x-3 mb-2">
        <a href="{{ route('spmb.dashboard.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h2 class="text-lg font-bold text-gray-900">Edit Data Pendaftaran</h2>
            <p class="text-xs text-gray-500">Perbarui informasi pendaftaran Anda</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('spmb.dashboard.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Personal Information -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Data Pribadi
            </h3>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name', $registration->full_name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NISN</label>
                        <input type="text" name="nisn" value="{{ old('nisn', $registration->nisn) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" placeholder="0000000000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik', $registration->nik) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" placeholder="16 digit NIK">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Lahir</label>
                        <input type="text" name="birth_place" value="{{ old('birth_place', $registration->birth_place) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $registration->birth_date?->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl hover:bg-amber-50 hover:border-amber-200 transition cursor-pointer">
                            <input type="radio" name="gender" value="male" {{ old('gender', $registration->gender) == 'male' ? 'checked' : '' }}
                                   class="w-4 h-4 text-amber-600 border-gray-300 focus:ring-amber-500">
                            <span class="ml-2 text-sm text-gray-700">Laki-laki</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl hover:bg-amber-50 hover:border-amber-200 transition cursor-pointer">
                            <input type="radio" name="gender" value="female" {{ old('gender', $registration->gender) == 'female' ? 'checked' : '' }}
                                   class="w-4 h-4 text-amber-600 border-gray-300 focus:ring-amber-500">
                            <span class="ml-2 text-sm text-gray-700">Perempuan</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea name="address" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan...">{{ old('address', $registration->address) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $registration->phone) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" placeholder="08xxxxxxxxxx">
                </div>
            </div>
        </div>

        <!-- School Information -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Data Sekolah
            </h3>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asal Sekolah</label>
                    <input type="text" name="origin_school" value="{{ old('origin_school', $registration->origin_school) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" placeholder="Nama sekolah asal">
                </div>

                @if(Setting::get('is_vocational', '1') == '1')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilihan Jurusan</label>
                    <select name="major_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                        <option value="">Pilih Jurusan</option>
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" {{ old('major_id', $registration->major_id) == $major->id ? 'selected' : '' }}>
                                {{ $major->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>

        <!-- Parent Information -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Data Orang Tua / Wali
            </h3>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Orang Tua / Wali</label>
                    <input type="text" name="parent_name" value="{{ old('parent_name', $registration->parent_name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon Orang Tua</label>
                    <input type="text" name="parent_phone" value="{{ old('parent_phone', $registration->parent_phone) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" placeholder="08xxxxxxxxxx">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Orang Tua</label>
                    <textarea name="parent_address" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan...">{{ old('parent_address', $registration->parent_address) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Upload Dokumen -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100" x-data="{ 
            photoName: '', photoSize: '', 
            kkName: '', kkSize: '', 
            birthName: '', birthSize: '',
            previewModal: false,
            previewImage: '',
            previewTitle: ''
        }">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Upload Dokumen
            </h3>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4">
                <p class="text-xs text-amber-800">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Upload ulang dokumen hanya jika ingin mengganti dokumen yang sudah ada
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $photoDoc = $registration->documents()->where('type', 'photo')->first();
                    $kkDoc = $registration->documents()->where('type', 'kk')->first();
                    $birthDoc = $registration->documents()->where('type', 'birth_certificate')->first();
                @endphp

                <!-- Foto -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Siswa</label>
                    <div class="relative group" x-data="{ dragging: false }">
                        <input type="file" name="photo" accept="image/*" 
                               @change="photoName = $event.target.files[0]?.name; photoSize = ($event.target.files[0]?.size / 1024 / 1024).toFixed(2)"
                               @dragenter="dragging = true"
                               @dragleave="dragging = false"
                               @drop="dragging = false"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div :class="dragging ? 'border-amber-500 bg-amber-50/80 scale-[1.02] ring-4 ring-amber-500/10' : 'border-gray-300 bg-white'" class="border-2 border-dashed rounded-xl p-4 text-center group-hover:border-amber-500 group-hover:bg-amber-50 transition-all duration-200">
                            @if($photoDoc && file_exists(public_path('img/' . $photoDoc->file_path)))
                                <img src="{{ asset('img/' . $photoDoc->file_path) }}" alt="Foto Siswa" 
                                     @click="previewImage = '{{ asset('img/' . $photoDoc->file_path) }}'; previewTitle = 'Foto Siswa'; previewModal = true"
                                     class="w-20 h-20 mx-auto mb-2 object-cover rounded-lg border-2 border-amber-500 cursor-pointer hover:scale-110 transition-transform">
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg :class="dragging ? 'animate-bounce text-amber-600' : 'text-blue-600'" class="w-6 h-6 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <p class="text-xs font-medium text-gray-700 truncate" x-text="photoName || (dragging ? 'Lepaskan foto di sini' : '{{ $photoDoc ? 'Klik / seret untuk ganti' : 'Klik / seret foto ke sini' }}')"></p>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG. Max 2MB</p>
                            <p class="text-xs text-blue-600 mt-1 font-medium" x-show="photoSize" x-text="photoSize + ' MB'"></p>
                        </div>
                    </div>
                    @if($photoDoc)
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-xs text-green-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Terupload
                            </p>
                            <button type="button" @click="previewImage = '{{ asset('img/' . $photoDoc->file_path) }}'; previewTitle = 'Foto Siswa'; previewModal = true" class="text-xs text-amber-600 hover:text-amber-700 font-medium flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Kartu Keluarga -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kartu Keluarga</label>
                    <div class="relative group" x-data="{ dragging: false }">
                        <input type="file" name="kk" accept="image/*,.pdf" 
                               @change="kkName = $event.target.files[0]?.name; kkSize = ($event.target.files[0]?.size / 1024 / 1024).toFixed(2)"
                               @dragenter="dragging = true"
                               @dragleave="dragging = false"
                               @drop="dragging = false"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div :class="dragging ? 'border-amber-500 bg-amber-50/80 scale-[1.02] ring-4 ring-amber-500/10' : 'border-gray-300 bg-white'" class="border-2 border-dashed rounded-xl p-4 text-center group-hover:border-amber-500 group-hover:bg-amber-50 transition-all duration-200">
                            @if($kkDoc && file_exists(public_path('img/' . $kkDoc->file_path)))
                                @if(str_contains($kkDoc->file_mime, 'image'))
                                    <img src="{{ asset('img/' . $kkDoc->file_path) }}" alt="KK" 
                                         @click="previewImage = '{{ asset('img/' . $kkDoc->file_path) }}'; previewTitle = 'Kartu Keluarga'; previewModal = true"
                                         class="w-20 h-20 mx-auto mb-2 object-cover rounded-lg border-2 border-green-500 cursor-pointer hover:scale-110 transition-transform">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg :class="dragging ? 'animate-bounce text-amber-600' : 'text-green-600'" class="w-6 h-6 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF Document</p>
                                @endif
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg :class="dragging ? 'animate-bounce text-amber-600' : 'text-green-600'" class="w-6 h-6 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <p class="text-xs font-medium text-gray-700 truncate" x-text="kkName || (dragging ? 'Lepaskan KK di sini' : '{{ $kkDoc ? 'Klik / seret untuk ganti' : 'Klik / seret KK ke sini' }}')"></p>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, PDF. Max 2MB</p>
                            <p class="text-xs text-green-600 mt-1 font-medium" x-show="kkSize" x-text="kkSize + ' MB'"></p>
                        </div>
                    </div>
                    @if($kkDoc)
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-xs text-green-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Terupload
                            </p>
                            <button type="button" @click="previewImage = '{{ asset('img/' . $kkDoc->file_path) }}'; previewTitle = 'Kartu Keluarga'; previewModal = true" class="text-xs text-amber-600 hover:text-amber-700 font-medium flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Akta Kelahiran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Akta Kelahiran</label>
                    <div class="relative group" x-data="{ dragging: false }">
                        <input type="file" name="birth_certificate" accept="image/*,.pdf" 
                               @change="birthName = $event.target.files[0]?.name; birthSize = ($event.target.files[0]?.size / 1024 / 1024).toFixed(2)"
                               @dragenter="dragging = true"
                               @dragleave="dragging = false"
                               @drop="dragging = false"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div :class="dragging ? 'border-amber-500 bg-amber-50/80 scale-[1.02] ring-4 ring-amber-500/10' : 'border-gray-300 bg-white'" class="border-2 border-dashed rounded-xl p-4 text-center group-hover:border-amber-500 group-hover:bg-amber-50 transition-all duration-200">
                            @if($birthDoc && file_exists(public_path('img/' . $birthDoc->file_path)))
                                @if(str_contains($birthDoc->file_mime, 'image'))
                                    <img src="{{ asset('img/' . $birthDoc->file_path) }}" alt="Akta Kelahiran" 
                                         @click="previewImage = '{{ asset('img/' . $birthDoc->file_path) }}'; previewTitle = 'Akta Kelahiran'; previewModal = true"
                                         class="w-20 h-20 mx-auto mb-2 object-cover rounded-lg border-2 border-purple-500 cursor-pointer hover:scale-110 transition-transform">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg :class="dragging ? 'animate-bounce text-amber-600' : 'text-purple-600'" class="w-6 h-6 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF Document</p>
                                @endif
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg :class="dragging ? 'animate-bounce text-amber-600' : 'text-purple-600'" class="w-6 h-6 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <p class="text-xs font-medium text-gray-700 truncate" x-text="birthName || (dragging ? 'Lepaskan Akta di sini' : '{{ $birthDoc ? 'Klik / seret untuk ganti' : 'Klik / seret berkas ke sini' }}')"></p>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, PDF. Max 2MB</p>
                            <p class="text-xs text-purple-600 mt-1 font-medium" x-show="birthSize" x-text="birthSize + ' MB'"></p>
                        </div>
                    </div>
                    @if($birthDoc)
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-xs text-green-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Terupload
                            </p>
                            <button type="button" @click="previewImage = '{{ asset('img/' . $birthDoc->file_path) }}'; previewTitle = 'Akta Kelahiran'; previewModal = true" class="text-xs text-amber-600 hover:text-amber-700 font-medium flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Preview Modal -->
            <div x-show="previewModal" 
                 x-cloak
                 @click.self="previewModal = false"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" 
                     @click.stop
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-4">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900" x-text="previewTitle"></h3>
                        <button type="button" @click="previewModal = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Modal Body -->
                    <div class="p-6 overflow-auto max-h-[calc(90vh-80px)] flex items-center justify-center bg-gray-100">
                        <img :src="previewImage" :alt="previewTitle" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-lg">
                    </div>
                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 bg-gray-50">
                        <a :href="previewImage" target="_blank" class="px-4 py-2 text-sm font-medium text-amber-600 bg-amber-50 rounded-lg hover:bg-amber-100 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Download
                        </a>
                        <button type="button" @click="previewModal = false" class="px-4 py-2 text-sm font-medium text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-extrabold text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition-all transform hover:scale-[1.01] flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Simpan Perubahan</span>
        </button>
    </form>
</div>
@endsection
