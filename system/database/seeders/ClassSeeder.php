<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        $teachers = User::whereHas('roles', function ($query) {
            $query->whereIn('slug', ['guru', 'teacher', 'admin']);
        })->get();

        $teacherCount = $teachers->count();
        $teacherIndex = 0;

        // Definition of integrated classes by Major code and grades
        $classStructure = [
            // SMA Majors (Academic)
            'IPA' => [
                10 => ['X IPA 1', 'X IPA 2'],
                11 => ['XI IPA 1', 'XI IPA 2'],
                12 => ['XII IPA 1', 'XII IPA 2'],
            ],
            'IPS' => [
                10 => ['X IPS 1', 'X IPS 2'],
                11 => ['XI IPS 1', 'XI IPS 2'],
                12 => ['XII IPS 1', 'XII IPS 2'],
            ],
            'BHS' => [
                10 => ['X BHS 1'],
                11 => ['XI BHS 1'],
                12 => ['XII BHS 1'],
            ],
            // SMK Majors (Vocational)
            'TKJ' => [
                10 => ['X TKJ 1', 'X TKJ 2'],
                11 => ['XI TKJ 1', 'XI TKJ 2'],
                12 => ['XII TKJ 1', 'XII TKJ 2'],
            ],
            'AKL' => [
                10 => ['X AKL 1', 'X AKL 2'],
                11 => ['XI AKL 1', 'XI AKL 2'],
                12 => ['XII AKL 1', 'XII AKL 2'],
            ],
            'PMN' => [
                10 => ['X PMN 1'],
                11 => ['XI PMN 1'],
                12 => ['XII PMN 1'],
            ],
        ];

        foreach ($classStructure as $majorCode => $grades) {
            $major = Major::where('code', $majorCode)->first() ?? Major::where('slug', Str::slug($majorCode))->first();

            if (!$major) {
                continue;
            }

            foreach ($grades as $grade => $classNames) {
                foreach ($classNames as $className) {
                    $slug = Str::slug($className);
                    $homeroomTeacher = $teacherCount > 0 ? $teachers->get($teacherIndex % $teacherCount) : null;
                    $teacherIndex++;

                    ClassModel::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $className,
                            'academic_year_id' => $academicYear?->id,
                            'major_id' => $major->id,
                            'homeroom_teacher_id' => $homeroomTeacher?->id,
                            'grade' => $grade,
                            'capacity' => 36,
                            'description' => 'Kelas ' . $className . ' Jurusan ' . $major->name,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
