@extends('layouts.landing')

@section('title', 'Ekstrakurikuler & Pengembangan Diri - ' . Setting::get('school_name', 'SMA Nusantara'))

@section('content')

<div x-data="{
    activeTab: 'all',
    search: '',
    matchesTab(category) {
        if (this.activeTab === 'all') return true;
        return this.activeTab === category;
    }
}">

<!-- ==========================================
     1. SUBPAGE HERO HEADER (Dark Navy SaaS Style - CENTERED)
     ========================================== -->
<section class="relative bg-slate-50 text-slate-800 pt-28 pb-16 lg:pt-36 lg:pb-24 overflow-hidden border-b border-slate-200/80 text-center">
    <!-- Ambient Glow Background -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[450px] bg-gradient-to-tr from-primary/10 via-secondary/10 to-purple-600/5 rounded-full blur-3xl pointer-events-none -z-0"></div>

    <div class="container-edunova relative z-10">
        <!-- Centered Breadcrumb -->
        <div class="flex items-center justify-center gap-2 text-xs font-semibold text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Beranda</a>
            <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span class="text-primary font-bold">Ekstrakurikuler</span>
        </div>

        <div class="max-w-3xl mx-auto" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-xs font-extrabold text-primary mb-5 shadow-inner">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                PENGEMBANGAN BAKAT & KARAKTER SISWA
            </div>
            
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-5">
                Ekstrakurikuler Unggulan
            </h1>
            
            <p class="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed font-normal max-w-2xl mx-auto">
                Wadah pembelajaran non-akademik bagi siswa {{ Setting::get('school_name', 'SMA Nusantara') }} untuk mengeksplorasi minat, mengasah kepemimpinan, sains, seni, dan olahraga.
            </p>
        </div>
    </div>
</section>

<!-- ==========================================
     2. FILTER BAR (STICKY & CENTERED)
     ========================================== -->
<div class="sticky top-16 z-30 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 py-3 shadow-xs">
    <div class="container-edunova flex items-center justify-center">
        <div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-none py-1 max-w-full -mx-1 px-1 sm:justify-center">
            <button @click="activeTab = 'all'" 
                    :class="activeTab === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all">
                Semua Ekstra
            </button>
            <button @click="activeTab = 'kepemimpinan'" 
                    :class="activeTab === 'kepemimpinan' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all">
                Kepemimpinan & Organisasi
            </button>
            <button @click="activeTab = 'sains'" 
                    :class="activeTab === 'sains' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all">
                Sains & Teknologi
            </button>
            <button @click="activeTab = 'olahraga'" 
                    :class="activeTab === 'olahraga' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all">
                Olahraga & Atletik
            </button>
            <button @click="activeTab = 'seni'" 
                    :class="activeTab === 'seni' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                    class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold transition-all">
                Seni & Budaya
            </button>
        </div>
    </div>
</div>

<!-- ==========================================
     3. EXTRACURRICULAR CARDS (CENTERED GRID)
     ========================================== -->
<section class="py-16 lg:py-24 bg-gradient-to-b from-slate-50 via-white to-slate-50 min-h-screen">
    <div class="container-edunova">
        
        <!-- Centered Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-14" data-aos="fade-up">
            <span class="text-xs font-extrabold uppercase tracking-widest text-primary block mb-2">
                DAFTAR KEGIATAN
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Ragam Pilihan Kegiatan Siswa
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm mt-3 leading-relaxed">
                Setiap kegiatan didampingi oleh pembina profesional serta pelatih berpengalaman di bidangnya.
            </p>
        </div>

        <!-- Dynamic 4-Column Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 justify-center items-stretch max-w-7xl mx-auto">
            
            @forelse($extracurriculars as $index => $extra)
                @php
                    $categoryColors = [
                        'kepemimpinan' => 'amber',
                        'sains' => 'blue',
                        'olahraga' => 'indigo',
                        'seni' => 'purple',
                    ];
                    $color = $categoryColors[$extra->category] ?? 'primary';
                    $categoryLabels = [
                        'kepemimpinan' => 'Kepemimpinan',
                        'sains' => 'Sains & Teknologi',
                        'olahraga' => 'Olahraga',
                        'seni' => 'Seni & Budaya',
                    ];
                    $label = $categoryLabels[$extra->category] ?? ucfirst($extra->category);
                @endphp
                <div x-show="matchesTab('{{ $extra->category }}')"
                     class="bg-white rounded-3xl p-7 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-{{ $color }}-500/40 transition-all duration-300 flex flex-col justify-between items-center text-center group transform hover:-translate-y-2 relative overflow-hidden" 
                     data-aos="fade-up" 
                     data-aos-delay="{{ ($index + 1) * 50 }}">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-{{ $color }}-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-full flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center mb-5 shadow-md border border-{{ $color }}-100 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-{{ $color }}-50 text-{{ $color }}-600 text-[10px] font-extrabold uppercase tracking-wider mb-2 border border-{{ $color }}-100">
                            {{ $label }}
                        </span>
                        <h3 class="text-lg font-extrabold text-slate-900 mb-2 group-hover:text-{{ $color }}-600 transition-colors">{{ $extra->name }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-6">
                            {{ $extra->description }}
                        </p>
                    </div>
                    @if($extra->schedule)
                    <div class="w-full pt-4 border-t border-slate-100 text-[11px] font-bold text-slate-400 flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5 text-{{ $color }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Jadwal: {{ $extra->schedule }}</span>
                    </div>
                    @endif
                </div>
            @empty
                <div class="col-span-4 text-center py-12 text-slate-400 text-sm">
                    Belum ada data kegiatan ekstrakurikuler yang ditambahkan.
                </div>
            @endforelse

        </div>


    </div>
</section>

<!-- ==========================================
     4. SPMB BANNER CTA (CENTERED BOTTOM)
     ========================================== -->
@if(Setting::get('spmb_enabled', '1') == '1')
<section class="py-16 bg-white border-t border-slate-100">
    <div class="container-edunova">
        <div class="bg-gradient-to-r from-blue-950 via-indigo-900 to-slate-950 rounded-3xl p-8 sm:p-12 text-white shadow-2xl text-center relative overflow-hidden border border-white/10 max-w-5xl mx-auto" data-aos="zoom-in">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl mx-auto space-y-4">
                <div class="inline-block px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-extrabold uppercase tracking-widest text-blue-300 border border-white/15">
                    PENDAFTARAN SISWA BARU
                </div>
                
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight">
                    Kembangkan Bakat Terbaik Anda Bersama Kami
                </h2>
                
                <p class="text-slate-300 text-xs sm:text-sm font-normal leading-relaxed">
                    Daftarkan diri Anda sekarang di {{ Setting::get('school_name', 'SMA Nusantara') }} dan nikmati berbagai fasilitas kegiatan ekstrakurikuler terlengkap.
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

</div>

@endsection
