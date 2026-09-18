@extends('layouts.landing')

@section('title', 'Galeri Momen & Dokumentasi - ' . Setting::get('school_name', 'SMA Nusantara'))

@section('content')

@php
    $galleryItems = collect(isset($galleries) && count($galleries) > 0 ? $galleries->items() : [])->map(function($g) {
        $imgPath = $g->image_url ?? (\Illuminate\Support\Str::startsWith($g->image, 'http') 
            ? $g->image 
            : (\Illuminate\Support\Str::startsWith($g->image, 'img/') ? asset($g->image) : asset('img/gallery/' . $g->image)));
        return [
            'id' => $g->id,
            'category' => $g->category ? $g->category->slug : 'umum',
            'catName' => $g->category ? $g->category->name : 'Kegiatan Sekolah',
            'title' => $g->title,
            'img' => $imgPath,
            'desc' => $g->description ?? 'Dokumentasi kegiatan dan momen berharga di sekolah.',
            'date' => $g->created_at ? $g->created_at->format('d M Y') : date('d M Y'),
        ];
    });

    if ($galleryItems->isEmpty()) {
        $defaultImg = Setting::get('school_hero_image') ? asset(Setting::get('school_hero_image')) : (Setting::get('logo_path') ? asset(Setting::get('logo_path')) : asset('img/logo.png'));
        $galleryItems = collect([
            [
                'id' => 1,
                'category' => 'akademik',
                'catName' => 'Akademik & Sains',
                'title' => 'Praktikum Sains & Pengujian Lab Terpadu',
                'img' => $defaultImg,
                'desc' => 'Siswa melakukan eksperimen sains dengan pendampingan guru ahli.',
                'date' => date('d M Y')
            ],
            [
                'id' => 2,
                'category' => 'olahraga',
                'catName' => 'Olahraga & Prestasi',
                'title' => 'Kegiatan Olahraga & Prestasi Pelajar',
                'img' => $defaultImg,
                'desc' => 'Kegiatan pembinaan prestasi dan kompetisi olahraga antar pelajar.',
                'date' => date('d M Y')
            ]
        ]);
    }

    $uniqueCategories = isset($categories) && count($categories) > 0 
        ? $categories 
        : collect([
            (object)['slug' => 'akademik', 'name' => 'Akademik & Sains'],
            (object)['slug' => 'olahraga', 'name' => 'Olahraga & Prestasi'],
            (object)['slug' => 'seni-budaya', 'name' => 'Seni & Budaya'],
            (object)['slug' => 'fasilitas', 'name' => 'Fasilitas Kampus'],
            (object)['slug' => 'ekstrakurikuler', 'name' => 'Ekstrakurikuler'],
        ]);
@endphp

