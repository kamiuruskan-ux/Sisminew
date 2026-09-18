@extends('layouts.landing')

@section('title', 'Kebijakan Privasi - ' . Setting::get('school_name', 'SMA Nusantara'))

@section('content')

<!-- ==========================================
     1. SUBPAGE HERO HEADER (Dark Navy SaaS Style)
     ========================================== -->
<section class="relative bg-slate-50 text-slate-800 pt-28 pb-14 lg:pt-36 lg:pb-16 overflow-hidden border-b border-slate-200/80">
    <!-- Ambient Glow Background -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[750px] h-[450px] bg-gradient-to-tr from-primary/10 via-secondary/10 to-purple-600/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="container-edunova relative z-10">
        <div class="max-w-3xl" data-aos="fade-up">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-primary/10 border border-primary/20 text-xs font-bold text-primary mb-4">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                DOKUMEN RESMI HUKUM & PRIVASI DATA
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-3">
                Kebijakan Privasi
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-normal">
                Komitmen {{ Setting::get('school_name', 'SMA Nusantara') }} dalam melindungi, menjaga kerahasiaan, dan mengelola data pribadi calon siswa serta wali murid.
            </p>
            <p class="text-xs text-slate-500 mt-4 font-medium">
                Terakhir Diperbarui: {{ date('d F Y') }}
            </p>
        </div>
    </div>
</section>

<!-- ==========================================
     2. MAIN CONTENT (Full Width 2-Column Layout)
     ========================================== -->
