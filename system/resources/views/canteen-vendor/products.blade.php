@extends('layouts.canteen-vendor')

@section('title', 'Kelola Menu Stand')
@section('header_title', 'Kelola Menu Produk')

@section('content')
<div x-data="{ 
        showAddModal: false, 
        editModalData: null,
        deleteModalData: null,
        addImagePreview: null,
        editImagePreview: null,

        openEditModal(product) {
            this.editModalData = Object.assign({}, product);
            this.editImagePreview = product.image_url || null;
        },

        openDeleteModal(product) {
            this.deleteModalData = Object.assign({}, product);
        },

        handleFileSelect(event, isEdit = false) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (isEdit) {
                        this.editImagePreview = e.target.result;
                    } else {
                        this.addImagePreview = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        }
    }" 
    class="space-y-5 pb-24">

    <!-- Top Action & Search Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white">Daftar Menu Stand</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Atur harga, stok, dan diskon promo produk stand Anda.</p>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                <a href="{{ route('canteen.vendor.categories') }}" class="px-3 py-2 bg-cyan-50 dark:bg-cyan-950/60 hover:bg-cyan-100 text-cyan-700 dark:text-cyan-300 font-bold text-xs rounded-2xl transition-all flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span class="hidden sm:inline">Kategori</span>
                </a>
                <a href="{{ route('canteen.vendor.stock') }}" class="px-3 py-2 bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100 text-amber-700 dark:text-amber-300 font-bold text-xs rounded-2xl transition-all flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span class="hidden sm:inline">Stok</span>
                </a>
                <button type="button" @click="showAddModal = true" class="px-4 py-2.5 bg-primary hover:opacity-90 text-white font-bold text-xs rounded-2xl shadow-md transition-all flex items-center justify-center space-x-1.5 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Menu</span>
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('canteen.vendor.products') }}" class="flex flex-col sm:flex-row items-center gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
            <div class="flex-1 w-full relative">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari nama menu..." 
                       class="w-full pl-9 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="w-full sm:w-64">
                <select name="category_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Error Validation Alert -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 dark:bg-rose-950/60 border border-rose-500/30 text-rose-800 dark:text-rose-200 text-xs space-y-1 shadow-xs">
            <div class="font-bold flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Gagal menyimpan menu produk:</span>
            </div>
            <ul class="list-disc list-inside pl-5 space-y-0.5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Product Cards Grid (Responsive Proportional on PC Desktop) -->
    @if($products->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-5">
            @foreach($products as $product)
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs relative flex flex-col justify-between transition-all hover:shadow-md">
                    
                    <!-- Discount Badge -->
                    @if($product->discount_percent > 0)
                        <div class="absolute top-2.5 left-2.5 z-10 bg-rose-600 text-white font-black text-[9px] uppercase px-2 py-0.5 rounded-full shadow-md">
                            DISKON {{ $product->discount_percent }}%
                        </div>
                    @endif

                    <!-- Product Image -->
                    <div class="relative aspect-[3/2] w-full bg-primary/5 dark:bg-primary/10 overflow-hidden group flex items-center justify-center">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 dark:text-slate-600">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-[10px] font-bold text-slate-400 mt-1">Menu Kantin</span>
                            </div>
                        @endif
                        
                        <div class="absolute bottom-2 right-2">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-xl text-[10px] font-extrabold shadow-md backdrop-blur-sm {{ $product->stock > 0 ? 'bg-emerald-500/90 text-white' : 'bg-slate-800/90 text-slate-200' }}">
                                @if($product->stock > 0)
                                    Stok {{ $product->stock }}
                                @else
                                    Habis
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-3 flex-1 flex flex-col justify-between space-y-2">
                        <div class="space-y-1">
                            <span class="font-bold text-primary text-[9px] uppercase block tracking-wider">{{ $product->category?->name ?? 'Menu' }}</span>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-snug line-clamp-2" title="{{ $product->name }}">{{ $product->name }}</h4>
                        </div>

                        <div>
                            @if($product->discount_percent > 0 && $product->original_price)
                                <span class="text-[10px] text-rose-500 line-through block font-medium">Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                            @endif
                            <span class="text-sm font-black text-emerald-600 dark:text-emerald-400 block">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Availability Toggle Switch -->
                        <form method="POST" action="{{ route('canteen.vendor.products.toggle-availability', $product->id) }}" class="pt-1.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            @csrf
                            <span class="text-[10px] font-bold text-slate-500">Status:</span>
                            <button type="submit" class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold transition-all {{ $product->is_available ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }}">
                                <span class="inline-flex items-center gap-1">
                                    @if($product->is_available)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Tersedia
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Non-Aktif
                                    @endif
                                </span>
                            </button>
                        </form>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-1.5">
                            <button type="button" 
                                    @click="openEditModal({{ json_encode([
                                        'id' => $product->id,
                                        'name' => $product->name,
                                        'category_id' => $product->category_id,
                                        'price' => (float)$product->price,
                                        'original_price' => $product->original_price ? (float)$product->original_price : '',
                                        'discount_percent' => (int)$product->discount_percent,
                                        'stock' => (int)$product->stock,
                                        'description' => $product->description ?? '',
                                        'image_url' => $product->image_url
                                    ]) }})" 
                                    class="flex-1 py-1.5 bg-primary/10 text-primary font-bold text-[11px] rounded-xl hover:bg-primary/20 text-center transition-all">
                                Edit
                            </button>

                            <button type="button" 
                                    @click="openDeleteModal({{ json_encode([
                                        'id' => $product->id,
                                        'name' => $product->name
                                    ]) }})" 
                                    class="px-2.5 py-1.5 bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 font-bold text-[11px] rounded-xl hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-all">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 text-center border border-slate-200 dark:border-slate-800 space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Menu Produk</p>
            <p class="text-[11px] text-slate-400">Klik 'Tambah Menu' di atas untuk memasukkan menu makanan atau minuman baru.</p>
        </div>
    @endif

    <!-- Add Product Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showAddModal = false"></div>

        <div class="relative transform rounded-3xl bg-white dark:bg-slate-900 p-5 sm:p-6 text-left align-middle shadow-2xl transition-all w-full max-w-2xl border border-slate-200 dark:border-slate-800 space-y-4 my-auto z-10 max-h-[90vh] overflow-y-auto scrollbar-none">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white">Tambah Menu Produk Baru</h3>
                </div>
                <button type="button" @click="showAddModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-lg transition-colors">&times;</button>
            </div>

            <form method="POST" action="{{ route('canteen.vendor.products.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    <!-- Left: Image Preview & Upload -->
                    <div class="md:col-span-5 space-y-2.5">
                        <label class="block font-bold text-slate-700 dark:text-slate-300">Foto Sampul Menu</label>
                        <div class="relative aspect-4/3 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex flex-col items-center justify-center overflow-hidden p-2 group">
                            <template x-if="addImagePreview">
                                <img :src="addImagePreview" class="w-full h-full object-cover rounded-xl">
                            </template>
                            <template x-if="!addImagePreview">
                                <div class="text-center p-3 text-slate-400">
                                    <svg class="w-10 h-10 mx-auto mb-1.5 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-[11px] font-semibold block text-slate-500 dark:text-slate-400">Belum Ada Foto</span>
                                    <span class="text-[9px] text-slate-400">Klik tombol di bawah untuk unggah</span>
                                </div>
                            </template>
                        </div>
                        <input type="file" name="image" accept="image/*" id="add-product-image" @change="handleFileSelect($event, false)" class="hidden">
                        <label for="add-product-image" class="w-full py-2.5 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 font-bold text-xs rounded-xl border border-indigo-200 dark:border-indigo-800 text-center cursor-pointer transition-all flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Pilih Foto Produk</span>
                        </label>
                        <p class="text-[10px] text-slate-400 text-center">Format: JPG, PNG, WEBP. Maks: 2MB.</p>
                    </div>

                    <!-- Right: Form Fields -->
                    <div class="md:col-span-7 space-y-3.5">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Menu Produk <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required placeholder="Contoh: Nasi Goreng Spesial Telur" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori Menu <span class="text-rose-500">*</span></label>
                                <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Stok Porsi / Unit <span class="text-rose-500">*</span></label>
                                <input type="number" name="stock" value="20" required min="0" placeholder="20" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2.5">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Harga Jual (Rp) <span class="text-rose-500">*</span></label>
                                <input type="number" name="price" required min="0" placeholder="12000" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-emerald-600 dark:text-emerald-400 focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Harga Asli (Rp)</label>
                                <input type="number" name="original_price" placeholder="15000" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Diskon (%)</label>
                                <input type="number" name="discount_percent" min="0" max="100" placeholder="20" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi & Catatan Menu</label>
                            <textarea name="description" rows="2" placeholder="Komposisi rasa, porsi, topping..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all"></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end space-x-2">
                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition-all">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md transition-all">Simpan Menu</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div x-show="editModalData !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="editModalData = null"></div>

        <div class="relative transform rounded-3xl bg-white dark:bg-slate-900 p-5 sm:p-6 text-left align-middle shadow-2xl transition-all w-full max-w-2xl border border-slate-200 dark:border-slate-800 space-y-4 my-auto z-10 max-h-[90vh] overflow-y-auto scrollbar-none">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white">Edit Menu Produk</h3>
                </div>
                <button type="button" @click="editModalData = null" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center font-bold text-lg transition-colors">&times;</button>
            </div>

            <template x-if="editModalData">
                <form method="POST" :action="'{{ url('/canteen-vendor/products') }}/' + editModalData.id" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                        <!-- Left: Image Preview & Upload -->
                        <div class="md:col-span-5 space-y-2.5">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Foto Sampul Menu</label>
                            <div class="relative aspect-4/3 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex flex-col items-center justify-center overflow-hidden p-2 group">
                                <template x-if="editImagePreview">
                                    <img :src="editImagePreview" class="w-full h-full object-cover rounded-xl">
                                </template>
                                <template x-if="!editImagePreview">
                                    <div class="text-center p-3 text-slate-400">
                                        <svg class="w-10 h-10 mx-auto mb-1.5 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-[11px] font-semibold block text-slate-500 dark:text-slate-400">Belum Ada Foto</span>
                                        <span class="text-[9px] text-slate-400">Klik tombol di bawah untuk ganti</span>
                                    </div>
                                </template>
                            </div>
                            <input type="file" name="image" accept="image/*" id="edit-product-image" @change="handleFileSelect($event, true)" class="hidden">
                            <label for="edit-product-image" class="w-full py-2.5 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 text-indigo-700 dark:text-indigo-300 font-bold text-xs rounded-xl border border-indigo-200 dark:border-indigo-800 text-center cursor-pointer transition-all flex items-center justify-center space-x-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>Ganti Foto Produk</span>
                            </label>
                            <p class="text-[10px] text-slate-400 text-center">Format: JPG, PNG, WEBP. Maks: 2MB.</p>
                        </div>

                        <!-- Right: Form Fields -->
                        <div class="md:col-span-7 space-y-3.5">
                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Menu Produk <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="editModalData.name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori Menu <span class="text-rose-500">*</span></label>
                                    <select name="category_id" x-model="editModalData.category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Stok Porsi / Unit <span class="text-rose-500">*</span></label>
                                    <input type="number" name="stock" x-model="editModalData.stock" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2.5">
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Harga Jual (Rp) <span class="text-rose-500">*</span></label>
                                    <input type="number" name="price" x-model="editModalData.price" required min="0" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-emerald-600 dark:text-emerald-400 focus:ring-2 focus:ring-indigo-500 transition-all">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Harga Asli (Rp)</label>
                                    <input type="number" name="original_price" x-model="editModalData.original_price" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Diskon (%)</label>
                                    <input type="number" name="discount_percent" x-model="editModalData.discount_percent" min="0" max="100" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi & Catatan Menu</label>
                                <textarea name="description" x-model="editModalData.description" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end space-x-2">
                        <button type="button" @click="editModalData = null" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold hover:bg-slate-200 transition-all">Batal</button>
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-md transition-all">Simpan Perubahan</button>
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
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Konfirmasi Hapus Menu</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Apakah Anda yakin ingin menghapus menu <strong class="text-slate-900 dark:text-white" x-text="deleteModalData?.name"></strong>?</p>
                    <p class="text-[11px] text-rose-500 font-semibold mt-1">Tindakan ini tidak dapat dibatalkan.</p>
                </div>

                <template x-if="deleteModalData">
                    <form method="POST" :action="'{{ url('/canteen-vendor/products') }}/' + deleteModalData.id" class="pt-2 flex items-center justify-center space-x-2">
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
</div>
@endsection
