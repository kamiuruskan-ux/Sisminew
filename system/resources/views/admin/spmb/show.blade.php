@extends('layouts.admin')

@section('title', 'Detail Pendaftaran SPMB - ' . $spmb->registration_number)
@section('page_title', 'Detail Pendaftaran Siswa Baru')

@section('content')
<div class="space-y-8 w-full pb-20" x-data="{
    showRejectModal: false,
    showPasswordModal: false,
    showPreviewModal: false,
    showDeleteModal: false,
    showPhotoModal: false,
    photoPreviewUrl: null,
    onPhotoFileChange(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (evt) => {
                this.photoPreviewUrl = evt.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            this.photoPreviewUrl = null;
        }
    },
    showRevisionModal: false,
    revisionNotes: '',
    setRevisionPreset(text) {
        this.revisionNotes = text;
    },
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
    },

    showWaModal: false,
    waTarget: {
        name: '{{ addslashes($spmb->full_name) }}',
        phone: '{{ $spmb->whatsapp_phone }}',
        phoneDisplay: '{{ $spmb->parent_phone ?: $spmb->phone }}',
        message: {{ json_encode($spmb->whatsapp_message) }},
        originalMessage: {{ json_encode($spmb->whatsapp_message) }}
    },
    openWaModal(name, phone, phoneDisplay, message) {
        if (name) this.waTarget.name = name;
        if (phone) this.waTarget.phone = phone;
        if (phoneDisplay) this.waTarget.phoneDisplay = phoneDisplay;
        if (message) {
            this.waTarget.message = message;
            this.waTarget.originalMessage = message;
        }
        this.showWaModal = true;
    },
    resetWaMessage() {
        this.waTarget.message = this.waTarget.originalMessage;
    },
    sendWaMessage() {
        if (!this.waTarget.phone) return;
        const url = 'https://wa.me/' + this.waTarget.phone + '?text=' + encodeURIComponent(this.waTarget.message);
        window.open(url, '_blank');
        this.showWaModal = false;
    }
}"
@if(session('open_wa_url'))
x-init="setTimeout(() => window.open('{{ session('open_wa_url') }}', '_blank'), 300)"
@endif
>
    @include('components.delete-modal', ['title' => 'Hapus Pendaftaran SPMB', 'message' => 'Apakah Anda yakin ingin menghapus pendaftaran SPMB :name ini? Tindakan ini tidak dapat dibatalkan.'])

    @if(session('quota_warning'))
        <div class="p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border-2 border-amber-400 text-amber-900 dark:text-amber-200 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-black text-lg">
                    !
                </div>
                <div>
                    <h4 class="font-extrabold text-sm text-amber-950 dark:text-amber-100">Peringatan: Kuota Gelombang Penuh</h4>
                    <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5 leading-relaxed">{{ session('quota_warning')['message'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                <a href="{{ request()->fullUrl() }}" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs hover:bg-slate-300 transition">
                    Batalkan
                </a>
                <form action="{{ session('quota_warning')['action'] }}" method="POST">
                    @csrf
                    <input type="hidden" name="override_quota" value="1">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition shadow-md flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        <span>Override & Konfirmasi</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

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

    <!-- Modal Preview & Edit Pesan WhatsApp -->
    <div x-show="showWaModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" @click="showWaModal = false"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-xl transform rounded-3xl bg-white dark:bg-boxdark p-6 sm:p-8 text-left shadow-2xl transition-all border border-slate-200 dark:border-strokedark"
                 @click.away="showWaModal = false">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-strokedark">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900 dark:text-white">Kirim Pesan WhatsApp</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Pratinjau & sesuaikan pesan sebelum membuka WhatsApp</p>
                        </div>
                    </div>
                    <button type="button" @click="showWaModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Recipient Info Card -->
                <div class="mt-4 p-3.5 bg-slate-50 dark:bg-meta-4/40 rounded-2xl border border-slate-100 dark:border-strokedark flex items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="text-slate-500 dark:text-slate-400 font-semibold block text-[11px]">Tujuan Penerima:</span>
                        <span class="font-black text-slate-900 dark:text-white" x-text="waTarget.name"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-500 dark:text-slate-400 font-semibold block text-[11px]">No. WhatsApp:</span>
                        <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="waTarget.phoneDisplay || waTarget.phone"></span>
                    </div>
                </div>

                <!-- Message Textarea -->
                <div class="mt-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Isi Pesan (Bisa diedit langsung):
                        </label>
                        <button type="button" @click="resetWaMessage()" class="text-[11px] text-[#3C50E0] hover:underline font-bold flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Reset ke Draf</span>
                        </button>
                    </div>
                    <textarea x-model="waTarget.message" rows="8"
                              class="w-full p-4 rounded-2xl bg-white dark:bg-boxdark-2 border border-slate-200 dark:border-strokedark text-xs font-medium text-slate-800 dark:text-slate-200 outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 leading-relaxed font-sans"></textarea>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 italic">
                        Tip: Gunakan tanda bintang (*) untuk menebalkan teks, misal: *Teks Tebal*.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-strokedark">
                    <button type="button" @click="showWaModal = false"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-strokedark text-slate-600 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 dark:hover:bg-meta-4 transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="sendWaMessage()"
                            class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/25 flex items-center gap-2 transition-all hover:scale-[1.02] active:scale-95">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Buka WhatsApp ↗</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ganti / Upload Pas Foto Calon Siswa (Admin Fast-Track) -->
    <div x-show="showPhotoModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" @click="showPhotoModal = false"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-lg transform rounded-3xl bg-white p-6 sm:p-8 text-left shadow-2xl transition-all border border-slate-100"
                 @click.away="showPhotoModal = false">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900">Upload / Ganti Pas Foto</h3>
                            <p class="text-xs text-slate-500">Perbarui pas foto pendaftar langsung oleh Admin</p>
                        </div>
                    </div>
                    <button type="button" @click="showPhotoModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form Upload -->
                <form action="{{ route('admin.spmb.update-photo', encode_id($spmb->id)) }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-5">
                    @csrf
                    
                    <!-- Live Image Preview Area -->
                    <div class="flex flex-col items-center justify-center p-5 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                        <template x-if="photoPreviewUrl">
                            <div class="relative w-32 h-40 rounded-xl overflow-hidden shadow-md border-2 border-indigo-500 bg-white mb-3">
                                <img :src="photoPreviewUrl" class="w-full h-full object-cover">
                            </div>
                        </template>
                        <template x-if="!photoPreviewUrl">
                            <div class="w-28 h-36 rounded-xl bg-slate-200 flex flex-col items-center justify-center text-slate-400 border border-slate-300 mb-3">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="text-[9px] font-extrabold mt-1 uppercase tracking-wider">Pas Foto 3x4</span>
                            </div>
                        </template>
                        
                        <label class="cursor-pointer px-4 py-2.5 bg-white border border-slate-300 hover:border-indigo-500 text-indigo-600 hover:bg-indigo-50 rounded-xl font-extrabold text-xs shadow-2xs transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Pilih File Foto Baru</span>
                            <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg,image/webp" required class="hidden" @change="onPhotoFileChange($event)">
                        </label>
                        <p class="text-[11px] text-slate-400 mt-2 text-center leading-relaxed">Format didukung: <strong>JPG, JPEG, PNG, WEBP</strong> (Maks. 3MB).<br>Gunakan rasio 3:4 atau pas foto formal latar biru/merah.</p>
                    </div>

                    <div class="p-3.5 bg-indigo-50/70 border border-indigo-100 rounded-2xl text-[11px] text-indigo-900 leading-relaxed">
                        <strong class="font-bold">Informasi:</strong> Pas foto ini akan langsung menggantikan berkas foto pendaftaran siswa a.n. <strong>{{ $spmb->full_name }}</strong> dan otomatis diterapkan pada Formulir SPMB, Kartu Ujian, dan Cetak Berkas.
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showPhotoModal = false; photoPreviewUrl = null"
                                class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/25 flex items-center gap-2 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Pas Foto</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Minta Revisi Berkas ke Orang Tua -->
    <div x-show="showRevisionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" @click="showRevisionModal = false"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-xl transform rounded-3xl bg-white p-6 sm:p-8 text-left shadow-2xl transition-all border border-slate-100"
                 @click.away="showRevisionModal = false">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base text-slate-900">Minta Perbaikan / Revisi Berkas</h3>
                            <p class="text-xs text-slate-500">Berikan instruksi revisi foto/dokumen untuk wali murid</p>
                        </div>
                    </div>
                    <button type="button" @click="showRevisionModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form Permintaan Revisi -->
                <form action="{{ route('admin.spmb.request-revision', encode_id($spmb->id)) }}" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilihan Cepat Masalah Berkas:</label>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" @click="setRevisionPreset('Pas foto calon siswa belum memenuhi syarat (mohon gunakan pas foto formal rapi/berseragam dengan latar belakang merah atau biru, wajah tampak jelas dan tidak buram).')"
                                    class="px-2.5 py-1 bg-slate-100 hover:bg-amber-100 hover:text-amber-800 text-slate-700 text-[11px] font-bold rounded-lg transition">
                                📷 Pas Foto Tidak Sesuai
                            </button>
                            <button type="button" @click="setRevisionPreset('File Kartu Keluarga (KK) yang diunggah buram / terpotong dan nomor NIK tidak terbaca. Mohon unggah ulang foto/scan KK yang jelas dan utuh.')"
                                    class="px-2.5 py-1 bg-slate-100 hover:bg-amber-100 hover:text-amber-800 text-slate-700 text-[11px] font-bold rounded-lg transition">
                                📄 Kartu Keluarga Buram
                            </button>
                            <button type="button" @click="setRevisionPreset('File Akta Kelahiran tidak terbaca jelas / salah file. Mohon unggah kembali dokumen/scan asli Akta Kelahiran.')"
                                    class="px-2.5 py-1 bg-slate-100 hover:bg-amber-100 hover:text-amber-800 text-slate-700 text-[11px] font-bold rounded-lg transition">
                                📜 Akta Lahir Kurang Jelas
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                            Catatan Instruksi Revisi untuk Wali Murid <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="notes" x-model="revisionNotes" required rows="4"
                                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200/90 rounded-2xl text-xs font-medium text-slate-800 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none transition leading-relaxed"
                                  placeholder="Tuliskan bagian berkas/foto mana yang harus diperbaiki oleh wali murid..."></textarea>
                        <p class="text-[11px] text-slate-400">Catatan ini akan langsung tampil di akun portal SPMB wali murid dan dapat dikirimkan ke WhatsApp wali murid.</p>
                    </div>

                    @if($spmb->whatsapp_phone)
                    <div class="p-3.5 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl flex items-center justify-between gap-3 text-xs">
                        <label class="flex items-center gap-2 text-emerald-900 font-bold cursor-pointer select-none">
                            <input type="checkbox" name="send_wa" value="1" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                            <span>Kirim pemberitahuan revisi ke WhatsApp Wali ({{ $spmb->parent_phone ?: $spmb->phone }})</span>
                        </label>
                    </div>
                    @endif

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showRevisionModal = false"
                                class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-lg shadow-amber-600/25 flex items-center gap-2 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Kirim Permintaan Revisi</span>
                        </button>
                    </div>
                </form>
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
            @if($spmb->whatsapp_phone)
                <button type="button" 
                        @click="openWaModal('{{ addslashes($spmb->full_name) }}', '{{ $spmb->whatsapp_phone }}', '{{ $spmb->parent_phone ?: $spmb->phone }}', {{ json_encode($spmb->whatsapp_message) }})"
                        class="w-full sm:w-auto px-4 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-200/80 rounded-xl hover:bg-emerald-600 hover:text-white transition-all font-extrabold text-xs flex items-center justify-center space-x-1.5 shadow-2xs cursor-pointer">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    <span>Hubungi WhatsApp</span>
                </button>
            @endif
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
                        'need_revision' => ['bg' => 'bg-amber-500/20', 'text' => 'text-amber-300', 'border' => 'border-amber-500/30', 'label' => 'PERLU REVISI BERKAS'],
                        default => ($spmb->status === 'draft' && $spmb->verification_notes) ? ['bg' => 'bg-amber-500/20', 'text' => 'text-amber-300', 'border' => 'border-amber-500/30', 'label' => 'PERLU REVISI BERKAS'] : ['bg' => 'bg-slate-500/20', 'text' => 'text-slate-300', 'border' => 'border-slate-500/30', 'label' => strtoupper($spmb->status)]
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

    @if($spmb->payment_status === 'paid' && $spmb->status === 'draft')
        <!-- Alert Reminder Pengisian Formulir -->
        <div class="p-5 rounded-3xl bg-amber-500/10 border border-amber-500/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-600 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-amber-900">Pembayaran Sudah Divalidasi, Formulir Masih Draft!</h3>
                    <p class="text-xs text-amber-800/80 mt-0.5 font-medium">Orang tua calon siswa telah melunasi biaya pendaftaran namun belum mengirimkan (submit) formulir registrasi dan kelengkapan berkas.</p>
                </div>
            </div>
            @if($spmb->whatsapp_phone)
                <button type="button" 
                        @click="openWaModal('{{ addslashes($spmb->full_name) }}', '{{ $spmb->whatsapp_phone }}', '{{ $spmb->parent_phone ?: $spmb->phone }}', {{ json_encode($spmb->whatsapp_message) }})"
                        class="shrink-0 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-2 transition-all cursor-pointer">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    <span>Ingatkan via WhatsApp</span>
                </button>
            @endif
        </div>
    @endif

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
                            @if($spmb->phone)
                                @php
                                    $studentPhoneClean = \App\Services\WhatsAppService::formatPhoneNumber($spmb->phone);
                                @endphp
                                <button type="button" 
                                        @click="openWaModal('{{ addslashes($spmb->full_name) }}', '{{ $studentPhoneClean }}', '{{ $spmb->phone }}', {{ json_encode($spmb->whatsapp_message) }})"
                                        class="inline-flex items-center gap-1.5 text-sm font-mono font-bold text-emerald-600 hover:text-emerald-700 hover:underline cursor-pointer"
                                        title="Klik untuk Sesuaikan Pesan & Chat WhatsApp">
                                    <svg class="w-3.5 h-3.5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    <span>{{ $spmb->phone }}</span>
                                </button>
                            @else
                                <p class="text-sm font-mono font-bold text-slate-800">-</p>
                            @endif
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
                            @if($spmb->parent_phone)
                                @php
                                    $parentPhoneClean = \App\Services\WhatsAppService::formatPhoneNumber($spmb->parent_phone);
                                @endphp
                                <button type="button" 
                                        @click="openWaModal('{{ addslashes($spmb->parent_name ?: $spmb->full_name) }}', '{{ $parentPhoneClean }}', '{{ $spmb->parent_phone }}', {{ json_encode($spmb->whatsapp_message) }})"
                                        class="inline-flex items-center gap-1.5 text-sm font-mono font-extrabold text-emerald-600 hover:text-emerald-700 hover:underline cursor-pointer"
                                        title="Klik untuk Sesuaikan Pesan & Chat WhatsApp">
                                    <svg class="w-3.5 h-3.5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    <span>{{ $spmb->parent_phone }}</span>
                                </button>
                            @else
                                <p class="text-sm font-mono font-extrabold text-slate-400">-</p>
                            @endif
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

            <!-- Card Khusus: Informasi Tambahan / Kuesioner Formulir SPMB -->
            @php
                try {
                    $allCustomFields = \App\Models\SpmbFormField::ordered()->get();
                } catch (\Throwable $e) {
                    $allCustomFields = collect();
                }
                $hasCustomData = is_array($spmb->custom_fields) && count($spmb->custom_fields) > 0;
            @endphp

            @if($allCustomFields->count() > 0 || $hasCustomData)
            <div class="premium-card overflow-hidden shadow-sm border-slate-200">
                <div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-extrabold text-xs border border-teal-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h2 class="text-base font-extrabold text-slate-900">Data Tambahan &amp; Formulir Khusus</h2>
                    </div>
                    <span class="px-3 py-1 bg-teal-50 text-teal-700 rounded-full text-xs font-extrabold border border-teal-200">
                        {{ $allCustomFields->count() }} Poin Pertanyaan
                    </span>
                </div>

                <div class="p-4 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        @foreach($allCustomFields as $cField)
                            @php
                                $cVal = $spmb->getCustomFieldValue($cField->field_key, '-');
                            @endphp
                            <div class="space-y-1">
                                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">{{ $cField->label }}</p>
                                <p class="text-sm font-bold text-slate-800 leading-relaxed">{{ $cVal }}</p>
                            </div>
                        @endforeach

                        @if(is_array($spmb->custom_fields))
                            @foreach($spmb->custom_fields as $extraKey => $extraVal)
                                @if(!$allCustomFields->contains('field_key', $extraKey))
                                    <div class="space-y-1">
                                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">{{ ucwords(str_replace('_', ' ', $extraKey)) }}</p>
                                        <p class="text-sm font-bold text-slate-800 leading-relaxed">{{ is_array($extraVal) ? implode(', ', $extraVal) : ($extraVal ?: '-') }}</p>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Card 3: Dokumen Upload & Lampiran Berkas -->
            <div class="premium-card overflow-hidden shadow-sm border-slate-200">
                <div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-slate-100 bg-slate-50/50 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-extrabold text-xs border border-purple-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Berkas Lampiran Pendukung</h2>
                            <p class="text-[11px] text-slate-400 font-semibold">{{ $spmb->documents ? $spmb->documents->count() : 0 }} Berkas Terunggah</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="showPhotoModal = true"
                                class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-extrabold flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ ($spmb->documents && $spmb->documents->firstWhere('type', 'photo')) ? 'Ganti Pas Foto' : 'Upload Pas Foto' }}</span>
                        </button>
                        <button type="button" @click="showRevisionModal = true"
                                class="px-3.5 py-2 bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white border border-amber-200/80 rounded-xl text-xs font-extrabold flex items-center gap-1.5 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Minta Revisi ke Wali</span>
                        </button>
                    </div>
                </div>

                <div class="p-4 sm:p-8">
                    @if($spmb->documents && $spmb->documents->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($spmb->documents as $doc)
                            @php
                                $docSrc = \Illuminate\Support\Str::startsWith($doc->file_path, ['http://', 'https://', 'doc/', 'img/']) ? asset($doc->file_path) : (file_exists(public_path('doc/' . $doc->file_path)) ? asset('doc/' . $doc->file_path) : asset('img/' . $doc->file_path));
                                $isPhoto = $doc->type === 'photo';
                            @endphp
                            <div class="group relative flex flex-col p-3 bg-slate-50 border {{ $isPhoto ? 'border-indigo-300 ring-2 ring-indigo-500/10' : 'border-slate-200/90' }} rounded-2xl transition-all hover:bg-white hover:shadow-lg hover:border-purple-300">
                                @if(str_contains($doc->file_mime, 'image'))
                                    <div class="relative aspect-[4/3] rounded-xl overflow-hidden bg-slate-200 group-hover:cursor-zoom-in"
                                         @click="openPreview('{{ $docSrc }}', '{{ ucfirst(str_replace('_', ' ', $doc->type)) }}')">
                                        <img src="{{ $docSrc }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        <div class="absolute inset-0 bg-slate-950/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <div class="w-9 h-9 bg-white/30 backdrop-blur-md rounded-xl flex items-center justify-center text-white border border-white/40 shadow-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </div>
                                        </div>
                                        @if($isPhoto)
                                            <span class="absolute top-2 left-2 px-2 py-0.5 bg-indigo-600 text-white font-extrabold text-[9px] uppercase tracking-wider rounded-md shadow-sm">
                                                Pas Foto Resmi
                                            </span>
                                        @endif
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
                                    <div class="min-w-0 pr-2">
                                        <p class="text-xs font-extrabold text-slate-900 uppercase tracking-wider truncate">{{ str_replace('_', ' ', $doc->type) }}</p>
                                        <p class="text-[10px] font-semibold text-slate-400">{{ number_format($doc->file_size / 1024, 0) }} KB</p>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        @if($isPhoto)
                                            <button type="button" @click="showPhotoModal = true" class="p-2 bg-indigo-50 border border-indigo-200 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl transition-all" title="Ganti Pas Foto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </button>
                                        @endif
                                        <a href="{{ $docSrc }}" target="_blank" class="p-2 bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="Unduh / Buka File">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="p-8 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-sm text-slate-800">Belum Ada Berkas yang Diunggah</h4>
                            <p class="text-xs text-slate-500 max-w-md mx-auto mt-0.5">Wali murid belum mengunggah dokumen persyaratan, atau Anda dapat langsung mengunggah pas foto calon siswa sekarang (Fast-Track).</p>
                        </div>
                        <div class="pt-2 flex flex-wrap justify-center gap-2">
                            <button type="button" @click="showPhotoModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-extrabold shadow-sm transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Upload Pas Foto Sekarang</span>
                            </button>
                            <button type="button" @click="showRevisionModal = true" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-extrabold shadow-sm transition flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Minta Berkas ke Wali</span>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
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
                            // Ambil transaksi yang memiliki bukti transfer terlebih dahulu
                            $manualTx = \App\Models\PaymentTransaction::where('reference_type', 'spmb')
                                ->where('reference_id', $spmb->id)
                                ->whereNotNull('payment_proof')
                                ->latest()
                                ->first();

                            // Fallback jika belum ada transaksi yang kolom payment_proof-nya terisi
                            if (!$manualTx) {
                                $manualTx = \App\Models\PaymentTransaction::where('reference_type', 'spmb')
                                    ->where('reference_id', $spmb->id)
                                    ->latest()
                                    ->first();
                            }

                            // Dapatkan bukti transfer (dari transaksi atau dari tabel pendaftaran siswa)
                            $proofImage = $manualTx?->payment_proof ?? $spmb->active_payment_proof ?? $spmb->payment_proof;
                        @endphp

                        @if($manualTx || $proofImage)
                            <div class="space-y-2">
                                <div class="flex justify-between py-1 text-xs">
                                    <span class="text-slate-400 font-semibold">Nomor Invoice:</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $manualTx?->invoice_number ?? ('INV-SPMB-' . $spmb->id) }}</span>
                                </div>
                                <div class="flex justify-between py-1 text-xs border-t border-slate-50">
                                    <span class="text-slate-400 font-semibold">Nominal Tagihan:</span>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($manualTx?->amount ?? ($spmb->wave?->registration_fee ?? 0), 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-1 text-xs border-t border-slate-50">
                                    <span class="text-slate-400 font-semibold">Metode:</span>
                                    <span class="font-bold uppercase text-indigo-600">{{ $manualTx?->payment_gateway ?? 'MANUAL' }}</span>
                                </div>
                                @if($proofImage)
                                    <div class="pt-3 border-t border-slate-100">
                                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Bukti Transfer Calon Siswa</p>
                                        <div class="relative rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 group cursor-zoom-in"
                                             @click="openPreview('{{ get_public_file_url($proofImage, 'img/spmb/proofs') }}', 'Bukti Transfer Pendaftaran')">
                                            <img src="{{ get_public_file_url($proofImage, 'img/spmb/proofs') }}" class="w-full max-h-48 object-cover group-hover:scale-105 transition-transform duration-300">
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
                            @php
                                $isWaveFull = $spmb->wave && $spmb->wave->isQuotaFull() && $spmb->payment_status !== 'pending';
                            @endphp
                            @if($isWaveFull)
                                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-[11px] leading-relaxed">
                                    <strong>Peringatan Kuota:</strong> Kuota gelombang ini ({{ $spmb->wave->quota }} siswa) telah penuh. Mengonfirmasi pembayaran ini akan menggunakan izin kuota berlebih (override).
                                </div>
                            @endif
                            <form action="{{ route('admin.spmb.confirm-payment', encode_id($spmb->id)) }}" method="POST" class="pt-2">
                                @csrf
                                @if($isWaveFull)
                                    <input type="hidden" name="override_quota" value="1">
                                @endif
                                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-all font-extrabold text-xs uppercase tracking-wider shadow-md shadow-emerald-600/20 flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                    <span>Konfirmasi Lunas Pembayaran</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Action Verification Panel -->
                @if($spmb->status === 'submitted' || $spmb->status === 'verified' || $spmb->status === 'need_revision')
                <div class="premium-card overflow-hidden shadow-md border-indigo-200/80">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-700 to-indigo-900 text-white flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <h3 class="font-extrabold text-xs uppercase tracking-wider">Aksi Verifikasi Admin</h3>
                        </div>
                        <span class="text-[10px] font-extrabold bg-white/20 px-2 py-0.5 rounded-full uppercase">Action</span>
                    </div>

                    <div class="p-6 space-y-5">
                        @if($spmb->status === 'submitted' || $spmb->status === 'need_revision')
                        <form action="{{ route('admin.spmb.verify', encode_id($spmb->id)) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all font-extrabold text-xs uppercase tracking-wider shadow-md flex items-center justify-center space-x-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Tandai Terverifikasi</span>
                            </button>
                        </form>
                        @endif

                        @if($spmb->status === 'need_revision')
                        <div class="p-3.5 bg-amber-500/10 border border-amber-300 rounded-2xl text-amber-900 text-xs space-y-1">
                            <p class="font-extrabold flex items-center gap-1.5 text-amber-800">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Menunggu Perbaikan dari Wali Murid
                            </p>
                            <p class="text-[11px] text-amber-700 italic">"{{ $spmb->verification_notes }}"</p>
                        </div>
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

                        <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row gap-2.5">
                            <button type="button" @click="showRevisionModal = true"
                                    class="w-full sm:flex-1 py-2.5 px-2 bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-600 hover:text-white rounded-xl transition-all font-extrabold text-[11px] uppercase tracking-wider flex items-center justify-center space-x-1 cursor-pointer"
                                    title="Minta perbaikan berkas ke wali murid">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Minta Revisi</span>
                            </button>
                            <button type="button" @click="openRejectModal({{ $spmb->id }}, '{{ addslashes($spmb->full_name) }}')"
                                    class="w-full sm:flex-1 py-2.5 px-2 bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-600 hover:text-white rounded-xl transition-all font-extrabold text-[11px] uppercase tracking-wider flex items-center justify-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Tolak</span>
                            </button>
                            <button type="button" @click="openPasswordModal({{ $spmb->id }}, '{{ addslashes($spmb->full_name) }}')"
                                    class="w-full sm:flex-1 py-2.5 px-2 bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-600 hover:text-white rounded-xl transition-all font-extrabold text-[11px] uppercase tracking-wider flex items-center justify-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                <span>Password</span>
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

                            @if($spmb->status === 'need_revision')
                            <div class="relative flex items-start space-x-4">
                                <div class="w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center ring-4 ring-white shadow-sm shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div class="space-y-0.5 pt-0.5">
                                    <p class="text-xs font-extrabold text-amber-600">Perbaikan Berkas Diminta</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $spmb->updated_at->format('d M Y, H:i') }} WIB</p>
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
