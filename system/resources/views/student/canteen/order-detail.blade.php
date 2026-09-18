@extends('layouts.student-mobile')

@section('title', 'Detail Pesanan #' . $order->order_number)
@section('header_title', 'Detail Pesanan')

@section('content')
<div class="max-w-2xl mx-auto space-y-4 pb-24 lg:pb-10">

    <!-- Header Navigation Back Link -->
    <div class="flex items-center gap-2">
        <a href="{{ route('student.canteen.my-orders') }}" class="flex-1 inline-flex items-center justify-center space-x-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-extrabold shadow-xs hover:bg-slate-50 dark:hover:bg-slate-850 transition-colors">
            <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span class="truncate">Riwayat</span>
        </a>

        <a href="{{ route('student.canteen.index') }}" class="flex-1 inline-flex items-center justify-center space-x-1 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-xl text-xs shadow-xs transition-all text-center">
            <span>Marketplace</span>
        </a>
    </div>

    <!-- Main Order Card with QR Code -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
        
        <!-- Status Header Banner -->
        <div class="flex flex-col gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400">Order ID Kantin</span>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white font-mono leading-none mt-1">{{ $order->order_number }}</h2>
                </div>
                <div class="flex flex-col items-end gap-1.5 shrink-0">
                    {!! $order->order_status_badge !!}
                    {!! $order->payment_status_badge !!}
                </div>
            </div>
            <p class="text-[10px] text-slate-400 dark:text-slate-500">Dipesan pada {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>

        <!-- Payment Actions (If Unpaid) -->
        @if($order->payment_status === 'unpaid')
            <!-- Minimal Alert Banner -->
            <div class="p-3 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center gap-2.5 text-amber-800 dark:text-amber-300 text-xs">
                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="min-w-0">
                    <span class="font-black block">Pesanan Belum Dibayar</span>
                    <p class="text-[11px] leading-normal text-slate-600 dark:text-slate-300 mt-0.5">Selesaikan pembayaran atau tunjukkan barcode QR di kasir.</p>
                </div>
            </div>

            <!-- Premium QRpay Action Card -->
            <div class="bg-slate-50 dark:bg-slate-800/40 rounded-2xl p-4 sm:p-5 border border-slate-200/60 dark:border-slate-800 space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Metode Pembayaran</span>
                        <h4 class="text-xs font-black text-slate-900 dark:text-white mt-0.5">QRpay</h4>
                    </div>
                    <div class="text-right">
                        <span class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Total Tagihan</span>
                        <span class="text-sm sm:text-base font-black text-indigo-600 dark:text-indigo-400">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($student && $student->savings_balance >= $order->total_amount)
                    <div x-data="{ showPinModal: false, pin: '', maxLength: 6, loading: false }">
                        <!-- Trigger Button -->
                        <button type="button" @click="showPinModal = true; $nextTick(() => $refs.modalPinInput.focus())" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md shadow-emerald-600/20 transition-all active:scale-95 flex items-center justify-center space-x-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 shrink-0 text-emerald-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Bayar Sekarang (QRpay)</span>
                        </button>
                        
                        <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 px-1 font-bold mt-2.5">
                            <span>Sisa Saldo Anda:</span>
                            <span class="text-slate-700 dark:text-slate-200">
                                Rp {{ number_format($student->savings_balance, 0, ',', '.') }}
                                <span class="text-emerald-500 font-extrabold">(Mencukupi)</span>
                            </span>
                        </div>

                        <!-- Modal PIN overlay -->
                        <div x-show="showPinModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
                            <div class="relative bg-white dark:bg-slate-900 rounded-3xl p-6 w-full max-w-sm border border-slate-200 dark:border-slate-800 shadow-2xl text-center space-y-4" @click.away="!loading && (showPinModal = false)">
                                
                                <!-- Loading State Overlay -->
                                <div x-show="loading" x-cloak class="absolute inset-0 bg-white/90 dark:bg-slate-900/90 rounded-3xl flex flex-col items-center justify-center space-y-3 z-30">
                                    <div class="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Memproses Pembayaran...</p>
                                </div>

                                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mx-auto border border-indigo-100 dark:border-indigo-800 shadow-xs">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-black text-sm text-slate-900 dark:text-white">Masukkan PIN QRpay</h4>
                                    <p class="text-[11px] text-slate-500 mt-1">Masukkan 6 digit PIN untuk menyetujui transaksi sebesar <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
                                </div>

                                <form method="POST" action="{{ route('student.canteen.pay-savings', $order->id) }}" id="pin-payment-form">
                                    @csrf
                                    <!-- Hidden PIN input -->
                                    <input type="tel" 
                                           name="pin" 
                                           x-ref="modalPinInput" 
                                           class="absolute opacity-0 -z-50 pointer-events-none w-0 h-0" 
                                           maxlength="6"
                                           :disabled="loading"
                                           @input="pin = $event.target.value.replace(/\D/g, '').substring(0, maxLength); if(pin.length === maxLength) { loading = true; document.getElementById('pin-payment-form').submit(); }">

                                    <!-- PIN Dots UI -->
                                    <div class="flex items-center justify-center gap-3 py-2 cursor-pointer" @click="!loading && $refs.modalPinInput.focus()">
                                        <template x-for="(item, index) in Array.from({length: maxLength})">
                                            <div class="w-9 h-11 rounded-xl border flex items-center justify-center text-lg font-black transition-all"
                                                 :class="{
                                                     'border-indigo-600 dark:border-indigo-500 bg-indigo-50/20': pin.length === index,
                                                     'border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800': pin.length > index,
                                                     'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900': pin.length <= index
                                                 }">
                                                <span x-text="pin.length > index ? pin.charAt(index) : ''" class="text-xl font-black text-slate-900 dark:text-white"></span>
                                            </div>
                                        </template>
                                    </div>
                                </form>

                                <div class="flex gap-2.5 pt-2">
                                    <button type="button" :disabled="loading" @click="showPinModal = false" class="flex-1 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-500 hover:bg-slate-50 cursor-pointer disabled:opacity-50">Batal</button>
                                    <button type="button" :disabled="loading" @click="$refs.modalPinInput.focus()" class="flex-1 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 cursor-pointer disabled:opacity-50">Fokus Input</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-[10px] text-rose-600 dark:text-rose-400 font-bold bg-white dark:bg-slate-900 p-3 rounded-2xl border border-rose-200 dark:border-rose-800 text-center leading-normal">
                        Saldo QRpay Anda (Rp {{ number_format($student->savings_balance ?? 0, 0, ',', '.') }}) tidak mencukupi. Silakan lakukan pembayaran tunai di kasir kantin.
                    </div>
                @endif
            </div>
        @endif

        <!-- Per-Transaction QR Code Box -->
        <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white text-center space-y-3.5 shadow-md border border-indigo-500/20 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-8 translate-x-8 blur-sm pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-indigo-500/5 rounded-full translate-y-6 -translate-x-6 pointer-events-none"></div>

            <span class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest block relative z-10">QR Code Transaksi</span>
            
            <div class="w-40 h-40 bg-white p-2.5 rounded-xl mx-auto shadow-lg flex items-center justify-center border-2 border-indigo-500/50 relative z-10 overflow-hidden">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($order->qr_code) }}" 
                     alt="QR Code Transaksi {{ $order->order_number }}" 
                     class="w-full h-full object-contain relative z-10">
                
                <!-- Cool Interactive Scanner Sweep Overlay on Student QR Code -->
                <div class="absolute inset-0 overflow-hidden rounded-lg pointer-events-none z-20 scan-grid-bg">
                    <!-- Scanning Laser Line -->
                    <div class="absolute left-1 right-1 h-0.5 bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-95 rounded-full laser-scan-line"></div>
                    <!-- Scanning Laser Sweep Gradient Area -->
                    <div class="w-full h-1/3 bg-gradient-to-b from-indigo-500/5 to-transparent absolute left-0 laser-sweep-area"></div>
                </div>
            </div>

            <div class="space-y-1 relative z-10">
                <p class="text-xs font-mono font-extrabold text-indigo-300 tracking-widest">{{ $order->qr_code }}</p>
                <p class="text-[10px] text-slate-300 max-w-sm mx-auto leading-normal px-2">
                    Tunjukkan QR Code ini ke Kasir Kantin saat pembayaran atau saat penyerahan pesanan.
                </p>
            </div>
        </div>

        <!-- Order Items Breakdown -->
        <div class="space-y-3.5">
            <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Rincian Menu</h3>
            
            <div class="divide-y divide-slate-100 dark:divide-slate-800/80 border-t border-b border-slate-100 dark:border-slate-800/80">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between text-xs gap-2">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <span class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-black flex items-center justify-center text-[11px] shrink-0">
                                {{ $item->quantity }}x
                            </span>
                            <div class="min-w-0">
                                <span class="font-bold text-slate-900 dark:text-white block truncate text-[11px] sm:text-xs">{{ $item->item_name }}</span>
                                <span class="text-[9px] text-slate-400 block mt-0.5">@ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <span class="font-extrabold text-slate-900 dark:text-white shrink-0 text-[11px] sm:text-xs">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Notes if any -->
            @if($order->notes)
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-xs border border-slate-100 dark:border-slate-700/30">
                    <span class="font-bold text-slate-400 block text-[9px] uppercase tracking-wider">Catatan Pesanan:</span>
                    <p class="text-slate-700 dark:text-slate-300 mt-0.5 font-medium leading-normal">{{ $order->notes }}</p>
                </div>
            @endif

            <!-- Summary Totals -->
            <div class="pt-2 space-y-2 text-xs">
                <div class="flex justify-between items-center text-slate-500">
                    <span>Metode Pembayaran</span>
                    <span class="font-bold text-slate-700 dark:text-slate-200">
                        @if($order->payment_method === 'savings_balance') Saldo Tabungan
                        @elseif($order->payment_method === 'qris') QRIS / Scan QR
                        @else Tunai Kasir @endif
                    </span>
                </div>
                <div class="flex justify-between items-center text-slate-900 dark:text-white font-extrabold text-sm pt-2.5 border-t border-slate-100 dark:border-slate-800">
                    <span>Total Pembayaran</span>
                    <span class="text-indigo-600 dark:text-indigo-400 text-base font-black">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Custom High-Tech Scanner Animation for Student QRpay screen */
    @keyframes scan-laser-indigo {
        0% { top: 2%; opacity: 0.3; }
        50% { top: 98%; opacity: 1; }
        100% { top: 2%; opacity: 0.3; }
    }
    .laser-scan-line {
        position: absolute;
        animation: scan-laser-indigo 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        box-shadow: 0 0 10px 2px rgba(99, 102, 241, 0.5), 0 0 4px 1px rgba(255, 255, 255, 0.8);
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
        background-size: 20px 20px;
        background-image: 
            linear-gradient(to right, rgba(99, 102, 241, 0.05) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(99, 102, 241, 0.05) 1px, transparent 1px);
    }
</style>
@endpush
@endsection
