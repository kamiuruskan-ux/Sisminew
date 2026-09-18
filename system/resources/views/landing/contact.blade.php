@extends('layouts.landing')

@section('title', 'Hubungi & Lokasi Kampus - ' . Setting::get('school_name', 'SMA Nusantara'))

@section('content')

<div x-data="{
    submitted: false,
    sendMessage() {
        this.submitted = true;
        setTimeout(() => this.submitted = false, 5000);
    }
}">

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
            <span class="text-primary font-bold">Kontak Kami</span>
        </div>

        <div class="max-w-3xl mx-auto" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-xs font-extrabold text-primary mb-5 shadow-inner">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                PUSAT LAYANAN & INFORMASI
            </div>

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-5">
                Kontak & Lokasi Sekolah
            </h1>

            <p class="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed font-normal max-w-2xl mx-auto">
                Punya pertanyaan seputar penerimaan siswa baru, konsultasi beasiswa, atau jadwal kunjungan kampus? Tim {{ Setting::get('school_name', 'SMA Nusantara') }} siap membantu Anda.
            </p>
        </div>
    </div>
</section>

<!-- ==========================================
     2. QUICK CONTACT 4 CARDS (2-GRID MOBILE, 4-GRID DESKTOP)
     ========================================== -->
<section class="py-12 bg-slate-50/80 border-b border-slate-100">
    <div class="container-edunova">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6" data-aos="fade-up">
            
            <!-- Card 1: Alamat -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-card transition-all flex flex-col justify-between h-full">
                <div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3 sm:mb-4 border border-blue-100 shadow-xs">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-blue-600 block mb-1">KAMPUS UTAMA</span>
                    <h4 class="text-xs sm:text-base font-extrabold text-slate-900 mb-1">Alamat Sekolah</h4>
                    <p class="text-[11px] sm:text-xs text-slate-500 leading-relaxed line-clamp-3 sm:line-clamp-none">{{ Setting::get('school_address', 'Jl. Pendidikan No. 10, Jakarta Selatan, DKI Jakarta 12345') }}</p>
                </div>
            </div>

            <!-- Card 2: Telepon & WA -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-card transition-all flex flex-col justify-between h-full">
                <div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 sm:mb-4 border border-emerald-100 shadow-xs">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-emerald-600 block mb-1">RESPON CEPAT</span>
                    <h4 class="text-xs sm:text-base font-extrabold text-slate-900 mb-1">Telepon & WA</h4>
                    <p class="text-[11px] sm:text-xs text-slate-500 leading-relaxed truncate">{{ Setting::get('school_phone', '(021) 1234 5678') }}</p>
                    <p class="text-[11px] sm:text-xs font-bold text-emerald-600 mt-0.5 truncate">{{ Setting::get('school_whatsapp', '081234567890') }} (WA)</p>
                </div>
            </div>

            <!-- Card 3: Email Layanan -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-card transition-all flex flex-col justify-between h-full">
                <div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 sm:mb-4 border border-indigo-100 shadow-xs">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-indigo-600 block mb-1">EMAIL INSTITUSI</span>
                    <h4 class="text-xs sm:text-base font-extrabold text-slate-900 mb-1">Email Resmi</h4>
                    <p class="text-[11px] sm:text-xs text-slate-500 leading-relaxed truncate">{{ Setting::get('school_email', 'info@smanusantara.sch.id') }}</p>
                </div>
            </div>

            <!-- Card 4: Jam Kerja -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-card transition-all flex flex-col justify-between h-full">
                <div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3 sm:mb-4 border border-amber-100 shadow-xs">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-amber-600 block mb-1">JAM LAYANAN</span>
                    <h4 class="text-xs sm:text-base font-extrabold text-slate-900 mb-1">Jam Operasional</h4>
                    <p class="text-[11px] sm:text-xs text-slate-500 leading-relaxed">{{ Setting::get('school_operating_hours', 'Senin - Jumat: 07:00 - 16:00 WIB') }}</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     3. MAIN SECTION: 2-COLUMN BALANCED LAYOUT (FORM + MAPS)
     ========================================== -->
