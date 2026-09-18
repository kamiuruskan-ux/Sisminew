@extends('layouts.landing')

@section('title', 'Syarat & Ketentuan - ' . Setting::get('school_name', 'SMA Nusantara'))

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
                REGULASI & ATURAN RESMI PENDIDIKAN
            </div>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-3">
                Syarat & Ketentuan
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-normal">
                Ketentuan regulasi pendaftaran, kewajiban calon peserta didik, dan tata tertib penerimaan siswa baru di {{ Setting::get('school_name', 'SMA Nusantara') }}.
            </p>
            <p class="text-xs text-slate-500 mt-4 font-medium">
                Berlaku Untuk Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}
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
                        Navigasi Ketentuan
                    </h3>
                    <nav class="space-y-2 text-xs font-bold">
                        <a href="#ketentuan-umum" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            1. Ketentuan Umum Sekolah
                        </a>
                        <a href="#persyaratan-siswa" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            2. Persyaratan Calon Siswa
                        </a>
                        <a href="#prosedur-pendaftaran" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            3. Prosedur Pendaftaran & Seleksi
                        </a>
                        <a href="#hak-kewajiban" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            4. Hak & Kewajiban Orang Tua
                        </a>
                        <a href="#pembatalan-sanksi" class="block p-3 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-600 transition-colors">
                            5. Ketentuan Pembatalan & Sanksi
                        </a>
                    </nav>
                </div>

                <!-- CS Support Card -->
                <div class="bg-[#0B132B] text-white rounded-3xl p-6 border border-blue-400/20 shadow-xl">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-blue-400 block mb-1">PANITIA SELEKSI</span>
                    <h4 class="text-sm font-extrabold text-white">Pertanyaan Ketentuan SPMB?</h4>
                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                        Panitia Penerimaan Siswa Baru {{ Setting::get('school_name', 'SMA Nusantara') }} siap memberikan penjelasan teknis.
                    </p>
                    <a href="{{ route('contact') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition-all">
                        Hubungi Panitia SPMB ↗
                    </a>
                </div>
            </div>

            <!-- RIGHT COLUMN: TERMS DETAILS (8 Cols) -->
            <div class="lg:col-span-8 bg-white rounded-3xl p-8 sm:p-12 border border-slate-200/80 shadow-card space-y-10 text-slate-700 text-sm leading-relaxed">
                
                <!-- Section 1 -->
                <div id="ketentuan-umum" class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        REGULASI 01
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">1. Ketentuan Umum Sekolah</h2>
                    <p>
                        Dokumen Syarat & Ketentuan ini mengatur tata cara penerimaan peserta didik baru, regulasi akademik, serta kewajiban administrasi di {{ Setting::get('school_name', 'SMA Nusantara') }}.
                    </p>
                    <p>
                        Seluruh calon peserta didik dan orang tua/wali murid wajib memahami dan menyetujui seluruh ketentuan yang tercantum sebelum melengkapi formulir pendaftaran SPMB.
                    </p>
                </div>

                <!-- Section 2 -->
                <div id="persyaratan-siswa" class="pt-8 border-t border-slate-100 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        REGULASI 02
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">2. Persyaratan Calon Peserta Didik</h2>
                    <p>Calon peserta didik baru {{ Setting::get('school_name', 'SMA Nusantara') }} wajib memenuhi kriteria berikut:</p>
                    
                    <div class="space-y-3">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider mb-1">A. Persyaratan Akademik</h4>
                            <p class="text-xs text-slate-600">Lulusan SMP/MTs/Sederajat resmi yang dibuktikan dengan Ijazah, Surat Keterangan Lulus (SKL), atau Buku Rapor SMP.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider mb-1">B. Persyaratan Administrasi</h4>
                            <p class="text-xs text-slate-600">Memiliki NIK sah yang terdaftar di KK, mengunggah pasfoto terbaru ukuran 3x4, Kartu Keluarga, dan Akta Kelahiran.</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <h4 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider mb-1">C. Persyaratan Sikap & Kesehatan</h4>
                            <p class="text-xs text-slate-600">Berkelakuan baik, tidak terlibat tindakan kriminal atau penyalahgunaan narkoba, serta sehat jasmani dan rohani.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div id="prosedur-pendaftaran" class="pt-8 border-t border-slate-100 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        REGULASI 03
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">3. Prosedur Pendaftaran & Seleksi</h2>
                    <ol class="list-decimal pl-5 space-y-2.5 text-xs font-medium text-slate-600">
                        <li>Pendaftaran dilakukan secara mandiri melalui website resmi SPMB {{ Setting::get('school_name', 'SMA Nusantara') }}.</li>
                        <li>Pendaftar wajib mengisi formulir pendaftaran 5-tahapan secara jujur, akurat, dan dapat dipertanggungjawabkan.</li>
                        <li>Verifikasi berkas fisik akan dilakukan oleh panitia seleksi setelah data online berhasil dikirim.</li>
                        <li>Calon siswa wajib mengikuti seluruh rangkaian seleksi (Tes Potensi Akademik & Wawancara) sesuai jadwal gelombang pendaftaran yang dipilih.</li>
                        <li>Pengumuman hasil kelulusan bersifat mutlak dan ditentukan berdasarkan keputusan Rapat Panitia Seleksi SPMB.</li>
                    </ol>
                </div>

                <!-- Section 4 -->
                <div id="hak-kewajiban" class="pt-8 border-t border-slate-100 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        REGULASI 04
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">4. Hak & Kewajiban Orang Tua / Wali</h2>
                    <ul class="list-disc pl-5 space-y-2 text-xs font-medium text-slate-600">
                        <li>Orang tua/wali berhak menerima laporan perkembangan seleksi dan pengumuman resmi dari pihak sekolah.</li>
                        <li>Orang tua/wali wajib menyelesaikan registrasi ulang (*re-registrasi*) dan administrasi keuangan sesuai batas waktu yang ditentukan setelah dinyatakan lulus.</li>
                        <li>Orang tua/wali bersedia mendukung pelaksanaan tata tertib dan peraturan disiplin sekolah selama siswa menempuh pendidikan di {{ Setting::get('school_name', 'SMA Nusantara') }}.</li>
                    </ul>
                </div>

                <!-- Section 5 -->
                <div id="pembatalan-sanksi" class="pt-8 border-t border-slate-100 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                        REGULASI 05
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">5. Ketentuan Pembatalan & Sanksi</h2>
                    <p>
                        Apabila di kemudian hari ditemukan kecurangan, pemalsuan dokumen ijazah/KK, atau manipulasi data pribadi yang diisikan pada formulir pendaftaran:
                    </p>
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-medium">
                        <strong class="font-extrabold block uppercase tracking-wider mb-1">Sanksi Pembatalan Hak Kelulusan:</strong>
                        Panitia SPMB {{ Setting::get('school_name', 'SMA Nusantara') }} berhak secara sepihak **membatalkan hak kelulusan** atau **mengeluarkan siswa terdaftar** tanpa pengembalian biaya pendaftaran yang telah dibayarkan.
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

@endsection
