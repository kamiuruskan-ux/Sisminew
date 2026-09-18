<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Grade::query()->delete();

        $students = Student::with('class')->get();
        $teacher = User::role('guru')->first() ?? User::role('admin')->first() ?? User::first();

        if ($students->isEmpty() || !$teacher) {
            return;
        }

        $subjects = [
            'Matematika',
            'Fisika',
            'Kimia',
            'Biologi',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'Informatika',
            'PJOK',
            'Sejarah',
            'Pendidikan Agama',
        ];

        $gradeTemplates = [
            [
                'type' => 'daily',
                'notes' => 'Tugas Harian Bab 1 - Pemahaman Dasar',
                'scores' => [85, 90, 88, 92, 78, 82, 95],
                'days_ago' => 30,
            ],
            [
                'type' => 'daily',
                'notes' => 'Kuis Harian Bab 2 - Latihan Soal',
                'scores' => [88, 85, 92, 90, 80, 86, 94],
                'days_ago' => 20,
            ],
            [
                'type' => 'mid_term',
                'notes' => 'Ujian Tengah Semester (UTS) Ganjil',
                'scores' => [86, 88, 90, 92, 84, 80, 96],
                'days_ago' => 14,
            ],
            [
                'type' => 'daily',
                'notes' => 'Tugas Praktikum & Project Kelompok',
                'scores' => [92, 94, 88, 90, 85, 87, 98],
                'days_ago' => 7,
            ],
            [
                'type' => 'exam',
                'notes' => 'Ujian Evaluasi Bulanan',
                'scores' => [84, 89, 91, 93, 82, 85, 95],
                'days_ago' => 3,
            ],
        ];

        foreach ($students as $studentIndex => $student) {
            if (!$student->class_id) {
                continue;
            }

            foreach ($subjects as $subjectIndex => $subject) {
                foreach ($gradeTemplates as $templateIndex => $tpl) {
                    $scoreIndex = ($studentIndex + $subjectIndex + $templateIndex) % count($tpl['scores']);
                    $score = $tpl['scores'][$scoreIndex];

                    Grade::create([
                        'student_id' => $student->id,
                        'class_id' => $student->class_id,
                        'subject' => $subject,
                        'type' => $tpl['type'],
                        'score' => $score,
                        'notes' => $tpl['notes'],
                        'recorded_by' => $teacher->id,
                        'created_at' => now()->subDays($tpl['days_ago'])->addHours($subjectIndex),
                        'updated_at' => now()->subDays($tpl['days_ago'])->addHours($subjectIndex),
                    ]);
                }
            }
        }
    }
}
