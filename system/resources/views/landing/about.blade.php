@extends('layouts.landing')

@section('title', 'Profil & Tentang Kami - ' . Setting::get('school_name', 'SDIT AL-FAHMI PALU'))

@section('content')

<!-- ==========================================
     1. SUBPAGE HERO HEADER (Dark Navy SaaS Style)
     ========================================== -->
<section class="relative bg-slate-50 text-slate-800 pt-28 pb-16 lg:pt-36 lg:pb-24 overflow-hidden border-b border-slate-200/80">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[500px] bg-gradient-to-tr from-primary/10 via-secondary/10 to-purple-600/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="container-edunova relative z-10">
        <div class="max-w-4xl" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-xs font-extrabold text-primary mb-4">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                TENTANG INSTITUSI
            </div>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-5">
                Membimbing Potensi, Mencetak <br class="hidden sm:block"/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-indigo-600 to-secondary">
                    Generasi Unggul & Berkarakter
                </span>
            </h1>
            <p class="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed font-normal max-w-2xl">
                {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }} hadir sebagai lembaga pendidikan berkualitas tinggi yang memadukan keunggulan akademik, teknologi modern, dan penguatan budi pekerti luhur.
            </p>
        </div>
    </div>
</section>

<!-- ==========================================
     2. SEJARAH SINGKAT & FILOSOFI PENDIDIKAN
     ========================================== -->
<section class="py-16 lg:py-24 bg-white">
    <div class="container-edunova">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Image Card -->
            <div class="lg:col-span-5 relative" data-aos="fade-right">
                <div class="rounded-3xl overflow-hidden shadow-card border border-slate-200/80 bg-white group">
                    <img src="{{ Setting::get('school_hero_image') ? asset(Setting::get('school_hero_image')) : (Setting::get('logo_path') ? asset(Setting::get('logo_path')) : asset('img/logo.png')) }}" 
                         alt="Gedung {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}" 
                         class="w-full h-[440px] object-cover group-hover:scale-105 transition-transform duration-700">
                </div>
                <div class="absolute -bottom-6 -right-6 bg-[#0B132B] text-white p-6 rounded-2xl shadow-xl border border-white/15 hidden sm:block">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-400 block mb-1">Berdiri Sejak</span>
                    <span class="text-3xl font-extrabold text-white">Tahun {{ Setting::get('school_founded_year', '2008') }}</span>
                    <span class="text-[11px] text-slate-300 block mt-1">{{ $stats['years'] ?? '15+' }} Pengalaman</span>
                </div>
            </div>

            <!-- Right Content -->
            <div class="lg:col-span-7 space-y-6" data-aos="fade-left">
                <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 block">
                    SEJARAH & REPUTASI
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                    Ekosistem Pembelajaran Terintegrasi Berstandar Internasional
                </h2>
                @if(Setting::get('school_history'))
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        {{ Setting::get('school_history') }}
                    </p>
                @else
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        Didirikan pada tahun {{ Setting::get('school_founded_year', '2008') }}, {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }} berkembang menjadi salah satu sekolah terfavorit yang berkomitmen melahirkan lulusan berdaya saing tinggi. Kami meyakini bahwa setiap siswa memiliki potensi unik yang memerlukan bimbingan terarah, lingkungan kondusif, dan sarana modern.
                    </p>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        Dengan implementasi Kurikulum Merdeka yang disempurnakan dengan program pengayaan internasional, siswa kami tidak hanya siap menembus Perguruan Tinggi Negeri (PTN) terkemuka, tetapi juga siap menjadi pemimpin masa depan di tingkat global.
                    </p>
                @endif

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 block mb-0.5">{{ number_format($stats['students'] ?? 0, 0, ',', '.') }}+</span>
                        <span class="text-xs font-semibold text-slate-500">Siswa Aktif</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 block mb-0.5">{{ number_format($stats['teachers'] ?? 0, 0, ',', '.') }}+</span>
                        <span class="text-xs font-semibold text-slate-500">Guru & Staff</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 block mb-0.5">{{ $stats['alumni'] ?? '0' }}+</span>
                        <span class="text-xs font-semibold text-slate-500">Alumni Tersebar</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-[#0B132B] text-white text-center">
                        <span class="text-2xl sm:text-3xl font-extrabold text-blue-400 block mb-0.5">A+</span>
                        <span class="text-xs font-semibold text-slate-300">Akreditasi Unggul</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     3. VISI, MISI & NILAI UTAMA (CORE VALUES)
     ========================================== -->
<section id="visi-misi" class="py-16 lg:py-24 bg-slate-50/70 border-t border-b border-slate-100">
    <div class="container-edunova">
        <div class="text-center max-w-xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 block mb-1">FONDASI INSTITUSI</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900">Visi, Misi & Nilai Utama</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            
            <!-- Visi Card -->
            <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-card transition-all" data-aos="fade-right">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 shadow-sm border border-blue-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 mb-3">Visi Utama</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    {{ Setting::get('school_vision', 'Menjadi lembaga pendidikan unggul yang berkarakter, berdaya saing global, dan berlandaskan imtak & iptek.') }}
                </p>
            </div>

            <!-- Misi Card -->
            <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm hover:shadow-card transition-all" data-aos="fade-left">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-6 shadow-sm border border-indigo-100">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 mb-3">Misi Pendidikan</h3>
                @php
                    $misiText = Setting::get('school_mission', "Menyelenggarakan proses pembelajaran interaktif berbasis sains dan teknologi modern.\nMengembangkan bakat, minat, dan kepemimpinan siswa melalui ekstra kurikulum unggulan.\nMembangun karakter disiplin, toleransi, dan kepedulian sosial dalam kehidupan bermasyarakat.");
                    $misis = array_filter(explode("\n", $misiText));
                @endphp
                <ul class="space-y-3 text-slate-600 text-sm">
                    @foreach($misis as $misi)
                        @if(trim($misi))
                            <li class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-indigo-600 mt-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ trim($misi) }}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>

        </div>

        <!-- 4 Core Values Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
            
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm" data-aos="fade-up" data-aos-delay="50">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold mb-4">01</div>
                <h4 class="text-base font-bold text-slate-900 mb-1">Integritas Tinggi</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Jujur, bertanggung jawab, dan memegang teguh norma kedisiplinan sekolah.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold mb-4">02</div>
                <h4 class="text-base font-bold text-slate-900 mb-1">Inovasi Digital</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Berpikir kritis, adaptif teknologi, serta kreatif memecahkan masalah masa kini.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm" data-aos="fade-up" data-aos-delay="150">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold mb-4">03</div>
                <h4 class="text-base font-bold text-slate-900 mb-1">Daya Saing Global</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Kemampuan Bahasa Asing yang fasih dan wawasan luas di tingkat internasional.</p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold mb-4">04</div>
                <h4 class="text-base font-bold text-slate-900 mb-1">Budi Pekerti Luhur</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Menghormati sesama, ramah, serta mengutamakan kebersamaan dan rasa kekeluargaan.</p>
            </div>

        </div>

    </div>
