@extends('layouts.canteen-vendor')

@section('title', 'POS Kasir Kantin')
@section('header_title', 'Kasir POS Kantin')

@section('content')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<div x-data="{
        products: {{ json_encode($products) }},
        categories: {{ json_encode($categories) }},
        cart: [],
        selectedCategory: 'all',
        searchQuery: '',
        
        // Payment Method & Cash State
        paymentMethod: 'savings_balance', // 'savings_balance', 'qris', 'cash'
        paidAmount: 0,

        // Student & Tabungan State
        selectedStudent: null,
        studentSearchQuery: '',
        studentSearchResults: [],
        isSearchingStudent: false,

        // KTS Live Camera Scanner Modal State
        showKtsScannerModal: false,
        ktsHtml5QrCode: null,
        isKtsCameraActive: false,
        ktsScannerError: '',

        // PIN Confirmation Modal State
        showPinModal: false,
        inputPin: '',
        pinErrorMessage: '',

        // Processing & Modal States
        isSubmitting: false,
        errorMessage: '',
        receiptData: null,

        // QR Code Modal & Polling State
        qrModalData: null,
        pollingTimer: null,
        isCheckingStatus: false,
        isPaidSuccess: false,

        // Mobile Cart Drawer State
        showMobileCart: false,

        get filteredProducts() {
            return this.products.filter(p => {
                const matchCategory = this.selectedCategory === 'all' || p.category_id == this.selectedCategory;
                const matchSearch = p.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                return matchCategory && matchSearch;
            });
        },

        get totalItemsCount() {
            return this.cart.reduce((sum, i) => sum + i.quantity, 0);
        },

        get totalCartAmount() {
            return this.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        },

        get changeAmount() {
            if (this.paymentMethod !== 'cash') return 0;
            const diff = (parseFloat(this.paidAmount) || 0) - this.totalCartAmount;
            return diff > 0 ? diff : 0;
        },

        get remainingSavingsAfterPayment() {
            if (!this.selectedStudent) return 0;
            const rem = this.selectedStudent.savings_balance - this.totalCartAmount;
            return rem >= 0 ? rem : 0;
        },

        addToCart(product) {
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.quantity < product.stock) {
                    existing.quantity++;
                }
            } else {
                if (product.stock > 0) {
                    this.cart.push({
                        id: product.id,
                        name: product.name,
                        price: parseFloat(product.price),
                        quantity: 1,
                        max_stock: product.stock,
                        image_url: product.image_url
                    });
                }
            }
        },

        updateCartQty(index, qty) {
            if (qty <= 0) {
                this.cart.splice(index, 1);
            } else {
                if (qty <= this.cart[index].max_stock) {
                    this.cart[index].quantity = qty;
                }
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            this.cart = [];
            this.paidAmount = 0;
            this.errorMessage = '';
            this.stopPolling();
            this.showMobileCart = false;
        },

        setPresetCash(amount) {
            if (amount === 'exact') {
                this.paidAmount = this.totalCartAmount;
            } else {
                this.paidAmount = amount;
            }
        },

        searchStudentsList() {
            if (!this.studentSearchQuery.trim()) {
                this.studentSearchResults = [];
                return;
            }
            this.isSearchingStudent = true;
            fetch('{{ route('canteen.vendor.pos.search-students') }}?q=' + encodeURIComponent(this.studentSearchQuery))
                .then(res => res.json())
                .then(data => {
                    this.isSearchingStudent = false;
                    this.studentSearchResults = data;
                })
                .catch(() => {
                    this.isSearchingStudent = false;
                });
        },

        selectStudent(studentObj) {
            this.selectedStudent = studentObj;
            this.studentSearchResults = [];
            this.studentSearchQuery = '';
            this.errorMessage = '';
            if (window.innerWidth < 1024) {
                this.showMobileCart = true;
            }
        },

        clearStudent() {
            this.selectedStudent = null;
            this.studentSearchQuery = '';
            this.studentSearchResults = [];
        },

        startKtsCamera() {
            this.showMobileCart = false;
            this.showKtsScannerModal = true;
            this.isKtsCameraActive = true;
            this.ktsScannerError = '';

            this.$nextTick(() => {
                if (!this.ktsHtml5QrCode) {
                    this.ktsHtml5QrCode = new Html5Qrcode('kts-qr-reader');
                }
                const config = { fps: 10, qrbox: { width: 220, height: 220 } };
                this.ktsHtml5QrCode.start(
                    { facingMode: 'environment' },
                    config,
                    (decodedText) => {
                        this.stopKtsCamera();
                        this.lookupStudentByQr(decodedText);
                    },
                    () => {}
                ).catch(err => {
                    this.ktsScannerError = 'Tidak dapat mengakses kamera: ' + err;
                    this.isKtsCameraActive = false;
                });
            });
        },

        stopKtsCamera() {
            if (this.ktsHtml5QrCode && this.isKtsCameraActive) {
                this.ktsHtml5QrCode.stop().then(() => {
                    this.isKtsCameraActive = false;
                    this.showKtsScannerModal = false;
                }).catch(() => {
                    this.isKtsCameraActive = false;
                    this.showKtsScannerModal = false;
                });
            } else {
                this.isKtsCameraActive = false;
                this.showKtsScannerModal = false;
            }
        },

        lookupStudentByQr(qrData) {
            fetch('{{ route('canteen.vendor.pos.lookup-student') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_data: qrData })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.student) {
                    this.selectStudent(data.student);
                    this.showKtsScannerModal = false;
                } else {
                    this.errorMessage = data.message || 'QR Code Siswa tidak ditemukan.';
                }
            })
            .catch(() => {
                this.errorMessage = 'Gagal memverifikasi QR Code Siswa.';
            });
        },

        openPinModal() {
            if (this.cart.length === 0) {
                this.errorMessage = 'Keranjang POS masih kosong! Silakan pilih menu makanan/minuman.';
                return;
            }
            if (!this.selectedStudent) {
                this.errorMessage = 'Silakan pilih atau scan QR siswa terlebih dahulu.';
                return;
            }
            if (this.selectedStudent.savings_balance < this.totalCartAmount) {
                this.errorMessage = 'Saldo tabungan siswa tidak mencukupi untuk total tagihan transaksi ini.';
                return;
            }
            this.inputPin = '';
            this.pinErrorMessage = '';
            this.errorMessage = '';
            this.showPinModal = true;
        },

        appendPinDigit(digit) {
            if (this.inputPin.length < 6) {
                this.inputPin += digit;
                if (this.inputPin.length === 6) {
                    this.submitPinPayment();
                }
            }
        },

        backspacePin() {
            this.inputPin = this.inputPin.slice(0, -1);
        },

        clearPin() {
            this.inputPin = '';
        },

        submitPinPayment() {
            if (this.inputPin.length !== 6) {
                this.pinErrorMessage = 'PIN Keamanan wajib 6 digit angka.';
                return;
            }
            this.processCheckout();
        },

        startPolling(orderId) {
            this.stopPolling();
            this.pollingTimer = setInterval(() => {
                this.checkOrderStatus(orderId);
            }, 2500);
        },

        stopPolling() {
            if (this.pollingTimer) {
                clearInterval(this.pollingTimer);
                this.pollingTimer = null;
            }
        },

        checkOrderStatus(orderId) {
            fetch('{{ url('/canteen-vendor/pos/order-status') }}/' + orderId)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.is_paid) {
                        this.stopPolling();
                        this.isPaidSuccess = true;
                        
                        if (this.qrModalData) {
                            this.qrModalData.student_name = data.student_name || 'Siswa';
                            
                            this.receiptData = Object.assign({}, this.qrModalData.receipt);
                            this.receiptData.student_name = data.student_name || 'Siswa';
                        }

                        setTimeout(() => {
                            this.qrModalData = null;
                            this.isPaidSuccess = false;
                        }, 2000);
                    }
                })
                .catch(() => {});
        },

        processCheckout() {
            if (this.cart.length === 0) {
                this.errorMessage = 'Keranjang POS masih kosong! Silakan pilih menu makanan/minuman.';
                return;
            }

            if (this.paymentMethod === 'cash' && (parseFloat(this.paidAmount) || 0) < this.totalCartAmount) {
                this.errorMessage = 'Nominal uang tunai kurang dari total tagihan.';
                return;
            }

            if (this.paymentMethod === 'savings_balance') {
                if (!this.selectedStudent) {
                    this.errorMessage = 'Silakan pilih atau scan QR siswa terlebih dahulu.';
                    return;
                }
                if (!this.inputPin || this.inputPin.length !== 6) {
                    this.pinErrorMessage = 'PIN Keamanan Siswa 6-digit wajib dimasukkan.';
                    return;
                }
            }

            this.errorMessage = '';
            this.pinErrorMessage = '';
            this.isSubmitting = true;

            const payload = {
                items: this.cart.map(i => ({ id: i.id, quantity: i.quantity })),
                payment_method: this.paymentMethod,
                paid_amount: this.paymentMethod === 'cash' ? (parseFloat(this.paidAmount) || 0) : this.totalCartAmount,
                student_id: this.paymentMethod === 'savings_balance' ? this.selectedStudent?.id : null,
                pin: this.paymentMethod === 'savings_balance' ? this.inputPin : null
            };

            fetch('{{ route('canteen.vendor.pos.checkout') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(res => {
                this.isSubmitting = false;
                if (res.status === 200 && res.body.success) {
                    if (this.paymentMethod === 'qris') {
                        // Show QR Code Modal to Student
                        this.qrModalData = {
                            order_id: res.body.order_id,
                            qr_code: res.body.qr_code,
                            qr_image_url: res.body.qr_image_url,
                            receipt: res.body.receipt
                        };
                        this.startPolling(res.body.order_id);
                        this.clearCart();
                    } else if (this.paymentMethod === 'savings_balance') {
                        // Tabungan payment complete
                        this.showPinModal = false;
                        this.receiptData = res.body.receipt;
                        this.clearCart();
                        this.clearStudent();
                        this.clearPin();
                    } else {
                        // Cash payment complete
                        this.receiptData = res.body.receipt;
                        this.clearCart();
                    }
                } else {
                    const msg = res.body.message || 'Terjadi kesalahan saat memproses transaksi.';
                    if (this.paymentMethod === 'savings_balance' && this.showPinModal) {
                        this.pinErrorMessage = msg;
                    } else {
                        this.errorMessage = msg;
                    }
                }
            })
            .catch(err => {
                this.isSubmitting = false;
                if (this.paymentMethod === 'savings_balance' && this.showPinModal) {
                    this.pinErrorMessage = 'Terjadi gangguan koneksi server.';
                } else {
                    this.errorMessage = 'Terjadi gangguan koneksi server.';
                }
            });
        }
    }" 
    class="pb-24 w-full space-y-4">

    <!-- POS Header Banner Card -->
    <div class="text-white rounded-2xl sm:rounded-3xl p-3.5 sm:p-5 shadow-md flex items-center justify-between gap-3 border border-white/10" style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#6366f1') }} 0%, {{ Setting::get('secondary_color', '#4f46e5') }} 100%);">
        <div class="flex items-center space-x-2.5 sm:space-x-3 min-w-0">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-white/20 text-white flex items-center justify-center font-black shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-xs sm:text-base font-extrabold truncate">Kasir Point of Sale (POS)</h2>
                <p class="text-[10px] sm:text-xs text-indigo-200 truncate">Kantin {{ $stall->name }} &bull; Scan QR Tabungan & Tunai</p>
            </div>
        </div>

        <div class="flex items-center space-x-1.5 sm:space-x-2 shrink-0">
            <button type="button" @click="showMobileCart = true" class="lg:hidden px-2.5 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-extrabold flex items-center space-x-1 shadow-xs transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span>Keranjang (<span x-text="totalItemsCount"></span>)</span>
            </button>
            <button type="button" @click="clearCart(); clearStudent()" class="px-2.5 py-1.5 sm:px-3.5 sm:py-2 rounded-xl sm:rounded-2xl bg-white/10 hover:bg-white/20 text-[11px] sm:text-xs font-bold transition-all flex items-center space-x-1 sm:space-x-1.5 shrink-0">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Reset</span>
                <span class="hidden sm:inline"> Form</span>
            </button>
        </div>
    </div>

    <!-- Error Alert Box -->
    <template x-if="errorMessage">
        <div class="p-4 rounded-2xl bg-rose-500/10 dark:bg-rose-950/60 border border-rose-500/30 text-rose-800 dark:text-rose-200 text-xs font-bold flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="errorMessage"></span>
            </div>
            <button type="button" @click="errorMessage = ''" class="text-rose-500 hover:text-rose-700 text-lg font-bold">&times;</button>
        </div>
    </template>

    <!-- Main POS Workspace Grid: 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- LEFT COLUMN: Catalog & Filters (7 Columns) -->
        <div class="lg:col-span-7 space-y-4">
            
            <!-- Search & Category Filters Bar -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Cari makanan atau minuman..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Category Filter Pills -->
                <div class="flex items-center space-x-1.5 overflow-x-auto scrollbar-none text-xs font-bold pb-0.5">
                    <button type="button" 
                            @click="selectedCategory = 'all'" 
                            class="px-3.5 py-2 rounded-xl shrink-0 transition-all"
                            :class="selectedCategory === 'all' ? 'bg-indigo-600 text-white shadow-xs font-extrabold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'">
                        Semua Kategori
                    </button>
                    <template x-for="cat in categories" :key="cat.id">
                        <button type="button" 
                                @click="selectedCategory = cat.id" 
                                class="px-3.5 py-2 rounded-xl shrink-0 transition-all"
                                :class="selectedCategory == cat.id ? 'bg-indigo-600 text-white shadow-xs font-extrabold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200'"
                                x-text="cat.name">
                        </button>
                    </template>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 gap-2.5">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div @click="addToCart(product)" 
                         :class="product.stock <= 0 ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer hover:border-indigo-400 hover:shadow-md'"
                         class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs transition-all flex flex-col overflow-hidden group">
                        
                        <!-- Product Image -->
                        <div class="relative w-full aspect-square bg-indigo-50/50 dark:bg-indigo-950/30 flex items-center justify-center overflow-hidden">
                            <template x-if="product.image_url">
                                <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </template>
                            <template x-if="!product.image_url">
                                <div class="w-full h-full flex flex-col items-center justify-center text-indigo-300 dark:text-indigo-600">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-[8px] font-bold text-slate-400 mt-1">No Image</span>
                                </div>
                            </template>

                            <!-- Stock Badge -->
                            <span class="absolute top-1.5 right-1.5 px-1.5 py-0.5 rounded-lg text-[8px] font-black shadow-sm leading-none"
                                  :class="product.stock <= 0 ? 'bg-slate-400 text-white' : product.stock <= 5 ? 'bg-amber-500 text-white' : 'bg-emerald-500 text-white'"
                                  x-text="product.stock <= 0 ? 'Habis' : product.stock + ' stok'">
                            </span>
                        </div>

                        <!-- Product Info -->
                        <div class="p-2.5 flex flex-col gap-1.5 flex-1">
                            <span class="text-[8px] font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-wider leading-none truncate" x-text="product.category ? product.category.name : 'Menu'"></span>
                            <h4 class="text-[11px] font-extrabold text-slate-900 dark:text-white line-clamp-2 leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" x-text="product.name"></h4>

                            <!-- Price & Add Button -->
                            <div class="flex items-center justify-between mt-auto pt-1.5 border-t border-slate-100 dark:border-slate-800">
                                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400" x-text="'Rp ' + parseInt(product.price).toLocaleString('id-ID')"></span>
                                <button type="button" 
                                        :disabled="product.stock <= 0"
                                        class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-black text-sm flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <template x-if="filteredProducts.length === 0">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 text-center border border-slate-200 dark:border-slate-800 space-y-2">
                    <p class="text-xs font-bold text-slate-500">Tidak ada produk ditemukan.</p>
                </div>
            </template>
        </div>

        <!-- RIGHT COLUMN: Cart & Checkout Panel (5 Columns, Sticky on Desktop) -->
        <div class="lg:col-span-5 lg:sticky lg:top-20">
            
            <div :class="{
                     'fixed inset-x-0 bottom-0 bg-white dark:bg-slate-900 rounded-t-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.2)] p-4 pb-6 max-h-[85vh] overflow-y-auto z-50 block': showMobileCart,
                     'hidden lg:block lg:bg-white lg:dark:bg-slate-900 lg:rounded-3xl lg:p-5 lg:border lg:border-slate-200/80 lg:dark:border-slate-800 lg:shadow-xs space-y-4': !showMobileCart
                 }">
                
                <!-- Mobile Drawer Header -->
                <div class="lg:hidden flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Detail Pesanan</span>
                    <button type="button" @click="showMobileCart = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-extrabold flex items-center justify-center hover:bg-slate-200">
                        &times;
                    </button>
                </div>

                <!-- Cart Header -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">Keranjang POS</h3>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300" x-text="totalItemsCount + ' Item'"></span>
                </div>

                <!-- Cart Items List -->
                <div class="space-y-2 max-h-48 overflow-y-auto scrollbar-none pr-1">
                    <template x-for="(item, idx) in cart" :key="item.id">
                        <div class="p-2.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between space-x-2">
                            <div class="min-w-0 flex-1">
                                <h5 class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="item.name"></h5>
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-extrabold" x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></span>
                            </div>

                            <div class="flex items-center space-x-1 shrink-0 bg-white dark:bg-slate-900 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                                <button type="button" @click="updateCartQty(idx, item.quantity - 1)" class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center">-</button>
                                <span class="w-6 text-center text-xs font-black text-slate-900 dark:text-white" x-text="item.quantity"></span>
                                <button type="button" @click="updateCartQty(idx, item.quantity + 1)" class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-emerald-50 hover:text-emerald-600 flex items-center justify-center">+</button>
                            </div>
                        </div>
                    </template>

                    <template x-if="cart.length === 0">
                        <div class="p-6 text-center text-xs text-slate-400 font-medium">
                            Klik menu produk untuk menambahkan ke keranjang.
                        </div>
                    </template>
                </div>

                <!-- Payment Method Segmented Switcher (Tabungan Siswa vs Scan QR vs Tunai) -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Pilih Metode Pembayaran</label>
                        <div class="bg-slate-100 dark:bg-slate-800/80 p-1 rounded-2xl grid grid-cols-3 gap-1 text-[11px] font-bold">
                            <button type="button" 
                                    @click="paymentMethod = 'savings_balance'"
                                    class="py-2.5 rounded-xl transition-all text-center flex flex-col sm:flex-row items-center justify-center space-x-1"
                                    :class="paymentMethod === 'savings_balance' ? 'bg-indigo-600 text-white shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Tabungan Siswa</span>
                            </button>
                            <button type="button" 
                                    @click="paymentMethod = 'qris'"
                                    class="py-2.5 rounded-xl transition-all text-center flex flex-col sm:flex-row items-center justify-center space-x-1"
                                    :class="paymentMethod === 'qris' ? 'bg-purple-600 text-white shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span>Scan QR</span>
                            </button>
                            <button type="button" 
                                    @click="paymentMethod = 'cash'"
                                    class="py-2.5 rounded-xl transition-all text-center flex flex-col sm:flex-row items-center justify-center space-x-1"
                                    :class="paymentMethod === 'cash' ? 'bg-emerald-600 text-white shadow-xs font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Tunai</span>
                            </button>
                        </div>
                    </div>

                    <!-- TABUNGAN SISWA / SCAN QR PANEL -->
                    <template x-if="paymentMethod === 'savings_balance'">
                        <div class="space-y-2.5 bg-indigo-50/60 dark:bg-indigo-950/40 p-3 rounded-2xl border border-indigo-100 dark:border-indigo-900/60">
                            
                            <!-- State 1: No Student Selected -->
                            <template x-if="!selectedStudent">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Pilih / Scan QR Siswa</span>
                                        <button type="button" @click="startKtsCamera()" class="px-2.5 py-1 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-extrabold flex items-center space-x-1 shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span>Scan QR Siswa</span>
                                        </button>
                                    </div>

                                    <!-- Student Search Box -->
                                    <div class="relative">
                                        <input type="text" 
                                               x-model="studentSearchQuery" 
                                               @input.debounce.300ms="searchStudentsList()"
                                               placeholder="Ketik Nama / NISN / Kode Siswa..." 
                                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-indigo-500">
                                        
                                        <!-- Search Dropdown Results -->
                                        <div x-show="studentSearchResults.length > 0" 
                                             x-cloak
                                             class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 max-h-48 overflow-y-auto z-30 p-1 space-y-1">
                                            <template x-for="st in studentSearchResults" :key="st.id">
                                                <div @click="selectStudent(st)" 
                                                     class="p-2 hover:bg-indigo-50 dark:hover:bg-slate-800 rounded-xl cursor-pointer flex items-center justify-between text-xs transition-colors">
                                                    <div class="flex items-center space-x-2">
                                                        <img :src="st.photo_url" class="w-7 h-7 rounded-full object-cover border border-indigo-200">
                                                        <div>
                                                            <p class="font-extrabold text-slate-900 dark:text-white" x-text="st.name"></p>
                                                            <p class="text-[9px] text-slate-400" x-text="st.class_name + ' &bull; NISN: ' + st.nisn"></p>
                                                        </div>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded-lg">Pilih</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- State 2: Student Selected -->
                            <template x-if="selectedStudent">
                                <div class="space-y-2">
                                    <div class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-800 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 min-w-0">
                                            <img :src="selectedStudent.photo_url" class="w-9 h-9 rounded-full object-cover border-2 border-indigo-500 shrink-0">
                                            <div class="min-w-0">
                                                <h5 class="text-xs font-black text-slate-900 dark:text-white truncate" x-text="selectedStudent.name"></h5>
                                                <p class="text-[9.5px] text-slate-400 truncate" x-text="selectedStudent.class_name + ' &bull; NISN: ' + selectedStudent.nisn"></p>
                                                <div class="mt-0.5">
                                                    <span class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/70 px-1.5 py-0.5 rounded-md border border-emerald-200/60">
                                                        Tabungan Terhubung
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" @click="clearStudent()" class="px-2 py-1 bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-600 rounded-lg text-[10px] font-bold shrink-0 transition-colors">
                                            Ganti
                                        </button>
                                    </div>

                                    <!-- Insufficient Balance Warning -->
                                    <template x-if="selectedStudent.savings_balance < totalCartAmount">
                                        <div class="p-2 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-[10px] font-bold flex items-center space-x-1.5">
                                            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span>Saldo Tabungan tidak mencukupi untuk total tagihan ini.</span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                        </div>
                    </template>

                    <!-- Cash Input Preset (If Cash Selected) -->
                    <template x-if="paymentMethod === 'cash'">
                        <div class="space-y-2 bg-slate-50 dark:bg-slate-800/40 p-3 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300">Nominal Tunai Diterima (Rp)</label>
                            <input type="number" 
                                   x-model="paidAmount" 
                                   placeholder="0" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-sm font-black text-slate-900 dark:text-white">

                            <!-- Quick Preset Cash Buttons -->
                            <div class="grid grid-cols-5 gap-1 text-[10px] font-bold">
                                <button type="button" @click="setPresetCash('exact')" class="py-1 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 hover:bg-indigo-50 text-center truncate">Uang Pas</button>
                                <button type="button" @click="setPresetCash(10000)" class="py-1 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 hover:bg-indigo-50 text-center">10k</button>
                                <button type="button" @click="setPresetCash(20000)" class="py-1 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 hover:bg-indigo-50 text-center">20k</button>
                                <button type="button" @click="setPresetCash(50000)" class="py-1 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 hover:bg-indigo-50 text-center">50k</button>
                                <button type="button" @click="setPresetCash(100000)" class="py-1 rounded-lg bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600 hover:bg-indigo-50 text-center">100k</button>
                            </div>

                            <div class="flex items-center justify-between text-xs font-bold pt-1">
                                <span class="text-slate-500">Kembalian:</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-extrabold text-sm" x-text="'Rp ' + changeAmount.toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Total Summary & Submit Button -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-700 dark:text-slate-300">TOTAL TAGIHAN:</span>
                        <span class="text-base sm:text-lg font-black text-indigo-600 dark:text-indigo-400" x-text="'Rp ' + totalCartAmount.toLocaleString('id-ID')"></span>
                    </div>

                    <button type="button" 
                            @click="paymentMethod === 'savings_balance' ? openPinModal() : processCheckout()" 
                            :disabled="isSubmitting || cart.length === 0 || (paymentMethod === 'savings_balance' && (!selectedStudent || selectedStudent.savings_balance < totalCartAmount))" 
                            class="w-full py-3.5 rounded-2xl text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center space-x-2 disabled:opacity-50"
                            :class="paymentMethod === 'savings_balance' ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-600/30' : paymentMethod === 'qris' ? 'bg-purple-600 hover:bg-purple-700 shadow-purple-600/30' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/30'">
                        <template x-if="!isSubmitting">
                            <span class="flex items-center space-x-2">
                                <template x-if="paymentMethod === 'savings_balance'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </template>
                                <template x-if="paymentMethod === 'qris'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                </template>
                                <template x-if="paymentMethod === 'cash'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </template>
                                <span x-text="paymentMethod === 'savings_balance' ? 'Konfirmasi PIN & Bayar' : paymentMethod === 'qris' ? 'Buat QR Transaksi' : 'Proses Bayar Tunai'"></span>
                            </span>
                        </template>
                        <template x-if="isSubmitting">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Memproses...</span>
                            </span>
                        </template>
                    </button>
                </div>

            </div>

    </div>

    <!-- MOBILE CART BACKDROP OVERLAY -->
    <div x-show="showMobileCart" 
         x-cloak
         @click="showMobileCart = false"
         class="lg:hidden fixed inset-0 bg-slate-900/75 backdrop-blur-xs z-45">
    </div>

    <!-- FLOATING BOTTOM BAR FOR MOBILE CART TRIGGER -->
    <div x-show="!showMobileCart" 
         class="lg:hidden fixed bottom-[68px] inset-x-3.5 z-50 transition-all duration-300 max-w-md mx-auto"
         x-cloak>
        <button type="button" 
                @click="showMobileCart = true"
                class="w-full bg-slate-900/95 dark:bg-indigo-600/95 backdrop-blur-xl text-white rounded-full py-2.5 px-3.5 shadow-2xl shadow-slate-900/30 flex items-center justify-between font-bold transition-all active:scale-95 border border-white/15">
            <div class="flex items-center space-x-2.5 min-w-0">
                <div class="relative w-8 h-8 rounded-full bg-indigo-600 dark:bg-white/20 text-white flex items-center justify-center shrink-0 shadow-inner">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span x-show="totalItemsCount > 0" class="absolute -top-1 -right-1 bg-emerald-500 text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center font-black shadow-xs ring-2 ring-slate-900" x-text="totalItemsCount"></span>
                </div>
                <div class="text-left min-w-0">
                    <p class="text-[9.5px] font-black text-indigo-300 dark:text-indigo-100 uppercase tracking-wider leading-none" x-text="totalItemsCount > 0 ? (totalItemsCount + ' Menu Terpilih') : 'Keranjang POS'"></p>
                    <p class="text-xs font-bold text-slate-200 dark:text-white truncate leading-tight mt-0.5" x-text="selectedStudent ? selectedStudent.name : 'Pilih Siswa / Scan QR'"></p>
                </div>
            </div>
            <div class="flex items-center space-x-2 shrink-0 ml-2">
                <span class="text-xs font-black text-emerald-400 dark:text-emerald-300" x-text="totalItemsCount > 0 ? ('Rp ' + totalCartAmount.toLocaleString('id-ID')) : 'Buka'"></span>
                <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-slate-300 dark:text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                    </svg>
                </div>
            </div>
        </button>
    </div>

    <!-- LIVE CAMERA SCANNER MODAL -->
    <div x-show="showKtsScannerModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto" style="z-index: 9999;" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/85 backdrop-blur-md" @click="stopKtsCamera()"></div>

        <div class="relative transform rounded-3xl bg-white dark:bg-slate-900 p-6 text-center shadow-2xl transition-all w-full max-w-sm border border-slate-200 dark:border-slate-800 space-y-4 my-auto z-10">
            <div class="space-y-1">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center mx-auto shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Scan Barcode / QR Code Siswa</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Arahkan kamera ke QR Code Siswa.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border-2 border-indigo-500 bg-slate-950 p-1 relative shadow-inner">
                <div id="kts-qr-reader" class="w-full"></div>
            </div>

            <template x-if="ktsScannerError">
                <div class="p-2.5 rounded-xl bg-rose-500/10 text-rose-600 text-xs font-bold" x-text="ktsScannerError"></div>
            </template>

            <button type="button" @click="stopKtsCamera()" class="w-full py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200">
                Batal / Tutup Kamera
            </button>
        </div>
    </div>

    <!-- STUDENT PIN CONFIRMATION MODAL -->
    <div x-show="showPinModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto" style="z-index: 9999;" role="dialog" aria-modal="true" @keydown.window="if(showPinModal && $event.key >= '0' && $event.key <= '9') appendPinDigit($event.key); else if(showPinModal && $event.key === 'Backspace') backspacePin();">
        <div class="fixed inset-0 bg-slate-900/85 backdrop-blur-md" @click="showPinModal = false"></div>

        <div class="relative transform rounded-3xl bg-white dark:bg-slate-900 p-6 text-center shadow-2xl transition-all w-full max-w-sm border border-slate-200 dark:border-slate-800 space-y-4 my-auto z-10">
            
            <div class="space-y-1">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center mx-auto shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Verifikasi PIN Keamanan Siswa</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Silakan minta siswa memasukkan 6-digit PIN keamanan.</p>
            </div>

            <!-- Student & Transaction Summary Box -->
            <div class="p-3 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900 space-y-2 text-left">
                <div class="flex items-center space-x-2.5">
                    <img :src="selectedStudent?.photo_url" class="w-8 h-8 rounded-full object-cover border border-indigo-400">
                    <div>
                        <p class="text-xs font-extrabold text-slate-900 dark:text-white" x-text="selectedStudent?.name"></p>
                        <p class="text-[9px] text-slate-400" x-text="selectedStudent?.class_name"></p>
                    </div>
                </div>

                <div class="pt-2 border-t border-indigo-100 dark:border-indigo-900/60 grid grid-cols-2 gap-2 text-[10px] font-bold">
                    <div>
                        <span class="text-slate-400 block">Total Tagihan:</span>
                        <span class="text-xs font-black text-indigo-600 dark:text-indigo-400" x-text="'Rp ' + totalCartAmount.toLocaleString('id-ID')"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 block">Metode Pembayaran:</span>
                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">Tabungan Siswa</span>
                    </div>
                </div>
            </div>

            <!-- PIN Masked Display Dots -->
            <div class="space-y-1">
                <div class="flex justify-center items-center space-x-2 py-2">
                    <template x-for="i in 6" :key="i">
                        <div class="w-4 h-4 rounded-full border-2 transition-all"
                             :class="inputPin.length >= i ? 'bg-indigo-600 border-indigo-600 scale-110' : 'bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-700'">
                        </div>
                    </template>
                </div>
                <p class="text-[10px] text-slate-400">Gunakan Keypad di bawah atau keyboard fisik</p>
            </div>

            <!-- PIN Error Alert -->
            <template x-if="pinErrorMessage">
                <div class="p-2.5 rounded-xl bg-rose-500/10 text-rose-600 text-xs font-bold" x-text="pinErrorMessage"></div>
            </template>

            <!-- Touch Keypad (1-9, C, 0, Delete) -->
            <div class="grid grid-cols-3 gap-2 max-w-[240px] mx-auto text-sm font-extrabold">
                <template x-for="n in [1,2,3,4,5,6,7,8,9]" :key="n">
                    <button type="button" @click="appendPinDigit(n.toString())" class="py-3 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-600 hover:text-white text-slate-900 dark:text-white shadow-xs transition-all active:scale-95">
                        <span x-text="n"></span>
                    </button>
                </template>
                <button type="button" @click="clearPin()" class="py-3 rounded-2xl bg-slate-100 dark:bg-slate-800 text-rose-600 hover:bg-rose-500 hover:text-white text-xs shadow-xs transition-all">
                    Reset
                </button>
                <button type="button" @click="appendPinDigit('0')" class="py-3 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-indigo-600 hover:text-white text-slate-900 dark:text-white shadow-xs transition-all active:scale-95">
                    0
                </button>
                <button type="button" @click="backspacePin()" class="py-3 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 hover:bg-slate-200 dark:text-slate-300 shadow-xs transition-all flex items-center justify-center">
                    &larr;
                </button>
            </div>

            <div class="pt-2 flex items-center justify-between space-x-2">
                <button type="button" @click="showPinModal = false" class="w-full py-3 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200">
                    Batal
                </button>
                <button type="button" @click="submitPinPayment()" :disabled="inputPin.length !== 6 || isSubmitting" class="w-full py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center space-x-1">
                    <span x-text="isSubmitting ? 'Memverifikasi...' : 'Konfirmasi & Bayar'"></span>
                </button>
            </div>

        </div>
    </div>

    <!-- QR CODE DISPLAY MODAL (Shown for Student to Scan) -->
    <div x-show="qrModalData !== null" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto" style="z-index: 9999;" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/85 backdrop-blur-md" @click="if(!isPaidSuccess) { stopPolling(); qrModalData = null; }"></div>

        <div class="relative transform rounded-3xl bg-white dark:bg-slate-900 p-6 text-center shadow-2xl transition-all w-full max-w-sm border border-slate-200 dark:border-slate-800 space-y-4 my-auto">
            
            <!-- 1. Normal State: Waiting for Payment -->
            <div x-show="!isPaidSuccess" class="space-y-4">
                <div class="space-y-1">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mx-auto shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Scan Barcode Transaksi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Mintalah siswa memindai Kode QR ini di aplikasi E-Kantin.</p>
                </div>

                <!-- Big QR Image Code Display -->
                <div class="p-4 bg-white rounded-3xl border border-slate-200 shadow-md inline-block mx-auto space-y-2">
                    <img :src="qrModalData?.qr_image_url" alt="QR Transaksi POS" class="w-56 h-56 mx-auto object-contain">
                    <span class="font-mono text-xs font-bold text-indigo-600 block" x-text="qrModalData?.qr_code"></span>
                </div>

                <!-- Total Amount Card -->
                <div class="p-3 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-900">
                    <span class="text-[10px] font-bold text-slate-400 block uppercase">Total Tagihan Transaksi</span>
                    <span class="text-lg font-black text-indigo-600 dark:text-indigo-400" x-text="qrModalData?.receipt?.total_formatted"></span>
                </div>

                <!-- Live Polling Loader Status -->
                <div class="flex items-center justify-center space-x-2 text-xs font-bold text-slate-500">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                    </span>
                    <span>Menunggu siswa melakukan scan...</span>
                </div>

                <!-- Modal Action Buttons -->
                <div class="pt-2 flex items-center justify-between space-x-2">
                    <button type="button" @click="stopPolling(); qrModalData = null" class="w-full py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200">
                        Batal Transaksi
                    </button>
                </div>
            </div>

            <!-- 2. Success State: Payment Successful -->
            <div x-show="isPaidSuccess" class="space-y-5 py-4">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto shadow-md">
                    <svg class="w-10 h-10 animate-bounce-short" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                
                <div class="space-y-1">
                    <h3 class="text-lg font-black text-emerald-600 dark:text-emerald-400">Pembayaran Berhasil!</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">
                        Siswa: <span class="font-black text-slate-800 dark:text-white" x-text="qrModalData?.student_name"></span>
                    </p>
                </div>

                <div class="p-3.5 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900 text-center">
                    <span class="text-[9px] font-bold text-slate-400 block uppercase">Nominal Lunas</span>
                    <span class="text-xl font-black text-emerald-600 dark:text-emerald-400" x-text="qrModalData?.receipt?.total_formatted"></span>
                </div>

                <div class="flex items-center justify-center space-x-2 text-[11px] font-bold text-slate-400">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Membuka struk belanja...</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Receipt / Struk Modal (For Completed Transaction) -->
    <div x-show="receiptData !== null" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto" style="z-index: 9999;" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md" @click="receiptData = null"></div>

        <div class="relative transform rounded-3xl bg-white dark:bg-slate-900 p-6 text-left shadow-2xl transition-all w-full max-w-sm border border-slate-200 dark:border-slate-800 space-y-4 my-auto z-10 font-mono text-xs">
            
            <div class="text-center space-y-1 pb-3 border-b border-dashed border-slate-300 dark:border-slate-700">
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white" x-text="receiptData?.stall_name"></h3>
                <p class="text-[10px] text-slate-500">Struk Pembelian Kantin Sekolah</p>
                <div class="text-[10px] text-slate-400 pt-1" x-text="receiptData?.date"></div>
                <div class="text-[10px] font-bold text-indigo-600" x-text="receiptData?.order_number"></div>
            </div>

            <!-- Receipt Details -->
            <div class="space-y-1.5">
                <div class="flex justify-between text-[11px]">
                    <span class="text-slate-500">Pelanggan:</span>
                    <span class="font-bold text-slate-900 dark:text-white" x-text="receiptData?.student_name"></span>
                </div>
                <div class="flex justify-between text-[11px]">
                    <span class="text-slate-500">Metode Bayar:</span>
                    <span class="font-bold text-slate-900 dark:text-white" x-text="receiptData?.payment_method_label"></span>
                </div>
            </div>

            <!-- Items Breakdown Table -->
            <div class="py-2 border-y border-dashed border-slate-300 dark:border-slate-700 space-y-2">
                <template x-for="item in receiptData?.items" :key="item.name">
                    <div class="space-y-0.5">
                        <div class="flex justify-between font-bold text-slate-900 dark:text-white">
                            <span x-text="item.name"></span>
                            <span x-text="item.subtotal_formatted"></span>
                        </div>
                        <div class="text-[10px] text-slate-400" x-text="item.qty + 'x @ ' + item.price_formatted"></div>
                    </div>
                </template>
            </div>

            <!-- Totals & Payment Summary -->
            <div class="space-y-1.5 pt-1">
                <div class="flex justify-between font-black text-sm text-slate-900 dark:text-white">
                    <span>TOTAL:</span>
                    <span x-text="receiptData?.total_formatted"></span>
                </div>
                <div class="flex justify-between text-[11px] text-slate-600 dark:text-slate-400">
                    <span>DIBAYAR:</span>
                    <span x-text="receiptData?.paid_formatted"></span>
                </div>
                <template x-if="receiptData?.change_amount > 0">
                    <div class="flex justify-between text-[11px] text-emerald-600 font-bold">
                        <span>KEMBALIAN:</span>
                        <span x-text="receiptData?.change_formatted"></span>
                    </div>
                </template>
            </div>

            <!-- Modal Action Buttons -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between space-x-2 no-print">
                <button type="button" @click="receiptData = null" class="flex-1 py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-200">
                    Selesai
                </button>
                <button type="button" onclick="window.print()" class="flex-1 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs flex items-center justify-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Struk</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
