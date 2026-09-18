@extends('layouts.landing')

@section('title', 'Informasi SPMB & Pendaftaran - ' . Setting::get('school_name', 'Sekolah'))

@section('content')

<!-- ==========================================
     1. FULL-WIDTH HERO SECTION (Dark Navy SaaS Split Layout)
     ========================================== -->
<section class="relative bg-slate-50 text-slate-800 pt-24 pb-12 sm:pt-28 sm:pb-16 lg:pt-36 lg:pb-24 overflow-hidden border-b border-slate-200/80">
    <!-- Ambient Glow background -->
    <div class="absolute top-1/3 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[300px] sm:w-[800px] h-[300px] sm:h-[500px] bg-gradient-to-tr from-primary/10 via-secondary/10 to-purple-600/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-5 w-[250px] sm:w-[450px] h-[200px] sm:h-[300px] bg-secondary/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="container-edunova relative z-10 px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- Left Hero Content (7 Cols) -->
            <div class="lg:col-span-7 space-y-4 sm:space-y-6 text-left" data-aos="fade-right">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-1.5 rounded-full bg-primary/10 border border-primary/20 text-[11px] sm:text-xs font-bold text-primary shadow-sm max-w-full">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse shrink-0"></span>
                    <span class="truncate">{{ Setting::get('spmb_hero_badge', 'PENERIMAAN SISWA BARU T.A. ' . date('Y') . '/' . (date('Y')+1)) }}</span>
                </div>
                
                <h1 class="text-2xl sm:text-4xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    {!! Setting::get('spmb_hero_title', 'Raih Masa Depan Gemilang di <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary via-indigo-600 to-secondary">' . Setting::get('school_name', 'Sekolah') . '</span>') !!}
                </h1>
                
                <p class="text-slate-650 text-xs sm:text-base lg:text-lg leading-relaxed font-normal">
                    {{ Setting::get('spmb_hero_subtitle', 'Bergabunglah dengan institusi pendidikan unggulan terakreditasi A. Kami membuka kesempatan emas pendaftaran murid baru secara online untuk semua jalur seleksi.') }}
                </p>

                <!-- Action CTA Buttons (Responsive Full Width on HP) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                    <a href="{{ route('spmb.register') }}" class="w-full sm:w-auto px-6 py-3.5 sm:px-8 sm:py-4 rounded-xl sm:rounded-full bg-primary hover:bg-secondary text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all flex items-center justify-center gap-2 border border-slate-200/80">
                        <span>{{ Setting::get('spmb_hero_cta_text', 'Daftar SPMB Online Now') }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    @php
                        $waNumber = preg_replace('/[^0-9]/', '', Setting::get('spmb_whatsapp', Setting::get('school_whatsapp', '')));
                    @endphp
                    <a href="{{ $waNumber ? 'https://wa.me/'.$waNumber : route('contact') }}" target="_blank" class="w-full sm:w-auto px-6 py-3.5 sm:px-8 sm:py-4 rounded-xl sm:rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs uppercase tracking-wider border border-slate-200/80 transition-all text-center">
                        Konsultasi Pendaftaran
                    </a>
                </div>

                <!-- Checkmarks Feature Pill List -->
                <div class="pt-2 sm:pt-4 grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-6 text-xs text-slate-300 font-semibold">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Pendaftaran 100% Online</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ Setting::get('spmb_scholarship_info', 'Potongan SPP Beasiswa') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Proses Seleksi Transparan</span>
                    </div>
                </div>
            </div>

            <!-- Right Hero Card Showcase (5 Cols) -->
            <div class="lg:col-span-5" data-aos="fade-left">
                <div class="bg-[#0B132B] rounded-2xl sm:rounded-3xl p-5 sm:p-7 border border-blue-400/30 shadow-2xl relative overflow-hidden backdrop-blur-xl group">
                    <div class="absolute -top-12 -right-12 w-40 h-40 bg-blue-500/20 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <div class="flex items-center justify-between pb-4 sm:pb-6 border-b border-white/10 mb-4 sm:mb-6">
                        <div>
                            <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-blue-400 block mb-1">STATUS PENDAFTARAN</span>
                            <h3 class="text-base sm:text-lg font-extrabold text-white">Gelombang Pendaftaran</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-full {{ Setting::get('spmb_enabled', '1') == '1' ? 'bg-emerald-500/20 border-emerald-400/40 text-emerald-400' : 'bg-amber-500/20 border-amber-400/40 text-amber-400' }} border text-[10px] sm:text-xs font-bold flex items-center gap-1.5 shrink-0">
                            <span class="w-2 h-2 rounded-full {{ Setting::get('spmb_enabled', '1') == '1' ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' }}"></span>
                            {{ Setting::get('spmb_enabled', '1') == '1' ? 'Aktif' : 'Tutup' }}
                        </span>
                    </div>

                    <div class="space-y-3 sm:space-y-4 mb-5 sm:mb-6 text-xs">
                        <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between gap-2">
                            <span class="text-slate-400 font-medium">Kuota Penerimaan:</span>
                            <span class="font-extrabold text-white text-xs sm:text-sm text-right">{{ isset($totalQuota) && $totalQuota > 0 ? number_format($totalQuota) . ' Siswa Baru' : '280 Siswa Baru' }}</span>
                        </div>
                        <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between gap-2">
                            <span class="text-slate-400 font-medium">Biaya Formulir:</span>
                            <span class="font-extrabold text-amber-400 text-xs sm:text-sm text-right">Rp {{ number_format(Setting::get('spmb_registration_fee', 150000), 0, ',', '.') }}</span>
                        </div>
                        <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-white/5 border border-white/10 flex items-center justify-between gap-2">
                            <span class="text-slate-400 font-medium">Beasiswa Prestasi:</span>
                            <span class="font-extrabold text-emerald-400 text-xs sm:text-sm text-right">{{ Setting::get('spmb_scholarship_info', 's.d. 100% Bebas SPP') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('spmb.register') }}" class="w-full py-3.5 rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider text-center block shadow-glow transition-all">
                        Isi Formulir Pendaftaran Sekarang &rarr;
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     2. GELOMBANG SELEKSI (FULL CONTAINER WIDTH)
     ========================================== -->
<section class="py-12 sm:py-16 lg:py-24 bg-white">
    <div class="container-edunova px-4 sm:px-6">
        
        <!-- Section Title Header -->
        <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14" data-aos="fade-up">
            <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 block mb-1">GELOMBANG & JALUR SELEKSI</span>
            <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">Pilihan Gelombang Pendaftaran</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">Membuka beberapa jalur pendaftaran dengan fasilitas beasiswa menarik pada setiap gelombangnya.</p>
        </div>

        <!-- 3 Wave Cards Full Width -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 items-stretch">
            @if(isset($waves) && count($waves) > 0)
                @foreach($waves as $wave)
                    @php
                        $now = \Carbon\Carbon::now();
                        $startDate = \Carbon\Carbon::parse($wave->start_date);
                        $endDate = \Carbon\Carbon::parse($wave->end_date);
                        
                        $isCurrentActive = $wave->status === 'active' && $now->between($startDate, $endDate);
                        $isUpcoming = $now->lt($startDate) && $wave->status === 'active';
                        $isClosed = $now->gt($endDate) || in_array($wave->status, ['closed', 'inactive', 'draft']);
                    @endphp
                    <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 border {{ $isCurrentActive ? 'border-2 border-blue-500 shadow-card' : ($isUpcoming ? 'border-amber-200 bg-amber-50/10' : 'border-slate-200/80 bg-slate-50/50') }} hover:shadow-card transition-all relative flex flex-col justify-between group" data-aos="fade-up">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-100">SELEKSI RESMI</span>
                                @if($isCurrentActive)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500 text-white text-[10px] font-bold shadow-xs">Sedang Dibuka</span>
                                @elseif($isUpcoming)
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-bold shadow-xs">Belum Dibuka</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-bold shadow-xs">Ditutup</span>
                                @endif
                            </div>
                            <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-2">{{ $wave->name }}</h3>
                            <p class="text-xs text-slate-500 mb-5 leading-relaxed">{{ $wave->description ?? 'Pendaftaran siswa baru jalur reguler & prestasi.' }}</p>
                            
                            <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 mb-5 space-y-2 text-xs">
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-slate-500 font-medium">Tanggal Mulai:</span>
                                    <span class="font-bold text-slate-900">{{ $startDate->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-slate-500 font-medium">Tanggal Selesai:</span>
                                    <span class="font-bold text-slate-900">{{ $endDate->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-slate-500 font-medium">Kuota Pendaftaran:</span>
                                    <span class="font-bold text-indigo-600">{{ $wave->quota }} Siswa</span>
                                </div>

                                @php
                                    $filledCount = $wave->spmb_registrations_count ?? 0;
                                    $targetQuota = $wave->quota > 0 ? $wave->quota : 100;
                                    $quotaPercent = min(100, round(($filledCount / $targetQuota) * 100));
                                    $remainingSeats = max(0, $targetQuota - $filledCount);
                                @endphp
                                <!-- Visual Quota Progress Bar -->
                                <div class="pt-2 border-t border-slate-200/60 mt-2 space-y-1.5">
                                    <div class="flex justify-between items-center text-[11px] font-bold">
                                        <span class="text-slate-500 font-medium">Status Kuota Terisi:</span>
                                        <span class="text-slate-900 font-mono">{{ $filledCount }} / {{ $targetQuota }} Siswa</span>
                                    </div>
                                    <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden p-0.5 border border-slate-200/80">
                                        <div class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 h-full rounded-full transition-all duration-500" style="width: {{ max(5, $quotaPercent) }}%"></div>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] text-slate-400 font-semibold">
                                        <span>Sisa: <strong class="text-slate-700">{{ $remainingSeats }} Kursi</strong></span>
                                        <span class="font-extrabold text-blue-600">{{ $quotaPercent }}% Terisi</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($isCurrentActive)
                            <a href="{{ route('spmb.register') }}" class="w-full py-3.5 rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider text-center block shadow-glow transition-all">
                                Daftar Gelombang Ini &rarr;
                            </a>
                        @elseif($isUpcoming)
                            <button type="button" disabled class="w-full py-3.5 rounded-xl sm:rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 font-bold text-xs uppercase tracking-wider text-center block cursor-not-allowed">
                                Belum Dibuka (Buka {{ $startDate->format('d M Y') }})
                            </button>
                        @else
                            <button type="button" disabled class="w-full py-3.5 rounded-xl sm:rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 font-bold text-xs uppercase tracking-wider text-center block cursor-not-allowed">
                                Pendaftaran Ditutup
                            </button>
                        @endif
                    </div>
                @endforeach
            @else
                <!-- Fallback Wave 1: Jalur Prestasi -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 border-2 border-blue-500/80 shadow-card hover:shadow-2xl transition-all relative flex flex-col justify-between group" data-aos="fade-up">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-100">GELOMBANG I</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500 text-white text-[10px] font-bold shadow-xs">Aktif</span>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-2">Jalur Prestasi & Beasiswa</h3>
                        <p class="text-xs text-slate-500 mb-5 leading-relaxed">Fasilitas beasiswa potongan SPP hingga 100% dan keringanan uang gedung bagi siswa berprestasi akademik & non-akademik.</p>
                        
                        <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 mb-5 space-y-2 text-xs">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-slate-500 font-medium">Periode Pendaftaran:</span>
                                <span class="font-bold text-slate-900">01 Mei - 30 Juni</span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-slate-500 font-medium">Fasilitas Khusus:</span>
                                <span class="font-bold text-emerald-600">Beasiswa SPP 100%</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('spmb.register') }}" class="w-full py-3.5 rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider text-center block shadow-glow transition-all">
                        Daftar Gelombang 1 &rarr;
                    </a>
                </div>

                <!-- Fallback Wave 2: Reguler -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-slate-200/80 shadow-xs hover:shadow-card transition-all relative flex flex-col justify-between group" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full border border-purple-100">GELOMBANG II</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold">Mendatang</span>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-2">Jalur Reguler & Tes Akademik</h3>
                        <p class="text-xs text-slate-500 mb-5 leading-relaxed">Seleksi penerimaan umum berdasarkan Tes Potensi Akademik (TPA), psikotes, dan wawancara pemetaan bakat minat calon siswa.</p>
                        
                        <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 mb-5 space-y-2 text-xs">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-slate-500 font-medium">Periode Pendaftaran:</span>
                                <span class="font-bold text-slate-900">01 Juli - 31 Juli</span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-slate-500 font-medium">Jalur Tes:</span>
                                <span class="font-bold text-purple-600">TPA & Wawancara</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('spmb.register') }}" class="w-full py-3.5 rounded-xl sm:rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider text-center block transition-all">
                        Daftar Gelombang 2 &rarr;
                    </a>
                </div>

                <!-- Fallback Wave 3: Undangan -->
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-slate-200/80 shadow-xs hover:shadow-card transition-all relative flex flex-col justify-between group" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-100">GELOMBANG III</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold">Mendatang</span>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 mb-2">Jalur Khusus Undangan</h3>
                        <p class="text-xs text-slate-500 mb-5 leading-relaxed">Jalur apresiasi bagi lulusan SMP/MTs mitra terbaik berdasarkan peringkat paralel sekolah rekomendasi.</p>
                        
                        <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-100 mb-5 space-y-2 text-xs">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-slate-500 font-medium">Periode Pendaftaran:</span>
                                <span class="font-bold text-slate-900">01 Ags - 15 Ags</span>
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-slate-500 font-medium">Seleksi:</span>
                                <span class="font-bold text-amber-600">Bebas Tes TPA</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('spmb.register') }}" class="w-full py-3.5 rounded-xl sm:rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider text-center block transition-all">
                        Daftar Gelombang 3 &rarr;
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- ==========================================
     3. ALUR PENDAFTARAN 4 LANGKAH (FULL CONTAINER WIDTH)
     ========================================== -->
