<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = (int) date('Y');

        // Tahun Ajaran Aktif (Ganjil: Juli - Desember)
        AcademicYear::firstOrCreate(['slug' => $currentYear . '-' . ($currentYear + 1) . '-ganjil'], [
            'name' => $currentYear . '/' . ($currentYear + 1) . ' - Ganjil',
            'start_year' => $currentYear,
            'end_year' => $currentYear + 1,
            'start_date' => $currentYear . '-07-01',
            'end_date' => $currentYear . '-12-31',
            'semester' => 'ganjil',
            'is_active' => true,
            'description' => 'Tahun Ajaran Aktif',
        ]);

        // Semester Genap (Januari - Juni)
        AcademicYear::firstOrCreate(['slug' => $currentYear . '-' . ($currentYear + 1) . '-genap'], [
            'name' => $currentYear . '/' . ($currentYear + 1) . ' - Genap',
            'start_year' => $currentYear,
            'end_year' => $currentYear + 1,
            'start_date' => ($currentYear + 1) . '-01-01',
            'end_date' => ($currentYear + 1) . '-06-30',
            'semester' => 'genap',
            'is_active' => false,
        ]);

        // Tahun Ajaran Lalu (2025/2026)
        AcademicYear::firstOrCreate(['slug' => ($currentYear - 1) . '-' . $currentYear . '-genap'], [
            'name' => ($currentYear - 1) . '/' . $currentYear . ' - Genap',
            'start_year' => $currentYear - 1,
            'end_year' => $currentYear,
            'start_date' => $currentYear . '-01-01',
            'end_date' => $currentYear . '-06-30',
            'semester' => 'genap',
            'is_active' => false,
            'description' => 'Tahun Ajaran Lalu',
        ]);
    }
}
