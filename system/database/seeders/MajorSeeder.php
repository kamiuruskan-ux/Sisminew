<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SMA Majors (Academic)
        Major::firstOrCreate(['slug' => 'ipa'], [
            'name' => 'IPA (Ilmu Pengetahuan Alam)',
            'code' => 'IPA',
            'description' => 'Jurusan Ilmu Pengetahuan Alam',
            'type' => 'academic',
            'is_active' => true,
        ]);

        Major::firstOrCreate(['slug' => 'ips'], [
            'name' => 'IPS (Ilmu Pengetahuan Sosial)',
            'code' => 'IPS',
            'description' => 'Jurusan Ilmu Pengetahuan Sosial',
            'type' => 'academic',
            'is_active' => true,
        ]);

        Major::firstOrCreate(['slug' => 'bahasa'], [
            'name' => 'Bahasa',
            'code' => 'BHS',
            'description' => 'Jurusan Bahasa',
            'type' => 'academic',
            'is_active' => true,
        ]);

        // SMK Majors (Vocational)
        Major::firstOrCreate(['slug' => 'tkj'], [
            'name' => 'Teknik Komputer dan Jaringan',
            'code' => 'TKJ',
            'description' => 'Teknik Komputer dan Jaringan',
            'type' => 'vocational',
            'is_active' => true,
        ]);

        Major::firstOrCreate(['slug' => 'akuntansi'], [
            'name' => 'Akuntansi',
            'code' => 'AKL',
            'description' => 'Akuntansi dan Keuangan Lembaga',
            'type' => 'vocational',
            'is_active' => true,
        ]);

        Major::firstOrCreate(['slug' => 'pemasaran'], [
            'name' => 'Pemasaran',
            'code' => 'PMN',
            'description' => 'Bisnis Daring dan Pemasaran',
            'type' => 'vocational',
            'is_active' => true,
        ]);
    }
}