<section class="py-12 sm:py-16 lg:py-24 bg-white">
    <div class="container-edunova">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-stretch">
            
            <!-- Left Column: Contact Form (6 Cols) -->
            <div class="lg:col-span-6 bg-slate-50/80 rounded-2xl sm:rounded-3xl p-5 sm:p-8 lg:p-10 border border-slate-200/80 shadow-sm flex flex-col justify-between" data-aos="fade-right">
                <div>
                    <div class="mb-6">
                        <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 block mb-1">LAYANAN PESAN</span>
                        <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900">Kirim Pesan Ke Kami</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Isi formulir di bawah ini dan tim layanan kami akan membalas pesan Anda.</p>
                    </div>

                    <!-- Success Toast -->
                    <div x-show="submitted" x-transition x-cloak class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Pesan Anda berhasil terkirim! Tim kami akan membalas pesan Anda dalam 1x24 jam.
                    </div>

                    <form @submit.preventDefault="sendMessage()" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap *</label>
                                <input type="text" required placeholder="Masukkan nama Anda" class="w-full bg-white border border-slate-200 text-slate-900 text-xs px-4 py-3 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email *</label>
                                <input type="email" required placeholder="nama@email.com" class="w-full bg-white border border-slate-200 text-slate-900 text-xs px-4 py-3 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp</label>
                                <input type="tel" placeholder="0812xxxxxxxx" class="w-full bg-white border border-slate-200 text-slate-900 text-xs px-4 py-3 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Topik Pertanyaan</label>
                                <select class="w-full bg-white border border-slate-200 text-slate-900 text-xs px-4 py-3 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all">
                                    <option value="spmb">Pendaftaran SPMB Online</option>
                                    <option value="academic">Kurikulum & Jurusan</option>
                                    <option value="extracurricular">Ekstrakurikuler & Beasiswa</option>
                                    <option value="other">Kunjungan / Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Subjek Pesan *</label>
                            <input type="text" required placeholder="Contoh: Konsultasi Beasiswa SPMB" class="w-full bg-white border border-slate-200 text-slate-900 text-xs px-4 py-3 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pesan Anda *</label>
                            <textarea rows="4" required placeholder="Tuliskan pertanyaan atau informasi yang Anda butuhkan..." class="w-full bg-white border border-slate-200 text-slate-900 text-xs px-4 py-3 rounded-xl focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 transition-all"></textarea>
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider shadow-glow transition-all flex items-center justify-center gap-2 active:scale-98">
                            <span>Kirim Pesan Sekarang</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column: Interactive Google Maps & CS Pill (6 Cols) -->
            <div class="lg:col-span-6 flex flex-col justify-between space-y-6" data-aos="fade-left">
                <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col h-full">
                    
                    <!-- Header -->
                    <div class="p-4 sm:p-6 border-b border-slate-100 bg-slate-50/50 flex flex-row items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-widest text-blue-600 block mb-0.5">LOKASI FISIK</span>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Peta Google Maps Kampus</h3>
                        </div>
                        <a href="https://maps.google.com" target="_blank" class="px-3 sm:px-3.5 py-1.5 rounded-full bg-blue-600 hover:bg-blue-500 text-white text-[10px] sm:text-[11px] font-bold shadow-sm transition-all inline-flex items-center gap-1 shrink-0">
                            Buka Maps ↗
                        </a>
                    </div>

                    <!-- Google Maps Iframe -->
                    <div class="relative flex-1 min-h-[260px] sm:min-h-[360px] bg-slate-100">
                        <iframe 
                            src="{{ Setting::get('maps_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.273615468565!2d106.82496417587787!3d-6.227608260986791!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e481b490cd%3A0x6b2e1f5ff0b4e3e3!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid') }}" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            class="w-full h-full min-h-[260px] sm:min-h-[360px] grayscale hover:grayscale-0 transition-all duration-500">
                        </iframe>
                    </div>

                    <!-- Location Footer Info -->
                    <div class="p-3.5 sm:p-4 bg-slate-900 text-white flex items-center justify-between text-xs">
                        <span class="font-semibold flex items-center gap-2 text-[11px] sm:text-xs truncate">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                            <span class="truncate">{{ Setting::get('school_name', 'SMA Nusantara') }} - Kampus Utama</span>
                        </span>
                        <span class="text-slate-400 text-[10px] sm:text-[11px] shrink-0 hidden sm:inline">Terverifikasi di Google Maps</span>
                    </div>

                </div>

                <!-- WhatsApp Live Chat CTA Card -->
                <div class="p-5 sm:p-6 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-emerald-200 block mb-0.5">RESPON CEPAT</span>
                        <h4 class="text-sm sm:text-base font-extrabold text-white">Butuh Informasi Langsung?</h4>
                        <p class="text-xs text-emerald-100 mt-0.5 leading-relaxed">Hubungi Customer Service kami via WhatsApp Live Chat.</p>
                    </div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', Setting::get('school_whatsapp', '081234567890')) }}?text=Halo%20Admin%20{{ urlencode(Setting::get('school_name', 'SMA Nusantara')) }},%20saya%20ingin%20bertanya" target="_blank" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-full bg-white text-emerald-800 hover:bg-emerald-50 font-extrabold text-xs shadow-md transition-all shrink-0 uppercase tracking-wider inline-flex items-center justify-center gap-2">
                        Chat WhatsApp →
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     4. FAQ ACCORDION SECTION (PERTANYAAN UMUM)
     ========================================== -->
