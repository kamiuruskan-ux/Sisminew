# Dokumentasi Fitur Sistem Website & Manajemen Sekolah Terpadu (SIAKAD, LMS, CBT & E-Kantin)

Sistem Informasi Akademik (SIAKAD), Learning Management System (LMS), Ujian Online CBT, Bimbingan Konseling (BK), E-Kantin Digital, dan Portal Sekolah Terpadu dikembangkan dengan dukungan **Role-Based Access Control (RBAC)** Multi-Portal serta integrasi **WhatsApp Gateway**, **AI Face Recognition**, dan **Manajemen Transaksi Terpadu**.

---

## 🏗️ Arsitektur Sistem & Spesifikasi Utama

- **Kerangka Kerja (Framework)**: Berbasis Teknologi Modern Web Framework (PHP 8.x+), Responsive Styling, & Interaktivitas Web.
- **Keamanan Akses (RBAC)**: Multi-Role Access Control dengan 11 Peran Pengguna.
- **Arsitektur Media & Penyimpanan Berkas**:
  - Pengelolaan media gambar, foto, dan dokumen digital yang aman dan responsif.
  - Kompatibel penuh untuk berbagai lingkungan hosting web server (cPanel, WAMP, XAMPP, dan VPS Server).
- **Komponen Input & Antarmuka Cerdas**:
  - Filter pencarian siswa presisi berbasis Nama, NISN, atau Kelas.
  - Filter pencarian guru pengajar & staf sekolah.
  - Form input nominal mata uang Rupiah otomatis terformat dengan generator kalimat **Terbilang** dinamis.
- **Keamanan Rute (Obfuscated Hash ID)**: Proteksi ID Rute URL menggunakan Hash Obfuscation untuk mencegah celah keamanan akses data tanpa hak (IDOR).

---

## 📋 Daftar Modul & Fitur Terintegrasi

### 1. Sistem Autentikasi, Keamanan Siber & Multi-Role (10 Role)
- **Autentikasi Multi-Role 10 Akses**: Super Admin, Admin/Tata Usaha (TU), Guru, Guru BK, Bendahara, Operator, Staff, Siswa, Orang Tua, Calon Siswa (SPMB), dan Pengelola Kantin.
- **Peran Terisolasi Guru BK**: Akses terpisah khusus pengelola layanan Bimbingan Konseling, pencatatan poin pelanggaran, penerbitan Berita Acara (BAP), dan asesmen minat bakat.
- **Variasi Metode Login**:
  - Admin, Guru, Staff, & Vendor: Login via Email/Username & Password.
  - Siswa: Login via NISN & Password.
  - Orang Tua: Login Instan via NISN + Tanggal Lahir Siswa + Kode OTP WhatsApp.
  - Calon Siswa: Login via Kode Registrasi Pendaftaran SPMB.
- **Keamanan Transaksi Dompet Digital (PIN 6-Digit)**: Autentikasi PIN 6-digit untuk transaksi finansial, pembayaran SPP, dan belanja di E-Kantin.
- **Sistem Proteksi Bruteforce & Lockout Account**:
  - Pembatasan batas percobaan login gagal yang otomatis mengunci akun pengguna (Lockout).
  - **Aktivasi Akun Instan (Status Toggle)**: Fitur aktivasi atau nonaktivasi instan akun pengguna & siswa dari Panel Admin.
  - **Buka Kunci Akun (Lockout Unlocker)**: Fitur satu-klik bagi Admin untuk membuka kembali akun yang terkunci akibat salah password/OTP.
- **Dashboard Audit Trail & Log Keamanan**: Log aktivitas login real-time mencakup alamat IP, User Agent, status percobaan (sukses/gagal/otp/captcha), dan alasan kegagalan.
- **Proteksi Captcha & IP Whitelist/Blacklist**: Pengamanan form autentikasi dari bot/bruteforce menggunakan sistem Captcha dan manajemen blokir IP.

---

### 2. Kartu Tanda Pelajar (KTS) Digital & Generator QR/Barcode Vektor
- **Standar Fisik ISO CR-80**: Layout kartu identitas digital dengan rasio fisik 85.6mm x 54mm memuat Logo Sekolah, Pasfoto, NISN, NIK, Nama Lengkap, Kelas, Jurusan, serta Stempel & Tanda Tangan Kepala Sekolah.
- **Generator QR Code Vektor**: QR Code berbasis NISN berpresisi tinggi untuk keperluan scan presensi digital dan transaksi E-Kantin.
- **KTS Builder & Designer Template**: Pengaturan warna header, warna aksen, logo watermark background, serta visibilitas atribut kartu.
- **Cetak Massal (Bulk Print) & Satuan**: Opsi cetak kartu per kelas/angkatan atau per siswa langsung ke format cetak PDF/Printer standar dari Admin, Portal Siswa, dan Portal Orang Tua.

