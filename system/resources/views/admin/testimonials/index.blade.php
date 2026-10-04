@extends('layouts.admin')

@section('title', 'Kelola Testimoni')
@section('page_title', 'Testimoni Landing Page')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    showModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    modalTitle: 'Tambah Testimoni',
    submitButtonText: 'Simpan Testimoni',
    formAction: '{{ route('admin.testimonials.store') }}',
    formMethod: 'POST',
    previewUrl: null,
    formData: {
        id: null,
        name: '',
        role: 'Orang Tua Siswa',
        title: '',
        content: '',
        rating: 5,
        order: 1,
        is_active: true,
        current_avatar: ''
    },
    openCreateModal() {
        this.formAction = '{{ route('admin.testimonials.store') }}';
        this.formMethod = 'POST';
        this.modalTitle = 'Tambah Testimoni Baru';
        this.submitButtonText = 'Simpan Testimoni';
        this.previewUrl = null;
        this.formData = {
            id: null,
            name: '',
            role: 'Orang Tua Siswa',
            title: '',
            content: '',
            rating: 5,
            order: {{ (int) ($testimonials->max('order') + 1) }},
            is_active: true,
            current_avatar: ''
        };
        const fileInput = document.getElementById('testiAvatarInput');
        if (fileInput) fileInput.value = '';
        this.showModal = true;
    },
    openEditModal(item) {
        this.formAction = '{{ url('admin/testimonials') }}/' + item.encoded_id;
        this.formMethod = 'PUT';
        this.modalTitle = 'Edit Testimoni';
        this.submitButtonText = 'Perbarui Testimoni';
        this.previewUrl = item.avatar_url || null;
        this.formData = {
            id: item.encoded_id,
            name: item.name || '',
            role: item.role || 'Orang Tua Siswa',
            title: item.title || '',
            content: item.content || '',
            rating: item.rating || 5,
            order: item.order || 0,
            is_active: Boolean(item.is_active),
            current_avatar: item.avatar || ''
        };
        const fileInput = document.getElementById('testiAvatarInput');
        if (fileInput) fileInput.value = '';
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
        this.previewUrl = null;
    },
    confirmDelete(encodedId, name) {
        this.deleteTarget = { id: encodedId, name: name || 'Testimoni' };
        this.deleteFormAction = '{{ url('admin/testimonials') }}/' + encodedId;
        this.showDeleteModal = true;
    },
    handleAvatarChange(event) {
        const file = event.target.files[0];
        if (file) {
            this.previewUrl = URL.createObjectURL(file);
        }
    }
}">

    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Testimoni Website</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola ulasan & kesan pesan orang tua, alumni, atau siswa yang tampil di beranda website sekolah.</p>
                </div>
            </div>
        </div>

        <button type="button" @click="openCreateModal()" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-500/20 transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Testimoni</span>
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Testimonial Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($testimonials as $item)
            @php
                $itemEncodedId = encode_id($item->id);
                $jsonData = [
                    'id' => $item->id,
                    'encoded_id' => $itemEncodedId,
                    'name' => $item->name,
                    'role' => $item->role,
                    'title' => $item->title,
                    'content' => $item->content,
                    'rating' => (int) $item->rating,
                    'order' => (int) $item->order,
                    'is_active' => (bool) $item->is_active,
                    'avatar' => $item->avatar,
                    'avatar_url' => $item->avatar_url,
                ];
            @endphp
            <div class="group bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md transition-all flex flex-col justify-between relative overflow-hidden">
                <!-- Status & Order Badges -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 text-[11px] font-extrabold rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800">
                                {{ $item->role }}
                            </span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 dark:bg-slate-800 text-slate-500">
                                Urutan: #{{ $item->order }}
                            </span>
                        </div>

                        <!-- Active Toggle Indicator -->
                        <form action="{{ route('admin.testimonials.toggle-active', $itemEncodedId) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold transition-all cursor-pointer {{ $item->is_active ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 border border-slate-200 dark:border-slate-700' }}" title="Klik untuk mengubah status aktif">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                <span>{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Rating Stars -->
                    <div class="flex items-center gap-1 text-amber-400 mb-3">
                        @for($i=1; $i<=5; $i++)
                            @if($i <= $item->rating)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-slate-200 dark:text-slate-700 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endif
                        @endfor
                    </div>

                    <!-- Testimony Text -->
                    <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed mb-6 italic">
                        "{{ $item->content }}"
                    </p>
                </div>

                <!-- Author Info & Action Buttons -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="{{ $item->avatar_url }}" alt="{{ $item->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                        <div class="min-w-0">
                            <h4 class="text-sm font-extrabold text-slate-900 dark:text-white truncate leading-snug">{{ $item->name }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ $item->title ?: $item->role }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" @click='openEditModal(@json($jsonData))' class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors cursor-pointer" title="Edit Testimoni">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button type="button" @click="confirmDelete('{{ $itemEncodedId }}', '{{ addslashes($item->name) }}')" class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-600 hover:text-white transition-colors cursor-pointer" title="Hapus Testimoni">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200/80 dark:border-slate-800">
                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Belum Ada Testimoni</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">Tambahkan testimoni pertama untuk ditampilkan di halaman beranda website.</p>
                <button type="button" @click="openCreateModal()" class="mt-5 px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-500/20 transition-all cursor-pointer">
                    Tambah Testimoni Sekarang
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal Form Tambah / Edit Testimoni -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full border border-slate-200/80 dark:border-slate-800">
                <form :action="formAction" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" :value="formMethod">

                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white" x-text="modalTitle"></h3>
                        <button type="button" @click="closeModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" x-model="formData.name" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="Contoh: Ibu Ratna Sari, M.Pd">
                        </div>

                        <!-- Grid Peran & Keterangan Tambahan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Kategori / Peran <span class="text-rose-500">*</span></label>
                                <select name="role" x-model="formData.role" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="Orang Tua Siswa">Orang Tua Siswa</option>
                                    <option value="Alumni">Alumni</option>
                                    <option value="Siswa">Siswa</option>
                                    <option value="Komite Sekolah">Komite Sekolah</option>
                                    <option value="Tokoh Masyarakat">Tokoh Masyarakat</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Keterangan / Kelas / Profesi</label>
                                <input type="text" name="title" x-model="formData.title" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="Contoh: Orang Tua Siswa Kelas 3">
                            </div>
                        </div>

                        <!-- Rating & Urutan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Rating Bintang <span class="text-rose-500">*</span></label>
                                <select name="rating" x-model="formData.rating" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                    <option value="5">⭐⭐⭐⭐⭐ (5 Bintang)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 Bintang)</option>
                                    <option value="3">⭐⭐⭐ (3 Bintang)</option>
                                    <option value="2">⭐⭐ (2 Bintang)</option>
                                    <option value="1">⭐ (1 Bintang)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Urutan Tampil</label>
                                <input type="number" name="order" x-model="formData.order" min="0" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            </div>
                        </div>

                        <!-- Isi Testimoni -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Isi Kesan & Pesan Testimoni <span class="text-rose-500">*</span></label>
                            <textarea name="content" x-model="formData.content" rows="4" required class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 leading-relaxed" placeholder="Tuliskan pengalaman atau kesan terhadap sekolah..."></textarea>
                        </div>

                        <!-- Foto Profil / Avatar -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Foto Profil / Avatar (Opsional)</label>
                            <div class="flex items-center gap-4">
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" class="w-12 h-12 rounded-full object-cover border border-slate-200 shrink-0">
                                </template>
                                <div class="flex-1">
                                    <input type="file" id="testiAvatarInput" name="avatar" accept="image/*" @change="handleAvatarChange($event)" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-slate-300 hover:file:bg-slate-200 cursor-pointer">
                                    <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maks 5MB. Kosongkan jika ingin menggunakan inisial nama otomatis.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Status Tampil -->
                        <div class="pt-2">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" :checked="formData.is_active" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">Tampilkan di Halaman Beranda Website</span>
                            </label>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                        <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 font-extrabold text-xs hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs shadow-md shadow-indigo-500/20 transition-all" x-text="submitButtonText">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" @click="showDeleteModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-200/80 dark:border-slate-800 p-6">
                <form :action="deleteFormAction" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>

                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white text-center">Hapus Testimoni?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 text-center mt-1">
                        Apakah Anda yakin ingin menghapus testimoni dari <span class="font-bold text-slate-800 dark:text-slate-200" x-text="deleteTarget?.name"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="mt-6 flex items-center justify-center gap-3">
                        <button type="button" @click="showDeleteModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-xs hover:bg-slate-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md shadow-rose-600/20 transition-all">
                            Ya, Hapus Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
