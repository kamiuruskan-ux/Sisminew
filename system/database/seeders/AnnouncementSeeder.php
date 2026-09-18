<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user as author
        $adminUser = User::role('admin')->first() ?? User::role('super-admin')->first() ?? User::first();

        if (!$adminUser) {
            $this->command->error('No admin user found. Please run UserSeeder first.');
            return;
        }

        Announcement::truncate();

        $currentYear = date('Y');
        $nextYear = $currentYear + 1;

        $announcements = [
            [
                'title' => "Selamat Datang di Tahun Ajaran Baru {$currentYear}/{$nextYear}",
                'content' => "Kami mengucapkan selamat datang kepada seluruh siswa di tahun ajaran {$currentYear}/{$nextYear}. Semoga dapat belajar dengan baik dan meraih prestasi yang gemilang. Mari bersama-sama membangun semangat belajar untuk masa depan yang lebih baik.",
                'author_id' => $adminUser->id,
                'type' => 'general',
                'is_published' => true,
                'published_at' => now()->subDays(10),
                'expires_at' => null,
            ],
            [
                'title' => 'Jadwal Ujian Tengah Semester Ganjil',
                'content' => 'Ujian Tengah Semester (UTS) Ganjil akan dilaksanakan pada bulan depan. Harap siswa-siswi mempersiapkan diri dengan baik dan mempelajari materi yang telah diajarkan. Jadwal lengkap dapat diakses pada menu Ujian CBT.',
                'author_id' => $adminUser->id,
                'type' => 'academic',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'expires_at' => now()->addDays(20),
            ],
            [
                'title' => 'Penerimaan Siswa Baru Gelombang 2',
                'content' => "Sekolah kami membuka pendaftaran siswa baru gelombang 2 untuk tahun ajaran {$currentYear}/{$nextYear}. Pendaftaran masih dibuka. Informasi lebih lanjut dapat diakses melalui menu PPDB / SPMB atau menghubungi panitia pendaftaran.",
                'author_id' => $adminUser->id,
                'type' => 'general',
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'expires_at' => now()->addDays(30),
            ],
            [
                'title' => 'Kegiatan Ekstrakurikuler Dimulai',
                'content' => 'Kegiatan ekstrakurikuler untuk semester ini akan dimulai pada minggu depan. Siswa-siswi diharapkan dapat memilih dan mendaftar minimal 1 ekstrakurikuler sesuai minat dan bakat.',
                'author_id' => $adminUser->id,
                'type' => 'event',
                'is_published' => true,
                'published_at' => now()->subDay(2),
                'expires_at' => now()->addDays(10),
            ],
            [
                'title' => 'Libur Hari Raya Nasional',
                'content' => 'Sehubungan dengan perayaan Hari Raya Nasional, sekolah akan meliburkan kegiatan belajar mengajar pada tanggal yang ditetapkan pemerintah. Kegiatan pembelajaran akan dilanjutkan seperti biasa pada hari kerja berikutnya.',
                'author_id' => $adminUser->id,
                'type' => 'general',
                'is_published' => false,
                'published_at' => null,
                'expires_at' => null,
            ],
            [
                'title' => 'Workshop Persiapan Ujian Nasional & SNBT',
                'content' => 'Bagi siswa kelas 12, sekolah akan mengadakan workshop persiapan Ujian dan Seleksi Masuk Perguruan Tinggi Negeri. Workshop ini membahas strategi belajar efektif dan latihan soal.',
                'author_id' => $adminUser->id,
                'type' => 'academic',
                'is_published' => true,
                'published_at' => now()->subHours(6),
                'expires_at' => now()->addDays(45),
            ],
            [
                'title' => 'Perubahan Jadwal Pelajaran (Mapel)',
                'content' => 'Mulai minggu depan, terdapat pembaruan jadwal pelajaran untuk mengoptimalkan jam belajar mengajar dan fasilitas laboratorium. Siswa diharapkan memeriksa menu Jadwal Pelajaran di dashboard.',
                'author_id' => $adminUser->id,
                'type' => 'urgent',
                'is_published' => true,
                'published_at' => now()->subHours(12),
                'expires_at' => now()->addDays(7),
            ],
            [
                'title' => 'Lomba Karya Ilmiah Remaja',
                'content' => 'Sekolah mengadakan Lomba Karya Ilmiah Remaja (LKIR) internal. Peserta yang menang akan mewakili sekolah dalam kompetisi tingkat kota. Segera konsultasikan ide penelitianmu dengan guru pembimbing!',
                'author_id' => $adminUser->id,
                'type' => 'event',
                'is_published' => true,
                'published_at' => now()->subDays(1),
                'expires_at' => now()->addDays(25),
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }

        $this->command->info('Announcement seeder completed successfully!');
    }
}
