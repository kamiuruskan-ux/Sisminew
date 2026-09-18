<?php

namespace Database\Seeders;

use App\Models\Wave;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wave::truncate();
        $currentYear = date('Y');

        // 1. Gelombang 1: Active (Sedang Dibuka)
        Wave::create([
            'name' => "Gelombang 1 - Jalur Beasiswa Utama $currentYear",
            'slug' => Str::slug("Gelombang 1 - Jalur Beasiswa Utama $currentYear"),
            'quota' => 150,
            'start_date' => now()->subDays(15)->format('Y-m-d'),
            'end_date' => now()->addDays(45)->format('Y-m-d'),
            'registration_fee' => 150000,
            'status' => 'active',
            'description' => 'Jalur seleksi beasiswa prestasi akademik & non-akademik dengan diskon SPP hingga 100%.',
        ]);

        // 2. Gelombang 2: Upcoming (Belum Dibuka / Mendatang)
        Wave::create([
            'name' => "Gelombang 2 - Jalur Reguler TPA $currentYear",
            'slug' => Str::slug("Gelombang 2 - Jalur Reguler TPA $currentYear"),
            'quota' => 200,
            'start_date' => now()->addDays(50)->format('Y-m-d'),
            'end_date' => now()->addDays(110)->format('Y-m-d'),
            'registration_fee' => 200000,
            'status' => 'active',
            'description' => 'Jalur tes potensi akademik dan wawancara minat bakat calon siswa baru.',
        ]);

        // 3. Gelombang Early Bird: Closed (Sudah Ditutup)
        Wave::create([
            'name' => "Gelombang Early Bird $currentYear",
            'slug' => Str::slug("Gelombang Early Bird $currentYear"),
            'quota' => 100,
            'start_date' => now()->subDays(90)->format('Y-m-d'),
            'end_date' => now()->subDays(16)->format('Y-m-d'),
            'registration_fee' => 100000,
            'status' => 'closed',
            'description' => 'Pendaftaran prapembukaan resmi dengan penawaran gratis formulir registrasi.',
        ]);
    }
}
