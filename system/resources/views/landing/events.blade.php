@extends('layouts.landing')

@section('title', 'Agenda & Kegiatan Sekolah - ' . Setting::get('school_name', 'SDIT AL-FAHMI PALU'))

@section('content')

<!-- ==========================================
     1. SUBPAGE HERO HEADER (Dark Navy SaaS Style - CENTERED)
     ========================================== -->
<section class="relative bg-slate-50 text-slate-800 pt-28 pb-16 lg:pt-36 lg:pb-24 overflow-hidden border-b border-slate-200/80 text-center">
    <!-- Ambient Glow Background -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[450px] bg-gradient-to-tr from-primary/10 via-secondary/10 to-purple-600/5 rounded-full blur-3xl pointer-events-none -z-0"></div>

    <div class="container-edunova relative z-10">
        <!-- Centered Breadcrumbs -->
        <div class="flex items-center justify-center gap-2 text-xs font-semibold text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Beranda</a>
            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span class="text-primary font-bold">Agenda & Kegiatan</span>
        </div>

        <div class="max-w-3xl mx-auto" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-xs font-extrabold text-primary mb-5 shadow-inner">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                JADWAL & PENGUMUMAN RESMI
            </div>
            
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-5">
                Agenda & Kegiatan Sekolah
            </h1>
            
            <p class="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed font-normal max-w-2xl mx-auto">
                Kanal jadwal kegiatan akademik, pengumuman resmi, ujian semester, serta agenda kegiatan siswa di {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}.
            </p>
        </div>
    </div>
</section>

<!-- ==========================================
     2. STICKY FILTER & SEARCH BAR
     ========================================== -->
