@extends('layouts.canteen-vendor')

@section('title', 'Keuangan & Saldo Vendor')
@section('header_title', 'Keuangan & Saldo')

@section('content')
<div class="space-y-6 pb-24 w-full">

    <!-- Error Validation Alert -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 dark:bg-rose-950/60 border border-rose-500/30 text-rose-800 dark:text-rose-200 text-xs space-y-1 shadow-xs">
            <div class="font-bold flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Terjadi Kesalahan:</span>
            </div>
            <ul class="list-disc list-inside pl-5 space-y-0.5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Responsive 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Balance & Bank Info -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Balance Card -->
            <div class="relative overflow-hidden rounded-3xl text-white p-6 border border-indigo-500/20 shadow-xl" style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#6366f1') }} 0%, {{ Setting::get('secondary_color', '#4f46e5') }} 100%);">
                <!-- Background Pattern -->
                <div class="absolute top-0 right-0 w-36 h-36 bg-white/10 rounded-full -translate-y-10 translate-x-10 blur-sm pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-6 -translate-x-6 pointer-events-none"></div>
                
                <div class="relative z-10 space-y-4">
                    <div>
                        <span class="text-xs text-white/80 font-bold uppercase tracking-wider block">Saldo Kantin Anda</span>
                        <h2 class="text-3xl font-black tracking-tight text-white mt-1">
                            Rp {{ number_format($stall->balance, 0, ',', '.') }}
                        </h2>
                    </div>

                    <div class="pt-3 border-t border-white/15 text-xs text-white/80 flex items-center justify-between">
                        <span>Stand: <strong>{{ $stall->name }}</strong></span>
                        <span>Pemilik: <strong>{{ $stall->owner_name }}</strong></span>
                    </div>

                    <!-- Withdrawal Trigger Button -->
                    <div x-data="{ showWithdrawModal: false }">
                        <button @click="showWithdrawModal = true" type="button" class="w-full py-3 bg-white text-indigo-700 font-extrabold rounded-2xl text-xs hover:bg-slate-50 transition-all shadow-md mt-2 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span>Tarik Saldo (Withdrawal)</span>
                        </button>

                        <!-- Withdraw Modal -->
                        <div x-show="showWithdrawModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" role="dialog">
                            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md transition-opacity" @click="showWithdrawModal = false"></div>
                            
                            <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 text-left align-middle shadow-2xl transition-all w-full max-w-md mx-4 sm:mx-auto border border-slate-200 dark:border-slate-800 my-auto z-10 max-h-[90vh] overflow-y-auto scrollbar-none p-6 text-slate-800 dark:text-slate-100">
                                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
                                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Ajukan Penarikan Saldo</h3>
                                    <button type="button" @click="showWithdrawModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition-colors">&times;</button>
                                </div>

                                @if (empty($stall->bank_name) || empty($stall->bank_account_number) || empty($stall->bank_account_name))
                                    <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 rounded-2xl text-rose-700 dark:text-rose-300 text-xs mb-4">
                                        <strong>Peringatan:</strong> Rekening bank tujuan transfer belum diatur. Silakan isi form Rekening Bank terlebih dahulu.
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('canteen.vendor.finance.withdraw') }}" class="space-y-4">
                                        @csrf
                                        <div class="bg-indigo-50 dark:bg-indigo-950/40 p-4 rounded-2xl space-y-2 border border-indigo-100 dark:border-indigo-900/50">
                                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-extrabold uppercase block tracking-wider">Rekening Tujuan Transfer:</span>
                                            <div class="text-xs space-y-0.5">
                                                <p class="font-extrabold text-slate-900 dark:text-white">Bank: {{ $stall->bank_name }}</p>
                                                <p class="font-bold">No. Rekening: {{ $stall->bank_account_number }}</p>
                                                <p class="text-slate-500 dark:text-slate-400">Atas Nama: {{ $stall->bank_account_name }}</p>
                                            </div>
                                        </div>

                                        @php
                                            $minWithdrawal = (int)\App\Models\Setting::get('canteen_min_withdrawal', '10000');
                                        @endphp
                                        <div class="space-y-1">
                                            <x-rupiah-input name="amount" label="Nominal Penarikan (Rp)" placeholder="Minimal Rp {{ number_format($minWithdrawal, 0, ',', '.') }}" required show-terbilang />
                                            <span class="text-[10px] text-slate-450 dark:text-slate-500">Minimal penarikan: Rp {{ number_format($minWithdrawal, 0, ',', '.') }}</span>
                                        </div>

                                        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl text-xs shadow-md transition-all">
                                            Kirim Permintaan Penarikan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Settings Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Rekening Bank Transfer</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Atur informasi rekening bank tempat menerima pencairan saldo.</p>
                </div>

                <form method="POST" action="{{ route('canteen.vendor.finance.bank-account') }}" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $stall->bank_name) }}" required placeholder="Contoh: BANK BRI, BANK BCA, BANK MANDIRI" class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850 font-bold text-xs text-slate-900 dark:text-white">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $stall->bank_account_number) }}" required placeholder="Contoh: 123456789012" class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850 font-bold text-xs text-slate-900 dark:text-white">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Atas Nama Rekening</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $stall->bank_account_name) }}" required placeholder="Contoh: Toko Ani Sentosa" class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-850 font-bold text-xs text-slate-900 dark:text-white">
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-2xl text-xs shadow-md transition-all">
                        Simpan Rekening Bank
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Transactions & Withdrawals Lists -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Tabbed Panel -->
            <div x-data="{ currentTab: 'history' }" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
                <!-- Tabs header -->
                <div class="flex border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-850/50 p-2 gap-2 text-xs font-extrabold">
                    <button @click="currentTab = 'history'" :class="currentTab === 'history' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'" class="flex-1 py-2.5 rounded-2xl transition-all">
                        Riwayat Transaksi
                    </button>
                    <button @click="currentTab = 'withdrawals'" :class="currentTab === 'withdrawals' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'" class="flex-1 py-2.5 rounded-2xl transition-all">
                        Daftar Penarikan
                    </button>
                </div>

                <!-- Tab content: Transactions -->
                <div x-show="currentTab === 'history'" class="p-6">
                    <div class="space-y-4">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Mutasi Saldo Terakhir</h4>
                        
                        @if ($transactions->isEmpty())
                            <div class="py-8 text-center text-xs text-slate-400 dark:text-slate-500">
                                Belum ada riwayat transaksi saldo.
                            </div>
                        @else
                            <div class="space-y-3 max-h-[500px] overflow-y-auto scrollbar-none pr-1">
                                @foreach ($transactions as $tx)
                                    <div class="p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800/80 hover:border-slate-200 dark:hover:border-slate-700 bg-slate-50/50 dark:bg-slate-850/30 flex items-center justify-between gap-3 transition-all">
                                        <div class="flex items-center space-x-3 min-w-0">
                                            @if ($tx->type === 'income')
                                                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                </div>
                                            @elseif ($tx->type === 'withdraw')
                                                <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                                                </div>
                                            @endif
                                            
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $tx->description }}</p>
                                                <span class="text-[10px] text-slate-400 block mt-0.5">Ref: {{ $tx->reference_no ?? '-' }} &bull; {{ $tx->created_at->format('d M Y, H:i') }} WIB</span>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-xs font-extrabold block {{ $tx->type === 'income' || $tx->type === 'refund' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-450' }}">
                                                {{ $tx->type === 'income' || $tx->type === 'refund' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                            </span>
                                            <span class="text-[10px] text-slate-450 dark:text-slate-500 block mt-0.5">Saldo: Rp {{ number_format($tx->balance_after, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab content: Withdrawals -->
                <div x-show="currentTab === 'withdrawals'" class="p-6">
                    <div class="space-y-4">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Daftar Pengajuan Pencairan Saldo</h4>
                        
                        @if ($withdrawals->isEmpty())
                            <div class="py-8 text-center text-xs text-slate-400 dark:text-slate-500">
                                Belum ada pengajuan pencairan saldo.
                            </div>
                        @else
                            <div class="space-y-3 max-h-[500px] overflow-y-auto scrollbar-none pr-1">
                                @foreach ($withdrawals as $wd)
                                    <div class="p-4 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-850/30 space-y-3">
                                        <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200/50 dark:border-slate-700/50">
                                            <span class="text-slate-500 font-medium">{{ $wd->created_at->format('d M Y, H:i') }} WIB</span>
                                            
                                            @if ($wd->status === 'pending')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Menunggu</span>
                                            @elseif ($wd->status === 'approved')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">Berhasil Cair</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">Ditolak</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center justify-between text-xs">
                                            <div>
                                                <p class="text-[10px] text-slate-400">Nominal Penarikan</p>
                                                <p class="font-extrabold text-slate-900 dark:text-white mt-0.5">Rp {{ number_format($wd->amount, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] text-slate-400">Rekening Tujuan</p>
                                                <p class="font-bold text-slate-900 dark:text-white mt-0.5">{{ $wd->bank_name }} - {{ $wd->bank_account_number }}</p>
                                                <p class="text-[10px] text-slate-500">A/N: {{ $wd->bank_account_name }}</p>
                                            </div>
                                        </div>

                                        @if ($wd->status === 'rejected' && $wd->rejection_reason)
                                            <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-450 text-[10.5px] font-medium border border-rose-100 dark:border-rose-900/50">
                                                <strong>Alasan Ditolak:</strong> {{ $wd->rejection_reason }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