---

### 3. Modul Presensi Digital (QR Code, GPS Geofencing & AI Face Recognition)
- **Presensi Scan QR Code Real-Time Siswa**: Scan presensi siswa menggunakan kamera laptop, webcam, smartphone, maupun Barcode Scanner USB.
- **Notifikasi Presensi Instant WhatsApp**: Pengiriman notifikasi WA otomatis ke Orang Tua saat siswa melakukan scan presensi masuk atau pulang.
- **Presensi Guru & Staf via GPS Geolocation & Swafoto (Selfie)**: Presensi Check-In/Check-Out harian guru & staf dilengkapi koordinat lokasi GPS dan Geofencing radius sekolah.
- **Registrasi & Presensi AI Face Recognition**:
  - Halaman Registrasi Wajah (Face ID Registration) untuk Guru & Siswa.
  - **Overlay Siluet Wajah Manusia Ergonomis**: Panduan pemindaian wajah dengan indikator sampel titik tengah interaktif & status pemindaian.
  - Presensi Wajah Real-Time (*Live Camera Face ID Scanner*) dengan pencocokan data foto wajah terenkripsi.
- **Pengajuan Izin & Sakit**: Modul permohonan izin/sakit siswa & guru dengan unggah bukti lampiran surat/foto.
- **Rekapitulasi & Ekspor Laporan Presensi**: Rekap kehadiran harian/bulanan per kelas/divisi dengan ekspor Excel (.xlsx) & cetak rekapitulasi.

---

### 4. Dashboard Diagnostik Kapasitas Server CBT (CBT Server Capacity)
- **Monitoring Spesifikasi & Resource Server**: Deteksi kapasitas RAM Server, CPU Core, PHP Memory Limit, Max Execution Time, dan versi Database Engine.
- **Analisis Latensi & Database Connection Pool**: Pengujian kinerja query database dan throughput koneksi saat ujian berlangsung.
- **Kalkulator Concurrent Users (Estimasi Siswa Bersamaan)**: Kalkulasi otomatis batas aman jumlah siswa yang dapat mengerjakan ujian CBT secara bersamaan tanpa crash.
- **Toolkit Optimasi Satu-Klik**:
  - Cache Konfigurasi Sistem.
  - Cache Rute Aplikasi.
  - Cache Tampilan/View.
  - Penyesuaian Mode Produksi (Disable Debug Mode) langsung dari UI Panel Admin.

---

### 5. Ujian Online CBT (Computer Based Test) & Bank Soal Excel
- **Manajemen Ujian CBT**: Pengaturan jadwal ujian per kelas, mata pelajaran, durasi waktu pengerjaan, dan acak urutan soal/opsi jawaban.
- **Bank Soal Multi-Tipe**: Mendukung soal Pilihan Ganda (A, B, C, D, E) dan Soal Essay/Uraian.
- **Fitur Impor & Ekspor Soal via Excel**: Impor bank soal massal menggunakan Template Excel (.xlsx) terstruktur.
- **Ruang Ujian Siswa dengan Live Timer**: Hitung mundur otomatis (Countdown Timer) & pengumpulan otomatis (*Auto-Submit*) saat durasi habis.
- **Navigasi Soal Interaktif**: Panel nomor soal dengan warna indikator status (Sudah Dijawab, Belum Dijawab, Ragu-ragu).
- **Penilaian Auto-Grading & Manual Essay**: Koreksi otomatis soal Pilihan Ganda dan antarmuka penilaian manual untuk jawaban Essay + Catatan Ulasan Guru.

---

