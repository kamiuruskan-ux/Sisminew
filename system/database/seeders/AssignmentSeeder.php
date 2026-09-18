<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassModel;
use App\Models\LmsTopic;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AssignmentSubmission::query()->delete();
        Assignment::query()->delete();

        $classes = ClassModel::all();
        $teacher = User::role('guru')->first() ?? User::role('admin')->first() ?? User::first();
        $students = Student::all();

        if ($classes->isEmpty() || !$teacher) {
            return;
        }

        // Helper function to resolve linked LmsTopic based on subject and class
        $findTopic = function ($subject, $classId) {
            return LmsTopic::whereHas('chapter', function ($q) use ($subject, $classId) {
                $q->where(function ($sq) use ($subject) {
                    $sq->where('subject', 'LIKE', "%{$subject}%")
                       ->orWhere('subject', $subject);
                })->where(function ($cq) use ($classId) {
                    $cq->whereNull('class_id')->orWhere('class_id', $classId);
                });
            })->first();
        };

        foreach ($classes as $class) {
            // 1. Tugas Matematika
            $topicMat = $findTopic('Matematika', $class->id);
            $a1 = Assignment::create([
                'title' => 'Tugas 1: Aljabar Linear dan Persamaan Kuadrat',
                'description' => 'Kerjakan soal latihan halaman 45-48 di buku paket Matematika. Upload jawaban Anda dalam format PDF atau foto tulisan tangan yang jelas.',
                'subject' => 'Matematika',
                'class_id' => $class->id,
                'lms_chapter_id' => $topicMat?->lms_chapter_id,
                'lms_topic_id' => $topicMat?->id,
                'teacher_id' => $teacher->id,
                'due_date' => now()->addDays(3)->setHour(23)->setMinute(59),
                'attachment' => null,
                'max_score' => 100,
                'status' => 'published',
            ]);

            // 2. Tugas Esai Bahasa Inggris
            $topicEng = $findTopic('Bahasa Inggris', $class->id);
            $a2 = Assignment::create([
                'title' => 'Essay: The Impact of Artificial Intelligence in Education',
                'description' => 'Write an analytical exposition essay (minimum 300 words) discussing how AI technology reshapes modern learning environments.',
                'subject' => 'Bahasa Inggris',
                'class_id' => $class->id,
                'lms_chapter_id' => $topicEng?->lms_chapter_id,
                'lms_topic_id' => $topicEng?->id,
                'teacher_id' => $teacher->id,
                'due_date' => now()->addDays(6)->setHour(23)->setMinute(59),
                'attachment' => null,
                'max_score' => 100,
                'status' => 'published',
            ]);

            // 3. Laporan Praktikum Fisika
            $topicFis = $findTopic('Fisika', $class->id);
            $a3 = Assignment::create([
                'title' => 'Laporan Praktikum: Hukum II Newton & Bandul Sederhana',
                'description' => 'Buat laporan lengkap praktikum pengukuran gravitasi bumi menggunakan pendulum sederhana sesuai format standar laboratorium.',
                'subject' => 'Fisika',
                'class_id' => $class->id,
                'lms_chapter_id' => $topicFis?->lms_chapter_id,
                'lms_topic_id' => $topicFis?->id,
                'teacher_id' => $teacher->id,
                'due_date' => now()->addDays(1)->setHour(17)->setMinute(0),
                'attachment' => null,
                'max_score' => 100,
                'status' => 'published',
            ]);

            // 4. Tugas Kimia
            $topicKim = $findTopic('Kimia', $class->id) ?? $topicFis;
            $a4 = Assignment::create([
                'title' => 'Tugas Struktur Atom & Tabel Periodik Unsur',
                'description' => 'Tentukan konfigurasi elektron dan letak periode/golongan untuk 10 unsur pilihan pada lembar soal.',
                'subject' => 'Kimia',
                'class_id' => $class->id,
                'lms_chapter_id' => $topicKim?->lms_chapter_id,
                'lms_topic_id' => $topicKim?->id,
                'teacher_id' => $teacher->id,
                'due_date' => now()->subDays(4)->setHour(23)->setMinute(59),
                'attachment' => null,
                'max_score' => 100,
                'status' => 'closed',
            ]);

            // 5. Project Informatika
            $topicInf = $findTopic('Informatika', $class->id);
            $a5 = Assignment::create([
                'title' => 'Project Web Design: Landing Page Sekolah',
                'description' => 'Rancang dan buat tampilan landing page responsif menggunakan HTML, CSS, dan JavaScript.',
                'subject' => 'Informatika',
                'class_id' => $class->id,
                'lms_chapter_id' => $topicInf?->lms_chapter_id,
                'lms_topic_id' => $topicInf?->id,
                'teacher_id' => $teacher->id,
                'due_date' => now()->addDays(14)->setHour(23)->setMinute(59),
                'attachment' => null,
                'max_score' => 100,
                'status' => 'published',
            ]);

            // Seed sample submissions for students in this class
            $classStudents = $students->where('class_id', $class->id);
            foreach ($classStudents as $student) {
                // Student submits task 1 (graded)
                AssignmentSubmission::create([
                    'assignment_id' => $a1->id,
                    'student_id' => $student->id,
                    'content' => 'Saya telah menyelesaikan seluruh soal nomor 1-10 di lembar jawaban terlampir.',
                    'attachment' => null,
                    'submitted_at' => now()->subHours(5),
                    'score' => 95.00,
                    'feedback' => 'Pekerjaan sangat rapi dan jawaban tepat. Tingkatkan terus prestasimu!',
                    'graded_by' => $teacher->id,
                    'status' => 'graded',
                ]);

                // Student submits task 4 (graded closed)
                AssignmentSubmission::create([
                    'assignment_id' => $a4->id,
                    'student_id' => $student->id,
                    'content' => 'Tugas Kimia Konfigurasi Elektron telah diunggah.',
                    'attachment' => null,
                    'submitted_at' => now()->subDays(5),
                    'score' => 88.00,
                    'feedback' => 'Bagus, perhatikan kembali konsep elektron valensi pada golongan transisi.',
                    'graded_by' => $teacher->id,
                    'status' => 'graded',
                ]);

                // Student submits task 3 (submitted pending grading)
                AssignmentSubmission::create([
                    'assignment_id' => $a3->id,
                    'student_id' => $student->id,
                    'content' => 'Berikut file laporan praktikum fisika bandul sederhana kami.',
                    'attachment' => null,
                    'submitted_at' => now()->subHours(2),
                    'score' => null,
                    'feedback' => null,
                    'graded_by' => null,
                    'status' => 'submitted',
                ]);
            }
        }
    }
}
