@extends('layouts.admin')

@section('title', 'Database & Backup')
@section('page_title', 'Pemeliharaan Database & Backup')

@section('content')
<div class="space-y-6" x-data="{ 
    showMigrateModal: false, 
    showDeleteModal: false, 
    isMigrating: false,
    migrationStatusText: 'Menghubungkan ke server database...',
    migrationStatusIndex: 0,
    migrationStatusList: [
        'Menghubungkan ke server database...',
        'Memeriksa berkas skema & migrasi baru...',
        'Menyiapkan transaksi & mengunci tabel...',
        'Mengeksekusi SQL Skema (php artisan migrate)...',
        'Menyinkronkan struktur indeks & foreign key...',
        'Hampir selesai, memvalidasi integritas data...'
    ],
    startMigrateLoading() {
        this.isMigrating = true;
        setInterval(() => {
            this.migrationStatusIndex = (this.migrationStatusIndex + 1) % this.migrationStatusList.length;
            this.migrationStatusText = this.migrationStatusList[this.migrationStatusIndex];
        }, 1200);
        setTimeout(() => {
            document.getElementById('migrateForm').submit();
        }, 400);
    },
    deleteTargetUrl: '', 
    deleteTargetName: '' 
}">
    <!-- Header Banner -->
    <div class="tailadmin-card p-6 border-l-4 border-primary">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-primary/10 text-primary rounded-2xl flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Database & Backup Maintenance</h2>
                    <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">Kelola pembaruan skema database (migration) dan buat cadangan data (backup) sistem sekolah secara langsung.</p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" @click="showMigrateModal = true" class="btn-primary flex items-center space-x-2 px-4 py-2.5 shadow-md">
                    <svg class="w-4 h-4 animate-spin-hover" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Update Database</span>
                    @if(count($pendingMigrations) > 0)
                        <span class="bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full animate-bounce">{{ count($pendingMigrations) }} Baru</span>
                    @endif
                </button>

                <form action="{{ route('admin.database-maintenance.sync-features') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-md transition" title="Pastikan tabel presensi guru, KPI, halaqah, jurnal guru dibuat otomatis">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Sinkronkan Tabel Fitur Baru</span>
                    </button>
                </form>

                <form action="{{ route('admin.database-maintenance.backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-secondary flex items-center space-x-2 px-4 py-2.5">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span>Backup Data Sekarang</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Migration -->
    <form id="migrateForm" action="{{ route('admin.database-maintenance.migrate') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Hidden Form for Backup Deletion -->
    <form id="deleteBackupForm" :action="deleteTargetUrl" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Database Information Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="tailadmin-card p-5 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Nama Database</p>
                <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white mt-0.5 truncate max-w-[150px]" title="{{ $dbName }}">{{ $dbName }}</h3>
            </div>
        </div>

        <div class="tailadmin-card p-5 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Database Host</p>
                <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white mt-0.5 truncate max-w-[150px]" title="{{ $dbHost }}">{{ $dbHost }}</h3>
            </div>
        </div>

        <div class="tailadmin-card p-5 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Total Tabel</p>
                <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white mt-0.5">{{ $tableCount }} Tabel</h3>
            </div>
        </div>

        <div class="tailadmin-card p-5 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Ukuran DB</p>
                <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white mt-0.5">{{ $dbSize }}</h3>
            </div>
        </div>
    </div>

    <!-- Migration Output Console (If available in session) -->
    @if(session('migration_output'))
        <div class="tailadmin-card p-6 bg-slate-900 text-slate-100 dark:bg-black rounded-2xl border border-slate-800">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-mono text-slate-400 font-bold ml-2">Console Log Migration</span>
                </div>
            </div>
            <pre class="font-mono text-xs text-emerald-400 overflow-x-auto whitespace-pre-wrap leading-relaxed max-h-60 overflow-y-auto">{{ session('migration_output') }}</pre>
        </div>
    @endif

    <!-- Migration Status & Pending Migrations Section -->
    <div class="tailadmin-card overflow-hidden">
        <div class="p-6 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base">Status Berkas Migrasi Database</h3>
                    <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">Daftar berkas skema tabel yang perlu dieksekusi atau diperbarui.</p>
                </div>
            </div>

            @if(count($pendingMigrations) > 0)
                <span class="px-3 py-1 bg-amber-500/10 text-amber-600 dark:text-amber-400 font-extrabold text-xs rounded-full border border-amber-500/20">
                    {{ count($pendingMigrations) }} Migrasi Pending
                </span>
            @else
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-extrabold text-xs rounded-full border border-emerald-500/20 flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Database Up to Date</span>
                </span>
            @endif
        </div>

        @if(count($pendingMigrations) > 0)
            <div class="p-6 bg-amber-500/5 border-b border-amber-500/10">
                <p class="text-xs font-bold text-amber-800 dark:text-amber-300 mb-3">Berkas Migrasi Baru Ditemukan (Belum Dieksekusi):</p>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($pendingMigrations as $mig)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-[#24303F] border border-amber-500/20 shadow-2xs">
                            <span class="font-mono text-xs text-[#1C2434] dark:text-white font-bold">{{ $mig['file'] }}</span>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 bg-amber-500/20 text-amber-700 dark:text-amber-300 rounded">Pending</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="p-6">
            <div class="flex items-center justify-between text-xs text-[#64748B] dark:text-[#8A99AD]">
                <span>Total berkas migrasi terdaftar: <strong>{{ count($migrationFiles) }}</strong></span>
                <span>Telah dieksekusi di DB: <strong>{{ count($executedMigrations) }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Database Backups List Section -->
    <div class="tailadmin-card overflow-hidden">
        <div class="p-6 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base">Arsip Backup Database</h3>
                    <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">Berkas backup tersimpan di direktori publik <code>doc/backups/</code>.</p>
                </div>
            </div>

            <form action="{{ route('admin.database-maintenance.backup') }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary text-xs flex items-center space-x-1.5 px-3 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Backup Baru</span>
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Nama Berkas Backup</th>
                        <th>Ukuran File</th>
                        <th>Waktu Dibuat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $bk)
                        <tr>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="font-mono font-bold text-xs text-[#1C2434] dark:text-white">{{ $bk['name'] }}</span>
                                </div>
                            </td>
                            <td class="font-semibold text-xs text-[#64748B] dark:text-[#8A99AD]">
                                {{ $bk['size'] }}
                            </td>
                            <td class="text-xs text-[#64748B] dark:text-[#8A99AD]">
                                {{ $bk['created_at'] }}
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.database-maintenance.download', $bk['name']) }}" 
                                       class="p-2 rounded-lg bg-blue-500/10 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors" 
                                       title="Unduh Berkas SQL">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>

                                    <button type="button" 
                                            @click="
                                                deleteTargetUrl = '{{ route('admin.database-maintenance.destroy', $bk['name']) }}';
                                                deleteTargetName = '{{ $bk['name'] }}';
                                                showDeleteModal = true;
                                            "
                                            class="p-2 rounded-lg bg-rose-500/10 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors" 
                                            title="Hapus Backup">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-[#64748B] dark:text-[#8A99AD] text-xs">
                                Belum ada berkas backup tersimpan. Klik tombol <strong>"Buat Backup Baru"</strong> di atas untuk membuat cadangan database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Update Database (Migration) dengan Animasi Loader & Running Text -->
    <div x-show="showMigrateModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md"
         x-cloak>
        <div @click.outside="if(!isMigrating) showMigrateModal = false" 
             class="w-full max-w-md bg-white dark:bg-[#1A222C] rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 overflow-hidden">
            
            <!-- Standard Confirmation View -->
            <div x-show="!isMigrating" class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-800">
                        <svg class="w-6 h-6 animate-spin-hover" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Konfirmasi Update Database</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jalankan migrasi skema tabel terbaru</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800/80 text-xs text-indigo-950 dark:text-indigo-200 leading-relaxed space-y-2">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Informasi Pembaruan Database:</span>
                    </p>
                    <p>
                        Apakah Anda yakin ingin menjalankan <strong>Update Database</strong> sekarang? Proses ini akan memperbarui skema tabel basis data sistem ke versi terbaru.
                    </p>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="showMigrateModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" 
                            @click="startMigrateLoading()" 
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-600/30 transition flex items-center space-x-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Ya, Jalankan Update</span>
                    </button>
                </div>
            </div>

            <!-- Animated Loader & Running Status View -->
            <div x-show="isMigrating" x-cloak class="py-6 text-center space-y-6">
                <!-- Glowing Cyber Database Spinner -->
                <div class="relative w-24 h-24 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-4 border-indigo-100 dark:border-indigo-950 border-t-indigo-600 border-r-purple-600 animate-spin"></div>
                    <div class="absolute inset-2 rounded-full border-2 border-purple-100 dark:border-purple-950 border-b-purple-500 border-l-indigo-500 animate-spin" style="animation-direction: reverse; animation-duration: 2s;"></div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center animate-pulse">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                    </div>
                </div>

                <!-- Running Status Ticker -->
                <div class="space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase tracking-wider rounded-full border border-emerald-200 dark:border-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        Proses Migrasi Database Berlangsung...
                    </span>
                    <h4 class="text-sm font-black text-slate-900 dark:text-white h-7 flex items-center justify-center transition-all duration-300" x-text="migrationStatusText"></h4>
                    <p class="text-xs text-slate-400 font-medium px-4">Mohon tunggu sebentar dan jangan menutup atau merefresh halaman ini.</p>
                </div>

                <!-- Animated Shimmer Beam Progress Bar -->
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden relative border border-slate-200 dark:border-slate-700">
                    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-500 h-2.5 rounded-full w-full animate-pulse"></div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Backup Database -->
    <div x-show="showDeleteModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         x-cloak>
        <div @click.outside="showDeleteModal = false" 
             class="w-full max-w-md bg-white dark:bg-[#24303F] rounded-2xl p-6 shadow-2xl border border-[#E2E8F0] dark:border-[#2E3A47] space-y-4">
            
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-500 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white">Konfirmasi Hapus Backup</h3>
                    <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-900 dark:text-rose-200 leading-relaxed">
                Apakah Anda yakin ingin menghapus berkas backup <code class="font-mono font-bold text-rose-700 dark:text-rose-300" x-text="deleteTargetName"></code> dari penyimpanan server?
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" @click="showDeleteModal = false" class="btn-secondary text-xs px-4 py-2">
                    Batal
                </button>
                <button type="button" 
                        @click="document.getElementById('deleteBackupForm').submit()" 
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs">
                    Hapus Berkas
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