<script>
function galleryApp() {
    return {
        activeCategory: 'all',
        searchQuery: '',
        lightboxOpen: false,
        currentIndex: 0,
        items: @json($galleryItems),

        openLightbox(index) {
            this.currentIndex = index;
            this.lightboxOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeLightbox() {
            this.lightboxOpen = false;
            document.body.classList.remove('overflow-hidden');
        },
        nextImage() {
            if (!this.filteredItems || !this.filteredItems.length) return;
            this.currentIndex = (this.currentIndex + 1) % this.filteredItems.length;
        },
        prevImage() {
            if (!this.filteredItems || !this.filteredItems.length) return;
            this.currentIndex = (this.currentIndex - 1 + this.filteredItems.length) % this.filteredItems.length;
        },
        get filteredItems() {
            return (this.items || []).filter(item => {
                const matchesCategory = this.activeCategory === 'all' || item.category === this.activeCategory;
                const matchesSearch = !this.searchQuery || 
                    (item.title && item.title.toLowerCase().includes(this.searchQuery.toLowerCase())) || 
                    (item.desc && item.desc.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                    (item.catName && item.catName.toLowerCase().includes(this.searchQuery.toLowerCase()));
                return matchesCategory && matchesSearch;
            });
        }
    };
}
</script>

<div x-data="galleryApp()" 
     @keydown.window.escape="closeLightbox()"
     @keydown.window.arrow-right="if(lightboxOpen) nextImage()"
     @keydown.window.arrow-left="if(lightboxOpen) prevImage()">

<!-- ==========================================
     1. SUBPAGE HERO HEADER (Dark Navy SaaS Style)
     ========================================== -->
<section class="relative bg-slate-50 text-slate-800 pt-28 pb-16 lg:pt-36 lg:pb-20 overflow-hidden border-b border-slate-200/80">
    <!-- Ambient Glow Background -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[750px] h-[500px] bg-gradient-to-tr from-primary/10 via-secondary/10 to-purple-600/5 rounded-full blur-3xl pointer-events-none -z-0"></div>

    <div class="container-edunova relative z-10">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Beranda</a>
            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span class="text-primary font-bold">Galeri Momen</span>
        </div>

        <div class="max-w-3xl" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-xs font-extrabold text-primary mb-4">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                DOKUMENTASI & MOMEN TERBAIK
            </div>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
                Galeri Momen & Dokumentasi Sekolah
            </h1>
            <p class="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed font-normal">
                Jelajahi suasana pembelajaran interaktif, kejuaraan siswa, festival budaya, dan fasilitas unggulan di {{ Setting::get('school_name', 'SMA Nusantara') }}.
            </p>
        </div>
    </div>
</section>

<!-- ==========================================
     2. GALLERY FILTER & GRID SECTION
     ========================================== -->
<section class="py-16 lg:py-24 bg-gradient-to-b from-slate-50 via-white to-slate-50 min-h-screen">
    <div class="container-edunova">
        
        <!-- Controls Bar: Category Filters & Real-time Search -->
        <div class="flex flex-col lg:flex-row items-center justify-between gap-6 mb-12" data-aos="fade-up">
            
            <!-- Category Filter Pills Bar (Swipeable on Mobile) -->
            <div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-none py-1 max-w-full -mx-1 px-1 w-full lg:w-auto">
                <button @click="activeCategory = 'all'" 
                        :class="activeCategory === 'all' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/10 ring-2 ring-primary/30 font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 font-semibold'" 
                        class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs tracking-wide transition-all shrink-0">
                    Semua Momen
                </button>

                @foreach($uniqueCategories as $cat)
                    <button @click="activeCategory = '{{ $cat->slug }}'" 
                            :class="activeCategory === '{{ $cat->slug }}' ? 'bg-slate-900 text-white shadow-md shadow-slate-900/10 ring-2 ring-primary/30 font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 font-semibold'" 
                            class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-full text-xs tracking-wide transition-all shrink-0">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            <!-- Search Bar Input -->
            <div class="relative w-full lg:w-72 shrink-0">
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari momen foto..." 
                       class="w-full pl-10 pr-9 py-2.5 rounded-full bg-white border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary shadow-xs transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-bold p-1">✕</button>
            </div>

        </div>

        <!-- Filter Results Count Indicator -->
        <div class="flex items-center justify-between mb-6 px-1">
            <span class="text-xs font-semibold text-slate-500">
                Menampilkan <span x-text="filteredItems.length" class="font-bold text-slate-800"></span> foto dokumentasi
            </span>
        </div>

        <!-- Empty State (No Items Found) -->
        <div x-show="filteredItems.length === 0" x-cloak class="text-center py-20 bg-white rounded-3xl border border-slate-200/80 shadow-sm max-w-lg mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800 mb-1">Foto Tidak Ditemukan</h3>
            <p class="text-xs text-slate-500 max-w-xs mx-auto mb-4">Tidak ada dokumentasi foto yang cocok dengan pencarian atau kategori ini.</p>
            <button @click="activeCategory = 'all'; searchQuery = ''" class="px-4 py-2 rounded-full bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                Reset Filter
            </button>
        </div>

        <!-- Photo Grid Container (2 Grid Columns on Mobile) -->
        <div x-show="filteredItems.length > 0" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
            
            <template x-for="(item, idx) in filteredItems" :key="item.id || idx">
                <div @click="openLightbox(idx)" 
                     class="group rounded-2xl sm:rounded-3xl overflow-hidden relative shadow-sm border border-slate-200/80 bg-white cursor-pointer hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1.5 aspect-[4/3] flex flex-col justify-between">
                    
                    <!-- Thumbnail Image -->
                    <img :src="item.img" :alt="item.title" class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-700 ease-out">
                    
                    <!-- Category Badge Tag (Top Left) -->
                    <div class="absolute top-2.5 left-2.5 sm:top-3.5 sm:left-3.5 z-10 px-2 sm:px-3 py-0.5 sm:py-1 rounded-full bg-slate-950/75 backdrop-blur-md text-[9px] sm:text-[10px] font-extrabold text-white border border-white/20 shadow-xs max-w-[70%] truncate">
                        <span x-text="item.catName"></span>
                    </div>

                    <!-- Date Badge Tag (Top Right) -->
                    <div class="absolute top-2.5 right-2.5 sm:top-3.5 sm:right-3.5 z-10 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full bg-slate-950/60 backdrop-blur-md text-[9px] sm:text-[10px] font-medium text-slate-300 border border-white/10 hidden sm:block">
                        <span x-text="item.date"></span>
                    </div>

                    <!-- Hover Gradient Overlay & Information -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 p-3 sm:p-6 flex flex-col justify-end">
                        <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-indigo-400 block mb-0.5 sm:mb-1" x-text="item.catName"></span>
                        <h4 class="text-xs sm:text-sm font-extrabold text-white leading-snug line-clamp-2" x-text="item.title"></h4>
                        <p class="text-[10px] sm:text-[11px] text-slate-300 line-clamp-1 mt-0.5 sm:mt-1 font-normal hidden sm:block" x-text="item.desc"></p>
                        
                        <div class="mt-2 sm:mt-4 flex items-center justify-between pt-2 sm:pt-3 border-t border-white/15">
                            <span class="text-[10px] sm:text-[11px] font-bold text-indigo-300 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Lihat Foto</span>
                            </span>
                            <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-white/20 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>
                        </div>
                    </div>

                </div>
            </template>

        </div>

        <!-- Modern Proportional Pagination Bar -->
        @if(isset($galleries) && method_exists($galleries, 'hasPages') && $galleries->hasPages())
            <div class="mt-14 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200/60 pt-8 max-w-5xl mx-auto px-2">
                <!-- Results Counter -->
                <span class="text-xs font-semibold text-slate-500">
                    Halaman <span class="font-extrabold text-slate-900">{{ $galleries->currentPage() }}</span> dari <span class="font-extrabold text-slate-900">{{ $galleries->lastPage() }}</span>
                    @if(method_exists($galleries, 'total'))
                        <span class="text-slate-400 font-normal ml-1">({{ $galleries->total() }} foto dokumentasi)</span>
                    @endif
                </span>

                <!-- Pagination Controls -->
                <div class="inline-flex items-center gap-1.5 p-1.5 rounded-full bg-white border border-slate-200/80 shadow-xs">
                    {{-- Previous Page Link --}}
                    @if ($galleries->onFirstPage())
                        <span class="w-9 h-9 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </span>
                    @else
                        <a href="{{ $galleries->previousPageUrl() }}" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 font-bold text-xs flex items-center justify-center transition-all active:scale-95 shadow-xs" title="Halaman Sebelumnya">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    @endif

                    {{-- Page Number Links --}}
                    @foreach (range(1, $galleries->lastPage()) as $page)
                        @if ($page == $galleries->currentPage())
                            <span class="w-9 h-9 rounded-full bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shadow-md shadow-slate-900/10 ring-2 ring-primary/30">
                                {{ $page }}
                            </span>
                        @elseif ($page == 1 || $page == $galleries->lastPage() || abs($page - $galleries->currentPage()) <= 1)
                            <a href="{{ $galleries->url($page) }}" class="w-9 h-9 rounded-full bg-transparent hover:bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center transition-all">
                                {{ $page }}
                            </a>
                        @elseif (abs($page - $galleries->currentPage()) == 2)
                            <span class="w-5 text-center text-slate-300 font-bold text-xs">...</span>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($galleries->hasMorePages())
                        <a href="{{ $galleries->nextPageUrl() }}" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 font-bold text-xs flex items-center justify-center transition-all active:scale-95 shadow-xs" title="Halaman Selanjutnya">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span class="w-9 h-9 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif

    </div>
</section>

<!-- ==========================================
     3. INTERACTIVE LIGHTBOX MODAL WITH THUMBNAILS
     ========================================== -->
<div x-show="lightboxOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-950/95 backdrop-blur-xl" 
     x-cloak>
    
    <!-- Modal Dialog Box -->
    <div @click.away="closeLightbox()" class="relative max-w-5xl w-full bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-white/10 flex flex-col max-h-[92vh]">
        
        <!-- Header Bar -->
        <div class="p-4 px-6 border-b border-white/10 bg-slate-950/80 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-primary/20 text-indigo-300 border border-primary/30 text-[10px] font-extrabold uppercase tracking-wider" x-text="filteredItems[currentIndex]?.catName"></span>
                <span class="text-xs text-slate-400 font-medium" x-text="filteredItems[currentIndex]?.date"></span>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-300 bg-white/5 px-3 py-1 rounded-full border border-white/10">
                    <span x-text="currentIndex + 1"></span> / <span x-text="filteredItems.length"></span>
                </span>
                
                <button @click="closeLightbox()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-rose-600 text-white flex items-center justify-center transition-colors border border-white/10 focus:outline-none" title="Tutup Modal">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Navigation Prev Button -->
        <button @click="prevImage()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-slate-900/80 hover:bg-primary text-white flex items-center justify-center transition-all border border-white/20 shadow-xl active:scale-95">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>

        <!-- Navigation Next Button -->
        <button @click="nextImage()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-slate-900/80 hover:bg-primary text-white flex items-center justify-center transition-all border border-white/20 shadow-xl active:scale-95">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>

        <!-- Main Photo Display -->
        <div class="relative flex-1 bg-black/80 flex items-center justify-center p-4 min-h-[300px] overflow-hidden">
            <img :src="filteredItems[currentIndex]?.img" 
                 :alt="filteredItems[currentIndex]?.title" 
                 class="max-h-[65vh] w-auto max-w-full object-contain rounded-xl shadow-2xl transition-all duration-300">
        </div>

        <!-- Thumbnail Strip Slider -->
        <div class="px-6 py-2.5 bg-slate-950/60 border-t border-white/5 overflow-x-auto flex items-center justify-center gap-2 scrollbar-none shrink-0">
            <template x-for="(item, idx) in filteredItems" :key="idx">
                <button @click="currentIndex = idx" 
                        class="w-12 h-10 rounded-lg overflow-hidden shrink-0 border-2 transition-all"
                        :class="currentIndex === idx ? 'border-primary ring-2 ring-primary/40 scale-105 opacity-100' : 'border-transparent opacity-40 hover:opacity-80'">
                    <img :src="item.img" :alt="item.title" class="w-full h-full object-cover">
                </button>
            </template>
        </div>

        <!-- Photo Caption Footer -->
        <div class="p-5 px-6 bg-[#050914] text-white border-t border-white/10 shrink-0">
            <h3 class="text-base sm:text-lg font-extrabold leading-snug text-white" x-text="filteredItems[currentIndex]?.title"></h3>
            <p class="text-xs text-slate-300 font-normal mt-1 leading-relaxed max-w-3xl" x-text="filteredItems[currentIndex]?.desc"></p>
        </div>

    </div>
</div>

</div>

@endsection
