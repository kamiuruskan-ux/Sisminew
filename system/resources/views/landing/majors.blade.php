@extends('layouts.landing')

@section('title', 'Jurusan & Program Unggulan - ' . Setting::get('school_name', 'SDIT AL-FAHMI PALU'))

@section('content')

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
            <span class="text-primary font-bold">Jurusan & Peminatan</span>
        </div>

        <div class="max-w-3xl mx-auto" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-xs font-extrabold text-primary mb-5 shadow-inner">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                PEMINATAN & PROGRAM AKADEMIK UNGGULAN
            </div>
            
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-5">
                Jurusan & Program Unggulan
            </h1>
            
            <p class="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed font-normal max-w-2xl mx-auto">
                Pilihan peminatan akademik terpadu yang dirancang sesuai minat, bakat, serta proyeksi karir masa depan siswa di {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}.
            </p>
        </div>
    </div>
</section>

<!-- ==========================================
     2. DAFTAR JURUSAN & PEMINATAN CARDS (CENTERED GRID)
     ========================================== -->
<section class="py-16 lg:py-24 bg-gradient-to-b from-slate-50 via-white to-slate-50">
    <div class="container-edunova">
        
        <!-- Centered Section Header -->
        <div class="text-center max-w-2xl mx-auto mb-14" data-aos="fade-up">
            <span class="text-xs font-extrabold uppercase tracking-widest text-primary block mb-2">
                PILIHAN PEMINATAN
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Temukan Peminatan Sesuai Passion Anda
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm mt-3 leading-relaxed">
                Setiap program dilengkapi dengan fasilitas pendukung modern, bimbingan konseling karir, dan kurikulum komprehensif.
            </p>
        </div>

        <!-- Centered Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 justify-center items-stretch max-w-6xl mx-auto">
            
            @if(isset($majors) && count($majors) > 0)
                @foreach($majors as $major)
                    <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-primary/40 transition-all duration-300 flex flex-col justify-between items-center text-center group transform hover:-translate-y-2 relative overflow-hidden" 
                         data-aos="fade-up" 
                         data-aos-delay="{{ $loop->index * 100 }}">
                        
                        <!-- Top Gradient Accent Line -->
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-primary via-indigo-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="w-full flex flex-col items-center">
                            <!-- Icon Box -->
                            <div class="w-16 h-16 rounded-2xl bg-indigo-50/80 text-primary flex items-center justify-center mb-6 shadow-md border border-indigo-100/80 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            </div>

                            <!-- Code Badge -->
                            <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[11px] font-extrabold uppercase tracking-wider mb-3 border border-primary/20">
                                Kode: {{ $major->code }}
                            </span>

                            <h3 class="text-xl font-extrabold text-slate-900 mb-3 group-hover:text-primary transition-colors leading-snug">
                                {{ $major->name }}
                            </h3>

                            <p class="text-xs text-slate-500 leading-relaxed mb-6">
                                {{ $major->description ?? 'Program pembelajaran komprehensif yang dirancang untuk mempersiapkan siswa bersaing di universitas terkemuka.' }}
                            </p>

                            <!-- Feature Tags -->
                            <div class="w-full pt-4 border-t border-slate-100 mb-6 space-y-2.5 text-left text-xs">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span class="font-medium">Kurikulum Riset & Praktikum</span>
                                </div>
                                <div class="flex items-center gap-2 text-slate-700">
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span class="font-medium">Bimbingan Masuk PTN Impian</span>
                                </div>
                                <div class="flex items-center gap-2 text-slate-700">
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span class="font-medium">Fasilitas Laboratorium Modern</span>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Link Button -->
                        <a href="{{ route('spmb.register') }}" class="w-full py-3.5 rounded-full bg-slate-900 hover:bg-primary text-white font-extrabold text-xs uppercase tracking-wider text-center transition-all duration-200 shadow-md transform active:scale-95 flex items-center justify-center gap-2">
                            <span>Pilih {{ $major->name }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                @endforeach
            @else
                <!-- Fallback MIPA -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-blue-500/40 transition-all duration-300 flex flex-col justify-between items-center text-center group transform hover:-translate-y-2 relative overflow-hidden" data-aos="fade-up" data-aos-delay="50">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    <div class="w-full flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 shadow-md border border-blue-100 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.183.187l-1.2.6A2 2 0 002 17.697V19a2 2 0 002 2h16a2 2 0 002-2v-1.303a2 2 0 00-1.183-1.815l-1.389-.695z"/></svg>
                        </div>
                        
                        <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[11px] font-extrabold uppercase tracking-wider mb-3 border border-blue-100">
                            PROGRAM MIPA
                        </span>
                        
                        <h3 class="text-xl font-extrabold text-slate-900 mb-3 group-hover:text-blue-600 transition-colors leading-snug">
                            Matematika & Ilmu Pengetahuan Alam
                        </h3>
                        
                        <p class="text-xs text-slate-500 leading-relaxed mb-6">
                            Fokus pada penguasaan sains fisika, kimia, biologi, dan matematika tingkat lanjut untuk calon insinyur, dokter, dan saintis.
                        </p>

                        <div class="w-full pt-4 border-t border-slate-100 mb-6 space-y-2.5 text-left text-xs">
                            <div class="flex items-center gap-2 text-slate-700">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="font-medium">Praktikum Fisika & Biologi Modern</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="font-medium">Persiapan Fakultas Kedokteran & Teknik</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="font-medium">Klub Robotik & Olimpiade Sains</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('spmb.register') }}" class="w-full py-3.5 rounded-full bg-slate-900 hover:bg-blue-600 text-white font-extrabold text-xs uppercase tracking-wider text-center transition-all duration-200 shadow-md transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Pilih MIPA</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- Fallback IPS -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-purple-500/40 transition-all duration-300 flex flex-col justify-between items-center text-center group transform hover:-translate-y-2 relative overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-purple-500 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="w-full flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-6 shadow-md border border-purple-100 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        
                        <span class="px-3 py-1 rounded-full bg-purple-50 text-purple-600 text-[11px] font-extrabold uppercase tracking-wider mb-3 border border-purple-100">
                            PROGRAM IPS
                        </span>
                        
                        <h3 class="text-xl font-extrabold text-slate-900 mb-3 group-hover:text-purple-600 transition-colors leading-snug">
                            Ilmu Pengetahuan Sosial
                        </h3>
                        
                        <p class="text-xs text-slate-500 leading-relaxed mb-6">
                            Mendalami ekonomi, sosiologi, geografi, dan kewirausahaan untuk membentuk calon pemimpin bisnis, diplomat, dan hukum.
                        </p>

                        <div class="w-full pt-4 border-t border-slate-100 mb-6 space-y-2.5 text-left text-xs">
                            <div class="flex items-center gap-2 text-slate-700">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="font-medium">Kewirausahaan & Simulasi Bisnis</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="font-medium">Persiapan Hukum, Manajamen, & Akuntansi</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="font-medium">Debat Bahasa & Studi Hubungan Internasional</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('spmb.register') }}" class="w-full py-3.5 rounded-full bg-slate-900 hover:bg-purple-600 text-white font-extrabold text-xs uppercase tracking-wider text-center transition-all duration-200 shadow-md transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Pilih IPS</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- Fallback Bahasa -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm hover:shadow-2xl hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between items-center text-center group transform hover:-translate-y-2 relative overflow-hidden" data-aos="fade-up" data-aos-delay="150">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="w-full flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-6 shadow-md border border-emerald-100 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V7.437M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
                        </div>
                        
                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-extrabold uppercase tracking-wider mb-3 border border-emerald-100">
                            PROGRAM BAHASA
                        </span>
                        
                        <h3 class="text-xl font-extrabold text-slate-900 mb-3 group-hover:text-emerald-600 transition-colors leading-snug">
                            Bahasa & Komunikasi Global
                        </h3>
                        
                        <p class="text-xs text-slate-500 leading-relaxed mb-6">
                            Fokus pada penguasaan Bahasa Inggris, Bahasa Jepang/Mandarin, sastra, serta media komunikasi digital.
                        </p>

                        <div class="w-full pt-4 border-t border-slate-100 mb-6 space-y-2.5 text-left text-xs">
                            <div class="flex items-center gap-2 text-slate-700">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="font-medium">Native Speaker & Studio Bahasa</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="font-medium">Persiapan Sertifikasi TOEFL & JLPT</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-700">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span class="font-medium">Public Speaking & Digital Media Content</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('spmb.register') }}" class="w-full py-3.5 rounded-full bg-slate-900 hover:bg-emerald-600 text-white font-extrabold text-xs uppercase tracking-wider text-center transition-all duration-200 shadow-md transform active:scale-95 flex items-center justify-center gap-2">
                        <span>Pilih Bahasa</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            @endif

        </div>

    </div>