### 6. LMS (Learning Management System) & Gamifikasi Pembelajaran
- **Struktur Bab & Topik Pembelajaran (Chapters & Topics)**: Pengelompokan materi pelajaran berdasarkan Bab, Topik, dan Urutan Pembelajaran.
- **Modul Bahan Ajar (Materials)**: Distribusi berkas materi (PDF, Excel, Word, PPT, Video, Link External).
- **Modul Tugas & Pengumpulan (Assignments & Submissions)**: Pengungahan tugas oleh guru, batas waktu (Deadline), pengumpulan berkas oleh siswa, serta pemberian nilai dan ulasan guru.
- **Interaktif Topik Kuis (Topic Quizzes)**: Kuis singkat di akhir setiap topik pembelajaran untuk menguji pemahaman siswa.
- **Live Class Meeting Stream**: Integrasi tautan & status sesi tatap muka online / video conference per topik.
- **LMS Gamification & Progress Tracking**:
  - Perolehan XP (Experience Points) siswa setiap menyelesaikan topik/tugas.
  - Peraihan Lencana & Lencana Prestasi (Badges & Achievements).
  - Papan Peringkat LMS (Leaderboard) per kelas & tingkat sekolah.
  - Progress Bar kelengkapan pembelajaran per siswa.

---

### 7. e-Raport Digital, Sistem Penilaian & Ekspor-Impor Excel
- **Transkrip Nilai Akademik Terpadu**: Rekapitulasi nilai harian, nilai tugas, nilai kuis, nilai UTS, dan nilai UAS.
- **Fitur Impor & Ekspor Nilai via Excel**: Modul impor nilai massal per mata pelajaran menggunakan Template Excel baku.
- **Entri Nilai Massal (Bulk Grading)**: Antarmuka penginputan nilai cepat per kelas/mata pelajaran.
- **Modul e-Raport Digital**:
  - Generasi lembar Raport Hasil Belajar Siswa (Format Cetak PDF Ready).
  - Pengaturan Template Raport (Header, Tanggal Terbit, Tanda Tangan Kepala Sekolah & Wali Kelas).
  - Kalkulasi Predikat KKM & Catatan Akademik Wali Kelas otomatis.

---

### 8. Modul Bimbingan & Konseling (BK / Guidance & Counseling)
- **Menu Terdedikasi Guru BK**: Akses terpisah khusus pengelola layanan Bimbingan Konseling dan Kedisiplinan Siswa.
- **Pencatatan Pelanggaran & Poin Kedisiplinan**: Pencatatan kasus pelanggaran tata tertib, akumulasi bobot poin per siswa (Kategori: Ringan, Sedang, Berat, Sangat Berat), kronologi kejadian, dan sanksi.
- **Penerbitan Surat Peringatan (SP)**: Pengelolaan tingkat sanksi (Pending, Diproses BK, SP-1, SP-2, SP-3 / Skorsing, dan Resolved / Tuntas).
- **Berita Acara Pelanggaran (BAP) Generator**: Pembuatan lembar dokumen Berita Acara Pelanggaran (BAP) resmi beserta fitur cetak BAP dan unggah bukti fisik.
- **Pencatatan Sesi Konseling Siswa**: Dokumentasi bimbingan (Konseling Individu, Kelompok, Karir, Akademik, Perilaku, Solusi, dan Tindak Lanjut).
- **Asesmen Minat Bakat & Bimbingan Karir**: Pemetaan potensi siswa dan pengelompokan rekomendasi jurusan/studi lanjut.

---

### 9. Manajemen Keuangan Sekolah, POS SPP & Dynamic Rupiah Input
- **Master Pos Biaya & Tagihan**: Pengaturan pos pembayaran (SPP Bulanan, Uang Gedung, Seragam, Ujian, Kegiatan, dll).
- **Distribusi Tagihan Massal**: Pembagian tagihan otomatis ke seluruh siswa berdasarkan kelas atau angkatan.
- **POS Kasir Pembayaran Sekolah**:
  - Antarmuka kasir pembayaran SPP & tagihan sekolah oleh Bendahara/TU.
  - **Form Input Rupiah Cerdas**: Input nominal uang otomatis terformat Rupiah dengan pengubah kalimat **Terbilang** otomatis.
  - Opsi pembayaran Tunai, Transfer Bank, atau Potong Saldo Tabungan Siswa (E-Wallet).
- **Verifikasi Transfer Bank Manual**: Proses persetujuan (Approve/Reject) bukti bayar yang diunggah oleh Orang Tua/Siswa.
- **Cetak Kwitansi & Riwayat Pembayaran**: Cetak kwitansi pembayaran resmi per transaksi dan cetak laporan riwayat pembayaran siswa.
- **Pencatatan Arus Kas (Income & Expense)**: Pencatatan Pemasukan dan Pengeluaran operasional sekolah di luar SPP + Laporan Neraca Keuangan & Pembayaran SPP.

---

