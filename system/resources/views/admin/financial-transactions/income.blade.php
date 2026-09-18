@extends('layouts.admin')

@section('title', 'Kas Masuk (Pemasukan)')
@section('page_title', 'Pemasukan Kas Sekolah')

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
    @include('components.delete-modal', ['title' => 'Hapus Transaksi Pemasukan', 'message' => 'Apakah Anda yakin ingin menghapus transaksi pemasukan :name ini? Saldo kas akan disesuaikan.'])


    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pemasukan Kas Sekolah</h1>
            <p class="text-xs text-slate-500 mt-1">Mencatat seluruh penerimaan kas masuk (SPP, Dana BOS, Sumbangan, Hibah, dll).</p>
        </div>
        <div>
            <button type="button" @click="createModalOpen = true" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Kas Masuk</span>
            </button>
        </div>
    </div>


    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Riwayat Transaksi Pemasukan Kas</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">No. Transaksi</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Rekening Penerima</th>
                        <th class="px-5 py-3">Penyetor / Sumber</th>
                        <th class="px-5 py-3 text-right">Nominal (Rp)</th>
                        <th class="px-5 py-3">Keterangan</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs font-bold text-slate-900">{{ $tx->transaction_number }}</td>
                            <td class="px-5 py-3.5 text-slate-600 font-medium">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-xs">
                                    {{ $tx->financialCategory->name }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $tx->bankAccount->bank_name }}</td>
                            <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $tx->recipient_or_payee ?: '-' }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-emerald-700 text-xs">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-slate-500 max-w-xs truncate">{{ $tx->description }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <button type="button" @click="confirmDelete({{ $tx->id }}, '{{ addslashes($tx->transaction_number) }}')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                     </svg>
                                 </button>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">Belum ada transaksi pemasukan kas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Create Modal -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="createModalOpen = false">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-900 text-sm">Catat Kas Masuk (Pemasukan)</h3>
                <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.financial-transactions.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="type" value="pemasukan">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori Pemasukan <span class="text-rose-500">*</span></label>
                        <select name="financial_category_id" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kas / Bank Penerima <span class="text-rose-500">*</span></label>
                        <select name="bank_account_id" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                            @foreach($bankAccounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->bank_name }} - {{ $acc->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <div>
                        <x-rupiah-input name="amount" label="Nominal (Rp)" required show-terbilang />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penyetor / Sumber Dana</label>
                    <input type="text" name="recipient_or_payee" placeholder="Contoh: Kementerian Pendidikan / Donatur Utama" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan / Uraian Transaksi</label>
                    <textarea name="description" rows="2" placeholder="Detail transaksi..." class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Simpan Kas Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
