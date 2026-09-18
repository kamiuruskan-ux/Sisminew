@extends('layouts.admin')

@section('title', 'Kategori Keuangan')
@section('page_title', 'Kategori Keuangan Sekolah')

@section('content')
<div class="space-y-6" x-data="{ 
    createModalOpen: false, 
    editModalOpen: false, 
    editItem: {},
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(id, name) {
        this.deleteTarget = { id: id, name: name };
        this.deleteFormAction = '{{ url('admin/financial-categories') }}/' + id;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Kategori Keuangan', 'message' => 'Apakah Anda yakin ingin menghapus kategori :name ini? Tindakan ini tidak dapat dibatalkan.'])


    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Kategori Keuangan Sekolah</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola pengelompokan kategori transaksi Pemasukan (Kas Masuk) & Pengeluaran (Kas Keluar).</p>
        </div>
        <div>
            <button type="button" @click="createModalOpen = true" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Kategori Baru</span>
            </button>
        </div>
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kategori</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($categories->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori Pemasukan</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1 tracking-tight">{{ number_format($categories->where('type', 'pemasukan')->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m-7 7l7-7 7 7"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori Pengeluaran</p>
                <p class="text-2xl font-bold text-rose-600 mt-1 tracking-tight">{{ number_format($categories->where('type', 'pengeluaran')->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7l-7 7-7-7"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Kategori Keuangan</h3>
            <span class="text-xs text-slate-500">Total {{ $categories->count() }} kategori terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Kode Kategori</th>
                        <th class="px-5 py-3">Nama Kategori</th>
                        <th class="px-5 py-3">Tipe Transaksi</th>
                        <th class="px-5 py-3">Keterangan</th>
                        <th class="px-5 py-3 text-center">Jumlah Transaksi</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Code -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded bg-slate-100 text-slate-800 font-mono font-bold text-xs border border-slate-200">
                                    {{ $cat->code }}
                                </span>
                            </td>

                            <!-- Name -->
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $cat->name }}
                            </td>

                            <!-- Type -->
                            <td class="px-5 py-3.5">
                                @if($cat->type === 'pemasukan')
                                    <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase">
                                        Pemasukan (Kas Masuk)
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold uppercase">
                                        Pengeluaran (Kas Keluar)
                                    </span>
                                @endif
                            </td>

                            <!-- Description -->
                            <td class="px-5 py-3.5 text-slate-500">
                                {{ $cat->description ?: '-' }}
                            </td>

                            <!-- Count -->
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded font-semibold text-xs border border-indigo-100">
                                    {{ number_format($cat->financial_transactions_count) }} Transaksi
                                </span>
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <button type="button" @click="editItem = { id: {{ $cat->id }}, code: '{{ $cat->code }}', name: '{{ addslashes($cat->name) }}', type: '{{ $cat->type }}', description: '{{ addslashes($cat->description) }}', is_active: {{ $cat->is_active ? 1 : 0 }} }; editModalOpen = true"
                                            class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Kategori">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <button type="button" @click="confirmDelete({{ $cat->id }}, '{{ addslashes($cat->name) }}')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" title="Hapus Kategori">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">
                                Belum ada kategori keuangan yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="createModalOpen = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-900 text-sm">Tambah Kategori Keuangan Baru</h3>
                <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.financial-categories.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="Contoh: KAT-IN-01, KAT-EX-01" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Dana BOS, Gaji Guru, Maintenance AC" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Transaksi <span class="text-rose-500">*</span></label>
                    <select name="type" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        <option value="pemasukan">Pemasukan (Kas Masuk)</option>
                        <option value="pengeluaran">Pengeluaran (Kas Keluar)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan</label>
                    <textarea name="description" rows="2" placeholder="Catatan opsional..." class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none"></textarea>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" checked id="is_active_cat_chk" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="is_active_cat_chk" class="text-xs font-semibold text-slate-700 cursor-pointer">Kategori Aktif</label>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="editModalOpen = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-bold text-slate-900 text-sm">Edit Kategori Keuangan</h3>
                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="'{{ url('admin/financial-categories') }}/' + editItem.id" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" x-model="editItem.code" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="editItem.name" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Transaksi <span class="text-rose-500">*</span></label>
                    <select name="type" x-model="editItem.type" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        <option value="pemasukan">Pemasukan (Kas Masuk)</option>
                        <option value="pengeluaran">Pengeluaran (Kas Keluar)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan</label>
                    <textarea name="description" x-model="editItem.description" rows="2" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 resize-none"></textarea>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_active" value="1" :checked="editItem.is_active" id="is_active_cat_edit" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="is_active_cat_edit" class="text-xs font-semibold text-slate-700 cursor-pointer">Kategori Aktif</label>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Perbarui Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
