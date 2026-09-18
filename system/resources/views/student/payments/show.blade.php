@extends('layouts.student-mobile')

@section('title', 'Transaksi Tagihan - ' . $paymentPost->name)
@section('header_title', 'Transaksi Tagihan')

@section('content')
<div class="space-y-6" x-data="paymentApp()">
    
    <!-- Top Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.payments.index') }}" class="inline-flex items-center space-x-2 px-3 py-1.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-extrabold text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition-colors shadow-2xs">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span>Kembali ke Pos Pembayaran</span>
        </a>
    </div>

    <!-- Toast/Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Header Pos Summary Card -->
    <div class="bg-gradient-to-br from-indigo-600 to-blue-700 dark:from-indigo-900 dark:to-blue-900 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 w-36 h-36 bg-white/10 rounded-full blur-md -translate-y-6 translate-x-6 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col justify-between h-full">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="px-2 py-0.5 bg-white/20 text-white font-mono text-[9px] font-extrabold rounded border border-white/20 uppercase">
                        POS: {{ $paymentPost->code }}
                    </span>
                </div>
                <h2 class="text-lg font-extrabold">{{ $paymentPost->name }}</h2>
                <p class="text-xs text-indigo-100 font-medium mt-0.5">{{ $student->name }} &bull; Kelas: {{ $student->class?->name ?? '-' }}</p>
            </div>
            
            <div class="mt-4 pt-3 border-t border-white/15 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <div class="min-w-0">
                    <p class="text-[10px] text-indigo-200 font-bold uppercase tracking-wider">Keterangan Pos</p>
                    <p class="text-xs font-medium text-white mt-0.5 truncate">{{ $paymentPost->description ?? 'Tagihan resmi pembayaran sekolah' }}</p>
                </div>
                <span class="self-start sm:self-auto px-2.5 py-1 bg-white/15 text-indigo-100 rounded-full text-[9px] sm:text-[10px] font-extrabold uppercase border border-white/20 whitespace-nowrap">STATUS: TERHUBUNG</span>
            </div>
        </div>
    </div>

    <!-- PENDING MANUAL PROOF UPLOAD -->
    @if(session('pending_manual_transaction') || ($pendingTransactions->count() > 0))
        @php
            $tx = session('pending_manual_transaction') 
                ? \App\Models\PaymentTransaction::find(session('pending_manual_transaction'))
                : $pendingTransactions->first();
        @endphp
        @if($tx)            <!-- Alert Banner -->
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/20 dark:to-orange-950/20 rounded-2xl p-4 border border-amber-200 dark:border-amber-900/60 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm animate-pulse">
                        <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 dark:text-white text-xs uppercase tracking-wide">Menunggu Bukti Transfer</h4>
                        <p class="text-[11px] text-slate-650 dark:text-slate-400 mt-0.5 leading-relaxed">
                            Tagihan senilai <strong class="text-amber-600 dark:text-amber-400 font-extrabold">Rp {{ number_format($tx->amount, 0, ',', '.') }}</strong> (Invoice: *{{ $tx->invoice_number }}*) belum terkonfirmasi. Silakan kirim bukti transfer Anda.
                        </p>
                    </div>
                </div>
                <button type="button" @click="showUploadProofModal = true" class="w-full md:w-auto px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all shadow-xs text-center shrink-0">
                    Kirim Bukti Transfer
                </button>
            </div>

            <!-- Modal Upload Bukti Transfer -->
            <div x-show="showUploadProofModal" class="fixed inset-0 bg-slate-950/60 dark:bg-slate-950/80 backdrop-blur-xs z-[998] flex items-center justify-center p-4 overflow-y-auto" x-cloak x-transition>
                <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-xl space-y-5" @click.stop>
                    
                    <!-- Close button -->
                    <button type="button" @click="showUploadProofModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-250 text-lg font-bold">&times;</button>
                    
                    <div class="text-center pb-2 border-b border-slate-100 dark:border-slate-850">
                        <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-950/60 rounded-full flex items-center justify-center mx-auto border border-indigo-100 dark:border-indigo-800 shadow-sm mb-3">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                        </div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-sm uppercase">Kirim Bukti Pembayaran</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Silakan transfer sebesar <strong class="text-indigo-600 dark:text-indigo-400">Rp {{ number_format($tx->amount, 0, ',', '.') }}</strong> untuk Invoice: <strong>{{ $tx->invoice_number }}</strong>
                        </p>
                    </div>

                    <form action="{{ route('student.payments.upload-proof') }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2" x-data="{ targetBankId: '{{ $bankAccounts->first()?->id ?? '' }}' }">
                        @csrf
                        <input type="hidden" name="transaction_id" value="{{ $tx->id }}">
                        <input type="hidden" name="bank_account_id" :value="targetBankId">
                        
                        <div class="space-y-2">
                            <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase">Rekening Tujuan Transfer <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-1 gap-2 max-h-40 overflow-y-auto pr-1">
                                @foreach($bankAccounts as $bank)
                                    <label @click="targetBankId = '{{ $bank->id }}'"
                                           :class="targetBankId == '{{ $bank->id }}' ? 'border-emerald-600 bg-emerald-50/40 dark:bg-emerald-950/30 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900'"
                                           class="p-3 rounded-2xl border-2 cursor-pointer transition-all flex items-start justify-between gap-3 relative">
                                        <div class="flex items-start space-x-2.5 min-w-0 flex-1">
                                            <input type="radio" name="bank_option" value="{{ $bank->id }}" x-model="targetBankId" class="mt-0.5 w-4 h-4 text-emerald-600 focus:ring-emerald-500 shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-extrabold text-slate-800 dark:text-white text-xs">{{ $bank->bank_name }}</span>
                                                    @if($bank->account_number)
                                                        <span class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 font-mono">{{ $bank->account_number }}</span>
                                                    @endif
                                                </div>
                                                <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 truncate">a.n. {{ $bank->account_name }}</p>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pilih File Bukti Pembayaran <span class="text-rose-500">*</span></label>
                            <x-file-upload name="payment_proof" accept="image/*" required="true" label="Upload Foto Bukti Transfer SPP" help="Seret & lepas foto struk pembayaran di sini" />
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Catatan Pembayaran (Opsional)</label>
                            <textarea name="notes" rows="2" placeholder="Contoh: Pembayaran SPP bulan Agustus" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl outline-none text-xs text-slate-750 dark:text-slate-350 focus:border-indigo-500"></textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all shadow-xs text-center">
                            Unggah & Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endif

    <!-- MAIN TRANSACTION FORM -->
    @php
        $firstBill = $paymentBills->first();
        $isBulananType = ($firstBill && $firstBill->paymentBill && $firstBill->paymentBill->type === 'bulanan');
        
        $allMonthlyList = [];
        if ($isBulananType) {
            foreach($paymentBills as $bill) {
                if ($bill->paymentBill) {
                    foreach($bill->details->sortBy('month_no') as $detail) {
                        if ($detail->status !== 'paid') {
                            $allMonthlyList[] = [
                                'id' => (string) $detail->id,
                                'billName' => $bill->paymentBill->name,
                                'monthName' => $detail->month_name,
                                'amount' => (int) ($detail->amount - $detail->paid_amount),
                                'status' => $detail->status,
                            ];
                        }
                    }
                }
            }
        }
    @endphp

    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm space-y-5">
        <div class="pb-2 border-b border-slate-100 dark:border-slate-700">
            <h4 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider">
                {{ $isBulananType ? 'Pilih Bulan Tagihan yang Ingin Dibayar' : 'Rincian Tagihan Bebas / Cicilan' }}
            </h4>
            <p class="text-[10px] text-slate-400 mt-0.5">
                {{ $isBulananType ? 'Memilih bulan tertentu akan otomatis mencakup bulan tunggakan sebelumnya.' : 'Masukkan jumlah nominal yang ingin Anda bayarkan sebagai cicilan.' }}
            </p>
        </div>

        <form action="{{ route('student.payments.checkout') }}" method="POST" class="space-y-5" x-ref="paymentForm" @submit="onFormSubmit($event)">
            @csrf
            <input type="hidden" name="pin" :value="pinCode">
            <input type="hidden" name="payment_type" value="{{ $isBulananType ? 'bulanan' : 'bebas' }}">

            @if($isBulananType)
                <!-- TAGIHAN BULANAN CONTENT -->
                <div class="space-y-2.5 max-h-[26rem] overflow-y-auto pr-1">
                    @forelse($allMonthlyList as $index => $item)
                        <div @click="selectMonthIndex({{ $index }})"
                             :class="selectedMonths['{{ $item['id'] }}'] 
                                ? 'border-indigo-500 bg-indigo-50/40 dark:bg-indigo-950/30 ring-2 ring-indigo-500/20 shadow-xs' 
                                : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-indigo-300 dark:hover:border-indigo-700'"
                             class="p-3.5 rounded-2xl border transition-all duration-150 cursor-pointer select-none space-y-1.5 relative">
                            
                            <!-- Top Row: Checkbox, Month Pill & Price -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <input type="checkbox" name="detail_ids[]" value="{{ $item['id'] }}" 
                                           :checked="!!selectedMonths['{{ $item['id'] }}']"
                                           @click.stop="selectMonthIndex({{ $index }})"
                                           class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 shrink-0 cursor-pointer">

                                    <span :class="selectedMonths['{{ $item['id'] }}'] ? 'bg-indigo-600 text-white shadow-xs' : 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800'"
                                          class="px-2.5 py-0.5 rounded-lg text-xs font-extrabold shrink-0 transition-colors">
                                        Bulan {{ $item['monthName'] }}
                                    </span>

                                    @if($item['status'] === 'partial')
                                        <span class="text-[9px] text-amber-600 dark:text-amber-400 font-extrabold bg-amber-50 dark:bg-amber-950/60 px-1.5 py-0.5 rounded border border-amber-200 dark:border-amber-800">Dicicil</span>
                                    @endif
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-xs sm:text-sm font-black font-mono" :class="selectedMonths['{{ $item['id'] }}'] ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-900 dark:text-white'">
                                        Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Bottom Row: Bill Title & Status -->
                            <div class="flex items-center justify-between text-[11px] pl-6.5">
                                <p class="truncate font-medium text-slate-500 dark:text-slate-400">{{ $item['billName'] }}</p>
                                <span x-show="selectedMonths['{{ $item['id'] }}']" class="inline-flex items-center gap-0.5 text-[9px] font-extrabold text-indigo-600 dark:text-indigo-400 shrink-0 ml-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Terpilih
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-xs text-slate-400 italic">Selamat! Seluruh tagihan bulanan pos ini telah lunas.</p>
                        </div>
                    @endforelse
                </div>
            @else
                <!-- TAGIHAN BEBAS CONTENT -->
                <div class="space-y-4">
                    @php $hasBebasUnpaid = false; @endphp
                    @foreach($paymentBills as $bill)
                        @foreach($bill->details as $detail)
                            @php $remaining = $detail->amount - $detail->paid_amount; @endphp
                            @if($remaining > 0)
                                @php $hasBebasUnpaid = true; @endphp
                                <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="font-extrabold text-slate-800 dark:text-white text-xs">{{ $bill->paymentBill->name }}</h5>
                                            <p class="text-[10px] text-slate-400 mt-0.5">Total Tagihan: Rp {{ number_format($detail->amount, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] text-slate-400">Sisa Tagihan</p>
                                            <p class="font-black text-slate-900 dark:text-white text-xs font-mono">Rp {{ number_format($remaining, 0, ',', '.') }}</p>
                                        </div>
                                    </div>

                                    <div class="pt-2 flex items-center space-x-2">
                                        <input type="radio" name="detail_id" value="{{ $detail->id }}" 
                                               @change="selectBebasBill('{{ $detail->id }}', {{ $remaining }})"
                                               class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                        <div class="flex-1 relative rounded-xl shadow-xs">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-xs font-bold text-slate-400">Rp</span>
                                            </div>
                                            <input type="number" name="amount" min="1000" max="{{ $remaining }}" 
                                                   x-bind:disabled="selectedBebasId !== '{{ $detail->id }}'"
                                                   @input="updateBebasAmount($event.target.value)"
                                                   class="w-full pl-8 pr-3 py-2 bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-700 rounded-xl text-xs outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" 
                                                   placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endforeach

                    @if(!$hasBebasUnpaid)
                        <div class="text-center py-8">
                            <p class="text-xs text-slate-400 italic">Selamat! Tagihan bebas ini telah terbayar lunas.</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- PAYMENT METODE CHANNELS -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                <div class="pb-1">
                    <h4 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider">Metode Pembayaran</h4>
                    <p class="text-[10px] text-slate-400">Pilih metode pembayaran yang ingin Anda gunakan.</p>
                </div>

                <div class="space-y-2.5">
                    <!-- Saldo Tabungan Siswa -->
                    <label :class="gateway === 'savings' ? 'border-emerald-600 bg-emerald-50/40 ring-1 ring-emerald-500/20 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900'" class="flex items-center justify-between p-4 rounded-xl border-2 cursor-pointer transition-all">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-extrabold text-slate-900 dark:text-white text-xs">Saldo Tabungan Siswa (Bayar Instant)</p>
                                <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">Saldo Tersedia: Rp {{ number_format($student->savings_balance, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <input type="radio" name="payment_gateway" value="savings" x-model="gateway" class="w-4 h-4 text-emerald-600">
                    </label>

                    <!-- Manual Bank Transfer -->
                    @if(in_array('manual', $activeGateways))
                        <label :class="gateway === 'manual' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900'" class="flex items-center justify-between p-4 rounded-xl border-2 cursor-pointer transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white text-xs">Transfer Bank (Verifikasi Manual)</p>
                                    <p class="text-[10px] text-slate-400">Verifikasi transfer manual oleh bendahara (1-24 jam)</p>
                                </div>
                            </div>
                            <input type="radio" name="payment_gateway" value="manual" x-model="gateway" class="w-4 h-4 text-indigo-600">
                        </label>
                    @endif

                    <!-- Midtrans Payment -->
                    @if(in_array('midtrans', $activeGateways))
                        <label :class="gateway === 'midtrans' ? 'border-blue-600 bg-blue-50/40 ring-1 ring-blue-500/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900'" class="flex items-center justify-between p-4 rounded-xl border-2 cursor-pointer transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-black text-xs shadow-sm">M</div>
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white text-xs">Pembayaran Online Instan (QRIS / VA)</p>
                                    <p class="text-[10px] text-slate-400">QRIS, Virtual Accounts Bank, G-Pay (Otomatis Aktif)</p>
                                </div>
                            </div>
                            <input type="radio" name="payment_gateway" value="midtrans" x-model="gateway" class="w-4 h-4 text-blue-600">
                        </label>
                    @endif

                    <!-- Tripay Payment -->
                    @if(in_array('tripay', $activeGateways))
                        <label :class="gateway === 'tripay' ? 'border-orange-600 bg-orange-50/40 ring-1 ring-orange-500/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900'" class="flex items-center justify-between p-4 rounded-xl border-2 cursor-pointer transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-orange-600 text-white flex items-center justify-center font-black text-xs shadow-sm">T</div>
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white text-xs">Virtual Account & QRIS Otomatis</p>
                                    <p class="text-[10px] text-slate-400">Saluran Bank Virtual Account Instan (Otomatis Aktif)</p>
                                </div>
                            </div>
                            <input type="radio" name="payment_gateway" value="tripay" x-model="gateway" class="w-4 h-4 text-orange-600">
                        </label>
                    @endif

                    <!-- Duitku Payment -->
                    @if(in_array('duitku', $activeGateways))
                        <label :class="gateway === 'duitku' ? 'border-green-600 bg-green-50/40 ring-1 ring-green-500/20' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900'" class="flex items-center justify-between p-4 rounded-xl border-2 cursor-pointer transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-green-600 text-white flex items-center justify-center font-black text-xs shadow-sm">D</div>
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white text-xs">Pembayaran Multi Gateway Instan</p>
                                    <p class="text-[10px] text-slate-400">QRIS, E-Wallet, Kartu Kredit (Otomatis Aktif)</p>
                                </div>
                            </div>
                            <input type="radio" name="payment_gateway" value="duitku" x-model="gateway" class="w-4 h-4 text-green-600">
                        </label>
                    @endif
                </div>
            </div>

            <!-- Tripay Channel Select -->
            <div x-show="gateway === 'tripay'" x-transition class="space-y-2">
                <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Pilih Saluran Virtual Account / QRIS</label>
                <select name="payment_channel" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl outline-none text-xs font-bold text-slate-800 dark:text-slate-200">
                    <option value="">-- Pilih Saluran --</option>
                    @foreach($tripayChannels as $ch)
                        <option value="{{ $ch['code'] }}">{{ $ch['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Info Rekening Bank (muncul saat pilih Transfer Manual) -->
            @if(in_array('manual', $activeGateways))
                <div x-show="gateway === 'manual'" x-transition class="space-y-3 p-4 bg-indigo-50/40 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900 rounded-2xl">
                    <input type="hidden" name="bank_account_id" :value="selectedBank">
                    <p class="text-[11px] text-slate-600 dark:text-slate-300 font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Pilih Rekening Tujuan Transfer:</span>
                    </p>

                    @if($bankAccounts->isEmpty())
                        <p class="text-xs text-slate-400 italic">Belum ada rekening yang dikonfigurasi.</p>
                    @else
                        <div class="grid grid-cols-1 gap-2.5">
                            @foreach($bankAccounts as $bank)
                                <label @click="selectedBank = '{{ $bank->id }}'" 
                                       :class="selectedBank == '{{ $bank->id }}' ? 'border-indigo-600 bg-white dark:bg-slate-900 ring-2 ring-indigo-500/20 shadow-xs' : 'border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-900/60 hover:border-slate-300'"
                                       class="p-3.5 rounded-2xl border-2 cursor-pointer transition-all block relative">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-start space-x-3 min-w-0 flex-1">
                                            <input type="radio" name="selected_bank_id" value="{{ $bank->id }}" x-model="selectedBank" class="mt-0.5 w-4 h-4 text-indigo-600 focus:ring-indigo-500 shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-extrabold text-slate-900 dark:text-white text-xs">{{ $bank->bank_name }}</span>
                                                    <span class="text-[9px] font-bold px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-300 rounded border border-indigo-100 dark:border-indigo-800">Transfer Bank</span>
                                                </div>

                                                @if($bank->account_number)
                                                    <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                                        <span class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 tracking-wide">{{ $bank->account_number }}</span>
                                                        <button type="button"
                                                                @click.stop="navigator.clipboard.writeText('{{ $bank->account_number }}'); $dispatch('copied-{{ $bank->id }}');"
                                                                x-data="{ copied: false }"
                                                                @copied-{{ $bank->id }}.window="copied = true; setTimeout(() => copied = false, 1500)"
                                                                class="text-[9px] bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 px-1.5 py-0.5 rounded font-bold hover:bg-indigo-600 hover:text-white transition-colors shrink-0">
                                                            <span x-text="copied ? '✓ Disalin' : 'Salin'">Salin</span>
                                                        </button>
                                                    </div>
                                                @endif
                                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 truncate">a.n. {{ $bank->account_name }}</p>
                                            </div>
                                        </div>
                                        @if($bank->qr_code)
                                            <div @click.stop class="shrink-0 text-center">
                                                <a href="{{ \Illuminate\Support\Str::startsWith($bank->qr_code, 'img/') ? asset($bank->qr_code) : asset('img/' . $bank->qr_code) }}" target="_blank" title="Buka QR Code" class="block">
                                                    <img src="{{ \Illuminate\Support\Str::startsWith($bank->qr_code, 'img/') ? asset($bank->qr_code) : asset('img/' . $bank->qr_code) }}" alt="QR Code {{ $bank->bank_name }}"
                                                         class="w-14 h-14 object-contain rounded-xl border border-indigo-200 dark:border-indigo-800 bg-white p-0.5 hover:scale-105 transition-transform shadow-xs">
                                                </a>
                                                <span class="text-[8px] text-indigo-500 font-bold mt-0.5 block">Tap QR</span>
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <!-- Modal PIN Popup (seperti saat login) -->
            <div x-show="showPinModal" class="fixed inset-0 bg-slate-950/60 dark:bg-slate-950/80 backdrop-blur-xs z-[999] flex items-center justify-center p-4 overflow-y-auto" x-cloak x-transition>
                <div class="relative w-full max-w-sm bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6 text-center" @click.stop="focusPinInput()">
                    
                    <!-- Close button -->
                    <button type="button" @click="showPinModal = false; pinCode = ''" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">&times;</button>
                    
                    <!-- Lock Icon -->
                    <div class="relative w-16 h-16 bg-emerald-50 dark:bg-emerald-950/60 rounded-full flex items-center justify-center mx-auto border border-emerald-100 dark:border-emerald-800 shadow-md">
                        <div class="absolute inset-0 rounded-full bg-emerald-400/10 animate-ping"></div>
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400 relative z-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>

                    <!-- Info -->
                    <div>
                        <h2 class="text-base font-black text-slate-900 dark:text-white leading-tight">Masukkan PIN Keamanan</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-xs mx-auto leading-relaxed">
                            Silakan masukkan 6 digit PIN keamanan akun siswa Anda untuk memverifikasi pembayaran menggunakan Saldo Tabungan.
                        </p>
                    </div>

                    <!-- Hidden Input inside modal (for typing) -->
                    <input type="tel" 
                           x-ref="checkoutPinInput" 
                           @input="onPinInput($event)"
                           :value="pinCode"
                           class="absolute opacity-0 -z-50 pointer-events-none w-0 h-0" 
                           maxlength="6"
                           autocomplete="one-time-code">

                    <!-- Dots UI -->
                    <div class="flex items-center justify-center gap-2 sm:gap-3 py-2">
                        <template x-for="(item, index) in Array.from({length: maxLength})">
                            <div class="w-9 h-12 sm:w-11 sm:h-14 rounded-xl sm:rounded-2xl border-2 flex items-center justify-center text-lg font-black transition-all duration-150 cursor-pointer shadow-xs select-none"
                                 :class="{
                                     'border-emerald-600 dark:border-emerald-500 bg-emerald-50/30 dark:bg-emerald-950/20 text-slate-900 dark:text-white ring-2 ring-emerald-500/20': pinCode.length === index,
                                     'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white': pinCode.length > index,
                                     'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900': pinCode.length <= index
                                 }"
                                 @click="focusPinInput()">
                                <!-- Dot -->
                                <span x-text="pinCode.length > index ? '●' : ''" class="text-xs font-black text-slate-900 dark:text-white leading-none"></span>
                            </div>
                        </template>
                    </div>

                    <div class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                        Ketuk di mana saja untuk memfokuskan input keyboard
                    </div>
                </div>
            </div>

            <!-- SUMMARY CHECKOUT BAR -->
            <div class="p-4 bg-slate-50 dark:bg-slate-900/80 rounded-2xl border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center justify-between sm:justify-start sm:gap-4 min-w-0">
                    <div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-extrabold uppercase tracking-wider">Total Pembayaran</p>
                        <p class="text-lg sm:text-xl font-black text-indigo-600 dark:text-indigo-400 leading-tight mt-0.5 font-mono" x-text="'Rp ' + formatRupiah(totalAmount)">Rp 0</p>
                    </div>
                </div>
                
                <button type="submit" 
                        :disabled="totalAmount <= 0 || (gateway === 'manual' && !selectedBank) || (gateway === 'tripay' && !paymentChannel)" 
                        class="w-full sm:w-auto px-5 py-3 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] disabled:bg-slate-200 dark:disabled:bg-slate-800 disabled:text-slate-400 text-white rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all disabled:cursor-not-allowed shrink-0 shadow-xs text-center"
                        x-text="gateway === 'manual' ? (selectedBank ? 'Simpan & Unggah Bukti' : 'Pilih Rekening Dahulu') : (gateway === 'tripay' && !paymentChannel ? 'Pilih Saluran Dahulu' : 'Bayar Tagihan')">
                    Bayar Tagihan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function paymentApp() {
        return {
            tab: '{{ $isBulananType ? "bulanan" : "bebas" }}',
            gateway: '{{ in_array('manual', $activeGateways) ? 'manual' : ($activeOnlineGateway ?? '') }}',
            selectedBank: '',
            paymentChannel: '',
            totalAmount: 0,
            selectedMonths: {},
            selectedBebasId: '',
            bebasBillMax: 0,
            monthlyList: @json($allMonthlyList),
            showPinModal: false,
            pinCode: '',
            maxLength: 6,
            showUploadProofModal: {{ session('pending_manual_transaction') ? 'true' : 'false' }},

            onFormSubmit(e) {
                if (this.gateway === 'savings' && this.pinCode.length < 6) {
                    e.preventDefault();
                    this.showPinModal = true;
                    this.pinCode = '';
                    this.$nextTick(() => {
                        this.focusPinInput();
                    });
                }
            },

            focusPinInput() {
                if (this.$refs.checkoutPinInput) {
                    this.$refs.checkoutPinInput.focus();
                }
            },

            onPinInput(e) {
                this.pinCode = e.target.value.replace(/\D/g, '').substring(0, this.maxLength);
                if (this.pinCode.length === this.maxLength) {
                    this.$nextTick(() => {
                        this.$refs.paymentForm.submit();
                    });
                }
            },

            selectMonthIndex(targetIdx) {
                const targetItem = this.monthlyList[targetIdx];
                if (!targetItem) return;

                const isAlreadySelected = !!this.selectedMonths[targetItem.id];

                if (!isAlreadySelected) {
                    for (let i = 0; i <= targetIdx; i++) {
                        const item = this.monthlyList[i];
                        this.selectedMonths[item.id] = item.amount;
                    }
                } else {
                    for (let i = targetIdx; i < this.monthlyList.length; i++) {
                        const item = this.monthlyList[i];
                        delete this.selectedMonths[item.id];
                    }
                }
                this.recalculateTotal();
            },

            selectBebasBill(id, max) {
                this.selectedBebasId = id;
                this.bebasBillMax = max;
                this.totalAmount = 0;
            },

            updateBebasAmount(val) {
                let parsed = parseInt(val) || 0;
                if (parsed > this.bebasBillMax) parsed = this.bebasBillMax;
                this.totalAmount = parsed;
            },

            recalculateTotal() {
                if (this.tab === 'bulanan') {
                    let sum = 0;
                    for (let key in this.selectedMonths) {
                        sum += this.selectedMonths[key];
                    }
                    this.totalAmount = sum;
                }
            },

            formatRupiah(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }
        }
    }
</script>
@endsection