<section class="py-12 sm:py-16 lg:py-24 bg-slate-50/70 border-t border-b border-slate-100">
    <div class="container-edunova px-4 sm:px-6">
        
        <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14" data-aos="fade-up">
            <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 block mb-1">PROSES PENDAFTARAN</span>
            <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900">4 Langkah Mudah Menjadi Siswa</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">Seluruh alur pendaftaran dirancang cepat dan transparan secara digital.</p>
        </div>

        <!-- 4 Step Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6" data-aos="fade-up">
            
            <!-- Step 1 -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs relative group hover:shadow-card transition-all">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 font-extrabold text-xs sm:text-sm flex items-center justify-center mb-3.5 border border-blue-100 shadow-xs">
                    01
                </div>
                <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-blue-600 block mb-1">LANGKAH AWAL</span>
                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 mb-1">{{ Setting::get('spmb_step_1_title', 'Isi Formulir Online') }}</h4>
                <p class="text-xs text-slate-500 leading-relaxed">{{ Setting::get('spmb_step_1_desc', 'Calon siswa mengisi data diri lengkap, data orang tua, dan pilihan jurusan pada portal pendaftaran.') }}</p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs relative group hover:shadow-card transition-all">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-indigo-50 text-indigo-600 font-extrabold text-xs sm:text-sm flex items-center justify-center mb-3.5 border border-indigo-100 shadow-xs">
                    02
                </div>
                <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-indigo-600 block mb-1">VERIFIKASI BERKAS</span>
                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 mb-1">{{ Setting::get('spmb_step_2_title', 'Upload Dokumen') }}</h4>
                <p class="text-xs text-slate-500 leading-relaxed">{{ Setting::get('spmb_step_2_desc', 'Unggah scan Ijazah/SKL SMP, Kartu Keluarga, Akta Kelahiran, serta pasfoto warna terbaru.') }}</p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs relative group hover:shadow-card transition-all">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-purple-50 text-purple-600 font-extrabold text-xs sm:text-sm flex items-center justify-center mb-3.5 border border-purple-100 shadow-xs">
                    03
                </div>
                <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-purple-600 block mb-1">TES SELEKSI</span>
                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 mb-1">{{ Setting::get('spmb_step_3_title', 'Tes TPA & Wawancara') }}</h4>
                <p class="text-xs text-slate-500 leading-relaxed">{{ Setting::get('spmb_step_3_desc', 'Mengikuti ujian potensi akademik secara online / hadir di sekolah serta sesi pemetaan minat bakat.') }}</p>
            </div>

            <!-- Step 4 -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs relative group hover:shadow-card transition-all">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 font-extrabold text-xs sm:text-sm flex items-center justify-center mb-3.5 border border-emerald-100 shadow-xs">
                    04
                </div>
                <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-emerald-600 block mb-1">HASIL SELEKSI</span>
                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 mb-1">{{ Setting::get('spmb_step_4_title', 'Pengumuman & Re-Registrasi') }}</h4>
                <p class="text-xs text-slate-500 leading-relaxed">{{ Setting::get('spmb_step_4_desc', 'Pengumuman hasil kelulusan di portal SPMB dan verifikasi pendaftaran ulang calon siswa baru.') }}</p>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     4. PERSYARATAN & BANNER CALL TO ACTION
     ========================================== -->
