@extends('layouts.admin')

@section('title', 'Master Data Mata Pelajaran')
@section('page_title', 'Master Data Mata Pelajaran')

@section('content')
<div class="space-y-6" x-data="{
    showAddModal: false,
    showEditModal: false,
    editData: { id: '', name: '', code: '', category: 'Umum', description: '', order: 0, is_active: true },
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',

    openEdit(subject) {
        this.editData = {
            id: subject.id,
            name: subject.name,
            code: subject.code || '',
            category: subject.category || 'Umum',
            description: subject.description || '',
            order: subject.order || 0,
            is_active: Boolean(subject.is_active)
        };
        this.showEditModal = true;
    },

    confirmDelete(actionUrl, itemName) {
        this.deleteTarget = { name: itemName };
        this.deleteFormAction = actionUrl;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Mata Pelajaran', 'message' => 'Apakah Anda yakin ingin menghapus Mata Pelajaran :name?'])

    <!-- Alert Flash Message -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold text-xs flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Header & Statistics Cards -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Master Data Mata Pelajaran</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Kelola daftar mata pelajaran global yang digunakan secara terpusat di seluruh sistem (LMS, Jadwal, Bahan Ajar, Ujian).
            </p>
        </div>

        <button @click="showAddModal = true" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-md shadow-indigo-600/30 flex items-center space-x-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Mata Pelajaran</span>
        </button>
    </div>

    <!-- Quick Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase">Total Mata Pelajaran</span>
                <span class="text-xl font-black text-[#1C2434] dark:text-white">{{ $totalSubjects }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase">Pelajaran Aktif</span>
                <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">{{ $activeSubjects }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <span class="block text-xs font-bold text-slate-400 uppercase">Kategori Pelajaran</span>
                <span class="text-xl font-black text-purple-600 dark:text-purple-400">{{ $categoriesCount }}</span>
            </div>
        </div>
    </div>

    <!-- Table & Search Filter Box -->
    <div class="tailadmin-card overflow-hidden bg-white dark:bg-[#1A222C] rounded-2xl border border-slate-200 dark:border-slate-800">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.subjects.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama atau kode..." 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full pl-9 pr-8 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 text-[#1C2434] dark:text-white">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if(request('search'))
                        <a href="{{ route('admin.subjects.index', request()->except('search')) }}" class="absolute right-2.5 top-1.5 text-slate-400 hover:text-rose-500 font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>

                <select name="category" class="w-full sm:w-44 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 text-[#1C2434] dark:text-white" @change="$el.closest('form').submit()">
                    <option value="">Semua Kategori</option>
                    <option value="Umum" {{ request('category') == 'Umum' ? 'selected' : '' }}>Umum</option>
                    <option value="Kejuruan" {{ request('category') == 'Kejuruan' ? 'selected' : '' }}>Kejuruan</option>
                    <option value="Muatan Lokal" {{ request('category') == 'Muatan Lokal' ? 'selected' : '' }}>Muatan Lokal</option>
                    <option value="Pilihan" {{ request('category') == 'Pilihan' ? 'selected' : '' }}>Pilihan</option>
                </select>

                <select name="status" class="w-full sm:w-36 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 text-[#1C2434] dark:text-white" @change="$el.closest('form').submit()">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800 text-slate-500 uppercase font-bold">
                    <tr>
                        <th class="p-4 w-12 text-center">Urutan</th>
                        <th class="p-4">Kode</th>
                        <th class="p-4">Nama Mata Pelajaran</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($subjects as $sub)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                        <td class="p-4 text-center font-mono font-bold text-slate-500">{{ $sub->order ?: '-' }}</td>
                        <td class="p-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $sub->code ?: '-' }}</td>
                        <td class="p-4 font-bold text-[#1C2434] dark:text-white">
                            {{ $sub->name }}
                            @if($sub->description)
                            <p class="text-[11px] text-slate-400 font-normal mt-0.5 line-clamp-1">{{ $sub->description }}</p>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                {{ $sub->category }}
                            </span>
                        </td>
                        <td class="p-4">
                            <form action="{{ route('admin.subjects.toggle-status', $sub->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase transition border {{ $sub->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-300 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-700 border-rose-300 dark:bg-rose-950 dark:text-rose-300' }}">
                                    {{ $sub->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <button @click="openEdit({{ json_encode($sub) }})" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg text-xs font-bold transition">
                                Edit
                            </button>
                            <button type="button" @click="confirmDelete('{{ route('admin.subjects.destroy', $sub->id) }}', '{{ addslashes($sub->name) }}')" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-bold transition">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400 text-xs font-bold">
                            Belum ada Mata Pelajaran yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $subjects->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah Mata Pelajaran -->
    <div x-show="showAddModal" x-cloak @click.self="showAddModal = false" @keydown.escape.window="showAddModal = false" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#1A222C] rounded-2xl max-w-lg w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800">
            <h3 class="font-extrabold text-[#1C2434] dark:text-white text-lg">Tambah Mata Pelajaran Global</h3>
            <form action="{{ route('admin.subjects.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-1">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Kode Singkat</label>
                        <input type="text" name="code" placeholder="IPA" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Nama Mata Pelajaran</label>
                        <input type="text" name="name" placeholder="Ilmu Pengetahuan Alam" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Kategori</label>
                        <select name="category" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                            <option value="Umum">Umum</option>
                            <option value="Kejuruan">Kejuruan</option>
                            <option value="Muatan Lokal">Muatan Lokal</option>
                            <option value="Pilihan">Pilihan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Urutan Tampil</label>
                        <input type="number" name="order" value="0" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi & Catatan (Opsional)</label>
                    <textarea name="description" rows="2" placeholder="Catatan mata pelajaran..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white"></textarea>
                </div>

                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_active" id="add_is_active" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="add_is_active" class="text-xs font-bold text-slate-700 dark:text-slate-300">Aktifkan Mata Pelajaran ini</label>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl">Simpan Mata Pelajaran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Mata Pelajaran -->
    <div x-show="showEditModal" x-cloak @click.self="showEditModal = false" @keydown.escape.window="showEditModal = false" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#1A222C] rounded-2xl max-w-lg w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800">
            <h3 class="font-extrabold text-[#1C2434] dark:text-white text-lg">Edit Mata Pelajaran</h3>
            <form :action="'{{ url('admin/subjects') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-1">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Kode Singkat</label>
                        <input type="text" name="code" x-model="editData.code" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Nama Mata Pelajaran</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Kategori</label>
                        <select name="category" x-model="editData.category" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                            <option value="Umum">Umum</option>
                            <option value="Kejuruan">Kejuruan</option>
                            <option value="Muatan Lokal">Muatan Lokal</option>
                            <option value="Pilihan">Pilihan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Urutan Tampil</label>
                        <input type="number" name="order" x-model="editData.order" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi & Catatan</label>
                    <textarea name="description" x-model="editData.description" rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-2.5 text-xs text-[#1C2434] dark:text-white"></textarea>
                </div>

                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="editData.is_active" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="edit_is_active" class="text-xs font-bold text-slate-700 dark:text-slate-300">Aktifkan Mata Pelajaran ini</label>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl">Update Mata Pelajaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