<section class="py-12 lg:py-16 bg-slate-50/80 min-h-screen">
    <div class="container-edunova">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: STICKY TABLE OF CONTENTS (4 Cols) -->
            <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-28">
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm">
                    <h3 class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-4 pb-3 border-b border-slate-100">
                        Daftar Isi Kebijakan
                    </h3>
                    <nav class="space-y-2 text-xs font-bold">
                        <a href="#ringkasan" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            1. Ringkasan Kebijakan Privasi
                        </a>
                        <a href="#pengumpulan-data" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            2. Jenis Data yang Dikuap
                        </a>
                        <a href="#penggunaan-informasi" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            3. Penggunaan & Pemrosesan Data
                        </a>
                        <a href="#keamanan-data" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            4. Perlindungan & Keamanan Sistem
                        </a>
                        <a href="#hak-pengguna" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            5. Hak Pemilik Data Pribadi
                        </a>
                        <a href="#kontak-layanan" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            6. Kontak Petugas Privasi
                        </a>
                    </nav>
                </div>

                <!-- CS Support Card -->
                <div class="bg-[#0B132B] text-white rounded-3xl p-6 border border-blue-400/20 shadow-xl">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-400 block mb-1">BANTUAN PRIVASI</span>
                    <h4 class="text-sm font-extrabold text-white">Ada Pertanyaan Data?</h4>
                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                        Tim Petugas Kerahasiaan Data {{ Setting::get('school_name', 'SMA Nusantara') }} siap membantu Anda.
                    </p>
                    <a href="mailto:{{ Setting::get('school_email', 'info@sekolah.sch.id') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition-all">
                        Hubungi via Email ↗
                    </a>
                </div>
            </div>

            <!-- RIGHT COLUMN: PRIVACY POLICY DETAILS (8 Cols) -->
            <div class="lg:col-span-8 bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/80 shadow-card space-y-10 text-slate-700 text-sm leading-relaxed">
                
                <!-- Section 1 -->
                <div id="ringkasan" class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        BAGIAN 01
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">1. Ringkasan Kebijakan Privasi</h2>
                    <p>
                        Kebijakan Privasi ini menjelaskan bagaimana {{ Setting::get('school_name', 'SMA Nusantara') }} ("Sekolah", "Kami") mengumpulkan, menggunakan, menyimpan, mengelola, dan melindungi data pribadi yang Anda berikan saat mengakses website resmi kami atau mendaftarkan diri sebagai calon siswa baru melalui Portal SPMB online.
                    </p>
                    <p>
                        Dengan mengakses website ini dan mengirimkan formulir pendaftaran, Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui seluruh ketentuan pengolahan data pribadi dalam dokumen ini.
                    </p>
                </div>

                <!-- Section 2 -->
                <div id="pengumpulan-data" class="pt-8 border-t border-slate-100 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        BAGIAN 02
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">2. Jenis Data yang Dikumpulkan</h2>
                    <p>Kami mengumpulkan informasi pribadi yang diperlukan untuk keperluan verifikasi akademik dan administrasi pendaftaran sekolah, antara lain:</p>
                    
                    <div class="space-y-3">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider mb-1">A. Data Identitas Calon Siswa</h4>
                            <p class="text-xs text-slate-600">Nama lengkap, NISN, NIK, tempat & tanggal lahir, jenis kelamin, pasfoto resmi, dan scan dokumen resmi (KK, Akta Kelahiran, Ijazah/SKL).</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider mb-1">B. Data Orang Tua / Wali Murid</h4>
                            <p class="text-xs text-slate-600">Nama lengkap orang tua/wali, nomor WhatsApp/telepon yang dapat dihubungi, dan alamat domisili lengkap.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider mb-1">C. Data Akun & Perangkat</h4>
                            <p class="text-xs text-slate-600">Alamat email terdaftar, password terenkripsi, serta log aktivitas akses sistem portal pendaftaran.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div id="penggunaan-informasi" class="pt-8 border-t border-slate-100 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        BAGIAN 03
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">3. Penggunaan & Pemrosesan Data</h2>
                    <p>Informasi yang kami kumpulkan hanya digunakan secara terbatas untuk tujuan-tujuan berikut:</p>
                    <ul class="list-disc pl-5 space-y-2 text-xs font-medium text-slate-600">
                        <li>Memproses administrasi verifikasi formulir dan kelayakan pendaftaran siswa baru.</li>
                        <li>Mengirimkan pemberitahuan resmi terkait jadwal tes seleksi, wawancara, dan pengumuman hasil kelulusan.</li>
                        <li>Membuat akun portal siswa terdaftar apabila calon peserta didik dinyatakan diterima di {{ Setting::get('school_name', 'SMA Nusantara') }}.</li>
                        <li>Pelaporan data akademik internal kepada Dinas Pendidikan sesuai peraturan perundang-undangan yang berlaku.</li>
                    </ul>
                </div>

                <!-- Section 4 -->
                <div id="keamanan-data" class="pt-8 border-t border-slate-100 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        BAGIAN 04
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">4. Perlindungan & Keamanan Sistem</h2>
                    <p>
                        Kami berkomitmen tinggi menjaga keamanan data pribadi Anda. Sistem kami dilengkapi dengan enkripsi enkripsi SSL (Secure Sockets Layer), pembatasan hak akses berbasis peran (*role-based access control*), serta proteksi firewall untuk mencegah akses tidak sah, pembocoran, atau perusakan data.
                    </p>
                    <p>
                        Kami **tidak akan pernah menjual, menyewakan, atau membagikan** data pribadi Anda kepada pihak ketiga manapun untuk tujuan komersial atau pemasaran.
                    </p>
                </div>

                <!-- Section 5 -->
                <div id="hak-pengguna" class="pt-8 border-t border-slate-100 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        BAGIAN 05
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">5. Hak Pemilik Data Pribadi</h2>
                    <p>Sebagai pemilik data pribadi, Anda memiliki hak-hak berikut sesuai dengan undang-undang perlindungan data pribadi yang berlaku:</p>
                    <ul class="list-disc pl-5 space-y-2 text-xs font-medium text-slate-600">
                        <li>Hak untuk mengakses dan meminta salinan data pribadi yang tersimpan di sistem kami.</li>
                        <li>Hak untuk memperbarui atau mengoreksi data pribadi yang tidak akurat atau tidak lengkap.</li>
                        <li>Hak untuk meminta penghapusan data pendaftaran apabila tidak melanjutkan proses seleksi.</li>
                    </ul>
                </div>

                <!-- Section 6 -->
                <div id="kontak-layanan" class="pt-8 border-t border-slate-100 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        BAGIAN 06
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">6. Kontak Petugas Privasi Data</h2>
                    <p>
                        Apabila Anda memiliki pertanyaan, saran, atau keluhan terkait Kebijakan Privasi ini, silakan hubungi tim administrasi kami melalui:
                    </p>
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2 font-medium">
                        <p><strong class="text-slate-900 font-extrabold">Lembaga:</strong> {{ Setting::get('school_name', 'SMA Nusantara') }}</p>
                        <p><strong class="text-slate-900 font-extrabold">Alamat:</strong> {{ Setting::get('school_address', 'Jl. Pendidikan No. 123, Jakarta') }}</p>
                        <p><strong class="text-slate-900 font-extrabold">Email Resmi:</strong> {{ Setting::get('school_email', 'info@sekolah.sch.id') }}</p>
                        <p><strong class="text-slate-900 font-extrabold">Telepon / WhatsApp:</strong> {{ Setting::get('school_phone', '0812-3456-7890') }}</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

@endsection
