@extends('layouts.admin')

@section('title', 'Kelola Ekstrakurikuler')

@section('content')
<div class="space-y-6" x-data="{
    showModal: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    modalTitle: 'Tambah Ekstrakurikuler',
    submitButtonText: 'Simpan Ekstrakurikuler',
    formAction: '{{ route('admin.extracurriculars.store') }}',
    formMethod: 'POST',
    previewUrl: null,
    formData: {
        id: null,
        name: '',
        category: 'kepemimpinan',
        schedule: '',
        description: '',
        order: 0,
        is_active: true
    },
    openCreateModal() {
        this.formAction = '{{ route('admin.extracurriculars.store') }}';
        this.formMethod = 'POST';
        this.modalTitle = 'Tambah Ekstrakurikuler Baru';
        this.submitButtonText = 'Simpan Ekstrakurikuler';
        this.previewUrl = null;
        this.formData = {
            id: null,
            name: '',
            category: 'kepemimpinan',
            schedule: '',
            description: '',
            order: 0,
            is_active: true
        };
        const fileInput = document.getElementById('extraImageInput');
        if (fileInput) fileInput.value = '';
        this.showModal = true;
    },
    openEditModal(item) {
        this.formAction = '{{ url('admin/extracurriculars') }}/' + item.id;
        this.formMethod = 'PUT';
        this.modalTitle = 'Edit Ekstrakurikuler';
        this.submitButtonText = 'Perbarui Ekstrakurikuler';
        this.previewUrl = item.image_url || null;
        this.formData = {
            id: item.id,
            name: item.name || '',
            category: item.category || 'kepemimpinan',
            schedule: item.schedule || '',
            description: item.description || '',
            order: item.order || 0,
            is_active: Boolean(item.is_active)
        };
        const fileInput = document.getElementById('extraImageInput');
        if (fileInput) fileInput.value = '';
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
        this.previewUrl = null;
    },
    confirmDelete(id, name) {
        this.deleteTarget = { id: id, name: name };
        this.deleteFormAction = '{{ url('admin/extracurriculars') }}/' + id;
        this.showDeleteModal = true;
    }
}">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Kelola Ekstrakurikuler</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola daftar kegiatan ekstrakurikuler &amp; pengembangan minat bakat siswa yang tampil di website publik.</p>
        </div>
        <div>
            <button type="button" @click="openCreateModal()" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Ekstrakurikuler</span>
            </button>
        </div>
    </div>


    <!-- Card Table -->
    <div class="tailadmin-card p-5">
        <!-- Filter & Search -->
        <form method="GET" action="{{ route('admin.extracurriculars.index') }}" class="mb-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="category" onchange="this.form.submit()" class="text-xs py-2 px-3 rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $key => $catName)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $catName }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="relative w-full sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" 
                       @input.debounce.400ms="$el.closest('form').submit()"
                       x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                       placeholder="Cari nama / jadwal..." class="w-full text-xs py-2 pl-9 pr-8 rounded-lg border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 focus:bg-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-3 top-2 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">Urutan</th>
                        <th>Ekstrakurikuler</th>
                        <th>Kategori</th>
                        <th>Jadwal</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($extracurriculars as $extra)
                        <tr>
                            <td class="text-center font-bold text-slate-500">{{ $extra->order }}</td>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 flex items-center justify-center">
                                        @if($extra->image_url)
                                            <img src="{{ $extra->image_url }}" alt="{{ $extra->name }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $extra->name }}</div>
                                        <div class="text-xs text-slate-500 line-clamp-1 max-w-md mt-0.5">{{ $extra->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $categories[$extra->category] ?? ucfirst($extra->category) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                                    {{ $extra->schedule ?: '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.extracurriculars.toggle', $extra->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $extra->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400' }}">
                                        {{ $extra->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-right space-x-1">
                                <button type="button" 
                                        @click="openEditModal({
                                            id: {{ $extra->id }},
                                            name: '{{ addslashes($extra->name) }}',
                                            category: '{{ $extra->category }}',
                                            schedule: '{{ addslashes($extra->schedule ?? '') }}',
                                            description: '{{ addslashes($extra->description ?? '') }}',
                                            order: {{ (int) $extra->order }},
                                            is_active: {{ $extra->is_active ? 1 : 0 }},
                                            image_url: '{{ $extra->image_url ?? '' }}'
                                        })"
                                        class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-amber-600 inline-flex items-center cursor-pointer" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" 
                                        @click="confirmDelete({{ $extra->id }}, '{{ addslashes($extra->name) }}')"
                                        class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-rose-600 inline-flex items-center cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400 text-xs">Belum ada data ekstrakurikuler. Silakan tambahkan baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $extracurriculars->links() }}
        </div>
    </div>

    <!-- Create/Edit Extracurricular Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="closeModal()"></div>

            <div x-show="showModal" x-transition class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-2xl">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="modalTitle">Tambah Ekstrakurikuler</h3>
                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
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

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nama Ekstrakurikuler <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="formData.name" required class="w-full text-xs rounded-xl p-2.5 font-bold" placeholder="Contoh: Klub Robotik & AI">
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Foto / Sampul Kegiatan</label>
                        <div class="flex items-center space-x-3">
                            <template x-if="previewUrl">
                                <div class="w-16 h-16 rounded-xl border border-slate-200 overflow-hidden shrink-0 bg-slate-100">
                                    <img :src="previewUrl" class="w-full h-full object-cover">
                                </div>
                            </template>
                            <input type="file" id="extraImageInput" name="image" accept="image/*" @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { previewUrl = e.target.result; };
                                    reader.readAsDataURL(file);
                                }
                            " class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Kategori <span class="text-rose-500">*</span></label>
                            <select name="category" x-model="formData.category" required class="w-full text-xs rounded-xl p-2.5 font-semibold">
                                @foreach($categories as $key => $catName)
                                    <option value="{{ $key }}">{{ $catName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Jadwal Pelaksanaan</label>
                            <input type="text" name="schedule" x-model="formData.schedule" class="w-full text-xs rounded-xl p-2.5" placeholder="Contoh: Rabu (15:30 WIB)">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Deskripsi Kegiatan</label>
                        <textarea name="description" x-model="formData.description" rows="3" class="w-full text-xs rounded-xl p-2.5 resize-none" placeholder="Deskripsi peruntukan ekstrakurikuler..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 items-center">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Urutan (Order)</label>
                            <input type="number" name="order" x-model="formData.order" min="0" class="w-full text-xs rounded-xl p-2.5">
                        </div>
                        <div class="pt-5 flex items-center space-x-2">
                            <input type="checkbox" name="is_active" value="1" id="extra_is_active" :checked="formData.is_active" class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary">
                            <label for="extra_is_active" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Tampilkan (Aktif)</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="closeModal()" class="btn-secondary">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary" x-text="submitButtonText">
                            Simpan Ekstrakurikuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 max-w-sm w-full shadow-2xl border border-slate-200 dark:border-slate-800 text-center" @click.away="showDeleteModal = false">
            <div class="w-12 h-12 bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-100 dark:border-rose-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Hapus Ekstrakurikuler?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">Yakin ingin menghapus ekstrakurikuler <strong class="text-slate-800 dark:text-slate-200" x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex space-x-2">
                <button type="button" @click="showDeleteModal = false" class="flex-1 btn-secondary">
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
