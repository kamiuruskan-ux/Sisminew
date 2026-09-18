<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::create([
            'title' => 'Pemrograman Web Modern dengan Laravel & TailwindCSS',
            'author' => 'Ahmad Risyad, S.Kom.',
            'publisher' => 'Informatika Press',
            'category' => 'Komputer & IT',
            'isbn' => '978-602-1234-56-7',
            'description' => 'Panduan lengkap membangun aplikasi web profesional berstandar industri berbasis arsitektur modern.',
            'stock' => 15,
            'is_active' => true,
        ]);

        Book::create([
            'title' => 'Matematika Terapan untuk Sains dan Teknik',
            'author' => 'Dr. Bambang Sugiarto',
            'publisher' => 'Erlangga Pendidikan',
            'category' => 'Pelajaran',
            'isbn' => '978-979-4567-89-0',
            'description' => 'Kumpulan teori kalkulus, aljabar linear, dan statistika dasar untuk tingkat SMA/SMK.',
            'stock' => 25,
            'is_active' => true,
        ]);

        Book::create([
            'title' => 'English Grammar in Use & Academic Writing',
            'author' => 'Raymond Murphy & Team',
            'publisher' => 'Cambridge Education',
            'category' => 'Bahasa & Sastra',
            'isbn' => '978-052-1123-45-6',
            'description' => 'Buku referensi lengkap tata bahasa Inggris dan teknik penulisan esai akademis.',
            'stock' => 20,
            'is_active' => true,
        ]);

        Book::create([
            'title' => 'Fisika Kuantum & Mekanika Dasar',
            'author' => 'Prof. Ir. Hendra Wijaya',
            'publisher' => 'Yudhistira Karya',
            'category' => 'Sains & Teknologi',
            'isbn' => '978-602-9876-54-3',
            'description' => 'Konsep dasar ilmu fisika modern, hukum termodinamika, dan gelombang elektromagnetik.',
            'stock' => 10,
            'is_active' => true,
        ]);

        Book::create([
            'title' => 'Kewirausahaan Muda & Bisnis Digital',
            'author' => 'Dewi Lestari, M.M.',
            'publisher' => 'Mitra Edukasi',
            'category' => 'Bisnis & Ekonomi',
            'isbn' => '978-602-5555-11-2',
            'description' => 'Langkah praktis merancang rencana bisnis, analisis pasar, dan strategi pemasaran digital.',
            'stock' => 12,
            'is_active' => true,
        ]);
    }
}
