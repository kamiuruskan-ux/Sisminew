# Sistem Website & Management Sekolah Profesional (SIAKAD & LMS)

Platform **Sistem Informasi Manajemen Sekolah (SIMS)** profesional terpadu berbasis **Laravel 13** yang dirancang untuk mendukung operasional lengkap sekolah (SD, SMP, SMA, SMK). Sistem ini menggabungkan Website Profil Publik (CMS), Portal Akademik Siswa (*Mobile-First*), Portal Orang Tua (Parent Monitoring), Sistem Presensi QR Code, Ujian Online (CBT), Perpustakaan Digital (E-Library), Manajemen Keuangan & SPP, serta Penerimaan Murid Baru (SPMB Online).

---

## Fitur Utama Sistem

### 1. Multi-Role System & RBAC (Role-Based Access Control)
Sistem memiliki pengontrol hak akses yang sangat fleksibel dan aman:
- **Super Admin**: Akses penuh manajemen user, role, permission dinamis, dan konfigurasi sistem.
- **Admin / Tata Usaha**: Pengelolaan data master (Siswa, Guru, Kelas, Jurusan, Tahun Ajaran), keuangan, dan verifikasi SPMB.
- **Guru**: Manajemen jadwal KBM, pembuatan kuis/ujian CBT, upload materi, pemberian tugas, koreksi jawaban, input nilai massal (*Bulk Grading*), dan pengumuman.
- **Bendahara**: Pengelolaan pos pembayaran, penerimaan SPP, cetak kwitansi, pencatatan transaksi masuk/keluar, dan laporan keuangan.
- **Siswa**: Dashboard responsif mobile-first, Kartu Pelajar Digital + Vektor SVG QR Code, CBT Ujian Online, E-Library PDF, Tugas, dan Rapor Nilai.
- **Orang Tua / Wali Siswa**: Portal khusus pemantauan presensi harian, nilai akademis, dan kewajiban SPP anak.
- **Calon Siswa**: Pendaftaran akun SPMB, pengisian formulir multi-step, unggah berkas persetujuan, dan cek status kelulusan.

---

