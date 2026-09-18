<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Slider::truncate();

        $sliders = [
            [
                'title' => 'Selamat Datang di SMA Nusantara',
                'description' => 'Mewujudkan generasi unggul, berkarakter, dan berprestasi berstandar internasional untuk masa depan yang cemerlang.',
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1800&auto=format&fit=crop',
                'link' => '/tentang',
                'link_text' => 'Pelajari Profil',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Pendaftaran Siswa Baru (SPMB) Telah Dibuka',
                'description' => 'Bergabunglah menjadi bagian dari civitas akademika berprestasi dengan fasilitas pembelajaran berbasis teknologi modern.',
                'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1800&auto=format&fit=crop',
                'link' => '/spmb',
                'link_text' => 'Daftar SPMB Online',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Prestasi Akademik & Inovasi Riset Sains',
                'description' => 'Fasilitas laboratorium terpadu, bimbingan olimpiade sains, serta ekstrakurikuler terlengkap untuk mengasah potensi.',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=1800&auto=format&fit=crop',
                'link' => '/galeri',
                'link_text' => 'Jelajahi Galeri',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }
}
