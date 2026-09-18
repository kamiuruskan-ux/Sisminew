@extends('layouts.admin')

@section('title', 'Pengaturan Template Raport')
@section('page_title', 'Pengaturan Cetak Raport')

@section('content')
<div class="w-full space-y-6 pb-20">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Pengaturan Cetak Raport</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola judul header, tempat/tanggal cetak, dan template catatan wali kelas pada raport siswa</p>
        </div>
        <a href="{{ route('admin.raport.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-2xl font-bold text-xs transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Raport</span>
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-2xl text-xs font-bold flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Centered Info Banner regarding Profile Pimpinan -->
    <div class="p-4 bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-900/50 rounded-3xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs shadow-2xs">
        <div class="flex items-center gap-3 text-purple-900 dark:text-purple-200 font-semibold">
            <div class="w-9 h-9 rounded-2xl bg-purple-600 text-white flex items-center justify-center font-extrabold shrink-0 shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="font-extrabold text-purple-950 dark:text-purple-100">Profil Pimpinan, Stempel & TTD Digital Terpusat</p>
                <p class="text-[11px] text-purple-700 dark:text-purple-300 font-medium">Nama Kepala Sekolah, NIP, Cap Stempel Resmi, dan TTD Digital kini dikelola terpusat di menu Profile Pimpinan.</p>
            </div>
        </div>
        <a href="{{ route('admin.settings', ['tab' => 'profile_principal']) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition-all shrink-0">
            Kelola Profile Pimpinan
        </a>
    </div>

    <form action="{{ route('admin.raport.update-settings') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Card 1: Format & Tanggal Cetak Raport --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-5">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Format &amp; Tanggal Cetak Raport</span>
            </h3>

            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Judul Sub-Header Raport</label>
                    <input type="text" name="raport_header_title" value="{{ old('raport_header_title', \App\Models\Setting::get('raport_header_title', 'RAPORT HASIL BELAJAR SISWA')) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl font-medium focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kota / Tempat Cetak</label>
                        <input type="text" name="raport_place" value="{{ old('raport_place', \App\Models\Setting::get('raport_place', \App\Models\Setting::get('school_city', 'Jakarta'))) }}"
                               class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl font-medium focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Cetak Raport</label>
                        <input type="text" name="raport_date" value="{{ old('raport_date', \App\Models\Setting::get('raport_date', date('d F Y'))) }}"
                               placeholder="Contoh: 20 Desember 2026"
                               class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl font-medium focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Default Wali Kelas (Teks Override Statis)</label>
                    <textarea name="raport_default_note" rows="2"
                              placeholder="Kosongkan jika ingin menggunakan catatan otomatis per-predikat di bawah..."
                              class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl font-medium focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">{{ old('raport_default_note', \App\Models\Setting::get('raport_default_note')) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Card 2: Template Catatan Otomatis Per Predikat --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-5">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Template Catatan Otomatis Per-Predikat Nilai</span>
            </h3>

            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-emerald-700 dark:text-emerald-400 mb-1">Catatan Predikat A (Sangat Baik / Rata-rata &ge; 88)</label>
                    <textarea name="raport_note_a" rows="2"
                              class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl font-medium focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">{{ old('raport_note_a', \App\Models\Setting::get('raport_note_a', 'Selamat atas pencapaian hasil belajar yang sangat istimewa! Pertahankan ketekunan dan motivasi belajarmu.')) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-indigo-700 dark:text-indigo-400 mb-1">Catatan Predikat B (Baik / Rata-rata 78 – 87)</label>
                    <textarea name="raport_note_b" rows="2"
                              class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl font-medium focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">{{ old('raport_note_b', \App\Models\Setting::get('raport_note_b', 'Capaian hasil belajar Anda sudah baik dan konsisten. Tingkatkan terus ketelitian dan keaktifan di semester berikutnya.')) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-amber-700 dark:text-amber-400 mb-1">Catatan Predikat C (Cukup / Rata-rata 68 – 77)</label>
                    <textarea name="raport_note_c" rows="2"
                              class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl font-medium focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition">{{ old('raport_note_c', \App\Models\Setting::get('raport_note_c', 'Hasil belajar Anda cukup baik. Perbanyak latihan mandiri dan tingkatkan kedisiplinan dalam mengulas materi.')) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-rose-700 dark:text-rose-400 mb-1">Catatan Predikat D (Perlu Bimbingan / Rata-rata &lt; 68)</label>
                    <textarea name="raport_note_d" rows="2"
                              class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl font-medium focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition">{{ old('raport_note_d', \App\Models\Setting::get('raport_note_d', 'Memerlukan bimbingan serta semangat belajar yang lebih giat. Tingkatkan waktu belajar dan selalu berkonsultasi dengan bapak/ibu guru.')) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-end gap-4 pt-2">
            <a href="{{ route('admin.raport.index') }}" class="px-6 py-3 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white font-extrabold text-xs uppercase tracking-wider transition-colors">
                Batal
            </a>
            <button type="submit" class="px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/30 hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Pengaturan Raport</span>
            </button>
        </div>
    </form>
</div>
@endsection