### 2. Digital Student ID Card & Presensi QR Code
- **Kartu Pelajar Digital High-Tech**: Kartu identitas digital siswa dengan Kop Sekolah, NISN, foto profil, kelas, jurusan, dan status aktif.
- **Vektor SVG QR Code**: Kode QR dihasilkan menggunakan facade `QrCode` dengan format Vektor SVG beresolusi tinggi (*crisp & sharp*) berbasis **NISN Siswa**.
- **Modal Pop-Up QR Code**: Tampilan perbesar QR Code yang modern, bersih, dan dilengkapi caption nomor NISN di bawahnya.
- **Scanner Presensi Instant**: Pemindaian QR Code presensi terpisah ([`QrAttendanceController.php`](file:///c:/wamp64/www/sekolah_lrv/system/app/Http/Controllers/QrAttendanceController.php)) yang mendukung kamera perangkat maupun *handheld barcode scanner*.
- **Integrasi WhatsApp Gateway**: Notifikasi otomatis dikirimkan ke nomor HP Orang Tua saat siswa berhasil memindai QR presensi.

---

### 3. Portal Orang Tua (Parent Monitoring Portal)
Modul khusus bagi Orang Tua/Wali siswa untuk memantau aktivitas anak secara *real-time*:
- **Login Bebas Repot**: Akses masuk menggunakan **NISN Siswa** dan **Tanggal Lahir Siswa** tanpa menghafal password (`/parent/login`).
- **Monitoring Kehadiran Real-time**: Rekapitulasi statistik kehadiran (Hadir, Terlambat, Sakit, Izin, Alpa) dan jam masuk siswa.
- **Status SPP & Keuangan**: Transparansi tagihan terbayar, rincian tunggakan, dan tanggal jatuh tempo.
- **Transkrip Nilai Akademis**: Rata-rata nilai rapor dan daftar nilai per mata pelajaran.
- **Kontak WhatsApp Wali Kelas**: Tombol cepat untuk menghubungi pihak sekolah atau wali kelas via WhatsApp.

---

### 4. Modul Ujian Online CBT (Computer Based Test)
Sistem kuis dan ujian online untuk pengerjaan soal berbasis komputer maupun ponsel:
- **Bank Soal Pilihan Ganda**: Dukungan pilihan A, B, C, D, E, kunci jawaban, dan pembobotan skor per soal.
- **Ruang Ujian Live Countdown Timer**: Penguji hitung mundur waktu otomatis yang langsung mengumpulkan jawaban jika waktu habis.
- **Navigasi Soal Responsif**: Indikator nomor soal aktif, soal sudah terisi, dan tombol konfirmasi pengumpulkan.
- **Penilaian Instan**: Kalkulasi nilai ujian otomatis langsung setelah siswa mengumpulkan ujian.

---

### 5. Modul Perpustakaan Digital (Digital E-Library)
Modul katalog buku digital dan fisik sekolah:
- **Katalog E-Book**: Pencarian buku berdasarkan judul, penulis, ISBN, dan filter kategori (Pelajaran, Novel, Sains, Agama, dll).
- **Pembaca E-Book PDF Browser**: Membaca berkas modul / e-book PDF langsung di browser tanpa aplikasi tambahan.
- **Informasi Stok Fisik**: Pemantauan jumlah fisik buku yang siap dipinjam di perpustakaan sekolah.

---

### 6. Modul Keuangan & SPP Sekolah (Financial Management)
- **Pos Pembayaran (`PaymentPost`)**: Pengaturan kategori biaya (SPP Bulanan, Uang Gedung, Seragam, Ujian).
- **Tagihan Pembayaran (`PaymentBill`)**: Distibusi tagihan otomatis ke seluruh siswa per kelas / angkatan.
- **Proses Pembayaran & Kwitansi (`StudentPayment`)**: Pembayaran tunai/bank transfer, tracking histori pembayaran, dan cetak kwitansi resmi.
- **Manajemen Rekening Bank (`BankAccount`)**: Pengelolaan nomor rekening sekolah untuk penerimaan transfer.
- **Arus Kas & Laporan Keuangan (`FinancialTransaction` & `FinancialReport`)**: Pencatatan Pemasukan & Pengeluaran serta cetak Laporan Keuangan Bulanan/Tahunan.

---

### 7. Sistem SPMB (Pendaftaran Murid Baru / PPDB Online)
- **Manajemen Gelombang (`Wave`)**: Batas kuota pendaftar dan tanggal buka/tutup gelombang.
- **Formulir Registrasi Multi-Step**: Data diri pendaftar, data orang tua/wali, dan unggah berkas (KK, Ijazah/SKL, Akta, Pas Foto).
- **Verifikasi & Keputusan Seleksi**: Verifikasi berkas oleh admin (*Draft*, *Verified*, *Accepted*, *Rejected* + Catatan).
- **Auto Convert Candidate**: Calon siswa yang dinyatakan *Accepted* otomatis dikonversi menjadi akun **Siswa** aktif dan didaftarkan ke kelas.
- **Cetak Bukti Pendaftaran**: Cetak kartu formulir bukti pendaftaran SPMB.

---

### 8. Modul Akademik & E-Learning
- **Jadwal Pelajaran (`Schedule`)**: Manajemen jadwal KBM harian per kelas dan hari.
- **Materi & Modul (`Material`)**: Upload berkas bahan ajar guru dan fitur unduh siswa.
- **Tugas & Pengumpulan (`Assignment` & `Submission`)**: Pemberian tugas, deadline, unggah file jawaban siswa, dan koreksi nilai guru.
- **Input Nilai Massal (`Bulk Grade`)**: Fitur entri nilai kelas secara sekaligus untuk menghemat waktu administrasi guru.
- **Pengumuman Sekolah (`Announcement`)**: Publikasi pengumuman internal sekolah.

---

### 9. Dashboard Siswa Mobile-First Layout
- **Desain Modern & Glassmorphic**: Kartu identitas, statistik ringkasan header 3 kolom sejajar, dan skema warna cyan/indigo.
- **Menu Bottom Nav**: Navigasi bawah ponsel (Beranda, Jadwal, Tugas, Nilai, Profil) dan sidebar desktop lengkap.

---

### 10. Landing Page CMS Profil Sekolah (Public Site)
- **Hero Banner Slider**: Spanduk ucapan/promosi geser dinamis.
- **Identitas & Sambutan Kepala Sekolah**: Visi, misi, sejarah, sambutan kepala sekolah, dan pilar kurikulum.
- **Program Keahlian / Jurusan (`Major`)**: Menampilkan jurusan aktif (SMA/SMK) yang otomatis beradaptasi jika sekolah diatur sebagai SD/SMP.
- **Berita, Blog, Kategori & Tag**: Artikel berita teroptimasi SEO URL dengan fitur pencarian.
- **Galeri Foto & Album (`Gallery`)**: Dokumentasi album kegiatan sekolah.
- **Kontak & Peta**: Peta Google Maps interaktif, alamat, email, telepon, dan media sosial resmi.

---

## Spesifikasi Teknologi (Tech Stack)

Sistem ini dibangun menggunakan kombinasi teknologi modern, handal, dan berkinerja tinggi:

### 1. Backend & Framework
- **Framework**: [Laravel 13.x](https://laravel.com/) (Framework PHP Modern)
- **Bahasa Pemrograman**: PHP `>= 8.3` (Dukungan penuh PHP 8.3 & PHP 8.4)
- **Database Engine**: MySQL / MariaDB (Menggunakan Eloquent ORM & Relational Schema)

### 2. Package PHP & Library (Composer)
- **`simplesoftwareio/simple-qrcode` (^4.2)**: Generasi QR Code format Vektor SVG & PNG beresolusi tinggi (digunakan untuk Kartu Pelajar Digital & Pemindai Presensi).
- **`phpoffice/phpspreadsheet` (^5.9)**: Pengolahan, ekspor, dan impor data laporan format Excel (`.xlsx`, `.xls`) dan CSV.
- **`vinkla/hashids`**: Hashing & enkripsi ID unik untuk perlindungan parameter URL aplikasi.
- **`laravel/tinker` (^3.0)**: Console REPL interaktif Laravel untuk debugging data.
- **`fakerphp/faker`**: Generator data dummy untuk seeder database.
- **`laravel/pint`**: Code style formatter standar PHP Laravel.
- **`phpunit/phpunit` (^12.0)**: Framework pengujian otomatis (*Automated Unit & Feature Testing*).

### 3. Frontend & User Interface (UI Stack)
- **CSS Framework**: [Tailwind CSS v4.0](https://tailwindcss.com/) (menggunakan `@tailwindcss/vite` & konfigurasi tema kustom *Dark Mode*, *Primary/Secondary Colors*).
- **Reaktivitas UI & Script**: [Alpine.js v3.x](https://alpinejs.dev/) + Plugin `@alpinejs/collapse` untuk komponen interaktif yang cepat & ringan.
- **Komponen & Plugin UI Client-Side**:
  - **Tom Select (v2.3.1 / v2.5.2)**: Plugin dropdown pencarian siswa & guru interaktif (`<x-student-select-search>`, `<x-teacher-select-search>`).
  - **Chart.js**: Visualisasi grafik analitik & statistik (Dashboard Admin, Analitik LMS, Laporan Kantin).
  - **HTML5-QRCode (v2.3.8) & jsQR**: Pemindai QR Code berbasis kamera real-time & *handheld barcode scanner* (Presensi Guru/Siswa & POS Kantin).
  - **AOS (Animate On Scroll v2.3.1)**: Efek animasi scroll pada Landing Page Publik.
  - **Lucide Icons & Heroicons**: Set ikon vektor SVG modern.
  - **Axios (^1.11.0)**: Client HTTP AJAX request.

### 4. Build Tools & Utilities Pengembang
- **Bundler Asset**: [Vite v7.0](https://vitejs.dev/) + `laravel-vite-plugin v2.0` & `@tailwindcss/vite v4.0`.
- **Node.js & NPM**: Pengelolaan dependensi modul JavaScript.
- **Concurrently (^9.0.1)**: Manajemen eksekusi skrip pengembang secara paralel (`php artisan serve`, `queue:listen`, `pail`, `vite`).

---

## Persyaratan Sistem & Instalasi

### Persyaratan Environment:
- **PHP**: `>= 8.3` (atau PHP 8.4)
- **Database**: MySQL / MariaDB
- **Web Server**: Apache / Nginx (WAMP / XAMPP / Laragon)
- **Composer**: `>= 2.0`
- **PHP Extensions**: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd` (untuk QR code & image processing).

### Langkah-Langkah Instalasi:

1. **Masuk ke Direktori Proyek**:
   ```bash
   cd c:/wamp64/www/sekolah_lrv/system
   ```

2. **Instal Dependensi PHP (Composer)**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**:
   Duplikat file `.env.example` menjadi `.env` dan atur kredensial database Anda:
   ```env
   APP_NAME="Sekolah LRV"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost/sekolah_lrv/system/public

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sekolah_lrv
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Database Migration & Seeder**:
   ```bash
   php artisan migrate --seed
   ```

6. **Struktur Penyimpanan Berkas (Direct Public Upload)**:
   Aplikasi ini **tidak memerlukan `php artisan storage:link`** karena semua berkas unggahan (foto, avatar, gambar, dokumen PDF, dan spreadsheet) disimpan langsung di folder public `img/` dan `doc/` agar kompatibel 100% dengan lingkungan WAMP dan hosting cPanel.

7. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi melalui alamat: `http://127.0.0.1:8000`

---

## Struktur Direktori Proyek

```
sekolah_lrv/
├── FITUR.md                  # Dokumentasi Fitur Aplikasi
├── README.md                 # Dokumentasi Utama Proyek
└── system/
    ├── app/
    │   ├── Http/Controllers/
    │   │   ├── Admin/        # Controller Panel Admin & Manajemen Sekolah
    │   │   ├── Student/      # Controller Portal Siswa (CBT, E-Library, Tugas, Profile)
    │   │   ├── Parent/       # Controller Portal Orang Tua (Login & Monitoring)
    │   │   ├── Spmb/         # Controller SPMB Online & Registrasi
    │   │   ├── Auth/         # Controller Autentikasi Login & Reset Password
    │   │   ├── Blog/         # Controller Artikel Publik
    │   │   ├── Landing/      # Controller Landing Page Publik
    │   │   └── QrAttendanceController.php # Controller Scanner Presensi QR Code
    │   └── Models/           # Models (User, Student, Exam, Book, Attendance, PaymentBill, dll)
    ├── database/
    │   ├── migrations/       # Skema Migrasi Database Relasional
    │   └── seeders/          # Seeder Data Master & Hak Akses
    ├── resources/views/
    │   ├── admin/            # Tampilan Panel Admin
    │   ├── student/          # Tampilan Portal Siswa Mobile-First
    │   ├── parent/           # Tampilan Portal Orang Tua
    │   ├── spmb/             # Tampilan Registrasi & Dashboard SPMB
    │   ├── landing/          # Tampilan Website Publik
    │   └── layouts/          # Layout Utama (Student Mobile, Admin, Landing)
    └── routes/
        └── web.php           # Seluruh Route Aplikasi
```

---

## Hak Cipta & Lisensi Aplikasi

**Hak Cipta (c) Developer. Hak Cipta Dilindungi Undang-Undang.**

---

### 1. Ketentuan Lisensi Penggunaan (Software License Agreement)
1. **Lisensi Resmi Pengembang (Commercial / Proprietary License)**: Hak guna dan akses perangkat lunak ini diberikan secara eksklusif kepada sekolah / lembaga pendidikan yang telah memiliki lisensi resmi dari pengembang.
2. **Larangan Jual-Beli & Redistribusi**: Dilarang keras memperjualbelikan, menyewakan, mendistribusikan ulang, mengunggah ke forum/marketplace/platform publik, atau mengkomersialkan ulang seluruh atau sebagian kode sumber (*source code*), skema database, dan sistem ini dalam bentuk apa pun tanpa izin tertulis resmi dari pengembang.
3. **Perlindungan Hak Cipta & System Protection**: Dilarang membongkar (*decompile*), meretas, menghapus, atau memodifikasi modul proteksi lisensi dan identitas hak cipta yang ada di dalam aplikasi.

---

### 2. Aktivasi Lisensi Aplikasi (Domain-Bound Lifetime)
- **Terikat Domain Target (Domain-Bound)**: Setiap kunci lisensi dikunci secara khusus untuk nama domain aktif sekolah (*contoh*: `sekolah.sch.id` atau `localhost` untuk server pengujian).
- **Masa Berlaku Permanen (Lifetime)**: Lisensi berlaku secara permanen (**Lifetime**) tanpa ada biaya perpanjangan bulanan atau tahunan.
- **Prosedur Aktivasi**: Apabila aplikasi dipasang pada domain baru atau lisensi belum aktif, sistem akan mengarahkan ke halaman aktivasi (`/license/activate`). Masukkan **Kunci Lisensi Resmi** yang diberikan oleh pengembang untuk mengaktifkan seluruh fitur sistem secara penuh.


