@extends('layouts.student-mobile')

@section('title', 'E-Kantin Sekolah')
@section('header_title', 'E-Kantin')

@section('content')
<div x-data="{
        searchQuery: '{{ request('search') }}',
        selectedCategory: '{{ request('category', 'all') }}',
        cart: JSON.parse(localStorage.getItem('student_canteen_cart') || '[]'),
        showCartDrawer: false,
        paymentMethod: 'savings_balance',
        notes: '',
        isSubmitting: false,
        activeSlide: 0,
        totalSlides: {{ count($promoBanners) }},
        activeStallModal: null,
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
            setInterval(() => {
                this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
            }, 6000);

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

    <!-- Top Navigation Bar & Action Buttons (Identik seperti Halaman Profil Toko) -->
    <div class="flex items-center justify-between">
        <!-- Tombol Riwayat Transaksi -->
        <a href="{{ route('student.canteen.my-orders') }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold shadow-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Riwayat Transaksi</span>
        </a>

        <!-- Tombol Keranjang Belanja -->
        <button @click="showCartDrawer = true" type="button" class="relative inline-flex items-center space-x-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-xs transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            <span class="hidden sm:inline">Keranjang</span>
            <span x-show="totalItems > 0" x-text="totalItems" class="absolute -top-2 -right-2 inline-flex items-center justify-center min-w-[20px] h-[20px] px-1 bg-rose-500 text-white text-[10px] font-extrabold leading-none rounded-full ring-2 ring-white dark:ring-slate-900 shadow-sm"></span>
        </button>
    </div>

    <!-- 1. Banner Slider (Tampilan Cerah & Smooth Tanpa Text Saldo Tabungan) -->
    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800">
        <div class="relative min-h-[160px] sm:min-h-[200px]">
            @foreach($promoBanners as $idx => $banner)
                <div x-show="activeSlide === {{ $idx }}" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform translate-x-4"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-x-0"
                     x-transition:leave-end="opacity-0 transform -translate-x-4"
                     class="absolute inset-0 bg-gradient-to-r {{ $banner['bg_gradient'] }} text-white p-5 sm:p-7 flex flex-col justify-between">
                    
                    <div class="space-y-1.5 relative z-10">
                        <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 bg-white/15 backdrop-blur rounded-full text-[10px] sm:text-xs font-semibold tracking-wide text-indigo-100 border border-white/20 uppercase">
                            <span>{{ $banner['badge'] }}</span>
                        </div>
                        <h2 class="text-lg sm:text-2xl font-bold tracking-tight leading-snug">{{ $banner['title'] }}</h2>
                        <p class="text-xs text-indigo-100 max-w-xl line-clamp-2">{{ $banner['subtitle'] }}</p>
                    </div>

                    <div class="flex items-center justify-end pt-3 border-t border-white/10 relative z-10">
                        @if($banner['filter'] === 'promo')
                            <a href="{{ route('student.canteen.index', ['promo' => 1]) }}" class="px-4 py-1.5 bg-white text-indigo-950 hover:bg-slate-100 font-bold text-xs rounded-xl shadow-xs transition-all">
                                {{ $banner['btn_text'] }} &rarr;
                            </a>
                        @elseif($banner['filter'] === 'qr')
                            <button onclick="window.dispatchEvent(new CustomEvent('open-qr-modal'))" type="button" class="px-4 py-1.5 bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-xs transition-all hover:bg-indigo-600">
                                {{ $banner['btn_text'] }}
                            </button>
                        @else
                            <a href="{{ route('student.savings.index') }}" class="px-4 py-1.5 bg-white text-indigo-950 font-bold text-xs rounded-xl shadow-xs transition-all hover:bg-slate-100">
                                {{ $banner['btn_text'] }}
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Slider Dots Navigation -->
        <div class="absolute bottom-2 left-4 z-20 flex items-center space-x-1.5">
            @foreach($promoBanners as $idx => $banner)
                <button type="button" @click="activeSlide = {{ $idx }}" 
                        :class="activeSlide === {{ $idx }} ? 'w-5 bg-white' : 'w-2 bg-white/40'" 
                        class="h-1.5 rounded-full transition-all duration-300"></button>
            @endforeach
        </div>
    </div>



    <!-- Active Vendor Stand Banner (If Filtered) -->
    @if($selectedStall)
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-4 sm:p-5 shadow-xs border border-indigo-200 dark:border-indigo-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3.5">
                <img src="{{ $selectedStall->logo_url }}" alt="{{ $selectedStall->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-xs">
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">{{ $selectedStall->name }}</h3>
                        <span class="inline-flex items-center space-x-1 px-2 py-0.5 text-[9px] font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-md border border-amber-200 dark:border-amber-900">
                            <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span>{{ $selectedStall->rating }}</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">{{ $selectedStall->description }}</p>
                </div>
            </div>
            <a href="{{ route('student.canteen.index') }}" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 transition-colors">
                Tampilkan Semua Stand
            </a>
        </div>
    @endif

    <!-- 3. Search Bar & Category Filter Pills (Desain Smooth & Soft) -->
    <div class="space-y-3">
        <!-- Search input -->
        <form method="GET" action="{{ route('student.canteen.index') }}" class="relative">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('stall'))
                <input type="hidden" name="stall" value="{{ request('stall') }}">
            @endif
            @if(request('promo'))
                <input type="hidden" name="promo" value="{{ request('promo') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari makanan, minuman, atau menu promo..." 
                   class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-indigo-500 shadow-xs transition-all">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-slate-400 absolute left-3.5 top-3 sm:top-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </form>

        <!-- Category Horizontal Scroll Pills + Diskon Promo Pill (Proportional & Smooth Scroll) -->
        <div class="flex items-center space-x-2 overflow-x-auto py-1 px-0.5 scrollbar-none touch-pan-x" style="-ms-overflow-style: none; scrollbar-width: none;">
            <a href="{{ route('student.canteen.index', array_filter(['search' => request('search'), 'stall' => request('stall')])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all border shrink-0 {{ !request('category') && !request('promo') ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-indigo-300' }}">
                Semua Menu
            </a>

            <!-- Diskon Filter Pill (Red Theme) -->
            <a href="{{ route('student.canteen.index', array_filter(['promo' => 1, 'search' => request('search'), 'stall' => request('stall')])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all border shrink-0 flex items-center space-x-1.5 {{ request('promo') == 1 ? 'bg-red-600 text-white border-red-600 shadow-xs' : 'bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 border-red-200 dark:border-red-900 hover:border-red-400' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span>Diskon Promo</span>
            </a>

            @foreach($categories as $cat)
                <a href="{{ route('student.canteen.index', array_filter(['category' => $cat->slug, 'search' => request('search'), 'stall' => request('stall')])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all border shrink-0 {{ request('category') === $cat->slug ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:border-indigo-300' }}">
                    {{ $cat->name }} ({{ $cat->active_items_count }})
                </a>
            @endforeach
        </div>
    </div>

    <!-- 4. Product Grid Section: 2 Columns on Mobile (Smooth Light Design, No Emoji) -->
    <div>
        <div class="flex items-center justify-between mb-3 px-1">
            <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">Daftar Menu Kantin</h3>
            <span class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium">Menampilkan {{ $items->count() }} dari {{ $items->total() }} Menu</span>
        </div>

        @if($items->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($items as $item)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs hover:border-indigo-300 dark:hover:border-indigo-700 transition-all group flex flex-col justify-between relative">
                        
                        <!-- Discount Badge Over Image (Bright Red) -->
                        @if($item->discount_percent > 0)
                            <div class="absolute top-2 left-2 z-10 bg-red-600 text-white font-black text-[9px] uppercase tracking-wider px-2 py-0.5 rounded-md shadow-sm">
                                DISKON {{ $item->discount_percent }}%
                            </div>
                        @endif

                        <a href="{{ route('student.canteen.stall', $item->stall?->slug ?? \Illuminate\Support\Str::slug($item->stall_name)) }}" class="flex-1 flex flex-col">
                            <!-- Product Image & Badges -->
                            <div class="relative h-32 sm:h-40 w-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                
                                @if($item->discount_percent <= 0)
                                    <div class="absolute top-2 left-2 px-2 py-0.5 bg-slate-900/70 backdrop-blur-md rounded-md text-[9px] font-semibold text-white uppercase tracking-wider max-w-[70%] truncate">
                                        {{ $item->stall_name }}
                                    </div>
                                @endif
                                
                                <div class="absolute top-2 right-2 px-2 py-0.5 rounded-md text-[9px] font-bold {{ $item->stock > 0 ? 'bg-emerald-600 text-white' : 'bg-slate-600 text-white' }}">
                                    {{ $item->stock > 0 ? 'Stok ' . $item->stock : 'Habis' }}
                                </div>
                            </div>

                            <!-- Product Info & Pricing -->
                            <div class="p-3 sm:p-4 flex-1 flex flex-col justify-between space-y-3">
                                <div class="space-y-1">
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider text-[9px] block">{{ $item->category?->name }}</span>
                                    
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white leading-snug line-clamp-1 sm:line-clamp-2">{{ $item->name }}</h4>
                                    
                                    <!-- Nama Kantin & Rating Langsung di Bawah Nama Produk -->
                                    <div class="flex items-center justify-between text-[10px] pt-0.5">
                                        <div class="font-medium text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 truncate flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-indigo-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            <span class="truncate">{{ $item->stall_name ?? $item->stall?->name }}</span>
                                        </div>

                                        <span class="inline-flex items-center space-x-0.5 text-amber-500 font-bold shrink-0">
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span>{{ $item->stall?->rating ?? '4.8' }}</span>
                                        </span>
                                    </div>

                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 hidden sm:line-clamp-2 leading-relaxed pt-0.5">{{ $item->description }}</p>
                                </div>
                            </div>
                        </a>

                        <div class="p-3 sm:p-4 pt-0 sm:pt-0">
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

            <!-- 5. Pagination Links -->
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
                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Tidak ada menu ditemukan</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Coba gunakan kata kunci pencarian lain atau pilih kategori lain.</p>
            </div>
        @endif
    </div>

    <!-- 6. Vendor Stand Profile Modal (Desain Soft & Smooth Tanpa Emoji) -->
    <div x-show="activeStallModal !== null" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="activeStallModal = null"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left align-middle shadow-xl transition-all w-full max-w-lg border border-slate-200 dark:border-slate-800">
                
                <template x-if="activeStallModal">
                    <div>
                        <!-- Banner Header -->
                        <div class="relative h-36 w-full bg-slate-800">
                            <img :src="activeStallModal.banner_url || 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1000&q=80'" alt="Vendor Banner" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent"></div>
                            
                            <button type="button" @click="activeStallModal = null" class="absolute top-3 right-3 w-8 h-8 rounded-full bg-slate-900/70 hover:bg-slate-900 text-white flex items-center justify-center text-lg font-bold">
                                &times;
                            </button>

                            <div class="absolute bottom-3 left-4 right-4 flex items-end justify-between">
                                <div class="flex items-end space-x-3">
                                    <img :src="activeStallModal.logo_url || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=300&q=80'" alt="Vendor Logo" class="w-12 h-12 rounded-xl object-cover border border-white shadow-md">
                                    <div class="text-white">
                                        <h3 class="text-base font-bold leading-snug" x-text="activeStallModal.name"></h3>
                                        <p class="text-[11px] text-indigo-200">Pemilik: <span x-text="activeStallModal.owner_name || '-'"></span></p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center space-x-1 px-2 py-0.5 bg-white text-slate-900 font-bold text-xs rounded-lg shadow-xs">
                                    <svg class="w-3 h-3 fill-amber-500" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span x-text="activeStallModal.rating"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Content Details -->
                        <div class="p-5 space-y-4">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs space-y-1">
                                <span class="font-bold text-slate-500 dark:text-slate-400 block text-[10px] uppercase">Tentang Stand:</span>
                                <p x-text="activeStallModal.description" class="text-slate-700 dark:text-slate-300 leading-relaxed"></p>
                                <div class="pt-2 flex justify-between text-[11px] text-slate-500 border-t border-slate-200 dark:border-slate-700 mt-2">
                                    <span>Jam Buka: <strong x-text="activeStallModal.operating_hours"></strong></span>
                                    <span>Kontak: <strong x-text="activeStallModal.phone || '-'"></strong></span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <a :href="'{{ route('student.canteen.index') }}?stall=' + activeStallModal.slug" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs text-center rounded-xl shadow-xs transition-colors">
                                    Lihat Semua Menu Dari Stand Ini &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Mobile Sticky Quick Checkout Bar (Desain Light Smooth) -->
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
        }" class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl text-sm font-semibold">
            <!-- icon -->
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
