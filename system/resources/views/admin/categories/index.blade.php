@extends('layouts.admin')

@section('title', 'Kategori Konten')
@section('page_title', 'Kategori Konten & Website')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    showModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    modalTitle: 'Tambah Kategori',
    submitButtonText: 'Simpan Kategori',
    formAction: '{{ route('admin.categories.store') }}',
    formMethod: 'POST',
    formData: {
        name: '',
        slug: '',
        type: 'post',
        color: '#4F46E5',
        order: 0,
        is_active: true,
        description: ''
    },
    openCreateModal() {
        this.formAction = '{{ route('admin.categories.store') }}';
        this.formMethod = 'POST';
        this.modalTitle = 'Tambah Kategori Konten';
        this.submitButtonText = 'Simpan Kategori';
        this.formData = {
            name: '',
            slug: '',
            type: 'post',
            color: '#4F46E5',
            order: 0,
            is_active: true,
            description: ''
        };
        this.showModal = true;
    },
    openEditModal(category) {
        this.formAction = '{{ url('admin/categories') }}/' + category.id;
        this.formMethod = 'PUT';
        this.modalTitle = 'Edit Kategori Konten';
        this.submitButtonText = 'Perbarui Kategori';
        this.formData = {
            name: category.name || '',
            slug: category.slug || '',
            type: category.type || 'post',
            color: category.color || '#4F46E5',
            order: category.order || 0,
            is_active: Boolean(category.is_active),
            description: category.description || ''
        };
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
    },
    confirmDelete(categoryId, categoryName) {
        this.deleteTarget = { id: categoryId, name: categoryName };
        this.deleteFormAction = '{{ url('admin/categories') }}/' + categoryId;
        this.showDeleteModal = true;
    },
    toggleStatus(categoryId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url('admin/categories') }}/' + categoryId + '/toggle';
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Kategori Konten</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola taksonomi dan pengelompokan artikel berita serta galeri foto sekolah.</p>
        </div>
        @permission('create-categories')
        <div>
            <button type="button" @click="openCreateModal()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Kategori Baru</span>
            </button>
        </div>
        @endpermission
    </div>


    <!-- Stats Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kategori</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($categories->total()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori Aktif</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1 tracking-tight">{{ number_format($categories->where('is_active', true)->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori Nonaktif</p>
                <p class="text-2xl font-bold text-slate-600 mt-1 tracking-tight">{{ number_format($categories->where('is_active', false)->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-200 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Categories Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Kategori</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} dari {{ $categories->total() }} kategori</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Nama Kategori & Slug</th>
                        <th class="px-5 py-3">Tipe Modul</th>
                        <th class="px-5 py-3">Warna Aksen</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5">
                                <div>
                                    <p class="font-bold text-slate-900 text-xs">{{ $category->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $category->slug }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded border {{ $category->type === 'post' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-pink-50 text-pink-700 border-pink-200' }}">
                                    {{ $category->type === 'post' ? 'Artikel Berita' : 'Galeri Foto' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-2">
                                    <span class="w-4 h-4 rounded-md border border-slate-200 shadow-xs" style="background-color: {{ $category->color ?? '#4F46E5' }}"></span>
                                    <code class="text-[11px] text-slate-600 font-mono">{{ $category->color ?? '#4F46E5' }}</code>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <button type="button" @click="toggleStatus({{ $category->id }})" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $category->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}" title="Klik untuk mengubah status">
                                    <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transform ring-0 transition duration-200 ease-in-out {{ $category->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    @permission('edit-categories')
                                    <button type="button" 
                                            @click="openEditModal({
                                                id: {{ $category->id }},
                                                name: '{{ addslashes($category->name) }}',
                                                slug: '{{ addslashes($category->slug) }}',
                                                type: '{{ $category->type }}',
                                                color: '{{ $category->color }}',
                                                order: {{ (int) $category->order }},
                                                is_active: {{ $category->is_active ? 1 : 0 }},
                                                description: '{{ addslashes($category->description ?? '') }}'
                                            })" 
                                            class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" title="Edit Kategori">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @endpermission

                                    @permission('delete-categories')
                                    <button type="button" 
                                            @click="confirmDelete({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" title="Hapus Kategori">
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
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">
                                Belum ada kategori yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Category Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="closeModal()"></div>

            <div x-show="showModal" x-transition class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white border border-slate-200 shadow-xl rounded-2xl">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900" x-text="modalTitle">Tambah Kategori</h3>
                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="formAction" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="formMethod === 'PUT'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="formData.name" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-bold" placeholder="Contoh: Berita Akademik">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Slug URL</label>
                        <input type="text" name="slug" x-model="formData.slug" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs font-mono rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500" placeholder="Biarkan kosong untuk auto-generate">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Modul <span class="text-rose-500">*</span></label>
                            <select name="type" x-model="formData.type" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-semibold">
                                <option value="post">Artikel Berita (Post)</option>
                                <option value="gallery">Galeri Foto</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Warna Label</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" x-model="formData.color" class="w-9 h-9 border border-slate-200 rounded-lg cursor-pointer bg-slate-50 p-0.5">
                                <input type="text" name="color" x-model="formData.color" class="flex-1 bg-slate-50 border border-slate-200 text-slate-900 text-xs font-mono rounded-xl p-2 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 items-center">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Urutan (Order)</label>
                            <input type="number" name="order" x-model="formData.order" min="0" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div class="pt-5 flex items-center space-x-2">
                            <input type="checkbox" name="is_active" value="1" id="cat_is_active" :checked="formData.is_active" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                            <label for="cat_is_active" class="text-xs font-bold text-slate-700 cursor-pointer">Status Kategori Aktif</label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Singkat</label>
                        <textarea name="description" x-model="formData.description" rows="3" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none" placeholder="Deskripsi peruntukan kategori..."></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
                        <button type="button" @click="closeModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer" x-text="submitButtonText">
                            Simpan Kategori
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
            <h3 class="text-base font-bold text-slate-900">Hapus Kategori?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">Yakin ingin menghapus kategori <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
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
