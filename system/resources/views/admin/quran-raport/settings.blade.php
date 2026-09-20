@extends('layouts.admin')

@section('title', "Pengaturan Template Raport Al-Qur'an")
@section('page_title', "Template Raport Al-Qur'an")

@section('content')
<div class="space-y-6" x-data="{
    logoType: '{{ $raportSettings['logo_type'] ?? 'default' }}',
    activeTemplateTab: 'tpl_1'
}">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-slate-900 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.quran-raport.index') }}" class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
                    ⚙️ EDIT TEMPLATE &amp; KOP RAPORT AL-QUR'AN
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Atur kop surat, logo placeholder, tanda tangan, tanggal (Kota Palu), dan preset capaian tiap jenjang kelas</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('admin.quran-raport.preview') }}" target="_blank"
               class="px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 text-xs font-bold rounded-2xl border border-indigo-200 dark:border-indigo-800 shadow-xs transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Live Preview Raport ↗</span>
            </a>

            <button type="submit" form="raportSettingsForm"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-2xl shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Perubahan Template</span>
            </button>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-2xs">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <form id="raportSettingsForm" action="{{ route('admin.quran-raport.settings.save') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- KARTU 1: KOP SURAT & LOGO PLACEHOLDER -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                        🏛️
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Kop Surat &amp; Logo Raport</h4>
                        <p class="text-[11px] text-slate-400">Sesuaikan header instansi dan logo placeholder di bagian atas lembar raport</p>
                    </div>
                </div>

                <!-- Pilihan Mode Logo -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-2">Tampilan Logo Lembaga</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex flex-col items-center justify-center p-3 rounded-2xl border cursor-pointer transition text-center"
                               :class="logoType === 'default' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-500'">
                            <input type="radio" name="quran_raport_logo_type" value="default" x-model="logoType" class="hidden">
                            <span class="text-lg mb-1">🏫</span>
                            <span class="text-xs font-bold">Logo Resmi Sekolah</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-3 rounded-2xl border cursor-pointer transition text-center"
                               :class="logoType === 'custom' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-500'">
                            <input type="radio" name="quran_raport_logo_type" value="custom" x-model="logoType" class="hidden">
                            <span class="text-lg mb-1">📤</span>
                            <span class="text-xs font-bold">Upload Logo Khusus</span>
                        </label>

                        <label class="flex flex-col items-center justify-center p-3 rounded-2xl border cursor-pointer transition text-center"
                               :class="logoType === 'placeholder' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 font-bold' : 'border-slate-200 dark:border-slate-800 text-slate-500'">
                            <input type="radio" name="quran_raport_logo_type" value="placeholder" x-model="logoType" class="hidden">
                            <span class="text-lg mb-1">🖼️</span>
                            <span class="text-xs font-bold">Logo Placeholder</span>
                        </label>
                    </div>
                </div>

                <!-- Input Upload Custom Logo -->
                <div x-show="logoType === 'custom'" x-cloak class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pilih Berkas Logo Khusus Raport Al-Qur'an (PNG/JPG)</label>
                    <input type="file" name="custom_logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                    @if(!empty($raportSettings['custom_logo_path']))
                        <div class="flex items-center gap-2 pt-2">
                            <span class="text-[10px] text-slate-400">Logo saat ini:</span>
                            <img src="{{ asset($raportSettings['custom_logo_path']) }}" class="h-8 max-w-[60px] object-contain" alt="Current Logo">
                        </div>
                    @endif
                </div>

                <!-- Baris Atas Kop -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Baris Sub-Header Kop (Atas)</label>
                    <input type="text" name="quran_raport_kop_top" value="{{ old('quran_raport_kop_top', $raportSettings['kop_top']) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                </div>

                <!-- Nama Sekolah/Lembaga -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lembaga / Sekolah <span class="text-rose-500">*</span></label>
                    <input type="text" name="quran_raport_school_name" value="{{ old('quran_raport_school_name', $raportSettings['school_name']) }}" required
                           class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-black text-slate-800 dark:text-white">
                </div>

                <!-- Alamat Sekolah -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                    <textarea name="quran_raport_school_address" rows="2"
                              class="w-full px-3.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-medium">{{ old('quran_raport_school_address', $raportSettings['school_address']) }}</textarea>
                </div>

                <!-- Kontak Telepon, Email, Website -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 mb-1">No. Telepon / WA</label>
                        <input type="text" name="quran_raport_school_phone" value="{{ old('quran_raport_school_phone', $raportSettings['school_phone']) }}"
                               class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 mb-1">Alamat Email</label>
                        <input type="text" name="quran_raport_school_email" value="{{ old('quran_raport_school_email', $raportSettings['school_email']) }}"
                               class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 mb-1">Alamat Website</label>
                        <input type="text" name="quran_raport_school_website" value="{{ old('quran_raport_school_website', $raportSettings['school_website']) }}"
                               class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                    </div>
                </div>

            </div>


            <!-- KARTU 2: TEMPAT, TANGGAL & PENANDATANGAN RAPORT -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
                <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                        ✍️
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Tempat, Tanggal &amp; Tanda Tangan</h4>
                        <p class="text-[11px] text-slate-400">Kota default raport ditetapkan otomatis ke <strong>Palu</strong></p>
                    </div>
                </div>

                <!-- Kota Raport (Default Palu) & Tanggal Raport -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">
                            Kota Penerbitan Raport <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="quran_raport_city" value="{{ old('quran_raport_city', $raportSettings['city']) }}" required
                                   class="w-full pl-9 pr-3.5 py-2.5 border border-purple-200 dark:border-purple-800 bg-purple-50/40 dark:bg-purple-950/20 rounded-xl text-xs font-black text-purple-900 dark:text-purple-300">
                            <span class="absolute left-3 top-2.5 text-xs">📍</span>
                        </div>
                        <p class="text-[10px] text-purple-600 dark:text-purple-400 mt-1">Default kota: <strong>Palu</strong></p>
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">
                            Tanggal Raport
                        </label>
                        <input type="text" name="quran_raport_date" value="{{ old('quran_raport_date', $raportSettings['date']) }}"
                               placeholder="Contoh: 20 September 2026"
                               class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                    </div>
                </div>

                <!-- Nama Penandatangan (Kepala Sekolah / Mudir) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Jabatan Penandatangan</label>
                        <input type="text" name="quran_raport_principal_title" value="{{ old('quran_raport_principal_title', $raportSettings['principal_title']) }}"
                               class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Nama Kepala / Mudir <span class="text-rose-500">*</span></label>
                        <input type="text" name="quran_raport_principal_name" value="{{ old('quran_raport_principal_name', $raportSettings['principal_name']) }}" required
                               class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                    </div>
                </div>

                <!-- NIP / NIY Kepala -->
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">NIP / NIY / NPY</label>
                    <input type="text" name="quran_raport_principal_nip" value="{{ old('quran_raport_principal_nip', $raportSettings['principal_nip']) }}"
                           class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-mono font-bold">
                </div>

                <!-- Upload TTD & Stempel Digital -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Tanda Tangan Digital (PNG)</label>
                        <input type="file" name="signature_file" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-purple-600 file:text-white">
                        @if(!empty($raportSettings['signature_path']))
                            <div class="flex items-center gap-2 pt-1">
                                <span class="text-[10px] text-slate-400">TTD Tersimpan:</span>
                                <img src="{{ asset($raportSettings['signature_path']) }}" class="h-8 max-w-[60px] object-contain" alt="Signature">
                            </div>
                        @endif
                    </div>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Stempel Lembaga (PNG)</label>
                        <input type="file" name="stamp_file" accept="image/*" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-purple-600 file:text-white">
                        @if(!empty($raportSettings['stamp_path']))
                            <div class="flex items-center gap-2 pt-1">
                                <span class="text-[10px] text-slate-400">Stempel Tersimpan:</span>
                                <img src="{{ asset($raportSettings['stamp_path']) }}" class="h-8 max-w-[60px] object-contain" alt="Stamp">
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        <!-- KARTU 3: MULTI-TEMPLATE TARGET CAPAIAN SANTRI PER TINGKAT KELAS -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        📖
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">
                            MULTI-TEMPLATE CAPAIAN SANTRI TIAP JENJANG KELAS
                        </h4>
                        <p class="text-[11px] text-slate-400">Capaian santri tiap tingkat berbeda, guru Al-Qur'an dapat memilih template capaian yang sesuai saat cetak</p>
                    </div>
                </div>

                <!-- Template Selector Tabs -->
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl text-xs font-bold">
                    <button type="button" @click="activeTemplateTab = 'tpl_1'" :class="activeTemplateTab === 'tpl_1' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl transition">
                        Kelas 1-2 (Pemula)
                    </button>
                    <button type="button" @click="activeTemplateTab = 'tpl_2'" :class="activeTemplateTab === 'tpl_2' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl transition">
                        Kelas 3-4 (Menengah)
                    </button>
                    <button type="button" @click="activeTemplateTab = 'tpl_3'" :class="activeTemplateTab === 'tpl_3' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl transition">
                        Kelas 5-6 (Lanjutan)
                    </button>
                    <button type="button" @click="activeTemplateTab = 'tpl_4'" :class="activeTemplateTab === 'tpl_4' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl transition">
                        Halaqah Khusus
                    </button>
                </div>
            </div>

            <!-- Tab Content 1: Kelas 1-2 -->
            <div x-show="activeTemplateTab === 'tpl_1'" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Nama Template 1</label>
                    <input type="text" name="quran_tpl_1_name" value="{{ old('quran_tpl_1_name', $templates['kelas_1_2']['name']) }}"
                           class="w-full px-3.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                        <span class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase">Target Aspek Tahsin (Kelas 1 - 2):</span>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahsin 1</label>
                            <input type="text" name="quran_tpl_1_tahsin_1" value="{{ old('quran_tpl_1_tahsin_1', $templates['kelas_1_2']['tahsin_aspect_1']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahsin 2</label>
                            <input type="text" name="quran_tpl_1_tahsin_2" value="{{ old('quran_tpl_1_tahsin_2', $templates['kelas_1_2']['tahsin_aspect_2']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                        <span class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase">Target Aspek Tahfidz (Kelas 1 - 2):</span>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahfidz 1</label>
                            <input type="text" name="quran_tpl_1_tahfidz_1" value="{{ old('quran_tpl_1_tahfidz_1', $templates['kelas_1_2']['tahfidz_aspect_1']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahfidz 2</label>
                            <input type="text" name="quran_tpl_1_tahfidz_2" value="{{ old('quran_tpl_1_tahfidz_2', $templates['kelas_1_2']['tahfidz_aspect_2']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content 2: Kelas 3-4 -->
            <div x-show="activeTemplateTab === 'tpl_2'" x-cloak class="space-y-4">
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Nama Template 2</label>
                    <input type="text" name="quran_tpl_2_name" value="{{ old('quran_tpl_2_name', $templates['kelas_3_4']['name']) }}"
                           class="w-full px-3.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                        <span class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase">Target Aspek Tahsin (Kelas 3 - 4):</span>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahsin 1</label>
                            <input type="text" name="quran_tpl_2_tahsin_1" value="{{ old('quran_tpl_2_tahsin_1', $templates['kelas_3_4']['tahsin_aspect_1']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahsin 2</label>
                            <input type="text" name="quran_tpl_2_tahsin_2" value="{{ old('quran_tpl_2_tahsin_2', $templates['kelas_3_4']['tahsin_aspect_2']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                        <span class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase">Target Aspek Tahfidz (Kelas 3 - 4):</span>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahfidz 1</label>
                            <input type="text" name="quran_tpl_2_tahfidz_1" value="{{ old('quran_tpl_2_tahfidz_1', $templates['kelas_3_4']['tahfidz_aspect_1']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahfidz 2</label>
                            <input type="text" name="quran_tpl_2_tahfidz_2" value="{{ old('quran_tpl_2_tahfidz_2', $templates['kelas_3_4']['tahfidz_aspect_2']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content 3: Kelas 5-6 -->
            <div x-show="activeTemplateTab === 'tpl_3'" x-cloak class="space-y-4">
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Nama Template 3</label>
                    <input type="text" name="quran_tpl_3_name" value="{{ old('quran_tpl_3_name', $templates['kelas_5_6']['name']) }}"
                           class="w-full px-3.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                        <span class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase">Target Aspek Tahsin (Kelas 5 - 6):</span>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahsin 1</label>
                            <input type="text" name="quran_tpl_3_tahsin_1" value="{{ old('quran_tpl_3_tahsin_1', $templates['kelas_5_6']['tahsin_aspect_1']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahsin 2</label>
                            <input type="text" name="quran_tpl_3_tahsin_2" value="{{ old('quran_tpl_3_tahsin_2', $templates['kelas_5_6']['tahsin_aspect_2']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                        <span class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase">Target Aspek Tahfidz (Kelas 5 - 6):</span>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahfidz 1</label>
                            <input type="text" name="quran_tpl_3_tahfidz_1" value="{{ old('quran_tpl_3_tahfidz_1', $templates['kelas_5_6']['tahfidz_aspect_1']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahfidz 2</label>
                            <input type="text" name="quran_tpl_3_tahfidz_2" value="{{ old('quran_tpl_3_tahfidz_2', $templates['kelas_5_6']['tahfidz_aspect_2']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content 4: Halaqah Khusus -->
            <div x-show="activeTemplateTab === 'tpl_4'" x-cloak class="space-y-4">
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Nama Template 4</label>
                    <input type="text" name="quran_tpl_4_name" value="{{ old('quran_tpl_4_name', $templates['intensif']['name']) }}"
                           class="w-full px-3.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                        <span class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase">Target Aspek Tahsin (Halaqah Khusus):</span>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahsin 1</label>
                            <input type="text" name="quran_tpl_4_tahsin_1" value="{{ old('quran_tpl_4_tahsin_1', $templates['intensif']['tahsin_aspect_1']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahsin 2</label>
                            <input type="text" name="quran_tpl_4_tahsin_2" value="{{ old('quran_tpl_4_tahsin_2', $templates['intensif']['tahsin_aspect_2']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                        <span class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase">Target Aspek Tahfidz (Halaqah Khusus):</span>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahfidz 1</label>
                            <input type="text" name="quran_tpl_4_tahfidz_1" value="{{ old('quran_tpl_4_tahfidz_1', $templates['intensif']['tahfidz_aspect_1']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Aspek Tahfidz 2</label>
                            <input type="text" name="quran_tpl_4_tahfidz_2" value="{{ old('quran_tpl_4_tahfidz_2', $templates['intensif']['tahfidz_aspect_2']) }}" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection
