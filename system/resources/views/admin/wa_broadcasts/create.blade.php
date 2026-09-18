@extends('layouts.admin')

@section('title', 'Buat Broadcast WhatsApp')
@section('page_title', 'Formulir Broadcast Pesan')

@section('content')
<div class="space-y-4 sm:space-y-6 pb-12 w-full" x-data="waBroadcastForm()">
    <!-- Compact Hero Header -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-slate-950 via-emerald-950 to-slate-900 p-4 sm:p-6 lg:p-8 text-white shadow-xl shadow-emerald-950/20 border border-slate-800/80">
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-2">
                <div class="inline-flex items-center space-x-2 px-3 py-0.5 bg-white/10 backdrop-blur-md rounded-full border border-white/10 text-[10px] sm:text-xs font-extrabold text-emerald-200">
                    <span class="w-2 h-2 rounded-full {{ $activeProvider === 'disabled' ? 'bg-amber-400' : 'bg-emerald-400 animate-pulse' }}"></span>
                    <span>Provider WA Aktif: <strong class="uppercase text-white font-black tracking-wider">{{ $activeProvider }}</strong></span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight text-white">
                    Buat Broadcast WhatsApp Baru
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/80 font-medium">
                    Susun pesan pengumuman dan tentukan kelompok target penerima broadcast secara presisi.
                </p>
            </div>

            <a href="{{ route('admin.wa-broadcasts.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center h-10 px-4 rounded-xl sm:rounded-2xl bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs uppercase tracking-wider transition-all duration-200 border border-white/15 backdrop-blur-md shadow-sm space-x-2 shrink-0 text-center hover:scale-[1.02] active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali Ke Daftar</span>
            </a>
        </div>
    </div>

    <!-- Alert Gateway Disabled -->
    @if($activeProvider === 'disabled')
        <div class="p-4 sm:p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Perhatian: Provider WA Gateway Masih Nonaktif</h4>
                    <p class="text-xs text-slate-600 dark:text-slate-400 font-medium">Pengiriman pesan broadcast memerlukan provider WA (Fonnte / Onesender) yang aktif.</p>
                </div>
            </div>
            <a href="{{ route('admin.settings', ['tab' => 'wagateway']) }}" class="w-full sm:w-auto text-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-extrabold text-xs uppercase tracking-wider shrink-0 shadow-sm">Buka Setting WA</a>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.wa-broadcasts.store') }}" class="grid lg:grid-cols-2 gap-6 lg:gap-8 items-start" x-data="{ submitting: false }">
        @csrf

        <!-- Card 1: Judul & Target -->
        <div class="premium-card p-4 sm:p-6 lg:p-8 space-y-5 sm:space-y-6 shadow-sm border-slate-200">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-extrabold text-xs flex items-center justify-center border border-emerald-100 dark:border-emerald-900/60 shadow-2xs">1</div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Judul & Target Penerima</h3>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 font-semibold">Tentukan judul internal dan kelompok kontak penerima</p>
                    </div>
                </div>

                <!-- Title Input -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Judul / Subjek Broadcast <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Pengumuman Pembagian Rapor Semester Ganjil"
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-boxdark-2 border border-slate-200/90 dark:border-slate-700 rounded-2xl focus:bg-white dark:focus:bg-boxdark focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none font-bold text-sm text-slate-800 dark:text-white transition-all shadow-2xs">
                    @error('title') <p class="text-xs text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Target Cards Grid -->
                <div class="space-y-3">
                    <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Pilih Target Penerima Broadcast <span class="text-rose-500">*</span></label>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3">
                        <!-- All Students -->
                        <label :class="targetType === 'all_students' ? 'border-emerald-600 bg-emerald-50/60 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-300 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300 bg-white dark:bg-boxdark-2'"
                               class="p-3.5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between space-y-2 min-h-[90px] relative group">
                            <div class="flex items-center justify-between">
                                <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                </div>
                                <input type="radio" name="target_type" value="all_students" x-model="targetType" class="w-4 h-4 text-emerald-600">
                            </div>
                            <div>
                                <p class="font-extrabold text-xs text-slate-900 dark:text-white leading-tight">Semua Siswa</p>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold">{{ number_format($counts['all_students']) }} Kontak</span>
                            </div>
                        </label>

                        <!-- All Parents -->
                        <label :class="targetType === 'all_parents' ? 'border-emerald-600 bg-emerald-50/60 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-300 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300 bg-white dark:bg-boxdark-2'"
                               class="p-3.5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between space-y-2 min-h-[90px] relative group">
                            <div class="flex items-center justify-between">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </div>
                                <input type="radio" name="target_type" value="all_parents" x-model="targetType" class="w-4 h-4 text-emerald-600">
                            </div>
                            <div>
                                <p class="font-extrabold text-xs text-slate-900 dark:text-white leading-tight">Orang Tua / Wali</p>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold">{{ number_format($counts['all_parents']) }} Kontak</span>
                            </div>
                        </label>

                        <!-- All Teachers -->
                        <label :class="targetType === 'all_teachers' ? 'border-emerald-600 bg-emerald-50/60 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-300 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300 bg-white dark:bg-boxdark-2'"
                               class="p-3.5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between space-y-2 min-h-[90px] relative group">
                            <div class="flex items-center justify-between">
                                <div class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="radio" name="target_type" value="all_teachers" x-model="targetType" class="w-4 h-4 text-emerald-600">
                            </div>
                            <div>
                                <p class="font-extrabold text-xs text-slate-900 dark:text-white leading-tight">Guru & Staff</p>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold">{{ number_format($counts['all_teachers']) }} Kontak</span>
                            </div>
                        </label>

                        <!-- Specific Class -->
                        <label :class="targetType === 'class' ? 'border-emerald-600 bg-emerald-50/60 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-300 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300 bg-white dark:bg-boxdark-2'"
                               class="p-3.5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between space-y-2 min-h-[90px] relative group">
                            <div class="flex items-center justify-between">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                                <input type="radio" name="target_type" value="class" x-model="targetType" class="w-4 h-4 text-emerald-600">
                            </div>
                            <div>
                                <p class="font-extrabold text-xs text-slate-900 dark:text-white leading-tight">Per Kelas</p>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold">Filter Kelas</span>
                            </div>
                        </label>

                        <!-- SPMB Candidates -->
                        <label :class="targetType === 'spmb' ? 'border-emerald-600 bg-emerald-50/60 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-300 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300 bg-white dark:bg-boxdark-2'"
                               class="p-3.5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between space-y-2 min-h-[90px] relative group">
                            <div class="flex items-center justify-between">
                                <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <input type="radio" name="target_type" value="spmb" x-model="targetType" class="w-4 h-4 text-emerald-600">
                            </div>
                            <div>
                                <p class="font-extrabold text-xs text-slate-900 dark:text-white leading-tight">Calon Siswa SPMB</p>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold">{{ number_format($counts['spmb']) }} Pendaftar</span>
                            </div>
                        </label>

                        <!-- Custom Manual -->
                        <label :class="targetType === 'custom' ? 'border-emerald-600 bg-emerald-50/60 dark:bg-emerald-950/30 text-emerald-900 dark:text-emerald-300 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300 bg-white dark:bg-boxdark-2'"
                               class="p-3.5 rounded-2xl border-2 cursor-pointer transition-all flex flex-col justify-between space-y-2 min-h-[90px] relative group">
                            <div class="flex items-center justify-between">
                                <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <input type="radio" name="target_type" value="custom" x-model="targetType" class="w-4 h-4 text-emerald-600">
                            </div>
                            <div>
                                <p class="font-extrabold text-xs text-slate-900 dark:text-white leading-tight">Kontak Custom</p>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold">Input Manual</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Conditional Class Select -->
                <div x-show="targetType === 'class'" x-transition class="space-y-2 pt-1">
                    <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Pilih Kelas Target <span class="text-rose-500">*</span></label>
                    <select name="target_class_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-boxdark-2 border border-slate-200/90 dark:border-slate-700 rounded-2xl font-bold text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500 shadow-2xs">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('target_class_id') == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }} ({{ $c->students_count ?? '' }} Siswa)</option>
                        @endforeach
                    </select>
                </div>

                <!-- Conditional Custom Numbers Textarea -->
                <div x-show="targetType === 'custom'" x-transition class="space-y-2 pt-1">
                    <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Daftar Nomor WhatsApp Custom <span class="text-rose-500">*</span></label>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">Format per baris: <code>08123456789 - Nama Penerima</code> atau pisahkan dengan koma.</p>
                    <textarea name="custom_numbers" rows="4" class="w-full px-4 py-3 bg-slate-50 dark:bg-boxdark-2 border border-slate-200/90 dark:border-slate-700 rounded-2xl font-mono text-xs text-slate-800 dark:text-white outline-none focus:border-emerald-500 shadow-2xs" placeholder="081234567890 - Ahmad&#10;089876543210 - Budi">{{ old('custom_numbers') }}</textarea>
                </div>
            </div>

            <!-- Card 2: Message Content -->
            <div class="premium-card p-4 sm:p-6 lg:p-8 space-y-5 sm:space-y-6 shadow-sm border-slate-200">
                <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-extrabold text-xs flex items-center justify-center border border-emerald-100 dark:border-emerald-900/60 shadow-2xs">2</div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Isi Pesan Broadcast</h3>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 font-semibold">Gunakan tag variabel dinamis untuk menyisipkan data personal penerima</p>
                    </div>
                </div>

                <!-- Dynamic Variables Toolbar -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Klik Tag Untuk Menyisipkan Variabel Dinamis:</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="insertVar('{nama}')" class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-900/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 font-mono text-xs font-bold transition-all shadow-2xs flex items-center space-x-1">
                            <span class="text-emerald-500 font-black">+</span>
                            <span>{nama}</span>
                        </button>
                        <button type="button" @click="insertVar('{nisn}')" class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200/80 dark:border-indigo-900/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 font-mono text-xs font-bold transition-all shadow-2xs flex items-center space-x-1">
                            <span class="text-indigo-500 font-black">+</span>
                            <span>{nisn}</span>
                        </button>
                        <button type="button" @click="insertVar('{kelas}')" class="px-3 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-400 border border-purple-200/80 dark:border-purple-900/40 hover:bg-purple-100 dark:hover:bg-purple-900/40 font-mono text-xs font-bold transition-all shadow-2xs flex items-center space-x-1">
                            <span class="text-purple-500 font-black">+</span>
                            <span>{kelas}</span>
                        </button>
                        <button type="button" @click="insertVar('{sekolah}')" class="px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200/80 dark:border-amber-900/40 hover:bg-amber-100 dark:hover:bg-amber-900/40 font-mono text-xs font-bold transition-all shadow-2xs flex items-center space-x-1">
                            <span class="text-amber-500 font-black">+</span>
                            <span>{sekolah}</span>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Isi Teks Pesan WhatsApp <span class="text-rose-500">*</span></label>
                    <textarea name="message" x-model="message" x-ref="messageTextarea" rows="8" required
                              class="w-full px-4 py-3.5 bg-slate-50 dark:bg-boxdark-2 border border-slate-200/90 dark:border-slate-700 rounded-2xl focus:bg-white dark:focus:bg-boxdark focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none text-sm leading-relaxed text-slate-800 dark:text-white font-medium shadow-2xs transition-all"></textarea>
                    @error('message') <p class="text-xs text-rose-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Real-time WhatsApp Chat Bubble Preview (Inside Card 2) -->
                <div class="space-y-2 pt-2">
                    <span class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Live Preview Tampilan WhatsApp:</span>
                    <div class="rounded-2xl bg-[#0b141a] p-4 border border-slate-200 dark:border-slate-800 shadow-inner relative overflow-hidden" 
                         style="background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px); background-size: 14px 14px;">
                        <div class="self-end bg-[#005c4b] text-[#e9edef] p-3 rounded-xl rounded-tr-none shadow-md max-w-[95%] sm:max-w-[85%] space-y-1.5 border border-[#005c4b]/30 font-sans ml-auto">
                            <p class="text-[11px] leading-relaxed whitespace-pre-line font-medium text-slate-100 animate-pulse-once" 
                               x-text="getPreviewText()"></p>
                            <div class="text-[9px] text-[#8696a0] flex items-center justify-end space-x-1 font-semibold pt-1">
                                <span>{{ now()->format('H:i') }}</span>
                                <span class="text-[#53bdeb] font-extrabold">✓✓</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Action Block (Inside Card 2) -->
                <div class="pt-5 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <button type="submit" 
                            @click="submitting = true"
                            :disabled="'{{ $activeProvider }}' === 'disabled' || submitting"
                            class="w-full py-3.5 bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-500 hover:to-teal-500 text-white rounded-2xl font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/25 hover:scale-[1.02] active:scale-95 transition-all duration-200 flex items-center justify-center space-x-2 disabled:opacity-50 cursor-pointer">
                        <svg x-show="!submitting" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span x-text="submitting ? 'Memproses Broadcast...' : 'Kirim Broadcast WA Sekarang'"></span>
                    </button>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 text-center font-semibold leading-relaxed">
                        Pesan akan diproses dan dikirimkan secara serentak ke nomor penerima yang valid.
                    </p>
                </div>
            </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('waBroadcastForm', () => ({
        targetType: {!! json_encode(old('target_type', 'all_students')) !!}, 
        message: {!! json_encode(old('message', "Yth. {nama},\n\nBerikut pengumuman resmi dari {sekolah}:\n\n[Tuliskan Isi Informasi Di Sini]\n\nTerima kasih.")) !!},
        schoolName: {!! json_encode(Setting::get('school_name', 'Sekolah')) !!},
        getPreviewText() {
            let txt = this.message || '';
            txt = txt.replace(/{nama}/g, 'Ahmad Supriyadi');
            txt = txt.replace(/{nisn}/g, '1234567890');
            txt = txt.replace(/{kelas}/g, 'XII IPA 1');
            txt = txt.replace(/{sekolah}/g, this.schoolName);
            return txt;
        },
        insertVar(tag) {
            const textarea = this.$refs.messageTextarea;
            if (!textarea) {
                this.message += " " + tag;
                return;
            }
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = this.message;
            const before = text.substring(0, start);
            const after = text.substring(end, text.length);
            this.message = before + tag + after;
            
            this.$nextTick(() => {
                textarea.focus();
                const newCursorPos = start + tag.length;
                textarea.setSelectionRange(newCursorPos, newCursorPos);
            });
        }
    }));
});
</script>
@endpush
@endsection