<section class="py-12 sm:py-16 lg:py-24 bg-white">
    <div class="container-edunova px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- Left Documents List (6 Cols) -->
            <div class="lg:col-span-6 space-y-4 sm:space-y-6" data-aos="fade-right">
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 block mb-1">CHECKLIST DOKUMEN</span>
                    <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 leading-tight">Persyaratan Dokumen Administrasi</h2>
                    <p class="text-xs text-slate-500 mt-1">Pastikan berkas digital berikut telah dipersiapkan dengan jelas.</p>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 sm:gap-4 hover:border-blue-300 transition-colors">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shrink-0 shadow-xs">1</div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">{{ Setting::get('spmb_doc_req_1_title', 'Fotokopi Ijazah / SKL SMP') }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ Setting::get('spmb_doc_req_1_desc', 'Surat Keterangan Lulus resmi atau Ijazah SMP/MTs yang dilegalisir oleh pihak sekolah.') }}</p>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 sm:gap-4 hover:border-indigo-300 transition-colors">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-extrabold text-xs shrink-0 shadow-xs">2</div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">{{ Setting::get('spmb_doc_req_2_title', 'Kartu Keluarga & Akta Kelahiran') }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ Setting::get('spmb_doc_req_2_desc', 'Fotokopi Kartu Keluarga dan Akta Kelahiran calon siswa yang terdaftar resmi NIK.') }}</p>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 sm:gap-4 hover:border-purple-300 transition-colors">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-extrabold text-xs shrink-0 shadow-xs">3</div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900">{{ Setting::get('spmb_doc_req_3_title', 'Pasfoto Terbaru (3x4)') }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ Setting::get('spmb_doc_req_3_desc', '2 lembar pasfoto berwarna terbaru dengan latar belakang merah atau biru.') }}</p>
                        </div>
                    </div>

                    @if(Setting::get('spmb_doc_req_4_title'))
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 sm:gap-4 hover:border-emerald-300 transition-colors">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-extrabold text-xs shrink-0 shadow-xs">4</div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900">{{ Setting::get('spmb_doc_req_4_title') }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ Setting::get('spmb_doc_req_4_desc') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Dark Navy CTA Card (6 Cols) -->
            <div class="lg:col-span-6" data-aos="fade-left">
                <div class="bg-gradient-to-r from-primary/10 via-indigo-50/50 to-secondary/10 text-slate-800 rounded-2xl sm:rounded-3xl p-6 sm:p-10 shadow-lg relative overflow-hidden border border-primary/20 group">
                    <div class="absolute -top-16 -right-16 w-56 h-56 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-extrabold uppercase tracking-wider mb-4 border border-primary/20">
                        PENDAFTARAN SPMB ONLINE
                    </div>
                    
                    <h3 class="text-xl sm:text-3xl font-extrabold text-slate-900 mb-3 leading-tight">
                        Siap Menjadi Bagian Dari {{ Setting::get('school_name', 'Sekolah') }}?
                    </h3>
                    
                    <p class="text-slate-650 text-xs sm:text-sm leading-relaxed mb-6">
                        {{ Setting::get('spmb_info_text', 'Proses pendaftaran online mudah, serba cepat, dan transparan. Dapatkan nomor bukti pendaftaran dan akun portal pendaftaran siswa dalam hitungan menit.') }}
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <a href="{{ route('spmb.register') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl sm:rounded-full bg-primary hover:bg-secondary text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all flex items-center justify-center gap-2">
                            <span>Mulai Pendaftaran Online</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="{{ $waNumber ? 'https://wa.me/'.$waNumber : route('contact') }}" target="_blank" class="w-full sm:w-auto px-6 py-3.5 rounded-xl sm:rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs uppercase tracking-wider border border-slate-200/80 transition-all text-center">
                            Hubungi Admin WA
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
