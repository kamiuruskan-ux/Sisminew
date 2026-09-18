@extends('layouts.student-mobile')

@section('title', $stall->name . ' - Profil Toko Kantin')
@section('header_title', $stall->name)

@section('content')
<div x-data="{
        searchQuery: '{{ request('search') }}',
        selectedCategory: '{{ request('category', 'all') }}',
        cart: JSON.parse(localStorage.getItem('student_canteen_cart') || '[]'),
        showCartDrawer: false,
        paymentMethod: 'savings_balance',
        notes: '',
        isSubmitting: false,
        touchStartX: 0,
        touchStartY: 0,
        toast: { show: false, message: '', type: 'info' },
        showToast(message, type = 'info') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 3000);
        },

        init() {
            this.$watch('cart', val => {
                localStorage.setItem('student_canteen_cart', JSON.stringify(val));
            });

            window.addEventListener('touchstart', (e) => {
                if (e.target.closest('.overflow-x-auto')) return;
                this.touchStartX = e.touches[0].clientX;
                this.touchStartY = e.touches[0].clientY;
            }, { passive: true });

            window.addEventListener('touchend', (e) => {
                if (e.target.closest('.overflow-x-auto')) return;
                let touchEndX = e.changedTouches[0].clientX;
                let touchEndY = e.changedTouches[0].clientY;
                let diffX = this.touchStartX - touchEndX;
                let diffY = Math.abs(this.touchStartY - touchEndY);

                if (diffX > 75 && diffY < 60 && !this.showCartDrawer) {
                    this.showCartDrawer = true;
                }
            }, { passive: true });
        },
        addToCart(item) {
            let existing = this.cart.find(c => c.id === item.id);
            if (existing) {
                if (existing.qty < item.stock) {
                    existing.qty++;
                } else {
                    this.showToast('Stok maksimal telah tercapai.', 'warning');
                }
            } else {
                this.cart.push({
                    id: item.id,
                    name: item.name,
                    price: parseFloat(item.price),
                    original_price: item.original_price ? parseFloat(item.original_price) : null,
                    discount_percent: item.discount_percent || 0,
                    stock: item.stock,
                    image_url: item.image_url,
                    stall_name: item.stall_name,
                    qty: 1
                });
            }
            localStorage.setItem('student_canteen_cart', JSON.stringify(this.cart));
        },
        updateQty(index, delta) {
            let item = this.cart[index];
            if (item) {
                let newQty = item.qty + delta;
                if (newQty > 0 && newQty <= item.stock) {
                    item.qty = newQty;
                } else if (newQty <= 0) {
                    this.cart.splice(index, 1);
                } else {
                    this.showToast('Stok tidak mencukupi.', 'warning');
                }
            }
            localStorage.setItem('student_canteen_cart', JSON.stringify(this.cart));
        },
        get totalItems() {
            return this.cart.reduce((sum, item) => sum + item.qty, 0);
        },
        get totalPrice() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },
        formatRupiah(val) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
        },
        submitCheckout() {
            if (this.cart.length === 0) return;
            this.isSubmitting = true;

            fetch('{{ route("student.canteen.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    items: this.cart,
                    payment_method: this.paymentMethod,
                    notes: this.notes,
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isSubmitting = false;
                if (data.success) {
                    this.cart = [];
                    localStorage.removeItem('student_canteen_cart');
                    window.location.href = data.redirect_url;
                } else {
                    this.showToast(data.message || 'Gagal checkout pesanan.', 'error');
                }
            })
            .catch(err => {
                this.isSubmitting = false;
                this.showToast('Terjadi kesalahan sistem.', 'error');
            });
        }
    }" 
    class="space-y-5 pb-28 lg:pb-10">

    <!-- Top Navigation Bar & Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.canteen.index') }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold shadow-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span>Kembali ke E-Kantin</span>
        </a>

        <!-- Cart Button -->
        <button @click="showCartDrawer = true" type="button" class="relative inline-flex items-center space-x-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-xs transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            <span class="hidden sm:inline">Keranjang</span>
            <span x-show="totalItems > 0" x-text="totalItems" class="absolute -top-2 -right-2 inline-flex items-center justify-center min-w-[20px] h-[20px] px-1 bg-rose-500 text-white text-[10px] font-extrabold leading-none rounded-full ring-2 ring-white dark:ring-slate-900 shadow-sm"></span>
        </button>
    </div>

    <!-- 1. Shopee-style Store Front Header Banner Card -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-slate-900 text-white shadow-md border border-slate-800">
        <!-- Banner Background Cover -->
        <div class="relative h-32 sm:h-44 w-full bg-slate-800 overflow-hidden">
            <img src="{{ $stall->banner_url }}" alt="{{ $stall->name }} Banner" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
        </div>

        <!-- Store Info Content Area -->
        <div class="p-4 sm:p-6 relative z-10 -mt-10 sm:-mt-14">
            <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4">
                <div class="flex items-end space-x-3 sm:space-x-4">
                    <!-- Logo / Avatar -->
                    <div class="relative shrink-0">
                        <img src="{{ $stall->logo_url }}" alt="{{ $stall->name }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-2 border-white shadow-md bg-white">
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] border border-white" title="Terverifikasi">
                            ✓
                        </span>
                    </div>

                    <!-- Store Name & Rating -->
                    <div>
                        <div class="flex items-center space-x-2">
                            <h1 class="text-base sm:text-xl font-extrabold text-white leading-tight">{{ $stall->name }}</h1>
                            <span class="inline-flex items-center space-x-0.5 px-2 py-0.5 text-[10px] font-bold bg-amber-500/20 text-amber-300 rounded-md border border-amber-500/30">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>{{ $stall->rating }}</span>
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 mt-1 line-clamp-1 max-w-lg">{{ $stall->description }}</p>
                    </div>
                </div>

                <!-- Operating Info Badges -->
                <div class="flex items-center space-x-2 text-[11px] text-slate-300 w-full sm:w-auto justify-between sm:justify-end border-t border-slate-800 sm:border-t-0 pt-2 sm:pt-0">
                    <div class="px-3 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700 text-center">
                        <span class="text-[9px] text-slate-400 block uppercase font-semibold">Jam Operasional</span>
                        <span class="font-bold text-white">{{ $stall->operating_hours }}</span>
                    </div>
                    <div class="px-3 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700 text-center">
                        <span class="text-[9px] text-slate-400 block uppercase font-semibold">Total Menu</span>
                        <span class="font-bold text-indigo-400">{{ $items->total() }} Produk</span>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- 3. Store Product Grid (2 Columns on Mobile, Shopee Style) -->
    <div>
        <div class="flex items-center justify-between mb-3 px-1">
            <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Daftar Produk Toko {{ $stall->name }}</h3>
            <span class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $items->total() }} Produk</span>
        </div>

        @if($items->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($items as $item)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs hover:border-indigo-300 dark:hover:border-indigo-700 transition-all group flex flex-col justify-between relative">
                        
                        <!-- Discount Badge Over Image -->
                        @if($item->discount_percent > 0)
                            <div class="absolute top-2 left-2 z-10 bg-red-600 text-white font-black text-[9px] uppercase tracking-wider px-2 py-0.5 rounded-md shadow-sm">
                                DISKON {{ $item->discount_percent }}%
                            </div>
                        @endif

                        <!-- Product Image & Badges -->
                        <div class="relative h-32 sm:h-40 w-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <div class="absolute top-2 right-2 px-2 py-0.5 rounded-md text-[9px] font-bold {{ $item->stock > 0 ? 'bg-emerald-600 text-white' : 'bg-slate-600 text-white' }}">
                                {{ $item->stock > 0 ? 'Stok ' . $item->stock : 'Habis' }}
                            </div>
                        </div>

                        <!-- Product Info & Pricing -->
                        <div class="p-3 sm:p-4 flex-1 flex flex-col justify-between space-y-3">
                            <div class="space-y-1">
                                <span class="font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider text-[9px] block">{{ $item->category?->name }}</span>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white leading-snug line-clamp-1 sm:line-clamp-2">{{ $item->name }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 hidden sm:line-clamp-2 leading-relaxed pt-0.5">{{ $item->description }}</p>
                            </div>

                            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-1">
                                <div class="min-w-0 flex-1">
                                    @if($item->discount_percent > 0 && $item->original_price)
                                        <span class="text-[10px] text-red-500 line-through block font-medium">Rp {{ number_format($item->original_price, 0, ',', '.') }}</span>
                                    @endif
                                    <span class="text-xs sm:text-sm font-extrabold {{ $item->discount_percent > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-white' }} truncate block">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </span>
                                </div>

                                <button @click="addToCart({
                                            id: {{ $item->id }},
                                            name: '{{ addslashes($item->name) }}',
                                            price: {{ $item->price }},
                                            original_price: {{ $item->original_price ?? 'null' }},
                                            discount_percent: {{ $item->discount_percent }},
                                            stock: {{ $item->stock }},
                                            image_url: '{{ $item->image_url }}',
                                            stall_name: '{{ addslashes($item->stall_name) }}'
                                        })" 
                                        type="button" 
                                        :disabled="{{ $item->stock <= 0 ? 'true' : 'false' }}"
                                        class="px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-200 dark:disabled:bg-slate-800 text-white font-bold transition-all shadow-xs active:scale-95 flex items-center justify-center shrink-0 space-x-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="text-[11px] sm:text-xs">Beli</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Links -->
            <div class="mt-6 flex justify-center">
                {{ $items->links() }}
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-8 sm:p-12 text-center border border-slate-200 dark:border-slate-800">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Tidak ada menu di toko ini</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Coba gunakan kata kunci pencarian lain atau pilih kategori lain.</p>
            </div>
        @endif
    </div>

    <!-- Mobile Sticky Quick Checkout Bar -->
    <div x-show="totalItems > 0" 
         x-transition
         class="fixed bottom-16 left-3 right-3 lg:hidden z-30 bg-slate-900 text-white p-3 rounded-2xl shadow-xl border border-slate-700 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                <span x-text="totalItems"></span>
            </div>
            <div>
                <span class="text-[10px] font-semibold text-slate-300 block uppercase">Total Keranjang</span>
                <span x-text="formatRupiah(totalPrice)" class="text-xs font-bold text-white"></span>
            </div>
        </div>

        <button type="button" @click="showCartDrawer = true" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-xs transition-transform active:scale-95 flex items-center space-x-1">
            <span>Checkout</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </button>
    </div>

    <!-- Cart Slide-Over Drawer Modal -->
    <div x-show="showCartDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showCartDrawer = false"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white dark:bg-slate-900 shadow-2xl flex flex-col border-l border-slate-200 dark:border-slate-800">
                <!-- Drawer Header -->
                <div class="p-5 bg-indigo-900 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                        <h3 class="text-sm sm:text-base font-bold">Keranjang Belanja</h3>
                    </div>
                    <button type="button" @click="showCartDrawer = false" class="text-slate-300 hover:text-white text-xl font-bold">&times;</button>
                </div>

                <!-- Cart Items List -->
                <div class="flex-1 p-5 overflow-y-auto space-y-4">
                    <template x-if="cart.length === 0">
                        <div class="py-16 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <p class="text-sm font-semibold">Keranjang Anda masih kosong.</p>
                            <p class="text-xs mt-1">Pilih menu dari daftar kantin untuk menambahkan.</p>
                        </div>
                    </template>

                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="flex items-center space-x-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <img :src="item.image_url" :alt="item.name" class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                            <div class="flex-1 min-w-0">
                                <span x-text="item.stall_name" class="text-[9px] font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider"></span>
                                <h5 x-text="item.name" class="text-xs font-bold text-slate-900 dark:text-white truncate"></h5>
                                
                                <div class="flex items-center space-x-2">
                                    <span x-text="formatRupiah(item.price)" class="text-xs font-bold text-slate-900 dark:text-white"></span>
                                    <template x-if="item.discount_percent > 0 && item.original_price">
                                        <span x-text="formatRupiah(item.original_price)" class="text-[10px] text-slate-400 line-through"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-center space-x-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-1">
                                <button type="button" @click="updateQty(index, -1)" class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs">-</button>
                                <span x-text="item.qty" class="w-5 text-center text-xs font-bold text-slate-900 dark:text-white"></span>
                                <button type="button" @click="updateQty(index, 1)" class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-xs">+</button>
                            </div>
                        </div>
                    </template>

                    <!-- Payment Method Picker -->
                    <div x-show="cart.length > 0" class="pt-4 border-t border-slate-200 dark:border-slate-800 space-y-3">
                        <label class="block text-xs font-bold text-slate-900 dark:text-white">Metode Pembayaran</label>
                        
                        <div class="space-y-2">
                            <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all"
                                   :class="paymentMethod === 'savings_balance' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200' : 'border-slate-200 dark:border-slate-800'">
                                <div class="flex items-center space-x-3">
                                    <input type="radio" name="pay_method" value="savings_balance" x-model="paymentMethod" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <span class="text-xs font-bold block">Saldo Tabungan Siswa</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium block">Saldo: Rp {{ number_format($student->savings_balance ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded">Otomatis Lunas</span>
                            </label>

                            <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all"
                                   :class="paymentMethod === 'qris' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200' : 'border-slate-200 dark:border-slate-800'">
                                <div class="flex items-center space-x-3">
                                    <input type="radio" name="pay_method" value="qris" x-model="paymentMethod" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <span class="text-xs font-bold block">QR Code / QRIS Pertransaksi</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block">Scan QR Code saat penyerahan pesanan</span>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all"
                                   :class="paymentMethod === 'cash' ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200' : 'border-slate-200 dark:border-slate-800'">
                                <div class="flex items-center space-x-3">
                                    <input type="radio" name="pay_method" value="cash" x-model="paymentMethod" class="text-indigo-600 focus:ring-indigo-500">
                                    <div>
                                        <span class="text-xs font-bold block">Tunai di Kasir Kantin</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 block">Bayar tunai di stand saat mengambil pesanan</span>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" x-model="notes" placeholder="Contoh: Sambal dipisah" 
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-800">
                        </div>
                    </div>
                </div>

                <!-- Drawer Footer Checkout Button -->
                <div x-show="cart.length > 0" class="p-5 bg-slate-50 dark:bg-slate-850 border-t border-slate-200 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-600 dark:text-slate-400 font-semibold">Total Pembayaran:</span>
                        <span x-text="formatRupiah(totalPrice)" class="text-base font-bold text-slate-900 dark:text-white"></span>
                    </div>

                    <button type="button" 
                            @click="submitCheckout()" 
                            :disabled="isSubmitting"
                            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-xs transition-all active:scale-95 text-xs tracking-wide flex items-center justify-center space-x-2">
                        <svg x-show="isSubmitting" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Buat Pesanan & Bayar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast Notifikasi HTML -->
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-3"
        class="fixed top-4 left-1/2 -translate-x-1/2 z-[999] w-[90vw] max-w-sm"
        style="display:none;"
    >
        <div :class="{
            'bg-amber-500 text-white': toast.type === 'warning',
            'bg-rose-600 text-white': toast.type === 'error',
            'bg-indigo-600 text-white': toast.type === 'info'
        }" class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl font-semibold">
            <template x-if="toast.type === 'warning'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </template>
            <template x-if="toast.type === 'error'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </template>
            <template x-if="toast.type === 'info'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/></svg>
            </template>
            <span x-text="toast.message" class="flex-1 leading-snug text-xs"></span>
            <button @click="toast.show = false" class="ml-auto opacity-75 hover:opacity-100 text-lg leading-none">&times;</button>
        </div>
    </div>

</div>
@endsection
