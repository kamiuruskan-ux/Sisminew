@extends('layouts.landing')

@section('title', ($event->title ?? 'Detail Kegiatan') . ' - ' . Setting::get('school_name', 'SMA Nusantara'))

@section('content')

@php
    $pubDate = isset($event->published_at) && $event->published_at ? (is_string($event->published_at) ? \Carbon\Carbon::parse($event->published_at)->format('d M Y, H:i') : $event->published_at->format('d M Y, H:i')) . ' WIB' : date('d M Y, H:i') . ' WIB';
    $authorName = isset($event->author) ? (is_object($event->author) ? $event->author->name : 'Panitia Humas') : 'Panitia Humas';
    $eventType = $event->type ?? 'Kegiatan';
@endphp

<!-- ==========================================
     1. SUBPAGE HERO HEADER (Dark Navy SaaS Style)
     ========================================== -->
<section class="relative bg-slate-50 text-slate-800 pt-28 pb-16 lg:pt-36 lg:pb-20 overflow-hidden border-b border-slate-200/80">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[450px] bg-gradient-to-tr from-primary/10 via-secondary/10 to-purple-600/5 rounded-full blur-3xl pointer-events-none -z-0"></div>

    <div class="container-edunova relative z-10">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-4">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Beranda</a>
            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('events') }}" class="hover:text-primary transition-colors">Agenda & Kegiatan</a>
            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span class="text-primary font-bold truncate max-w-[200px] sm:max-w-xs">{{ $event->title }}</span>
        </div>

        <div class="max-w-4xl" data-aos="fade-up">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-3.5 py-1 rounded-full bg-primary/10 text-primary border border-primary/20 text-xs font-extrabold uppercase tracking-wider">
                    {{ $eventType }}
                </span>
                <span class="text-xs font-semibold text-slate-650 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $pubDate }}
                </span>
                <span class="text-xs font-semibold text-emerald-650 flex items-center gap-1 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Informasi Resmi
                </span>
            </div>

            <h1 class="text-2.5xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
                {{ $event->title }}
            </h1>

            <div class="flex items-center gap-4 text-xs text-slate-500 pt-2 border-t border-slate-200/85">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs border border-primary/20">
                        {{ strtoupper(substr($authorName, 0, 1)) }}
                    </div>
                    <span>Diterbitkan oleh <strong class="text-slate-800">{{ $authorName }}</strong></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     2. MAIN CONTENT & SIDEBAR SECTION
     ========================================== -->
<section class="py-16 lg:py-24 bg-gradient-to-b from-slate-50 via-white to-slate-50 min-h-screen">
    <div class="container-edunova">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Left Main Content (8 Cols) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Detail Card Container -->
                <div class="bg-white rounded-3xl p-7 sm:p-10 border border-slate-200/80 shadow-sm space-y-6">
                    
                    <!-- Highlight Quick Info Box -->
                    <div class="bg-gradient-to-r from-slate-900 to-navy-950 p-6 rounded-2xl text-white shadow-md border border-slate-800 space-y-3">
                        <h4 class="text-xs font-extrabold text-indigo-400 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            RINGKASAN JADWAL & LOKASI
                        </h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-indigo-300 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[11px] text-slate-400 block font-medium">Tanggal Pelaksanaan</span>
                                    <span class="text-xs sm:text-sm font-bold text-white">{{ $pubDate }}</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-indigo-300 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[11px] text-slate-400 block font-medium">Lokasi / Tempat</span>
                                    <span class="text-xs sm:text-sm font-bold text-white">Kampus {{ Setting::get('school_name', 'SMA Nusantara') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Article / Announcement Content -->
                    <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed text-sm sm:text-base font-normal space-y-4">
                        {!! $event->content !!}
                    </div>

                    <!-- Notice / Alert Box -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-900 text-xs sm:text-sm flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <span class="font-bold text-amber-800 block mb-0.5">Catatan Penting:</span>
                            <span>Harap memperhatikan waktu kehadiran serta petunjuk teknis yang telah ditentukan oleh panitia sekolah.</span>
                        </div>
                    </div>

                    <!-- Social Share Actions -->
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-500 mr-1">Bagikan:</span>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($event->title . ' - ' . request()->fullUrl()) }}" target="_blank" class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center hover:bg-emerald-600 transition-colors shadow-xs" title="Bagikan ke WhatsApp">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-colors shadow-xs" title="Bagikan ke Facebook">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <button onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan berhasil disalin!')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center hover:bg-slate-200 transition-colors shadow-xs" title="Salin Tautan">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Back Link Button -->
                <div>
                    <a href="{{ route('events') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-all shadow-md active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Kembali ke Daftar Agenda & Kegiatan</span>
                    </a>
                </div>

            </div>

            <!-- Right Sidebar (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Widget 1: Quick Status Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                        Informasi Publikasi
                    </h3>
                    
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Kategori Agenda</span>
                            <span class="font-bold text-slate-800">{{ $eventType }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Status</span>
                            <span class="font-bold text-emerald-600">Berlaku Aktif</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Penerbit</span>
                            <span class="font-bold text-slate-800">{{ $authorName }}</span>
                        </div>
                    </div>
                </div>

                <!-- Widget 2: Agenda & Kegiatan Terbaru -->
                @if(isset($recentEvents) && count($recentEvents) > 0)
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                        <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-900 pb-3 border-b border-slate-100">
                            Agenda & Pengumuman Lainnya
                        </h3>

                        <div class="space-y-4">
                            @foreach($recentEvents as $recent)
                                <a href="{{ route('events.show', $recent->id) }}" class="group block space-y-1">
                                    <span class="px-2 py-0.5 rounded-md bg-primary/10 text-primary text-[9px] font-extrabold uppercase">
                                        {{ $recent->type ?? 'Kegiatan' }}
                                    </span>
                                    <h4 class="text-xs font-bold text-slate-800 group-hover:text-primary transition-colors leading-snug line-clamp-2">
                                        {{ $recent->title }}
                                    </h4>
                                    <span class="text-[10px] text-slate-400 font-medium block">
                                        {{ $recent->published_at ? (is_string($recent->published_at) ? \Carbon\Carbon::parse($recent->published_at)->format('d M Y') : $recent->published_at->format('d M Y')) : date('d M Y') }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Widget 3: SPMB Banner Callout -->
                @if(Setting::get('spmb_enabled', '1') == '1')
                    <div class="bg-gradient-to-br from-blue-900 via-indigo-900 to-slate-950 text-white rounded-3xl p-6 border border-white/10 shadow-xl space-y-3 relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-primary/20 rounded-full blur-2xl pointer-events-none"></div>
                        <span class="px-2.5 py-0.5 rounded-full bg-white/15 text-blue-300 text-[10px] font-extrabold uppercase tracking-wider">Penerimaan Siswa</span>
                        <h3 class="text-base font-extrabold leading-snug">Tertarik Bergabung dengan {{ Setting::get('school_name', 'SMA Nusantara') }}?</h3>
                        <p class="text-xs text-slate-300 font-normal leading-relaxed">Pendaftaran peserta didik baru telah dibuka. Isi formulir pendaftaran secara online sekarang.</p>
                        <a href="{{ route('spmb.register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white text-blue-950 font-extrabold text-xs hover:bg-slate-100 transition-all shadow-md mt-1 uppercase tracking-wider">
                            <span>Daftar Sekarang</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                @endif

            </div>

        </div>
    </div>
</section>

@endsection
