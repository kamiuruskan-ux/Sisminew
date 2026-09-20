@extends('layouts.admin')

@section('title', 'Data Siswa')
@section('page_title', 'Direktori Siswa')

@section('content')
<div class="space-y-6" x-data="{
    selected: [],
    selectAll: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    isBulkDelete: false,
    toast: { show: false, message: '', type: 'success' },

    // Import Modal State
    showImportModal: false,
    mergeOnly: false,
    importStep: 'idle', // 'idle', 'processing', 'completed'
    importProgress: 0,
    importStatusText: 'Menyiapkan berkas...',
    selectedFile: null,
    importResult: { total: 0, imported: 0, skipped: 0, logs: [] },
    terminalLogs: [],

    toggleSelectAll() {
        if (this.selectAll) {
            this.selected = [{{ implode(',', $students->pluck('id')->toArray()) }}];
        } else {
            this.selected = [];
        }
    },
    
    updateSelectAll() {
        const allIds = [{{ implode(',', $students->pluck('id')->toArray()) }}];
        this.selectAll = allIds.length > 0 && allIds.every(id => this.selected.includes(id));
    },

    confirmDelete(encodedId, studentName) {
        this.isBulkDelete = false;
        this.deleteTarget = { id: encodedId, name: studentName };
        this.deleteFormAction = '{{ url('admin/students') }}/' + encodedId;
        this.showDeleteModal = true;
    },

    confirmBulkDelete() {
        if (this.selected.length === 0) return;
        this.isBulkDelete = true;
        this.deleteTarget = { id: null, name: this.selected.length + ' siswa terpilih' };
        this.deleteFormAction = '{{ route('admin.students.bulk-destroy') }}';
        this.showDeleteModal = true;
    },

    navigateToBulkEdit() {
        if (this.selected.length === 0) return;
        const queryParams = this.selected.map(id => 'ids[]=' + encodeURIComponent(id)).join('&');
        window.location.href = '{{ route('admin.students.bulk-edit') }}?' + queryParams;
    },

    handleFileSelect(event) {
        this.selectedFile = event.target.files[0] || null;
    },

    async submitImport() {
        if (!this.selectedFile) return;

        this.importStep = 'processing';
        this.importProgress = 12;
        this.importStatusText = 'Membaca & Memvalidasi File CSV...';
        this.terminalLogs = [
            'Initiating secure import engine...',
            'Reading file: ' + this.selectedFile.name + ' (' + (this.selectedFile.size / 1024).toFixed(1) + ' KB)'
        ];

        let progressTimer = setInterval(() => {
            if (this.importProgress < 85) {
                this.importProgress += Math.floor(Math.random() * 12) + 4;
                if (this.importProgress >= 35 && this.importProgress < 60) {
                    this.importStatusText = 'Membuat Akun User & Assign Role Siswa...';
                } else if (this.importProgress >= 60 && this.importProgress < 85) {
                    this.importStatusText = 'Mengaitkan Rombel Kelas & Program Keahlian...';
                }
            }
        }, 220);

        const formData = new FormData();
        formData.append('file', this.selectedFile);
        formData.append('merge_only', this.mergeOnly ? '1' : '0');
        formData.append('_token', '{{ csrf_token() }}');

        try {
            const response = await fetch('{{ route('admin.students.import') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            clearInterval(progressTimer);
            const data = await response.json();

            if (data.success) {
                this.importProgress = 100;
                this.importStatusText = 'Proses Import Selesai!';
                this.importResult = data;
                
                if (data.logs && data.logs.length > 0) {
                    data.logs.forEach(log => this.terminalLogs.push(log));
                }
                this.terminalLogs.push('[COMPLETE] ' + data.message);
                this.importStep = 'completed';
            } else {
                this.importStep = 'idle';
                this.importProgress = 0;
                alert(data.message || 'Gagal mengimpor data.');
            }
        } catch (err) {
            clearInterval(progressTimer);
            this.importStep = 'idle';
            this.importProgress = 0;
            alert('Terjadi kesalahan koneksi atau server: ' + err.message);
        }
    },

    resetImportModal() {
        this.showImportModal = false;
        if (this.importStep === 'completed') {
            window.location.reload();
        } else {
            this.importStep = 'idle';
            this.importProgress = 0;
            this.selectedFile = null;
            this.terminalLogs = [];
        }
    }
}"
@notify-toast.window="
    toast.message = $event.detail.message;
    toast.type = $event.detail.type || 'success';
    toast.show = true;
    setTimeout(() => { toast.show = false; }, 3500);
