@extends('layouts.admin')

@section('title', 'Master Kategori & Poin Pelanggaran BK')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(actionUrl, itemName) {
        this.deleteTarget = { name: itemName };
        this.deleteFormAction = actionUrl;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Kategori Pelanggaran', 'message' => 'Apakah Anda yakin ingin menghapus kategori :name ini? Tindakan ini tidak dapat dibatalkan.'])
    <!-- Header Bar -->
    <div class="flex items-center justify-between bg-white dark:bg-[#1A222C] p-6 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] shadow-xs">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center font-bold text-xl border border-amber-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-[#1C2434] dark:text-white">Master Kategori &amp; Poin Pelanggaran</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Pengaturan jenis-jenis pelanggaran tata tertib, bobot poin sanksi, dan rekomendasi sanksi BK</p>
            </div>
        </div>

        <a href="{{ route('admin.bk.violations.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-[#24303F] hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-[#2E3A47] transition">
            Kembali ke Daftar
        </a>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah Kategori -->
        <div class="bg-white dark:bg-[#1A222C] p-6 rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] shadow-xs h-fit">
            <h3 class="font-extrabold text-sm text-[#1C2434] dark:text-white pb-3 border-b border-slate-200 dark:border-[#2E3A47] mb-4">Tambah Kategori Baru</h3>
            
            <form action="{{ route('admin.bk.violations.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nama Pelanggaran *</label>
                    <input type="text" name="name" required placeholder="Contoh: Merokok di Lingkungan Sekolah" 
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tingkat *</label>
                        <select name="level" required class="w-full px-3 py-2.5 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white">
                            <option value="ringan">Ringan</option>
                            <option value="sedang">Sedang</option>
                            <option value="berat">Berat</option>
                            <option value="sangat_berat">Sangat Berat</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Bobot Poin *</label>
                        <input type="number" name="points" min="1" value="10" required 
                               class="w-full px-3 py-2.5 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs font-black text-rose-600 dark:text-rose-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Rekomendasi Sanksi</label>
                    <textarea name="penalty_recommendation" rows="2" placeholder="Contoh: Pemanggilan orang tua & Surat SP-1" 
                              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white leading-relaxed"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Keterangan / Deskripsi</label>
                    <textarea name="description" rows="2" placeholder="Deskripsi penjelasan aturan tata tertib..." 
                              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-xl text-xs text-slate-800 dark:text-white leading-relaxed"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                    Simpan Kategori Pelanggaran
                </button>
            </form>
        </div>

        <!-- Table Master Kategori -->
        <div class="lg:col-span-2 bg-white dark:bg-[#1A222C] rounded-3xl border border-[#E2E8F0] dark:border-[#2E3A47] overflow-hidden shadow-xs">
            <div class="p-6 border-b border-slate-200 dark:border-[#2E3A47]">
                <h3 class="font-extrabold text-sm text-[#1C2434] dark:text-white">Daftar Master Kategori &amp; Bobot Poin ({{ $categories->count() }})</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#24303F] font-extrabold uppercase text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-[#2E3A47]">
                            <th class="py-4 px-6">Nama Pelanggaran</th>
                            <th class="py-4 px-6">Tingkat</th>
                            <th class="py-4 px-6 text-center">Poin</th>
                            <th class="py-4 px-6">Rekomendasi Sanksi</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-[#2E3A47]">
                        @forelse($categories as $cat)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#24303F]/50 transition">
                                <td class="py-4 px-6 font-extrabold text-[#1C2434] dark:text-white">
                                    {{ $cat->name }}
                                    <div class="text-[10px] text-slate-400 font-normal mt-0.5">{{ $cat->description ?: 'Tanpa keterangan' }}</div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    @if($cat->level == 'ringan')
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-blue-500/10 text-blue-600 border border-blue-500/20">Ringan</span>
                                    @elseif($cat->level == 'sedang')
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">Sedang</span>
                                    @elseif($cat->level == 'berat')
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20">Berat</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-red-600 text-white shadow-xs">Sangat Berat</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center font-black text-rose-600 dark:text-rose-400 whitespace-nowrap">
                                    +{{ $cat->points }}
                                </td>
                                <td class="py-4 px-6 text-slate-600 dark:text-slate-300">
                                    {{ $cat->penalty_recommendation ?: '-' }}
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button type="button" @click="confirmDelete('{{ route('admin.bk.violations.categories.destroy', $cat->id) }}', '{{ addslashes($cat->name) }}')" class="p-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 rounded-lg transition cursor-pointer" title="Hapus Kategori">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 font-medium">Belum ada data master kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
