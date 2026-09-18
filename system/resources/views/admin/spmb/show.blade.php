@extends('layouts.admin')

@section('title', 'Detail Pendaftaran SPMB - ' . $spmb->registration_number)
@section('page_title', 'Detail Pendaftaran Siswa Baru')

@section('content')
<div class="space-y-8 w-full pb-20" x-data="{
    showRejectModal: false,
    showPasswordModal: false,
    showPreviewModal: false,
    showDeleteModal: false,
    previewImage: '',
    previewTitle: '',
    deleteTarget: null,
    rejectFormAction: '',
    passwordFormAction: '',
    deleteFormAction: '',
    openRejectModal(registrationId, registrationName) {
        this.deleteTarget = { id: registrationId, name: registrationName };
        this.rejectFormAction = '{{ route('admin.spmb.reject', encode_id($spmb->id)) }}';
        this.showRejectModal = true;
    },
    openPasswordModal(registrationId, registrationName) {
        this.deleteTarget = { id: registrationId, name: registrationName };
        this.passwordFormAction = '{{ route('admin.spmb.reset-password', encode_id($spmb->id)) }}';
        this.showPasswordModal = true;
    },

    openPreview(imageUrl, title) {
        this.previewImage = imageUrl;
        this.previewTitle = title;
        this.showPreviewModal = true;
    },
    confirmDelete(encodedId, name) {
        this.deleteTarget = { name: name };
        this.deleteFormAction = '{{ url('admin/spmb') }}/' + encodedId;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Pendaftaran SPMB', 'message' => 'Apakah Anda yakin ingin menghapus pendaftaran SPMB :name ini? Tindakan ini tidak dapat dibatalkan.'])

    <!-- Reject Modal -->
    <div x-show="showRejectModal"
         x-cloak
         class="fixed inset-0 z-[110] overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showRejectModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-slate-950/60 backdrop-blur-md"
                 @click="showRejectModal = false">
            </div>

            <div x-show="showRejectModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full max-w-lg p-8 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-slate-100">

                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-rose-50 rounded-2xl text-rose-500 border border-rose-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-extrabold text-center text-slate-900 mb-1">Tolak Pendaftaran</h3>
                <p class="text-xs text-center text-slate-500 mb-6 leading-relaxed">Apakah Anda yakin ingin menolak berkas pendaftaran <strong class="text-slate-900" x-text="deleteTarget?.name"></strong>?</p>

                <form :action="rejectFormAction" method="POST">
                    @csrf
                    <div class="mb-6 space-y-2">
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">Alasan Penolakan Berkas <span class="text-rose-500">*</span></label>
                        <textarea name="notes" rows="4" required 
                                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200/90 rounded-2xl text-xs font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition"
                                  placeholder="Tuliskan alasan penolakan secara jelas agar calon pendaftar memahaminya..."></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" @click="showRejectModal = false"
                                class="w-full sm:flex-1 py-3 border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 transition font-extrabold text-xs">
                            Batal
                        </button>
                        <button type="submit"
                                class="w-full sm:flex-1 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-rose-600/20">
                            Tolak Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Reset Modal -->
    <div x-show="showPasswordModal"
         x-cloak
         class="fixed inset-0 z-[110] overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showPasswordModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-slate-950/60 backdrop-blur-md"
                 @click="showPasswordModal = false">
            </div>

            <div x-show="showPasswordModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full max-w-lg p-8 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-slate-100">

                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-indigo-50 text-indigo-600 rounded-2xl border border-indigo-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-extrabold text-center text-slate-900 mb-1">Reset Password Akun</h3>
                <p class="text-xs text-center text-slate-500 mb-6 leading-relaxed">Atur ulang kata sandi login untuk pendaftar <strong class="text-slate-900" x-text="deleteTarget?.name"></strong></p>

                <form :action="passwordFormAction" method="POST">
                    @csrf
                    <div class="mb-6 space-y-2">
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider text-center">Password Baru <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200/90 rounded-2xl text-center font-mono text-base font-extrabold text-indigo-700 tracking-widest focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition"
                               placeholder="********">
                        <p class="text-[10px] font-bold text-slate-400 text-center uppercase tracking-wider italic">Gunakan minimal 8 karakter aman</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" @click="showPasswordModal = false"
                                class="w-full sm:flex-1 py-3 border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 transition font-extrabold text-xs">
                            Batal
                        </button>
                        <button type="submit"
                                class="w-full sm:flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition font-extrabold text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/20">
                            Reset Password Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Document Lightbox Preview Modal -->
    <div x-show="showPreviewModal"
         x-cloak
         class="fixed inset-0 z-[120] overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showPreviewModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-md"
                 @click="showPreviewModal = false">
            </div>

            <div x-show="showPreviewModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative inline-block w-full max-w-4xl p-0 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-slate-100">

                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/60">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2" x-text="previewTitle"></h3>
                    <button type="button" @click="showPreviewModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 bg-slate-950 flex items-center justify-center min-h-[50vh]">
                    <img :src="previewImage" :alt="previewTitle" class="max-w-full max-h-[65vh] object-contain rounded-xl shadow-2xl border border-white/10">
                </div>

                <!-- Modal Footer -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 bg-white border-t border-slate-100">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Peninjauan Dokumen Berkas</span>
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                        <a :href="previewImage" download class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all font-extrabold text-xs flex items-center justify-center space-x-1.5 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download File</span>
                        </a>
                        <button type="button" @click="showPreviewModal = false" class="w-full sm:w-auto px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-extrabold text-xs flex items-center justify-center uppercase tracking-wider">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Bar Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.spmb.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200/90 text-slate-600 hover:text-indigo-600 hover:border-indigo-200 hover:shadow-md transition-all rounded-xl shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Detail Pendaftaran SPMB</h2>
                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Siswa Baru • {{ $spmb->wave?->name ?? 'Gelombang Umum' }}</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('admin.spmb.print', encode_id($spmb->id)) }}" target="_blank"
               class="w-full sm:w-auto px-4 py-2.5 bg-indigo-50 text-indigo-700 border border-indigo-200/80 rounded-xl hover:bg-indigo-600 hover:text-white transition-all font-extrabold text-xs flex items-center justify-center space-x-1.5 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Formulir</span>
            </a>
            <button type="button" @click="confirmDelete('{{ encode_id($spmb->id) }}', '{{ addslashes($spmb->full_name) }}')"
                    class="w-full sm:w-auto px-4 py-2.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl hover:bg-rose-600 hover:text-white transition-all font-extrabold text-xs flex items-center justify-center space-x-1.5 shadow-2xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus Data</span>
            </button>
        </div>

    </div>

    <!-- Status Banner Premium Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 p-4 sm:p-8 lg:p-10 text-white shadow-2xl shadow-indigo-950/20 border border-slate-800/80">
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center font-black text-2xl sm:text-3xl text-indigo-300 shadow-xl shrink-0">
                    {{ strtoupper(substr($spmb->full_name, 0, 1)) }}
                </div>
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-0.5 bg-indigo-500/20 backdrop-blur-md rounded-lg text-[10px] font-extrabold uppercase tracking-wider text-indigo-200 border border-indigo-500/30">
                            {{ $spmb->wave?->name ?? 'Gelombang Umum' }}
                        </span>
                        <span class="text-xs text-indigo-200/60">•</span>
                        <span class="text-xs font-mono font-extrabold text-emerald-300">NO. REG: {{ $spmb->registration_number }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight break-words">{{ $spmb->full_name }}</h1>
                    <p class="text-xs text-slate-300 font-medium">Diajukan pada {{ $spmb->created_at->format('d M Y, H:i') }} WIB ({{ $spmb->created_at->diffForHumans() }})</p>
                </div>
            </div>
            
            <div class="flex flex-col items-start sm:items-end space-y-2 shrink-0 w-full sm:w-auto">
                @php
                    $statusStyles = match($spmb->status) {
                        'accepted' => ['bg' => 'bg-emerald-500/20', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/30', 'label' => 'DITERIMA / LULUS'],
                        'rejected' => ['bg' => 'bg-rose-500/20', 'text' => 'text-rose-300', 'border' => 'border-rose-500/30', 'label' => 'DITOLAK'],
                        'verified' => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-300', 'border' => 'border-blue-500/30', 'label' => 'TERVERIFIKASI'],
                        'submitted' => ['bg' => 'bg-amber-500/20', 'text' => 'text-amber-300', 'border' => 'border-amber-500/30', 'label' => 'PENDING VERIFIKASI'],
                        default => ['bg' => 'bg-slate-500/20', 'text' => 'text-slate-300', 'border' => 'border-slate-500/30', 'label' => strtoupper($spmb->status)]
                    };
                @endphp
                <div class="px-4 py-2 {{ $statusStyles['bg'] }} {{ $statusStyles['text'] }} rounded-2xl border {{ $statusStyles['border'] }} backdrop-blur-md flex items-center space-x-2 shadow-lg w-full sm:w-auto justify-center sm:justify-start">
                    <span class="w-2 h-2 rounded-full {{ str_replace('text-', 'bg-', $statusStyles['text']) }} animate-pulse"></span>
                    <span class="text-xs font-black uppercase tracking-wider">{{ $statusStyles['label'] }}</span>
                </div>
                <p class="text-[10px] text-slate-400 font-semibold sm:text-right w-full">Update Terakhir: {{ $spmb->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content & Sidebar Grid -->
    <div class="grid lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Main Information Sections (8 Columns) -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Card 1: Data Calon Siswa -->
            <div class="premium-card overflow-hidden shadow-sm border-slate-200">
                <div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold text-xs border border-indigo-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h2 class="text-base font-extrabold text-slate-900">Informasi Pribadi Calon Siswa</h2>
                    </div>
                </div>

                <div class="p-4 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Nama Lengkap Siswa</p>
                            <p class="text-base font-extrabold text-slate-900 leading-tight">{{ $spmb->full_name }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Nomor Induk Siswa Nasional (NISN)</p>
                            <p class="text-sm font-mono font-extrabold text-indigo-600">{{ $spmb->nisn ?? '-' }}</p>
                        </div>

                        <div class="space-y-1 pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Nomor Induk Kependudukan (NIK)</p>
                            <p class="text-sm font-mono font-bold text-slate-800">{{ $spmb->nik ?? '-' }}</p>
                        </div>

                        <div class="space-y-1 pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Tempat & Tanggal Lahir</p>
                            <p class="text-sm font-bold text-slate-800">{{ $spmb->birth_place ?? '-' }}, {{ $spmb->birth_date ? $spmb->birth_date->format('d F Y') : '-' }}</p>
                        </div>

                        <div class="space-y-1 pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Jenis Kelamin</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold {{ $spmb->gender === 'male' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                {{ $spmb->gender === 'male' ? 'Laki-Laki' : 'Perempuan' }}
                            </span>
                        </div>

                        <div class="space-y-1 pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Agama</p>
                            <p class="text-sm font-bold text-slate-800">{{ ucfirst($spmb->religion ?? '-') }}</p>
                        </div>

                        <div class="space-y-1 pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Alamat Email Aktif</p>
                            <p class="text-sm font-bold text-slate-800">{{ $spmb->email ?? '-' }}</p>
                        </div>

                        <div class="space-y-1 pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Nomor HP / WhatsApp</p>
                            <p class="text-sm font-mono font-bold text-slate-800">{{ $spmb->phone ?? '-' }}</p>
                        </div>

                        <div class="sm:col-span-2 pt-4 border-t border-slate-100 space-y-1">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Alamat Domisili Lengkap</p>
                            <p class="text-xs font-semibold text-slate-800 leading-relaxed">{{ $spmb->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Data Orang Tua / Wali -->
            <div class="premium-card overflow-hidden shadow-sm border-slate-200">
                <div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold text-xs border border-emerald-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h2 class="text-base font-extrabold text-slate-900">Data Orang Tua / Wali Siswa</h2>
                    </div>
                </div>

                <div class="p-4 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Nama Orang Tua / Wali</p>
                            <p class="text-base font-extrabold text-slate-900">{{ $spmb->parent_name ?? '-' }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Nomor HP / WhatsApp Orang Tua</p>
                            <p class="text-sm font-mono font-extrabold text-emerald-600">{{ $spmb->parent_phone ?? '-' }}</p>
                        </div>

                        <div class="space-y-1 pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Pekerjaan Orang Tua</p>
                            <p class="text-sm font-bold text-slate-800">{{ $spmb->parent_job ?? '-' }}</p>
                        </div>

                        <div class="space-y-1 pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Penghasilan Orang Tua</p>
                            <p class="text-sm font-bold text-slate-800">{{ $spmb->parent_income ?? '-' }}</p>
                        </div>

                        <div class="sm:col-span-2 pt-4 border-t border-slate-100 space-y-1">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Alamat Domisili Orang Tua</p>
                            <p class="text-xs font-semibold text-slate-800 leading-relaxed">{{ $spmb->parent_address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Dokumen Upload & Lampiran Berkas -->
            @if($spmb->documents && $spmb->documents->count() > 0)
            <div class="premium-card overflow-hidden shadow-sm border-slate-200">
                <div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-extrabold text-xs border border-purple-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h2 class="text-base font-extrabold text-slate-900">Berkas Lampiran Pendukung</h2>
                    </div>
                    <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-extrabold border border-purple-200">
                        {{ $spmb->documents->count() }} Berkas
                    </span>
                </div>

                <div class="p-4 sm:p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($spmb->documents as $doc)
                            @php
                                $docSrc = \Illuminate\Support\Str::startsWith($doc->file_path, ['http://', 'https://', 'doc/', 'img/']) ? asset($doc->file_path) : (file_exists(public_path('doc/' . $doc->file_path)) ? asset('doc/' . $doc->file_path) : asset('img/' . $doc->file_path));
                            @endphp
                            <div class="group relative flex flex-col p-3 bg-slate-50 border border-slate-200/90 rounded-2xl transition-all hover:bg-white hover:shadow-lg hover:border-purple-300">
                                @if(str_contains($doc->file_mime, 'image'))
                                    <div class="relative aspect-[4/3] rounded-xl overflow-hidden bg-slate-200 group-hover:cursor-zoom-in"
                                         @click="openPreview('{{ $docSrc }}', '{{ ucfirst(str_replace('_', ' ', $doc->type)) }}')">
                                        <img src="{{ $docSrc }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        <div class="absolute inset-0 bg-slate-950/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <div class="w-9 h-9 bg-white/30 backdrop-blur-md rounded-xl flex items-center justify-center text-white border border-white/40 shadow-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="relative aspect-[4/3] rounded-xl overflow-hidden bg-white border border-slate-200 flex flex-col items-center justify-center space-y-2">
                                        <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                        <p class="text-[10px] font-extrabold text-rose-600 uppercase tracking-wider">Dokumen PDF/FILE</p>
                                    </div>
                                @endif
                                <div class="pt-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-900 uppercase tracking-wider truncate">{{ str_replace('_', ' ', $doc->type) }}</p>
                                        <p class="text-[10px] font-semibold text-slate-400">{{ number_format($doc->file_size / 1024, 0) }} KB</p>
                                    </div>
                                    <a href="{{ $docSrc }}" target="_blank" class="p-2 bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="Unduh / Buka File">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Verification Sidebar & History (4 Columns) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="sticky top-24 space-y-6">
                
                <!-- Payment Status Panel -->
                <div class="premium-card overflow-hidden shadow-sm border-slate-200">
                    <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Status Pembayaran SPMB</h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $spmb->payment_status === 'paid' ? 'bg-green-50 text-green-700 border border-green-200' : ($spmb->payment_status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200 animate-pulse' : 'bg-rose-50 text-rose-700 border border-rose-200') }}">
                            {{ $spmb->payment_status === 'paid' ? 'LUNAS' : ($spmb->payment_status === 'pending' ? 'PENDING' : 'BELUM BAYAR') }}
                        </span>
                    </div>

                    <div class="p-6 space-y-4">
                        @php
                            $manualTx = \App\Models\PaymentTransaction::where('reference_type', 'spmb')
                                ->where('reference_id', $spmb->id)
                                ->latest()
                                ->first();
                        @endphp

                        @if($manualTx)
                            <div class="space-y-2">
                                <div class="flex justify-between py-1 text-xs">
                                    <span class="text-slate-400 font-semibold">Nomor Invoice:</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $manualTx->invoice_number }}</span>
                                </div>
                                <div class="flex justify-between py-1 text-xs border-t border-slate-50">
                                    <span class="text-slate-400 font-semibold">Nominal Tagihan:</span>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($manualTx->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-1 text-xs border-t border-slate-50">
                                    <span class="text-slate-400 font-semibold">Metode:</span>
                                    <span class="font-bold uppercase text-indigo-600">{{ $manualTx->payment_gateway }}</span>
                                </div>
                                @if($manualTx->payment_proof)
                                    <div class="pt-3 border-t border-slate-100">
                                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Bukti Transfer Calon Siswa</p>
                                        <div class="relative rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 group cursor-zoom-in"
                                             @click="openPreview('{{ get_public_file_url($manualTx->payment_proof, 'img/spmb/proofs') }}', 'Bukti Transfer Pendaftaran')">
                                            <img src="{{ get_public_file_url($manualTx->payment_proof, 'img/spmb/proofs') }}" class="w-full max-h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-slate-950/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <div class="w-8 h-8 bg-white/25 backdrop-blur-md rounded-lg flex items-center justify-center text-white border border-white/40 shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-xs text-slate-400 italic">Belum ada transaksi pembayaran yang dicatat.</p>
                            </div>
                        @endif

                        @if($spmb->payment_status !== 'paid')
                            <form action="{{ route('admin.spmb.confirm-payment', encode_id($spmb->id)) }}" method="POST" class="pt-2">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-all font-extrabold text-xs uppercase tracking-wider shadow-md shadow-emerald-600/20 flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                    <span>Konfirmasi Lunas Pembayaran</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Action Verification Panel -->
                @if($spmb->status === 'submitted' || $spmb->status === 'verified')
                <div class="premium-card overflow-hidden shadow-md border-indigo-200/80">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-700 to-indigo-900 text-white flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <h3 class="font-extrabold text-xs uppercase tracking-wider">Aksi Verifikasi Admin</h3>
                        </div>
                        <span class="text-[10px] font-extrabold bg-white/20 px-2 py-0.5 rounded-full uppercase">Action</span>
                    </div>

                    <div class="p-6 space-y-5">
                        @if($spmb->status === 'submitted')
                        <form action="{{ route('admin.spmb.verify', encode_id($spmb->id)) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all font-extrabold text-xs uppercase tracking-wider shadow-md flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Tandai Terverifikasi</span>
                            </button>
                        </form>
                        @endif

                        <!-- Form Penerimaan Siswa Baru -->
                        <form action="{{ route('admin.spmb.accept', encode_id($spmb->id)) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Assign NISN Baru Siswa <span class="text-rose-500">*</span></label>
                                <input type="text" name="nisn" value="{{ old('nisn', $spmb->nisn) }}" required
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-mono font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition"
                                       placeholder="Contoh: 0098765432">
                            </div>
                            <div class="space-y-1.5">
                                <x-major-class-select :required="true" major-label="Pilih Penempatan Jurusan" class-label="Pilih Penempatan Kelas" :selected-major="$spmb->major_id" select-class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition cursor-pointer" label-class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Potongan SPP Bulanan (Rp)</label>
                                <input type="number" name="spp_discount" value="{{ old('spp_discount', $spmb->wave?->spp_discount ?? 0) }}" min="0"
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-bold text-emerald-700 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition"
                                       placeholder="Contoh: 100000">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Keterangan Potongan</label>
                                <input type="text" name="discount_description" value="{{ old('discount_description', $spmb->wave?->spp_discount > 0 ? 'Potongan ' . $spmb->wave->name : '') }}"
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition"
                                       placeholder="Contoh: Potongan Gelombang 1 / Beasiswa Prestasi">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">Catatan Tambahan</label>
                                <textarea name="notes" rows="2" 
                                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition"
                                          placeholder="Catatan hasil verifikasi (opsional)..."></textarea>
                            </div>

                            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl transition-all font-extrabold text-xs uppercase tracking-wider shadow-md flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                <span>Terima Siswa & Buat Akun</span>
                            </button>
                        </form>

                        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row gap-3">
                            <button type="button" @click="openRejectModal({{ $spmb->id }}, '{{ addslashes($spmb->full_name) }}')"
                                    class="w-full sm:flex-1 py-2.5 px-3 bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-600 hover:text-white rounded-xl transition-all font-extrabold text-[11px] uppercase tracking-wider flex items-center justify-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Tolak Pendaftaran</span>
                            </button>
                            <button type="button" @click="openPasswordModal({{ $spmb->id }}, '{{ addslashes($spmb->full_name) }}')"
                                    class="w-full sm:flex-1 py-2.5 px-3 bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-600 hover:text-white rounded-xl transition-all font-extrabold text-[11px] uppercase tracking-wider flex items-center justify-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                <span>Reset Password</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                <!-- History Timeline Card -->
                <div class="premium-card overflow-hidden shadow-sm border-slate-200">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Riwayat Status Berkas</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative space-y-6">
                            <div class="absolute left-3 top-2 bottom-2 w-0.5 bg-slate-100"></div>

                            <!-- Step 1: Submit -->
                            <div class="relative flex items-start space-x-4">
                                <div class="w-6 h-6 bg-emerald-500 text-white rounded-full flex items-center justify-center ring-4 ring-white shadow-sm shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="space-y-0.5 pt-0.5">
                                    <p class="text-xs font-extrabold text-slate-900">Formulir Dikirim</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $spmb->created_at->format('d M Y, H:i') }} WIB</p>
                                </div>
                            </div>

                            <!-- Step 2: Verified -->
                            @if($spmb->verified_at)
                            <div class="relative flex items-start space-x-4">
                                <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center ring-4 ring-white shadow-sm shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="space-y-0.5 pt-0.5">
                                    <p class="text-xs font-extrabold text-slate-900">Diverifikasi Admin</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $spmb->verified_at->format('d M Y, H:i') }} WIB</p>
                                    <p class="text-[10px] text-slate-500 font-bold">Oleh {{ $spmb->verifier->name ?? 'System' }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Step 3: Final Status -->
                            @if($spmb->status === 'accepted' || $spmb->status === 'rejected')
                            <div class="relative flex items-start space-x-4">
                                <div class="w-6 h-6 {{ $spmb->status === 'accepted' ? 'bg-emerald-600' : 'bg-rose-600' }} text-white rounded-full flex items-center justify-center ring-4 ring-white shadow-sm shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="{{ $spmb->status === 'accepted' ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/></svg>
                                </div>
                                <div class="space-y-0.5 pt-0.5">
                                    <p class="text-xs font-extrabold text-slate-900">{{ $spmb->status === 'accepted' ? 'Siswa Diterima' : 'Pendaftaran Ditolak' }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $spmb->updated_at->format('d M Y, H:i') }} WIB</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($spmb->verification_notes)
                <div class="premium-card p-6 bg-gradient-to-br from-indigo-900 to-slate-900 text-white space-y-2 shadow-lg">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-300">Catatan Verifikator</p>
                    <p class="text-xs font-semibold leading-relaxed italic text-indigo-100">"{{ $spmb->verification_notes }}"</p>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
