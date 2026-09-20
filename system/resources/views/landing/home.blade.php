@section('title', Setting::get('school_name', 'SDIT AL-FAHMI PALU') . ' - ' . Setting::get('school_tagline', 'Sekolahnya Calon Pemimpin Peradaban'))

@section('content')

<div x-data="{ 
    videoModalOpen: false,
    activeSlide: 0,
    totalSlides: {{ max(1, count($sliders ?? [])) }},
    timer: null,
    heroTouchStartX: 0,
    init() {
        if (this.totalSlides > 1) {
            this.timer = setInterval(() => {
                this.nextSlide();
            }, 5000);
        }
    },
    nextSlide() {
        this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
    },
    prevSlide() {
        this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
    },
    goToSlide(index) {
        this.activeSlide = index;
    }
}">

<!-- ==========================================
     1. HERO SECTION (Full-Bleed Dynamic Background Slider)
     ========================================== -->
<section class="relative bg-slate-900 text-white pt-24 pb-16 sm:pt-28 sm:pb-20 lg:pt-32 lg:pb-24 overflow-hidden border-b border-slate-800">

    <!-- Ambient Mesh Glow Background -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] sm:w-[750px] h-[400px] sm:h-[500px] bg-gradient-to-tr from-primary/15 via-secondary/15 to-purple-600/10 rounded-full blur-3xl pointer-events-none -z-0"></div>

    @if(isset($sliders) && count($sliders) > 0)
        <!-- Full-Bleed Section Background Photo Slider -->
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            @foreach($sliders as $index => $slider)
                <div x-show="activeSlide === {{ $index }}"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 scale-105"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-105"
                     class="absolute inset-0 w-full h-full">
                    <img src="{{ $slider->image_url }}" 
                         alt="{{ $slider->title }}" 
                         class="w-full h-full object-cover object-center opacity-40">
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/70 to-slate-950/30"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-slate-950/40"></div>
                </div>
            @endforeach
        </div>

        <!-- DYNAMIC CONTENT SLIDER -->
        <div class="grid grid-cols-1 grid-rows-1 relative w-full z-10"
             @touchstart="heroTouchStartX = $event.touches[0].clientX"
             @touchend="if (heroTouchStartX - $event.changedTouches[0].clientX > 50) { nextSlide() } else if ($event.changedTouches[0].clientX - heroTouchStartX > 50) { prevSlide() }">
            @foreach($sliders as $index => $slider)
                <div x-show="activeSlide === {{ $index }}"
                     x-transition:enter="transition ease-out duration-500 transform"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300 transform"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="col-start-1 row-start-1 relative z-10 w-full">

                    <div class="container-edunova relative z-10">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 lg:gap-12 items-center">
                            
                            <!-- Left Content Column -->
                            <div class="lg:col-span-7 space-y-4 sm:space-y-6 pt-1 sm:pt-2" data-aos="fade-right">
                                
                                <!-- Tagline Badge -->
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/20 border border-primary/30 backdrop-blur-md">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_8px_2px_rgba(52,211,153,0.6)]"></span>
                                    <span class="text-[9px] sm:text-xs font-extrabold uppercase tracking-widest text-indigo-300">
                                        SELAMAT DATANG DI {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}
                                    </span>
                                </div>

                                <!-- Main Heading -->
                                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight sm:leading-[1.15]">
                                    {{ $slider->title }}
                                </h1>

                                <!-- Subtitle / Description -->
                                <p class="text-slate-200 text-xs sm:text-base lg:text-lg leading-relaxed font-normal max-w-2xl line-clamp-3">
                                    {{ $slider->description }}
                                </p>

                                <!-- Call to Action Buttons -->
                                <div class="flex flex-wrap items-center gap-3 sm:gap-4 pt-2">
                                    @if($slider->link)
                                        <a href="{{ $slider->link }}" class="px-6 sm:px-7 py-3 sm:py-3.5 rounded-full bg-gradient-to-r from-primary to-indigo-600 hover:from-primary/90 hover:to-indigo-700 text-white font-extrabold text-xs sm:text-sm shadow-xl shadow-indigo-600/25 transition-all inline-flex items-center gap-2 transform hover:-translate-y-0.5 border border-indigo-400/30">
                                            <span>{{ $slider->link_text ?: 'Jelajahi Sekarang' }}</span>
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    @endif
                                    
                                    @if(Setting::get('spmb_enabled', '1') == '1')
                                        <a href="{{ route('spmb.register') }}" class="px-6 sm:px-7 py-3 sm:py-3.5 rounded-full bg-white/15 hover:bg-white/25 text-white font-extrabold text-xs sm:text-sm backdrop-blur-md border border-white/25 transition-all inline-flex items-center gap-2 transform hover:-translate-y-0.5 shadow-lg">
                                            <span>Daftar SPMB</span>
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        </a>
                                    @endif

                                    <button type="button" @click="videoModalOpen = true" class="px-5 sm:px-6 py-3 sm:py-3.5 rounded-full text-slate-200 hover:text-white font-bold text-xs sm:text-sm inline-flex items-center gap-2 transition-colors hover:bg-white/10">
                                        <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center text-white border border-white/30 shrink-0">
                                            <svg class="w-3.5 h-3.5 fill-current ml-0.5" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                        <span>Video Profil</span>
                                    </button>
                                </div>

                            </div>

                            <!-- Right Image Card Column -->
                            <div class="lg:col-span-5 relative mt-4 lg:mt-0" data-aos="fade-left">
                                <div class="absolute -inset-4 bg-gradient-to-tr from-primary/20 via-secondary/15 to-purple-600/10 rounded-3xl blur-2xl -z-10"></div>
                                <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden border border-white/20 shadow-2xl bg-slate-900 group">
                                    <img src="{{ $slider->image_url }}" 
                                         alt="{{ $slider->title }}" 
                                         class="w-full h-[240px] sm:h-[320px] lg:h-[380px] object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                    
                                    <!-- Floating Accreditation Badge -->
                                    @if(Setting::get('school_accreditation_show', '1') == '1')
                                    <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 bg-slate-950/85 backdrop-blur-md p-3 rounded-xl sm:rounded-2xl border border-white/15 shadow-xl flex items-center gap-3">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-gradient-to-br from-primary to-secondary text-white flex items-center justify-center font-extrabold text-sm sm:text-base shrink-0 shadow-md border border-white/20">
                                            {{ Setting::get('school_accreditation_grade', 'A') }}
                                        </div>
                                        <div>
                                            <h4 class="text-[11px] sm:text-xs font-extrabold uppercase tracking-wide text-white leading-tight">{{ Setting::get('school_accreditation_label', 'AKREDITASI UNGGUL (A)') }}</h4>
                                            <p class="text-[10px] sm:text-[11px] text-slate-300 font-medium">{{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Sleek Unified Glassmorphism Slider Controls -->
            <div class="relative z-20 mt-6 sm:mt-8 flex justify-center">
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white/95 border border-slate-200/80 backdrop-blur-xl shadow-xl">
                    <!-- Prev Arrow -->
                    <button @click="prevSlide()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all active:scale-95 shrink-0" title="Slide Sebelumnya">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <!-- Dots Indicator -->
                    <div class="flex items-center gap-1.5 px-1.5">
                        @foreach($sliders as $idx => $s)
                            <button @click="goToSlide({{ $idx }})" 
                                    class="h-1.5 rounded-full transition-all duration-300"
                                    :class="activeSlide === {{ $idx }} ? 'w-6 bg-primary' : 'w-1.5 bg-slate-300 hover:bg-slate-400'"
                                    title="Slide {{ $idx + 1 }}">
                            </button>
                        @endforeach
                    </div>

                    <!-- Slide Counter -->
                    <div class="text-[11px] font-extrabold text-slate-800 tracking-wider px-1 shrink-0 flex items-center">
                        <span class="text-primary" x-text="'0' + (activeSlide + 1)"></span><span class="text-slate-400 mx-0.5">/</span><span class="text-slate-500" x-text="'0' + totalSlides"></span>
                    </div>

                    <!-- Next Arrow -->
                    <button @click="nextSlide()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all active:scale-95 shrink-0" title="Slide Selanjutnya">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

    @else
        <!-- DEFAULT HERO (Full-Bleed Background) -->
        @php
            $defaultHeroImg = Setting::get('school_hero_image') ? asset(Setting::get('school_hero_image')) : (Setting::get('logo_path') ? asset(Setting::get('logo_path')) : asset('img/logo.png'));
        @endphp
        <div class="absolute inset-0 z-0 pointer-events-none">
            <img src="{{ $defaultHeroImg }}" 
                 alt="Gedung {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}" 
                 class="w-full h-full object-cover object-center opacity-40">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900/95 via-slate-900/75 to-slate-900/30"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-slate-900/40"></div>
        </div>

        <div class="container-edunova relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-10 lg:gap-12 items-center">
                
                <!-- Left Hero Text Column (lg:col-span-7) -->
                <div class="lg:col-span-7 space-y-4 sm:space-y-6 pt-1 sm:pt-2" data-aos="fade-right">
                    
                    <!-- Tagline Badge -->
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3.5 sm:py-1 rounded-full bg-primary/10 border border-primary/20 backdrop-blur-md">
                        <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-green-400 animate-pulse shadow-[0_0_8px_2px_rgba(74,222,128,0.6)]"></span>
                        <span class="text-[8px] sm:text-xs font-bold uppercase tracking-widest text-primary">
                            SELAMAT DATANG DI {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}
                        </span>
                    </div>
                    
                    <!-- Main Headline -->
                    <h1 class="text-2xl sm:text-4xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight sm:leading-[1.15]">
                        Mencetak Generasi <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-indigo-400 to-secondary">
                            Cerdas, Berkarakter,
                        </span> <br/>
                        dan Siap Menghadapi Masa Depan
                    </h1>
                    
                    <!-- Subparagraph -->
                    <p class="text-slate-200 text-xs sm:text-base lg:text-lg max-w-xl leading-relaxed font-normal">
                        Kami berkomitmen memberikan pendidikan terbaik untuk membentuk generasi yang unggul, berakhlak mulia, dan siap bersaing di dunia global.
                    </p>
                    
                    <!-- Action Buttons -->
                    <div class="pt-1 flex flex-row items-center gap-2 sm:gap-4">
                        <a href="{{ route('about') }}" class="flex-1 sm:flex-initial sm:w-auto px-3 sm:px-7 py-3 rounded-full bg-gradient-to-r from-primary via-secondary to-primary hover:brightness-110 text-white font-extrabold text-[10px] sm:text-xs shadow-lg shadow-primary/30 border border-white/20 transition-all flex items-center justify-center gap-1.5 sm:gap-2 transform hover:-translate-y-0.5 uppercase tracking-wider">
                            <span>Kenali Sekolah</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                        
                        <button @click="videoModalOpen = true" class="flex-1 sm:flex-initial sm:w-auto px-3 sm:px-6 py-3 rounded-full bg-white/10 hover:bg-white/20 text-white border border-white/30 backdrop-blur-sm transition-all flex items-center justify-center gap-2 sm:gap-3 transform hover:-translate-y-0.5 text-[10px] sm:text-xs font-bold">
                            <div class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center shrink-0 shadow-md">
                                <svg class="w-2.5 h-2.5 fill-current ml-0.5" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                            <span>Lihat Video Profil</span>
                        </button>
                    </div>

                </div>

                <!-- Right Hero Image Column -->
                <div class="lg:col-span-5 relative mt-2 lg:mt-0" data-aos="fade-left">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-primary/10 via-secondary/10 to-purple-600/5 rounded-3xl blur-2xl -z-10"></div>
                    
                    <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden border-2 border-slate-200/80 shadow-2xl bg-white group">
                        <img src="{{ $defaultHeroImg }}" 
                             alt="Siswa {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}" 
                             class="w-full h-[220px] sm:h-[340px] lg:h-[420px] object-cover object-top group-hover:scale-105 transition-transform duration-700">
                        
                        <!-- Floating Accreditation Badge -->
                        @if(Setting::get('school_accreditation_show', '1') == '1')
                        <div class="absolute bottom-3 left-3 right-3 sm:bottom-5 sm:left-5 sm:right-5 bg-white/90 backdrop-blur-md p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200/80 shadow-xl flex items-center gap-2.5 sm:gap-3">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-gradient-to-br from-primary to-secondary text-white flex items-center justify-center font-extrabold text-sm sm:text-lg shrink-0 shadow-md border border-white/20">
                                {{ Setting::get('school_accreditation_grade', 'A') }}
                            </div>
                            <div>
                                <h4 class="text-[11px] sm:text-xs font-extrabold uppercase tracking-wide text-slate-900 leading-tight">{{ Setting::get('school_accreditation_label', 'AKREDITASI UNGGUL (A)') }}</h4>
                                <p class="text-[10px] sm:text-[11px] text-slate-500 font-medium">{{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>
    @endif

</section>

<!-- ==========================================
     2. TOP FEATURES BAR (Floating White Cards overlapping Hero bottom - Clean SaaS Style)
     ========================================== -->
<section class="relative z-30 -mt-14 sm:-mt-16 mb-16">
    <div class="container-edunova">
        <div class="bg-white rounded-3xl shadow-[0_15px_40px_-10px_rgba(14,30,75,0.1)] border border-slate-100 p-6 sm:p-8" data-aos="zoom-in" data-aos-duration="600">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-4 divide-y-0 lg:divide-x divide-slate-100">
                
                <!-- Feature 1: Akademik Unggul -->
                <div class="lg:px-4 flex flex-col items-center text-center first:lg:pl-0">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-blue-50/80 text-blue-600 flex items-center justify-center mb-3 shadow-sm border border-blue-100/80">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <h3 class="text-xs sm:text-base font-extrabold text-slate-900 mb-1 leading-snug">{{ Setting::get('school_feature_title_1', 'Akademik Unggul') }}</h3>
                    <p class="text-[11px] sm:text-xs text-slate-500 leading-relaxed font-medium max-w-[220px]">
                        {{ Setting::get('school_feature_desc_1', 'Kurikulum berkualitas untuk hasil belajar maksimal.') }}
                    </p>
                </div>

                <!-- Feature 2: Fasilitas Modern -->
                <div class="lg:px-4 flex flex-col items-center text-center">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-purple-50/80 text-purple-600 flex items-center justify-center mb-3 shadow-sm border border-purple-100/80">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xs sm:text-base font-extrabold text-slate-900 mb-1 leading-snug">{{ Setting::get('school_feature_title_2', 'Fasilitas Modern') }}</h3>
                    <p class="text-[11px] sm:text-xs text-slate-500 leading-relaxed font-medium max-w-[220px]">
                        {{ Setting::get('school_feature_desc_2', 'Sarana lengkap dan teknologi pendukung pembelajaran.') }}
                    </p>
                </div>

                <!-- Feature 3: Pembinaan Karakter -->
                <div class="lg:px-4 flex flex-col items-center text-center">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-emerald-50/80 text-emerald-600 flex items-center justify-center mb-3 shadow-sm border border-emerald-100/80">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xs sm:text-base font-extrabold text-slate-900 mb-1 leading-snug">{{ Setting::get('school_feature_title_3', 'Pembinaan Karakter') }}</h3>
                    <p class="text-[11px] sm:text-xs text-slate-500 leading-relaxed font-medium max-w-[220px]">
                        {{ Setting::get('school_feature_desc_3', 'Membentuk kepribadian unggul dan berakhlak mulia.') }}
                    </p>
                </div>

                <!-- Feature 4: Prestasi Membanggakan -->
                <div class="lg:px-4 flex flex-col items-center text-center last:lg:pr-0">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-amber-50/80 text-amber-600 flex items-center justify-center mb-3 shadow-sm border border-amber-100/80">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xs sm:text-base font-extrabold text-slate-900 mb-1 leading-snug">{{ Setting::get('school_feature_title_4', 'Prestasi Membanggakan') }}</h3>
                    <p class="text-[11px] sm:text-xs text-slate-500 leading-relaxed font-medium max-w-[220px]">
                        {{ Setting::get('school_feature_desc_4', 'Beragam prestasi di tingkat nasional & internasional.') }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     3. STATS SECTION (BUKTI KOMITMEN PENDIDIKAN DALAM ANGKA)
     ========================================== -->
<section class="py-6">
    <div class="container-edunova">
        <div class="bg-white text-slate-850 rounded-3xl p-8 sm:p-12 shadow-[0_15px_50px_-15px_rgba(14,30,75,0.08)] relative overflow-hidden border border-slate-200/80" data-aos="fade-up">
            <!-- Background Glow -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                
                <!-- Left Title Column -->
                <div class="lg:col-span-5 space-y-3">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-primary block">
                        {{ Setting::get('school_stats_kicker', strtoupper(Setting::get('school_name', 'SDIT AL-FAHMI PALU')) . ' DALAM ANGKA') }}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                        {{ Setting::get('school_stats_title', 'Bukti Komitmen Kami dalam Pendidikan') }}
                    </h2>
                    <div class="w-16 h-1 bg-primary rounded-full"></div>
                </div>

                <!-- Right Stats Counter Grid -->
                <div class="lg:col-span-7">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-center divide-x divide-slate-100">
                        
                        <!-- Stat 1: Siswa Aktif -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-3 border border-primary/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                {{ number_format((int)($stats['students'] ?? 0), 0, ',', '.') }}+
                            </span>
                            <span class="text-xs font-medium text-slate-500 mt-1">Siswa Aktif</span>
                        </div>

                        <!-- Stat 2: Guru Profesional -->
                        <div class="flex flex-col items-center pl-4">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-3 border border-primary/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                {{ number_format((int)($stats['teachers'] ?? 0), 0, ',', '.') }}+
                            </span>
                            <span class="text-xs font-medium text-slate-500 mt-1">Guru Profesional</span>
                        </div>

                        <!-- Stat 3: Prestasi Diraih -->
                        <div class="flex flex-col items-center pl-4">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-3 border border-primary/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            </div>
                            <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                {{ Setting::get('stats_achievements', (isset($stats['achievements']) ? $stats['achievements'] . '+' : '50+')) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500 mt-1">Prestasi Diraih</span>
                        </div>

                        <!-- Stat 4: Tahun Terpercaya -->
                        <div class="flex flex-col items-center pl-4">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-3 border border-primary/20">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                {{ Setting::get('stats_years', (isset($stats['years']) ? $stats['years'] . '+' : '15+')) }}
                            </span>
                            <span class="text-xs font-medium text-slate-500 mt-1">Tahun Terpercaya</span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     4. SAMBUTAN & KEUNGGULAN UTAMA
     ========================================== -->
<section class="py-16 bg-slate-50/50">
    <div class="container-edunova">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            
            <!-- Left Image Card -->
            @php
                $hasHeroImage = !empty(Setting::get('school_hero_image'));
                $sambutanImg = $hasHeroImage 
                    ? asset(Setting::get('school_hero_image')) 
                    : (Setting::get('logo_path') ? asset(Setting::get('logo_path')) : asset('img/logo.png'));
            @endphp
            <div class="lg:col-span-5 relative" data-aos="fade-right">
                <div class="rounded-3xl overflow-hidden shadow-card border border-slate-200/80 bg-gradient-to-br from-indigo-50/70 via-white to-slate-100/70 group relative min-h-[300px] sm:min-h-[360px] flex items-center justify-center p-4">
                    @if($hasHeroImage)
                        <img src="{{ $sambutanImg }}" 
                             alt="Gedung Utama {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}" 
                             class="w-full h-[320px] sm:h-[380px] object-cover rounded-2xl group-hover:scale-105 transition-transform duration-700">
                    @else
                        <div class="flex flex-col items-center justify-center text-center space-y-3 py-6 px-4">
                            <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl bg-white p-4 shadow-lg border border-slate-200/80 flex items-center justify-center group-hover:scale-105 transition-transform duration-500">
                                <img src="{{ $sambutanImg }}" 
                                     alt="Logo {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}" 
                                     class="max-w-full max-h-full object-contain">
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">
                                    {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}
                                </h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-1">
                                    {{ Setting::get('school_tagline', 'Sekolahnya Calon Pemimpin Peradaban') }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="absolute -bottom-4 -right-4 sm:-bottom-5 sm:-right-5 bg-blue-600 text-white rounded-2xl p-3.5 sm:p-4 shadow-xl border border-blue-400 hidden sm:flex items-center gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/20 flex items-center justify-center font-bold text-lg shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-[11px] sm:text-xs font-extrabold uppercase">Sekolah Penggerak</h4>
                        <p class="text-[10px] sm:text-[11px] text-blue-100 font-medium">Pendidikan Berkarakter Pancasila</p>
                    </div>
                </div>
            </div>

            <!-- Right Vision & Mission Tabs / Cards (Centered) -->
            <div class="lg:col-span-7 space-y-6 flex flex-col items-center text-center" data-aos="fade-left">
                <span class="text-xs font-extrabold uppercase tracking-widest text-primary block">
                    TENTANG {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight max-w-xl">
                    Membimbing Potensi Siswa Menuju Keunggulan Global
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                    <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-card transition-all flex flex-col items-center text-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary flex items-center justify-center mb-3 shadow-xs">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1.5">Visi Utama</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Menjadi lembaga pendidikan terdepan yang menghasilkan lulusan berintegritas tinggi, inovatif, dan berdaya saing global.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-card transition-all flex flex-col items-center text-center">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-secondary flex items-center justify-center mb-3 shadow-xs">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1.5">Misi Pendidikan</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Menyelenggarakan pembelajaran berbasis teknologi modern serta penguatan nilai-nilai budi pekerti luhur.
                        </p>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('about') }}" class="inline-flex items-center text-xs font-bold text-primary hover:text-secondary gap-1.5 transition-colors uppercase tracking-wider">
                        <span>Pelajari Profil Selengkapnya</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     5. SEKSI KEGIATAN & PENGUMUMAN SEKOLAH (Announcements & School Events)
     ========================================== -->
<section id="kegiatan" class="py-16 bg-slate-50/80 border-t border-slate-100">
    <div class="container-edunova">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10" data-aos="fade-up">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-indigo-600 block mb-1">
                    AGENDA & KEGIATAN
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Kegiatan & Pengumuman Sekolah
                </h2>
            </div>
            <a href="{{ route('events') }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors gap-1 group uppercase tracking-wider">
                Lihat Agenda Lengkap
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-center items-stretch max-w-6xl mx-auto">
            @if(isset($announcements) && count($announcements) > 0)
                @foreach($announcements->take(3) as $ann)
                    <a href="{{ route('events.show', $ann->id) }}" class="bg-white rounded-3xl p-7 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between cursor-pointer group transform hover:-translate-y-2 relative overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-indigo-600 via-primary to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="flex flex-col justify-between h-full">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                                    <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-[11px] font-extrabold border border-indigo-100">
                                        {{ $ann->type ?? 'Kegiatan' }}
                                    </span>
                                    <span class="text-[11px] font-semibold text-slate-400">
                                        {{ $ann->published_at ? $ann->published_at->format('d M Y') : date('d M Y') }}
                                    </span>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-900 mb-2 leading-snug group-hover:text-indigo-600 transition-colors line-clamp-2 min-h-[3rem]">
                                    {{ $ann->title }}
                                </h3>
                                <p class="text-xs text-slate-500 leading-relaxed mb-6 line-clamp-3 min-h-[4rem]">
                                    {{ strip_tags($ann->content) }}
                                </p>
                            </div>
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs mt-auto">
                                <span class="font-extrabold text-indigo-600 inline-flex items-center gap-1.5 group-hover:translate-x-1 transition-transform">
                                    <span>Lihat Detail Agenda</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <!-- Fallback Activity Card 1 -->
                <a href="{{ route('events.show', 1) }}" class="bg-white rounded-3xl p-7 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-indigo-500/40 transition-all duration-300 flex flex-col justify-between cursor-pointer group transform hover:-translate-y-2 relative overflow-hidden" data-aos="fade-up" data-aos-delay="50">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-indigo-600 via-primary to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                                <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-[11px] font-extrabold border border-indigo-100">
                                    Agenda Sekolah
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400">17 Mei 2024</span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900 mb-2 leading-snug group-hover:text-indigo-600 transition-colors line-clamp-2 min-h-[3rem]">
                                Upacara Peringatan & Gelar Seni Budaya 2024
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-6 line-clamp-3 min-h-[4rem]">
                                Seluruh siswa-siswi diharapkan hadir dengan seragam lengkap untuk mengikuti upacara dan menyaksikan gelar pentas seni tahunan.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs mt-auto">
                            <span class="font-extrabold text-indigo-600 inline-flex items-center gap-1.5 group-hover:translate-x-1 transition-transform">
                                <span>Lihat Detail Agenda</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </div>
                    </div>
                </a>

                <!-- Fallback Activity Card 2 -->
                <a href="{{ route('events.show', 2) }}" class="bg-white rounded-3xl p-7 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-amber-500/40 transition-all duration-300 flex flex-col justify-between cursor-pointer group transform hover:-translate-y-2 relative overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-500 to-orange-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                                <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-extrabold border border-amber-100">
                                    Pengumuman Ujian
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400">22 Mei 2024</span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900 mb-2 leading-snug group-hover:text-amber-600 transition-colors line-clamp-2 min-h-[3rem]">
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

                <!-- Fallback Activity Card 3 -->
                <a href="{{ route('events.show', 3) }}" class="bg-white rounded-3xl p-7 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between cursor-pointer group transform hover:-translate-y-2 relative overflow-hidden" data-aos="fade-up" data-aos-delay="150">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-slate-100">
                                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-extrabold border border-emerald-100">
                                    Pertemuan
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400">28 Mei 2024</span>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900 mb-2 leading-snug group-hover:text-emerald-600 transition-colors line-clamp-2 min-h-[3rem]">
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
            @endif
        </div>
    </div>
</section>

<!-- ==========================================
     6. GALERI SEKOLAH ("Momen Terbaik Kami" - Clean SaaS Light Card Strip)
     ========================================== -->
<section class="py-20 bg-white text-slate-800 relative border-t border-b border-slate-200/80">
    <div class="container-edunova">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10" data-aos="fade-up">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-primary block mb-1">
                    GALERI SEKOLAH
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Momen Terbaik Kami
                </h2>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('gallery') }}" class="text-xs font-bold text-primary hover:text-secondary flex items-center gap-1 transition-colors uppercase tracking-wider">
                    Lihat Galeri Lengkap
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Photo Strip Grid (Dynamic from DB with fallback) -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5 sm:gap-5">
            @if(isset($galleries) && count($galleries) > 0)
                @foreach($galleries->take(5) as $index => $item)
                    @php
                        $gImg = $item->image ?? $item->image_path ?? '';
                        $gSrc = \Illuminate\Support\Str::startsWith($gImg, 'http') ? $gImg : ($gImg ? (\Illuminate\Support\Str::startsWith($gImg, 'img/') ? asset($gImg) : asset('img/gallery/' . $gImg)) : asset('img/fav.png'));
                    @endphp
                    <div class="{{ $loop->last && $loop->count % 2 != 0 ? 'col-span-2 lg:col-span-1' : '' }} rounded-2xl overflow-hidden relative group shadow-xl border border-slate-200/60 aspect-[4/3]" data-aos="zoom-in" data-aos-delay="{{ 50 * ($index + 1) }}">
                        <img src="{{ $gSrc }}" 
                             alt="{{ $item->title }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                            <h4 class="text-xs font-bold text-white">{{ $item->title }}</h4>
                            <span class="text-[10px] text-indigo-300">{{ $item->category ? $item->category->name : 'Kegiatan Sekolah' }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                @php
                    $gFallbackImg = Setting::get('school_hero_image') ? asset(Setting::get('school_hero_image')) : (Setting::get('logo_path') ? asset(Setting::get('logo_path')) : asset('img/logo.png'));
                @endphp
                <!-- Gallery Item 1 -->
                <div class="rounded-2xl overflow-hidden relative group shadow-xl border border-slate-200/60 aspect-[4/3] bg-slate-50 flex items-center justify-center p-4" data-aos="zoom-in" data-aos-delay="50">
                    <img src="{{ $gFallbackImg }}" alt="Praktikum Lab Sains" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                        <h4 class="text-xs font-bold text-white">Praktikum Lab Sains</h4>
                        <span class="text-[10px] text-indigo-300">Kegiatan Akademik</span>
                    </div>
                </div>

                <!-- Gallery Item 2 -->
                <div class="rounded-2xl overflow-hidden relative group shadow-xl border border-slate-200/60 aspect-[4/3] bg-slate-50 flex items-center justify-center p-4" data-aos="zoom-in" data-aos-delay="100">
                    <img src="{{ $gFallbackImg }}" alt="Pertandingan Basket" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                        <h4 class="text-xs font-bold text-white">Pertandingan Basket</h4>
                        <span class="text-[10px] text-indigo-300">Olahraga & Ekstrakurikuler</span>
                    </div>
                </div>

                <!-- Gallery Item 3 -->
                <div class="rounded-2xl overflow-hidden relative group shadow-xl border border-slate-200/60 aspect-[4/3] bg-slate-50 flex items-center justify-center p-4" data-aos="zoom-in" data-aos-delay="150">
                    <img src="{{ $gFallbackImg }}" alt="Kehidupan Sekolah" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                        <h4 class="text-xs font-bold text-white">Kebersamaan Siswa</h4>
                        <span class="text-[10px] text-indigo-300">Lingkungan Sekolah</span>
                    </div>
                </div>

                <!-- Gallery Item 4 -->
                <div class="rounded-2xl overflow-hidden relative group shadow-xl border border-slate-200/60 aspect-[4/3] bg-slate-50 flex items-center justify-center p-4" data-aos="zoom-in" data-aos-delay="200">
                    <img src="{{ $gFallbackImg }}" alt="Pentas Seni & Budaya" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                        <h4 class="text-xs font-bold text-white">Pentas Seni & Budaya</h4>
                        <span class="text-[10px] text-indigo-300">Seni Tradisional</span>
                    </div>
                </div>

                <!-- Gallery Item 5 -->
                <div class="col-span-2 lg:col-span-1 rounded-2xl overflow-hidden relative group shadow-xl border border-slate-200/60 aspect-[4/3] bg-slate-50 flex items-center justify-center p-4" data-aos="zoom-in" data-aos-delay="250">
                    <img src="{{ $gFallbackImg }}" alt="Gedung Sekolah Modern" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                        <h4 class="text-xs font-bold text-white">Gedung Sekolah Modern</h4>
                        <span class="text-[10px] text-indigo-300">Fasilitas Utama</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- ==========================================
     7. TESTIMONI (Clean & Elegant Cards Slider)
     ========================================== -->
<section class="py-20 lg:py-24 bg-slate-50 border-y border-slate-200/80 overflow-hidden relative" x-data="{
    activeIdx: 0,
    totalItems: 6,
    isDragging: false,
    startX: 0,
    scrollLeft: 0,

    init() {
        if (this.$refs.testiSlider) {
            this.totalItems = this.$refs.testiSlider.children.length;
            this.updateActiveIdx();
        }
    },

    scrollToIdx(idx) {
        const container = this.$refs.testiSlider;
        if (!container || !container.children[idx]) return;
        this.activeIdx = idx;
        const targetChild = container.children[idx];
        const targetLeft = targetChild.offsetLeft - container.offsetLeft;
        container.scrollTo({
            left: targetLeft,
            behavior: 'smooth'
        });
    },

    updateActiveIdx() {
        const container = this.$refs.testiSlider;
        if (!container) return;
        const children = Array.from(container.children);
        if (!children.length) return;

        const scrollPos = container.scrollLeft;
        let closestIndex = 0;
        let minDistance = Infinity;

        children.forEach((child, idx) => {
            const distance = Math.abs(child.offsetLeft - container.offsetLeft - scrollPos);
            if (distance < minDistance) {
                minDistance = distance;
                closestIndex = idx;
            }
        });

        this.activeIdx = closestIndex;
    }
}">
    <div class="container-edunova relative z-10">
        
        <!-- Centered Header -->
        <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16" data-aos="fade-up">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-200/70 text-slate-700 text-xs font-semibold uppercase tracking-wider mb-3">
                Testimoni
            </span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                Apa Kata Mereka Tentang {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}?
            </h2>
            <p class="text-slate-600 text-sm sm:text-base mt-2.5 leading-relaxed">
                Pengalaman dan kesan nyata dari para orang tua, alumni, dan siswa kami.
            </p>
        </div>

        <!-- Touch & Drag Slider Container -->
        <div x-ref="testiSlider"
             @scroll.passive="updateActiveIdx()"
             @mousedown="isDragging = true; startX = $event.pageX - $refs.testiSlider.offsetLeft; scrollLeft = $refs.testiSlider.scrollLeft"
             @mouseleave="isDragging = false"
             @mouseup="isDragging = false"
             @mousemove="if(!isDragging) return; $event.preventDefault(); const x = $event.pageX - $refs.testiSlider.offsetLeft; const walk = (x - startX) * 1.5; $refs.testiSlider.scrollLeft = scrollLeft - walk;"
             class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-6 pt-1 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-none scroll-smooth select-none cursor-grab active:cursor-grabbing">
            
            <!-- Testimoni 1 -->
            <div class="w-[85vw] sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] shrink-0 snap-start bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs hover:border-slate-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700">
                            Orang Tua Siswa
                        </span>
                        <div class="flex gap-1 text-amber-400">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed font-normal mb-6">
                        "{{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }} memberikan lingkungan belajar yang sangat positif. Para guru sangat perhatian terhadap perkembangan akademik dan kepribadian anak kami."
                    </p>
                </div>
                <div class="flex items-center gap-3.5 pt-4 border-t border-slate-100">
                    <img src="https://ui-avatars.com/api/?name=Ratna+Sari&background=4f46e5&color=fff" alt="Ibu Ratna Sari" class="w-11 h-11 rounded-full object-cover border border-slate-200 shrink-0">
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900 leading-snug">Ibu Ratna Sari, M.Pd</h4>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Orang Tua Siswa Kelas XI</p>
                    </div>
                </div>
            </div>

            <!-- Testimoni 2 -->
            <div class="w-[85vw] sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] shrink-0 snap-start bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs hover:border-slate-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700">
                            Alumni
                        </span>
                        <div class="flex gap-1 text-amber-400">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed font-normal mb-6">
                        "Fasilitas lab dan bimbingan guru yang luar biasa sangat mendukung saya meraih prestasi olimpiade sains hingga berhasil diterima di PTN impian."
                    </p>
                </div>
                <div class="flex items-center gap-3.5 pt-4 border-t border-slate-100">
                    <img src="https://ui-avatars.com/api/?name=Dimas+Prasetyo&background=0284c7&color=fff" alt="Dimas Prasetyo" class="w-11 h-11 rounded-full object-cover border border-slate-200 shrink-0">
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900 leading-snug">Dimas Prasetyo</h4>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Alumni (Teknik Elektro ITB)</p>
                    </div>
                </div>
            </div>

            <!-- Testimoni 3 -->
            <div class="w-[85vw] sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] shrink-0 snap-start bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs hover:border-slate-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700">
                            Orang Tua Siswa
                        </span>
                        <div class="flex gap-1 text-amber-400">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed font-normal mb-6">
                        "Lingkungan belajar yang aman dan nyaman. Program bimbingan konseling karirnya sangat membantu dalam mengarahkan rencana studi lanjut anak saya."
                    </p>
                </div>
                <div class="flex items-center gap-3.5 pt-4 border-t border-slate-100">
                    <img src="https://ui-avatars.com/api/?name=Hendra+Wijaya&background=059669&color=fff" alt="dr. Hendra Wijaya" class="w-11 h-11 rounded-full object-cover border border-slate-200 shrink-0">
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900 leading-snug">dr. Hendra Wijaya</h4>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Orang Tua Siswa Kelas XII</p>
                    </div>
                </div>
            </div>

            <!-- Testimoni 4 -->
            <div class="w-[85vw] sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] shrink-0 snap-start bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs hover:border-slate-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700">
                            Alumni
                        </span>
                        <div class="flex gap-1 text-amber-400">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed font-normal mb-6">
                        "Pembelajaran berbasis digital dan kurikulum yang aplikatif di {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }} membuat kami sangat siap beradaptasi dan bersaing di masa depan."
                    </p>
                </div>
                <div class="flex items-center gap-3.5 pt-4 border-t border-slate-100">
                    <img src="https://ui-avatars.com/api/?name=Rizky+Pratama&background=d97706&color=fff" alt="Muhammad Rizky Pratama" class="w-11 h-11 rounded-full object-cover border border-slate-200 shrink-0">
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900 leading-snug">Muhammad Rizky Pratama</h4>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Alumni (Sistem Informasi UI)</p>
                    </div>
                </div>
            </div>

            <!-- Testimoni 5 -->
            <div class="w-[85vw] sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] shrink-0 snap-start bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs hover:border-slate-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700">
                            Orang Tua Alumni
                        </span>
                        <div class="flex gap-1 text-amber-400">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed font-normal mb-6">
                        "Pendidikan karakter dan pembinaan akhlak di sekolah ini berjalan beriringan dengan keunggulan akademik. Sangat kami rekomendasikan."
                    </p>
                </div>
                <div class="flex items-center gap-3.5 pt-4 border-t border-slate-100">
                    <img src="https://ui-avatars.com/api/?name=Bambang+Suryono&background=7c3aed&color=fff" alt="Drs. H. Bambang Suryono" class="w-11 h-11 rounded-full object-cover border border-slate-200 shrink-0">
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900 leading-snug">Drs. H. Bambang Suryono</h4>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Orang Tua Alumni (Angkatan 2023)</p>
                    </div>
                </div>
            </div>

            <!-- Testimoni 6 -->
            <div class="w-[85vw] sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] shrink-0 snap-start bg-white rounded-2xl p-6 sm:p-7 border border-slate-200 shadow-xs hover:border-slate-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-slate-100 text-slate-700">
                            Siswa Aktif
                        </span>
                        <div class="flex gap-1 text-amber-400">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-slate-700 text-sm sm:text-[15px] leading-relaxed font-normal mb-6">
                        "Banyak pilihan ekstrakurikuler serta guru yang selalu suportif membuat kami percaya diri untuk berorganisasi dan mengeksplorasi minat bakat."
                    </p>
                </div>
                <div class="flex items-center gap-3.5 pt-4 border-t border-slate-100">
                    <img src="https://ui-avatars.com/api/?name=Clarissa+Putri&background=db2777&color=fff" alt="Clarissa Putri" class="w-11 h-11 rounded-full object-cover border border-slate-200 shrink-0">
                    <div>
                        <h4 class="text-sm font-semibold text-slate-900 leading-snug">Clarissa Putri</h4>
                        <p class="text-xs text-slate-500 font-normal mt-0.5">Siswi Kelas XII (Ketua OSIS)</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Controls & Dots Indicator -->
        <div class="flex items-center justify-center gap-3 mt-6 sm:mt-8">
            <!-- Prev Button -->
            <button @click="scrollToIdx(Math.max(0, activeIdx - 1))"
                    :disabled="activeIdx === 0"
                    class="w-9 h-9 rounded-full bg-white border border-slate-200 shadow-xs flex items-center justify-center text-slate-600 hover:bg-slate-50 hover:text-slate-900 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-200 active:scale-95"
                    title="Sebelumnya"
                    aria-label="Sebelumnya">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </button>
            
            <!-- Dots -->
            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-full border border-slate-200 shadow-xs">
                <template x-for="(item, index) in Array.from({ length: totalItems })" :key="index">
                    <button @click="scrollToIdx(index)"
                            class="h-2 rounded-full transition-all duration-300 focus:outline-none"
                            :class="activeIdx === index ? 'w-6 bg-slate-800' : 'w-2 bg-slate-300 hover:bg-slate-400'"
                            :title="'Ke Slide ' + (index + 1)"></button>
                </template>
            </div>

            <!-- Next Button -->
            <button @click="scrollToIdx(Math.min(totalItems - 1, activeIdx + 1))"
                    :disabled="activeIdx === totalItems - 1"
                    class="w-9 h-9 rounded-full bg-white border border-slate-200 shadow-xs flex items-center justify-center text-slate-600 hover:bg-slate-50 hover:text-slate-900 disabled:opacity-30 disabled:cursor-not-allowed transition-all duration-200 active:scale-95"
                    title="Selanjutnya"
                    aria-label="Selanjutnya">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

    </div>
</section>

@if(Setting::get('spmb_enabled', '1') == '1')
<!-- ==========================================
     8. SPMB BANNER ("Penerimaan Peserta Didik Baru" - Modern SaaS CTA Card)
     ========================================== -->
<section class="py-16 bg-white">
    <div class="container-edunova">
        <div class="bg-gradient-to-r from-primary/10 via-indigo-50/50 to-secondary/10 rounded-3xl p-8 sm:p-12 text-slate-800 shadow-[0_15px_50px_-15px_rgba(14,30,75,0.08)] relative overflow-hidden border border-primary/20" data-aos="zoom-in">
            <!-- Decorative Glow Background Elements -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl -z-0 pointer-events-none"></div>
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                
                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="inline-block px-3.5 py-1 rounded-full bg-primary/10 backdrop-blur-md text-xs font-extrabold uppercase tracking-widest text-primary border border-primary/25">
                        SPMB 2024/2025
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                        Penerimaan Peserta Didik Baru
                    </h2>
                    <p class="text-slate-600 text-xs sm:text-sm max-w-xl font-normal leading-relaxed">
                        Bergabunglah bersama {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }} dan raih masa depan cerah bersama kami. Hubungi narahubung kami atau daftar online melalui portal SPMB.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('spmb.register') }}" class="px-8 py-3.5 rounded-full bg-gradient-to-r from-primary via-secondary to-primary hover:brightness-110 text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-primary/20 transition-all inline-flex items-center gap-2 transform hover:-translate-y-0.5 uppercase tracking-wider">
                            Daftar Sekarang
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Right Highlights (3 Points) -->
                <div class="lg:col-span-5 border-t lg:border-t-0 lg:border-l border-slate-200/80 pt-6 lg:pt-0 lg:pl-8 space-y-4">
                    
                    <!-- Point 1 -->
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 backdrop-blur-md flex items-center justify-center text-primary shrink-0 border border-primary/20 mt-0.5">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Proses Mudah</h4>
                            <p class="text-xs text-slate-500">Pendaftaran online cepat & praktis</p>
                        </div>
                    </div>

                    <!-- Point 2 -->
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 backdrop-blur-md flex items-center justify-center text-primary shrink-0 border border-primary/20 mt-0.5">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Beasiswa Prestasi</h4>
                            <p class="text-xs text-slate-500">Tersedia berbagai program beasiswa</p>
                        </div>
                    </div>

                    <!-- Point 3 -->
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 backdrop-blur-md flex items-center justify-center text-primary shrink-0 border border-primary/20 mt-0.5">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Lingkungan Positif</h4>
                            <p class="text-xs text-slate-500">Belajar nyaman dalam lingkungan inspiratif</p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('spmb.info') }}" class="inline-flex items-center text-xs font-bold text-primary hover:text-secondary transition-colors gap-1 uppercase tracking-wider">
                            Informasi SPMB Selengkapnya
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>
@endif

