@extends('layouts.student-mobile')

@section('title', 'Perpustakaan Digital (E-Library)')
@section('header_title', 'E-Library Sekolah')

@section('content')
<div class="space-y-6" x-data="{ stockModal: { show: false, stock: 0 } }">

    <!-- Banner Header -->
    <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 text-white rounded-3xl p-6 shadow-xl relative overflow-hidden">
        <div class="flex items-center justify-between relative z-10">
            <div>
                <span class="px-3 py-1 bg-white/10 text-emerald-200 text-[10px] font-extrabold uppercase tracking-wider rounded-full border border-white/20 inline-block mb-2">
                    Digital E-Library
                </span>
                <h2 class="text-lg sm:text-xl font-extrabold text-white">Katalog Perpustakaan Digital</h2>
                <p class="text-xs text-emerald-200 mt-1">Akses ribuan modul pembelajaran, e-book, dan koleksi bacaan sekolah.</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/20">
                <svg class="w-7 h-7 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs space-y-3">
        <form method="GET" action="{{ route('student.library.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, penulis, atau topik..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white pl-10 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all cursor-pointer">
                Cari Buku
            </button>
        </form>
    </div>

    <!-- Book Grid Catalog -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-sm">Koleksi Buku E-Library</h3>
            <span class="text-xs font-bold text-slate-500">{{ $books->count() }} Judul Ditemukan</span>
        </div>

        @if($books->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 sm:gap-6">
                @foreach($books as $book)
                    <div class="bg-white rounded-2xl p-3.5 border border-slate-200 shadow-xs flex flex-col justify-between group hover:border-emerald-300 transition-all">
                        <div class="space-y-3">
                            <!-- Book Cover Image -->
                            <div class="aspect-[3/4] rounded-xl bg-slate-100 overflow-hidden relative border border-slate-100">
                                @if($book->cover_path)
                                    <img src="{{ \Illuminate\Support\Str::startsWith($book->cover_path, 'img/') ? asset($book->cover_path) : asset('img/' . $book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center bg-gradient-to-br from-slate-800 to-indigo-950 text-white">
                                        <svg class="w-8 h-8 text-emerald-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-300">{{ $book->category }}</span>
                                    </div>
                                @endif
                                <span class="absolute top-2 left-2 px-2 py-0.5 bg-slate-900/80 backdrop-blur-md text-white text-[9px] font-bold rounded-md border border-white/20">
                                    {{ $book->category }}
                                </span>
                            </div>

                            <!-- Book Info -->
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-xs line-clamp-2 leading-snug group-hover:text-emerald-700 transition-colors">
                                    {{ $book->title }}
                                </h4>
                                <p class="text-[11px] text-slate-400 mt-1 truncate">Penulis: {{ $book->author ?? '-' }}</p>
                            </div>
                        </div>

                        <!-- Read / View Action Button -->
                        <div class="pt-3 border-t border-slate-100 mt-3">
                            @if($book->file_path)
                                <a href="{{ \Illuminate\Support\Str::startsWith($book->file_path, ['http://', 'https://', 'doc/', 'img/']) ? asset($book->file_path) : (file_exists(public_path('doc/' . $book->file_path)) ? asset('doc/' . $book->file_path) : asset('img/' . $book->file_path)) }}" target="_blank" class="w-full py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] rounded-xl border border-emerald-200 transition-all flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Baca E-Book</span>
                                </a>
                            @else
                                <button type="button"
                                    @click="stockModal = { show: true, stock: {{ $book->stock }} }"
                                    class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] rounded-xl border border-slate-200 transition-all flex items-center justify-center gap-1 cursor-pointer">
                                    <span>Tersedia: {{ $book->stock }} Pcs</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-2xl border border-slate-200">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <p class="text-xs text-slate-500 font-medium">Buku tidak ditemukan atau katalog masih kosong.</p>
            </div>
        @endif
    </div>

    <!-- Modal Info Stok Fisik Buku -->
    <div
        x-show="stockModal.show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display:none;"
    >
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="stockModal.show = false"></div>
        <div
            x-show="stockModal.show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-xs bg-white rounded-3xl shadow-2xl overflow-hidden z-10"
        >
            <div class="bg-gradient-to-r from-teal-500 to-emerald-500 px-6 pt-5 pb-8 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h2 class="text-white font-extrabold text-base">Buku Fisik Tersedia</h2>
                <p class="text-emerald-100 text-xs mt-1">Perpustakaan Sekolah</p>
            </div>
            <div class="-mt-4 px-5 pb-5">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-4 text-center">
                    <p class="text-xs text-slate-500 mb-1">Stok Fisik Tersedia</p>
                    <p class="text-3xl font-black text-emerald-600" x-text="stockModal.stock + ' Pcs'"></p>
                    <p class="text-[11px] text-slate-400 mt-1">Kunjungi perpustakaan sekolah untuk meminjam buku ini.</p>
                </div>
                <button @click="stockModal.show = false"
                    class="w-full py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-all">
                    Mengerti
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
