@extends('layouts.student-mobile')

@section('title', 'Tabungan Saya')
@section('header_title', 'Tabungan Siswa')

@section('content')
<div class="space-y-6 pb-12 w-full" x-data="{ showUploadModal: false }">
    
    <!-- Notifications -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl dark:bg-emerald-950/30 dark:border-emerald-800 dark:text-emerald-350 font-bold text-xs">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl dark:bg-rose-950/30 dark:border-rose-800 dark:text-rose-350 font-bold text-xs">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Pending Manual Deposit Notification -->
    @if($pendingManualDeposit)
        @if(!$pendingManualDeposit->payment_proof)
            <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-amber-500 text-white flex items-center justify-center shrink-0 font-bold shadow-xs rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-extrabold text-amber-900 dark:text-amber-200 text-xs">Bukti Transfer Menunggu Unggah</p>
                        <p class="text-[11px] text-amber-700 dark:text-amber-400 mt-0.5">Setoran tabungan manual sebesar <strong class="font-black">Rp {{ number_format($pendingManualDeposit->amount, 0, ',', '.') }}</strong> belum dikirim bukti pembayarannya.</p>
                    </div>
                </div>
                <button @click="showUploadModal = true" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition shrink-0 text-center">
                    Unggah Bukti
                </button>
            </div>
        @else
            <div class="p-4 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900 rounded-2xl flex items-center space-x-3 shadow-xs">
                <div class="w-10 h-10 bg-indigo-600 text-white flex items-center justify-center shrink-0 font-bold shadow-xs rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-extrabold text-indigo-900 dark:text-indigo-200 text-xs">Setoran Menunggu Verifikasi</p>
                    <p class="text-[11px] text-indigo-700 dark:text-indigo-400 mt-0.5">Setoran manual sebesar <strong class="font-black">Rp {{ number_format($pendingManualDeposit->amount, 0, ',', '.') }}</strong> sedang diproses & menunggu persetujuan admin.</p>
                </div>
            </div>
        @endif
    @endif
    
    <!-- Digital Passbook / ATM Card Banner -->
    <div class="relative bg-gradient-to-br from-slate-900 via-emerald-950 to-teal-900 text-white rounded-3xl p-6 sm:p-8 shadow-2xl overflow-hidden border border-emerald-500/20">
        <!-- Decorative Glow Effects -->
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-teal-400/15 rounded-full blur-2xl pointer-events-none"></div>
 
        <div class="relative z-10 space-y-6">
            <!-- Card Header: Title & Chip -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-emerald-300/90">TABUNGAN SISWA</p>
                        <p class="text-xs font-semibold text-slate-300">Kartu Tabungan Digital</p>
                    </div>
                </div>
 
                <!-- Contactless Icon -->
                <div class="text-emerald-400/60">
                    <svg class="w-7 h-7 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18a6 6 0 100-12 6 6 0 000 12zM15 15a3 3 0 100-6 3 3 0 000 6z"></path>
                    </svg>
                </div>
            </div>
 
            <!-- Card Body: Balance -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase text-emerald-200/80 tracking-wider">Saldo Tabungan Saat Ini</p>
                    <div class="flex items-baseline space-x-2 mt-1">
                        <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                            Rp {{ number_format($student->savings_balance, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>
                <div>
                    <a href="{{ route('student.savings.deposit.show') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs rounded-2xl shadow-lg shadow-emerald-500/20 transition-all border border-emerald-400/20 w-full sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Setor Tabungan / Top Up
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards (3 Col Responsive) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <!-- Deposit -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200 dark:border-slate-700/80 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Setoran</p>
                <h3 class="text-lg sm:text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($totalDeposit, 0, ',', '.') }}</h3>
            </div>
            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center shrink-0 border border-emerald-100 dark:border-emerald-800/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </div>

        <!-- Withdraw -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200 dark:border-slate-700/80 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Penarikan</p>
                <h3 class="text-lg sm:text-xl font-black text-amber-600 dark:text-amber-400 mt-1">Rp {{ number_format($totalWithdraw, 0, ',', '.') }}</h3>
            </div>
            <div class="w-11 h-11 bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center shrink-0 border border-amber-100 dark:border-amber-800/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
            </div>
        </div>

        <!-- Tagihan via Tabungan -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200 dark:border-slate-700/80 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Bayar SPP / Tagihan</p>
                <h3 class="text-lg sm:text-xl font-black text-indigo-600 dark:text-indigo-400 mt-1">Rp {{ number_format($totalPaidBills, 0, ',', '.') }}</h3>
            </div>
            <div class="w-11 h-11 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-800/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Mutasi Tabungan Section -->
    <div class="bg-white dark:bg-slate-800/90 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 rounded-lg flex items-center justify-center border border-emerald-100 dark:border-emerald-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm">Riwayat Mutasi Tabungan</h3>
            </div>
            <span class="text-xs text-slate-400 font-semibold">{{ $transactions->total() }} Transaksi</span>
        </div>

        <!-- Desktop View (Table) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/80 dark:bg-slate-800 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-5">Waktu</th>
                        <th class="py-3.5 px-5">Jenis Transaksi</th>
                        <th class="py-3.5 px-5">Keterangan</th>
                        <th class="py-3.5 px-5 text-right">Nominal</th>
                        <th class="py-3.5 px-5 text-right">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-5 text-slate-500 dark:text-slate-400 font-mono">
                                {{ $t->created_at->format('d/m/Y H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider rounded-full border {{ $t->type_badge_class }}">
                                    {{ $t->type_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-slate-700 dark:text-slate-200">
                                {{ $t->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-5 text-right font-black {{ $t->transaction_type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-white' }}">
                                {{ $t->formatted_amount }}
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-slate-600 dark:text-slate-300">
                                Rp {{ number_format($t->balance_after, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 font-medium">
                                Belum ada transaksi mutasi tabungan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Card List for HP Screen) -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-700/60">
            @forelse($transactions as $t)
                <div class="p-4 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded-full border {{ $t->type_badge_class }}">
                            {{ $t->type_label }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-mono">
                            {{ $t->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    <div class="flex items-baseline justify-between pt-0.5">
                        <div class="min-w-0 pr-3">
                            <p class="text-xs font-bold text-slate-800 dark:text-white line-clamp-1">
                                {{ $t->notes ?? '-' }}
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-black {{ $t->transaction_type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-white' }}">
                                {{ $t->formatted_amount }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end text-[10px] text-slate-400 border-t border-slate-50 dark:border-slate-800/80 pt-1.5">
                        <span>Saldo Setelahnya: <strong class="font-mono text-slate-600 dark:text-slate-300">Rp {{ number_format($t->balance_after, 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-400 text-xs font-medium">
                    Belum ada transaksi mutasi tabungan.
                </div>
            @endforelse
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>

    <!-- Modal Upload Bukti Transfer Manual -->
    @if($pendingManualDeposit && !$pendingManualDeposit->payment_proof)
        <div x-show="showUploadModal" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showUploadModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl transition-all w-full max-w-md border border-slate-200 dark:border-slate-800 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-tight">Unggah Bukti Transfer</h3>
                        <button type="button" @click="showUploadModal = false" class="text-slate-400 hover:text-slate-650 text-lg font-bold">&times;</button>
                    </div>

                    <form action="{{ route('student.savings.upload-proof') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                        @csrf
                        <input type="hidden" name="transaction_id" value="{{ $pendingManualDeposit->id }}">

                        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-850">
                            <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Nominal Transfer</span>
                            <span class="text-lg font-black text-emerald-600 dark:text-emerald-400 mt-1 block">Rp {{ number_format($pendingManualDeposit->amount, 0, ',', '.') }}</span>
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase mb-1">Rekening Tujuan</label>
                            <select name="bank_account_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                <option value="" disabled selected>Pilih rekening tujuan transfer...</option>
                                @foreach($bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->bank_name }} - {{ $acc->account_number }} a.n. {{ $acc->account_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase mb-1">Bukti Transfer (Gambar) <span class="text-rose-500">*</span></label>
                            <x-file-upload name="payment_proof" accept="image/*" required="true" label="Upload Foto Struk Transfer" help="Seret & lepas bukti transfer di sini" />
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase mb-1">Catatan / Keterangan (Opsional)</label>
                            <input type="text" name="notes" placeholder="Contoh: Transfer dari rekening BRI a.n..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-xs">
                        </div>

                        <div class="pt-2 flex justify-end space-x-2">
                            <button type="button" @click="showUploadModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-805 text-slate-700 dark:text-slate-300 rounded-xl font-bold">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-xs">Kirim Bukti Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
