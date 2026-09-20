@extends('layouts.admin')

@section('title', 'Tambah User')
@section('page_title', 'Tambah User Baru')

@section('content')
<div class="w-full space-y-6" x-data="userCreateForm()">
    <!-- Toast Notification Alert -->
    <div x-show="toast.show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed top-6 right-6 z-50 max-w-sm w-full shadow-2xl rounded-2xl p-4 border flex items-center space-x-3 backdrop-blur-md"
         :class="{
             'bg-rose-900/95 border-rose-700 text-white': toast.type === 'error',
             'bg-emerald-900/95 border-emerald-700 text-white': toast.type === 'success',
             'bg-amber-900/95 border-amber-700 text-white': toast.type === 'warning'
         }"
         style="display: none;">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
             :class="{
                 'bg-rose-500/20 text-rose-300': toast.type === 'error',
                 'bg-emerald-500/20 text-emerald-300': toast.type === 'success',
                 'bg-amber-500/20 text-amber-300': toast.type === 'warning'
             }">
            <template x-if="toast.type === 'error'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </template>
            <template x-if="toast.type === 'warning'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </template>
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </template>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-[10px] font-black uppercase tracking-wider opacity-80" x-text="toast.type === 'error' ? 'Peringatan Validasi' : (toast.type === 'warning' ? 'Perhatian' : 'Sukses')"></p>
            <p class="text-xs font-semibold leading-snug mt-0.5" x-text="toast.message"></p>
        </div>
        <button type="button" @click="toast.show = false" class="text-white/70 hover:text-white p-1 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Tambah User Baru</h2>
                <p class="text-sm text-gray-500">Tambahkan pengguna ke sistem</p>
            </div>
        </div>
        <button type="button" @click="submitForm()" class="px-6 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-xl hover:shadow-lg transition font-semibold text-sm">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan User
        </button>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center space-x-2 text-red-800 font-semibold mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Ada kesalahan pada form</span>
            </div>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1 ml-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Form -->
    <form id="userForm" 
          action="{{ route('admin.users.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="space-y-6"
          @submit.prevent="submitForm()">
        @csrf
        
        <!-- Top Row: Avatar + Account Info -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Avatar Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border overflow-hidden sticky top-6">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Foto Profil
                        </h3>
                    </div>
                    <div class="p-6 text-center">
                        <div class="relative group inline-block">
                            <div id="avatarPreview" 
                                 class="w-32 h-32 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full border-4 border-white shadow-lg flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:shadow-xl"
                                 :class="avatarPreview ? 'ring-4 ring-primary/20' : 'border-dashed border-2 border-gray-300'">
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!avatarPreview">
                                    <div class="text-center p-4">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <p class="text-xs text-gray-500">Upload</p>
                                    </div>
                                </template>
                            </div>
                            <input type="file" 
                                   name="avatar" 
                                   id="avatar" 
                                   accept="image/*" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                   @change="previewAvatar($event)">
                        </div>
                        
                        <div id="avatarInfo" class="hidden mt-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1 min-w-0 text-left">
                                    <p class="text-sm font-medium text-green-900 truncate" id="avatarName"></p>
                                    <p class="text-xs text-green-600 mt-0.5" id="avatarSize"></p>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mt-4">
                            PNG, JPG atau WEBP (Maks. 2MB)
                        </p>
                        @error('avatar')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Right Side - Main Details -->
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <h3 class="text-base font-bold text-gray-900 border-b pb-4 mb-6 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Informasi Akun & Data Pribadi</span>
                    </h3>

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Nama Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input type="text" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                       placeholder="Nama lengkap sesuai identitas" 
                                       required>
                            </div>
                            @error('name')
                                <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                       placeholder="contoh@sekolah.sch.id" 
                                       required>
                            </div>
                            @error('email')
                                <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       x-model="password"
                                       class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                       placeholder="Minimal 8 karakter" 
                                       required>
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.959 8.959 0 014.122-.977c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"/></svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <input :type="showConfirmPassword ? 'text' : 'password'" 
                                       name="password_confirmation" 
                                       x-model="passwordConfirmation"
                                       class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                       :class="isPasswordMismatch ? 'border-red-500 focus:ring-red-500' : (isPasswordMatching ? 'border-emerald-500 focus:ring-emerald-500' : '')"
                                       placeholder="Ulangi password" 
                                       required>
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showConfirmPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.959 8.959 0 014.122-.977c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"/></svg>
                                </button>
                            </div>
                            <p x-show="isPasswordMismatch" x-cloak class="text-xs text-red-500 mt-1 font-semibold flex items-center space-x-1">
                                <span>⚠️ Konfirmasi password tidak cocok!</span>
                            </p>
                            <p x-show="isPasswordMatching" x-cloak class="text-xs text-emerald-600 mt-1 font-semibold flex items-center space-x-1">
                                <span>✓ Password cocok</span>
                            </p>
                        </div>

                        <!-- NIP / NIPPPK -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIP / NIPPPK</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6"/>
                                    </svg>
                                </div>
                                <input type="text" 
                                       name="nip" 
                                       value="{{ old('nip') }}" 
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition font-mono" 
                                       placeholder="Contoh: 198503152010011002">
                            </div>
                            @error('nip')
                                <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- No WhatsApp / Telepon -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">No. WhatsApp / Telepon</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input type="text" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition" 
                                       placeholder="08123456789">
                            </div>
                            @error('phone')
                                <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- TMT (Terhitung Mulai Tanggal) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                TMT (Terhitung Mulai Tanggal)
                                <span class="text-xs text-gray-400 font-normal ml-1">(Awal Masuk Kerja)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="date" 
                                       name="tmt" 
                                       value="{{ old('tmt') }}" 
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            </div>
                            @error('tmt')
                                <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pendidikan Terakhir -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pendidikan Terakhir</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                    </svg>
                                </div>
                                <select name="last_education" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition appearance-none bg-white">
                                    <option value="">-- Pilih Pendidikan Terakhir --</option>
                                    @php
                                        $eduList = [
                                            'S3 / Doktor',
                                            'S2 / Magister',
                                            'S1 / Sarjana',
                                            'D4 / Diploma 4',
                                            'D3 / Diploma 3',
                                            'D2 / Diploma 2',
                                            'D1 / Diploma 1',
                                            'SMA / MA / SMK Sederajat',
                                            'Pondok Pesantren / Ma\'had Aly',
                                            'SMP / MTs Sederajat',
                                            'Lainnya'
                                        ];
                                    @endphp
                                    @foreach($eduList as $edu)
                                        <option value="{{ $edu }}" {{ old('last_education') == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            @error('last_education')
                                <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role / Peran -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role / Peran <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <select name="role_id" @change="checkRoleSlug()" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition appearance-none bg-white" required>
                                    <option value="" data-slug="">Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" data-slug="{{ $role->slug }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            @error('role_id')
                                <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Penugasan Bina Kelas (Multi-class Wali Kelas) -->
                        @if(isset($classes) && count($classes) > 0)
                            @php
                                $isVocational = \App\Models\Setting::get('is_vocational', '1') == '1';
                            @endphp
                            <div x-show="selectedRoleSlug === 'guru' || selectedRoleSlug === 'teacher'" x-cloak class="md:col-span-2 p-4 bg-purple-50/50 dark:bg-purple-950/20 rounded-2xl border border-purple-100 dark:border-purple-900/50 space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider">
                                        Penugasan Wali Kelas (Bisa Pilihan Multi-Kelas)
                                    </label>
                                    @if($isVocational)
                                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300 rounded-md">
                                            Dikelompokkan Per Jurusan
                                        </span>
                                    @endif
                                </div>

                                @if($isVocational)
                                    @php
                                        $groupedClasses = $classes->groupBy(function($cls) {
                                            return $cls->major ? $cls->major->name : 'Umum / Non-Jurusan';
                                        });
                                    @endphp

                                        <div class="space-y-3">
                                            @foreach($groupedClasses as $majorName => $classList)
                                                <div class="bg-white dark:bg-boxdark p-3 rounded-xl border border-purple-100 dark:border-purple-900/40 space-y-2">
                                                    <div class="text-xs font-black text-purple-800 dark:text-purple-300 flex items-center space-x-1.5 border-b border-purple-50 dark:border-slate-800 pb-1.5">
                                                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                        <span>Jurusan: {{ $majorName }}</span>
                                                        <span class="text-[10px] text-slate-400 font-semibold">({{ count($classList) }} Kelas)</span>
                                                    </div>
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 pt-0.5">
                                                        @foreach($classList as $cls)
                                                            <label class="flex items-center space-x-2 p-2 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-purple-100/80 dark:border-purple-900/30 cursor-pointer hover:bg-purple-100/50 dark:hover:bg-purple-950/40 transition">
                                                                <input type="checkbox" name="homeroom_classes[]" value="{{ $cls->id }}" 
                                                                       {{ is_array(old('homeroom_classes')) && in_array($cls->id, old('homeroom_classes')) ? 'checked' : '' }}
                                                                       class="w-4 h-4 text-purple-600 rounded">
                                                                <span class="text-xs font-bold text-gray-800 dark:text-slate-200">Kelas {{ $cls->name }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                            @foreach($classes as $cls)
                                                <label class="flex items-center space-x-2 p-2 bg-white dark:bg-boxdark rounded-xl border border-purple-100 dark:border-purple-900/40 cursor-pointer hover:bg-purple-100/50 dark:hover:bg-purple-950/40 transition">
                                                    <input type="checkbox" name="homeroom_classes[]" value="{{ $cls->id }}" 
                                                           {{ is_array(old('homeroom_classes')) && in_array($cls->id, old('homeroom_classes')) ? 'checked' : '' }}
                                                           class="w-4 h-4 text-purple-600 rounded">
                                                    <span class="text-xs font-bold text-gray-800 dark:text-slate-200">Kelas {{ $cls->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Penugasan Kelas Halaqah Al-Qur'an (Multi-Kelas untuk Guru Qur'an) -->
                            @if(isset($classes) && count($classes) > 0)
                                <div x-show="selectedRoleSlug === 'guru-quran' || selectedRoleSlug === 'guru' || selectedRoleSlug === 'teacher'" x-cloak class="md:col-span-2 p-4 bg-emerald-50/60 dark:bg-emerald-950/20 rounded-2xl border border-emerald-200 dark:border-emerald-800/50 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <label class="block text-xs font-bold text-emerald-900 dark:text-emerald-300 uppercase tracking-wider">
                                                Penugasan Kelas Halaqah Al-Qur'an (Hak Akses Guru Qur'an)
                                            </label>
                                        </div>
                                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300 rounded-md">
                                            Khusus Guru Al-Qur'an
                                        </span>
                                    </div>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-400">
                                        Guru Al-Qur'an hanya dapat menginput nilai halaqah, melihat hafalan, dan mencetak raport santri dari kelas-kelas yang dicentang di bawah ini:
                                    </p>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 pt-1">
                                        @foreach($classes as $cls)
                                            <label class="flex items-center space-x-2 p-2.5 bg-white dark:bg-boxdark rounded-xl border border-emerald-200/80 dark:border-emerald-900/40 cursor-pointer hover:bg-emerald-100/50 dark:hover:bg-emerald-950/40 transition">
                                                <input type="checkbox" name="quran_classes[]" value="{{ $cls->id }}" 
                                                       {{ is_array(old('quran_classes')) && in_array($cls->id, old('quran_classes')) ? 'checked' : '' }}
                                                       class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500">
                                                <span class="text-xs font-bold text-gray-800 dark:text-slate-200">Kelas {{ $cls->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <textarea name="address" 
                                              rows="3" 
                                              class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition resize-none" 
                                              placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                
                <!-- Status Card -->
                <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                    <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Status Akun
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               name="is_active" 
                                               value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}
                                               class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary focus:ring-2">
                                        <span class="ml-3 text-sm font-medium text-gray-700">Akun Aktif</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1 ml-8">User non-aktif tidak bisa login ke sistem</p>
                                </div>
                            </div>
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function userCreateForm() {
    return {
        avatarPreview: null,
        avatarFile: null,
        password: '',
        passwordConfirmation: '',
        showPassword: false,
        showConfirmPassword: false,
        toast: { show: false, message: '', type: 'error' },
        showToast(msg, type) {
            this.toast.message = msg;
            this.toast.type = type || 'error';
            this.toast.show = true;
            const self = this;
            setTimeout(function() { self.toast.show = false; }, 4000);
        },
        get isPasswordMatching() {
            return this.password.length > 0 && this.passwordConfirmation.length > 0 && this.password === this.passwordConfirmation;
        },
        get isPasswordMismatch() {
            return this.passwordConfirmation.length > 0 && this.password !== this.passwordConfirmation;
        },
        selectedRoleSlug: '',
        checkRoleSlug() {
            const selectEl = document.querySelector('select[name="role_id"]');
            if (selectEl && selectEl.selectedIndex !== -1) {
                this.selectedRoleSlug = selectEl.options[selectEl.selectedIndex].getAttribute('data-slug') || '';
                if (this.selectedRoleSlug !== 'guru' && this.selectedRoleSlug !== 'teacher') {
                    const cbs = document.querySelectorAll('input[name="homeroom_classes[]"]');
                    for (let i = 0; i < cbs.length; i++) {
                        cbs[i].checked = false;
                    }
                }
                if (this.selectedRoleSlug !== 'guru-quran' && this.selectedRoleSlug !== 'guru' && this.selectedRoleSlug !== 'teacher') {
                    const qbs = document.querySelectorAll('input[name="quran_classes[]"]');
                    for (let i = 0; i < qbs.length; i++) {
                        qbs[i].checked = false;
                    }
                }
            }
        },
        init() {
            const self = this;
            this.$nextTick(function() { self.checkRoleSlug(); });
        },
        submitForm() {
            if (this.password.length < 8) {
                this.showToast('Password minimal harus 8 karakter!', 'error');
                return false;
            }
            if (this.isPasswordMismatch) {
                this.showToast('Konfirmasi password tidak cocok dengan password!', 'error');
                return false;
            }
            document.getElementById('userForm').submit();
        }
    };
}

function previewAvatar(event) {
    const input = event.target;
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('avatarPreview').innerHTML = 
                '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
            document.getElementById('avatarInfo').classList.remove('hidden');
            document.getElementById('avatarName').textContent = file.name;
            document.getElementById('avatarSize').textContent = (file.size / 1024).toFixed(2) + ' KB';
        }
        
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection
