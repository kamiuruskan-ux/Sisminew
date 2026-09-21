@extends('layouts.admin')

@section('title', 'Checklist Mutabaah Yaumiyah Guru')

@section('content')
<div x-data="{ activeTab: 'semua' }" class="space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Main Grid Layout (Matching Image 5) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- LEFT COLUMN: CHECKLIST FORM (approx 70% width) -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="lg:col-span-8 space-y-4">
            
            <div class="bg-white dark:bg-[#1C2434] p-6 rounded-3xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs space-y-5">
                
                <!-- Header with Date Picker -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-start space-x-3.5">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-900/50 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 11l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                Checklist Mutabaah Yaumiyah Guru
                            </h2>
                            <div class="flex items-center space-x-1.5 text-xs font-black text-slate-500 uppercase tracking-wider mt-0.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>TANGGAL: {{ strtoupper($targetDate->translatedFormat('l, d F Y')) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Date Picker -->
                    <form method="GET" action="{{ route('admin.employee-mutabaah.index') }}" class="shrink-0">
                        <div class="relative">
                            <input type="date" 
                                   name="date" 
                                   value="{{ $date }}" 
                                   max="{{ date('Y-m-d') }}"
                                   onchange="this.form.submit()" 
                                   class="text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 px-3.5 py-2.5 shadow-xs cursor-pointer focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </form>
                </div>

                <!-- Filter Tabs (Matching Image 5) -->
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="activeTab = 'semua'"
                            :class="activeTab === 'semua' 
                                ? 'bg-slate-900 text-white shadow-xs' 
                                : 'bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
                            class="px-3.5 py-2 rounded-xl text-xs font-black transition flex items-center space-x-1.5">
                        <span>✨ Semua Amalan (6)</span>
                    </button>

                    <button type="button" @click="activeTab = 'harian'"
                            :class="activeTab === 'harian' 
                                ? 'bg-emerald-600 text-white shadow-xs' 
                                : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300'"
                            class="px-3.5 py-2 rounded-xl text-xs font-black transition flex items-center space-x-1.5">
                        <span>📅 Amalan Harian (4)</span>
                    </button>

                    <button type="button" @click="activeTab = 'pekanan'"
                            :class="activeTab === 'pekanan' 
                                ? 'bg-indigo-600 text-white shadow-xs' 
                                : 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300'"
                            class="px-3.5 py-2 rounded-xl text-xs font-black transition flex items-center space-x-1.5">
                        <span>📅 Amalan Pekanan (1)</span>
                    </button>

                    <button type="button" @click="activeTab = 'bulanan'"
                            :class="activeTab === 'bulanan' 
                                ? 'bg-purple-600 text-white shadow-xs' 
                                : 'bg-purple-50 hover:bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300'"
                            class="px-3.5 py-2 rounded-xl text-xs font-black transition flex items-center space-x-1.5">
                        <span>📅 Amalan Bulanan (1)</span>
                    </button>
                </div>

                <!-- Form 6 Checklist Items -->
                <form method="POST" action="{{ route('admin.employee-mutabaah.store') }}" class="space-y-3.5 pt-2">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">

                    <!-- 1. Sholat Fardhu (HARIAN) -->
                    @php
                        $checkedFardhu = $mutabaah->sholat_fardhu || ($mutabaah->subuh_jamaah && $mutabaah->dzuhur_jamaah && $mutabaah->ashar_jamaah);
                    @endphp
                    <div x-show="activeTab === 'semua' || activeTab === 'harian'"
                         class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700 transition flex items-center justify-between gap-4 shadow-2xs">
                        <label class="flex items-center space-x-3.5 cursor-pointer select-none flex-1">
                            <input type="checkbox" name="sholat_fardhu" value="1" {{ $checkedFardhu ? 'checked' : '' }}
                                   class="w-5 h-5 rounded-md border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <span class="font-extrabold text-sm text-slate-800 dark:text-white">
                                Sholat Fardhu
                            </span>
                        </label>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            HARIAN
                        </span>
                    </div>

                    <!-- 2. Sholat Sunnah Rawatib & Dhuha (HARIAN) -->
                    @php
                        $checkedRawatib = $mutabaah->rawatib_dhuha || ($mutabaah->rawatib_count >= 8 || $mutabaah->dhuha);
                    @endphp
                    <div x-show="activeTab === 'semua' || activeTab === 'harian'"
                         class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700 transition flex items-center justify-between gap-4 shadow-2xs">
                        <label class="flex items-center space-x-3.5 cursor-pointer select-none flex-1">
                            <input type="checkbox" name="rawatib_dhuha" value="1" {{ $checkedRawatib ? 'checked' : '' }}
                                   class="w-5 h-5 rounded-md border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <span class="font-extrabold text-sm text-slate-800 dark:text-white">
                                Sholat Sunnah Rawatib &amp; Dhuha
                            </span>
                        </label>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            HARIAN
                        </span>
                    </div>

                    <!-- 3. Tilawah Al-Qur'an Minimal 10 halaman (HARIAN) -->
                    @php
                        $checkedTilawah = $mutabaah->tilawah_quran || ($mutabaah->tilawah_pages >= 4);
                    @endphp
                    <div x-show="activeTab === 'semua' || activeTab === 'harian'"
                         class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700 transition flex items-center justify-between gap-4 shadow-2xs">
                        <label class="flex items-center space-x-3.5 cursor-pointer select-none flex-1">
                            <input type="checkbox" name="tilawah_quran" value="1" {{ $checkedTilawah ? 'checked' : '' }}
                                   class="w-5 h-5 rounded-md border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <span class="font-extrabold text-sm text-slate-800 dark:text-white">
                                Tilawah Al-Qur'an Minimal 10 halaman
                            </span>
                        </label>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            HARIAN
                        </span>
                    </div>

                    <!-- 4. Dzikir Pagi & Petang (HARIAN) -->
                    <div x-show="activeTab === 'semua' || activeTab === 'harian'"
                         class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700 transition flex items-center justify-between gap-4 shadow-2xs">
                        <label class="flex items-center space-x-3.5 cursor-pointer select-none flex-1">
                            <input type="checkbox" name="dzikir_pagi_petang" value="1" {{ $mutabaah->dzikir_pagi_petang ? 'checked' : '' }}
                                   class="w-5 h-5 rounded-md border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <span class="font-extrabold text-sm text-slate-800 dark:text-white">
                                Dzikir Pagi &amp; Petang
                            </span>
                        </label>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            HARIAN
                        </span>
                    </div>

                    <!-- 5. Sholat Tahajud / Qiyamul Lail (PEKANAN) -->
                    @php
                        $checkedTahajud = $mutabaah->sholat_tahajud || $mutabaah->tahajjud_witir;
                    @endphp
                    <div x-show="activeTab === 'semua' || activeTab === 'pekanan'"
                         class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700 transition flex items-center justify-between gap-4 shadow-2xs">
                        <label class="flex items-center space-x-3.5 cursor-pointer select-none flex-1">
                            <input type="checkbox" name="sholat_tahajud" value="1" {{ $checkedTahajud ? 'checked' : '' }}
                                   class="w-5 h-5 rounded-md border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </div>
                            <span class="font-extrabold text-sm text-slate-800 dark:text-white">
                                Sholat Tahajud / Qiyamul Lail
                            </span>
                        </label>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300">
                                PEKANAN
                            </span>
                            @if($tahajudThisWeek || $checkedTahajud)
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 flex items-center gap-1">
                                    <span>✅ Sudah Pekan Ini</span>
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 flex items-center gap-1">
                                    <span>⏳ Belum Pekan Ini</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- 6. Puasa Sunnah (BULANAN) -->
                    <div x-show="activeTab === 'semua' || activeTab === 'bulanan'"
                         class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700 transition flex items-center justify-between gap-4 shadow-2xs">
                        <label class="flex items-center space-x-3.5 cursor-pointer select-none flex-1">
                            <input type="checkbox" name="puasa_sunnah" value="1" {{ $mutabaah->puasa_sunnah ? 'checked' : '' }}
                                   class="w-5 h-5 rounded-md border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <div class="w-8 h-8 rounded-xl bg-teal-50 dark:bg-teal-950/50 text-teal-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="font-extrabold text-sm text-slate-800 dark:text-white">
                                Puasa Sunnah
                            </span>
                        </label>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300">
                                BULANAN
                            </span>
                            @if($puasaThisMonth || $mutabaah->puasa_sunnah)
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 flex items-center gap-1">
                                    <span>✅ Sudah Bulan Ini</span>
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 flex items-center gap-1">
                                    <span>⏳ Belum Bulan Ini</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Button Full-Width (Matching Image 5) -->
                    <button type="submit" 
                            class="w-full py-4 mt-6 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-black text-sm uppercase tracking-wider rounded-2xl shadow-xs transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span>SIMPAN MUTABAAH GURU HARI INI</span>
                    </button>
                </form>

            </div>

        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- RIGHT COLUMN: SIDEBAR (approx 30% width) -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Card 1: Riwayat Mutabaah Guru (7 Hari) -->
            <div class="bg-white dark:bg-[#1C2434] p-5 rounded-3xl border border-slate-200/80 dark:border-[#2E3A47] shadow-xs space-y-3">
                <div class="flex items-center space-x-2.5 pb-2 border-b border-slate-100 dark:border-slate-800">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-white">
                        Riwayat Mutabaah Guru (7 Hari)
                    </h3>
                </div>

                <div class="space-y-2">
                    @foreach($historyDays as $h)
                        <a href="{{ route('admin.employee-mutabaah.index', ['date' => $h['date']]) }}" 
                           class="flex items-center justify-between p-3 rounded-xl border transition {{ $h['is_current'] ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20 shadow-2xs' : 'border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 hover:bg-slate-100/60' }}">
                            <span class="text-xs font-extrabold text-slate-700 dark:text-slate-300">
                                {{ $h['label'] }}
                            </span>
                            @if($h['has_record'])
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                    Sudah
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-500 dark:bg-slate-700 dark:text-slate-400">
                                    Belum
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Card 2: MUTIARA HADITS (Matching Image 5) -->
            <div class="bg-indigo-50/70 dark:bg-indigo-950/30 p-5 rounded-3xl border border-indigo-100/80 dark:border-indigo-900/40 shadow-xs space-y-3">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300 inline-block">
                    MUTIARA HADITS
                </span>
                <p class="text-xs font-medium italic text-slate-700 dark:text-slate-300 leading-relaxed">
                    "Sebaik-baik amalan di sisi Allah adalah amalan yang dikerjakan secara istiqomah (terus-menerus), meskipun sedikit."
                </p>
                <span class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-400 block pt-1">
                    — HR. BUKHARI &amp; MUSLIM
                </span>
            </div>

            <!-- Card 3: Rekap Mutabaah Seluruh Guru (Accessible for Admin/Kepsek) -->
            @if(auth()->user()->hasRole(['super-admin', 'admin', 'operator', 'kepala-sekolah', 'wakasek-kurikulum', 'wakasek-kesiswaan']))
                <a href="{{ route('admin.employee-mutabaah.recap') }}" 
                   class="block p-5 rounded-3xl bg-white dark:bg-[#1C2434] border border-slate-200/80 dark:border-[#2E3A47] hover:border-indigo-500 shadow-xs transition group">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-white group-hover:text-indigo-600 transition">
                                Rekap Mutabaah Seluruh Guru
                            </h4>
                            <p class="text-[11px] text-slate-400 mt-0.5">Monitoring komprehensif amalan guru sekolah</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            Hari Ini
                        </span>
                    </div>
                </a>
            @endif

        </div>

    </div>

</div>
@endsection
