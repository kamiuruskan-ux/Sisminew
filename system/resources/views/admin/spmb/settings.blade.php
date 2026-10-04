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

        <button type="button" @click="activeTab = 'whatsapp'" 
                :class="activeTab === 'whatsapp' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="inline-flex items-center justify-center h-11 px-5 rounded-2xl font-extrabold text-xs transition-all duration-200 gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
            <span>Template WhatsApp</span>
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
                        @php
                            $currentBadge = Setting::get('spmb_hero_badge');
                            if (!$currentBadge || str_contains($currentBadge, '2026/2027')) {
                                $currentBadge = 'PENERIMAAN SISWA BARU T.A. ' . ($spmbAcademicYear ?? '2027/2028');
                            }
                        @endphp
                        <input type="text" name="spmb_hero_badge" value="{{ old('spmb_hero_badge', $currentBadge) }}" placeholder="PENERIMAAN SISWA BARU T.A. {{ $spmbAcademicYear ?? '2027/2028' }}"
                               class="w-full px-4 h-11 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">
                        <p class="text-[11px] text-slate-400 font-medium">Teks badge kecil di bagian paling atas hero banner. Otomatis tersinkronisasi dengan Tahun Akademik Gelombang aktif ({{ $spmbAcademicYear ?? '2027/2028' }}).</p>
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

                    <!-- Teks Tombol Aksi Hero (CTA) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Teks Tombol Pendaftaran Utama</label>
                            <input type="text" name="spmb_hero_cta_text" value="{{ old('spmb_hero_cta_text', Setting::get('spmb_hero_cta_text', 'Daftar SPMB Online Now')) }}" placeholder="Daftar SPMB Online Now"
                                   class="w-full px-4 h-11 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Teks Tombol Konsultasi</label>
                            <input type="text" name="spmb_hero_consult_text" value="{{ old('spmb_hero_consult_text', Setting::get('spmb_hero_consult_text', 'Konsultasi Pendaftaran')) }}" placeholder="Konsultasi Pendaftaran"
                                   class="w-full px-4 h-11 bg-slate-50/80 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 outline-none focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-2xs">
                        </div>
                    </div>

                    <!-- 3 Poin Keunggulan (Checkmark) di Bawah Tombol -->
                    <div class="pt-2 border-t border-slate-100">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">3 Poin Fitur / Keunggulan (Di Bawah Tombol Hero)</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1.5">
                                <span class="text-[11px] font-bold text-slate-500">Poin 1 (Kiri)</span>
                                <input type="text" name="spmb_hero_feature_1" value="{{ old('spmb_hero_feature_1', Setting::get('spmb_hero_feature_1', 'Pendaftaran 100% Online')) }}" placeholder="Pendaftaran 100% Online"
                                       class="w-full px-3 h-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="space-y-1.5">
                                <span class="text-[11px] font-bold text-slate-500">Poin 2 (Tengah) / Beasiswa</span>
                                <input type="text" name="spmb_scholarship_info" value="{{ old('spmb_scholarship_info', Setting::get('spmb_scholarship_info', 'Potongan SPP Beasiswa')) }}" placeholder="Potongan SPP Beasiswa"
                                       class="w-full px-3 h-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="space-y-1.5">
                                <span class="text-[11px] font-bold text-slate-500">Poin 3 (Kanan)</span>
                                <input type="text" name="spmb_hero_feature_3" value="{{ old('spmb_hero_feature_3', Setting::get('spmb_hero_feature_3', 'Proses Seleksi Transparan')) }}" placeholder="Proses Seleksi Transparan"
                                       class="w-full px-3 h-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Informasi Pendaftaran (Kotak Kanan Hero) -->
                    <div class="pt-4 border-t border-slate-100 space-y-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Kartu Kanan Hero (Kotak Status Pendaftaran)</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide">Label Status Atas</label>
                                <input type="text" name="spmb_card_badge" value="{{ old('spmb_card_badge', Setting::get('spmb_card_badge', 'STATUS PENDAFTARAN')) }}" placeholder="STATUS PENDAFTARAN"
                                       class="w-full px-3 h-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide">Judul Kartu</label>
                                <input type="text" name="spmb_card_title" value="{{ old('spmb_card_title', Setting::get('spmb_card_title', 'Gelombang Pendaftaran')) }}" placeholder="Gelombang Pendaftaran"
                                       class="w-full px-3 h-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide">Teks Tombol Kartu</label>
                                <input type="text" name="spmb_card_cta_text" value="{{ old('spmb_card_cta_text', Setting::get('spmb_card_cta_text', 'Isi Formulir Pendaftaran Sekarang')) }}" placeholder="Isi Formulir Pendaftaran Sekarang"
                                       class="w-full px-3 h-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Judul Pilihan Gelombang Pendaftaran (Section 2) -->
                    <div class="pt-4 border-t border-slate-100 space-y-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Bagian Pilihan Gelombang Pendaftaran (Di Bawah Hero)</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide">Badge Sub-Judul</label>
                                <input type="text" name="spmb_wave_badge" value="{{ old('spmb_wave_badge', Setting::get('spmb_wave_badge', 'GELOMBANG & JALUR SELEKSI')) }}" placeholder="GELOMBANG & JALUR SELEKSI"
                                       class="w-full px-3 h-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide">Judul Utama Bagian</label>
                                <input type="text" name="spmb_wave_title" value="{{ old('spmb_wave_title', Setting::get('spmb_wave_title', 'Pilihan Gelombang Pendaftaran')) }}" placeholder="Pilihan Gelombang Pendaftaran"
                                       class="w-full px-3 h-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide">Deskripsi Singkat Bagian Gelombang</label>
                            <input type="text" name="spmb_wave_subtitle" value="{{ old('spmb_wave_subtitle', Setting::get('spmb_wave_subtitle', 'Membuka beberapa jalur pendaftaran dengan fasilitas beasiswa menarik pada setiap gelombangnya.')) }}" placeholder="Membuka beberapa jalur pendaftaran..."
                                   class="w-full px-3 h-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:bg-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="space-y-2 pt-4 border-t border-slate-100">
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

        <!-- TAB 5: Template WhatsApp Follow-Up -->
        <div x-show="activeTab === 'whatsapp'" x-transition class="space-y-6">
            <div class="premium-card p-6 sm:p-8 bg-white border border-slate-200/90 rounded-3xl shadow-sm space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-700 font-extrabold text-[11px] rounded-full uppercase tracking-wider mb-2 border border-emerald-200/70">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Template Pesan WhatsApp Otomatis</span>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Kustomisasi Pesan Chat Panitia SPMB</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Atur redaksi pesan template saat panitia menghubungi orang tua / calon siswa via WhatsApp. Anda dapat menggunakan variabel cerdas di bawah ini yang akan otomatis diganti dengan data siswa bersangkutan.
                    </p>
                </div>

                <!-- Variable Badges -->
                <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-xs text-indigo-950 space-y-2">
                    <p class="font-black text-indigo-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Variabel Dinamis yang Bisa Disisipkan ke Template:</span>
                    </p>
                    <div class="flex flex-wrap gap-2 pt-1 font-mono text-[11px]">
                        <span class="px-2 py-1 bg-white rounded-lg border border-indigo-200 text-indigo-700 font-bold">{nama_siswa}</span>
                        <span class="px-2 py-1 bg-white rounded-lg border border-indigo-200 text-indigo-700 font-bold">{nama_orang_tua}</span>
                        <span class="px-2 py-1 bg-white rounded-lg border border-indigo-200 text-indigo-700 font-bold">{no_daftar}</span>
                        <span class="px-2 py-1 bg-white rounded-lg border border-indigo-200 text-indigo-700 font-bold">{sekolah}</span>
                        <span class="px-2 py-1 bg-white rounded-lg border border-indigo-200 text-indigo-700 font-bold">{gelombang}</span>
                        <span class="px-2 py-1 bg-white rounded-lg border border-indigo-200 text-indigo-700 font-bold">{link_login}</span>
                        <span class="px-2 py-1 bg-white rounded-lg border border-indigo-200 text-indigo-700 font-bold">{kontak_spmb}</span>
                    </div>
                </div>

                <!-- Template 1: Pendaftar Sudah Bayar Tapi Belum Lengkapi Formulir (Draft) -->
                <div class="space-y-3 pt-2">
                    <label class="block">
                        <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span>1. Pesan Pengingat Formulir (Sudah Bayar Lunas Tapi Belum Lengkapi Data)</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Status: Draft</span>
                        </span>
                        <span class="text-[11px] text-slate-500 block mt-0.5">Pesan ini dipakai ketika admin mengklik nomor WA pendaftar yang sudah bayar namun formulirnya masih Draft.</span>
                    </label>
                    <textarea name="spmb_wa_template_draft" rows="7" 
                              class="w-full p-4 bg-slate-50/50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-700 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 leading-relaxed font-mono">{{ old('spmb_wa_template_draft', Setting::get('spmb_wa_template_draft', "Halo Bapak/Ibu orang tua dari *{nama_siswa}*,\n\nKami dari panitia SPMB {sekolah} menginformasikan bahwa pembayaran uang pendaftaran ananda (No. Registrasi: *{no_daftar}*) telah berhasil terkonfirmasi lunas.\n\nNamun, formulir pendaftaran siswa tercatat masih berstatus *Draft (belum selesai diisi)*.\n\nMohon untuk segera login ke portal SPMB guna melengkapi formulir data diri dan mengunggah dokumen persyaratan di tautan berikut:\n{link_login}\n\nTerima kasih atas kerja samanya.")) }}</textarea>
                </div>

                <!-- Template 2: Pesan Umum Hubungi Pendaftar -->
                <div class="space-y-3 pt-2">
                    <label class="block">
                        <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span>2. Pesan Umum Hubungi Pendaftar</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Standar / Status Lain</span>
                        </span>
                        <span class="text-[11px] text-slate-500 block mt-0.5">Pesan ini dipakai untuk menghubungi pendaftar dengan status pendaftaran umum/lainnya.</span>
                    </label>
                    <textarea name="spmb_wa_template_general" rows="5" 
                              class="w-full p-4 bg-slate-50/50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-700 outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 leading-relaxed font-mono">{{ old('spmb_wa_template_general', Setting::get('spmb_wa_template_general', "Halo Bapak/Ibu orang tua dari *{nama_siswa}*,\n\nKami dari panitia SPMB {sekolah} menghubungi terkait pendaftaran calon siswa baru ananda dengan No. Registrasi: *{no_daftar}*.\n\nPortal SPMB: {link_login}\n\nTerima kasih.")) }}</textarea>
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
