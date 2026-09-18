@extends('layouts.admin')

@section('title', 'Daftar Pengumuman')
@section('page_title', 'Pengumuman Sekolah')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(announcementId, announcementTitle) {
        this.deleteTarget = { id: announcementId, name: announcementTitle };
        this.deleteFormAction = '{{ url('admin/announcements') }}/' + announcementId;
        this.showDeleteModal = true;
    }
}">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Pengumuman Sekolah</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola informasi publik, pemberitahuan kegiatan, dan edaran untuk seluruh warga sekolah.</p>
        </div>
        @permission('create-announcements')
        <div>
            <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Pengumuman Baru</span>
            </a>
        </div>
        @endpermission
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengumuman</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($announcements->total()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Aktif</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1 tracking-tight">{{ number_format($announcements->where('is_active', true)->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Dilihat</p>
                <p class="text-2xl font-bold text-blue-600 mt-1 tracking-tight">{{ number_format($announcements->sum('views')) }} Kali</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Target Umum</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1 tracking-tight">{{ number_format($announcements->where('target', 'all')->count()) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Pengumuman</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $announcements->firstItem() ?? 0 }} - {{ $announcements->lastItem() ?? 0 }} dari {{ $announcements->total() }} pengumuman</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Pengumuman & Tanggal</th>
                        <th class="px-5 py-3">Target & Kategori</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Jumlah Pembaca</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($announcements as $announcement)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Title & Date -->
                            <td class="px-5 py-3.5 max-w-md">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs border border-indigo-100 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-xs truncate">{{ $announcement->title }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $announcement->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Target & Category -->
                            <td class="px-5 py-3.5">
                                <div class="space-y-1">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase">
                                        {{ $announcement->target == 'all' ? 'Seluruh Warga' : ($announcement->target == 'student' ? 'Siswa' : 'Guru & Staf') }}
                                    </span>
                                    <p class="text-[11px] text-slate-500 font-medium">{{ $announcement->category ?? 'Umum' }}</p>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5 text-center">
                                @if($announcement->is_active)
                                    <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase">
                                        Published
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold uppercase">
                                        Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Views -->
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-800 rounded font-semibold text-xs border border-slate-200">
                                    {{ number_format($announcement->views) }} views
                                </span>
                            </td>

                             <!-- Action Buttons -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    @permission('edit-announcements')
                                    <a href="{{ route('admin.announcements.edit', encode_id($announcement->id)) }}"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Pengumuman">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @endpermission

                                    @permission('delete-announcements')
                                    <button type="button" @click="confirmDelete({{ $announcement->id }}, '{{ addslashes($announcement->title) }}')"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors" title="Hapus Pengumuman">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endpermission
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">
                                Belum ada data pengumuman yang dipublikasikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl p-6 max-w-sm w-full shadow-xl border border-slate-200 text-center" @click.away="showDeleteModal = false">
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-rose-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Hapus Pengumuman?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">Yakin ingin menghapus pengumuman <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex space-x-2">
                <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition">
                    Batal
                </button>
                <form :action="deleteFormAction" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
