@extends('layouts.landing')

@php
use Illuminate\Support\Str;

$wordCount = str_word_count(strip_tags($post->content));
$readingTimeMinutes = max(1, ceil($wordCount / 200));
$readingTimeText = $readingTimeMinutes . ' menit baca';

$getBlogThumb = function($img) {
    if (!$img) return null;
    if (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://'])) {
        return $img;
    }
    if (\Illuminate\Support\Str::startsWith($img, 'img/')) {
        return asset($img);
    }
    return asset('img/blog/' . $img);
};

$mainThumb = $getBlogThumb($post->thumbnail);
@endphp

@section('title', $post->meta_title ?? $post->title)
@section('description', $post->meta_description ?? \Illuminate\Support\Str::limit($post->excerpt ?? strip_tags($post->content), 160))
@section('keywords', $post->meta_keywords ?? ($post->tags ? $post->tags->pluck('name')->implode(', ') : ''))

@section('og_title', $post->title . ' - ' . Setting::get('school_name', 'SMA Nusantara'))
@section('og_description', $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 200))
@section('og_image', $mainThumb ?? asset('img/logo.png'))
@section('og_type', 'article')
@section('og_url', route('blog.show', $post->slug))

@section('twitter_card', 'summary_large_image')
@section('twitter_title', $post->title)
@section('twitter_description', $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 200))
@section('twitter_image', $mainThumb ?? asset('img/logo.png'))

@push('styles')
<style>
    /* Article Typography & Formatting */
    .article-content {
        font-size: 1rem;
        line-height: 1.85;
        color: #334155;
    }

    @media (min-width: 640px) {
        .article-content {
            font-size: 1.125rem;
        }
    }

    .article-content p {
        margin-bottom: 1.5rem;
    }

    .article-content h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        letter-spacing: -0.025em;
        line-height: 1.3;
    }

    @media (min-width: 640px) {
        .article-content h2 {
            font-size: 1.875rem;
        }
    }

    .article-content h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-top: 2rem;
        margin-bottom: 0.875rem;
        line-height: 1.4;
    }

    .article-content blockquote {
        border-left: 4px solid #6366f1;
        padding: 1.25rem 1.5rem;
        margin: 2rem 0;
        background: #f8fafc;
        font-style: italic;
        border-radius: 0.75rem;
        color: #475569;
    }

    .article-content img {
        border-radius: 1.25rem;
        margin: 2rem 0;
        width: 100%;
        height: auto;
        object-fit: cover;
        box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.1);
    }

    .article-content ul, .article-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
    }

    .article-content ul {
        list-style-type: disc;
    }

    .article-content ol {
        list-style-type: decimal;
    }

    .article-content li {
        margin-bottom: 0.5rem;
    }

    .article-content a {
        color: #4f46e5;
        text-decoration: underline;
        font-weight: 600;
    }

    /* Reading Progress Bar */
    .reading-progress-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        z-index: 100;
    }

    .reading-progress-bar {
        height: 100%;
        background: linear-gradient(to right, #6366f1, #3b82f6, #06b6d4);
        width: 0%;
        transition: width 0.1s ease;
    }
</style>
@endpush

@section('content')
<!-- Reading Progress Bar -->
<div class="reading-progress-container">
    <div class="reading-progress-bar" id="readingBar"></div>
</div>

<!-- ==========================================
     1. HERO HEADER (Dark Navy SaaS Style)
     ========================================== -->
<section class="relative bg-slate-50 text-slate-800 pt-28 pb-20 lg:pt-36 lg:pb-24 overflow-hidden border-b border-slate-200/80">
    <!-- Ambient Glow Background -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[450px] bg-gradient-to-tr from-primary/10 via-secondary/10 to-purple-600/5 rounded-full blur-3xl pointer-events-none -z-0"></div>

    <div class="container-edunova relative z-10 text-center max-w-4xl mx-auto">
        <!-- Breadcrumbs -->
        <div class="flex items-center justify-center gap-2 text-xs font-semibold text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Beranda</a>
            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('blog') }}" class="hover:text-primary transition-colors">Berita & Artikel</a>
            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span class="text-primary font-bold truncate max-w-[150px] sm:max-w-xs">{{ \Illuminate\Support\Str::limit($post->title, 25) }}</span>
        </div>

        @if($post->category)
            <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-extrabold tracking-widest uppercase mb-6 shadow-inner">
                {{ $post->category->name }}
            </div>
        @endif

        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 mb-6 leading-tight sm:leading-tight tracking-tight break-words">
            {{ $post->title }}
        </h1>

        <!-- Author & Date Meta Pill -->
        <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-6 text-xs sm:text-sm text-slate-600">
            @if($post->author)
                <div class="flex items-center gap-2 sm:gap-3">
                    <img src="{{ $post->author->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($post->author->name).'&background=6366f1&color=fff&size=40' }}"
                         alt="{{ $post->author->name }}"
                         class="w-7 h-7 sm:w-9 sm:h-9 rounded-full border-2 border-slate-200/80 shadow-md">
                    <span class="text-slate-800 font-bold">{{ $post->author->name }}</span>
                </div>
                <div class="hidden sm:block w-1 h-1 rounded-full bg-slate-400"></div>
            @endif

            <time datetime="{{ $post->published_at ?? $post->created_at->format('Y-m-d') }}" class="flex items-center gap-1.5">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ tanggal_indo($post->published_at ?? $post->created_at, false, false, true) }}
            </time>
            
            <div class="hidden sm:block w-1 h-1 rounded-full bg-slate-400"></div>
            
            <span class="flex items-center gap-1.5 text-slate-650">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                {{ $readingTimeText }}
            </span>
        </div>
    </div>
