@extends('layouts.admin')

@section('title', 'Pengaturan Template Kartu Siswa')
@section('page_title', 'Pengaturan & Desain Kartu Tanda Siswa')

@section('content')
<div class="space-y-8 w-full pb-28" x-data="{
    subtitle: '{{ addslashes(Setting::get('student_card_header_subtitle', 'KARTU TANDA SISWA RESMI (KTS)')) }}',
    principalName: '{{ addslashes(Setting::get('school_principal_name', 'Dr. H. Ahmad Wijaya, M.Pd.')) }}',
    accentColor: '{{ Setting::get('student_card_accent_color', '#4f46e5') }}',
    accentColorEnd: '{{ Setting::get('student_card_accent_color_end', Setting::get('student_card_accent_color', '#4f46e5')) }}',
    orientation: '{{ Setting::get('student_card_orientation', 'landscape') }}',
    rulesRaw: `{{ addslashes(Setting::get('student_card_rules', "1. Kartu ini adalah identitas resmi siswa sekolah.\n2. Wajib dibawa & ditunjukkan saat presensi QR.\n3. Kartu tidak dapat dipindahtangankan.")) }}`,

    get accentGradient() {
        return (this.accentColor === this.accentColorEnd) 
            ? this.accentColor 
            : 'linear-gradient(135deg, ' + this.accentColor + ', ' + this.accentColorEnd + ')';
    },
    
    bgFrontUrl: '{{ Setting::get('student_card_bg_front_path') ? asset(Setting::get('student_card_bg_front_path')) : '' }}',
    bgBackUrl: '{{ Setting::get('student_card_bg_back_path') ? asset(Setting::get('student_card_bg_back_path')) : '' }}',
    stampUrl: '{{ Setting::get('student_card_stamp_path') ? asset(Setting::get('student_card_stamp_path')) : '' }}',
    signatureUrl: '{{ Setting::get('student_card_signature_path') ? asset(Setting::get('student_card_signature_path')) : '' }}',

    deleteFront: false,
    deleteBack: false,
    deleteStamp: false,
    deleteSignature: false,

    get rulesArray() {
        return this.rulesRaw.split('\n').filter(r => r.trim() !== '');
    },

    handleFile(event, key) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this[key] = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
}">
    <!-- Hero Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-purple-950 to-slate-900 p-6 sm:p-8 lg:p-10 text-white shadow-2xl shadow-purple-950/20 border border-slate-800/80">
        <!-- Ambient Lights -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-24 w-96 h-96 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/10 text-xs font-extrabold text-purple-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Desain & Template Kartu Pelajar Custom</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Pengaturan Template Kartu Siswa (KTS)
                </h1>
                <p class="text-xs sm:text-sm text-purple-100/80 leading-relaxed font-medium">
                    Kustomisasi orientasi (Landscape / Portrait), background depan & belakang, stempel resmi, tanda tangan kepala sekolah, subtitle header, serta poin tata tertib kartu secara dinamis & real-time.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('admin.student-cards.index') }}" 
                   class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs transition-all border border-white/10 flex items-center space-x-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Cetak Kartu</span>
                </a>
            </div>
        </div>
    </div>


    <!-- Main Grid Layout: Form Settings (Left 7 Cols) & Live Preview (Right 5 Cols) -->
    <div class="grid lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN: Form Settings -->
        <div class="lg:col-span-7 space-y-8">
            <form method="POST" action="{{ route('admin.student-cards.update-settings') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Hidden inputs marker for image deletion -->
                <input type="hidden" name="delete_student_card_bg_front" :value="deleteFront ? '1' : '0'">
                <input type="hidden" name="delete_student_card_bg_back" :value="deleteBack ? '1' : '0'">
                <input type="hidden" name="delete_student_card_stamp" :value="deleteStamp ? '1' : '0'">
                <input type="hidden" name="delete_student_card_signature" :value="deleteSignature ? '1' : '0'">

                <!-- Card Orientation Option Box -->
                <div class="premium-card p-6 sm:p-8 space-y-6 shadow-md border-slate-200">
                    <div class="flex items-center space-x-3.5 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-extrabold border border-amber-100 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/><path d="M12 4v16"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Orientasi Tata Letak Kartu (Portrait / Landscape)</h3>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Pilih orientasi cetak KTS sesuai kebutuhan sekolah</p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <!-- Option Landscape -->
                        <label @click="orientation = 'landscape'"
                               class="relative flex flex-col p-4 rounded-2xl border-2 transition-all cursor-pointer shadow-2xs"
                               :class="orientation === 'landscape' ? 'border-purple-600 bg-purple-50/50 ring-4 ring-purple-500/10' : 'border-slate-200 bg-white hover:border-slate-300'">
                            <input type="radio" name="student_card_orientation" value="landscape" x-model="orientation" class="sr-only">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-black text-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2"/></svg>
                                </div>
                                <span x-show="orientation === 'landscape'" class="w-5 h-5 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px] font-bold">✓</span>
                            </div>
                            <span class="font-extrabold text-xs text-slate-900">Landscape (Mendatar)</span>
                            <span class="text-[10px] text-slate-500 mt-1 font-medium">CR-80 Mendatar (85.6mm x 54mm)</span>
                        </label>

                        <!-- Option Portrait -->
                        <label @click="orientation = 'portrait'"
                               class="relative flex flex-col p-4 rounded-2xl border-2 transition-all cursor-pointer shadow-2xs"
                               :class="orientation === 'portrait' ? 'border-purple-600 bg-purple-50/50 ring-4 ring-purple-500/10' : 'border-slate-200 bg-white hover:border-slate-300'">
                            <input type="radio" name="student_card_orientation" value="portrait" x-model="orientation" class="sr-only">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="6" y="3" width="12" height="18" rx="2"/></svg>
                                </div>
                                <span x-show="orientation === 'portrait'" class="w-5 h-5 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px] font-bold">✓</span>
                            </div>
                            <span class="font-extrabold text-xs text-slate-900">Portrait (Tegak)</span>
                            <span class="text-[10px] text-slate-500 mt-1 font-medium">CR-80 Vertikal (54mm x 85.6mm)</span>
                        </label>
                    </div>
                </div>

                <!-- Card Background Templates Box -->
                <div class="premium-card p-6 sm:p-8 space-y-6 shadow-md border-slate-200">
                    <div class="flex items-center space-x-3.5 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-extrabold border border-purple-100 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Gambar Background Kartu Custom</h3>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Upload background khusus untuk tampilan depan & belakang (Rasio CR-80)</p>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6">
                        <!-- Front Card Background Upload -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-3.5">
                            <div class="flex items-center justify-between min-h-[32px]">
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Background Kartu Depan</h4>
                                    <p class="text-[10px] font-semibold text-slate-400" x-text="orientation === 'portrait' ? 'Rasio 638 x 1011 px (CR-80 Portrait)' : 'Rasio 1011 x 638 px (CR-80)'"></p>
                                </div>
                                <template x-if="bgFrontUrl">
                                    <button type="button" @click="bgFrontUrl = ''; deleteFront = true" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 hover:border-rose-300 transition-all cursor-pointer shadow-2xs font-extrabold text-[10px] shrink-0">
                                        <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                </template>
                            </div>

                            <div class="relative group rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 group-hover:border-purple-400 transition-all overflow-hidden flex flex-col items-center justify-center cursor-pointer shadow-2xs"
                                 :class="orientation === 'portrait' ? 'aspect-[1/1.585]' : 'aspect-[1.585/1]'">
                                <template x-if="bgFrontUrl">
                                    <div class="w-full h-full relative">
                                        <img :src="bgFrontUrl" alt="Background Depan" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white p-3 text-center">
                                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mb-1 text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            </div>
                                            <span class="text-xs font-extrabold">Ganti Background</span>
                                            <span class="text-[9px] text-purple-200 mt-0.5">Klik untuk memilih file baru</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!bgFrontUrl">
                                    <div class="flex flex-col items-center justify-center text-center p-4">
                                        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center mb-2 shadow-2xs group-hover:scale-110 transition-transform">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        </div>
                                        <p class="text-xs font-extrabold text-slate-800 group-hover:text-purple-700 transition-colors">Pilih / Upload Gambar</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5 font-medium">Format PNG, JPG, WEBP</p>
                                        <span class="px-3.5 py-1.5 rounded-xl bg-white group-hover:bg-purple-600 text-slate-700 group-hover:text-white border border-slate-200 group-hover:border-purple-600 text-[10px] font-extrabold shadow-2xs transition-all mt-2.5">
                                            Browse File
                                        </span>
                                    </div>
                                </template>
                                <input type="file" name="student_card_bg_front" accept="image/*" @change="handleFile($event, 'bgFrontUrl'); deleteFront = false;" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-20">
                            </div>
                        </div>

                        <!-- Back Card Background Upload -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs space-y-3.5">
                            <div class="flex items-center justify-between min-h-[32px]">
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Background Kartu Belakang</h4>
                                    <p class="text-[10px] font-semibold text-slate-400" x-text="orientation === 'portrait' ? 'Rasio 638 x 1011 px (CR-80 Portrait)' : 'Rasio 1011 x 638 px (CR-80)'"></p>
                                </div>
                                <template x-if="bgBackUrl">
                                    <button type="button" @click="bgBackUrl = ''; deleteBack = true" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 hover:border-rose-300 transition-all cursor-pointer shadow-2xs font-extrabold text-[10px] shrink-0">
                                        <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                </template>
                            </div>

                            <div class="relative group rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 group-hover:border-purple-400 transition-all overflow-hidden flex flex-col items-center justify-center cursor-pointer shadow-2xs"
                                 :class="orientation === 'portrait' ? 'aspect-[1/1.585]' : 'aspect-[1.585/1]'">
                                <template x-if="bgBackUrl">
                                    <div class="w-full h-full relative">
                                        <img :src="bgBackUrl" alt="Background Belakang" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white p-3 text-center">
                                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mb-1 text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            </div>
                                            <span class="text-xs font-extrabold">Ganti Background</span>
                                            <span class="text-[9px] text-purple-200 mt-0.5">Klik untuk memilih file baru</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!bgBackUrl">
                                    <div class="flex flex-col items-center justify-center text-center p-4">
                                        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center mb-2 shadow-2xs group-hover:scale-110 transition-transform">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        </div>
                                        <p class="text-xs font-extrabold text-slate-800 group-hover:text-purple-700 transition-colors">Pilih / Upload Gambar</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5 font-medium">Format PNG, JPG, WEBP</p>
                                        <span class="px-3.5 py-1.5 rounded-xl bg-white group-hover:bg-purple-600 text-slate-700 group-hover:text-white border border-slate-200 group-hover:border-purple-600 text-[10px] font-extrabold shadow-2xs transition-all mt-2.5">
                                            Browse File
                                        </span>
                                    </div>
                                </template>
                                <input type="file" name="student_card_bg_back" accept="image/*" @change="handleFile($event, 'bgBackUrl'); deleteBack = false;" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-20">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Content & Header Settings -->
                <div class="premium-card p-6 sm:p-8 space-y-6 shadow-md border-slate-200">
                    <div class="flex items-center space-x-3.5 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold border border-indigo-100 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Identitas Teks & Header Kartu</h3>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Atur judul subtitle header kartu dan warna aksen tampilan</p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Subtitle Header Kartu Depan</label>
                            <input type="text" name="student_card_header_subtitle" x-model="subtitle"
                                   class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all font-extrabold text-xs text-slate-800 shadow-2xs">
                        </div>

                        <!-- Accent Colors (Gradasi) -->
                        <div class="space-y-3">
                            <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Warna Aksen Header Kartu (Gradasi 2 Warna)</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Warna Utama / Awal -->
                                <div class="space-y-1.5">
                                    <span class="text-[10px] font-bold text-slate-500">Warna Awal (Mulai)</span>
                                    <div class="flex items-center space-x-2.5">
                                        <input type="color" name="student_card_accent_color" x-model="accentColor"
                                               class="w-11 h-10 rounded-xl cursor-pointer border border-slate-200 p-1 bg-white shadow-2xs">
                                        <input type="text" x-model="accentColor" readonly class="w-full px-3 py-2 bg-slate-100 rounded-xl font-mono text-xs font-bold text-slate-700 border border-slate-200/80">
                                    </div>
                                </div>
                                <!-- Warna Akhir / Gradasi -->
                                <div class="space-y-1.5">
                                    <span class="text-[10px] font-bold text-slate-500">Warna Akhir (Gradasi)</span>
                                    <div class="flex items-center space-x-2.5">
                                        <input type="color" name="student_card_accent_color_end" x-model="accentColorEnd"
                                               class="w-11 h-10 rounded-xl cursor-pointer border border-slate-200 p-1 bg-white shadow-2xs">
                                        <input type="text" x-model="accentColorEnd" readonly class="w-full px-3 py-2 bg-slate-100 rounded-xl font-mono text-xs font-bold text-slate-700 border border-slate-200/80">
                                    </div>
                                </div>
                            </div>

                            <!-- Live Gradient Preview Bar -->
                            <div class="p-3 rounded-xl border border-slate-200/80 bg-slate-50 flex items-center justify-between gap-3">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Preview Gradasi Header:</span>
                                <div class="h-6 flex-1 rounded-lg shadow-inner border border-white/40 transition-all duration-300"
                                     :style="'background: ' + accentGradient"></div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <div class="p-4 bg-purple-50/80 border border-purple-200/80 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                                <div class="flex items-center space-x-3 text-purple-900 font-semibold">
                                    <div class="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center font-extrabold shrink-0 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-purple-950">Stempel & Tanda Tangan Digital Kepsek</p>
                                        <p class="text-[11px] text-purple-700 font-medium">Upload berkas stempel & tanda tangan transparan kini dikelola terpusat di Profile Pimpinan.</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.settings', ['tab' => 'profile_principal']) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition-all shrink-0">
                                    Kelola Stempel & TTD
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rules Box Settings -->
                <div class="premium-card p-6 sm:p-8 space-y-6 shadow-md border-slate-200">
                    <div class="flex items-center space-x-3.5 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold border border-emerald-100 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Tata Tertib Kartu Belakang</h3>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Tuliskan poin-poin ketentuan penggunaan kartu (1 poin per baris)</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <textarea name="student_card_rules" rows="4" x-model="rulesRaw"
                                  class="w-full px-4 py-3 bg-slate-50/80 border border-slate-200/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all font-semibold text-xs text-slate-800 shadow-2xs leading-relaxed"
                                  placeholder="Tuliskan poin-poin tata tertib kartu (satu poin per baris)..."></textarea>
                        <p class="text-[11px] text-amber-700 font-semibold bg-amber-50 border border-amber-200/80 p-2.5 rounded-xl flex items-center space-x-2">
                            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Disarankan 3-4 poin singkat agar tanda tangan & stempel kepala sekolah tetap proporsional dan tidak tergeser.</span>
                        </p>
                    </div>
                </div>

                <!-- Submit Action Button -->
                <div class="flex items-center justify-end space-x-4 pt-2">
                    <a href="{{ route('admin.student-cards.index') }}" class="px-6 py-3 text-slate-500 hover:text-slate-900 font-extrabold text-xs uppercase tracking-wider transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-purple-600 via-indigo-600 to-indigo-700 hover:from-purple-500 hover:to-indigo-600 text-white rounded-2xl font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-purple-600/30 hover:scale-105 active:scale-95 transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Pengaturan Kartu</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT COLUMN: Real-Time Interactive Live Card Preview -->
        <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-8">
            <div class="premium-card p-6 shadow-xl border-purple-100 bg-slate-950 text-white space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <h3 class="font-extrabold text-xs uppercase tracking-wider text-purple-200">Real-Time Live Card Preview</h3>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400" x-text="orientation === 'portrait' ? 'CR-80 Portrait (54 x 85.6mm)' : 'CR-80 Landscape (85.6 x 54mm)'"></span>
                </div>

                <!-- Real-Time Interactive Card Container -->
                <div class="flex flex-col items-center gap-6 py-2 transition-all duration-300">
                    
                    <!-- FRONT CARD PREVIEW -->
                    <div class="card-wrapper flex flex-col items-center gap-1.5 transition-all duration-300">
                        <span class="card-label text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Tampilan Depan (Front)</span>
                        <div class="kts-card bg-white rounded-2xl relative overflow-hidden shadow-2xl border border-slate-200 flex flex-col justify-between transition-all duration-300"
                             :class="orientation === 'portrait' ? 'w-[265px] h-[420px]' : 'w-[420px] h-[263px]'"
                             :style="bgFrontUrl ? 'background: url(' + bgFrontUrl + ') center/cover no-repeat;' : ''">
                            
                            <!-- Header -->
                            <div class="kts-header text-white px-3 py-2 flex items-center justify-between z-10 shrink-0 shadow-xs"
                                 :style="'background:' + accentGradient">
                                <div class="kts-header-brand flex items-center space-x-2 overflow-hidden">
                                    <div class="kts-logo-icon bg-white/10 border border-white/20 flex items-center justify-center text-indigo-300 shrink-0 overflow-hidden"
                                         :class="orientation === 'portrait' ? 'w-[22px] h-[22px] rounded-md' : 'w-6 h-6 rounded-md'">
                                        @if(Setting::get('logo_path'))
                                            <img src="{{ asset(Setting::get('logo_path')) }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                        @endif
                                    </div>
                                    <div class="kts-header-titles overflow-hidden">
                                        <h2 class="text-[10.5px] font-black uppercase tracking-wider text-white leading-tight truncate">{{ Setting::get('school_name', 'SEKOLAH INDONESIA') }}</h2>
                                        <p class="text-[7.5px] font-semibold text-slate-300 truncate" x-text="subtitle"></p>
                                    </div>
                                </div>
                                <span class="kts-tag bg-indigo-500/30 border border-indigo-400/40 text-indigo-200 text-[7.5px] font-extrabold px-2 py-0.5 rounded uppercase shrink-0">KTS</span>
                            </div>

                            <!-- Body (Landscape View) -->
                            <template x-if="orientation === 'landscape'">
                                <div class="kts-body flex-1 p-3 grid grid-cols-[90px_1fr] gap-3 items-center z-10"
                                     :class="bgFrontUrl ? 'bg-white/85 backdrop-blur-xs' : 'bg-gradient-to-br from-slate-50 to-white'">
                                    <div class="kts-photo-box flex flex-col items-center space-y-1">
                                        <div class="kts-photo-frame w-[86px] h-[110px] rounded-xl overflow-hidden border-2 border-slate-300 shadow-md bg-slate-100 flex items-center justify-center shrink-0">
                                            @if($sampleStudent?->photo_url)
                                                <img src="{{ $sampleStudent->photo_url }}" alt="Photo" class="w-full h-full object-cover">
                                            @else
                                                <div class="kts-photo-fallback w-full h-full bg-slate-200 text-slate-600 font-extrabold text-2xl flex items-center justify-center">
                                                    {{ strtoupper(substr($sampleStudent->user->name ?? 'S', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="kts-details flex flex-col space-y-1 text-slate-900">
                                        <div class="kts-student-name font-black text-xs text-slate-900 border-b border-slate-200 pb-0.5 leading-tight truncate">
                                            {{ $sampleStudent->user->name ?? '-' }}
                                        </div>

                                        <div class="kts-detail-row grid grid-cols-[65px_1fr] text-[9.5px] leading-tight">
                                            <span class="kts-detail-label font-bold text-slate-500">NISN</span>
                                            <span class="kts-detail-val font-mono font-extrabold" :style="'color:' + accentColor">{{ $sampleStudent->nisn ?? '1234567890' }}</span>
                                        </div>

                                        <div class="kts-detail-row grid grid-cols-[65px_1fr] text-[9.5px] leading-tight">
                                            <span class="kts-detail-label font-bold text-slate-500">NIK</span>
                                            <span class="kts-detail-val font-bold text-slate-800">{{ $sampleStudent->nik ?? '3171012345678901' }}</span>
                                        </div>

                                        <div class="kts-detail-row grid grid-cols-[65px_1fr] text-[9.5px] leading-tight">
                                            <span class="kts-detail-label font-bold text-slate-500">Kelas</span>
                                            <span class="kts-detail-val font-extrabold text-slate-900">{{ $sampleStudent->class?->name ?? 'X IPA 1' }}</span>
                                        </div>

                                        @if($sampleStudent->major)
                                            <div class="kts-detail-row grid grid-cols-[65px_1fr] text-[9.5px] leading-tight">
                                                <span class="kts-detail-label font-bold text-slate-500">Jurusan</span>
                                                <span class="kts-detail-val font-bold text-slate-800 truncate">{{ $sampleStudent->major->name }}</span>
                                            </div>
                                        @endif

                                        <div class="kts-detail-row grid grid-cols-[65px_1fr] text-[9.5px] leading-tight">
                                            <span class="kts-detail-label font-bold text-slate-500">Gender</span>
                                            <span class="kts-detail-val font-bold text-slate-800">{{ $sampleStudent->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Body (Portrait View - Clean Full-Width Layout) -->
                            <template x-if="orientation === 'portrait'">
                                <div class="kts-body flex-1 p-3.5 flex flex-col justify-between items-center text-center z-10 overflow-hidden text-slate-900"
                                     :class="bgFrontUrl ? 'bg-white/85 backdrop-blur-xs' : 'bg-gradient-to-br from-slate-50 to-white'">
                                    
                                    <!-- Foto & Nama di Atas -->
                                    <div class="flex flex-col items-center w-full my-auto shrink-0">
                                        <div class="kts-photo-frame w-[92px] h-[118px] rounded-xl overflow-hidden border-2 border-slate-300 shadow-md bg-slate-100 flex items-center justify-center shrink-0 mb-2">
                                            @if($sampleStudent?->photo_url)
                                                <img src="{{ $sampleStudent->photo_url }}" alt="Photo" class="w-full h-full object-cover">
                                            @else
                                                <div class="kts-photo-fallback w-full h-full bg-slate-200 text-slate-600 font-extrabold text-2xl flex items-center justify-center">
                                                    {{ strtoupper(substr($sampleStudent->user->name ?? 'S', 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>

                                        <h3 class="font-black text-xs text-slate-900 tracking-wide uppercase text-center w-full truncate border-b border-slate-200/80 pb-1 mb-1 leading-tight">{{ $sampleStudent->user->name ?? '-' }}</h3>
                                    </div>

                                    <!-- Clean Full-Width Details List -->
                                    <div class="w-full space-y-1 text-left my-auto px-1">
                                        <div class="kts-detail-row grid grid-cols-[60px_1fr] text-[9.5px] leading-tight py-1 border-b border-slate-100">
                                            <span class="kts-detail-label font-bold text-slate-400 uppercase text-[8px] tracking-wider">NISN</span>
                                            <span class="kts-detail-val font-mono font-extrabold" :style="'color:' + accentColor">{{ $sampleStudent->nisn ?? '1234567890' }}</span>
                                        </div>

                                        <div class="kts-detail-row grid grid-cols-[60px_1fr] text-[9.5px] leading-tight py-1 border-b border-slate-100">
                                            <span class="kts-detail-label font-bold text-slate-400 uppercase text-[8px] tracking-wider">NIK</span>
                                            <span class="kts-detail-val font-bold text-slate-800">{{ $sampleStudent->nik ?? '3171012345678901' }}</span>
                                        </div>

                                        <div class="kts-detail-row grid grid-cols-[60px_1fr] text-[9.5px] leading-tight py-1 border-b border-slate-100">
                                            <span class="kts-detail-label font-bold text-slate-400 uppercase text-[8px] tracking-wider">Kelas</span>
                                            <span class="kts-detail-val font-extrabold text-slate-900">{{ $sampleStudent->class?->name ?? 'X IPA 1' }}</span>
                                        </div>

                                        @if($sampleStudent->major)
                                            <div class="kts-detail-row grid grid-cols-[60px_1fr] text-[9.5px] leading-tight py-1 border-b border-slate-100">
                                                <span class="kts-detail-label font-bold text-slate-400 uppercase text-[8px] tracking-wider">Jurusan</span>
                                                <span class="kts-detail-val font-bold text-slate-800 truncate">{{ $sampleStudent->major->name }}</span>
                                            </div>
                                        @endif

                                        <div class="kts-detail-row grid grid-cols-[60px_1fr] text-[9.5px] leading-tight py-1">
                                            <span class="kts-detail-label font-bold text-slate-400 uppercase text-[8px] tracking-wider">Gender</span>
                                            <span class="kts-detail-val font-bold text-slate-800">{{ $sampleStudent->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                                        </div>
                                    </div>

                                </div>
                            </template>

                            <!-- Footer -->
                            <div class="kts-footer px-3 py-1 flex items-center justify-between text-[7.5px] font-semibold z-10 shrink-0"
                                 :class="bgFrontUrl ? 'bg-slate-950/80 text-slate-300' : 'bg-slate-50 text-slate-500 border-t border-slate-200'">
                                <span>Berlaku Selama Menjadi Siswa</span>
                                <span class="kts-footer-nisn font-mono font-bold">ID: {{ $sampleStudent->nisn ?? '1234567890' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- BACK CARD PREVIEW -->
                    <div class="card-wrapper flex flex-col items-center gap-1.5 transition-all duration-300">
                        <span class="card-label text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Tampilan Belakang (Back)</span>
                        <div class="kts-card bg-white rounded-2xl relative overflow-hidden shadow-2xl border border-slate-200 flex flex-col justify-between transition-all duration-300"
                             :class="orientation === 'portrait' ? 'w-[265px] h-[420px]' : 'w-[420px] h-[263px]'"
                             :style="bgBackUrl ? 'background: url(' + bgBackUrl + ') center/cover no-repeat;' : ''">
                            
                            <!-- Header -->
                            <div class="kts-header text-white px-3 py-2 flex items-center justify-between z-10 shrink-0 shadow-xs"
                                 :style="'background:' + accentGradient">
                                <div class="kts-header-brand flex items-center space-x-2 overflow-hidden">
                                    <div class="kts-logo-icon bg-white/10 border border-white/20 flex items-center justify-center text-indigo-300 shrink-0 overflow-hidden"
                                         :class="orientation === 'portrait' ? 'w-[22px] h-[22px] rounded-md' : 'w-6 h-6 rounded-md'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    </div>
                                    <div class="kts-header-titles overflow-hidden">
                                        <h2 class="text-[10.5px] font-black uppercase tracking-wider text-white leading-tight truncate">QR ABSENSI DIGITAL</h2>
                                        <p class="text-[7.5px] font-semibold text-slate-300 truncate">TATA TERTIB & KETENTUAN KARTU</p>
                                    </div>
                                </div>
                                <span class="kts-tag bg-emerald-500/30 border border-emerald-400/40 text-emerald-300 text-[7.5px] font-extrabold px-2 py-0.5 rounded uppercase shrink-0">SCAN</span>
                            </div>

                            <!-- Body Back Landscape -->
                            <template x-if="orientation === 'landscape'">
                                <div class="kts-back-body flex-1 p-3.5 grid grid-cols-[105px_1fr] gap-4 items-stretch z-10 overflow-hidden"
                                     :class="bgBackUrl ? 'bg-white/85 backdrop-blur-xs' : 'bg-white'">
                                    <div class="kts-qr-container flex flex-col items-center justify-center text-center space-y-1.5 my-auto shrink-0">
                                        <div class="kts-qr-box w-[92px] h-[92px] p-1.5 bg-white border-2 border-indigo-600 rounded-2xl shadow-md flex items-center justify-center overflow-hidden shrink-0 [&_svg]:w-full [&_svg]:h-full [&_svg]:max-w-full [&_svg]:max-h-full [&_svg]:object-contain">
                                            <img src="https://quickchart.io/qr?text={{ urlencode($sampleStudent->nisn ?? '1234567890') }}&size=150" alt="QR Code" class="w-full h-full object-contain">
                                        </div>
                                        <span class="kts-qr-caption font-mono font-extrabold text-[8.5px] text-indigo-700 uppercase tracking-wide">NISN: {{ $sampleStudent->nisn ?? '1234567890' }}</span>
                                    </div>

                                    <div class="kts-rules-box flex flex-col justify-between h-full text-left relative overflow-hidden py-0.5">
                                        <div class="space-y-1.5">
                                            <div class="kts-rules-title text-[9px] font-black text-slate-900 uppercase tracking-wider">KETENTUAN PENGGUNAAN:</div>
                                            <div class="space-y-1.5 max-h-[110px] overflow-hidden">
                                                <template x-for="(rule, idx) in rulesArray" :key="idx">
                                                    <div class="kts-rule-item flex items-center space-x-2 text-[9px] text-slate-700 font-semibold leading-tight">
                                                        <span class="kts-rule-num w-4 h-4 bg-indigo-100 text-indigo-800 font-black rounded-full flex items-center justify-center text-[8px] shrink-0 leading-none shadow-2xs" x-text="idx + 1"></span>
                                                        <span class="line-clamp-2" x-text="rule.replace(/^\d+\.\s*/, '')"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Sign & Stamp Overlay Box -->
                                        <div class="kts-sign-box flex flex-col items-center text-center self-end ml-auto text-[7.5px] text-slate-700 leading-tight relative mt-auto pt-1">
                                            <p class="font-semibold text-slate-500">{{ Setting::get('school_city', 'Jakarta') }}, {{ date('Y') }}</p>
                                            <p class="font-bold text-slate-700">Kepala Sekolah,</p>
                                            
                                            <div class="kts-sign-space h-7 w-full relative flex items-center justify-center my-0.5">
                                                <template x-if="stampUrl">
                                                    <img :src="stampUrl" alt="Stempel" class="absolute left-1/2 -translate-x-1/2 -top-2 h-11 object-contain opacity-55 mix-blend-multiply pointer-events-none z-0">
                                                </template>
                                                <template x-if="signatureUrl">
                                                    <img :src="signatureUrl" alt="TTD" class="absolute inset-0 m-auto h-8 object-contain pointer-events-none z-10">
                                                </template>
                                            </div>
                                            
                                            <p class="kts-sign-name font-extrabold text-slate-900 underline text-center whitespace-nowrap" x-text="principalName"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Body Back Portrait (Compact Grid) -->
                            <template x-if="orientation === 'portrait'">
                                <div class="kts-back-body flex-1 p-3 flex flex-col justify-between text-left z-10 overflow-hidden"
                                     :class="bgBackUrl ? 'bg-white/85 backdrop-blur-xs' : 'bg-white'">
                                    
                                    <!-- Upper Grid: QR Code Kiri & Description Kanan -->
                                    <div class="grid grid-cols-[82px_1fr] gap-2.5 items-center bg-white/70 p-2 rounded-xl border border-slate-200/80 shadow-2xs">
                                        <div class="kts-qr-box w-[82px] h-[82px] p-1 bg-white border-2 border-indigo-600 rounded-xl shadow-sm flex items-center justify-center overflow-hidden shrink-0 [&_svg]:w-full [&_svg]:h-full [&_svg]:max-w-full [&_svg]:max-h-full [&_svg]:object-contain">
                                            <img src="https://quickchart.io/qr?text={{ urlencode($sampleStudent->nisn ?? '1234567890') }}&size=150" alt="QR Code" class="w-full h-full object-contain">
                                        </div>

                                        <div class="space-y-1 overflow-hidden">
                                            <h4 class="text-[9.5px] font-black text-slate-900 uppercase tracking-wider leading-tight">QR PRESENSI</h4>
                                            <p class="text-[7.5px] font-medium text-slate-500 leading-tight">Tunjukkan kode QR ini pada mesin scanner presensi sekolah.</p>
                                            <span class="font-mono font-extrabold text-[8.5px] text-indigo-700 block mt-1">NISN: {{ $sampleStudent->nisn ?? '1234567890' }}</span>
                                        </div>
                                    </div>

                                    <!-- Middle Section: Rules Box -->
                                    <div class="kts-rules-box w-full space-y-1.5 text-left bg-slate-50/90 p-2.5 rounded-xl border border-slate-200/80 my-auto">
                                        <div class="kts-rules-title text-[8.5px] font-black text-slate-900 uppercase tracking-wider">KETENTUAN PENGGUNAAN:</div>
                                        <div class="space-y-1 max-h-[110px] overflow-hidden">
                                            <template x-for="(rule, idx) in rulesArray" :key="idx">
                                                <div class="kts-rule-item flex items-center space-x-1.5 text-[8px] text-slate-700 font-semibold leading-tight">
                                                    <span class="kts-rule-num w-3.5 h-3.5 bg-indigo-100 text-indigo-800 font-black rounded-full flex items-center justify-center text-[7.5px] shrink-0 leading-none shadow-2xs" x-text="idx + 1"></span>
                                                    <span class="line-clamp-2" x-text="rule.replace(/^\d+\.\s*/, '')"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Lower Section: Sign & Stamp Overlay Box -->
                                    <div class="kts-sign-box flex flex-col items-center text-center self-end ml-auto text-[7.5px] text-slate-700 leading-tight relative shrink-0 pt-0.5">
                                        <p class="font-semibold text-slate-500">{{ Setting::get('school_city', 'Jakarta') }}, {{ date('Y') }}</p>
                                        <p class="font-bold text-slate-700">Kepala Sekolah,</p>
                                        
                                        <div class="kts-sign-space h-7 w-full relative flex items-center justify-center my-0.5">
                                            <template x-if="stampUrl">
                                                <img :src="stampUrl" alt="Stempel" class="absolute left-1/2 -translate-x-1/2 -top-2 h-10 object-contain opacity-55 mix-blend-multiply pointer-events-none z-0">
                                            </template>
                                            <template x-if="signatureUrl">
                                                <img :src="signatureUrl" alt="TTD" class="absolute inset-0 m-auto h-7 object-contain pointer-events-none z-10">
                                            </template>
                                        </div>
                                        
                                        <p class="kts-sign-name font-extrabold text-slate-900 underline text-center whitespace-nowrap" x-text="principalName"></p>
                                    </div>
                                </div>
                            </template>

                            <!-- Footer -->
                            <div class="kts-footer px-3 py-1 flex items-center justify-between text-[7.5px] font-semibold z-10 shrink-0"
                                 :class="bgBackUrl ? 'bg-slate-950/80 text-slate-300' : 'bg-slate-50 text-slate-500 border-t border-slate-200'">
                                <span class="truncate max-w-[120px]">{{ Setting::get('school_name', 'Sekolah Indonesia') }}</span>
                                <span>Harap kembalikan jika menemukan</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
