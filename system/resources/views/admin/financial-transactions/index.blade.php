@extends('layouts.admin')

@section('title', 'Buku Kas Umum')

@section('content')
<div class="space-y-6" x-data="{ 
    createModalOpen: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(id, code) {
        this.deleteTarget = { id: id, name: code };
        this.deleteFormAction = '{{ url('admin/financial-transactions') }}/' + id;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Transaksi Kas', 'message' => 'Apakah Anda yakin ingin menghapus transaksi :name ini? Saldo rekening terkait akan disesuaikan secara otomatis.'])

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Jurnal Buku Kas Sekolah</h1>
            <p class="text-sm font-medium text-slate-500">Mencatat seluruh mutasi Kas Masuk (Pemasukan) dan Kas Keluar (Pengeluaran)</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.financial-categories.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h10M7 17h10"/>
                </svg>
                <span>Kategori Kas</span>
            </a>
            <button @click="createModalOpen = true" class="btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Mutasi Kas</span>
            </button>
        </div>
    </div>


    <!-- Cards Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="premium-card p-5 border-l-4 border-emerald-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kas Masuk (Pemasukan)</p>
            <p class="text-xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
        <div class="premium-card p-5 border-l-4 border-rose-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kas Keluar (Pengeluaran)</p>
            <p class="text-xl font-black text-rose-600 mt-1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>
        <div class="premium-card p-5 border-l-4 border-indigo-500">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Saldo Bersih Mutasi</p>
            <p class="text-xl font-black text-indigo-700 mt-1">Rp {{ number_format($netBalance, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="premium-card p-4">
        <form method="GET" action="{{ route('admin.financial-transactions.index') }}" class="flex flex-wrap items-end gap-3 text-xs font-semibold">
            <div>
                <label class="block text-slate-500 uppercase mb-1 text-[10px] font-bold">Tipe Mutasi</label>
                <select name="type" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-800">
                    <option value="">Semua Tipe</option>
                    <option value="pemasukan" {{ request('type') === 'pemasukan' ? 'selected' : '' }}>Pemasukan (Kas Masuk)</option>
                    <option value="pengeluaran" {{ request('type') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran (Kas Keluar)</option>
                </select>
            </div>
            <div>
                <label class="block text-slate-500 uppercase mb-1 text-[10px] font-bold">Rekening / Kas</label>
                <select name="bank_account_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-800">
                    <option value="">Semua Rekening</option>
                    @foreach($bankAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('bank_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->bank_name }} - {{ $acc->account_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-slate-500 uppercase mb-1 text-[10px] font-bold">Kategori</label>
                <select name="financial_category_id" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-800">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('financial_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }} ({{ strtoupper($cat->type) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-slate-500 uppercase mb-1 text-[10px] font-bold">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-800">
            </div>
            <div>
                <label class="block text-slate-500 uppercase mb-1 text-[10px] font-bold">Tanggal Sampai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-800">
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="premium-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Rekening / Kas</th>
                        <th>Uraian / Keterangan</th>
                        <th>Kas Masuk (Rp)</th>
                        <th>Kas Keluar (Rp)</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td>
                                <span class="font-mono text-xs font-bold text-slate-800">{{ $tx->transaction_number }}</span>
                            </td>
                            <td class="text-slate-600 text-xs font-medium">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs">
                                    {{ $tx->financialCategory->name }}
                                </span>
                            </td>
                            <td class="text-slate-700 text-xs font-semibold">{{ $tx->bankAccount->bank_name }}</td>
                            <td class="text-slate-700 text-xs font-medium">
                                <p>{{ $tx->description }}</p>
                                @if($tx->recipient_or_payee)
                                    <span class="text-[10px] text-slate-400 block mt-0.5">Penerima/Penyetor: {{ $tx->recipient_or_payee }}</span>
                                @endif
                            </td>
                            <td class="font-bold text-emerald-600 text-sm">
                                @if($tx->type === 'pemasukan')
                                    Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="font-bold text-rose-600 text-sm">
                                @if($tx->type === 'pengeluaran')
                                    Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right space-x-2">
                                @if($tx->reference_type === 'StudentPayment')
                                    <a href="{{ route('admin.student-payments.print', $tx->id) }}" target="_blank" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition inline-block" title="Cetak Kuitansi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </a>
                                @endif
                                <button type="button" @click="confirmDelete({{ $tx->id }}, '{{ addslashes($tx->reference_number ?: 'TRX-'.$tx->id) }}')" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Transaksi">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                     </svg>
                                 </button>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                Belum ada data mutasi kas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Create Mutasi Modal -->
    <div x-show="createModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="createModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl z-10" x-data="{ txType: 'pemasukan' }">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Catat Mutasi Kas Baru</h3>
                <form action="{{ route('admin.financial-transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jenis Mutasi</label>
                        <select name="type" x-model="txType" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-sm">
                            <option value="pemasukan">Kas Masuk (Pemasukan)</option>
                            <option value="pengeluaran">Kas Keluar (Pengeluaran)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Kategori</label>
                            <select name="financial_category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-sm">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <template x-if="txType === '{{ $cat->type }}'">
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    </template>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Rekening / Kas Target</label>
                            <select name="bank_account_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-sm">
                                @foreach($bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->bank_name }} - {{ $acc->account_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal Transaksi</label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-sm">
                        </div>
                        <div>
                            <x-rupiah-input name="amount" label="Nominal (Rp)" required show-terbilang />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Penyetor / Penerima (Pihak Ke-3)</label>
                        <input type="text" name="recipient_or_payee" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-sm" placeholder="e.g. Toko ATK Sejahtera / Bapak Heri">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Keterangan / Uraian Mutasi</label>
                        <textarea name="description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm" placeholder="Detail transaksi..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Upload Struk / Bukti Transaksi</label>
                        <x-file-upload name="proof_file" accept=".jpg,.jpeg,.png,.pdf" label="Upload Struk / Nota Kas" help="Seret & lepas foto/PDF nota di sini (Maks 2MB)" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-3">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 text-sm font-bold text-slate-500">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
