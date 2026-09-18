<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Gallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or ensure Gallery Categories exist
        $galleryCategories = [
            ['name' => 'Kegiatan Belajar & Sains', 'slug' => 'kegiatan-belajar', 'type' => 'gallery', 'color' => '#3B82F6'],
            ['name' => 'Olahraga & Kesehatan', 'slug' => 'olahraga', 'type' => 'gallery', 'color' => '#10B981'],
            ['name' => 'Seni, Musik & Budaya', 'slug' => 'seni-budaya', 'type' => 'gallery', 'color' => '#F59E0B'],
            ['name' => 'Upacara & Peringatan', 'slug' => 'upacara', 'type' => 'gallery', 'color' => '#EF4444'],
            ['name' => 'Study Tour & Kampus', 'slug' => 'study-tour', 'type' => 'gallery', 'color' => '#8B5CF6'],
            ['name' => 'Prestasi & Lomba', 'slug' => 'lomba', 'type' => 'gallery', 'color' => '#EC4899'],
        ];

        foreach ($galleryCategories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }

        // Get category IDs
        $kategoriBelajar = Category::where('slug', 'kegiatan-belajar')->first()->id ?? null;
        $olahraga = Category::where('slug', 'olahraga')->first()->id ?? null;
        $seniBudaya = Category::where('slug', 'seni-budaya')->first()->id ?? null;
        $upacara = Category::where('slug', 'upacara')->first()->id ?? null;
        $studyTour = Category::where('slug', 'study-tour')->first()->id ?? null;
        $lomba = Category::where('slug', 'lomba')->first()->id ?? null;

        // 2. Clear old gallery records if seeding fresh real data
        Gallery::truncate();

        // 3. Real High-Resolution Gallery Items with Unsplash URLs
        $galleries = [
            [
                'title' => 'Praktikum Sains & Eksperimen Kimia Lab Terpadu',
                'slug' => 'praktikum-sains-eksperimen-kimia-lab-terpadu',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $kategoriBelajar,
                'description' => 'Siswa-siswi jurusan IPA antusias melakukan reaksi kimia dan observasi praktikum di laboratorium sains sekolah.',
                'event_date' => now()->subDays(5),
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Diskusi Kelompok & Belajar Kolaboratif',
                'slug' => 'diskusi-kelompok-belajar-kolaboratif',
                'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $kategoriBelajar,
                'description' => 'Kegiatan belajar mengajar berbasis riset dan diskusi kelompok interaktif di kelas modern.',
                'event_date' => now()->subDays(8),
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Literasi Digital & Membaca di Perpustakaan',
                'slug' => 'literasi-digital-membaca-di-perpustakaan',
                'image' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $kategoriBelajar,
                'description' => 'Siswa menggunakan koleksi buku fisik dan e-library perpustakaan digital sekolah.',
                'event_date' => now()->subDays(12),
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Kejuaraan Basket Antar Sekolah Se-Provinsi',
                'slug' => 'kejuaraan-basket-antar-sekolah-se-provinsi',
                'image' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $olahraga,
                'description' => 'Tim basket putra sekolah berhasil mempertahankan gelar juara 1 pada laga final yang sengit.',
                'event_date' => now()->subDays(3),
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Laga Futsal Pekan Olahraga Antar Kelas',
                'slug' => 'laga-futsal-pekan-olahraga-antar-kelas',
                'image' => 'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $olahraga,
                'description' => 'Keseruan pertandingan futsal classmeeting antar tingkat kelas di lapangan indoor.',
                'event_date' => now()->subDays(15),
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Senam Kebugaran & Olahraga Bersama Guru-Siswa',
                'slug' => 'senam-kebugaran-olahraga-bersama',
                'image' => 'https://images.unsplash.com/photo-1517649763962-0c623266010b?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $olahraga,
                'description' => 'Kegiatan jumat sehat senam aerobik massal di halaman kampus utama.',
                'event_date' => now()->subDays(20),
                'order' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Pentas Seni & Festival Musik Kreasi Siswa',
                'slug' => 'pentas-seni-festival-musik-kreasi-siswa',
                'image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $seniBudaya,
                'description' => 'Pertunjukan megah band sekolah dan paduan suara pada puncak peringatan Dies Natalis.',
                'event_date' => now()->subDays(25),
                'order' => 7,
                'is_active' => true,
            ],
            [
                'title' => 'Pameran Karya Seni Rupa & Desain Grafis',
                'slug' => 'pameran-karya-seni-rupa-desain-grafis',
                'image' => 'https://images.unsplash.com/photo-1561214115-f2f134cc4912?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $seniBudaya,
                'description' => 'Pameran lukisan dan instalasi seni hasil karya orisinal para siswa.',
                'event_date' => now()->subDays(30),
                'order' => 8,
                'is_active' => true,
            ],
            [
                'title' => 'Pagelaran Tari Tradisional Nusantara',
                'slug' => 'pagelaran-tari-tradisional-nusantara',
                'image' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $seniBudaya,
                'description' => 'Pelestarian budaya melalui penampilan seni tari daerah nusantara oleh sanggar tari sekolah.',
                'event_date' => now()->subDays(35),
                'order' => 9,
                'is_active' => true,
            ],
            [
                'title' => 'Upacara Khidmat Bendera Peringatan Kemerdekaan',
                'slug' => 'upacara-khidmat-bendera-peringatan-kemerdekaan',
                'image' => 'https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $upacara,
                'description' => 'Pasukan Pengibar Bendera (Paskibra) sekolah memimpin upacara peringatan HUT Kemerdekaan RI.',
                'event_date' => now()->subDays(40),
                'order' => 10,
                'is_active' => true,
            ],
            [
                'title' => 'Peringatan Hari Guru Nasional & Penghargaan',
                'slug' => 'peringatan-hari-guru-nasional-penghargaan',
                'image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $upacara,
                'description' => 'Apresiasi dan kejutan indah dari para siswa untuk seluruh dewan guru pengajar.',
                'event_date' => now()->subDays(45),
                'order' => 11,
                'is_active' => true,
            ],
            [
                'title' => 'Kunjungan Edukasi Kampus & Study Tour',
                'slug' => 'kunjungan-edukasi-kampus-study-tour',
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $studyTour,
                'description' => 'Kegiatan kunjungan studi ilmiah siswa kelas XII ke universitas ternama.',
                'event_date' => now()->subDays(50),
                'order' => 12,
                'is_active' => true,
            ],
            [
                'title' => 'Olimpiade Sains & Kompetisi Robotika',
                'slug' => 'olimpiade-sains-kompetisi-robotika',
                'image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $lomba,
                'description' => 'Tim Robotika sekolah mendemonstrasikan rakitan robot cerdas di kompetisi teknologi.',
                'event_date' => now()->subDays(55),
                'order' => 13,
                'is_active' => true,
            ],
            [
                'title' => 'Praktikum Komputer & Pemrograman Web',
                'slug' => 'praktikum-komputer-pemrograman-web',
                'image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $kategoriBelajar,
                'description' => 'Siswa mendalami ilmu informatika, logika pemrograman, dan pembuatan aplikasi web.',
                'event_date' => now()->subDays(60),
                'order' => 14,
                'is_active' => true,
            ],
            [
                'title' => 'Juara 1 Lomba Debat Bahasa Inggris Nasional',
                'slug' => 'juara-1-lomba-debat-bahasa-inggris-nasional',
                'image' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?q=80&w=1200&auto=format&fit=crop',
                'category_id' => $lomba,
                'description' => 'Delegasi tim debat bahasa Inggris sukses mengukir prestasi membanggakan tingkat nasional.',
                'event_date' => now()->subDays(65),
                'order' => 15,
                'is_active' => true,
            ],
        ];

        foreach ($galleries as $galleryData) {
            Gallery::create($galleryData);
        }
    }
}
