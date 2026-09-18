@extends('layouts.canteen-vendor')

@section('title', 'Kelola Kategori Menu')
@section('header_title', 'Kategori Menu Kantin')

@section('content')
<div x-data="{
        showAddModal: false,
        editModalData: null,
        deleteModalData: null,

        openEditModal(category) {
            this.editModalData = Object.assign({}, category);
        },

        openDeleteModal(category) {
            this.deleteModalData = Object.assign({}, category);
        }
    }" 
    class="space-y-5 pb-24 w-full">

    <!-- Top Action & Search Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white">Kelola Kategori Menu</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Atur kategori makanan dan minuman untuk memudahkan filter siswa.</p>
            </div>

            <button type="button" @click="showAddModal = true" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-2xl shadow-md transition-all flex items-center justify-center space-x-1.5 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Kategori</span>
            </button>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('canteen.vendor.categories') }}" class="pt-2 border-t border-slate-100 dark:border-slate-800">
            <div class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama kategori..." 
                       class="w-full pl-9 pr-4 py-2 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>
    </div>

    <!-- Error Validation Alert -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 dark:bg-rose-950/60 border border-rose-500/30 text-rose-800 dark:text-rose-200 text-xs space-y-1 shadow-xs">
            <div class="font-bold flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Gagal menyimpan kategori:</span>
            </div>
            <ul class="list-disc list-inside pl-5 space-y-0.5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Categories List Grid -->
    @if($categories->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            @foreach($categories as $cat)
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between space-x-3 transition-all hover:shadow-md">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div class="min-w-0 space-y-0.5">
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white truncate">{{ $cat->name }}</h3>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    {{ $cat->items_count }} Menu Produk
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $cat->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' }}">
                                    {{ $cat->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-1 shrink-0">
                        <!-- Toggle Form -->
                        <form method="POST" action="{{ route('canteen.vendor.categories.toggle', $cat->id) }}">
                            @csrf
                            <button type="submit" title="Ubah Status" class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </button>
                        </form>

                        <button type="button" 
                                @click="openEditModal({{ json_encode([
                                    'id' => $cat->id,
                                    'name' => $cat->name,
                                    'icon' => $cat->icon ?? ''
                                ]) }})" 
                                class="p-2 rounded-xl text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 transition-colors" 
                                title="Edit Kategori">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>

                        <button type="button" 
                                @click="openDeleteModal({{ json_encode([
                                    'id' => $cat->id,
                                    'name' => $cat->name,
                                    'items_count' => $cat->items_count
                                ]) }})" 
                                class="p-2 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition-colors" 
                                title="Hapus Kategori">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $categories->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 text-center border border-slate-200 dark:border-slate-800 space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Kategori</p>
            <p class="text-[11px] text-slate-400">Klik 'Tambah Kategori' di atas untuk membuat kategori produk baru.</p>
        </div>
    @endif

    <!-- Add Category Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showAddModal = false"></div>

        <div class="relative transform rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl transition-all w-full max-w-sm border border-slate-200 dark:border-slate-800 space-y-4 my-auto z-10">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Tambah Kategori Baru</h3>
                <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('canteen.vendor.categories.store') }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Makanan Berat, Minuman Cold..." class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="pt-3 flex justify-end space-x-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl font-bold">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-xs">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div x-show="editModalData !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="editModalData = null"></div>

        <div class="relative transform rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl transition-all w-full max-w-sm border border-slate-200 dark:border-slate-800 space-y-4 my-auto z-10">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Edit Kategori Menu</h3>
                <button type="button" @click="editModalData = null" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <template x-if="editModalData">
                <form method="POST" :action="'{{ url('/canteen-vendor/categories') }}/' + editModalData.id" class="space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="editModalData.name" required class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="pt-3 flex justify-end space-x-2">
                        <button type="button" @click="editModalData = null" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold shadow-xs">Simpan Perubahan</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- Delete Confirmation HTML Modal -->
    <div x-show="deleteModalData !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteModalData = null"></div>

        <div class="relative transform rounded-3xl bg-white dark:bg-slate-900 p-6 text-center align-middle shadow-2xl transition-all w-full max-w-sm border border-slate-200 dark:border-slate-800 space-y-4 my-auto z-10">
            
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>

            <div class="space-y-1">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Hapus Kategori</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Apakah Anda yakin ingin menghapus kategori <strong class="text-slate-900 dark:text-white" x-text="deleteModalData?.name"></strong>?</p>
                <template x-if="deleteModalData?.items_count > 0">
                    <p class="text-[11px] text-rose-500 font-bold mt-1">Peringatan: Terdapat <span x-text="deleteModalData?.items_count"></span> menu produk dalam kategori ini.</p>
                </template>
            </div>

            <template x-if="deleteModalData">
                <form method="POST" :action="'{{ url('/canteen-vendor/categories') }}/' + deleteModalData.id" class="pt-2 flex items-center justify-center space-x-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModalData = null" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-2xl font-bold text-xs hover:bg-slate-200">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-bold text-xs shadow-md transition-all">
                        Ya, Hapus
                    </button>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
