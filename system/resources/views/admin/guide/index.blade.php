@extends('layouts.guide')

@section('title', 'Panduan Lengkap Pengguna - Fitur & Menu System')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Left Navigation Sticky Sidebar (No Print) -->
    <aside class="no-print lg:col-span-3 xl:col-span-3">
        <div class="sticky top-24 space-y-4">
            
            <!-- Quick Summary Card -->
            <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 dark:from-slate-800 dark:via-indigo-950 dark:to-slate-900 rounded-3xl p-5 text-white shadow-xl border border-indigo-500/20 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="flex items-center space-x-3 mb-4 relative z-10">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600/30 flex items-center justify-center font-black text-sm border border-indigo-400/30 text-indigo-300 shadow-inner">
                        PG
                    </div>
                    <div>
                        <h2 class="font-extrabold text-sm leading-tight tracking-tight text-white">Panduan Pengguna</h2>
                        <p class="text-[11px] text-indigo-200/80 font-medium">Panduan 18 Modul &amp; 50+ Menu</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-center pt-3 border-t border-white/10 relative z-10">
                    <div class="bg-white/5 rounded-xl p-2 border border-white/5">
                        <p class="text-[9px] text-indigo-200/70 font-bold uppercase tracking-wider">Kelompok</p>
                        <p class="font-black text-xs sm:text-sm text-white">6 Utama</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-2 border border-white/5">
                        <p class="text-[9px] text-indigo-200/70 font-bold uppercase tracking-wider">Total Modul</p>
                        <p class="font-black text-xs sm:text-sm text-indigo-300">21 Modul</p>
                    </div>
                </div>
            </div>

            <!-- Chapter Menu Jump List -->
            <nav class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-3xl p-4 border border-slate-200/80 dark:border-slate-700/60 shadow-xs space-y-1.5 text-xs max-h-[calc(100vh-230px)] overflow-y-auto">
                <div class="flex items-center justify-between px-2 py-1 mb-1">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500">
                        Navigasi Menu
                    </p>
                    <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300">21 Topik</span>
                </div>
                
                <!-- Group 1 -->
                <div class="space-y-0.5">
                    <p class="px-2 pt-2 pb-1 text-[9px] font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider">1. Menu Utama</p>
                    <a href="#menu-dashboard" 
                       @click="activeSection = 'menu-dashboard'"
                       :class="activeSection === 'menu-dashboard' ? 'bg-indigo-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-2 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-dashboard' ? 'bg-white' : 'bg-indigo-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Dashboard Utama</span>
                    </a>
                </div>

                <!-- Group 2 -->
                <div class="space-y-0.5 pt-1.5">
                    <p class="px-2 pt-2 pb-1 text-[9px] font-black uppercase text-purple-600 dark:text-purple-400 tracking-wider">2. Master Data Sekolah</p>
                    <a href="#menu-users" 
                       @click="activeSection = 'menu-users'"
                       :class="activeSection === 'menu-users' ? 'bg-purple-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-purple-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-users' ? 'bg-white' : 'bg-purple-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Guru &amp; Pegawai</span>
                    </a>
                    <a href="#menu-students" 
                       @click="activeSection = 'menu-students'"
                       :class="activeSection === 'menu-students' ? 'bg-purple-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-purple-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-students' ? 'bg-white' : 'bg-purple-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Siswa, KTS &amp; Face ID</span>
                    </a>
                    <a href="#menu-academic" 
                       @click="activeSection = 'menu-academic'"
                       :class="activeSection === 'menu-academic' ? 'bg-purple-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-purple-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-academic' ? 'bg-white' : 'bg-purple-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Struktur Akademik</span>
                    </a>
                    <a href="#menu-library" 
                       @click="activeSection = 'menu-library'"
                       :class="activeSection === 'menu-library' ? 'bg-purple-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-purple-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-library' ? 'bg-white' : 'bg-purple-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Perpustakaan Digital</span>
                    </a>
                </div>

                <!-- Group 3 -->
                <div class="space-y-0.5 pt-1.5">
                    <p class="px-2 pt-2 pb-1 text-[9px] font-black uppercase text-blue-600 dark:text-blue-400 tracking-wider">3. Akademik &amp; Kesiswaan</p>
                    <a href="#menu-lms-cbt" 
                       @click="activeSection = 'menu-lms-cbt'"
                       :class="activeSection === 'menu-lms-cbt' ? 'bg-blue-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-lms-cbt' ? 'bg-white' : 'bg-blue-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">LMS &amp; Ujian CBT</span>
                    </a>
                    <a href="#menu-cbt-capacity" 
                       @click="activeSection = 'menu-cbt-capacity'"
                       :class="activeSection === 'menu-cbt-capacity' ? 'bg-blue-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-cbt-capacity' ? 'bg-white' : 'bg-blue-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Diagnostik Server CBT</span>
                    </a>
                    <a href="#menu-raport" 
                       @click="activeSection = 'menu-raport'"
                       :class="activeSection === 'menu-raport' ? 'bg-blue-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-raport' ? 'bg-white' : 'bg-blue-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">E-Raport Digital</span>
                    </a>
                    <a href="#menu-attendance" 
                       @click="activeSection = 'menu-attendance'"
                       :class="activeSection === 'menu-attendance' ? 'bg-blue-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-attendance' ? 'bg-white' : 'bg-blue-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Presensi &amp; Terminal Scan</span>
                    </a>
                    <a href="#menu-bk" 
                       @click="activeSection = 'menu-bk'"
                       :class="activeSection === 'menu-bk' ? 'bg-blue-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-bk' ? 'bg-white' : 'bg-blue-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Bimbingan Konseling (BK)</span>
                    </a>
                    <a href="#menu-spmb" 
                       @click="activeSection = 'menu-spmb'"
                       :class="activeSection === 'menu-spmb' ? 'bg-blue-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-spmb' ? 'bg-white' : 'bg-blue-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Penerimaan (SPMB / PPDB)</span>
                    </a>
                </div>

                <!-- Group 4 -->
                <div class="space-y-0.5 pt-1.5">
                    <p class="px-2 pt-2 pb-1 text-[9px] font-black uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">4. Keuangan &amp; Operasional</p>
                    <a href="#menu-financial" 
                       @click="activeSection = 'menu-financial'"
                       :class="activeSection === 'menu-financial' ? 'bg-emerald-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-financial' ? 'bg-white' : 'bg-emerald-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Kasir Pembayaran SPP</span>
                    </a>
                    <a href="#menu-savings" 
                       @click="activeSection = 'menu-savings'"
                       :class="activeSection === 'menu-savings' ? 'bg-emerald-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-savings' ? 'bg-white' : 'bg-emerald-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Tabungan &amp; Dompet Siswa</span>
                    </a>
                    <a href="#menu-canteen" 
                       @click="activeSection = 'menu-canteen'"
                       :class="activeSection === 'menu-canteen' ? 'bg-emerald-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-canteen' ? 'bg-white' : 'bg-emerald-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Digital E-Kantin &amp; Withdraw</span>
                    </a>
                </div>

                <!-- Group 5 -->
                <div class="space-y-0.5 pt-1.5">
                    <p class="px-2 pt-2 pb-1 text-[9px] font-black uppercase text-amber-600 dark:text-amber-400 tracking-wider">5. Informasi &amp; Konten Web</p>
                    <a href="#menu-content" 
                       @click="activeSection = 'menu-content'"
                       :class="activeSection === 'menu-content' ? 'bg-amber-500 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-content' ? 'bg-white' : 'bg-amber-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Berita, Pengumuman &amp; Galeri</span>
                    </a>
                    <a href="#menu-wa-broadcast" 
                       @click="activeSection = 'menu-wa-broadcast'"
                       :class="activeSection === 'menu-wa-broadcast' ? 'bg-amber-500 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-wa-broadcast' ? 'bg-white' : 'bg-amber-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">WA Broadcast Manager</span>
                    </a>
                    <a href="#menu-notifications" 
                       @click="activeSection = 'menu-notifications'"
                       :class="activeSection === 'menu-notifications' ? 'bg-amber-500 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-notifications' ? 'bg-white' : 'bg-amber-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Pusat Notifikasi System</span>
                    </a>
                </div>

                <!-- Group 6 -->
                <div class="space-y-0.5 pt-1.5">
                    <p class="px-2 pt-2 pb-1 text-[9px] font-black uppercase text-rose-600 dark:text-rose-400 tracking-wider">6. Sistem &amp; Pengaturan</p>
                    <a href="#menu-roles" 
                       @click="activeSection = 'menu-roles'"
                       :class="activeSection === 'menu-roles' ? 'bg-rose-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-rose-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-roles' ? 'bg-white' : 'bg-rose-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Role &amp; Hak Akses Pengguna</span>
                    </a>
                    <a href="#menu-security" 
                       @click="activeSection = 'menu-security'"
                       :class="activeSection === 'menu-security' ? 'bg-rose-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-rose-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-security' ? 'bg-white' : 'bg-rose-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Keamanan Sistem &amp; Audit Log</span>
                    </a>
                    <a href="#menu-settings" 
                       @click="activeSection = 'menu-settings'"
                       :class="activeSection === 'menu-settings' ? 'bg-rose-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-rose-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-settings' ? 'bg-white' : 'bg-rose-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Profil Web, Logo &amp; SMTP</span>
                    </a>
                    <a href="#menu-database" 
                       @click="activeSection = 'menu-database'"
                       :class="activeSection === 'menu-database' ? 'bg-rose-600 text-white font-extrabold shadow-xs' : 'text-slate-700 dark:text-slate-200 hover:bg-rose-50 dark:hover:bg-slate-700/60 font-semibold'"
                       class="flex items-center space-x-2.5 px-3 py-1.5 rounded-xl transition-all">
                        <span :class="activeSection === 'menu-database' ? 'bg-white' : 'bg-rose-500'" class="w-2 h-2 rounded-full shrink-0 transition-colors"></span>
                        <span class="truncate">Database Backup &amp; Pembaruan</span>
                    </a>
                </div>

            </nav>

        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:col-span-9 xl:col-span-9 space-y-10">

        <!-- Minimalist Hero Header Banner -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-700/60 shadow-xs relative overflow-hidden">
            <div class="absolute -top-16 -right-16 w-72 h-72 bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-4">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/80 border border-indigo-200/80 dark:border-indigo-800/60 text-indigo-700 dark:text-indigo-300 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    <span>Buku Panduan Operasional Resmi Sistem Informasi Sekolah</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                    Panduan Penggunaan Fitur &amp; Menu Panel Admin
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-3xl leading-relaxed">
                    Petunjuk lengkap penggunaan seluruh fitur, penjelasan fungsi tombol, serta <b>alur kerja praktis langkah demi langkah</b> untuk pengelolaan 6 kelompok utama dan 21 modul operasional sekolah.
                </p>
            </div>

            <!-- Quick Metrics Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-100 dark:border-slate-700/60 relative z-10">
                <div class="p-3.5 bg-slate-50/80 dark:bg-slate-900/50 rounded-2xl border border-slate-200/60 dark:border-slate-700/40">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Peran Pengguna</p>
                    <p class="text-xs font-extrabold text-slate-800 dark:text-slate-200 mt-0.5">11 Peran RBAC Akses</p>
                </div>
                <div class="p-3.5 bg-slate-50/80 dark:bg-slate-900/50 rounded-2xl border border-slate-200/60 dark:border-slate-700/40">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Metode Presensi</p>
                    <p class="text-xs font-extrabold text-slate-800 dark:text-slate-200 mt-0.5">Scan QR + Wajah + GPS</p>
                </div>
                <div class="p-3.5 bg-slate-50/80 dark:bg-slate-900/50 rounded-2xl border border-slate-200/60 dark:border-slate-700/40">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Modul Keuangan</p>
                    <p class="text-xs font-extrabold text-slate-800 dark:text-slate-200 mt-0.5">POS SPP + Tabungan + Kantin</p>
                </div>
                <div class="p-3.5 bg-slate-50/80 dark:bg-slate-900/50 rounded-2xl border border-slate-200/60 dark:border-slate-700/40">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Layanan Komunikasi</p>
                    <p class="text-xs font-extrabold text-slate-800 dark:text-slate-200 mt-0.5">WA Gateway &amp; Broadcast</p>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- GROUP 1: MENU UTAMA -->
        <!-- ========================================== -->
        <div class="space-y-6">
            <div class="flex items-center space-x-3 pb-2 border-b-2 border-indigo-500">
                <span class="px-3 py-1 rounded-xl bg-indigo-500 text-white font-black text-xs uppercase tracking-wider shadow-2xs">Kelompok 1</span>
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Menu Utama Sistem</h2>
            </div>

            <!-- 1.1 DASHBOARD UTAMA -->
            <section id="menu-dashboard" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-indigo-100 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-black text-sm flex items-center justify-center border border-indigo-200 dark:border-indigo-800/50">
                            1.1
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Dashboard Utama Admin</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">Akses: Semua Pengurus</span>
                </div>
                
                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Halaman beranda utama untuk memantau ringkasan aktivitas, indikator kinerja sekolah, grafik statistik kehadiran, arus kas pembayaran, serta pengingat tugas peninjauan yang memerlukan tindakan pengurus.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                        <b class="text-slate-900 dark:text-white block mb-1">Kartu Ringkasan Data:</b>
                        Menampilkan jumlah total siswa aktif, total guru, persentase kehadiran hari ini, serta sisa tunggakan pembayaran sekolah.
                    </div>
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                        <b class="text-slate-900 dark:text-white block mb-1">Grafik Performa Operasional:</b>
                        Grafik tren presensi bulanan serta statistik pemasukan keuangan bulanan sekolah dan kantin.
                    </div>
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                        <b class="text-slate-900 dark:text-white block mb-1">Pemberitahuan &amp; Pengingat:</b>
                        Pengingat verifikasi pembayaran SPP pending, koreksi soal essay CBT, dan permohonan pencairan saldo kantin vendor.
                    </div>
                </div>

                <div class="p-4 sm:p-5 bg-indigo-50/40 dark:bg-indigo-950/20 rounded-2xl border border-indigo-100/80 dark:border-indigo-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-indigo-950 dark:text-indigo-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-indigo-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Masuk Sistem</b>: Setelah berhasil Login, halaman pertama yang langsung muncul adalah <b>Dashboard Utama</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-indigo-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Filter Periode Data</b>: Gunakan drop-down tanggal di bagian kanan atas layar untuk mengubah statistik bulanan atau tahunan.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-indigo-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Membuka Menu Pintas</b>: Klik pada salah satu <b>Kartu Statistik</b> untuk berpindah secara cepat ke halaman manajemen terkait.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- ========================================== -->
        <!-- GROUP 2: MASTER DATA SEKOLAH -->
        <!-- ========================================== -->
        <div class="space-y-6 pt-4">
            <div class="flex items-center space-x-3 pb-2 border-b-2 border-purple-500">
                <span class="px-3 py-1 rounded-xl bg-purple-500 text-white font-black text-xs uppercase tracking-wider shadow-2xs">Kelompok 2</span>
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Master Data Sekolah</h2>
            </div>

            <!-- 2.1 GURU & PEGAWAI -->
            <section id="menu-users" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400 font-black text-sm flex items-center justify-center border border-purple-200 dark:border-purple-800/50">
                            2.1
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Manajemen Data Guru &amp; Pegawai</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">Akses: Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Modul pengelolaan biodata dan akun pengguna untuk seluruh staf pengurus sekolah (Guru Pengajar, Guru BK, Bendahara, Staff TU, dan Operator Sekolah). Untuk akun pengelola kantin dikelola khusus pada menu <b>Digital E-Kantin &gt; Pengguna / Akun Kantin</b>.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                        <b class="text-slate-900 dark:text-white block mb-1">Entri Profil Lengkap:</b>
                        Pengisian NIP, Nama Lengkap, Nomor WhatsApp, Pasfoto Profil, Jenis Kelamin, serta Jabatan Staf.
                    </div>
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                        <b class="text-slate-900 dark:text-white block mb-1">Sakelar Status Aktif/Nonaktif:</b>
                        Fitur menonaktifkan akses staf yang mutasi atau berhenti bekerja hanya dengan 1 kali klik.
                    </div>
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                        <b class="text-slate-900 dark:text-white block mb-1">Reset Password Staf:</b>
                        Fasilitas pengaturan ulang kata sandi baru untuk staf yang lupa password akunnya.
                    </div>
                </div>

                <div class="p-4 sm:p-5 bg-purple-50/40 dark:bg-purple-950/20 rounded-2xl border border-purple-100/80 dark:border-purple-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-purple-950 dark:text-purple-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Buka Menu</b>: Pilih menu sidebar <b>Master Data</b> lalu klik <b>Guru &amp; Pegawai</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Tambah Guru Baru</b>: Klik tombol <b>+ Tambah Guru Baru</b>, isi formulir data diri, pilih <b>Role Akses</b>, unggah foto profil, lalu klik <b>Simpan Data</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Cari &amp; Filter Staf</b>: Ketik Nama/NIP di kotak pencarian atau gunakan dropdown filter peran.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">4</span>
                            <p class="leading-relaxed"><b>Ubah &amp; Reset Sandi</b>: Klik tombol <b>Aksi (Pensil/Kunci)</b> untuk memperbarui profil atau kata sandi baru.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2.2 DATA SISWA, KTS & FACE ID -->
            <section id="menu-students" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400 font-black text-sm flex items-center justify-center border border-purple-200 dark:border-purple-800/50">
                            2.2
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Data Siswa, Cetak Kartu KTS &amp; Registrasi Wajah Face ID</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">Akses: Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Pusat pengelolaan biodata siswa, pencetakan Kartu Tanda Siswa (KTS) resmi ber-barcode QR, serta perekaman titik koordinat wajah (Face Recognition) untuk presensi digital modern.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50 space-y-1">
                        <b class="text-slate-900 dark:text-white block">1. Manajemen &amp; Impor Excel:</b>
                        <p class="text-slate-600 dark:text-slate-300">Pencarian siswa per kelas/NISN, fitur <b>Impor Massal Excel</b>, ekspor data, dan penataan PIN dompet tabungan.</p>
                    </div>
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50 space-y-1">
                        <b class="text-slate-900 dark:text-white block">2. Cetak Kartu KTS Massal:</b>
                        <p class="text-slate-600 dark:text-slate-300">Desain KTS otomatis lengkap dengan foto, barcode QR NISN, stempel sekolah, dan tanda tangan Kepala Sekolah.</p>
                    </div>
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50 space-y-1">
                        <b class="text-slate-900 dark:text-white block">3. Perekaman Face ID Wajah:</b>
                        <p class="text-slate-600 dark:text-slate-300">Perekaman pola sampel wajah siswa melalui kamera webcam untuk integrasi mesin scan presensi gerbang.</p>
                    </div>
                </div>

                <div class="p-4 sm:p-5 bg-purple-50/40 dark:bg-purple-950/20 rounded-2xl border border-purple-100/80 dark:border-purple-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-purple-950 dark:text-purple-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Impor Siswa Massal</b>: Klik <b>Impor Excel</b>, unduh template, isi data NISN, Nama, Kelas, lalu unggah file Excel.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Cetak KTS Per Kelas</b>: Filter daftar siswa per <b>Kelas Target</b>, centang semua siswa, lalu klik <b>Cetak Kartu KTS (PDF)</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Rekam Face ID Wajah</b>: Klik icon kamera pada baris nama siswa, posisikan wajah di depan webcam hingga indikator berkedip hijau, lalu simpan.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2.3 STRUKTUR AKADEMIK -->
            <section id="menu-academic" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400 font-black text-sm flex items-center justify-center border border-purple-200 dark:border-purple-800/50">
                            2.3
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Struktur Akademik (Kelas, Jurusan, Mapel, Jadwal &amp; Tahun Pelajaran)</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">Akses: Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Fasilitas pendukung konfigurasi fondasi pembelajaran: penetapan Tingkat Kelas, Jurusan/Program Keahlian, Daftar Mata Pelajaran, Plotting Jadwal Mingguan, serta Penentuan Tahun Pelajaran Aktif.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                        <b class="text-slate-900 dark:text-white block mb-1">Manajemen Kelas &amp; Wali Kelas:</b>
                        Pengaturan nama rombel (misal: X-IPA 1) dan penunjukan Guru Pengampu Wali Kelas.
                    </div>
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                        <b class="text-slate-900 dark:text-white block mb-1">Mata Pelajaran &amp; KKM:</b>
                        Daftar Mata Pelajaran (Mapel), kode unik pelajaran, serta Bobot KKM Penilaian.
                    </div>
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50">
                        <b class="text-slate-900 dark:text-white block mb-1">Jadwal Jam Pelajaran:</b>
                        Penyusunan jadwal harian jam masuk, mata pelajaran, dan ruangan per kelas.
                    </div>
                </div>

                <div class="p-4 sm:p-5 bg-purple-50/40 dark:bg-purple-950/20 rounded-2xl border border-purple-100/80 dark:border-purple-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-purple-950 dark:text-purple-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Set Tahun Ajaran Aktif</b>: Masuk tab <b>Tahun Pelajaran</b>, klik tombol <b>Aktifkan</b> pada tahun periode berjalan (misal: 2025/2026 Ganjil).</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Buat Kelas &amp; Wali Kelas</b>: Buka tab <b>Data Kelas</b>, klik <b>+ Tambah Kelas</b>, isi nama kelas dan pilih Guru dari dropdown Wali Kelas.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Plotting Jadwal Pelajaran</b>: Masuk menu <b>Jadwal Pelajaran</b>, pilih Kelas, tentukan Hari &amp; Jam, lalu pilih Mapel beserta Guru Pengampu.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2.4 PERPUSTAKAAN DIGITAL -->
            <section id="menu-library" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-purple-100 dark:bg-purple-950 text-purple-600 dark:text-purple-400 font-black text-sm flex items-center justify-center border border-purple-200 dark:border-purple-800/50">
                            2.4
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Perpustakaan Digital &amp; Pembaca E-Book</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">Akses: Guru, Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Layanan Katalog Buku Digital (E-Book) dan Sirkulasi Peminjaman Buku Fisik. Fitur unggulannya adalah <b>Built-in PDF Reader</b> yang memungkinkan siswa membaca buku digital langsung di layar HP/Laptop tanpa perlu mengunduh file.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-purple-50/40 dark:bg-purple-950/20 rounded-2xl border border-purple-100/80 dark:border-purple-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-purple-950 dark:text-purple-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Tambah Judul Buku</b>: Klik <b>+ Tambah Buku</b>, unggah sampul buku, isi Judul, Pengarang, Kategori, serta unggah berkas PDF E-Book.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Catat Peminjaman Fisik</b>: Masuk tab <b>Sirkulasi Peminjaman</b>, scan barcode NISN siswa, pilih buku, lalu tentukan tanggal wajib kembali.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Pengembalian &amp; Denda</b>: Klik tombol <b>Kembalikan Buku</b> pada tabel sirkulasi; denda keterlambatan terhitung otomatis.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- ========================================== -->
        <!-- GROUP 3: AKADEMIK & KESISWAAN -->
        <!-- ========================================== -->
        <div class="space-y-6 pt-4">
            <div class="flex items-center space-x-3 pb-2 border-b-2 border-blue-500">
                <span class="px-3 py-1 rounded-xl bg-blue-500 text-white font-black text-xs uppercase tracking-wider shadow-2xs">Kelompok 3</span>
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Akademik &amp; Kesiswaan</h2>
            </div>

            <!-- 3.1 LMS & CBT -->
            <section id="menu-lms-cbt" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 font-black text-sm flex items-center justify-center border border-blue-200 dark:border-blue-800/50">
                            3.1
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">LMS Pembelajaran, Tugas Siswa &amp; Ujian Online CBT</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Akses: Guru, Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Sistem manajemen pembelajaran digital interaktif dan pelaksanaan ujian berbasis komputer (CBT) yang mendukung pengacakan soal, sistem keamanan ujian anti-curang, serta pemeriksaan nilai otomatis.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50 space-y-1">
                        <b class="text-slate-900 dark:text-white block">1. Materi &amp; Live Class:</b>
                        <p class="text-slate-600 dark:text-slate-300">Penyusunan modul ajar, penautan tautan Video Meeting, serta pemberian Tugas Online dengan tenggat waktu.</p>
                    </div>
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-900/60 rounded-2xl border border-slate-200/70 dark:border-slate-700/50 space-y-1">
                        <b class="text-slate-900 dark:text-white block">2. Ujian Online &amp; Impor Excel:</b>
                        <p class="text-slate-600 dark:text-slate-300">Impor Bank Soal dari Excel, penjadwalan ujian, acak opsi jawaban, serta analisis kelulusan otomatis.</p>
                    </div>
                </div>

                <div class="p-4 sm:p-5 bg-blue-50/40 dark:bg-blue-950/20 rounded-2xl border border-blue-100/80 dark:border-blue-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-blue-950 dark:text-blue-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Impor Bank Soal</b>: Pilih menu <b>Bank Soal</b>, klik tombol <b>Impor Excel</b>, unduh template soal, lalu unggah file soal yang telah terisi.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Buat Jadwal Ujian</b>: Pilih menu <b>Jadwal Ujian CBT</b>, klik <b>+ Jadwal Baru</b>, tentukan Kelas, Jam Mulai, Durasi Menit, dan Token Ujian.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Koreksi Soal Uraian</b>: Setelah ujian selesai, buka tab <b>Hasil Ujian</b>, klik <b>Koreksi Essay</b> untuk memberi skor pada jawaban uraian siswa.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3.2 CBT SERVER CAPACITY -->
            <section id="menu-cbt-capacity" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 font-black text-sm flex items-center justify-center border border-blue-200 dark:border-blue-800/50">
                            3.2
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Pemeriksaan &amp; Optimasi Performa Server Ujian CBT</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Akses: Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Alat ukur kesehatan server ujian untuk menghitung kapasitas maksimal jumlah siswa yang dapat mengerjakan ujian online secara serentak tanpa mengalami hambatan atau kelemotan.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-blue-50/40 dark:bg-blue-950/20 rounded-2xl border border-blue-100/80 dark:border-blue-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-blue-950 dark:text-blue-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Input Jumlah Siswa CBT</b>: Ketik angka jumlah siswa pada kotak <b>Jumlah Siswa Mengerjakan Ujian CBT</b>, atau klik tombol preset <b>Ujian Berlangsung Saat Ini</b> / <b>Total Siswa Aktif Sekolah</b> untuk mengisi otomatis jumlah siswa.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Pilih Intensitas Trafik Ujian</b>: Klik rentang waktu jeda trafik (misal <b>5 Dtk</b> untuk aktivitas simpan jawaban serentak atau <b>1-2 Menit</b> untuk trafik bertahap), lalu periksa status hasil simulasi di sebelah kanan.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Jalankan Optimasi 1-Klik</b>: Jika ada peringatan atau saran optimasi, klik tombol <b>Jalankan Semua Optimasi Server</b> sebelum ujian dilaksanakan agar respon server maksimal.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3.3 E-RAPORT DIGITAL -->
            <section id="menu-raport" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 font-black text-sm flex items-center justify-center border border-blue-200 dark:border-blue-800/50">
                            3.3
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">E-Raport Digital &amp; Pengolahan Nilai</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Akses: Guru, Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Modul pengolahan nilai Harian, Tugas, Kuis, UTS, dan UAS. Mendukung <b>Impor Nilai Excel</b>, pengisian deskripsi perkembangan siswa oleh Wali Kelas, dan pencetakan Raport resmi PDF A4.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-blue-50/40 dark:bg-blue-950/20 rounded-2xl border border-blue-100/80 dark:border-blue-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-blue-950 dark:text-blue-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Input Nilai Mata Pelajaran</b>: Pilih Kelas dan Mapel, ketik nilai harian dan ujian pada tabel, lalu klik <b>Simpan Nilai</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Impor Excel Kolektif</b>: Gunakan opsi <b>Impor Nilai Excel</b> untuk mengisi nilai satu kelas sekaligus.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Cetak Raport PDF</b>: Masuk menu <b>Cetak Raport</b>, pilih nama siswa atau seluruh kelas, lalu klik <b>Cetak Raport PDF</b>.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3.4 PRESENSI DIGITAL & TERMINAL -->
            <section id="menu-attendance" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 font-black text-sm flex items-center justify-center border border-blue-200 dark:border-blue-800/50">
                            3.4
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Presensi Digital, Izin Siswa &amp; Terminal Scan Gerbang</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Akses: Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Pengelolaan rekap presensi harian, verifikasi surat permohonan izin/sakit siswa, presensi swafoto GPS guru, serta pengoperasian Terminal Scan Barcode QR di gerbang sekolah.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-blue-50/40 dark:bg-blue-950/20 rounded-2xl border border-blue-100/80 dark:border-blue-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-blue-950 dark:text-blue-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Verifikasi Surat Izin Siswa</b>: Masuk tab <b>Persetujuan Izin</b>, periksa foto surat dari siswa, lalu klik <b>Setujui (Izin/Sakit)</b> atau <b>Tolak</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Buka Terminal Scan Gerbang</b>: Klik <b>Buka Standalone Terminal</b> pada layar monitor gerbang; setiap kali siswa men-scan KTS, notifikasi pesan otomatis terkirim ke WhatsApp Orang Tua.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Ekspor Rekap Laporan</b>: Filter tanggal dan kelas, lalu klik <b>Ekspor Excel</b> untuk mengunduh rekap presensi bulanan.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3.5 BIMBINGAN KONSELING (BK) -->
            <section id="menu-bk" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 font-black text-sm flex items-center justify-center border border-blue-200 dark:border-blue-800/50">
                            3.5
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Bimbingan Konseling (BK) &amp; Berita Acara Pelanggaran</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Akses: Guru BK, Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Layanan kedisiplinan dan pembinaan siswa: pencatatan poin pelanggaran tata tertib, reward poin prestasi, pembuatan <b>Berita Acara Pelanggaran (BAP)</b> resmi, penerbitan Surat Peringatan (SP-1, SP-2, SP-3), sesi bimbingan konseling individu/kelompok, serta asesmen minat bakat siswa.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-blue-50/40 dark:bg-blue-950/20 rounded-2xl border border-blue-100/80 dark:border-blue-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-blue-950 dark:text-blue-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Login Akun Guru BK Demo</b>: Gunakan email <b>guru.bk@sekolah.id</b> atau <b>bk@sekolah.id</b> dengan kata sandi <b>password</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Input Pelanggaran Siswa</b>: Cari nama siswa, pilih jenis pelanggaran tata tertib, masukkan poin sanksi, lalu simpan.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Cetak Berita Acara (BAP)</b>: Klik <b>Cetak BAP</b> pada baris pelanggaran untuk mencetak dokumen cetak panggilan orang tua.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3.6 PENERIMAAN (SPMB / PPDB) -->
            <section id="menu-spmb" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 font-black text-sm flex items-center justify-center border border-blue-200 dark:border-blue-800/50">
                            3.6
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Penerimaan Siswa Baru (SPMB / PPDB Online)</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">Akses: Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Manajemen pendaftaran calon siswa baru, verifikasi kelengkapan berkas fisik/PDF, konfirmasi pembayaran pendaftaran, serta <b>Fitur Konversi Akun Otomatis menjadi Siswa Aktif</b>.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-blue-50/40 dark:bg-blue-950/20 rounded-2xl border border-blue-100/80 dark:border-blue-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-blue-950 dark:text-blue-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Verifikasi Berkas Calon Siswa</b>: Buka menu <b>Pendaftar Masuk</b>, periksa lampiran Akta &amp; Kartu Keluarga, ubah status menjadi <b>Lolos Verifikasi</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Konversi ke Siswa Aktif</b>: Klik tombol <b>Terima Siswa &amp; Konversi Akun</b>; pendaftar otomatis terdaftar sebagai Siswa Aktif dan mendapatkan NISN serta Kelas tujuan.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- ========================================== -->
        <!-- GROUP 4: KEUANGAN & OPERASIONAL -->
        <!-- ========================================== -->
        <div class="space-y-6 pt-4">
            <div class="flex items-center space-x-3 pb-2 border-b-2 border-emerald-500">
                <span class="px-3 py-1 rounded-xl bg-emerald-500 text-white font-black text-xs uppercase tracking-wider shadow-2xs">Kelompok 4</span>
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Keuangan &amp; Operasional Sekolah</h2>
            </div>

            <!-- 4.1 POS PEMBAYARAN SPP & KAS -->
            <section id="menu-financial" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center border border-emerald-200 dark:border-emerald-800/50">
                            4.1
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Kasir Pembayaran SPP, Tagihan &amp; Jurnal Keuangan</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Akses: Bendahara &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Layanan Kasir POS untuk penerimaan pembayaran SPP Bulanan, Uang Gedung, Seragam, serta pencatatan arus kas Pemasukan dan Pengeluaran operational sekolah. Dilengkapi konversi nominal mata uang Rupiah dan kalimat Terbilang otomatis.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-emerald-50/40 dark:bg-emerald-950/20 rounded-2xl border border-emerald-100/80 dark:border-emerald-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-emerald-950 dark:text-emerald-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Pilih Siswa Target</b>: Ketik Nama/NISN pada komponen pencarian siswa. Sistem otomatis menampilkan daftar tunggakan SPP yang belum lunas.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Proses Bayar SPP</b>: Centang bulan tagihan yang ingin dibayar, isi nominal Rupiah (kalimat Terbilang akan otomatis muncul di bawah input), lalu klik <b>Bayar Sekarang</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Cetak Kwitansi</b>: Setelah transaksi sukses, klik <b>Cetak Struk Kwitansi</b> untuk menyerahkan bukti fisik pembayaran ke orang tua.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4.2 TABUNGAN SISWA & E-WALLET -->
            <section id="menu-savings" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center border border-emerald-200 dark:border-emerald-800/50">
                            4.2
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tabungan Siswa, Setoran Deposit &amp; Dompet Digital</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Akses: Bendahara &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Pengelolaan transaksi Setor Tunai dan Tarik Tunai Tabungan Siswa, konfirmasi top-up deposit transfer bank, pencetakan Buku Mutasi Tabungan, serta integrasi saldo dompet elektronik untuk belanja di Kantin.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-emerald-50/40 dark:bg-emerald-950/20 rounded-2xl border border-emerald-100/80 dark:border-emerald-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-emerald-950 dark:text-emerald-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Setor Tunai Tabungan</b>: Klik tombol <b>+ Setor Tunai</b>, masukkan nominal Rupiah, centang <b>Catat juga ke Laporan Pemasukan Kas Sekolah</b> jika setoran ingin langsung dimasukkan ke pembukuan kas sekolah, lalu klik <b>Simpan Setoran</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Tarik Tunai Tabungan</b>: Klik tombol <b>- Tarik Tunai</b>, masukkan nominal yang akan ditarik dan alasan/catatan penarikan, lalu klik <b>Simpan Penarikan</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Konfirmasi Deposit Transfer</b>: Buka tab <b>Permohonan Top-Up</b>, periksa bukti transfer dari orang tua, lalu klik <b>Setujui Deposit</b>.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4.3 DIGITAL E-KANTIN & WITHDRAW -->
            <section id="menu-canteen" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center border border-emerald-200 dark:border-emerald-800/50">
                            4.3
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Pengelolaan E-Kantin Digital &amp; Pencairan Saldo Vendor</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Akses: Kantin / Vendor, Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Modul pengelolaan ekosistem kantin sekolah digital: Manajemen Menu &amp; Stok Makanan, Kasir Transaksi Pembayaran <b>Scan QR Kartu Tanda Siswa (KTS) via Saldo Tabungan dengan PIN Keamanan</b>, serta Persetujuan Pencairan Saldo (Withdrawal) omset pedagang kantin.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-emerald-50/40 dark:bg-emerald-950/20 rounded-2xl border border-emerald-100/80 dark:border-emerald-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-emerald-950 dark:text-emerald-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Kelola Menu Makanan</b>: Masuk menu <b>Katalog Stand Kantin</b>, tambah foto makanan/minuman, tentukan harga dan jumlah stok harian.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Kasir POS Scan KTS &amp; Verifikasi PIN</b>: Di menu <b>Kasir POS Kantin</b>, pilih metode <b>Tabungan Siswa (Scan KTS)</b>. Pindai QR Code pada Kartu Siswa (KTS) menggunakan kamera scanner atau cari nama siswa. Sistem memverifikasi kecukupan saldo tabungan, lalu minta siswa memasukkan <b>6-digit PIN Keamanan Siswa</b> untuk konfirmasi pembayaran.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Persetujuan Pencairan Dana</b>: Buka tab <b>Pencairan Saldo Vendor</b>, periksa nominal penarikan pedagang, lalu klik <b>Setujui &amp; Transfer</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">4</span>
                            <p class="leading-relaxed"><b>Kelola Akun Pengelola Kantin</b>: Buka menu <b>Pengguna / Akun Kantin</b> di bawah navigasi <b>Digital E-Kantin</b> untuk membuat akun vendor baru, mengubah password, menonaktifkan akun, atau menautkan akun ke stand pedagang.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- ========================================== -->
        <!-- GROUP 5: INFORMASI & KONTEN WEB -->
        <!-- ========================================== -->
        <div class="space-y-6 pt-4">
            <div class="flex items-center space-x-3 pb-2 border-b-2 border-amber-500">
                <span class="px-3 py-1 rounded-xl bg-amber-500 text-white font-black text-xs uppercase tracking-wider shadow-2xs">Kelompok 5</span>
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Informasi &amp; Konten Website</h2>
            </div>

            <!-- 5.1 KONTEN WEBSITE -->
            <section id="menu-content" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 font-black text-sm flex items-center justify-center border border-amber-200 dark:border-amber-800/50">
                            5.1
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Pengelolaan Konten Website (Berita, Pengumuman &amp; Galeri)</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Akses: Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Fasilitas penyuntingan konten publik sekolah: Penerbitan Artikel Berita/Blog, Pengumuman Resmi Sekolah, Galeri Foto Dokumentasi, dan Banner Slider Halaman Depan.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-amber-50/40 dark:bg-amber-950/20 rounded-2xl border border-amber-100/80 dark:border-amber-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-amber-950 dark:text-amber-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-amber-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Tulis Artikel Berita &amp; Pengumuman</b>: Buka menu <b>Berita Sekolah</b> atau <b>Pengumuman</b>, klik <b>+ Tulis Artikel / Buat Pengumuman</b>, unggah Gambar Sampul (jika ada), isi Judul dan Narasi Konten menggunakan editor visual WYSIWYG gratis (dilengkapi fitur format teks tebal, miring, warna, ukuran huruf, tabel, gambar, tautan, dan video tanpa memerlukan kunci API), lalu klik <b>Publikasikan</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-amber-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Unggah Foto Galeri</b>: Masuk menu <b>Galeri Foto</b>, buat album kegiatan baru, unggah foto-foto dokumentasi, lalu simpan.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-amber-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">3</span>
                            <p class="leading-relaxed"><b>Kelola Hero Slider Banner</b>: Buka menu <b>Hero Slider Website</b>, klik <b>Tambah Slider Baru</b>, unggah gambar banner (hingga 5MB format JPG/PNG/WEBP/SVG), isi Judul, Deskripsi, Tautan Tujuan (misal URL penuh atau tautan relatif seperti <b>/spmb</b>), atur Nomor Urutan tampil, lalu klik <b>Simpan Slider</b>.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5.2 WA BROADCAST MANAGER -->
            <section id="menu-wa-broadcast" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 font-black text-sm flex items-center justify-center border border-amber-200 dark:border-amber-800/50">
                            5.2
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Pengiriman Pesan Massal WhatsApp (WA Broadcast)</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Akses: Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Layanan broadcast pesan otomatis WhatsApp ke nomor HP orang tua/siswa/guru secara serentak untuk mengabarkan pengumuman libur, kegiatan sekolah, atau edaran resmi.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-amber-50/40 dark:bg-amber-950/20 rounded-2xl border border-amber-100/80 dark:border-amber-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-amber-950 dark:text-amber-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-amber-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Pilih Grup Target Penerima</b>: Tentukan target penerima (misal: Seluruh Orang Tua Kelas X).</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-amber-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Ketik Isi Pesan Broadcast</b>: Tuliskan teks pesan pengumuman pada kolom narasi, lalu klik <b>Kirim Pesan Broadcast</b>.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5.3 PUSAT NOTIFIKASI -->
            <section id="menu-notifications" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 font-black text-sm flex items-center justify-center border border-amber-200 dark:border-amber-800/50">
                            5.3
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Pusat Pemberitahuan Sistem (Notifikasi)</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">Akses: Semua Pengurus</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Kotak masuk notifikasi real-time yang memuat pengingat penting mengenai transaksi pending, surat izin masuk, dan laporan insiden.
                    </p>
                </div>
            </section>
        </div>

        <!-- ========================================== -->
        <!-- GROUP 6: SISTEM & PENGATURAN -->
        <!-- ========================================== -->
        <div class="space-y-6 pt-4">
            <div class="flex items-center space-x-3 pb-2 border-b-2 border-rose-500">
                <span class="px-3 py-1 rounded-xl bg-rose-500 text-white font-black text-xs uppercase tracking-wider shadow-2xs">Kelompok 6</span>
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Sistem, Hak Akses &amp; Pengaturan</h2>
            </div>

            <!-- 6.1 ROLE & HAK AKSES -->
            <section id="menu-roles" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 font-black text-sm flex items-center justify-center border border-rose-200 dark:border-rose-800/50">
                            6.1
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Pengaturan Role &amp; Hak Akses Pengguna (RBAC)</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Akses: Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Manajemen matriks izin hak akses (Role-Based Access Control) untuk membatasi atau mengizinkan staf membuka menu tertentu di dalam panel admin.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-rose-50/40 dark:bg-rose-950/20 rounded-2xl border border-rose-100/80 dark:border-rose-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-rose-950 dark:text-rose-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-rose-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Pilih Peran Target</b>: Buka menu <b>Role &amp; Hak Akses</b>, pilih nama peran (misal: Guru BK, Bendahara, atau Operator).</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-rose-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Atur Matriks Izin (CRUD)</b>: Centang atau hilangkan centang pada kotak menu yang diizinkan untuk diakses (misalnya izin Tambah, Edit, Hapus data Siswa, Kelas, Jurusan, serta Layanan BK), lalu klik <b>Simpan Matriks Hak Akses</b>.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 6.2 KEAMANAN SYSTEM -->
            <section id="menu-security" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 font-black text-sm flex items-center justify-center border border-rose-200 dark:border-rose-800/50">
                            6.2
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Keamanan Sistem, Catatan Login &amp; Audit Log</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Akses: Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Memantau riwayat aktivitas login seluruh akun pengurus, audit trail perubahan data, serta fitur pembukaan akun terkunci akibat kegagalan login berulang kali.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-rose-50/40 dark:bg-rose-950/20 rounded-2xl border border-rose-100/80 dark:border-rose-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-rose-950 dark:text-rose-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-rose-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Buka Kunci Akun Terkunci</b>: Masuk tab <b>Akun Terkunci</b>, cari username pengguna yang terblokir, lalu klik <b>Buka Kunci Akun</b>.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-rose-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Periksa Audit Log Aktivitas</b>: Buka tab <b>Audit Log</b> untuk melacak siapa pengurus yang mengubah data nominal atau biodata tertentu.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 6.3 PROFIL WEB & KONFIGURASI -->
            <section id="menu-settings" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 font-black text-sm flex items-center justify-center border border-rose-200 dark:border-rose-800/50">
                            6.3
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Profil Pimpinan, Identitas Sekolah &amp; Server Email</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Akses: Admin &amp; Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Pengaturan identitas resmi lembaga sekolah: Nama Sekolah, NPSN, Alamat Lengkap, Foto Pimpinan, Stempel Resmi Sekolah, Tanda Tangan Digital Pimpinan, dan Logo Utama.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-rose-50/40 dark:bg-rose-950/20 rounded-2xl border border-rose-100/80 dark:border-rose-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-rose-950 dark:text-rose-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-rose-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Pengaturan Identitas &amp; Logo</b>: Buka tab <b>Umum</b> untuk memperbarui Nama Sekolah, NPSN, Jenjang, Logo Utama, dan Favicon, kemudian klik <b>Simpan Pengaturan Utama</b>. Informasi Nama Sekolah dan NPSN pada tab Kop Surat secara otomatis mengikuti data resmi yang tersimpan dari tab Umum.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-rose-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Atur Profil Pimpinan</b>: Masuk tab <b>Profil Kepala Sekolah</b>, isi Nama, NIP, Sambutan, dan unggah Tanda Tangan Digital untuk otomatis dicetak pada Kartu KTS dan Raport.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 6.4 DATABASE MAINTENANCE & MIGRATION -->
            <section id="menu-database" class="guide-card scroll-mt-24 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-3xl p-6 border border-slate-200/80 dark:border-slate-700/60 shadow-xs hover:shadow-md transition-all space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-2xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 font-black text-sm flex items-center justify-center border border-rose-200 dark:border-rose-800/50">
                            6.4
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Cadangan Database (Backup) &amp; Pembaruan Sistem</h3>
                    </div>
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Akses: Super Admin</span>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Keterangan Fitur &amp; Fungsi:
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Fasilitas pembuatan file cadangan data sekolah (Backup Database) secara berkala dan tombol pembaruan struktur database otomatis untuk menjamin keamanan dan keutuhan data sekolah.
                    </p>
                </div>

                <div class="p-4 sm:p-5 bg-rose-50/40 dark:bg-rose-950/20 rounded-2xl border border-rose-100/80 dark:border-rose-900/40 space-y-3 text-xs">
                    <h4 class="font-bold text-rose-950 dark:text-rose-200 flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Cara Penggunaan &amp; Langkah Kerja:
                    </h4>
                    <div class="space-y-2 text-slate-700 dark:text-slate-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-rose-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">1</span>
                            <p class="leading-relaxed"><b>Unduh Cadangan Database</b>: Klik tombol <b>+ Buat Cadangan Database Baru</b>, lalu simpan file cadangan ke tempat yang aman.</p>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-rose-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">2</span>
                            <p class="leading-relaxed"><b>Pembaruan Otomatis</b>: Apabila terdapat pembaruan fitur baru, klik <b>Jalankan Pembaruan Sistem</b> untuk meng-update struktur database tanpa kehilangan data lama.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>
</div>
@endsection