</section>

<!-- ==========================================
     3. FEATURE HIGHLIGHTS (CENTERED 3-COLUMN)
     ========================================== -->
<section class="py-16 bg-white border-t border-slate-100">
    <div class="container-edunova">
        
        <div class="text-center max-w-2xl mx-auto mb-12" data-aos="fade-up">
            <span class="text-xs font-extrabold uppercase tracking-widest text-indigo-600 block mb-2">
                KEUNGGULAN AKADEMIK
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Mengapa Memilih Program di {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}?
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <!-- Feature 1 -->
            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center space-y-4 hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="50">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Kurikulum Berstandar Tinggi</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Integrasi Kurikulum Merdeka yang disesuaikan dengan kebutuhan seleksi perguruan tinggi dan perkembangan industri global.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center space-y-4 hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Fasilitas Praktikum Modern</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Laboratorium sains berstandar internasional, studio media digital, lab komputer CBT, serta perpustakaan digital terpadu.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 text-center space-y-4 hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="150">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Bimbingan Karir & PTN Impian</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pendampingan intensif tes bakat minat, pembekalan ujian SNBP/SNBT, serta konsultasi beasiswa ke luar negeri.
                </p>
            </div>
        </div>

    </div>
</section>

<!-- ==========================================
     4. SPMB BANNER CTA (CENTERED BOTTOM)
     ========================================== -->