</section>

<!-- ==========================================
     2. FEATURED COVER IMAGE
     ========================================== -->
<div class="max-w-5xl mx-auto px-4 sm:px-6 -mt-10 sm:-mt-16 relative z-20">
    <div class="aspect-[16/9] sm:aspect-video rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border-4 sm:border-8 border-white bg-slate-100">
        @if($mainThumb)
            <img src="{{ $mainThumb }}"
                 alt="{{ $post->title }}"
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center bg-slate-900 text-slate-400 p-6 text-center">
                <svg class="w-16 h-16 text-indigo-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <span class="text-xs font-bold text-slate-300 uppercase tracking-widest">{{ Setting::get('school_name', 'SMA Nusantara') }}</span>
            </div>
        @endif
    </div>
</div>

<!-- ==========================================
     3. ARTICLE BODY & SIDEBAR LAYOUT
     ========================================== -->
<article class="py-12 sm:py-20 bg-white">
    <div class="container-edunova">
        <div class="flex flex-col lg:flex-row gap-10 lg:gap-16 items-start">
            
            <!-- Left Sticky Share Sidebar (Desktop Only) -->
            <aside class="hidden lg:block lg:w-16 shrink-0 sticky top-24">
                <div class="flex flex-col items-center gap-3">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1 [writing-mode:vertical-lr] rotate-180">Bagikan</span>
                    
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . url()->current()) }}" target="_blank"
                       class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all shadow-xs" title="Bagikan ke WhatsApp">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    </a>

                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"
                       class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-xs" title="Bagikan ke Facebook">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>

                    <button onclick="copyToClipboard()"
                            class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white flex items-center justify-center transition-all shadow-xs" title="Salin Tautan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </div>
            </aside>

            <!-- Center Article Content Body -->
            <div class="flex-1 w-full max-w-3xl mx-auto">
                
                <!-- Main Article Content -->
                <div class="article-content break-words overflow-hidden" id="articleBody">
                    {!! $post->content !!}
                </div>

                <!-- Mobile Share Bar -->
                <div class="lg:hidden mt-10 pt-6 border-t border-slate-100 flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500">Bagikan Berita:</span>
                    <div class="flex items-center gap-2">
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . url()->current()) }}" target="_blank"
                           class="w-9 h-9 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-xs" title="WhatsApp">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"
                           class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-xs" title="Facebook">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <button onclick="copyToClipboard()"
                                class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center shadow-xs" title="Salin Tautan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Tags Pill Cloud -->
                @if(isset($post->tags) && $post->tags->count() > 0)
                    <div class="mt-12 pt-6 border-t border-slate-100 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-slate-400 mr-1">Tagar:</span>
                        @foreach($post->tags as $tag)
                            <a href="{{ route('blog') }}?tag={{ $tag->slug }}" 
                               class="px-3.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-primary/10 hover:text-primary transition-colors">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Author Info Footer Box -->
                @if($post->author)
                    <div class="mt-12 p-6 sm:p-8 rounded-3xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left shadow-xs">
                        <img src="{{ $post->author->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($post->author->name).'&background=6366f1&color=fff&size=80' }}"
                             alt="{{ $post->author->name }}"
                             class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border-4 border-white shadow-md shrink-0">
                        <div>
                            <span class="text-[10px] font-extrabold text-primary uppercase tracking-widest block mb-0.5">PENULIS ARTIKEL</span>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">{{ $post->author->name }}</h3>
                            <p class="text-slate-500 text-xs mt-1 leading-relaxed">
                                Tim Redaksi & Kontributor Resmi {{ Setting::get('school_name', 'SMA Nusantara') }}. Menyajikan berita terbaru dan publikasi kegiatan sekolah.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Back Link Button -->
                <div class="mt-10">
                    <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-all shadow-md active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Kembali ke Indeks Berita</span>
                    </a>
                </div>

            </div>

            <!-- Right Column Sidebar: Related News & SPMB Widget (Desktop Only) -->
            <aside class="hidden lg:block w-80 shrink-0 sticky top-24 space-y-8">
                
                <!-- Related Posts Widget -->
                @if(isset($relatedPosts) && $relatedPosts->count() > 0)
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">Berita Terkait</h3>
                        <div class="space-y-4">
                            @foreach($relatedPosts as $relatedPost)
                                <a href="{{ route('blog.show', $relatedPost->slug) }}" class="group block">
                                    <div class="flex gap-3.5 items-center">
                                        <div class="w-16 h-16 rounded-2xl overflow-hidden shrink-0 border border-slate-100 bg-slate-100 relative">
                                            @php $rThumb = $getBlogThumb($relatedPost->thumbnail); @endphp
                                            @if($rThumb)
                                                <img src="{{ $rThumb }}" alt="" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-extrabold text-slate-800 line-clamp-2 group-hover:text-primary transition-colors leading-snug">{{ $relatedPost->title }}</h4>
                                            <span class="text-[10px] text-slate-400 font-medium block mt-1">{{ tanggal_indo($relatedPost->published_at ?? $relatedPost->created_at, false, false, true) }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- SPMB Register Widget -->
                @if(Setting::get('spmb_enabled', '1') == '1')
                <div class="p-6 rounded-3xl bg-gradient-to-br from-indigo-950 via-slate-900 to-blue-950 text-white shadow-xl border border-white/10 relative overflow-hidden text-center">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/20 rounded-full blur-2xl pointer-events-none"></div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-300 block mb-1">PENDAFTARAN SISWA</span>
                    <h3 class="font-extrabold text-base leading-snug">Penerimaan Siswa Baru {{ Setting::get('school_name', 'SMA Nusantara') }}</h3>
                    <p class="text-slate-300 text-xs mt-2 leading-relaxed">Daftarkan putra-putri Anda untuk masa depan cerah berkualifikasi unggul.</p>
                    <a href="{{ route('spmb.register') }}" class="inline-flex items-center justify-center mt-5 px-5 py-2.5 bg-white text-indigo-950 rounded-full text-xs font-extrabold hover:bg-slate-100 transition-colors shadow-md uppercase tracking-wider w-full">
                        Daftar SPMB Online →
                    </a>
                </div>
                @endif

            </aside>

        </div>
    </div>
</article>

<!-- ==========================================
     4. RELATED POSTS SECTION (Mobile/Tablet Only)
     ========================================== -->
@if(isset($relatedPosts) && $relatedPosts->count() > 0)
<section class="lg:hidden py-14 bg-slate-50 border-t border-slate-100">
    <div class="container-edunova">
        <h2 class="text-xl font-extrabold text-slate-900 mb-6">Berita Terkait Lainnya</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($relatedPosts as $relatedPost)
                @php $rThumb = $getBlogThumb($relatedPost->thumbnail); @endphp
                <a href="{{ route('blog.show', $relatedPost->slug) }}" class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center gap-4 group">
                    <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0 bg-slate-100 relative">
                        @if($rThumb)
                            <img src="{{ $rThumb }}" alt="" class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">{{ tanggal_indo($relatedPost->published_at ?? $relatedPost->created_at, false, false, true) }}</span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-primary transition-colors leading-snug line-clamp-2">{{ $relatedPost->title }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@push('scripts')
<script>
    // Reading Progress Indicator
    window.onscroll = function() {
        let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        let scrolled = (winScroll / height) * 100;
        let bar = document.getElementById("readingBar");
        if (bar) {
            bar.style.width = scrolled + "%";
        }
    };

    // Copy to clipboard helper
    function copyToClipboard() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Tautan berita berhasil disalin ke clipboard!');
        });
    }
</script>
@endpush

@endsection
