@extends('layouts.student-mobile')

@section('title', $book->title)

@section('content')
<div class="space-y-6 pb-20">
    <!-- Back Header -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('student.library.index') }}" class="p-2.5 bg-white text-slate-700 rounded-xl border border-slate-200 shadow-2xs hover:bg-slate-50 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-extrabold text-slate-900 line-clamp-1 tracking-tight">{{ $book->title }}</h1>
            <p class="text-xs text-slate-500 font-semibold">Perpustakaan Digital</p>
        </div>
    </div>

    <!-- Book Detail Hero Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col md:flex-row gap-5">
        <div class="w-32 h-44 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 mx-auto md:mx-0 shadow-md">
            @if($book->cover_path)
                <img src="{{ \Illuminate\Support\Str::startsWith($book->cover_path, 'img/') ? asset($book->cover_path) : asset('img/' . $book->cover_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center bg-gradient-to-br from-indigo-50 to-purple-50 text-indigo-500">
                    <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="text-[10px] font-extrabold text-slate-700 line-clamp-2">{{ $book->title }}</span>
                </div>
            @endif
        </div>

        <div class="flex-1 space-y-3">
            <div>
                <span class="px-2.5 py-1 text-xs font-extrabold bg-purple-50 text-purple-700 rounded-lg border border-purple-100">
                    {{ $book->category }}
                </span>
                <h2 class="text-xl font-extrabold text-slate-900 mt-2 tracking-tight">{{ $book->title }}</h2>
                <p class="text-xs font-bold text-indigo-600 mt-1">Penulis: {{ $book->author ?? 'Tidak terdaftar' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs font-semibold text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Penerbit</span>
                    <span>{{ $book->publisher ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">ISBN</span>
                    <span>{{ $book->isbn ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Stok Perpustakaan</span>
                    <span class="{{ $book->stock > 0 ? 'text-emerald-600 font-extrabold' : 'text-rose-600 font-bold' }}">
                        {{ $book->stock > 0 ? $book->stock . ' Eksemplar Tersedia' : 'Habis Dipinjam' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Format Digital</span>
                    <span>{{ $book->file_path ? 'PDF E-Book' : 'Buku Fisik' }}</span>
                </div>
            </div>

            @if($book->description)
                <div>
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ringkasan / Sinopsis</h4>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed">{{ $book->description }}</p>
                </div>
            @endif

            @if($book->file_path)
                <div class="pt-2">
                    <a href="{{ \Illuminate\Support\Str::startsWith($book->file_path, ['http://', 'https://', 'doc/', 'img/']) ? asset($book->file_path) : (file_exists(public_path('doc/' . $book->file_path)) ? asset('doc/' . $book->file_path) : asset('img/' . $book->file_path)) }}" target="_blank" download class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Berkas E-Book PDF</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- PDF Reader Frame (if file_path exists) -->
    @if($book->file_path)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    <h3 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider">Pembaca E-Book Langsung</h3>
                </div>
                <a href="{{ \Illuminate\Support\Str::startsWith($book->file_path, ['http://', 'https://', 'doc/', 'img/']) ? asset($book->file_path) : (file_exists(public_path('doc/' . $book->file_path)) ? asset('doc/' . $book->file_path) : asset('img/' . $book->file_path)) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:underline">
                    Buka di Tab Baru
                </a>
            </div>
            <div class="w-full h-[600px] bg-slate-900">
                <iframe src="{{ \Illuminate\Support\Str::startsWith($book->file_path, ['http://', 'https://', 'doc/', 'img/']) ? asset($book->file_path) : (file_exists(public_path('doc/' . $book->file_path)) ? asset('doc/' . $book->file_path) : asset('img/' . $book->file_path)) }}" class="w-full h-full border-0" title="{{ $book->title }}"></iframe>
            </div>
        </div>
    @endif
</div>
@endsection
