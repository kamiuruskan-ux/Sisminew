@extends('layouts.admin')

@section('title', 'Tambah Postingan')
@section('page_title', 'Tambah Postingan Baru')

@section('content')
<div class="w-full space-y-6" x-data="{
    showSlugInput: false,
    slug: '',
    title: '{{ old('title') }}',
    autoSlug() {
        if (!this.showSlugInput && this.title) {
            this.slug = this.title.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.querySelector('input[name=slug]').value = this.slug;
        }
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.posts.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Tambah Artikel Berita Baru</h1>
                <p class="text-xs text-slate-500 mt-0.5">Buat konten berita, artikel, atau publikasi sekolah.</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" onclick="saveDraft()" class="px-4 py-2 text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition font-semibold text-xs shadow-xs">
                Simpan Konsep (Draft)
            </button>
            <button type="submit" form="postForm" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition font-bold text-xs shadow-xs">
                Publikasikan Artikel
            </button>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-xs">
            <div class="flex items-center space-x-2 text-rose-800 font-bold mb-2">
                <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Terdapat beberapa kesalahan pengisian form:</span>
            </div>
            <ul class="list-disc list-inside text-rose-700 space-y-1 ml-1 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Form -->
    <form id="postForm" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title & Slug Card -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-4 shadow-xs">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Judul Postingan / Berita <span class="text-rose-500">*</span></label>
                        <input type="text"
                               name="title"
                               value="{{ old('title') }}"
                               @input="autoSlug()"
                               class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-base font-bold rounded-xl p-3 focus:bg-white focus:ring-1 focus:ring-indigo-500"
                               placeholder="Masukkan judul berita utama..."
                               required>
                    </div>

                    <div x-show="showSlugInput" x-cloak>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Slug URL</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 bg-slate-100 border border-r-0 border-slate-200 rounded-l-xl text-slate-500 text-xs font-mono">sekolah.id/berita/</span>
                            <input type="text"
                                   name="slug"
                                   x-model="slug"
                                   value="{{ old('slug') }}"
                                   class="flex-1 bg-slate-50 border border-slate-200 rounded-r-xl text-slate-900 text-xs font-mono p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    <button type="button" @click="showSlugInput = !showSlugInput" class="text-xs text-indigo-600 font-semibold hover:underline">
                        <span x-text="showSlugInput ? 'Tutup edit slug' : 'Edit URL slug secara manual'"></span>
                    </button>
                </div>

                <!-- Content Rich Editor -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Isi Konten Artikel <span class="text-rose-500">*</span></label>
                    <textarea name="content"
                              id="content"
                              rows="15"
                              class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs summernote">{{ old('content') }}</textarea>
                </div>

                <!-- SEO Optimization -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-4 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Pengaturan SEO Search Engine</span>
                    </h3>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Meta Title</label>
                        <input type="text"
                               name="meta_title"
                               value="{{ old('meta_title') }}"
                               class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500"
                               placeholder="Judul unik untuk Google (max 60 karakter)">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Meta Description</label>
                        <textarea name="meta_description"
                                  rows="3"
                                  class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none"
                                  placeholder="Ringkasan artikel untuk hasil pencarian Google (max 160 karakter)">{{ old('meta_description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Sidebar Panel -->
            <div class="space-y-6">
                <!-- Status & Publikasi -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-4 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900">Publikasi</h3>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Postingan</label>
                        <select name="status" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (Publik)</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived (Arsip)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jadwal Tanggal Terbit</label>
                        <input type="datetime-local"
                               name="published_at"
                               value="{{ old('published_at') }}"
                               class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-4 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900">Gambar Cover / Unggulan</h3>

                    <div class="relative">
                        <div id="imagePreview" class="w-full aspect-video bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden">
                            <div class="text-center p-4">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs font-semibold text-slate-500">Pilih berkas foto cover</p>
                                <p class="text-[10px] text-slate-400 mt-1">JPEG, PNG, JPG (Maks 2MB)</p>
                            </div>
                        </div>
                        <input type="file"
                               name="thumbnail"
                               id="thumbnail"
                               accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               onchange="previewImage(this)">
                    </div>
                </div>

                <!-- Kategori -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-4 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900">Kategori Artikel</h3>

                    <select name="category_id" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #e2e8f0 !important;
        border-radius: 1rem !important;
        overflow: hidden;
        box-shadow: none !important;
    }
    .note-toolbar {
        background-color: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 8px 12px !important;
    }
    .note-btn {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        padding: 5px 10px !important;
        font-size: 12px !important;
        color: #334155 !important;
    }
    .note-btn:hover {
        background-color: #f1f5f9 !important;
        color: #4f46e5 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
function saveDraft() {
    const form = document.getElementById('postForm');
    const statusSelect = form.querySelector('select[name=status]');
    statusSelect.value = 'draft';
    form.submit();
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
        }

        reader.readAsDataURL(file);
    }
}

$(document).ready(function() {
    $('#content').summernote({
        placeholder: 'Tuliskan isi konten artikel berita di sini...',
        tabsize: 2,
        height: 450,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onChange: function(contents, $editable) {
                $('#content').val(contents);
            }
        }
    });
});
</script>
@endpush
@endsection
