@extends('layouts.admin')

@section('title', 'Galeri Dokumentasi')
@section('page_title', 'Galeri Kegiatan Sekolah')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    showModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    modalTitle: 'Tambah Foto Galeri',
    submitButtonText: 'Simpan Foto Galeri',
    formAction: '{{ route('admin.gallery.store') }}',
    formMethod: 'POST',
    previewUrl: null,
    formData: {
        id: null,
        title: '',
        category_id: '',
        event_date: '',
        description: '',
        order: 0,
        is_active: true,
        current_image: ''
    },
    openCreateModal() {
        this.formAction = '{{ route('admin.gallery.store') }}';
        this.formMethod = 'POST';
        this.modalTitle = 'Tambah Foto Galeri';
        this.submitButtonText = 'Simpan Foto Galeri';
        this.previewUrl = null;
        this.formData = {
            id: null,
            title: '',
            category_id: '',
            event_date: '{{ date('Y-m-d') }}',
            description: '',
            order: 0,
            is_active: true,
            current_image: ''
        };
        const fileInput = document.getElementById('galleryImageInput');
        if (fileInput) fileInput.value = '';
        this.showModal = true;
    },
    openEditModal(gallery) {
        this.formAction = '{{ url('admin/gallery') }}/' + gallery.id;
        this.formMethod = 'PUT';
        this.modalTitle = 'Edit Foto Galeri';
        this.submitButtonText = 'Perbarui Galeri';
        this.previewUrl = gallery.image_url || null;
        this.formData = {
            id: gallery.id,
            title: gallery.title || '',
            category_id: gallery.category_id || '',
            event_date: gallery.event_date || '',
            description: gallery.description || '',
            order: gallery.order || 0,
            is_active: Boolean(gallery.is_active),
            current_image: gallery.image || ''
        };
        const fileInput = document.getElementById('galleryImageInput');
        if (fileInput) fileInput.value = '';
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
        this.previewUrl = null;
    },
    confirmDelete(galleryId, galleryTitle) {
        this.deleteTarget = { id: galleryId, name: galleryTitle || 'Galeri #' + galleryId };
        this.deleteFormAction = '{{ url('admin/gallery') }}/' + galleryId;
        this.showDeleteModal = true;
    },
    handleImageChange(event) {
        const file = event.target.files[0];
        if (file) {
            this.previewUrl = URL.createObjectURL(file);
        }
    }
}">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Galeri Kegiatan</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola dokumentasi foto album kegiatan, acara, dan prestasi sekolah.</p>
        </div>
        @permission('create-gallery')
        <div>
            <button type="button" @click="openCreateModal()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Foto Galeri</span>
            </button>
        </div>
        @endpermission
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Album Galeri</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($galleries->total()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dokumentasi Tahun Ini</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1 tracking-tight">{{ number_format($galleries->where('event_date.year', date('Y'))->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Gallery Grid Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Galeri & Dokumentasi</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $galleries->firstItem() ?? 0 }} - {{ $galleries->lastItem() ?? 0 }} dari {{ $galleries->total() }} album</span>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @forelse($galleries as $gallery)
                    <div class="group bg-white rounded-xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-md transition-all">
                        <div class="relative overflow-hidden aspect-4/3 bg-slate-100 border-b border-slate-100">
                            @if($gallery->image_url)
                                <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            @if($gallery->category)
                                <div class="absolute top-2.5 left-2.5">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-900/80 text-white backdrop-blur-xs">
                                        {{ $gallery->category->name }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute top-2.5 right-2.5">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $gallery->is_active ? 'bg-emerald-600/90 text-white' : 'bg-rose-600/90 text-white' }} backdrop-blur-xs">
                                    {{ $gallery->is_active ? 'Aktif' : 'Sembunyi' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-slate-900 text-xs truncate">{{ $gallery->title }}</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $gallery->event_date?->format('d M Y') ?? '-' }}</p>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center space-x-2">
                                @permission('edit-gallery')
                                <button type="button"
                                        @click="openEditModal({
                                            id: {{ $gallery->id }},
                                            title: '{{ addslashes($gallery->title) }}',
                                            category_id: '{{ $gallery->category_id ?? '' }}',
                                            event_date: '{{ $gallery->event_date?->format('Y-m-d') ?? '' }}',
                                            description: '{{ addslashes($gallery->description ?? '') }}',
                                            order: {{ (int) $gallery->order }},
                                            is_active: {{ $gallery->is_active ? 1 : 0 }},
                                            image: '{{ $gallery->image }}',
                                            image_url: '{{ $gallery->image_url ?? '' }}'
                                        })"
                                        class="flex-1 py-1.5 text-center bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg border border-slate-200 transition cursor-pointer">
                                    Edit
                                </button>
                                @endpermission
                                @permission('delete-gallery')
                                <button type="button" @click="confirmDelete({{ $gallery->id }}, '{{ addslashes($gallery->title) }}')"
                                        class="flex-1 py-1.5 text-center bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-xs rounded-lg border border-rose-200 transition cursor-pointer">
                                    Hapus
                                </button>
                                @endpermission
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs text-slate-500 font-medium">Belum ada foto galeri yang diunggah.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @if($galleries->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $galleries->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Gallery Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="closeModal()"></div>

            <div x-show="showModal" x-transition class="relative inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white border border-slate-200 shadow-xl rounded-2xl">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900" x-text="modalTitle">Tambah Foto Galeri</h3>
                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="formAction" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <template x-if="formMethod === 'PUT'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Image Upload with Live Preview -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Foto Dokumentasi <span class="text-rose-500" x-show="formMethod === 'POST'">*</span>
                        </label>
                        <div class="relative group">
                            <div class="w-full aspect-16/9 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden transition-colors group-hover:border-indigo-400 relative">
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!previewUrl">
                                    <div class="text-center p-4">
                                        <svg class="w-8 h-8 mx-auto text-slate-300 mb-1.5 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-xs font-semibold text-slate-600">Klik untuk memilih foto</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Format: JPEG, PNG, JPG, WEBP, SVG (Maks 10MB)</p>
                                    </div>
                                </template>
                            </div>
                            <input type="file" name="image" id="galleryImageInput" accept="image/*" :required="formMethod === 'POST'" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleImageChange">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1" x-show="formMethod === 'PUT'">Biarkan kosong jika tidak ingin mengubah foto yang ada.</p>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Foto / Kegiatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-bold" placeholder="Contoh: Upacara Bendera HUT RI">
                    </div>

                    <!-- Category & Event Date -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori Galeri</label>
                            <select name="category_id" x-model="formData.category_id" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-semibold">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Kegiatan</label>
                            <input type="date" name="event_date" x-model="formData.event_date" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan / Deskripsi</label>
                        <textarea name="description" x-model="formData.description" rows="2" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none" placeholder="Deskripsi singkat dokumentasi..."></textarea>
                    </div>

                    <!-- Status Active Switch -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">Tampilkan di Halaman Publik</span>
                            <span class="block text-[10px] text-slate-500">Jika diaktifkan, foto akan muncul di halaman /galeri</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="formData.is_active" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <!-- Form Footer -->
                    <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
                        <button type="button" @click="closeModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer" x-text="submitButtonText">
                            Simpan Foto Galeri
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl p-6 max-w-sm w-full shadow-xl border border-slate-200 text-center" @click.away="showDeleteModal = false">
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-rose-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Hapus Dokumentasi Galeri?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">Yakin ingin menghapus foto <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex space-x-2">
                <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition cursor-pointer">
                    Batal
                </button>
                <form :action="deleteFormAction" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