<!-- ==========================================
     9. SEKSI BERITA TERBARU & ARTIKEL BLOG (Placed right above Footer / right below SPMB Banner)
     ========================================== -->
<section class="py-16 bg-white border-t border-slate-100">
    <div class="container-edunova">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10" data-aos="fade-up">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 block mb-1">
                    BERITA TERBARU & ARTIKEL
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Informasi & Inspirasi
                </h2>
            </div>
            <a href="{{ route('blog') }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors gap-1 group uppercase tracking-wider">
                Lihat Semua Berita & Artikel
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        <!-- News Cards Grid (Dynamic if $posts has items, else Fallback to mockup cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            @if(isset($posts) && count($posts) > 0)
                @foreach($posts->take(3) as $post)
                    @php
                        $pSrc = $post->thumbnail_url ?? (Setting::get('logo_path') ? asset(Setting::get('logo_path')) : asset('img/logo.png'));
                    @endphp
                    <a href="{{ route('blog.show', $post->slug) }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden group hover:shadow-2xl hover:border-primary/40 transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1.5 cursor-pointer" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="h-48 overflow-hidden relative">
                            <img src="{{ $pSrc }}" 
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
                                    {{ $post->created_at ? $post->created_at->format('d M Y') : date('d M Y') }}
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
            @else
                @php
                    $blogFallbackImg = Setting::get('school_hero_image') ? asset(Setting::get('school_hero_image')) : (Setting::get('logo_path') ? asset(Setting::get('logo_path')) : asset('img/logo.png'));
                @endphp
                <!-- Fallback Card 1 (Tips Belajar) -->
                <a href="{{ route('blog') }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden group hover:shadow-2xl hover:border-primary/40 transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1.5 cursor-pointer" data-aos="fade-up" data-aos-delay="50">
                    <div class="h-48 overflow-hidden relative bg-slate-50 flex items-center justify-center p-4">
                        <img src="{{ $blogFallbackImg }}" alt="Tips Belajar" class="w-full h-full object-contain group-hover:scale-108 transition-transform duration-500">
                        <div class="absolute top-3 left-3 px-3 py-1 rounded-md bg-blue-600 text-white text-[11px] font-bold shadow-md">
                            Tips Belajar
                        </div>
                    </div>
                    <div class="p-6 flex flex-col justify-between flex-grow">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-400 block mb-2">{{ date('d M Y') }}</span>
                            <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors leading-snug">
                                Tips Belajar Efektif untuk Siswa di Era Digital
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Temukan berbagai metode belajar efektif yang dapat membantu meningkatkan fokus dan hasil...
                            </p>
                        </div>
                        <div class="inline-flex items-center text-xs font-bold text-blue-600 group-hover:text-blue-800 gap-1 mt-auto">
                            <span>Baca Selengkapnya</span>
                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                    </div>
                </a>

                <!-- Fallback Card 2 (Kegiatan Sekolah) -->
                <a href="{{ route('blog') }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden group hover:shadow-2xl hover:border-primary/40 transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1.5 cursor-pointer" data-aos="fade-up" data-aos-delay="100">
                    <div class="h-48 overflow-hidden relative bg-slate-50 flex items-center justify-center p-4">
                        <img src="{{ $blogFallbackImg }}" alt="Upacara Hari Pendidikan" class="w-full h-full object-contain group-hover:scale-108 transition-transform duration-500">
                        <div class="absolute top-3 left-3 px-3 py-1 rounded-md bg-purple-600 text-white text-[11px] font-bold shadow-md">
                            Artikel Sekolah
                        </div>
                    </div>
                    <div class="p-6 flex flex-col justify-between flex-grow">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-400 block mb-2">{{ date('d M Y') }}</span>
                            <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors leading-snug">
                                Peran Pendidikan Karakter dalam Membentuk Pemimpin Masa Depan
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Ulasan mendalam mengenai pentingnya fondasi budi pekerti dalam tantangan global era modern.
                            </p>
                        </div>
                        <div class="inline-flex items-center text-xs font-bold text-blue-600 group-hover:text-blue-800 gap-1 mt-auto">
                            <span>Baca Selengkapnya</span>
                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                    </div>
                </a>

                <!-- Fallback Card 3 (Prestasi) -->
                <a href="{{ route('blog') }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden group hover:shadow-2xl hover:border-primary/40 transition-all duration-300 flex flex-col h-full transform hover:-translate-y-1.5 cursor-pointer" data-aos="fade-up" data-aos-delay="150">
                    <div class="h-48 overflow-hidden relative bg-slate-50 flex items-center justify-center p-4">
                        <img src="{{ $blogFallbackImg }}" alt="Juara Olimpiade Sains" class="w-full h-full object-contain group-hover:scale-108 transition-transform duration-500">
                        <div class="absolute top-3 left-3 px-3 py-1 rounded-md bg-emerald-600 text-white text-[11px] font-bold shadow-md">
                            Prestasi
                        </div>
                    </div>
                    <div class="p-6 flex flex-col justify-between flex-grow">
                        <div>
                            <span class="text-[11px] font-semibold text-slate-400 block mb-2">{{ date('d M Y') }}</span>
                            <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors leading-snug">
                                Siswa {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }} Raih Prestasi Tingkat Provinsi
                            </h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                                Selamat kepada siswa-siswi kami yang berhasil meraih prestasi gemilang dalam kompetisi terbaru.
                            </p>
                        </div>
                        <div class="inline-flex items-center text-xs font-bold text-blue-600 group-hover:text-blue-800 gap-1 mt-auto">
                            <span>Baca Selengkapnya</span>
                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                    </div>
                </a>
            @endif

        </div>
    </div>
</section>

<!-- ==========================================
     10. VIDEO MODAL POPUP
     ========================================== -->
<div x-show="videoModalOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" 
     x-cloak>
    <div @click.away="videoModalOpen = false" class="relative w-full max-w-4xl bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-white/10">
        <!-- Close Button -->
        <button @click="videoModalOpen = false" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-slate-800 text-white flex items-center justify-center hover:bg-rose-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <!-- Video Player -->
        <div class="aspect-video w-full">
            <iframe class="w-full h-full" src="{{ Setting::get('school_video_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0') }}" title="Video Profil {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>

</div>

@endsection
