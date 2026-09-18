<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['name' => 'Matematika', 'code' => 'MTK', 'order' => 1],
            ['name' => 'IPA', 'code' => 'IPA', 'order' => 2],
            ['name' => 'Fisika', 'code' => 'FIS', 'order' => 3],
            ['name' => 'Kimia', 'code' => 'KIM', 'order' => 4],
            ['name' => 'Biologi', 'code' => 'BIO', 'order' => 5],
            ['name' => 'Bahasa Indonesia', 'code' => 'BIND', 'order' => 6],
            ['name' => 'Bahasa Inggris', 'code' => 'BING', 'order' => 7],
            ['name' => 'Informatika', 'code' => 'INF', 'order' => 8],
            ['name' => 'Pendidikan Agama & Budi Pekerti', 'code' => 'PAI', 'order' => 9],
            ['name' => 'Pancasila & Kewarganegaraan', 'code' => 'PKN', 'order' => 10],
            ['name' => 'Sejarah Indonesia', 'code' => 'SEJ', 'order' => 11],
            ['name' => 'Seni Budaya', 'code' => 'SENI', 'order' => 12],
            ['name' => 'PJOK', 'code' => 'PJOK', 'order' => 13],
            ['name' => 'Bimbingan Konseling', 'code' => 'BK', 'order' => 14],
        ];

        foreach ($subjects as $subj) {
            Subject::firstOrCreate(
                ['name' => $subj['name']],
                [
                    'code' => $subj['code'],
                    'order' => $subj['order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
