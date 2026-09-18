@extends('layouts.admin')

@section('title', 'Pengaturan SPMB & Landing Page')
@section('page_title', 'Pengaturan SPMB & Landing Page')

@section('content')
<div class="space-y-8 pb-24 w-full" x-data="{ activeTab: 'general' }">
    <!-- Hero Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 p-6 sm:p-8 lg:p-10 text-white shadow-2xl border border-slate-800/80">
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-24 w-96 h-96 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/10 text-xs font-extrabold text-indigo-200">
                    <span class="w-2 h-2 rounded-full {{ Setting::get('spmb_enabled', '1') == '1' ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' }}"></span>
                    <span>Status Pendaftaran SPMB: <strong>{{ Setting::get('spmb_enabled', '1') == '1' ? 'AKTIF' : 'NONAKTIF' }}</strong></span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Pengaturan SPMB & Landing Page
                </h1>
                <p class="text-xs sm:text-sm text-indigo-100/80 font-medium">
                    Kelola biaya pendaftaran, informasi operasional, serta seluruh konten halaman publik /spmb/info secara dinamis.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('spmb.info') }}" target="_blank" class="inline-flex items-center justify-center h-11 px-5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs uppercase tracking-wider transition-all duration-200 border border-white/15 backdrop-blur-md shadow-sm space-x-2 hover:scale-[1.02] active:scale-95">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Pratinjau /spmb/info ↗</span>
                </a>
            </div>
        </div>
    </div>


    <!-- Form Navigation Tabs (Standardized Height & Styling) -->
    <div class="flex flex-wrap gap-2.5 border-b border-slate-200/80 pb-3">
        <button type="button" @click="activeTab = 'general'" 
                :class="activeTab === 'general' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="inline-flex items-center justify-center h-11 px-5 rounded-2xl font-extrabold text-xs transition-all duration-200 gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            <span>Operasional & Biaya</span>
        </button>

        <button type="button" @click="activeTab = 'hero'" 
                :class="activeTab === 'hero' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="inline-flex items-center justify-center h-11 px-5 rounded-2xl font-extrabold text-xs transition-all duration-200 gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
            <span>Hero & Landing Banner</span>
        </button>

        <button type="button" @click="activeTab = 'steps'" 
                :class="activeTab === 'steps' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="inline-flex items-center justify-center h-11 px-5 rounded-2xl font-extrabold text-xs transition-all duration-200 gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span>Alur Pendaftaran (4 Langkah)</span>
        </button>

        <button type="button" @click="activeTab = 'requirements'" 
                :class="activeTab === 'requirements' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="inline-flex items-center justify-center h-11 px-5 rounded-2xl font-extrabold text-xs transition-all duration-200 gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Persyaratan Dokumen</span>
        </button>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.spmb.update-settings') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- TAB 1: Operasional & Biaya -->
        <div x-show="activeTab === 'general'" x-transition class="space-y-6">
            
            <!-- Status SPMB Switch Card -->
            <div class="premium-card p-6 sm:p-8 bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 border-none shadow-2xl relative overflow-hidden text-white">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/15 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 relative z-10">
                    <div class="space-y-1 max-w-xl">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ Setting::get('spmb_enabled', '1') == '1' ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' }}"></span>
                            <h3 class="font-extrabold text-white text-base sm:text-lg tracking-tight">Status Pendaftaran SPMB Online</h3>
                        </div>
                        <p class="text-xs text-indigo-200/80 font-medium leading-relaxed">
                            Buka (Aktif) atau tutup (Nonaktif) pendaftaran calon siswa baru secara global. Jika nonaktif, formulir dan menu SPMB di halaman publik akan disembunyikan.
                        </p>
                    </div>

                    <div x-data="{ enabled: {{ Setting::get('spmb_enabled', '1') === '1' ? 'true' : 'false' }} }" class="shrink-0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" :checked="enabled" @change="enabled = !enabled">
                            <input type="hidden" name="spmb_enabled" :value="enabled ? '1' : '0'">
                            <div class="w-16 h-8 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[3px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Detail Operasional & Biaya -->
            <div class="premium-card overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold border border-indigo-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Informasi Operasional & Biaya Pendaftaran</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Biaya Registrasi Awal & Kontak Panitia SPMB</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full uppercase border border-indigo-100">Operasional</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Biaya Formulir -->
                        <div class="space-y-1">
                            <x-rupiah-input name="spmb_registration_fee" label="Biaya Formulir Pendaftaran (Rp)" :value="old('spmb_registration_fee', Setting::get('spmb_registration_fee', 150000))" show-terbilang />
                            <p class="text-[11px] text-slate-400 font-medium">Biaya registrasi awal calon siswa baru (contoh: 150.000).</p>
                        </div>

                        <!-- Nomor WhatsApp Panitia -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nomor WhatsApp Panitia SPMB</label>
                            <input type="text" name="spmb_whatsapp" value="{{ old('spmb_whatsapp', Setting::get('spmb_whatsapp', Setting::get('school_whatsapp', '081234567890'))) }}" placeholder="081234567890"
                                   class="w-full px-4 h-11 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">
                            <p class="text-[11px] text-slate-400 font-medium">Nomor kontak layanan konsultasi calon orang tua / siswa.</p>
                        </div>

                        <!-- Telepon Panitia -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nomor Telepon Kantor Panitia</label>
                            <input type="text" name="spmb_contact_phone" value="{{ old('spmb_contact_phone', Setting::get('spmb_contact_phone', Setting::get('school_phone', '(021) 1234567'))) }}" placeholder="(021) 1234567"
                                   class="w-full px-4 h-11 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">
                            <p class="text-[11px] text-slate-400 font-medium">Telepon kantor panitia SPMB.</p>
                        </div>

                        <!-- Jadwal Pengumuman -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Keterangan Jadwal Pengumuman Hasil</label>
                            <input type="text" name="spmb_announcement_date" value="{{ old('spmb_announcement_date', Setting::get('spmb_announcement_date', 'Pengumuman hasil kelulusan setiap akhir bulan berjalan.')) }}" placeholder="Pengumuman hasil kelulusan..."
                                   class="w-full px-4 h-11 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">
                            <p class="text-[11px] text-slate-400 font-medium">Teks jadwal pengumuman kelulusan yang ditampilkan ke pendaftar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Hero Banner & Teks Utama Landing Page -->
        <div x-show="activeTab === 'hero'" x-transition class="space-y-6">
            <div class="premium-card overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold border border-blue-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Hero Banner & Konten Utama (/spmb/info)</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Pengaturan Teks Headline & Subtitle Hero Landing SPMB</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-blue-50 text-blue-600 px-3 py-1 rounded-full uppercase border border-blue-100">Hero Section</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Badge Text -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Teks Badge / Sub-Header Hero</label>
                        <input type="text" name="spmb_hero_badge" value="{{ old('spmb_hero_badge', Setting::get('spmb_hero_badge', 'PENERIMAAN SISWA BARU T.A. ' . date('Y') . '/' . (date('Y')+1))) }}" placeholder="PENERIMAAN SISWA BARU T.A. 2026/2027"
                               class="w-full px-4 h-11 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">
                        <p class="text-[11px] text-slate-400 font-medium">Teks badge kecil di bagian paling atas hero banner.</p>
                    </div>

                    <!-- Judul Utama Hero -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Judul Utama Hero Banner</label>
                        <input type="text" name="spmb_hero_title" value="{{ old('spmb_hero_title', Setting::get('spmb_hero_title', 'Raih Masa Depan Gemilang di ' . Setting::get('school_name', 'Sekolah'))) }}" placeholder="Raih Masa Depan Gemilang..."
                               class="w-full px-4 h-11 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">
                        <p class="text-[11px] text-slate-400 font-medium">Judul besar pada landing page SPMB.</p>
                    </div>

                    <!-- Subtitle / Deskripsi Hero -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Deskripsi Singkat Hero Banner</label>
                        <textarea name="spmb_hero_subtitle" rows="3" class="w-full p-4 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-medium text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">{{ old('spmb_hero_subtitle', Setting::get('spmb_hero_subtitle', 'Bergabunglah dengan institusi pendidikan unggulan terakreditasi A. Kami membuka kesempatan emas pendaftaran murid baru secara online untuk semua jalur seleksi.')) }}</textarea>
                    </div>

                    <!-- Teks CTA Tombol -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Teks Tombol Pendaftaran (CTA)</label>
                            <input type="text" name="spmb_hero_cta_text" value="{{ old('spmb_hero_cta_text', Setting::get('spmb_hero_cta_text', 'Daftar SPMB Online Now')) }}" placeholder="Daftar SPMB Online Now"
                                   class="w-full px-4 h-11 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Keterangan Beasiswa (Pill Card)</label>
                            <input type="text" name="spmb_scholarship_info" value="{{ old('spmb_scholarship_info', Setting::get('spmb_scholarship_info', 's.d. 100% Bebas SPP')) }}" placeholder="s.d. 100% Bebas SPP"
                                   class="w-full px-4 h-11 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">
                        </div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="space-y-2 pt-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan / Informasi Pengumuman Penting</label>
                        <textarea name="spmb_info_text" rows="3" placeholder="Informasi pendaftaran siswa baru..."
                                  class="w-full p-4 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-medium text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">{{ old('spmb_info_text', Setting::get('spmb_info_text', 'Pendaftaran Siswa Baru telah dibuka. Segera daftarkan diri Anda!')) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Alur Pendaftaran (4 Langkah) -->
        <div x-show="activeTab === 'steps'" x-transition class="space-y-6">
            <div class="premium-card overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-extrabold border border-purple-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Alur 4 Langkah Pendaftaran Mudah</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Judul & Deskripsi Tahapan Pendaftaran Siswa Baru</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-purple-50 text-purple-600 px-3 py-1 rounded-full uppercase border border-purple-100">Tahapan</span>
                </div>

                <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Step 1 -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-xl bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center shadow-sm">1</span>
                            <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Langkah 1</span>
                        </div>
                        <input type="text" name="spmb_step_1_title" value="{{ old('spmb_step_1_title', Setting::get('spmb_step_1_title', 'Isi Formulir Online')) }}" placeholder="Judul Langkah 1"
                               class="w-full px-4 h-10 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <textarea name="spmb_step_1_desc" rows="2" placeholder="Deskripsi Langkah 1"
                                  class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-600 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">{{ old('spmb_step_1_desc', Setting::get('spmb_step_1_desc', 'Calon siswa mengisi data diri lengkap, data orang tua, dan pilihan jurusan pada portal pendaftaran.')) }}</textarea>
                    </div>

                    <!-- Step 2 -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shadow-sm">2</span>
                            <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Langkah 2</span>
                        </div>
                        <input type="text" name="spmb_step_2_title" value="{{ old('spmb_step_2_title', Setting::get('spmb_step_2_title', 'Upload Dokumen')) }}" placeholder="Judul Langkah 2"
                               class="w-full px-4 h-10 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <textarea name="spmb_step_2_desc" rows="2" placeholder="Deskripsi Langkah 2"
                                  class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-600 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">{{ old('spmb_step_2_desc', Setting::get('spmb_step_2_desc', 'Unggah scan Ijazah/SKL SMP, Kartu Keluarga, Akta Kelahiran, serta pasfoto warna terbaru.')) }}</textarea>
                    </div>

                    <!-- Step 3 -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-xl bg-purple-600 text-white font-extrabold text-xs flex items-center justify-center shadow-sm">3</span>
                            <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Langkah 3</span>
                        </div>
                        <input type="text" name="spmb_step_3_title" value="{{ old('spmb_step_3_title', Setting::get('spmb_step_3_title', 'Tes TPA & Wawancara')) }}" placeholder="Judul Langkah 3"
                               class="w-full px-4 h-10 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <textarea name="spmb_step_3_desc" rows="2" placeholder="Deskripsi Langkah 3"
                                  class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-600 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">{{ old('spmb_step_3_desc', Setting::get('spmb_step_3_desc', 'Mengikuti ujian potensi akademik secara online / hadir di sekolah serta sesi pemetaan minat bakat.')) }}</textarea>
                    </div>

                    <!-- Step 4 -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shadow-sm">4</span>
                            <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Langkah 4</span>
                        </div>
                        <input type="text" name="spmb_step_4_title" value="{{ old('spmb_step_4_title', Setting::get('spmb_step_4_title', 'Pengumuman & Re-Registrasi')) }}" placeholder="Judul Langkah 4"
                               class="w-full px-4 h-10 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <textarea name="spmb_step_4_desc" rows="2" placeholder="Deskripsi Langkah 4"
                                  class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-600 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">{{ old('spmb_step_4_desc', Setting::get('spmb_step_4_desc', 'Pengumuman hasil kelulusan di portal SPMB dan verifikasi pendaftaran ulang calon siswa baru.')) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: Checklist Dokumen Persyaratan -->
        <div x-show="activeTab === 'requirements'" x-transition class="space-y-6">
            <div class="premium-card overflow-hidden">
                <div class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center space-x-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold border border-emerald-100 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Persyaratan Dokumen Administrasi</h2>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Daftar Berkas & Persyaratan Yang Harus Disiapkan</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full uppercase border border-emerald-100">Dokumen</span>
                </div>

                <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Requirement 1 -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-xl bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center shadow-sm">1</span>
                            <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Dokumen 1</span>
                        </div>
                        <input type="text" name="spmb_doc_req_1_title" value="{{ old('spmb_doc_req_1_title', Setting::get('spmb_doc_req_1_title', 'Fotokopi Ijazah / SKL SMP')) }}" placeholder="Judul Dokumen 1"
                               class="w-full px-4 h-10 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <textarea name="spmb_doc_req_1_desc" rows="2" placeholder="Keterangan Dokumen 1"
                                  class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-600 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">{{ old('spmb_doc_req_1_desc', Setting::get('spmb_doc_req_1_desc', 'Surat Keterangan Lulus resmi atau Ijazah SMP/MTs yang dilegalisir oleh pihak sekolah.')) }}</textarea>
                    </div>

                    <!-- Requirement 2 -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shadow-sm">2</span>
                            <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Dokumen 2</span>
                        </div>
                        <input type="text" name="spmb_doc_req_2_title" value="{{ old('spmb_doc_req_2_title', Setting::get('spmb_doc_req_2_title', 'Kartu Keluarga & Akta Kelahiran')) }}" placeholder="Judul Dokumen 2"
                               class="w-full px-4 h-10 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <textarea name="spmb_doc_req_2_desc" rows="2" placeholder="Keterangan Dokumen 2"
                                  class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-600 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">{{ old('spmb_doc_req_2_desc', Setting::get('spmb_doc_req_2_desc', 'Fotokopi Kartu Keluarga dan Akta Kelahiran calon siswa yang terdaftar resmi NIK.')) }}</textarea>
                    </div>

                    <!-- Requirement 3 -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-xl bg-purple-600 text-white font-extrabold text-xs flex items-center justify-center shadow-sm">3</span>
                            <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Dokumen 3</span>
                        </div>
                        <input type="text" name="spmb_doc_req_3_title" value="{{ old('spmb_doc_req_3_title', Setting::get('spmb_doc_req_3_title', 'Pasfoto Terbaru (3x4)')) }}" placeholder="Judul Dokumen 3"
                               class="w-full px-4 h-10 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <textarea name="spmb_doc_req_3_desc" rows="2" placeholder="Keterangan Dokumen 3"
                                  class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-600 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">{{ old('spmb_doc_req_3_desc', Setting::get('spmb_doc_req_3_desc', '2 lembar pasfoto berwarna terbaru dengan latar belakang merah atau biru.')) }}</textarea>
                    </div>

                    <!-- Requirement 4 -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-extrabold text-xs flex items-center justify-center shadow-sm">4</span>
                            <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Dokumen 4 (Opsional)</span>
                        </div>
                        <input type="text" name="spmb_doc_req_4_title" value="{{ old('spmb_doc_req_4_title', Setting::get('spmb_doc_req_4_title', 'Sertifikat Prestasi (Jika Ada)')) }}" placeholder="Judul Dokumen 4"
                               class="w-full px-4 h-10 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-800 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">
                        <textarea name="spmb_doc_req_4_desc" rows="2" placeholder="Keterangan Dokumen 4"
                                  class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-600 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500">{{ old('spmb_doc_req_4_desc', Setting::get('spmb_doc_req_4_desc', 'Sertifikat lomba/kejuaraan minimal tingkat kota/kabupaten untuk jalur beasiswa prestasi.')) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200/80">
            <button type="submit" class="inline-flex items-center justify-center h-11 px-6 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 text-white font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-indigo-500/25 transition-all duration-200 gap-2 hover:scale-[1.02] active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Pengaturan SPMB</span>
            </button>
        </div>
    </form>
</div>
@endsection
