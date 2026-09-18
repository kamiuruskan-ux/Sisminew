<?php

namespace Database\Seeders;

use App\Models\BkViolationCategory;
use Illuminate\Database\Seeder;

class BkViolationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Terlambat Masuk Sekolah',
                'level' => 'ringan',
                'points' => 5,
                'penalty_recommendation' => 'Peringatan lisan & pembersihan area sekolah 15 menit',
                'description' => 'Datang ke sekolah lebih dari pukul 07.15 WIB tanpa keterangan sah',
            ],
            [
                'name' => 'Tidak Rapi / Atribut Kurang Lengkap',
                'level' => 'ringan',
                'points' => 5,
                'penalty_recommendation' => 'Teguran lisan & melengkapi atribut esok hari',
                'description' => 'Seragam tidak sesuai jadwal, tidak pakai dasi/sabuk/kaos kaki logo',
            ],
            [
                'name' => 'Bolos / Meninggalkan Kelas Tanpa Izin',
                'level' => 'sedang',
                'points' => 15,
                'penalty_recommendation' => 'Surat pernyataan siswa & tugas tambahan pelajaran',
                'description' => 'Meninggalkan jam pelajaran atau keluar gerbang sekolah tanpa surat izin piket',
            ],
            [
                'name' => 'Merokok / Vaping di Area Sekolah',
                'level' => 'berat',
                'points' => 35,
                'penalty_recommendation' => 'Pemanggilan orang tua & Surat Peringatan Pertama (SP-1)',
                'description' => 'Membawa, menyimpan, atau merokok/vape di lingkungan sekolah atau berseragam',
            ],
            [
                'name' => 'Berkelahi / Perundungan (Bullying)',
                'level' => 'sangat_berat',
                'points' => 50,
                'penalty_recommendation' => 'Pemanggilan orang tua, Surat Peringatan (SP-2/SP-3) & Skorsing',
                'description' => 'Kekerasan fisik, verbal, atau cyberbullying kepada sesama siswa',
            ],
            [
                'name' => 'Merusak Fasilitas Sekolah',
                'level' => 'sedang',
                'points' => 20,
                'penalty_recommendation' => 'Mengganti barang yang rusak & sanksi kebersihan',
                'description' => 'Coret-coret meja/dinding atau merusak sarana kelas/sekolah',
            ],
        ];

        foreach ($categories as $cat) {
            BkViolationCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
