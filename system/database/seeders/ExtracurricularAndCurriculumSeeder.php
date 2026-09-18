<?php

namespace Database\Seeders;

use App\Models\CurriculumFeature;
use App\Models\Extracurricular;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExtracurricularAndCurriculumSeeder extends Seeder
{
    public function run(): void
    {
        $extracurriculars = [
            [
                'name' => 'Pramuka Wajib',
                'category' => 'kepemimpinan',
                'description' => 'Membentuk kemandirian, kedisiplinan, kecintaan alam, dan jiwa kepemimpinan Pancasila.',
                'schedule' => 'Sabtu (08:00 WIB)',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Paskibraka',
                'category' => 'kepemimpinan',
                'description' => 'Pelatihan PBB profesional, pembentukan ketahanan fisik, serta baris-berbaris upacara resmi.',
                'schedule' => 'Selasa & Kamis',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Klub Robotik & AI',
                'category' => 'sains',
                'description' => 'Pembelajaran pemograman mikrokontroler, IoT, dan perakitan robot untuk kontes kompetisi sains.',
                'schedule' => 'Rabu (15:30 WIB)',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Basket & Futsal',
                'category' => 'olahraga',
                'description' => 'Pelatihan kebugaran atletik dan partisipasi dalam liga turnamen olahraga antar SMA.',
                'schedule' => 'Senin & Jumat',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Pentas Seni & Musik',
                'category' => 'seni',
                'description' => 'Pengembangan bakat seni tari tradisional, modern dance, vokal grup, dan musik band kampus.',
                'schedule' => 'Kamis (15:30 WIB)',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Karya Ilmiah Remaja (KIR)',
                'category' => 'sains',
                'description' => 'Riset eksperimental, pembuatan artikel ilmiah, dan persiapan Olimpiade Sains Nasional (OSN).',
                'schedule' => 'Selasa (15:00 WIB)',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Palang Merah Remaja (PMR)',
                'category' => 'kepemimpinan',
                'description' => 'Pelatihan pertolongan pertama pada kecelakaan (P3K), kesiapsiagaan bencana, dan aksi donor darah.',
                'schedule' => 'Jumat (15:00 WIB)',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'English Debating Club',
                'category' => 'sains',
                'description' => 'Asah keterampilan debat Bahasa Inggris, public speaking, dan kompetisi model United Nations (MUN).',
                'schedule' => 'Rabu (15:00 WIB)',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($extracurriculars as $extra) {
            Extracurricular::firstOrCreate(
                ['slug' => Str::slug($extra['name'])],
                $extra
            );
        }

        $curriculums = [
            [
                'title' => 'Merdeka Belajar',
                'description' => 'Memberikan keleluasaan kepada siswa untuk mengeksplorasi bakat ilmiah, seni, dan kepemimpinan secara personalisasi.',
                'icon' => 'academic',
                'color' => 'blue',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Digital Hybrid Learning',
                'description' => 'Dukungan Google Classroom, LMS modern, dan laboratorium komputer berkecepatan tinggi untuk riset ilmiah.',
                'icon' => 'desktop',
                'color' => 'purple',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Profil Pelajar Pancasila',
                'description' => 'Pembentukan karakter beriman, bertaqwa, berkebinekaan global, mandiri, bernalar kritis, dan kreatif.',
                'icon' => 'users',
                'color' => 'emerald',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Bimbingan Karir & PTN',
                'description' => 'Program konseling intensif untuk kesiapan seleksi perguruan tinggi negeri favorit dan universitas internasional.',
                'icon' => 'globe',
                'color' => 'amber',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($curriculums as $curr) {
            CurriculumFeature::firstOrCreate(
                ['title' => $curr['title']],
                $curr
            );
        }
    }
}
