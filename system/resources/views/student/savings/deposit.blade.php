@extends('layouts.student-mobile')

@section('title', 'Setor Tabungan')
@section('header_title', 'Setor Tabungan')

@section('content')
<div class="space-y-6 pb-12 w-full" x-data="{ 
    gateway: '{{ $pendingDeposit ? $pendingDeposit->payment_gateway : (count($activeGateways) > 0 ? $activeGateways[0] : '') }}',
    selectedBank: '',
    paymentChannel: '{{ $pendingDeposit ? $pendingDeposit->payment_method_code : '' }}',
    amount: '{{ $pendingDeposit ? (int)$pendingDeposit->amount : '' }}',
    showUploadProofModal: {{ session('pending_manual_transaction') ? 'true' : 'false' }},
    pendingTransactionId: '{{ session('pending_manual_transaction') }}',
    showCancelModal: false,
    selectGateway(g) {
        this.gateway = g;
        if (g !== 'tripay') this.paymentChannel = '';
    }
}">
    
    <!-- Top Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('student.savings.index') }}" class="inline-flex items-center space-x-2 px-3 py-1.5 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-extrabold text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition shadow-2xs">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span>Kembali ke Tabungan</span>
        </a>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-350 text-xs font-bold font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-350 text-xs font-bold font-semibold space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- MAIN DEPOSIT FORM -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm space-y-5">
        <div class="pb-2 border-b border-slate-100 dark:border-slate-700">
            <h4 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider">
                @if($pendingDeposit)
                    Ubah Setoran Pending
                @else
                    Form Setor Tabungan
                @endif
            </h4>
            <p class="text-[10px] text-slate-400 mt-0.5">
                @if($pendingDeposit)
                    Ubah nominal atau metode pembayaran setoran pending Anda (Invoice: {{ $pendingDeposit->invoice_number }}).
                @else
                    Masukkan nominal yang ingin disetor dan pilih metode pembayaran di bawah.
                @endif
            </p>
        </div>

        <form action="{{ route('student.savings.deposit.checkout') }}" method="POST" class="space-y-5">
            @csrf
            @if($pendingDeposit)
                <input type="hidden" name="pending_id" value="{{ $pendingDeposit->id }}">
            @endif
            
            <!-- Nominal Input -->
            <div>
                <x-rupiah-input name="amount" label="Nominal Setoran (Rp)" placeholder="10.000" required show-terbilang />
            </div>

            <!-- Quick Amounts Buttons -->
            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="amount = '20000'" class="py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-850 rounded-xl text-center text-xs font-extrabold text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800">20K</button>
                <button type="button" @click="amount = '50000'" class="py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-850 rounded-xl text-center text-xs font-extrabold text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800">50K</button>
                <button type="button" @click="amount = '100000'" class="py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-850 rounded-xl text-center text-xs font-extrabold text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-800">100K</button>
            </div>

            <!-- Payment Gateways Options -->
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase mb-2.5">Metode Pembayaran <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 gap-2">
                    <input type="hidden" name="payment_gateway" :value="gateway">

                    @if(in_array('manual', $activeGateways))
                        <label @click="selectGateway('manual')" 
                               :class="gateway === 'manual' ? 'border-indigo-600 bg-indigo-50/40 dark:bg-indigo-950/30 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60'"
                               class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between gap-3 select-none">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs shrink-0">M</div>
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white text-xs">Transfer Bank Manual</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Transfer bank manual & unggah bukti transfer.</p>
                                </div>
                            </div>
                            <span :class="gateway === 'manual' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'" class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase">Terpilih</span>
                        </label>
                    @endif

                    @if(in_array('midtrans', $activeGateways))
                        <label @click="selectGateway('midtrans')" 
                               :class="gateway === 'midtrans' ? 'border-indigo-600 bg-indigo-50/40 dark:bg-indigo-950/30 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60'"
                               class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between gap-3 select-none">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs shrink-0">O</div>
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white text-xs">Midtrans (Virtual Account / QRIS)</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Proses pembayaran otomatis via Midtrans.</p>
                                </div>
                            </div>
                            <span :class="gateway === 'midtrans' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'" class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase">Terpilih</span>
                        </label>
                    @endif

                    @if(in_array('tripay', $activeGateways))
                        <label @click="selectGateway('tripay')" 
                               :class="gateway === 'tripay' ? 'border-indigo-600 bg-indigo-50/40 dark:bg-indigo-950/30 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-805 bg-white dark:bg-slate-900/60'"
                               class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between gap-3 select-none">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-xs shrink-0">T</div>
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white text-xs">Tripay Gateway</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Berbagai saluran pembayaran otomatis.</p>
                                </div>
                            </div>
                            <span :class="gateway === 'tripay' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-805 text-slate-400'" class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase">Terpilih</span>
                        </label>
                    @endif

                    @if(in_array('duitku', $activeGateways))
                        <label @click="selectGateway('duitku')" 
                               :class="gateway === 'duitku' ? 'border-indigo-600 bg-indigo-50/40 dark:bg-indigo-950/30 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60'"
                               class="p-4 rounded-2xl border-2 cursor-pointer transition-all flex items-center justify-between gap-3 select-none">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-xs shrink-0">D</div>
                                <div>
                                    <p class="font-extrabold text-slate-900 dark:text-white text-xs">Duitku Payment</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Pembayaran online cepat via Duitku.</p>
                                </div>
                            </div>
                            <span :class="gateway === 'duitku' ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'" class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase">Terpilih</span>
                        </label>
                    @endif
                </div>
            </div>

            <!-- Tripay Channel Selection (If Tripay is selected) -->
            <div x-show="gateway === 'tripay'" x-cloak class="space-y-2.5">
                <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase">Pilih Saluran Pembayaran Tripay <span class="text-rose-500">*</span></label>
                <input type="hidden" name="payment_channel" :value="paymentChannel">
                <div class="grid grid-cols-2 gap-2 max-h-[14rem] overflow-y-auto pr-1">
                    @foreach($tripayChannels as $ch)
                        @if($ch['active'])
                            <div @click="paymentChannel = '{{ $ch['code'] }}'"
                                 :class="paymentChannel === '{{ $ch['code'] }}' ? 'border-indigo-600 bg-indigo-50/40 dark:bg-indigo-950/20 ring-1 ring-indigo-500' : 'border-slate-200 dark:border-slate-800 hover:border-indigo-300 dark:hover:border-indigo-700 bg-white dark:bg-slate-900'"
                                 class="p-2.5 rounded-xl border text-center cursor-pointer select-none transition-all flex flex-col items-center justify-center space-y-1.5 min-h-[5.5rem]">
                                <img src="{{ $ch['icon_url'] }}" alt="{{ $ch['name'] }}" class="h-6 object-contain max-w-[80%] rounded">
                                <span class="text-[9px] font-black text-slate-700 dark:text-slate-300 truncate w-full">{{ $ch['name'] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Manual Transfer Bank Accounts (If Manual is selected) -->
            <div x-show="gateway === 'manual'" x-cloak class="space-y-2.5">
                <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase">Informasi Rekening Sekolah</label>
                <div class="grid grid-cols-1 gap-2.5">
                    @foreach($bankAccounts as $bank)
                        <div class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="font-extrabold text-slate-800 dark:text-white text-xs">{{ $bank->bank_name }}</p>
                                @if($bank->account_number)
                                    <div class="mt-0.5 flex items-center gap-1.5 flex-wrap">
                                        <span class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 tracking-wide">{{ $bank->account_number }}</span>
                                        <button type="button"
                                                @click.prevent="navigator.clipboard.writeText('{{ $bank->account_number }}'); alert('Nomor rekening disalin!')"
                                                class="text-[9px] bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded font-bold hover:bg-emerald-600 hover:text-white transition-colors shrink-0">
                                            Salin
                                        </button>
                                    </div>
                                @endif
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 truncate">a.n. {{ $bank->account_name }}</p>
                            </div>
                            @if($bank->qr_code)
                                <div class="shrink-0 text-center">
                                    <a href="{{ \Illuminate\Support\Str::startsWith($bank->qr_code, 'img/') ? asset($bank->qr_code) : asset('img/' . $bank->qr_code) }}" target="_blank" title="Lihat QR Code" class="block">
                                        <img src="{{ \Illuminate\Support\Str::startsWith($bank->qr_code, 'img/') ? asset($bank->qr_code) : asset('img/' . $bank->qr_code) }}" alt="QR Code" class="w-12 h-12 object-contain rounded-xl border border-slate-200 dark:border-slate-700 bg-white p-0.5">
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    :disabled="!amount || amount < 10000 || (gateway === 'tripay' && !paymentChannel)"
                    :class="(amount >= 10000 && (gateway !== 'tripay' || paymentChannel)) ? 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 cursor-not-allowed'"
                    class="w-full py-3.5 rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all duration-150 text-center font-semibold">
                <span>
                    @if($pendingDeposit)
                        Simpan Perubahan Setoran
                    @else
                        @{{ gateway === 'manual' ? 'Buat Setoran Transfer Bank' : 'Proses Pembayaran Setoran' }}
                    @endif
                </span>
            </button>
        </form>

        @if($pendingDeposit)
            <button type="button" @click="showCancelModal = true" class="w-full mt-2 py-3.5 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 text-rose-600 dark:text-rose-400 font-extrabold text-[11px] uppercase tracking-wider rounded-xl transition shadow-xs text-center cursor-pointer">
                Batalkan Setoran Pending
            </button>

            <!-- Modal Konfirmasi Batal Setoran -->
            <div x-show="showCancelModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" x-transition x-cloak style="display: none;">
                <div class="relative bg-white dark:bg-slate-800 max-w-sm w-full rounded-2xl p-6 shadow-2xl border border-slate-200 dark:border-slate-700 text-center">
                    <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 bg-rose-100 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-full">
                        <svg class="w-7 h-7 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-2">Batalkan Setoran Pending?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                        Apakah Anda yakin ingin membatalkan transaksi setoran pending sebesar <strong>Rp {{ number_format($pendingDeposit->amount, 0, ',', '.') }}</strong> ini?
                    </p>
                    <div class="flex items-center justify-center space-x-3">
                        <button type="button" @click="showCancelModal = false" class="px-5 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition font-bold text-xs cursor-pointer">
                            Kembali
                        </button>
                        <form action="{{ route('student.savings.deposit.cancel', $pendingDeposit->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition font-bold text-xs shadow-md shadow-rose-600/30 cursor-pointer">
                                Ya, Batalkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Active Pending Deposits Checklist -->
    @if($pendingTransactions->count() > 0)
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm space-y-3">
            <h4 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-700 pb-2">Transaksi Setoran Pending</h4>
            <div class="space-y-2">
                @foreach($pendingTransactions as $pt)
                    <div class="p-3 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-150 dark:border-slate-850 flex items-center justify-between text-xs">
                        <div>
                            <p class="font-extrabold text-slate-900 dark:text-white">Setoran Rp {{ number_format($pt->amount, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">Invoice: {{ $pt->invoice_number }} &bull; {{ strtoupper($pt->payment_gateway) }}</p>
                        </div>
                        @if($pt->payment_url)
                            <a href="{{ $pt->payment_url }}" target="_blank" class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 font-bold rounded-lg border border-indigo-100 dark:border-indigo-900 hover:bg-indigo-600 hover:text-white transition font-semibold">Bayar</a>
                        @else
                            <button type="button" @click="pendingTransactionId = '{{ $pt->id }}'; showUploadProofModal = true" class="px-2.5 py-1 bg-amber-50 dark:bg-amber-955 text-amber-600 dark:text-amber-400 font-bold rounded-lg border border-amber-100 dark:border-amber-900 hover:bg-amber-600 hover:text-white transition font-semibold">Upload Bukti</button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Modal Kirim Bukti Transfer -->
    <div x-show="showUploadProofModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" x-transition x-cloak>
        <div class="relative bg-white dark:bg-slate-800 max-w-sm w-full rounded-2xl p-5 shadow-2xl border border-slate-200 dark:border-slate-700" @click.away="showUploadProofModal = false">
            <button @click="showUploadProofModal = false" class="absolute top-4 right-4 p-2 rounded-xl bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-205 dark:hover:bg-slate-950 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="text-center space-y-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mx-auto shadow-2xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Kirim Bukti Transfer</h3>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold">Silakan unggah foto/screenshot bukti transfer bank Anda agar setoran dapat dikonfirmasi oleh bendahara.</p>
            </div>

            <form action="{{ route('student.savings.deposit.upload-proof') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="transaction_id" :value="pendingTransactionId">

                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 dark:text-slate-400 uppercase">Rekening Tujuan Transfer <span class="text-rose-500">*</span></label>
                    <select name="bank_account_id" required class="w-full text-xs p-3 bg-slate-50 dark:bg-slate-900 border border-slate-250 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 font-semibold text-slate-800 dark:text-slate-200">
                        <option value="" disabled selected>Pilih rekening tujuan transfer...</option>
                        @foreach($bankAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->bank_name }} - {{ $acc->account_number }} (a.n. {{ $acc->account_name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 dark:text-slate-400 uppercase">File Bukti Transfer (Foto/Struk) <span class="text-rose-500">*</span></label>
                    <x-file-upload name="payment_proof" accept="image/*" required="true" label="Upload Foto Bukti Transfer" help="Seret & lepas foto struk transfer di sini" />
                </div>

                <div class="space-y-1">
                    <label class="block text-[9px] font-black text-slate-500 dark:text-slate-400 uppercase">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" placeholder="Misal: Dari rekening atas nama Budi" class="w-full text-xs p-3 bg-slate-50 dark:bg-slate-900 border border-slate-250 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 h-16 resize-none font-semibold"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-xs text-center">Unggah & Konfirmasi</button>
            </form>
        </div>
    </div>

</div>
@endsection
