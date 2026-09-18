@extends('layouts.canteen-vendor')

@section('title', 'Scan QR Code Pesanan & Siswa')
@section('header_title', 'Scanner QR Pesanan & Siswa')

@section('content')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<div x-data="{
        qrInput: '',
        scannedOrder: null,
        scannedStudent: null,
        isLoading: false,
        errorMessage: '',
        successMessage: '',
        isCameraActive: false,
        html5QrCode: null,

        initScanner() {
            // Scanner initialized when user clicks camera button
        },

        startCamera() {
            this.isCameraActive = true;
            this.errorMessage = '';

            this.$nextTick(() => {
                if (!this.html5QrCode) {
                    this.html5QrCode = new Html5Qrcode('qr-reader');
                }

                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                
                this.html5QrCode.start(
                    { facingMode: 'environment' }, 
                    config,
                    (decodedText, decodedResult) => {
                        this.qrInput = decodedText;
                        this.stopCamera();
                        this.processVerification();
                    },
                    (errorMessage) => {
                        // Scan error or frame without QR
                    }
                ).catch(err => {
                    this.errorMessage = 'Tidak dapat mengakses kamera: ' + err;
                    this.isCameraActive = false;
                });
            });
        },

        stopCamera() {
            if (this.html5QrCode && this.isCameraActive) {
                this.html5QrCode.stop().then(() => {
                    this.isCameraActive = false;
                }).catch(err => {
                    this.isCameraActive = false;
                });
            }
        },

        processVerification() {
            if (!this.qrInput.trim()) return;
            this.isLoading = true;
            this.errorMessage = '';
            this.successMessage = '';
            this.scannedOrder = null;
            this.scannedStudent = null;

            fetch('{{ route("canteen.vendor.verify-qr") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    qr_data: this.qrInput
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isLoading = false;
                if (data.success) {
                    if (data.type === 'student') {
                        this.scannedStudent = data.student;
                        this.successMessage = data.message || 'Data Siswa berhasil ditemukan!';
                    } else {
                        this.scannedOrder = data.order;
                        this.successMessage = data.message || 'Pesanan berhasil ditemukan!';
                    }
                } else {
                    this.errorMessage = data.message || 'QR Code tidak valid atau data tidak ditemukan.';
                }
            })
            .catch(err => {
                this.isLoading = false;
                this.errorMessage = 'Terjadi kesalahan koneksi sistem. Silakan coba lagi.';
            });
        },

        updateStatus(newStatus) {
            if (!this.scannedOrder) return;
            this.isLoading = true;

            fetch('{{ url('canteen-vendor/orders') }}/' + this.scannedOrder.id + '/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    order_status: newStatus
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isLoading = false;
                if (data.success) {
                    this.processVerification(); // Re-verify to refresh state
                }
            })
            .catch(err => {
                this.isLoading = false;
            });
        }
    }" 
    class="space-y-6 pb-24 max-w-xl mx-auto">

    <!-- Scanner Control Box -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5 text-center">
        
        <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center mx-auto shadow-lg shadow-indigo-600/20">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
        </div>

        <div>
            <h2 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">Scanner QR Code Pesanan & Siswa</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pindai QR Code Pesanan atau QR Code Siswa untuk transaksi tabungan.</p>
        </div>

        <!-- Camera Scanner Area -->
        <div class="space-y-3">
            <div x-show="isCameraActive" x-cloak class="overflow-hidden rounded-2xl border-2 border-indigo-500 bg-slate-950 p-2 shadow-inner relative">
                <div id="qr-reader" class="w-full"></div>
                
                <!-- Futuristic Scanning Animation Overlay -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10 p-4">
                    <div class="relative w-full h-full max-w-[250px] max-h-[250px] aspect-square border-2 border-white/20 rounded-3xl shadow-[0_0_50px_rgba(99,102,241,0.15)]">
                        <div class="absolute -top-1 -left-1 w-8 h-8 border-t-4 border-l-4 border-indigo-500 rounded-tl-2xl"></div>
                        <div class="absolute -top-1 -right-1 w-8 h-8 border-t-4 border-r-4 border-indigo-500 rounded-tr-2xl"></div>
                        <div class="absolute -bottom-1 -left-1 w-8 h-8 border-b-4 border-l-4 border-indigo-500 rounded-bl-2xl"></div>
                        <div class="absolute -bottom-1 -right-1 w-8 h-8 border-b-4 border-r-4 border-indigo-500 rounded-br-2xl"></div>
                        
                        <!-- Futuristic Scanning Anim -->
                        <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none scan-grid-bg">
                            <!-- Scanning Laser Line -->
                            <div class="absolute left-2 right-2 h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-95 rounded-full laser-scan-line"></div>
                            <!-- Scanning Laser Sweep Gradient Area -->
                            <div class="w-full h-1/2 bg-gradient-to-b from-indigo-500/10 to-transparent absolute left-0 laser-sweep-area"></div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" 
                    @click="isCameraActive ? stopCamera() : startCamera()" 
                    class="w-full py-3 px-4 rounded-2xl text-xs font-extrabold transition-all shadow-md flex items-center justify-center space-x-2"
                    :class="isCameraActive ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-text="isCameraActive ? 'Matikan Kamera' : 'Buka Kamera Scanner Live'"></span>
            </button>
        </div>

        <!-- Manual Code Entry Input -->
        <form @submit.prevent="processVerification()" class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
            <div class="space-y-1 text-left">
                <label class="block text-[11px] font-bold text-slate-500 uppercase">Input Kode Manual (Pesanan / QR Siswa)</label>
                <input type="text" 
                       x-model="qrInput" 
                       placeholder="Contoh: KTNQR-xxx, STD-xxx, atau NISN" 
                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 text-center font-mono font-bold text-sm bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" 
                    :disabled="isLoading || !qrInput.trim()" 
                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 dark:disabled:bg-slate-800 text-white font-bold rounded-2xl text-xs shadow-md transition-all flex items-center justify-center space-x-2">
                <svg x-show="isLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Cari & Verifikasi QR</span>
            </button>
        </form>
    </div>

    <!-- Error Alert Banner -->
    <template x-if="errorMessage">
        <div class="p-4 rounded-3xl bg-rose-500/10 border border-rose-500/30 text-rose-800 dark:text-rose-200 text-xs font-bold flex items-center space-x-2">
            <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span x-text="errorMessage"></span>
        </div>
    </template>

    <!-- Scanned Student Result Card -->
    <template x-if="scannedStudent">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border-2 border-indigo-500/50 shadow-xl space-y-4">
            <div class="flex items-center space-x-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <img :src="scannedStudent.photo_url" class="w-12 h-12 rounded-full object-cover border-2 border-indigo-600 shadow-md">
                <div class="text-left">
                    <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider block">QR Code Siswa / Tabungan</span>
                    <h3 class="font-black text-sm text-slate-900 dark:text-white" x-text="scannedStudent.name"></h3>
                    <p class="text-xs text-slate-400" x-text="scannedStudent.class + ' &bull; NISN: ' + scannedStudent.nisn"></p>
                </div>
            </div>

            <!-- Savings Status Box -->
            <div class="p-4 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold text-slate-500 block uppercase">Status Tabungan Siswa</span>
                    <span class="text-xs font-black text-slate-700 dark:text-slate-300">Tabungan Terhubung (PIN Aktif)</span>
                </div>
                <span class="px-2.5 py-1 rounded-xl bg-emerald-500 text-white text-[10px] font-black uppercase">Aktif</span>
            </div>

            <div class="pt-2">
                <a :href="'{{ route('canteen.vendor.pos') }}'" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-2xl shadow-md transition-all flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Buka POS Kasir Kantin</span>
                </a>
            </div>
        </div>
    </template>

    <!-- Scanned Verification Order Card Result -->
    <template x-if="scannedOrder">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border-2 border-emerald-500/50 shadow-xl space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">No. Pesanan</span>
                    <span class="font-mono font-black text-sm text-indigo-600 dark:text-indigo-400" x-text="scannedOrder.order_number"></span>
                </div>
                <div class="flex items-center space-x-1" x-html="scannedOrder.order_status_badge + ' ' + scannedOrder.payment_status_badge"></div>
            </div>

            <!-- Student Info -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 space-y-1 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Nama Siswa:</span>
                    <span class="font-bold text-slate-900 dark:text-white" x-text="scannedOrder.student_name"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Kelas / NISN:</span>
                    <span class="font-bold text-slate-900 dark:text-white" x-text="scannedOrder.student_class + ' / ' + scannedOrder.student_nisn"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Metode Pembayaran:</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="scannedOrder.payment_method"></span>
                </div>
            </div>

            <!-- Items List Breakdown -->
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Daftar Pesanan:</span>
                <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                    <template x-for="item in scannedOrder.items" :key="item.item_name">
                        <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-800">
                            <span><strong class="text-indigo-600 dark:text-indigo-400" x-text="item.quantity + 'x'"></strong> <span x-text="item.item_name"></span></span>
                            <span class="font-bold text-slate-900 dark:text-white" x-text="item.subtotal_formatted"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Total Price -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <span class="text-xs font-extrabold text-slate-700 dark:text-slate-300">TOTAL BAYAR:</span>
                <span class="text-base font-black text-emerald-600 dark:text-emerald-400" x-text="scannedOrder.total_formatted"></span>
            </div>

            <!-- Action Status Update Buttons -->
            <div class="pt-2 flex flex-col gap-2">
                <template x-if="scannedOrder.order_status === 'pending'">
                    <button type="button" @click="updateStatus('processing')" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-2xl shadow-xs transition-all">
                        Mulai Proses Pesanan
                    </button>
                </template>

                <template x-if="scannedOrder.order_status === 'processing'">
                    <button type="button" @click="updateStatus('ready')" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-2xl shadow-xs transition-all">
                        Tandai Siap Diambil Siswa
                    </button>
                </template>

                <template x-if="scannedOrder.order_status === 'ready'">
                    <button type="button" @click="updateStatus('completed')" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-2xl shadow-md transition-all">
                        Konfirmasi Penyerahan & Serahkan Makanan (Selesai)
                    </button>
                </template>

                <template x-if="scannedOrder.order_status === 'completed'">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 rounded-2xl border border-emerald-200 dark:border-emerald-900 text-center text-xs font-extrabold text-emerald-700 dark:text-emerald-300">
                        ✓ Pesanan Ini Sudah Selesai & Lunas
                    </div>
                </template>
            </div>

        </div>
    </template>
</div>

@push('styles')
<style>
    /* Custom High-Tech Scanner Animation for QRpay */
    @keyframes scan-laser-indigo {
        0% { top: 2%; opacity: 0.3; }
        50% { top: 98%; opacity: 1; }
        100% { top: 2%; opacity: 0.3; }
    }
    .laser-scan-line {
        position: absolute;
        animation: scan-laser-indigo 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        box-shadow: 0 0 15px 4px rgba(99, 102, 241, 0.6), 0 0 6px 1px rgba(255, 255, 255, 0.9);
    }
    
    @keyframes scan-sweep-indigo {
        0% { transform: translateY(-100%); }
        50% { transform: translateY(100%); }
        100% { transform: translateY(-100%); }
    }
    .laser-sweep-area {
        animation: scan-sweep-indigo 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }

    .scan-grid-bg {
        background-size: 24px 24px;
        background-image: 
            linear-gradient(to right, rgba(99, 102, 241, 0.06) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(99, 102, 241, 0.06) 1px, transparent 1px);
    }
</style>
@endpush
@endsection
