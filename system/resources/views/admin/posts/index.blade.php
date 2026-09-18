@extends('layouts.admin')

@section('title', 'Manajemen Berita & Artikel')
@section('page_title', 'Berita & Artikel Sekolah')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(postId, postTitle) {
        this.deleteTarget = { id: postId, title: postTitle };
        this.deleteFormAction = '{{ url('admin/posts') }}/' + postId;
        this.showDeleteModal = true;
    }
}">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Manajemen Berita & Artikel</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola publikasi konten berita, artikel kegiatan, dan informasi publik sekolah.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition-colors border border-slate-300 dark:border-slate-700">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 12h10M7 17h10"/>
                </svg>
                <span>Kategori Artikel</span>
            </a>
            @permission('create-posts')
            <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Artikel Baru</span>
            </a>
            @endpermission
        </div>
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Artikel</p>
                <p class="text-2xl font-bold text-slate-900 mt-1 tracking-tight">{{ number_format($totalPosts) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l5 5v11a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dipublikasikan</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1 tracking-tight">{{ number_format($publishedPosts) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Konsep / Draft</p>
                <p class="text-2xl font-bold text-amber-600 mt-1 tracking-tight">{{ number_format($draftPosts) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.posts.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Search Keyword -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Cari Artikel</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        @input.debounce.400ms="$el.closest('form').submit()"
                        x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                        placeholder="Cari judul berita..." class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-lg p-2.5 pr-8 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    @if(request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                    @endif
                </div>
            </div>

            <!-- Filter Category -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Kategori</label>
                <select name="category_id" @change="$el.closest('form').submit()" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-lg p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                <select name="status" @change="$el.closest('form').submit()" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-lg p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Artikel & Berita</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $posts->firstItem() ?? 0 }} - {{ $posts->lastItem() ?? 0 }} dari {{ $posts->total() }} berita</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Thumbnail & Judul Artikel</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3">Tanggal Terbit</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($posts as $post)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Thumbnail & Title -->
                            <td class="px-5 py-3.5 max-w-md">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-slate-100 rounded-lg overflow-hidden shrink-0 border border-slate-200">
                                        @if($post->thumbnail)
                                            <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-xs truncate">{{ $post->title }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">Penulis: {{ $post->author?->name ?? 'Administrator' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 rounded font-semibold text-xs border border-indigo-200">
                                    {{ $post->category?->name ?? 'Umum' }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5 text-center">
                                @if($post->status === 'published')
                                    <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase">
                                        Published
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold uppercase">
                                        Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Published At -->
                            <td class="px-5 py-3.5 font-medium text-slate-600">
                                {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('d M Y') : '-' }}
                            </td>

                             <!-- Action Buttons -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    @permission('edit-posts')
                                    <a href="{{ route('admin.posts.edit', encode_id($post->id)) }}"
                                       class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Artikel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @endpermission

                                    @permission('delete-posts')
                                    <button type="button" @click="confirmDelete({{ $post->id }}, '{{ addslashes($post->title) }}')"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors" title="Hapus Artikel">
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
                                Belum ada data artikel atau berita yang dipublikasikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $posts->links() }}
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
            <h3 class="text-base font-bold text-slate-900">Hapus Artikel Berita?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-6">Yakin ingin menghapus artikel <strong x-text="deleteTarget?.title"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
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
