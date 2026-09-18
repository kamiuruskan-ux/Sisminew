@extends('layouts.admin')

@section('title', 'Slider Homepage')
@section('page_title', 'Hero Slider Website')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    showModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    modalTitle: 'Tambah Banner Slider',
    submitButtonText: 'Simpan Slider',
    formAction: '{{ route('admin.sliders.store') }}',
    formMethod: 'POST',
    previewUrl: null,
    formData: {
        id: null,
        title: '',
        description: '',
        link: '',
        link_text: 'Selengkapnya',
        order: 0,
        is_active: true,
        current_image: ''
    },
    openCreateModal() {
        this.formAction = '{{ route('admin.sliders.store') }}';
        this.formMethod = 'POST';
        this.modalTitle = 'Tambah Banner Hero Slider';
        this.submitButtonText = 'Simpan Slider';
        this.previewUrl = null;
        this.formData = {
            id: null,
            title: '',
            description: '',
            link: '',
            link_text: 'Selengkapnya',
            order: {{ (int) ($sliders->max('order') + 1) }},
            is_active: true,
            current_image: ''
        };
        const fileInput = document.getElementById('sliderImageInput');
        if (fileInput) fileInput.value = '';
        this.showModal = true;
    },
    openEditModal(slider) {
        this.formAction = '{{ url('admin/sliders') }}/' + slider.id;
        this.formMethod = 'PUT';
        this.modalTitle = 'Edit Banner Hero Slider';
        this.submitButtonText = 'Perbarui Slider';
        this.previewUrl = slider.image_url || null;
        this.formData = {
            id: slider.id,
            title: slider.title || '',
            description: slider.description || '',
            link: slider.link || '',
            link_text: slider.link_text || 'Selengkapnya',
            order: slider.order || 0,
            is_active: Boolean(slider.is_active),
            current_image: slider.image || ''
        };
        const fileInput = document.getElementById('sliderImageInput');
        if (fileInput) fileInput.value = '';
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
        this.previewUrl = null;
    },
    confirmDelete(sliderId, sliderTitle) {
        this.deleteTarget = { id: sliderId, name: sliderTitle || 'Slider #' + sliderId };
        this.deleteFormAction = '{{ url('admin/sliders') }}/' + sliderId;
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
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Hero Slider Homepage</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola banner gambar slider utama yang tampil di halaman depan website sekolah.</p>
        </div>
        <div>
            <button type="button" @click="openCreateModal()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Slider Baru</span>
            </button>
        </div>
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Banner Slider</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($sliders->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Slider Aktif</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1 tracking-tight">{{ number_format($sliders->where('is_active', true)->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nonaktif</p>
                <p class="text-2xl font-bold text-slate-600 mt-1 tracking-tight">{{ number_format($sliders->where('is_active', false)->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-200 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Sliders Grid Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Banner Slider</h3>
            <span class="text-xs text-slate-500">Urutan tampil berdasarkan nomor urutan (order)</span>
        </div>

        <div class="p-5">
            @if($sliders->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($sliders as $slider)
                        <div class="group bg-white rounded-xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-md transition-all">
                            <div class="relative overflow-hidden aspect-video bg-slate-100 border-b border-slate-100">
                                @if($slider->image)
                                    <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="absolute top-2.5 right-2.5">
                                    @if($slider->is_active)
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded bg-slate-100 text-slate-600 border border-slate-200 shadow-xs">
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                                <div class="absolute top-2.5 left-2.5">
                                    <span class="px-2.5 py-0.5 text-[10px] font-mono font-bold rounded bg-slate-900/80 text-white backdrop-blur-xs shadow-xs">
                                        Order #{{ $slider->order }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-slate-900 text-xs truncate">{{ $slider->title }}</h3>
                                <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $slider->description ?? 'Tanpa deskripsi' }}</p>
                                
                                @if($slider->link)
                                    <div class="mt-2.5">
                                        <a href="{{ \Illuminate\Support\Str::startsWith($slider->link, ['http://', 'https://', '#', '/']) ? $slider->link : url($slider->link) }}" target="_blank" class="inline-flex items-center space-x-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-800">
                                            <span>{{ $slider->link_text ?? 'Kunjungi Link' }}</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endif

                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center space-x-2">
                                    <button type="button"
                                            @click="openEditModal({
                                                id: {{ $slider->id }},
                                                title: '{{ addslashes($slider->title) }}',
                                                description: '{{ addslashes($slider->description ?? '') }}',
                                                link: '{{ addslashes($slider->link ?? '') }}',
                                                link_text: '{{ addslashes($slider->link_text ?? 'Selengkapnya') }}',
                                                order: {{ (int) $slider->order }},
                                                is_active: {{ $slider->is_active ? 1 : 0 }},
                                                image: '{{ $slider->image }}',
                                                image_url: '{{ $slider->image_url }}'
                                            })"
                                            class="flex-1 py-1.5 text-center bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg border border-slate-200 transition cursor-pointer">
                                        Edit
                                    </button>
                                    <button type="button" @click="confirmDelete({{ $slider->id }}, '{{ addslashes($slider->title) }}')"
                                            class="flex-1 py-1.5 text-center bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-xs rounded-lg border border-rose-200 transition cursor-pointer">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-xs text-slate-500 font-medium">Belum ada banner slider yang dibuat.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Slider Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="closeModal()"></div>

            <div x-show="showModal" x-transition class="relative inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white border border-slate-200 shadow-xl rounded-2xl">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900" x-text="modalTitle">Tambah Banner Slider</h3>
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
                            Gambar Banner Slider <span class="text-rose-500" x-show="formMethod === 'POST'">*</span>
                        </label>
                        <div class="relative group">
                            <div class="w-full aspect-video bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden transition-colors group-hover:border-indigo-400 relative">
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!previewUrl">
                                    <div class="text-center p-4">
                                        <svg class="w-8 h-8 mx-auto text-slate-300 mb-1.5 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-xs font-semibold text-slate-600">Klik untuk memilih gambar banner</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Format: JPEG, PNG, JPG, WEBP, SVG (Maks 5MB) • 1920x600 px</p>
                                    </div>
                                </template>
                            </div>
                            <input type="file" name="image" id="sliderImageInput" accept="image/*" :required="formMethod === 'POST'" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleImageChange">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1" x-show="formMethod === 'PUT'">Biarkan kosong jika tidak ingin mengubah gambar banner.</p>
                    </div>

                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Banner Slider <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-bold" placeholder="Contoh: Selamat Datang di Website Resmi Sekolah">
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi / Sub-Header</label>
                        <textarea name="description" x-model="formData.description" rows="2" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none" placeholder="Deskripsi singkat yang tampil di atas banner..."></textarea>
                    </div>

                    <!-- Link URL & Link Text -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">URL Link Tujuan</label>
                            <input type="text" name="link" x-model="formData.link" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500" placeholder="https://... atau /spmb">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Teks Tombol</label>
                            <input type="text" name="link_text" x-model="formData.link_text" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500" placeholder="Selengkapnya">
                        </div>
                    </div>

                    <!-- Order & Status -->
                    <div class="grid grid-cols-2 gap-4 items-center">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan (Order)</label>
                            <input type="number" name="order" x-model="formData.order" min="0" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div class="pt-5 flex items-center space-x-2">
                            <input type="checkbox" name="is_active" value="1" id="slider_is_active" :checked="formData.is_active" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                            <label for="slider_is_active" class="text-xs font-bold text-slate-700 cursor-pointer">Status Slider Aktif</label>
                        </div>
                    </div>

                    <!-- Form Footer -->
                    <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
                        <button type="button" @click="closeModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer" x-text="submitButtonText">
                            Simpan Slider
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
            <h3 class="text-base font-bold text-slate-900">Hapus Slider Homepage?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">Yakin ingin menghapus slider <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
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