</section>

<!-- ==========================================
     4. SAMBUTAN KEPALA SEKOLAH
     ========================================== -->
<section id="sambutan" class="py-16 lg:py-24 bg-white">
    <div class="container-edunova">
        <div class="bg-[#0B132B] text-white rounded-3xl p-8 sm:p-12 shadow-2xl relative overflow-hidden border border-white/10" data-aos="zoom-in">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                
                <!-- Principal Photo -->
                <div class="lg:col-span-4 flex justify-center">
                    @php
                        $pPhoto = Setting::get('school_principal_photo');
                        $pPhotoUrl = $pPhoto ? (\Illuminate\Support\Str::startsWith($pPhoto, ['http://', 'https://']) ? $pPhoto : (\Illuminate\Support\Str::startsWith($pPhoto, 'img/') ? asset($pPhoto) : asset('img/avatars/' . $pPhoto))) : (Setting::get('logo_path') ? asset(Setting::get('logo_path')) : asset('img/logo.png'));
                    @endphp
                    <div class="relative w-48 h-48 sm:w-56 sm:h-56 rounded-full overflow-hidden border-4 border-blue-500/30 shadow-2xl shrink-0">
                        <img src="{{ $pPhotoUrl }}" 
                             alt="Kepala Sekolah {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}" 
                             class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Principal Words -->
                <div class="lg:col-span-8 space-y-4 text-center lg:text-left">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-blue-400 block">
                        SAMBUTAN KEPALA SEKOLAH
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-white">
                        {{ Setting::get('school_principal_name', 'Dr. H. Ahmad Wijaya, M.Pd.') }}
                    </h3>
                    <p class="text-slate-300 text-sm leading-relaxed italic">
                        "{{ Setting::get('school_principal_welcome', 'Selamat datang di sekolah kami. Kami berkomitmen membentuk generasi unggul, berakhlak mulia, dan siap memimpin di masa depan.') }}"
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('spmb.register') }}" class="px-6 py-2.5 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-glow transition-all inline-flex items-center gap-2 uppercase tracking-wider">
                            Bergabung Bersama Kami →
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     5. STRUKTUR ORGANISASI SEKOLAH (COMPREHENSIVE PROPORTIONAL ORG TREE)
     ========================================== -->
