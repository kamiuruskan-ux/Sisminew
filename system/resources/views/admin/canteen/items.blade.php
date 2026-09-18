@extends('layouts.admin')

@section('title', 'Kelola Menu E-Kantin')

@section('content')
<div x-data="{
        showModal: false,
        showDeleteModal: false,
        deleteTarget: null,
        deleteFormAction: '',
        confirmDelete(id, name) {
            this.deleteTarget = { id: id, name: name };
            this.deleteFormAction = '{{ url('admin/canteen/items') }}/' + id;
            this.showDeleteModal = true;
        },
        isEdit: false,

        formAction: '{{ route("admin.canteen.items.store") }}',
        editItem: {
            id: '',
            category_id: '',
            stall_id: '',
            name: '',
            description: '',
            price: '',
            stock: '',
            stall_name: '',
            is_available: true
        },
        closeModal() {
            this.showModal = false;
        },
        openAdd() {
            this.isEdit = false;
            this.formAction = '{{ route("admin.canteen.items.store") }}';
            this.editItem = { id: '', category_id: '{{ $categories->first()?->id }}', stall_id: '{{ $stalls->first()?->id }}', name: '', description: '', price: '', stock: '50', stall_name: 'Kantin Utama', is_available: true };
            this.showModal = true;
        },
        openEdit(item) {
            this.isEdit = true;
            this.formAction = '{{ url("admin/canteen/items") }}/' + item.id;
            this.editItem = { ...item };
            this.showModal = true;
        }
    }" 
    class="space-y-6">
    @include('components.delete-modal', ['title' => 'Hapus Menu Kantin', 'message' => 'Apakah Anda yakin ingin menghapus produk :name ini dari katalog kantin?'])


    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Kelola Menu & Stok Kantin</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Atur ketersediaan produk, stok, harga, dan gambar makanan/minuman kantin.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('admin.canteen.items') }}" class="w-full flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       @input.debounce.400ms="$el.closest('form').submit()"
                       x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                       placeholder="Cari nama produk kantin..." 
                       class="w-full pl-9 pr-8 py-2.5 sm:py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850 text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500/20">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5 sm:top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                @endif
            </div>
            
            <div class="w-full sm:w-44">
                <select name="category_id" @change="$el.closest('form').submit()" class="w-full px-3.5 py-2.5 sm:py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850 text-slate-700 dark:text-slate-200 font-semibold">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-44">
                <select name="stall_id" @change="$el.closest('form').submit()" class="w-full px-3.5 py-2.5 sm:py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850 text-slate-700 dark:text-slate-200 font-semibold">
                    <option value="">Semua Stand</option>
                    @foreach($stalls as $st)
                        <option value="{{ $st->id }}" {{ request('stall_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Items Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
        @foreach($items as $item)
            <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs flex flex-col justify-between">
                <div class="relative h-28 sm:h-44 w-full bg-slate-100 dark:bg-slate-800 shrink-0">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                    
                    <div class="absolute top-2 left-2 sm:top-3 sm:left-3 px-1.5 py-0.5 sm:px-2.5 sm:py-1 bg-slate-900/80 backdrop-blur rounded-lg text-[8px] sm:text-[10px] font-bold text-white uppercase truncate max-w-[80px] sm:max-w-none">
                        {{ $item->stall?->name ?? $item->stall_name }}
                    </div>

                    <form method="POST" action="{{ route('admin.canteen.items.toggle', $item->id) }}" class="absolute top-2 right-2 sm:top-3 sm:right-3">
                        @csrf
                        <button type="submit" class="px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-lg text-[8px] sm:text-[10px] font-bold text-white {{ $item->is_available ? 'bg-emerald-600' : 'bg-rose-600' }}">
                            {{ $item->is_available ? 'Tersedia' : 'Non-aktif' }}
                        </button>
                    </form>
                </div>

                <div class="p-3 sm:p-5 space-y-2 sm:space-y-3 flex-1 flex flex-col justify-between">
                    <div>
                        <span class="text-[9px] sm:text-[10px] font-extrabold text-indigo-500 uppercase">{{ $item->category?->name }}</span>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white mt-0.5 leading-snug line-clamp-1">{{ $item->name }}</h4>
                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1 sm:line-clamp-2 leading-relaxed">{{ $item->description }}</p>
                    </div>

                    <div class="pt-2 sm:pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2 sm:space-y-3">
                        <div class="flex items-center justify-between text-xs gap-1">
                            <span class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm whitespace-nowrap">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            <span class="font-bold text-slate-500 text-[10px] sm:text-xs truncate">Stok: {{ $item->stock }}</span>
                        </div>

                        <div class="flex space-x-1.5 sm:space-x-2 pt-0.5">
                            <button type="button" @click="openEdit({{ json_encode($item) }})" class="flex-1 py-1.5 sm:py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-[10px] sm:text-xs rounded-lg sm:rounded-xl transition-colors">Edit</button>
                            
                            <button type="button" @click="confirmDelete({{ $item->id }}, '{{ addslashes($item->name) }}')" class="px-2.5 py-1.5 sm:px-3 sm:py-2 bg-rose-50 dark:bg-rose-950/40 text-rose-600 font-bold text-[10px] sm:text-xs rounded-lg sm:rounded-xl hover:bg-rose-100 transition-colors cursor-pointer">Hapus</button>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $items->links() }}
    </div>

    <!-- Add/Edit Item Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" @click="showModal = false"></div>
        
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-800 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white" x-text="isEdit ? 'Edit Produk Kantin' : 'Tambah Produk Kantin Baru'"></h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times;</button>
                </div>

                <form :action="formAction" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                        <select name="category_id" x-model="editItem.category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Stand / Outlet Kantin</label>
                        <select name="stall_id" x-model="editItem.stall_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850">
                            <option value="">-- Pilih Stand Kantin --</option>
                            @foreach($stalls as $st)
                                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->owner_name ?? 'Vendor' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Produk</label>
                        <input type="text" name="name" x-model="editItem.name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Custom Stand Kantin (Opsional)</label>
                        <input type="text" name="stall_name" x-model="editItem.stall_name" placeholder="Contoh: Kantin Ibu Ani" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Harga (Rp)</label>
                            <input type="number" name="price" x-model="editItem.price" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Stok Awal</label>
                            <input type="number" name="stock" x-model="editItem.stock" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Ringkas</label>
                        <textarea name="description" x-model="editItem.description" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-slate-50 dark:bg-slate-850"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Foto Produk (Opsional)</label>
                        <input type="file" name="image" accept="image/*,.jfif" class="w-full text-xs text-slate-500">
                    </div>

                    <div class="pt-4 flex space-x-3">
                        <button type="button" @click="showModal = false" class="flex-1 py-3 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">Batal</button>
                        <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white font-bold text-xs rounded-xl shadow-md">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
