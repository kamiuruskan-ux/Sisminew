@extends('layouts.admin')

@section('title', 'Detail Tabungan - ' . $student->name)

@section('content')
<div class="space-y-6" x-data="{ showDepositModal: false, showWithdrawModal: false }">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.savings.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-primary transition dark:text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Tabungan
        </a>
        <a href="{{ route('admin.savings.print', $student->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Cetak Buku Mutasi
        </a>
    </div>


    <!-- Pending Manual Deposits Section -->
    @if($pendingManualDeposits->count() > 0)
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Verifikasi Setoran Pending</h3>
            <div class="grid grid-cols-1 gap-4">
                @foreach($pendingManualDeposits as $pt)
                    <div class="tailadmin-card p-5 border-l-4 border-amber-500 bg-amber-50/20 dark:bg-amber-950/10 space-y-4">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-3 border-b border-slate-200 dark:border-slate-700">
                            <div>
                                <span class="px-2.5 py-0.5 bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-350 text-[10px] font-extrabold rounded-md uppercase tracking-wider">
                                    Setoran Manual Pending
                                </span>
                                <h4 class="text-base font-extrabold text-slate-800 dark:text-white mt-1">Rp {{ number_format($pt->amount, 0, ',', '.') }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-semibold">Invoice: <span class="font-mono font-bold">{{ $pt->invoice_number }}</span> &bull; Diajukan: {{ $pt->created_at->format('d/m/Y H:i') }} WIB</p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.savings.approve-deposit', [$student->id, $pt->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI setoran tabungan ini sebesar Rp {{ number_format($pt->amount, 0, ',', '.') }}?')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl transition shadow-xs cursor-pointer">
                                        Setujui & Tambah Saldo
                                    </button>
                                </form>
                                <form action="{{ route('admin.savings.reject-deposit', [$student->id, $pt->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK setoran tabungan ini?')">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs rounded-xl transition shadow-xs cursor-pointer">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Info & Catatan -->
                            <div class="md:col-span-2 space-y-2 text-xs">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Setoran / Rekening Pengirim</span>
                                    <p class="text-xs text-slate-700 dark:text-slate-350 font-medium whitespace-pre-line mt-1 bg-white dark:bg-slate-900 p-3 rounded-xl border border-slate-150 dark:border-slate-800">
                                        {{ $pt->notes ?? 'Tidak ada catatan.' }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Bukti Transfer Image -->
                            @if($pt->payment_proof)
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Bukti Transfer</span>
                                    <div class="relative group rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-1">
                                        <a href="{{ get_public_file_url($pt->payment_proof, 'img/savings/proofs') }}" target="_blank" class="block">
                                            <img src="{{ get_public_file_url($pt->payment_proof, 'img/savings/proofs') }}" class="w-full max-h-24 object-cover rounded-lg group-hover:scale-105 transition duration-300">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition duration-200">
                                                <span class="text-white text-[10px] font-bold">Lihat Ukuran Penuh</span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Profile & Savings Balance Card -->
    <div class="tailadmin-card p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary font-bold flex items-center justify-center text-xl">
                    {{ strtoupper(substr($student->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 dark:text-white">{{ $student->name }}</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">NISN: <span class="font-mono font-semibold">{{ $student->nisn }}</span> | Kelas: <span class="font-semibold">{{ $student->schoolClass->name ?? '-' }}</span></p>
                    <p class="text-xs text-slate-400 mt-0.5">Wali: {{ $student->parent_name ?? '-' }} ({{ $student->parent_phone ?? '-' }})</p>
                </div>
            </div>

            <!-- Balance & Action Buttons -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                <div class="px-5 py-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 rounded-2xl">
                    <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase">Saldo Tabungan Saat Ini</p>
                    <p class="text-3xl font-extrabold text-emerald-700 dark:text-emerald-300">Rp {{ number_format($student->savings_balance, 0, ',', '.') }}</p>
                </div>

                <div class="flex gap-2">
                    <button @click="showDepositModal = true" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-emerald-600/20 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Setor Tunai
                    </button>
                    <button @click="showWithdrawModal = true" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-amber-600/20 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                        Tarik Tunai
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Akumulasi Setor</p>
                <p class="text-lg font-bold text-slate-800 dark:text-white mt-0.5">Rp {{ number_format($totalDeposit, 0, ',', '.') }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Akumulasi Tarik</p>
                <p class="text-lg font-bold text-slate-800 dark:text-white mt-0.5">Rp {{ number_format($totalWithdraw, 0, ',', '.') }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl">
                <p class="text-xs text-slate-500 dark:text-slate-400">Total Bayar Tagihan via Tabungan</p>
                <p class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5">Rp {{ number_format($totalPaidBills, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Mutasi Table Card -->
    <div class="tailadmin-card p-6">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Riwayat Mutasi Tabungan</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-xs font-semibold text-slate-500 uppercase tracking-wider dark:text-slate-400">
                        <th class="py-3.5 px-4">Tanggal & Waktu</th>
                        <th class="py-3.5 px-4">No. Referensi</th>
                        <th class="py-3.5 px-4">Jenis Transaksi</th>
                        <th class="py-3.5 px-4">Keterangan</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                        <th class="py-3.5 px-4 text-right">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                    @forelse($transactions as $t)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                {{ $t->created_at->format('d/m/Y H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-500 dark:text-slate-400">
                                {{ $t->reference_no }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $t->type_badge_class }}">
                                    {{ $t->type_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                {{ $t->notes ?? '-' }}
                                @if($t->creator)
                                    <span class="text-xs text-slate-400 block">Oleh: {{ $t->creator->name }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold {{ $t->transaction_type === 'deposit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-800 dark:text-white' }}">
                                {{ $t->formatted_amount }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-700 dark:text-slate-300">
                                Rp {{ number_format($t->balance_after, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 dark:text-slate-400">
                                Belum ada riwayat mutasi transaksi tabungan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Modal Setor Tunai -->
    <div x-show="showDepositModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="showDepositModal = false">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Form Setor Tunai Tabungan</h3>
                <button @click="showDepositModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('admin.savings.deposit', $student->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <x-rupiah-input name="amount" label="Nominal Setoran (Rp)" placeholder="50.000" required show-terbilang />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Keterangan (Opsional)</label>
                    <input type="text" name="notes" placeholder="Catatan setoran..." class="w-full px-4 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="record_as_financial_income" name="record_as_financial_income" value="1" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                    <label for="record_as_financial_income" class="text-xs text-slate-600 dark:text-slate-400">Catat juga ke Laporan Pemasukan Kas Sekolah</label>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="showDepositModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-xl hover:bg-opacity-90">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition">
                        Simpan Setoran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tarik Tunai -->
    <div x-show="showWithdrawModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4" @click.away="showWithdrawModal = false">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-3">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Form Tarik Tunai Tabungan</h3>
                <button @click="showWithdrawModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('admin.savings.withdraw', $student->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-xl text-xs text-amber-800 dark:text-amber-300">
                    Saldo Maksimal Penarikan: <span class="font-bold text-sm">Rp {{ number_format($student->savings_balance, 0, ',', '.') }}</span>
                </div>

                <div>
                    <x-rupiah-input name="amount" label="Nominal Penarikan (Rp)" placeholder="25.000" required show-terbilang />
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">Alasan / Catatan Penarikan</label>
                    <input type="text" name="notes" required placeholder="Contoh: Diambil oleh wali murid..." class="w-full px-4 py-2 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <button type="button" @click="showWithdrawModal = false" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-xl hover:bg-opacity-90">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl transition">
                        Proses Penarikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