">
    <!-- Floating Toast Notification -->
    <div x-show="toast.show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
         class="fixed top-5 right-5 z-[9999] max-w-md px-5 py-3.5 rounded-2xl shadow-2xl flex items-center justify-between gap-3 border"
         :class="toast.type === 'error' ? 'bg-rose-600 text-white border-rose-400' : 'bg-emerald-600 text-white border-emerald-400'"
         style="display: none;">
        <div class="flex items-center space-x-3">
            <div class="w-7 h-7 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                <template x-if="toast.type !== 'error'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </template>
            </div>
            <p class="text-xs font-semibold text-white" x-text="toast.message"></p>
        </div>
        <button @click="toast.show = false" class="text-white/80 hover:text-white font-bold text-lg leading-none">&times;</button>
    </div>

    @component('components.delete-modal', ['title' => 'Hapus Data Siswa', 'message' => 'Apakah Anda yakin ingin menghapus data siswa <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.'])
        <template x-if="isBulkDelete">
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </template>
    @endcomponent

    <!-- Animated Import Modal -->
    <div x-show="showImportModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Backdrop -->
            <div x-show="showImportModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-md"
                 @click="if(importStep !== 'processing') resetImportModal()">
            </div>

            <!-- Modal Body Container -->
            <div x-show="showImportModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full max-w-2xl p-6 sm:p-8 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] shadow-2xl rounded-3xl">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-200 dark:border-indigo-800 font-bold shrink-0 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-[#1C2434] dark:text-white tracking-tight">Import Data Siswa (Excel .xlsx)</h3>
                            <p class="text-xs text-[#64748B] dark:text-[#8A99AD]">Unggah berkas Excel (.xlsx / .xls) dengan format data siswa sekolah</p>
                        </div>
                    </div>
                    <button type="button" @click="resetImportModal()" x-show="importStep !== 'processing'" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- STATE 1: IDLE / FILE SELECTION -->
                <div x-show="importStep === 'idle'" class="space-y-6">
                    <!-- Template Banner -->
                    <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/80 dark:border-indigo-800 flex items-center justify-between gap-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-indigo-500 text-white flex items-center justify-center font-bold shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-indigo-900 dark:text-indigo-200">Belum punya format file Excel?</p>
                                <p class="text-[11px] text-indigo-700/80 dark:text-indigo-300">Gunakan template resmi .xlsx untuk mencegah kesalahan format data.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.students.download-template') }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shrink-0 shadow-xs flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Unduh Template (.xlsx)</span>
                        </a>
                    </div>

                    <!-- Dropzone -->
                    <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-indigo-500 rounded-3xl p-8 text-center transition-all bg-slate-50/50 dark:bg-[#1A222C]/40 group">
                        <input type="file" @change="handleFileSelect($event)" accept=".xlsx,.xls,.csv,.txt" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="space-y-3 pointer-events-none">
                            <div class="w-16 h-16 mx-auto rounded-3xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-extrabold text-[#1C2434] dark:text-white" x-text="selectedFile ? selectedFile.name : 'Tarik & Letakkan berkas Excel (.xlsx) di sini, atau Klik untuk memilih'"></p>
                                <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] mt-1" x-text="selectedFile ? (selectedFile.size / 1024).toFixed(1) + ' KB' : 'Mendukung format .XLSX, .XLS, .CSV (Maksimal 10MB)'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Merge Mode Toggle -->
                    <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 rounded-2xl border border-amber-200 dark:border-amber-800/60">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" x-model="mergeOnly" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-700 mt-0.5">
                            <div class="text-[11px]">
                                <span class="font-bold text-amber-900 dark:text-amber-200">Mode Merge / Pembaruan Saja</span>
                                <p class="text-amber-700 dark:text-amber-300 text-[10px] mt-0.5">Hanya perbarui data siswa yang sudah terdaftar di sistem (berdasarkan NISN, NIK, Email, atau Nama) tanpa menambahkan siswa baru.</p>
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" @click="resetImportModal()" class="px-5 py-2.5 border border-slate-200 dark:border-slate-700 text-[#1C2434] dark:text-white rounded-xl text-xs font-bold hover:bg-slate-100 dark:hover:bg-[#1A222C] transition">
                            Batal
                        </button>
                        <button type="button" @click="submitImport()" :disabled="!selectedFile"
                                :class="selectedFile ? 'bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/30' : 'bg-slate-300 dark:bg-slate-800 opacity-50 cursor-not-allowed'"
                                class="px-6 py-2.5 text-white rounded-xl text-xs font-bold transition flex items-center space-x-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Mulai Import Realtime</span>
                        </button>
                    </div>
                </div>

                <!-- STATE 2: ANIMATED REAL-TIME PROCESSING -->
                <div x-show="importStep === 'processing'" class="space-y-6">
                    <!-- Progress Bar Banner -->
                    <div class="p-6 rounded-3xl bg-slate-900 text-white space-y-4 border border-slate-800 shadow-xl relative overflow-hidden">
                        <div class="absolute -right-12 -top-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-2xl"></div>
                        
                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></div>
                                <span class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider">Excel Engine Processing</span>
                            </div>
                            <span class="text-xl font-black text-white font-mono" x-text="importProgress + '%'"></span>
                        </div>

                        <!-- Animated Progress Bar -->
                        <div class="w-full bg-slate-800 rounded-full h-3.5 overflow-hidden p-0.5 border border-slate-700">
                            <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-400 h-full rounded-full transition-all duration-300 shadow-lg shadow-indigo-500/50" :style="'width: ' + importProgress + '%'"></div>
                        </div>

                        <p class="text-xs text-slate-300 font-mono font-semibold flex items-center space-x-2" x-text="importStatusText"></p>
                    </div>

                    <!-- Live Real-Time Terminal Log Feed -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between px-1">
                            <span class="text-[11px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Realtime Terminal Stream</span>
                            </span>
                            <span class="text-[10px] font-mono text-emerald-400">LOG ACTIVE</span>
                        </div>
                        <div class="bg-[#0D1117] text-emerald-400 p-4 rounded-2xl font-mono text-[11px] h-48 overflow-y-auto border border-slate-800 space-y-1.5 shadow-inner">
                            <template x-for="(log, idx) in terminalLogs" :key="idx">
                                <div class="leading-relaxed flex items-start space-x-2">
                                    <span class="text-slate-600 select-none">&gt;</span>
                                    <span x-text="log" :class="log.includes('[FAIL]') ? 'text-rose-400 font-bold' : (log.includes('[SKIP]') ? 'text-amber-400' : 'text-emerald-300')"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- STATE 3: COMPLETED SUMMARY SCREEN -->
                <div x-show="importStep === 'completed'" class="space-y-6">
                    <div class="text-center py-4 space-y-3">
                        <div class="w-16 h-16 mx-auto rounded-3xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/30 flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h4 class="text-xl font-black text-[#1C2434] dark:text-white">Proses Import Excel Selesai!</h4>
                        <p class="text-xs text-[#64748B] dark:text-[#8A99AD]" x-text="importResult.message"></p>
                    </div>

                    <!-- Summary Counter Grid -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-center">
                            <p class="text-[10px] font-extrabold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Total Data</p>
                            <p class="text-2xl font-black text-indigo-900 dark:text-indigo-200" x-text="importResult.total"></p>
                        </div>
                        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center">
                            <p class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Berhasil</p>
                            <p class="text-2xl font-black text-emerald-900 dark:text-emerald-200" x-text="importResult.imported"></p>
                        </div>
                        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-center">
                            <p class="text-[10px] font-extrabold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Dilewati / Gagal</p>
                            <p class="text-2xl font-black text-amber-900 dark:text-amber-200" x-text="importResult.skipped"></p>
                        </div>
                    </div>

                    <!-- Log Details (Expandable) -->
                    <div x-data="{ showLogs: false }" class="space-y-2">
                        <button type="button" @click="showLogs = !showLogs" class="w-full py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center justify-between transition cursor-pointer">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Rincian Log Eksekusi Baris (<span x-text="terminalLogs.length"></span> catatan)</span>
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="showLogs ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="showLogs" class="bg-[#0D1117] text-emerald-400 p-4 rounded-2xl font-mono text-[11px] max-h-48 overflow-y-auto border border-slate-800 space-y-1.5 shadow-inner">
                            <template x-for="(log, idx) in terminalLogs" :key="idx">
                                <div class="leading-relaxed flex items-start space-x-2">
                                    <span class="text-slate-600 select-none">&gt;</span>
                                    <span x-text="log" :class="log.includes('[FAIL]') ? 'text-rose-400 font-bold' : (log.includes('[SKIP]') ? 'text-amber-400' : 'text-emerald-300')"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="button" @click="resetImportModal()" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-emerald-600/30">
                            Selesai & Refresh Data
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
    <!-- TailAdmin Top Action Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Direktori Data Siswa</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Kelola informasi data siswa aktif, kelas, jurusan, serta cetak Kartu Tanda Siswa (KTS).
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Export Data -->
            <a href="{{ route('admin.students.export', request()->query()) }}" 
               class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-600 hover:text-white text-emerald-700 dark:text-emerald-300 text-xs font-bold rounded-xl transition-all border border-emerald-200 dark:border-emerald-800 shadow-2xs" title="Export Data Siswa ke Excel (.xlsx)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Excel</span>
            </a>

            <!-- Import Data Button -->
            @permission('create-students')
            <button type="button" @click="showImportModal = true"
                    class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-600 hover:text-white text-indigo-700 dark:text-indigo-300 text-xs font-bold rounded-xl transition-all border border-indigo-200 dark:border-indigo-800 shadow-2xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import Excel</span>
            </button>
            @endpermission

            <!-- Edit Masal Kelas & Jurusan -->
            @permission('edit-students')
            <a href="{{ route('admin.students.bulk-edit', request()->query()) }}"
               class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-600 hover:text-white text-amber-700 dark:text-amber-300 text-xs font-bold rounded-xl transition-all border border-amber-200 dark:border-amber-800 shadow-2xs" title="{{ Setting::get('is_vocational', '1') == '1' ? 'Edit Masal Kelas & Jurusan' : 'Edit Masal Kelas' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>{{ Setting::get('is_vocational', '1') == '1' ? 'Edit Masal Kelas & Jurusan' : 'Edit Masal Kelas' }}</span>
            </a>
            @endpermission

            <!-- Tambah Siswa Baru -->
            @permission('create-students')
            <a href="{{ route('admin.students.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-[#3C50E0] hover:bg-[#3C50E0]/90 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-[#3C50E0]/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Siswa</span>
            </a>
            @endpermission
        </div>
    </div>



    <!-- TailAdmin KPI Summary Cards (3 Cards Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="tailadmin-card p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Total Siswa Terdaftar</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white mt-1 tracking-tight">{{ number_format($totalStudents) }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-[#3C50E0]/10 text-[#3C50E0] flex items-center justify-center border border-[#3C50E0]/20 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100-8 4 4 0 000 8z"/>
                </svg>
            </div>
        </div>

        <div class="tailadmin-card p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Siswa Laki-Laki</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white mt-1 tracking-tight">{{ number_format($maleCount) }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-200 dark:border-blue-800 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>

        <div class="tailadmin-card p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Siswa Perempuan</p>
                <p class="text-2xl sm:text-3xl font-extrabold text-[#1C2434] dark:text-white mt-1 tracking-tight">{{ number_format($femaleCount) }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-200 dark:border-rose-800 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- TailAdmin Filter & Search Panel (Real-Time Auto Submit) -->
    <div class="tailadmin-card p-5">
        <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3.5">
            <!-- Search Keyword -->
            <div class="xl:col-span-2">
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Cari Siswa</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, NISN, NIK, email..." 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl pl-10 pr-8 py-2.5 focus:outline-none focus:border-[#3C50E0]">
                    <svg class="w-4 h-4 text-[#64748B] dark:text-[#8A99AD] absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if(request('search'))
                        <a href="{{ route('admin.students.index', request()->except('search')) }}" class="absolute right-3 top-2.5 text-[#64748B] hover:text-rose-500 transition font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
            </div>

            <x-major-class-select 
                :majors="$majors" 
                :classes="$classes" 
                :selected-major="request('major_id')" 
                :selected-class="request('class_id')" 
                :is-filter="true" 
                layout="inline" 
                major-label="Jurusan" 
                class-label="Kelas" 
                select-class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0]" 
                label-class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5" 
                :on-major-change="'$el.closest(\'form\').submit()'"
                :on-class-change="'$el.closest(\'form\').submit()'"
            />

            <!-- Filter Gender -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                <select name="gender" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0]">
                    <option value="">Semua Gender</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <!-- Sort By Name / Latest -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Urutan</label>
                <select name="sort" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0] font-semibold">
                    <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                    <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
                </select>
            </div>

            <!-- Per Page (10, 20, 50, 100) -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Tampilkan</label>
                <select name="per_page" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0] font-semibold">
                    <option value="10" {{ request('per_page', '10') == '10' ? 'selected' : '' }}>10 Baris</option>
                    <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 Baris</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 Baris</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 Baris</option>
                </select>
            </div>
        </form>
    </div>

    <!-- TailAdmin Students Directory Table -->
    <div class="tailadmin-card overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between bg-slate-50/50 dark:bg-[#1A222C]/40">
            <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base tracking-tight">Daftar Siswa</h3>
            <span class="text-xs text-[#64748B] dark:text-[#8A99AD]">Menampilkan {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} siswa</span>
        </div>

        <!-- Bulk Action Banner Bar -->
        <div x-show="selected.length > 0" x-cloak x-transition class="flex items-center justify-between px-6 py-3 bg-indigo-50 dark:bg-indigo-950/40 border-b border-indigo-200 dark:border-indigo-800">
            <div class="flex items-center space-x-2 text-indigo-700 dark:text-indigo-300 font-bold text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="selected.length + ' data siswa dipilih'"></span>
            </div>
            <div class="flex items-center space-x-2">
                <button type="button" @click="navigateToBulkEdit()" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center space-x-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Edit Masal Terpilih</span>
                </button>
                <button type="button" @click="confirmBulkDelete()" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center space-x-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Hapus Terpilih</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider font-bold border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                    <tr>
                        <th class="px-4 py-3.5 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-[#3C50E0] focus:ring-[#3C50E0] cursor-pointer" title="Pilih Semua">
                        </th>
                        <th class="px-6 py-3.5">Profil Siswa</th>
                        <th class="px-6 py-3.5">NISN / NIK</th>
                        <th class="px-6 py-3.5">{{ Setting::get('is_vocational', '1') == '1' ? 'Kelas & Jurusan' : 'Kelas' }}</th>
                        <th class="px-6 py-3.5">Kontak & Orang Tua</th>
                        <th class="px-6 py-3.5">Status Akun</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] text-[#1C2434] dark:text-[#DEE4EE]">
                    @forelse($students as $student)
                        <tr class="hover:bg-[#F1F5F9]/60 dark:hover:bg-[#1A222C]/50 transition-colors" :class="{ 'bg-rose-50/30 dark:bg-rose-950/20': selected.includes({{ $student->id }}) }">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" :value="{{ $student->id }}" x-model="selected" @change="updateSelectAll()" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-[#3C50E0] focus:ring-[#3C50E0] cursor-pointer">
                            </td>
                            <!-- Student Profile -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3.5">
                                     @if($student->photo_url)
                                        <img src="{{ $student->photo_url }}"
                                             alt="{{ $student->user->name ?? 'Siswa' }}"
                                             class="w-10 h-10 rounded-xl object-cover border border-[#E2E8F0] dark:border-[#2E3A47] shrink-0">
                                     @else
                                        <div class="w-10 h-10 rounded-xl bg-[#3C50E0]/10 text-[#3C50E0] font-extrabold flex items-center justify-center text-xs border border-[#3C50E0]/20 shrink-0">
                                            {{ strtoupper(substr($student->user->name ?? 'S', 0, 2)) }}
                                        </div>
                                     @endif
                                    <div>
                                        <p class="font-bold text-[#1C2434] dark:text-white">{{ $student->user->name ?? '-' }}</p>
                                        <div class="flex items-center space-x-2 mt-0.5">
                                            <span class="text-[11px] text-[#64748B] dark:text-[#8A99AD]">{{ $student->user->email ?? '-' }}</span>
                                            <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-full {{ $student->gender === 'male' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800' }}">
                                                {{ $student->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- NISN / NIK -->
                            <td class="px-6 py-4">
                                <p class="font-bold text-[#1C2434] dark:text-white font-mono text-xs">{{ $student->nisn ?? '-' }}</p>
                                <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] mt-0.5">NIK: {{ $student->nik ?? '-' }}</p>
                            </td>

                            <!-- Class & Major -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <span class="inline-flex px-3 py-1 rounded-full bg-[#F1F5F9] dark:bg-[#1A222C] text-[#1C2434] dark:text-white font-bold text-xs border border-[#E2E8F0] dark:border-[#2E3A47]">
                                        {{ $student->class->name ?? 'Belum Ditentukan' }}
                                    </span>
                                    @if($student->major && Setting::get('is_vocational', '1') == '1')
                                        <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] font-semibold truncate max-w-[160px]">{{ $student->major->name }}</p>
                                    @endif
                                </div>
                            </td>

                            <!-- Contact & Parent -->
                            <td class="px-6 py-4">
                                <p class="font-bold text-[#1C2434] dark:text-white">{{ $student->phone ?? $student->user->phone ?? '-' }}</p>
                                @if($student->parent_name)
                                    <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] mt-0.5">Wali: {{ $student->parent_name }}</p>
                                @endif
                            </td>

                            <!-- Status Akun & Toggle Unlock Bruteforce -->
                            <td class="px-6 py-4" x-data="{ 
                                isActive: {{ ($student->user?->status !== 'inactive' && !($student->user?->isLockedOut() ?? false)) ? 'true' : 'false' }},
                                isLocked: {{ ($student->user?->isLockedOut() ?? false) ? 'true' : 'false' }},
                                loading: false,
                                async toggleStatus() {
                                    this.loading = true;
                                    try {
                                        const res = await fetch('{{ route('admin.students.toggle-status', encode_id($student->id)) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            }
                                        });
                                        const data = await res.json();
                                        if (data.success) {
                                            this.isActive = (data.status === 'active' && !data.is_locked);
                                            this.isLocked = data.is_locked;
                                            window.dispatchEvent(new CustomEvent('notify-toast', { detail: { message: data.message, type: 'success' } }));
                                        } else {
                                            window.dispatchEvent(new CustomEvent('notify-toast', { detail: { message: data.message || 'Gagal mengubah status akun siswa.', type: 'error' } }));
                                        }
                                    } catch(e) {
                                        window.dispatchEvent(new CustomEvent('notify-toast', { detail: { message: 'Kesalahan jaringan: ' + e.message, type: 'error' } }));
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }">
                                <div class="flex items-center space-x-2">
                                    <button type="button" @click="toggleStatus()" :disabled="loading"
                                            :class="isActive ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'"
                                            class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" title="Klik toggle untuk mengaktifkan / membuka kuncian bruteforce akun siswa">
                                        <span :class="isActive ? 'translate-x-5' : 'translate-x-0'"
                                              class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"></span>
                                    </button>
                                    <template x-if="isLocked">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 animate-pulse flex items-center gap-1" title="Terkunci otomatis karena salah password berulang (bruteforce). Klik toggle untuk membuka kuncian.">
                                            🔒 Bruteforce
                                        </span>
                                    </template>
                                    <template x-if="!isLocked">
                                        <span :class="isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'"
                                              class="text-[10px] font-extrabold"
                                              x-text="isActive ? 'Aktif' : 'Nonaktif'"></span>
                                    </template>
                                </div>
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <!-- Detail -->
                                    <a href="{{ route('admin.students.show', encode_id($student->id)) }}"
                                       class="p-2 text-[#64748B] hover:text-[#3C50E0] hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition-colors" title="Detail Siswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <!-- Print KTS -->
                                    <a href="{{ route('admin.students.print-card', encode_id($student->id)) }}" target="_blank"
                                       class="p-2 text-[#64748B] hover:text-[#3C50E0] hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition-colors" title="Cetak Kartu Tanda Siswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 00-2 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </a>

                                    <!-- Edit -->
                                    @permission('edit-students')
                                    <a href="{{ route('admin.students.edit', encode_id($student->id)) }}"
                                       class="p-2 text-[#64748B] hover:text-[#3C50E0] hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition-colors" title="Edit Data Siswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @endpermission

                                    <!-- Delete -->
                                    @permission('delete-students')
                                    <button type="button" @click="confirmDelete('{{ encode_id($student->id) }}', '{{ addslashes($student->user->name ?? 'Siswa') }}')"
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition-colors cursor-pointer" title="Hapus Siswa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endpermission
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-[#64748B] dark:text-[#8A99AD] text-xs font-semibold">
                                Tidak ada data siswa yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-[#E2E8F0] dark:border-[#2E3A47] bg-slate-50/50 dark:bg-[#1A222C]/40">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