<section class="py-12 sm:py-16 bg-slate-50/70 border-t border-slate-100">
    <div class="container-edunova">
        <div class="text-center max-w-xl mx-auto mb-10" data-aos="fade-up">
            <span class="text-xs font-extrabold uppercase tracking-widest text-blue-600 block mb-1">TANYA JAWAB</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900">Pertanyaan Sering Diajukan</h2>
        </div>

        <div class="max-w-3xl mx-auto space-y-3.5" data-aos="fade-up">
            
            <div x-data="{ open: true }" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm">
                <button @click="open = !open" class="w-full flex items-center justify-between text-left font-extrabold text-slate-900 text-xs sm:text-sm gap-2">
                    <span>Bagaimana cara mendaftar secara online di {{ Setting::get('school_name', 'SMA Nusantara') }}?</span>
                    <svg class="w-4 h-4 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse class="mt-3 text-xs text-slate-500 leading-relaxed border-t border-slate-100 pt-3">
                    Anda dapat menekan tombol "Daftar SPMB" pada bagian header atau mengunjungi halaman SPMB Info untuk mengisi formulir pendaftaran digital dalam beberapa menit.
                </div>
            </div>

            <div x-data="{ open: false }" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm">
                <button @click="open = !open" class="w-full flex items-center justify-between text-left font-extrabold text-slate-900 text-xs sm:text-sm gap-2">
                    <span>Apakah tersedia jalur beasiswa bagi siswa berprestasi?</span>
                    <svg class="w-4 h-4 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse class="mt-3 text-xs text-slate-500 leading-relaxed border-t border-slate-100 pt-3">
                    Ya! Kami menyediakan beasiswa Jalur Prestasi Akademik dan Non-Akademik yang memberikan potongan SPP hingga 100% pada Gelombang 1.
                </div>
            </div>

            <div x-data="{ open: false }" class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm">
                <button @click="open = !open" class="w-full flex items-center justify-between text-left font-extrabold text-slate-900 text-xs sm:text-sm gap-2">
                    <span>Kapan jam kunjungan untuk tour kampus langsung?</span>
                    <svg class="w-4 h-4 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse class="mt-3 text-xs text-slate-500 leading-relaxed border-t border-slate-100 pt-3">
                    Kunjungan kampus dibuka setiap hari kerja (Senin - Jumat) pukul 08:00 - 16:00 WIB. Disarankan untuk mengkonfirmasi melalui WhatsApp terlebih dahulu.
                </div>
            </div>

        </div>
    </div>
</section>

</div>

@endsection