### 10. Modul Tabungan Siswa & Dompet Digital (Student Savings / E-Wallet)
- **Manajemen Tabungan Siswa**: Fitur Setor Tunai dan Tarik Tunai oleh Bendahara/Admin.
- **Setoran Deposit Online**: Unggah bukti transfer deposit tabungan via bank oleh siswa.
- **Verifikasi Deposit Online**: Approval / Rejection deposit online oleh Bendahara.
- **Buku Tabungan Digital & Cetak Mutasi**: Cetak lembar Buku Tabungan resmi dan laporan riwayat mutasi transaksi tabungan.
- **Integrasi Dompet Digital (E-Wallet)**: Saldo tabungan terhubung langsung sebagai metode pembayaran belanja makanan di Kantin Digital dan pembayaran biaya SPP/sekolah.

---

### 11. E-Kantin Digital (Vendor Portal, POS Kasir, Marketplace Siswa & Withdrawals)
- **Portal Stand Kantin / Vendor Mobile Portal**:
  - Sakelar Status Stand Buka / Tutup real-time.
  - **Manajemen Katalog Produk**: Pengelolaan item makanan & minuman (Foto Produk, Nama, Harga, Stok, Deskripsi).
  - **Manajemen Kategori & Stok**: Pengelompokan menu & fitur *Quick Update* / *Batch Stock Update* stok harian.
  - **Kasir POS Vendor**: Pencarian siswa (NISN/Nama), checkout barang, dan status pesanan real-time.
  - **Scan & Verifikasi QR Code**: Validasi QR Code pesanan / kupon makanan siswa.
  - **Saldo Vendor & Pencairan Dana (Withdrawal)**: Pencatatan saldo hasil penjualan vendor dan pengajuan penarikan dana ke rekening bank.
  - **Laporan Penjualan Vendor**: Grafik omzet, cetak laporan penjualan, dan ekspor data CSV.
- **Portal Kantin Siswa (Mobile Marketplace)**:
  - Mobile-first marketplace untuk jelajah menu per stand kantin.
  - Pemesanan online dari smartphone siswa & pembayaran via Saldo Tabungan (E-Wallet), Tunai, atau QR Code.
  - Status Pesanan Real-time (Menunggu, Diproses, Siap Diambil, Selesai).
- **Panel Admin Kantin Sekolah**:
  - Monitoring seluruh transaksi kantin sekolah.
  - Manajemen stand vendor dan menu kantin.
  - Persetujuan & Penolakan Pencairan Saldo Vendor (*Vendor Withdrawals*).

---

### 12. Portal Orang Tua (Parent Monitoring Portal 4-Tab & Instant Login)
- **Login Instan Tanpa Password**: Akses cepat menggunakan kombinasi NISN + Tanggal Lahir Siswa + Kode OTP WhatsApp.
- **Dashboard Monitoring Real-Time 4-Tab**:
  - **Tab Presensi Kehadiran**: Rekapitulasi statistik presensi (Hadir, Terlambat, Izin/Sakit, Alpa) dan riwayat jam masuk harian.
  - **Tab Nilai Akademis**: Transkrip perolehan nilai mata pelajaran, rata-rata nilai, nilai tertinggi, dan catatan wali kelas/guru.
  - **Tab Tagihan SPP & Keuangan**: Status pembayaran tagihan, sisa pembayaran, dan **Rincian Bulanan (Monthly Breakdown)**.
  - **Tab Jadwal & Pengumuman**: Jadwal KBM hari ini dan pengumuman resmi sekolah.
- **KTS Digital & Layanan Kontak WA**: Akses Kartu Tanda Siswa anak dan tombol whatsapp direct ke pihak sekolah.

---

### 13. Portal Siswa Mobile-First & LMS Interface
- **Desain Mobile-First Glassmorphic**: Navigasi bawah (*Bottom Navigation Bar*) khusus smartphone (Beranda, Jadwal, LMS, Tugas, Nilai, Profil).
- **Ringkasan Widget Dashboard**: NISN, statistik persentase kehadiran, saldo tabungan e-wallet, dan rata-rata nilai akademik.
- **Akses Bahan Ajar, Tugas, & Kuis**: Unduh materi pembelajaran, kumpulkan tugas online, serta kerjakan kuis & ujian CBT langsung dari smartphone.

---