<div class="sticky top-16 z-30 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 py-3 shadow-xs">
    <div class="container-edunova flex flex-col md:flex-row gap-3 md:items-center justify-between">
        
        <!-- Search Input Form -->
        <form method="GET" action="{{ route('events') }}" class="w-full md:max-w-xs">
            <div class="relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="search" value="{{ request('search') }}" placeholder="Cari kegiatan atau pengumuman..." class="w-full rounded-full border border-slate-200 pl-10 pr-4 py-2 text-xs font-semibold outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all bg-slate-50" />
                @if(request('type'))<input type="hidden" name="type" value="{{ request('type') }}">@endif
            </div>
        </form>

        <!-- Swipeable Type Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-none py-1 max-w-full -mx-1 px-1">
            <a href="{{ route('events') }}{{ request('search') ? '?search='.request('search') : '' }}" 
               class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all {{ !request('type') ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Tipe
            </a>
            <a href="{{ route('events') }}?type=Kegiatan{{ request('search') ? '&search='.request('search') : '' }}" 
               class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all {{ request('type') == 'Kegiatan' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Kegiatan
            </a>
            <a href="{{ route('events') }}?type=Pengumuman{{ request('search') ? '&search='.request('search') : '' }}" 
               class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all {{ request('type') == 'Pengumuman' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Pengumuman
            </a>
            <a href="{{ route('events') }}?type=Agenda{{ request('search') ? '&search='.request('search') : '' }}" 
               class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all {{ request('type') == 'Agenda' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Agenda Ujian
            </a>
        </div>
    </div>
</div>

<!-- ==========================================
     3. EVENTS GRID SECTION (PROPORTIONAL CARDS)
     ========================================== -->
<section class="py-16 lg:py-24 bg-gradient-to-b from-slate-50 via-white to-slate-50 min-h-screen">
    <div class="container-edunova">

        <!-- Centered Section Sub-header -->
        <div class="text-center max-w-2xl mx-auto mb-14" data-aos="fade-up">
            <span class="text-xs font-extrabold uppercase tracking-widest text-primary block mb-2">
                PENGUMUMAN & KABAR TERKINI
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Jadwal & Agenda Sekolah
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm mt-3 leading-relaxed">
                Klik kartu agenda di bawah ini untuk membaca rincian lengkap mengenai jadwal, tanggal, dan informasi resmi.
            </p>
        </div>

        @if(isset($announcements) && $announcements->count() > 0)
            <!-- Proportional 3-Column Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-center items-stretch max-w-6xl mx-auto">
                @foreach($announcements as $ann)
                    <a href="{{ route('events.show', $ann->id) }}" 
                       class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-primary/40 transition-all duration-300 flex flex-col justify-between cursor-pointer group transform hover:-translate-y-2 relative overflow-hidden h-full" 
                       data-aos="fade-up" 
                       data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                        
                        <!-- Top Ambient Line -->
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-primary via-indigo-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="flex flex-col justify-between h-full">
                            <div>
                                <!-- Card Header: Badge & Published Date -->
                                <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                                    <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[11px] font-extrabold border border-primary/20">
                                        {{ $ann->type ?? 'Kegiatan' }}
                                    </span>
                                    <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ tanggal_indo($ann->published_at ?? now(), false, false, true) }}
                                    </span>
                                </div>

                                <!-- Card Title -->
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 mb-3 group-hover:text-primary transition-colors leading-snug line-clamp-2 min-h-[3rem]">
                                    {{ $ann->title }}
                                </h3>

                                <!-- Card Excerpt -->
                                <p class="text-xs text-slate-500 leading-relaxed mb-6 line-clamp-3 min-h-[4rem]">
                                    {{ strip_tags($ann->content) }}
                                </p>
                            </div>

                            <!-- Card Footer Action -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs mt-auto">
                                <span class="font-extrabold text-primary inline-flex items-center gap-1.5 group-hover:translate-x-1 transition-transform">
                                    <span>Lihat Detail Agenda</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-14 flex justify-center">
                {{ $announcements->links() }}
            </div>
        @else
            <!-- Fallback Events Proportional Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-center items-stretch max-w-6xl mx-auto">
                <!-- Card 1 -->
                <a href="{{ route('events.show', 1) }}" 
                   class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-primary/40 transition-all duration-300 flex flex-col justify-between cursor-pointer group transform hover:-translate-y-2 relative overflow-hidden h-full" 
                   data-aos="fade-up" 
                   data-aos-delay="50">
                    
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-primary via-indigo-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                                <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[11px] font-extrabold border border-primary/20">
                                    Agenda Sekolah
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    17 Mei 2024
                                </span>
                            </div>

                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 mb-3 group-hover:text-primary transition-colors leading-snug line-clamp-2 min-h-[3rem]">
                                Upacara Peringatan & Gelar Seni Budaya 2024
                            </h3>

                            <p class="text-xs text-slate-500 leading-relaxed mb-6 line-clamp-3 min-h-[4rem]">
                                Seluruh siswa-siswi diharapkan hadir dengan seragam lengkap untuk mengikuti upacara dan menyaksikan gelar pentas seni tahunan.
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs mt-auto">
                            <span class="font-extrabold text-primary inline-flex items-center gap-1.5 group-hover:translate-x-1 transition-transform">
                                <span>Lihat Detail Agenda</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </div>
                    </div>
                </a>

                <!-- Card 2 -->
                <a href="{{ route('events.show', 2) }}" 
                   class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-amber-500/40 transition-all duration-300 flex flex-col justify-between cursor-pointer group transform hover:-translate-y-2 relative overflow-hidden h-full" 
                   data-aos="fade-up" 
                   data-aos-delay="100">
                    
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-500 to-orange-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                                <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 text-[11px] font-extrabold border border-amber-500/20">
                                    Pengumuman Ujian
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    22 Mei 2024
                                </span>
                            </div>

                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 mb-3 group-hover:text-amber-600 transition-colors leading-snug line-clamp-2 min-h-[3rem]">
                                Jadwal Penilaian Akhir Semester (PAS) Genap
                            </h3>

                            <p class="text-xs text-slate-500 leading-relaxed mb-6 line-clamp-3 min-h-[4rem]">
                                Informasi pelaksanaan Penilaian Akhir Semester Genap Tahun Ajaran 2023/2024 bagi siswa kelas X, XI, dan XII.
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs mt-auto">
                            <span class="font-extrabold text-amber-600 inline-flex items-center gap-1.5 group-hover:translate-x-1 transition-transform">
                                <span>Lihat Detail Agenda</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </div>
                    </div>
                </a>

                <!-- Card 3 -->
                <a href="{{ route('events.show', 3) }}" 
                   class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between cursor-pointer group transform hover:-translate-y-2 relative overflow-hidden h-full" 
                   data-aos="fade-up" 
                   data-aos-delay="150">
                    
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 text-[11px] font-extrabold border border-emerald-500/20">
                                    Pertemuan
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    28 Mei 2024
                                </span>
                            </div>

                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900 mb-3 group-hover:text-emerald-600 transition-colors leading-snug line-clamp-2 min-h-[3rem]">
                                Rapat Koordinasi Orang Tua & Komite Sekolah
                            </h3>

                            <p class="text-xs text-slate-500 leading-relaxed mb-6 line-clamp-3 min-h-[4rem]">
                                Undangan rapat persiapan kelulusan siswa kelas XII dan sosialisasi program kerja komite sekolah tahun depan.
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs mt-auto">
                            <span class="font-extrabold text-emerald-600 inline-flex items-center gap-1.5 group-hover:translate-x-1 transition-transform">
                                <span>Lihat Detail Agenda</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @endif
    </div>
</section>

<!-- ==========================================
     4. BOTTOM CTA BANNER (CENTERED)
     ========================================== -->
@if(Setting::get('spmb_enabled', '1') == '1')
<section class="py-16 bg-white border-t border-slate-100">
    <div class="container-edunova">
        <div class="bg-gradient-to-r from-blue-950 via-indigo-900 to-slate-950 rounded-3xl p-8 sm:p-12 text-white shadow-2xl text-center relative overflow-hidden border border-white/10 max-w-5xl mx-auto" data-aos="zoom-in">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl mx-auto space-y-4">
                <div class="inline-block px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-extrabold uppercase tracking-widest text-blue-300 border border-white/15">
                    INFORMASI & PENDAFTARAN
                </div>
                
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight">
                    Ikuti Setiap Agenda & Kegiatan Sekolah
                </h2>
                
                <p class="text-slate-300 text-xs sm:text-sm font-normal leading-relaxed">
                    Daftarkan diri Anda di {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }} dan jadilah bagian dari berbagai kegiatan pembelajaran yang inspiratif.
                </p>

                <div class="pt-3 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('spmb.register') }}" class="px-8 py-3.5 rounded-full bg-white hover:bg-slate-100 text-blue-950 font-extrabold text-xs sm:text-sm shadow-xl transition-all inline-flex items-center gap-2 uppercase tracking-wider transform hover:-translate-y-0.5">
                        <span>Daftar Sekolah Sekarang</span>
                        <svg class="w-4 h-4 text-blue-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@endsection
