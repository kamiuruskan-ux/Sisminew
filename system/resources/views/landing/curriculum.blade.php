@extends('layouts.landing')

@section('title', 'Kurikulum & Program Akademik - ' . Setting::get('school_name', 'SDIT AL-FAHMI PALU'))

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
                STANDAR AKADEMIK UNGGUL
            </div>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
                {{ $title ?? 'Kurikulum & Program Pendidikan' }}
            </h1>
            <p class="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed font-normal">
                {{ $description ?? Setting::get('curriculum_description', 'Mengintegrasikan Kurikulum Merdeka Belajar dengan penguatan karakter Pancasila dan literasi teknologi global.') }}
            </p>
        </div>
    </div>
</section>

<!-- ==========================================
     2. STRUKTUR KURIKULUM & FITUR UTAMA
     ========================================== -->
<section class="py-16 lg:py-24 bg-white">
    <div class="container-edunova">
        <div class="text-center max-w-xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 block mb-1">METODE PEMBELAJARAN</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900">Pilar Kurikulum Unggulan {{ Setting::get('school_short_name', 'Sekolah') }}</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($features as $index => $feature)
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-card transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 50 }}">
                    <div class="w-12 h-12 rounded-2xl bg-{{ $feature->color }}-50 text-{{ $feature->color }}-600 flex items-center justify-center mb-5 border border-{{ $feature->color }}-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $feature->title }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        {{ $feature->description }}
                    </p>
                </div>
            @empty
                <div class="col-span-4 text-center py-10 text-slate-400 text-sm">
                    Belum ada data pilar kurikulum yang ditambahkan.
                </div>
            @endforelse
        </div>
    </div>
</section>


<!-- ==========================================
     3. CTA CARD
     ========================================== -->
<section class="py-16 bg-slate-50/70 border-t border-slate-100">
    <div class="container-edunova">
        <div class="bg-[#0B132B] text-white rounded-3xl p-8 sm:p-12 shadow-2xl relative overflow-hidden border border-white/10 text-center max-w-4xl mx-auto" data-aos="zoom-in">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white mb-3">Siap Menjadi Bagian dari Pembelajar Masa Depan?</h2>
            <p class="text-slate-300 text-xs sm:text-sm max-w-xl mx-auto mb-6">
                Bergabunglah bersama {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }} dan rasakan pengalaman belajar yang menyenangkan, interaktif, dan penuh prestasi.
            </p>
            <a href="{{ route('spmb.register') }}" class="px-8 py-3.5 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider shadow-glow inline-flex items-center gap-2">
                Daftar Sekarang Online →
            </a>
        </div>
    </div>
</section>

@endsection
