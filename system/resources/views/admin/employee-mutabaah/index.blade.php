@extends('layouts.admin')

@section('title', 'Mutabaah Ibadah Harian Pegawai')

@section('content')
<div x-data="mutabaahForm()" class="space-y-6">

    <!-- Header & Date Navigation -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
        <div class="flex items-center space-x-3">
            <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </span>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Mutabaah Yaumiyah Pegawai
                </h1>
                <p class="text-xs text-slate-500">
                    Pencatatan amalan ibadah harian guru &amp; tenaga kependidikan (Terintegrasi ke KPI Pilar 4)
                </p>
            </div>
        </div>

        <!-- Date Selector Form -->
        <form method="GET" action="{{ route('admin.employee-mutabaah.index') }}" class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-500">Pilih Tanggal:</label>
            <input type="date" 
                   name="date" 
                   value="{{ $date }}" 
                   max="{{ date('Y-m-d') }}"
                   onchange="this.form.submit()" 
                   class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3 py-2 shadow-xs">
        </form>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Monthly Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Hari Terisi Bulan Ini</p>
            <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $daysFilled }} <span class="text-xs font-normal text-slate-400">/ {{ $targetDate->daysInMonth }} hari</span></h3>
            <p class="text-[11px] text-slate-500 mt-1">Konsistensi: <strong>{{ round(($daysFilled / $targetDate->daysInMonth) * 100) }}%</strong></p>
        </div>

        <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Rerata Skor Mutabaah</p>
            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $avgScore }} <span class="text-xs font-normal text-slate-400">/ 100</span></h3>
            <p class="text-[11px] text-slate-500 mt-1">Kontribusi langsung ke Indikator KPI 4.2.1</p>
        </div>

        <div class="bg-white dark:bg-[#1C2434] p-5 rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Entri Hari Ini</p>
                <h3 class="text-lg font-black {{ $mutabaah->exists ? 'text-emerald-600' : 'text-amber-500' }} mt-1">
                    {{ $mutabaah->exists ? 'Sudah Tercatat' : 'Belum Diisi' }}
                </h3>
                <span class="text-[11px] text-slate-500">{{ $targetDate->translatedFormat('l, d F Y') }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl {{ $mutabaah->exists ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center font-black text-sm">
                {{ $mutabaah->daily_score ?? 0 }}
            </div>
        </div>
    </div>

    <!-- Main Checklist Form -->
    <div class="bg-white dark:bg-[#1C2434] rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 dark:border-[#2E3A47] flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">
                    Formulir Amalan Yaumiyah: {{ $targetDate->translatedFormat('d F Y') }}
                </h2>
                <p class="text-xs text-slate-500">Centang ibadah yang telah dilaksanakan dengan penuh keikhlasan</p>
            </div>
            
            <!-- Live Score Preview -->
            <div class="flex items-center space-x-2 bg-emerald-50 dark:bg-emerald-950/70 px-3.5 py-1.5 rounded-xl border border-emerald-200 dark:border-emerald-800">
                <span class="text-xs text-emerald-800 dark:text-emerald-300 font-bold">Skor Hari Ini:</span>
                <span class="text-lg font-black text-emerald-600 dark:text-emerald-400" x-text="computedScore"></span>
                <span class="text-xs text-emerald-500 font-bold">/ 100</span>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.employee-mutabaah.store') }}" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">

            <!-- 1. Shalat 5 Waktu Berjamaah di Masjid -->
            <div>
                <div class="flex items-center space-x-2 mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="font-extrabold text-xs uppercase tracking-wider text-slate-800 dark:text-white">
                        1. Shalat Wajib 5 Waktu Berjamaah (Maksimal 40 Poin)
                    </h3>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-xs">
                    <!-- Subuh -->
                    <label class="flex items-center space-x-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors"
                           :class="form.subuh ? 'bg-emerald-50/70 border-emerald-300 dark:bg-emerald-950/40 dark:border-emerald-700' : ''">
                        <input type="checkbox" name="subuh_jamaah" value="1" x-model="form.subuh" @change="recalculate()" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <span class="font-bold text-slate-800 dark:text-white">Subuh</span>
                    </label>

                    <!-- Dzuhur -->
                    <label class="flex items-center space-x-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors"
                           :class="form.dzuhur ? 'bg-emerald-50/70 border-emerald-300 dark:bg-emerald-950/40 dark:border-emerald-700' : ''">
                        <input type="checkbox" name="dzuhur_jamaah" value="1" x-model="form.dzuhur" @change="recalculate()" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <span class="font-bold text-slate-800 dark:text-white">Dzuhur</span>
                    </label>

                    <!-- Ashar -->
                    <label class="flex items-center space-x-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors"
                           :class="form.ashar ? 'bg-emerald-50/70 border-emerald-300 dark:bg-emerald-950/40 dark:border-emerald-700' : ''">
                        <input type="checkbox" name="ashar_jamaah" value="1" x-model="form.ashar" @change="recalculate()" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <span class="font-bold text-slate-800 dark:text-white">Ashar</span>
                    </label>

                    <!-- Maghrib -->
                    <label class="flex items-center space-x-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors"
                           :class="form.maghrib ? 'bg-emerald-50/70 border-emerald-300 dark:bg-emerald-950/40 dark:border-emerald-700' : ''">
                        <input type="checkbox" name="maghrib_jamaah" value="1" x-model="form.maghrib" @change="recalculate()" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <span class="font-bold text-slate-800 dark:text-white">Maghrib</span>
                    </label>

                    <!-- Isya -->
                    <label class="flex items-center space-x-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors"
                           :class="form.isya ? 'bg-emerald-50/70 border-emerald-300 dark:bg-emerald-950/40 dark:border-emerald-700' : ''">
                        <input type="checkbox" name="isya_jamaah" value="1" x-model="form.isya" @change="recalculate()" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <span class="font-bold text-slate-800 dark:text-white">Isya</span>
                    </label>
                </div>
            </div>

            <!-- 2. Shalat Sunnah & Qiyamul Lail -->
            <div class="pt-4 border-t border-slate-100 dark:border-[#2E3A47]">
                <div class="flex items-center space-x-2 mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    <h3 class="font-extrabold text-xs uppercase tracking-wider text-slate-800 dark:text-white">
                        2. Shalat Sunnah Rawatib, Dhuha &amp; Qiyamul Lail (Maksimal 40 Poin)
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <!-- Rawatib -->
                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/40 dark:bg-slate-800/40">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Shalat Sunnah Rawatib:
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="number" 
                                   name="rawatib_count" 
                                   min="0" 
                                   max="12" 
                                   x-model.number="form.rawatib" 
                                   @input="recalculate()"
                                   class="w-24 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 font-bold text-slate-800 dark:text-white">
                            <span class="text-slate-500 text-xs">Rakaat (Target: 10-12)</span>
                        </div>
                    </div>

                    <!-- Dhuha -->
                    <label class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors"
                           :class="form.dhuha ? 'bg-indigo-50/70 border-indigo-300 dark:bg-indigo-950/40 dark:border-indigo-700' : ''">
                        <input type="checkbox" name="dhuha" value="1" x-model="form.dhuha" @change="recalculate()" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-white block">Shalat Dhuha</span>
                            <span class="text-[11px] text-slate-400">Minimal 2 rakaat di pagi hari</span>
                        </div>
                    </label>

                    <!-- Tahajjud & Witir -->
                    <label class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors"
                           :class="form.tahajjud ? 'bg-indigo-50/70 border-indigo-300 dark:bg-indigo-950/40 dark:border-indigo-700' : ''">
                        <input type="checkbox" name="tahajjud_witir" value="1" x-model="form.tahajjud" @change="recalculate()" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-white block">Qiyamul Lail / Witir</span>
                            <span class="text-[11px] text-slate-400">Shalat malam sebelum subuh</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 3. Al-Qur'an & Dzikir Yaumiyah -->
            <div class="pt-4 border-t border-slate-100 dark:border-[#2E3A47]">
                <div class="flex items-center space-x-2 mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <h3 class="font-extrabold text-xs uppercase tracking-wider text-slate-800 dark:text-white">
                        3. Tilawah Al-Qur'an, Dzikir &amp; Sedekah (Maksimal 20 Poin + Bonus)
                    </h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                    <!-- Tilawah Pages -->
                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/40 dark:bg-slate-800/40">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Tilawah Al-Qur'an:
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="number" 
                                   name="tilawah_pages" 
                                   min="0" 
                                   max="604" 
                                   x-model.number="form.tilawah" 
                                   @input="recalculate()"
                                   class="w-20 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 font-bold text-slate-800 dark:text-white">
                            <span class="text-slate-500 text-xs">Halaman (Target: 4)</span>
                        </div>
                    </div>

                    <!-- Dzikir Pagi Petang -->
                    <label class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors"
                           :class="form.dzikir ? 'bg-amber-50/70 border-amber-300 dark:bg-amber-950/40 dark:border-amber-700' : ''">
                        <input type="checkbox" name="dzikir_pagi_petang" value="1" x-model="form.dzikir" @change="recalculate()" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-white block">Dzikir Pagi-Petang</span>
                            <span class="text-[11px] text-slate-400">Al-Ma'tsurat / Wirid</span>
                        </div>
                    </label>

                    <!-- Sedekah Harian -->
                    <label class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors"
                           :class="form.sedekah ? 'bg-amber-50/70 border-amber-300 dark:bg-amber-950/40 dark:border-amber-700' : ''">
                        <input type="checkbox" name="sedekah" value="1" x-model="form.sedekah" @change="recalculate()" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-white block">Infaq / Sedekah Subuh</span>
                            <span class="text-[11px] text-slate-400">Gemar berbagi rezeki</span>
                        </div>
                    </label>

                    <!-- Puasa Sunnah -->
                    <label class="flex items-center space-x-3 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer transition-colors"
                           :class="form.puasa ? 'bg-amber-50/70 border-amber-300 dark:bg-amber-950/40 dark:border-amber-700' : ''">
                        <input type="checkbox" name="puasa_sunnah" value="1" x-model="form.puasa" @change="recalculate()" class="w-4 h-4 rounded text-primary focus:ring-primary">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-white block">Puasa Sunnah (Bonus)</span>
                            <span class="text-[11px] text-slate-400">Senin/Kamis / Ayyamul Bidh</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 4. Catatan & Doa Pribadi -->
            <div class="pt-4 border-t border-slate-100 dark:border-[#2E3A47]">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Catatan Refleksi / Doa Pribadi (Opsional):
                </label>
                <input type="text" 
                       name="notes" 
                       value="{{ $mutabaah->notes }}" 
                       placeholder="Catatan kebaikan, ayat Al-Qur'an yang ditadabburi hari ini..." 
                       class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3.5 py-2 text-slate-800 dark:text-slate-200">
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Catatan Mutabaah Hari Ini</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Recent History Table (Last 7 Days) -->
    @if(count($recentDays) > 0)
    <div class="bg-white dark:bg-[#1C2434] rounded-2xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-[#2E3A47]">
            <h3 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider">
                Riwayat Mutabaah 7 Hari Terakhir
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800 text-[10px] uppercase font-bold text-slate-400">
                        <th class="py-2.5 px-4">Tanggal</th>
                        <th class="py-2.5 px-3 text-center">Shalat Jamaah</th>
                        <th class="py-2.5 px-3 text-center">Rawatib</th>
                        <th class="py-2.5 px-3 text-center">Dhuha</th>
                        <th class="py-2.5 px-3 text-center">Tahajjud</th>
                        <th class="py-2.5 px-3 text-center">Tilawah</th>
                        <th class="py-2.5 px-3 text-center">Dzikir</th>
                        <th class="py-2.5 px-3 text-center">Skor Harian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#2E3A47]">
                    @foreach($recentDays as $r)
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-2.5 px-4 font-bold text-slate-700 dark:text-slate-300">
                            {{ \Carbon\Carbon::parse($r->date)->translatedFormat('d F Y') }}
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            @php
                                $sholatCount = ($r->subuh_jamaah ? 1 : 0) + ($r->dzuhur_jamaah ? 1 : 0) + ($r->ashar_jamaah ? 1 : 0) + ($r->maghrib_jamaah ? 1 : 0) + ($r->isya_jamaah ? 1 : 0);
                            @endphp
                            <span class="font-bold text-slate-700">{{ $sholatCount }} / 5</span>
                        </td>
                        <td class="py-2.5 px-3 text-center">{{ $r->rawatib_count }} Rkt</td>
                        <td class="py-2.5 px-3 text-center">
                            {!! $r->dhuha ? '<span class="text-emerald-500 font-bold">✓</span>' : '<span class="text-slate-300">-</span>' !!}
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            {!! $r->tahajjud_witir ? '<span class="text-emerald-500 font-bold">✓</span>' : '<span class="text-slate-300">-</span>' !!}
                        </td>
                        <td class="py-2.5 px-3 text-center">{{ $r->tilawah_pages }} Hlm</td>
                        <td class="py-2.5 px-3 text-center">
                            {!! $r->dzikir_pagi_petang ? '<span class="text-emerald-500 font-bold">✓</span>' : '<span class="text-slate-300">-</span>' !!}
                        </td>
                        <td class="py-2.5 px-3 text-center font-black text-emerald-600 dark:text-emerald-400">
                            {{ $r->daily_score }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
function mutabaahForm() {
    return {
        form: {
            subuh: {{ $mutabaah->subuh_jamaah ? 'true' : 'false' }},
            dzuhur: {{ $mutabaah->dzuhur_jamaah ? 'true' : 'false' }},
            ashar: {{ $mutabaah->ashar_jamaah ? 'true' : 'false' }},
            maghrib: {{ $mutabaah->maghrib_jamaah ? 'true' : 'false' }},
            isya: {{ $mutabaah->isya_jamaah ? 'true' : 'false' }},
            rawatib: {{ (int) $mutabaah->rawatib_count }},
            dhuha: {{ $mutabaah->dhuha ? 'true' : 'false' }},
            tahajjud: {{ $mutabaah->tahajjud_witir ? 'true' : 'false' }},
            tilawah: {{ (int) $mutabaah->tilawah_pages }},
            dzikir: {{ $mutabaah->dzikir_pagi_petang ? 'true' : 'false' }},
            sedekah: {{ $mutabaah->sedekah ? 'true' : 'false' }},
            puasa: {{ $mutabaah->puasa_sunnah ? 'true' : 'false' }},
        },
        computedScore: {{ $mutabaah->daily_score ?? 0 }},

        recalculate() {
            let score = 0;
            if (this.form.subuh) score += 8;
            if (this.form.dzuhur) score += 8;
            if (this.form.ashar) score += 8;
            if (this.form.maghrib) score += 8;
            if (this.form.isya) score += 8;

            const r = Math.min(12, Math.max(0, Number(this.form.rawatib || 0)));
            score += Math.min(15, (r / 10) * 15);

            if (this.form.dhuha) score += 10;
            if (this.form.tahajjud) score += 15;

            const t = Math.max(0, Number(this.form.tilawah || 0));
            score += Math.min(10, (t / 4) * 10);

            if (this.form.dzikir) score += 5;
            if (this.form.sedekah) score += 5;
            if (this.form.puasa) score += 5;

            this.computedScore = Math.min(100, Math.round(score));
        }
    };
}
</script>
@endpush
@endsection
