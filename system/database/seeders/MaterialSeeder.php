<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\LmsTopic;
use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Material::query()->delete();

        $classes = ClassModel::all();
        $teacher = User::role('guru')->first() ?? User::role('admin')->first() ?? User::first();

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
            $topicMat = $findTopic('Matematika', $class->id);
            Material::create([
                'title' => 'Modul Bab 1: Matriks dan Transformasi Geometri',
                'description' => 'Bahan ajar materi operasi matriks, determinan, invers, dan penerapannya dalam koordinat kartesius.',
                'subject' => 'Matematika',
                'class_id' => $class->id,
                'lms_chapter_id' => $topicMat?->lms_chapter_id,
                'lms_topic_id' => $topicMat?->id,
                'teacher_id' => $teacher->id,
                'file_path' => null,
                'file_type' => 'pdf',
                'external_link' => 'https://drive.google.com/file/d/sample-matematika/view',
                'is_published' => true,
            ]);

            $topicFis = $findTopic('Fisika', $class->id);
            Material::create([
                'title' => 'Slide Presentation: Kinematika & Dinamika Gerak',
                'description' => 'Materi presentasi interaktif hukum-hukum Newton dan gerak lurus berubah beraturan (GLBB).',
                'subject' => 'Fisika',
                'class_id' => $class->id,
                'lms_chapter_id' => $topicFis?->lms_chapter_id,
                'lms_topic_id' => $topicFis?->id,
                'teacher_id' => $teacher->id,
                'file_path' => null,
                'file_type' => 'ppt',
                'external_link' => 'https://slideshare.net/sample-fisika',
                'is_published' => true,
            ]);

            $topicEng = $findTopic('Bahasa Inggris', $class->id);
            Material::create([
                'title' => 'Video Learning: English Conversation & Grammar Tips',
                'description' => 'Panduan video percakapan sehari-hari dan penggunaan tenses dalam kalimat efektif.',
                'subject' => 'Bahasa Inggris',
                'class_id' => $class->id,
                'lms_chapter_id' => $topicEng?->lms_chapter_id,
                'lms_topic_id' => $topicEng?->id,
                'teacher_id' => $teacher->id,
                'file_path' => null,
                'file_type' => 'video',
                'external_link' => 'https://youtube.com/watch?v=sample-english',
                'is_published' => true,
            ]);

            $topicInf = $findTopic('Informatika', $class->id);
            Material::create([
                'title' => 'Panduan Lab: Dasar Pemrograman HTML5 & CSS3',
                'description' => 'Modul praktikum laboratorium pembuatan website statis dasar untuk pemula.',
                'subject' => 'Informatika',
                'class_id' => $class->id,
                'lms_chapter_id' => $topicInf?->lms_chapter_id,
                'lms_topic_id' => $topicInf?->id,
                'teacher_id' => $teacher->id,
                'file_path' => null,
                'file_type' => 'pdf',
                'external_link' => null,
                'is_published' => true,
            ]);
        }
    }
}