<section id="struktur-organisasi" class="py-16 lg:py-24 bg-slate-50/70 border-t border-slate-100 relative overflow-hidden">
    <div class="container-edunova relative z-10">
        
        <!-- Section Header -->
        <div class="text-center max-w-xl mx-auto mb-14" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-blue-500/10 border border-blue-400/25 text-xs font-bold text-blue-600 mb-3">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                BAGAN ORGANISASI RESMI
            </div>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Struktur Organisasi Sekolah</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-2">Bagan hierarki tata kelola dan pimpinan {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}.</p>
        </div>        <!-- ORG CHART IMAGE -->
        <div class="max-w-4xl mx-auto rounded-3xl overflow-hidden border border-slate-200/80 bg-white shadow-xl p-4 sm:p-6" data-aos="zoom-in">
            @if(Setting::get('school_org_chart_path'))
                <img src="{{ asset(Setting::get('school_org_chart_path')) }}" alt="Struktur Organisasi {{ Setting::get('school_name') }}" class="w-full h-auto object-contain">
            @else
                <div class="py-16 text-center text-slate-400">
                    <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    <p class="text-sm font-bold">Bagan Struktur Organisasi Belum Diunggah</p>
                    <p class="text-xs mt-1">Unggah bagan resmi melalui Halaman Konfigurasi Admin.</p>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- ==========================================
     6. FASILITAS UNGGULAN SEKOLAH
     ========================================== -->
<section class="py-16 lg:py-24 bg-white border-t border-slate-100">
    <div class="container-edunova">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12" data-aos="fade-up">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 block mb-1">INFRASTRUKTUR MODERN</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900">Fasilitas Penunjang Belajar</h2>
            </div>
            <a href="{{ route('gallery') }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors gap-1 uppercase tracking-wider">
                Lihat Galeri Fasilitas →
            </a>
        </div>

        @php
            $fasilityFallback = Setting::get('school_hero_image') ? asset(Setting::get('school_hero_image')) : (Setting::get('logo_path') ? asset(Setting::get('logo_path')) : asset('img/logo.png'));
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm group hover:shadow-card transition-all">
                <div class="h-44 overflow-hidden bg-slate-50 flex items-center justify-center p-3">
                    <img src="{{ $fasilityFallback }}" alt="Laboratorium Sains" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5">
                    <h4 class="text-base font-bold text-slate-900 mb-1">Laboratorium Sains & Robotik</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Peralatan eksperimen fisika, kimia, biologi, dan komputer IoT terbaru.</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm group hover:shadow-card transition-all">
                <div class="h-44 overflow-hidden bg-slate-50 flex items-center justify-center p-3">
                    <img src="{{ $fasilityFallback }}" alt="Perpustakaan Digital" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5">
                    <h4 class="text-base font-bold text-slate-900 mb-1">Perpustakaan Digital</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Ribuan koleksi e-book, jurnal ilmiah, dan ruang baca ber-AC yang nyaman.</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm group hover:shadow-card transition-all">
                <div class="h-44 overflow-hidden bg-slate-50 flex items-center justify-center p-3">
                    <img src="{{ $fasilityFallback }}" alt="Gelanggang Olahraga" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5">
                    <h4 class="text-base font-bold text-slate-900 mb-1">Lapangan Basket & Lap. Futsal</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Sarana olahraga indoor & outdoor berstandar kompetisi nasional.</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-sm group hover:shadow-card transition-all">
                <div class="h-44 overflow-hidden bg-slate-50 flex items-center justify-center p-3">
                    <img src="{{ $fasilityFallback }}" alt="Aula Serbaguna" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5">
                    <h4 class="text-base font-bold text-slate-900 mb-1">Aula Serbaguna & Panggung Seni</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Kapasitas 1.000 orang untuk seminar, graduasi, dan pertunjukan seni budaya.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     7. CTA REGISTER BANNER
     ========================================== -->
<section class="py-16 bg-slate-50/70">
    <div class="container-edunova">
        <div class="bg-[#0B132B] text-white rounded-3xl p-8 sm:p-12 shadow-2xl relative overflow-hidden border border-white/10 text-center max-w-4xl mx-auto" data-aos="zoom-in">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white mb-3">Ingin Mengetahui Lebih Banyak Tentang {{ Setting::get('school_name', 'SDIT AL-FAHMI PALU') }}?</h2>
            <p class="text-slate-300 text-xs sm:text-sm max-w-xl mx-auto mb-6">
                Tim layanan SPMB kami siap memberikan konsultasi gratis seputar pendaftaran, program beasiswa, dan kunjungan sekolah.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('spmb.register') }}" class="px-8 py-3.5 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider shadow-glow inline-flex items-center gap-2">
                    Daftar SPMB Online Now →
                </a>
                <a href="{{ route('contact') }}" class="px-7 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider border border-white/20 inline-flex items-center gap-2">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