### 14. Penerimaan Murid Baru (SPMB / PPDB Online) & Konversi Akun Otomatis
- **Manajemen Gelombang Pendaftaran**: Kuota pendaftar, tanggal buka/tutup, biaya pendaftaran, dan visual progress bar sisa kuota.
- **Formulir Pendaftaran Multi-Step**: Registrasi akun calon siswa, data diri, data orang tua, sekolah asal, dan unggah berkas pendaftaran.
- **Verifikasi Berkas & Pembayaran Admin**: Seleksi status pendaftar (Draft, Verified, Accepted, Rejected + Catatan) & verifikasi bayar pendaftaran.
- **Portal Calon Siswa (SPMB Dashboard)**: Pengumuman hasil seleksi status pendaftaran, cetak Bukti Pendaftaran PDF, serta pembaruan data diri.
- **Konversi Akun Otomatis**: Calon Siswa berstatus *Accepted* otomatis dikonversi menjadi akun Siswa aktif dan ditempatkan ke kelas tujuan.
- **Admin Testing Mode**: Sakelar simulasi pendaftaran SPMB untuk pengujian Admin.

---

### 15. Perpustakaan Digital (E-Book & Buku Fisik)
- **Katalog Koleksi Perpustakaan**: Data buku (Judul, Pengarang, Penerbit, ISBN, Tahun Terbit, Kategori, Sampul Buku, dan Stok Fisik).
- **Built-in E-Book PDF Reader**: Membaca buku digital PDF langsung di browser tanpa perlu mengunduh berkas.
- **Pencarian & Filter Cepat**: Pencarian koleksi berdasarkan judul, penulis, atau kategori buku.

---

### 16. Manajemen Akademik, Kurikulum & Ekstrakurikuler
- **Tahun Akademik, Kelas & Jurusan**: Pengelolaan tahun pelajaran aktif, data kelas, dan jurusan/program keahlian (SD, SMP, SMA, SMK).
- **Manajemen Jadwal Pelajaran**: Pengaturan jadwal KBM (Hari, Jam Mulai/Selesai, Ruangan, Mata Pelajaran, Guru Pengampu).
- **Kurikulum & Capaian Pembelajaran**: Pengelolaan deskripsi program kurikulum sekolah dan indikator capaian.
- **Manajemen Ekstrakurikuler**: Pengelolaan kegiatan ekstrakurikuler (Foto Kegiatan, Pembina, Hari/Jam Latihan, dan Status Aktif).

---

### 17. Integrasi WhatsApp Gateway & Broadcast Manager
- **Multi-Provider Service**: Support gateway WhatsApp populer dan Custom HTTP API dengan sakelar aktivasi instan & fitur *Send Test WA*.
- **Notifikasi Presensi Instant**: Kirim pesan WhatsApp otomatis ke Orang Tua saat presensi masuk/pulang.
- **Verifikasi OTP WhatsApp**: Pengiriman kode OTP 6-digit via WhatsApp untuk otentikasi login Parent Portal & Admin Security.
- **WA Broadcast Manager**: Fitur pesan pengumuman massal ke kelompok Siswa, Orang Tua, atau Guru dilengkapi log pengiriman.

---

### 18. Website Utama (CMS), Berita SEO & Galeri Sekolah
- **Landing Page Modern**: Halaman depan sekolah responsif, interaktif, dan SEO friendly.
- **Hero Banner Slider**: Pengelolaan banner promosi dan kegiatan utama.
- **Modul Berita & Blog SEO**: Artikel berita sekolah dengan Kategori, Tag, Gambar Sampul, dan URL Slug SEO.
- **Galeri Dokumentasi**: Album foto kegiatan dan fasilitas sekolah.
- **Profil Sekolah Lengkap**: Visi Misi, Sambutan Kepala Sekolah, Program Keahlian, Agenda Kegiatan, Google Maps, dan Media Sosial.

---

### 19. Pengaturan Sistem, Database Maintenance & Web Installer
- **Identitas Sekolah**: Nama Sekolah, NPSN, Alamat, Telepon, Email, Logo Sekolah, dan Favicon.
- **Konfigurasi Email SMTP**: Server mailer SMTP untuk pengiriman email notifikasi + Uji coba pengiriman email.
- **Database Maintenance & Backup**:
  - Backup database otomatis atau manual.
  - Download & Hapus berkas backup database.
  - Pembaruan struktur database secara otomatis dari Panel Admin.
- **Web Installer / Setup Wizard**: Wizard setup interaktif untuk verifikasi spesifikasi server, koneksi database, dan seeder awal.
- **Sistem Lisensi & Proteksi Fitur**: Pengelolaan status lisensi aplikasi sekolah.

---