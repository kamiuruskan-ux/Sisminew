<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::query()->delete();

        // Create Categories
        $categories = [
            ['name' => 'Akademik & Sains', 'slug' => 'akademik', 'color' => '#3B82F6'],
            ['name' => 'Kegiatan Sekolah', 'slug' => 'kegiatan', 'color' => '#10B981'],
            ['name' => 'Prestasi & Lomba', 'slug' => 'prestasi', 'color' => '#F59E0B'],
            ['name' => 'Pengumuman Resmi', 'slug' => 'pengumuman', 'color' => '#EF4444'],
            ['name' => 'Ekstrakurikuler', 'slug' => 'ekstrakurikuler', 'color' => '#8B5CF6'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }

        // Create Tags
        $tags = [
            ['name' => 'UN', 'slug' => 'un'],
            ['name' => 'UTBK', 'slug' => 'utbk'],
            ['name' => 'Olimpiade', 'slug' => 'olimpiade'],
            ['name' => 'Sport', 'slug' => 'sport'],
            ['name' => 'Seni', 'slug' => 'seni'],
            ['name' => 'Science', 'slug' => 'science'],
            ['name' => 'Literasi', 'slug' => 'literasi'],
            ['name' => 'Teknologi', 'slug' => 'teknologi'],
            ['name' => 'Keagamaan', 'slug' => 'keagamaan'],
            ['name' => 'Sosial', 'slug' => 'sosial'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }

        // Get admin user for author
        $admin = User::role('admin')->first() ?? User::first();

        $currentYear = date('Y');
        $nextYear = $currentYear + 1;

        // Create Posts with Real Unsplash Images
        $posts = [
            [
                'title' => "Penerimaan Peserta Didik Baru Tahun Ajaran {$currentYear}/{$nextYear}",
                'slug' => "penerimaan-peserta-didik-baru-tahun-ajaran-{$currentYear}-{$nextYear}",
                'excerpt' => "Informasi lengkap mengenai penerimaan siswa baru untuk tahun ajaran {$currentYear}/{$nextYear} telah dibuka. Segera daftarkan diri Anda!",
                'content' => "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\nDengan senang hati kami informasikan bahwa Sistem Penerimaan Murid Baru (SPMB) untuk Tahun Ajaran {$currentYear}/{$nextYear} telah resmi dibuka.\n\n**Tahapan Pendaftaran:**\n\n1. **Gelombang 1** (Jalur Utama)\n   - Biaya pendaftaran: Rp 150.000\n   - Kuota: 200 siswa\n\n2. **Gelombang 2** (Jalur Reguler)\n   - Biaya pendaftaran: Rp 200.000\n   - Kuota: 150 siswa\n\n**Persyaratan:**\n- Fotokopi Kartu Keluarga\n- Fotokopi Akta Kelahiran\n- Fotokopi Ijazah SD/SMP\n- Fotokopi NISN\n- Pas foto 3x4 (2 lembar)\n\nUntuk informasi lebih lanjut, silakan hubungi panitia PMB di nomor yang tersedia atau kunjungi sekolah kami.",
                'thumbnail' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1200&auto=format&fit=crop',
                'category_id' => 1,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'views' => 1250,
            ],
            [
                'title' => 'Siswa Kami Raih Medali Emas Olimpiade Sains Nasional',
                'slug' => 'siswa-kami-raih-medali-emas-olimpiade-sains-nasional',
                'excerpt' => 'Prestasi membanggakan! Siswa kami berhasil meraih medali emas dalam Olimpiade Sains Nasional tingkat SMA.',
                'content' => "Alhamdulillah, sebuah kebanggaan bagi sekolah kami!\n\n**Ahmad Rizki**, siswa kelas XI IPA 1, berhasil meraih **Medali Emas** dalam Olimpiade Sains Nasional (OSN) bidang Fisika yang diselenggarakan di Jakarta.\n\nAtas prestasinya ini, Ahmad menerima trofi, piagam penghargaan, dan beasiswa pendidikan.",
                'thumbnail' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=1200&auto=format&fit=crop',
                'category_id' => 3,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'views' => 890,
            ],
            [
                'title' => "Kegiatan Class Meeting & Turnamen Olahraga Semester Ganjil",
                'slug' => "kegiatan-class-meeting-semester-ganjil-{$currentYear}",
                'excerpt' => 'Seru! Berbagai lomba diadakan dalam kegiatan class meeting untuk mengisi waktu setelah ujian semester.',
                'content' => "Kegiatan Class Meeting telah dilaksanakan dengan meriah! Diisi dengan turnamen futsal, basket, badminton, serta kompetisi seni antar tingkat kelas.",
                'thumbnail' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=1200&auto=format&fit=crop',
                'category_id' => 2,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'views' => 654,
            ],
            [
                'title' => 'Jadwal Ujian Tengah Semester Genap & Persiapan CBT',
                'slug' => 'jadwal-ujian-tengah-semester-genap',
                'excerpt' => 'Diberitahukan kepada seluruh siswa bahwa Ujian Tengah Semester Genap berbasis CBT akan dilaksanakan sesuai jadwal.',
                'content' => "Diberitahukan bahwa **Ujian Tengah Semester (UTS) Genap** akan dilaksanakan sesuai jadwal akademik resmi melalui platform CBT Sekolah.",
                'thumbnail' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1200&auto=format&fit=crop',
                'category_id' => 4,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(14),
                'views' => 2100,
            ],
            [
                'title' => 'Ekstrakurikuler Pramuka Gelar Perkemahan Sabtu Minggu',
                'slug' => 'ekstrakurikuler-pramuka-gelar-perkemahan-sabtu-minggu',
                'excerpt' => 'Anggota Pramuka sekolah kami mengadakan Perkemahan Sabtu Minggu (Persami) dengan berbagai kegiatan pembinaan karakter.',
                'content' => "Kegiatan Perkemahan Sabtu Minggu (Persami) telah dilaksanakan dengan sukses! Meliputi pendirian tenda, malam api unggun, dan latihan navigasi darat.",
                'thumbnail' => 'https://images.unsplash.com/photo-1517649763962-0c623266010b?q=80&w=1200&auto=format&fit=crop',
                'category_id' => 5,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(20),
                'views' => 432,
            ],
            [
                'title' => 'Workshop Penggunaan Teknologi AI dalam Pembelajaran',
                'slug' => 'workshop-penggunaan-teknologi-dalam-pembelajaran',
                'excerpt' => 'Guru-guru mengikuti workshop untuk meningkatkan kompetensi dalam memanfaatkan platform pembelajaran digital.',
                'content' => "Sekolah kami mengadakan workshop peningkatan kompetensi guru dalam menggunakan media pembelajaran berbasis teknologi digital modern.",
                'thumbnail' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1200&auto=format&fit=crop',
                'category_id' => 1,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(25),
                'views' => 321,
            ],
        ];

        foreach ($posts as $postData) {
            $post = Post::create($postData);
            
            // Attach random tags (2-4 tags per post)
            $tagIds = Tag::inRandomOrder()->limit(rand(2, 4))->pluck('id');
            $post->tags()->attach($tagIds);
        }
    }
}
