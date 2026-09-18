@extends('layouts.admin')

@section('title', 'Perpustakaan Digital (E-Library)')
@section('page_title', 'Katalog & Stok Buku Perpustakaan')

@section('content')
<div class="space-y-6" x-data="{ 
    showAddBookModal: false, 
    editBook: null,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(id, title) {
        this.deleteTarget = { id: id, name: title };
        this.deleteFormAction = '{{ url('admin/books') }}/' + id;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Buku', 'message' => 'Apakah Anda yakin ingin menghapus buku :name ini dari katalog perpustakaan? Tindakan ini tidak dapat dibatalkan.'])

    <!-- Header Card -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Perpustakaan Digital & Buku Sekolah</h2>
                    <p class="text-xs text-slate-500 font-medium">Kelola e-book PDF digital, stok fisik buku perpustakaan, pencarian ISBN, dan kategori koleksi.</p>
                </div>
            </div>
        </div>
        <div>
            <button @click="showAddBookModal = true" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Buku Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row gap-4 justify-between">
        <form method="GET" action="{{ route('admin.books.index') }}" class="flex-1 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                    @input.debounce.400ms="$el.closest('form').submit()"
                    x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                    placeholder="Cari berdasarkan judul, penulis, ISBN..." class="w-full pl-10 pr-8 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-semibold">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                @endif
            </div>
            
            <select name="category" @change="$el.closest('form').submit()" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-indigo-500">
                <option value="">-- Semua Kategori --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Books Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Sampul & Judul Buku</th>
                        <th>Penulis & Penerbit</th>
                        <th>Kategori & ISBN</th>
                        <th>Stok Fisik</th>
                        <th>Berkas Digital</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-16 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                                        @if($book->cover_path)
                                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 text-sm leading-snug">{{ $book->title }}</p>
                                        @if($book->description)
                                            <p class="text-xs text-slate-500 font-medium line-clamp-1 mt-0.5">{{ $book->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-xs font-bold text-slate-800">{{ $book->author ?? '-' }}</p>
                                <p class="text-[11px] text-slate-500 font-semibold">{{ $book->publisher ?? '-' }}</p>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 text-xs font-bold bg-purple-50 text-purple-700 rounded-lg border border-purple-100">
                                    {{ $book->category }}
                                </span>
                                @if($book->isbn)
                                    <p class="text-[10px] text-slate-400 font-bold mt-1">ISBN: {{ $book->isbn }}</p>
                                @endif
                            </td>
                            <td>
                                @if($book->stock > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-lg gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        {{ $book->stock }} Eksemplar
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-rose-50 text-rose-600 rounded-lg">
                                        Habis
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($book->file_path)
                                    <a href="{{ \Illuminate\Support\Str::startsWith($book->file_path, ['http://', 'https://', 'doc/', 'img/']) ? asset($book->file_path) : (file_exists(public_path('doc/' . $book->file_path)) ? asset('doc/' . $book->file_path) : asset('img/' . $book->file_path)) }}" target="_blank" class="inline-flex items-center space-x-1 px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold hover:bg-indigo-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span>PDF E-Book</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Buku Fisik</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.books.toggle', $book->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-extrabold rounded-full transition-colors {{ $book->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                        {{ $book->is_active ? '● Aktif' : 'Non-Aktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="editBook = {{ json_encode($book) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors" title="Edit Buku">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" @click="confirmDelete({{ $book->id }}, '{{ addslashes($book->title) }}')" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-colors cursor-pointer" title="Hapus Buku">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400 font-medium">
                                Belum ada koleksi buku di perpustakaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($books->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $books->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: Tambah Buku Baru -->
    <div x-show="showAddBookModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl border border-slate-200 max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="showAddBookModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Tambah Koleksi Buku Baru</h3>
                <button @click="showAddBookModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Buku <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Fisika Dasar untuk SMA/SMK" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penulis / Pengarang</label>
                        <input type="text" name="author" placeholder="Nama Penulis" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penerbit</label>
                        <input type="text" name="publisher" placeholder="Nama Penerbit" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori <span class="text-rose-500">*</span></label>
                        <input type="text" name="category" required placeholder="Pelajaran, Novel, Sains" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ISBN (Opsional)</label>
                        <input type="text" name="isbn" placeholder="978-xxx-xxx" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stok Fisik <span class="text-rose-500">*</span></label>
                        <input type="number" name="stock" value="1" min="0" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Foto Sampul / Cover Image</label>
                        <input type="file" name="cover" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Berkas Digital E-Book (PDF)</label>
                        <input type="file" name="pdf_file" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi / Ringkasan Buku</label>
                    <textarea name="description" rows="2" placeholder="Sinopsis atau deskripsi buku..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-medium"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                    <button type="button" @click="showAddBookModal = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary">
                        Simpan Buku
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Edit Buku -->
    <template x-if="editBook">
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-slate-200 max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="editBook = null">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-extrabold text-slate-900 text-base">Edit Data Buku</h3>
                    <button @click="editBook = null" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form method="POST" :action="'{{ url('admin/books') }}/' + editBook.id" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Buku <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" :value="editBook.title" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penulis / Pengarang</label>
                            <input type="text" name="author" :value="editBook.author" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penerbit</label>
                            <input type="text" name="publisher" :value="editBook.publisher" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori <span class="text-rose-500">*</span></label>
                            <input type="text" name="category" :value="editBook.category" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">ISBN</label>
                            <input type="text" name="isbn" :value="editBook.isbn" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Stok Fisik <span class="text-rose-500">*</span></label>
                            <input type="number" name="stock" :value="editBook.stock" min="0" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ganti Cover Image</label>
                            <input type="file" name="cover" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ganti Berkas PDF E-Book</label>
                            <input type="file" name="pdf_file" accept=".pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi / Ringkasan Buku</label>
                        <textarea name="description" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-medium" x-text="editBook.description"></textarea>
                    </div>

                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="editBook.is_active" class="w-4 h-4 text-indigo-600 rounded">
                        <label for="edit_is_active" class="text-xs font-bold text-slate-800">Status Aktif Tersedia</label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end space-x-2">
                        <button type="button" @click="editBook = null" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary">
                            Update Data Buku
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