@if(Setting::get('spmb_enabled', '1') == '1')
<section class="py-16 bg-slate-50">
    <div class="container-edunova">
        <div class="bg-gradient-to-r from-blue-950 via-indigo-900 to-slate-950 rounded-3xl p-8 sm:p-12 text-white shadow-2xl text-center relative overflow-hidden border border-white/10 max-w-5xl mx-auto" data-aos="zoom-in">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl mx-auto space-y-4">
                <div class="inline-block px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-extrabold uppercase tracking-widest text-blue-300 border border-white/15">
                    PENDAFTARAN SISWA BARU
                </div>
                
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight">
                    Siap Memulai Perjalanan Masa Depan Anda?
                </h2>
                
                <p class="text-slate-300 text-xs sm:text-sm font-normal leading-relaxed">
                    Daftarkan diri Anda sekarang pada jurusan pilihan dan raih kesempatan mendapatkan Beasiswa Prestasi di {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}.
                </p>

                <div class="pt-3 flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('spmb.register') }}" class="px-8 py-3.5 rounded-full bg-white hover:bg-slate-100 text-blue-950 font-extrabold text-xs sm:text-sm shadow-xl transition-all inline-flex items-center gap-2 uppercase tracking-wider transform hover:-translate-y-0.5">
                        <span>Daftar Jurusan Sekarang</span>
                        <svg class="w-4 h-4 text-blue-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    
                    <a href="{{ route('contact') }}" class="px-6 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs sm:text-sm border border-white/20 transition-all uppercase tracking-wider">
                        Konsultasi Peminatan
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@endsection
