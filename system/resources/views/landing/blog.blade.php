@extends('layouts.landing')

@section('title', 'Berita & Artikel - ' . Setting::get('school_name', 'SDIT AL-FAHMI PALU'))

@section('content')

<!-- ==========================================
     1. SUBPAGE HERO HEADER (Dark Navy SaaS Style)
     ========================================== -->
<section class="relative bg-slate-50 text-slate-800 pt-28 pb-16 lg:pt-36 lg:pb-20 overflow-hidden border-b border-slate-200/80">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[400px] bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="container-edunova relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-primary/10 border border-primary/20 text-xs font-bold text-primary mb-4">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                INFORMASI & NEWSROOM
            </div>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
                Berita & Artikel Terbaru
            </h1>
            <p class="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed font-normal">
                Kanal kabar resmi {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }} mencakup prestasi siswa, tips edukasi, dan pengumuman kegiatan sekolah.
            </p>
        </div>
    </div>
</section>

<!-- ==========================================
     2. FILTER & SEARCH BAR
     ========================================== -->
<div class="sticky top-16 z-30 bg-white/90 backdrop-blur-xl border-b border-slate-100 py-3 shadow-sm">
    <div class="container-edunova flex flex-col sm:flex-row gap-3 sm:items-center justify-between">
        <form method="GET" action="{{ route('blog') }}" class="w-full sm:max-w-xs">
            <div class="relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="search" value="{{ request('search') }}" placeholder="Cari artikel..." class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2 text-xs font-semibold outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all bg-slate-50" />
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
            </div>
        </form>

        <div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-none py-1 max-w-full -mx-1 px-1">
            @php $categories = \App\Models\Category::withCount('posts')->whereHas('posts', fn($q) => $q->published())->get(); @endphp
            <a href="{{ route('blog') }}{{ request('search') ? '?search='.request('search') : '' }}" 
               class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all {{ !request('category') ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Kategori
            </a>
            @foreach($categories as $category)
                <a href="{{ route('blog') }}?category={{ $category->slug }}{{ request('search') ? '&search='.request('search') : '' }}" 
                   class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all {{ request('category') == $category->slug ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- ==========================================
     3. POSTS GRID
     ========================================== -->
<section class="py-16 bg-slate-50/60">
    <div class="container-edunova">
        @if(isset($posts) && $posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    @php
                        $thumbUrl = $post->thumbnail_url ?? (Setting::get('logo_path') ? asset(Setting::get('logo_path')) : asset('img/logo.png'));
                    @endphp
                    <a href="{{ route('blog.show', $post->slug) }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden group hover:shadow-2xl hover:border-primary/40 transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1.5 cursor-pointer" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                        <div class="h-48 overflow-hidden relative">
                            <img src="{{ $thumbUrl }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                            @if($post->category)
                                <div class="absolute top-3 left-3 px-3 py-1 rounded-md bg-blue-600 text-white text-[11px] font-bold shadow-md">
                                    {{ $post->category->name }}
                                </div>
                            @endif
                        </div>
                        <div class="p-6 flex flex-col justify-between flex-grow">
                            <div>
                                <span class="text-[11px] font-semibold text-slate-400 block mb-2">
                                    {{ tanggal_indo($post->created_at ?? now(), false, false, true) }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors leading-snug line-clamp-2">
                                    {{ $post->title }}
                                </h3>
                                <p class="text-xs text-slate-500 leading-relaxed mb-4 line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?? $post->content), 100) }}
                                </p>
                            </div>
                            <div class="inline-flex items-center text-xs font-bold text-blue-600 group-hover:text-blue-800 gap-1 mt-auto">
                                <span>Baca Selengkapnya</span>
                                <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-12 flex justify-center">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 shadow-sm max-w-lg mx-auto">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Artikel</h3>
                <p class="text-xs text-slate-500 mb-4">Artikel yang Anda cari belum tersedia saat ini.</p>
                <a href="{{ route('blog') }}" class="px-5 py-2 rounded-full bg-blue-600 text-white font-bold text-xs">Lihat Semua Artikel</a>
            </div>
        @endif
    </div>
</section>

@endsection
