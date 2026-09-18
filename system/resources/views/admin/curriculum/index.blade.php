@extends('layouts.admin')

@section('title', 'Kelola Kurikulum')

@section('content')
<div class="space-y-6" x-data="{
    showModal: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    modalTitle: 'Tambah Pilar Kurikulum',
    submitButtonText: 'Simpan Pilar Kurikulum',
    formAction: '{{ route('admin.curriculum.store') }}',
    formMethod: 'POST',
    formData: {
        id: null,
        title: '',
        color: 'blue',
        description: '',
        order: 0,
        is_active: true
    },
    openCreateModal() {
        this.formAction = '{{ route('admin.curriculum.store') }}';
        this.formMethod = 'POST';
        this.modalTitle = 'Tambah Pilar Kurikulum Baru';
        this.submitButtonText = 'Simpan Pilar Kurikulum';
        this.formData = {
            id: null,
            title: '',
            color: 'blue',
            description: '',
            order: 0,
            is_active: true
        };
        this.showModal = true;
    },
    openEditModal(item) {
        this.formAction = '{{ url('admin/curriculum') }}/' + item.id;
        this.formMethod = 'PUT';
        this.modalTitle = 'Edit Pilar Kurikulum';
        this.submitButtonText = 'Perbarui Pilar Kurikulum';
        this.formData = {
            id: item.id,
            title: item.title || '',
            color: item.color || 'blue',
            description: item.description || '',
            order: item.order || 0,
            is_active: Boolean(item.is_active)
        };
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
    },
    confirmDelete(id, title) {
        this.deleteTarget = { id: id, title: title };
        this.deleteFormAction = '{{ url('admin/curriculum') }}/' + id;
        this.showDeleteModal = true;
    }
}">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Kelola Kurikulum &amp; Program Pendidikan</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola judul, deskripsi umum, dan pilar metode pembelajaran yang tampil di halaman kurikulum publik.</p>
        </div>
        <div>
            <button type="button" @click="openCreateModal()" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Pilar Kurikulum</span>
            </button>
        </div>
    </div>


    <!-- Card Edit Deskripsi Utama -->
    <div class="tailadmin-card p-6">
        <h2 class="text-base font-extrabold text-slate-900 dark:text-white mb-4">Pengaturan Teks Halaman Utama Kurikulum</h2>
        <form action="{{ route('admin.curriculum.update-description') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Judul Utama Halaman Kurikulum</label>
                <input type="text" name="curriculum_title" value="{{ old('curriculum_title', $curriculumTitle) }}" required class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Deskripsi Singkat Halaman Kurikulum</label>
                <textarea name="curriculum_description" rows="2" required class="w-full text-sm py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-700">{{ old('curriculum_description', $curriculumDescription) }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Simpan Perubahan Teks</button>
            </div>
        </form>
    </div>

    <!-- Table Pilar Kurikulum -->
    <div class="tailadmin-card p-5">
        <div class="mb-4">
            <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Daftar Pilar Metode Pembelajaran</h2>
            <p class="text-xs text-slate-500">Pilar-pilar kurikulum unggulan sekolah yang ditampilkan pada kartu grid.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">Urutan</th>
                        <th>Judul Pilar</th>
                        <th>Deskripsi Pilar</th>
                        <th>Aksen Warna</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($features as $feature)
                        <tr>
                            <td class="text-center font-bold text-slate-500">{{ $feature->order }}</td>
                            <td>
                                <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $feature->title }}</div>
                            </td>
                            <td>
                                <div class="text-xs text-slate-500 line-clamp-2 max-w-md">{{ $feature->description }}</div>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-{{ $feature->color }}-100 text-{{ $feature->color }}-700 dark:bg-{{ $feature->color }}-950/60 dark:text-{{ $feature->color }}-300">
                                    {{ ucfirst($feature->color) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.curriculum.toggle', $feature->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $feature->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400' }}">
                                        {{ $feature->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-right space-x-1">
                                <button type="button" 
                                        @click="openEditModal({
                                            id: {{ $feature->id }},
                                            title: '{{ addslashes($feature->title) }}',
                                            color: '{{ $feature->color }}',
                                            description: '{{ addslashes($feature->description ?? '') }}',
                                            order: {{ (int) $feature->order }},
                                            is_active: {{ $feature->is_active ? 1 : 0 }}
                                        })"
                                        class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-amber-600 inline-flex items-center cursor-pointer" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" 
                                        @click="confirmDelete({{ $feature->id }}, '{{ addslashes($feature->title) }}')"
                                        class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-rose-600 inline-flex items-center cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400 text-xs">Belum ada pilar kurikulum. Silakan tambahkan baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $features->links() }}
        </div>
    </div>

    <!-- Create/Edit Curriculum Pillar Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="closeModal()"></div>

            <div x-show="showModal" x-transition class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl rounded-2xl">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="modalTitle">Tambah Pilar Kurikulum</h3>
                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
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
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Judul Pilar <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" required class="w-full text-xs rounded-xl p-2.5 font-bold" placeholder="Contoh: Digital Hybrid Learning">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Deskripsi Pilar</label>
                        <textarea name="description" x-model="formData.description" rows="3" class="w-full text-xs rounded-xl p-2.5 resize-none" placeholder="Deskripsi peruntukan pilar kurikulum..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Warna Aksen <span class="text-rose-500">*</span></label>
                            <select name="color" x-model="formData.color" required class="w-full text-xs rounded-xl p-2.5 font-semibold">
                                <option value="blue">Biru (Blue)</option>
                                <option value="purple">Ungu (Purple)</option>
                                <option value="emerald">Hijau (Emerald)</option>
                                <option value="amber">Kuning/Oranye (Amber)</option>
                                <option value="rose">Merah (Rose)</option>
                                <option value="indigo">Nila (Indigo)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Urutan (Order)</label>
                            <input type="number" name="order" x-model="formData.order" min="0" class="w-full text-xs rounded-xl p-2.5">
                        </div>
                    </div>

                    <div class="pt-2 flex items-center space-x-2">
                        <input type="checkbox" name="is_active" value="1" id="curr_is_active" :checked="formData.is_active" class="w-4 h-4 text-primary border-slate-300 rounded focus:ring-primary">
                        <label for="curr_is_active" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Tampilkan (Aktif)</label>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="closeModal()" class="btn-secondary">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary" x-text="submitButtonText">
                            Simpan Pilar Kurikulum
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
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Hapus Pilar Kurikulum?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">Yakin ingin menghapus pilar <strong class="text-slate-800 dark:text-slate-200" x-text="deleteTarget?.title"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
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
