<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LmsChapter;
use App\Models\LmsTopic;
use App\Models\LmsTopicQuiz;
use App\Models\Assignment;
use App\Models\Exam;
use App\Models\Material;
use App\Models\LmsLiveClass;
use App\Models\LmsGamification;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;

class LmsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        LmsChapter::truncate();
        LmsTopic::truncate();
        LmsTopicQuiz::truncate();
        LmsLiveClass::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $classes = ClassModel::all()->keyBy('name');
        $teacher = User::whereHas('roles', fn($q) => $q->whereIn('slug', ['guru', 'admin', 'super-admin']))->first() ?? User::first();
        $teacherId = $teacher ? $teacher->id : 1;

        $classXA   = $classes->get('X IPA 1')?->id ?? $classes->get('X-A')?->id ?? $classes->first()?->id;
        $classXB   = $classes->get('X TKJ 1')?->id ?? $classes->get('X-B')?->id ?? $classes->skip(1)->first()?->id;
        $classXIA  = $classes->get('XI IPA 1')?->id ?? $classes->get('XI-A')?->id ?? $classes->skip(2)->first()?->id;
        $classXIB  = $classes->get('XI TKJ 1')?->id ?? $classes->get('XI-B')?->id ?? $classes->skip(3)->first()?->id;
        $classXIIA = $classes->get('XII IPA 1')?->id ?? $classes->get('XII-A')?->id ?? $classes->skip(4)->first()?->id;

        // ==========================================
        // 1. MATA PELAJARAN: MATEMATIKA
        // ==========================================
        
        // A. Master Utama (Semua Kelas)
        $chMatMaster = LmsChapter::create([
            'subject' => 'Matematika',
            'title' => 'Bab 1: Eksponen & Logaritma',
            'description' => 'Kurikulum Master: Mempelajari sifat-sifat eksponen, bentuk pangkat, dan operasi dasar logaritma.',
            'class_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $tpMat1 = LmsTopic::create([
            'lms_chapter_id' => $chMatMaster->id,
            'title' => 'Sub-Bab 1.1: Sifat-sifat Bilangan Berpangkat (Eksponen)',
            'description' => 'Perkalian, pembagian, dan pemangkatan bilangan eksponen positif & negatif.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '09:30',
            'order' => 1,
            'xp_reward' => 50,
        ]);

        LmsTopicQuiz::create([
            'lms_topic_id' => $tpMat1->id,
            'question' => 'Berapakah hasil sederhana dari 2^3 × 2^4 ?',
            'option_a' => '2^7 (128)',
            'option_b' => '2^12 (4096)',
            'option_c' => '4^7',
            'option_d' => '2^1 (2)',
            'correct_option' => 'a',
            'explanation' => 'Perkalian eksponen berpangkat sama: a^m × a^n = a^(m+n). Maka 2^(3+4) = 2^7 = 128.',
            'xp_reward' => 100,
        ]);

        LmsTopicQuiz::create([
            'lms_topic_id' => $tpMat1->id,
            'question' => 'Bentuk sederhana dari (a^6 / a^2) adalah...',
            'option_a' => 'a^4',
            'option_b' => 'a^12',
            'option_c' => 'a^8',
            'option_d' => 'a^3',
            'correct_option' => 'a',
            'explanation' => 'Pembagian eksponen: a^m / a^n = a^(m-n). Maka a^(6-2) = a^4.',
            'xp_reward' => 100,
        ]);

        Material::create([
            'title' => 'Modul Ringkasan Rumus Eksponen & Logaritma PDF',
            'description' => 'Rangkuman rumus cepat penyelesaian soal eksponen.',
            'subject' => 'Matematika',
            'class_id' => $classXA ?? 1,
            'lms_chapter_id' => $chMatMaster->id,
            'lms_topic_id' => $tpMat1->id,
            'teacher_id' => $teacherId,
            'external_link' => 'https://drive.google.com',
            'is_published' => true,
        ]);

        $tpMat2 = LmsTopic::create([
            'lms_chapter_id' => $chMatMaster->id,
            'title' => 'Sub-Bab 1.2: Konsep Dasar & Sifat Logaritma',
            'description' => 'Menghubungkan eksponen ke logaritma dan sifat penyederhanaannya.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '12:15',
            'order' => 2,
            'xp_reward' => 50,
        ]);

        LmsTopicQuiz::create([
            'lms_topic_id' => $tpMat2->id,
            'question' => 'Berapakah nilai dari ^2log 8 ?',
            'option_a' => '3',
            'option_b' => '4',
            'option_c' => '2',
            'option_d' => '8',
            'correct_option' => 'a',
            'explanation' => 'Karena 2^3 = 8, maka ^2log 8 = 3.',
            'xp_reward' => 100,
        ]);

        // B. Spesifik Kelas X-A (Matematika)
        $chMatXA = LmsChapter::create([
            'subject' => 'Matematika',
            'title' => 'Bab 2: Persamaan & Pertidaksamaan Kuadrat (Kelas X-A)',
            'description' => 'Pendalaman materi khusus siswa Kelas X-A mengenai faktorisasi & rumus ABC.',
            'class_id' => $classXA,
            'order' => 2,
            'is_active' => true,
        ]);

        $tpMatXA = LmsTopic::create([
            'lms_chapter_id' => $chMatXA->id,
            'title' => 'Sub-Bab 2.1: Akar-akar Persamaan Kuadrat dengan Rumus ABC',
            'description' => 'Penggunaan rumus x1,2 = (-b ± √(b^2 - 4ac)) / 2a.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '11:00',
            'order' => 1,
            'xp_reward' => 60,
        ]);

        Assignment::create([
            'title' => 'Tugas Mandiri Bab 2 Persamaan Kuadrat',
            'description' => 'Kerjakan 5 soal essay pada lembar tugas dan unggah hasil pengerjaan.',
            'subject' => 'Matematika',
            'class_id' => $classXA,
            'lms_chapter_id' => $chMatXA->id,
            'lms_topic_id' => $tpMatXA->id,
            'teacher_id' => $teacherId,
            'due_date' => now()->addDays(5),
            'max_score' => 100,
            'status' => 'published',
        ]);

        Exam::create([
            'title' => 'Kuis CBT Harian Matematika Kelas X-A',
            'description' => 'Ujian interaktif 15 menit untuk menguji pemahaman rumus ABC.',
            'subject_name' => 'Matematika',
            'class_id' => $classXA,
            'lms_chapter_id' => $chMatXA->id,
            'lms_topic_id' => $tpMatXA->id,
            'duration_minutes' => 15,
            'start_time' => now(),
            'end_time' => now()->addDays(7),
            'is_published' => true,
            'exam_type' => 'quiz',
        ]);

        // C. Spesifik Kelas XI-A (Matematika)
        $chMatXIA = LmsChapter::create([
            'subject' => 'Matematika',
            'title' => 'Bab 1: Trigonometri Dasar & Identitas (Kelas XI-A)',
            'description' => 'Materi Matematika Lanjut khusus Kelas XI-A.',
            'class_id' => $classXIA,
            'order' => 1,
            'is_active' => true,
        ]);

        $tpMatXIA = LmsTopic::create([
            'lms_chapter_id' => $chMatXIA->id,
            'title' => 'Sub-Bab 1.1: Sinus, Kosinus, & Tangen Sudut Istimewa',
            'description' => 'Tabel sudut 0°, 30°, 45°, 60°, dan 90°.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '14:20',
            'order' => 1,
            'xp_reward' => 70,
        ]);

        // ==========================================
        // 2. MATA PELAJARAN: ILMU PENGETAHUAN ALAM (IPA)
        // ==========================================
        
        $chIpaMaster = LmsChapter::create([
            'subject' => 'Ilmu Pengetahuan Alam (IPA)',
            'title' => 'Bab 1: Struktur Sel & Fungsi Organel',
            'description' => 'Pengenalan sel hewan, sel tumbuhan, dan bagian-bagian mikroskopis sel.',
            'class_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $tpIpa1 = LmsTopic::create([
            'lms_chapter_id' => $chIpaMaster->id,
            'title' => 'Sub-Bab 1.1: Perbedaan Sel Hewan dan Sel Tumbuhan',
            'description' => 'Mengidentifikasi dinding sel, kloroplas, vakuola, dan membran sel.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '10:15',
            'order' => 1,
            'xp_reward' => 50,
        ]);

        LmsTopicQuiz::create([
            'lms_topic_id' => $tpIpa1->id,
            'question' => 'Organel sel yang berfungsi sebagai tempat fotosintesis pada tumbuhan adalah...',
            'option_a' => 'Kloroplas',
            'option_b' => 'Mitokondria',
            'option_c' => 'Ribosom',
            'option_d' => 'Lisosom',
            'correct_option' => 'a',
            'explanation' => 'Kloroplas mengandung klorofil yang menangkap energi cahaya matahari untuk fotosintesis.',
            'xp_reward' => 100,
        ]);

        // IPA Kelas X-B
        $chIpaXB = LmsChapter::create([
            'subject' => 'Ilmu Pengetahuan Alam (IPA)',
            'title' => 'Bab 2: Ekosistem & Pencemaran Lingkungan (Kelas X-B)',
            'description' => 'Pendalaman materi iklim dan ekologi untuk kelas X-B.',
            'class_id' => $classXB,
            'order' => 2,
            'is_active' => true,
        ]);

        $tpIpaXB = LmsTopic::create([
            'lms_chapter_id' => $chIpaXB->id,
            'title' => 'Sub-Bab 2.1: Rantai Makanan & Jaring-Jaring Makanan',
            'description' => 'Produsen, konsumen tingkat I, II, III, dan pengurai.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '09:00',
            'order' => 1,
            'xp_reward' => 50,
        ]);

        // ==========================================
        // 3. MATA PELAJARAN: FISIKA
        // ==========================================

        $chFisMaster = LmsChapter::create([
            'subject' => 'Fisika',
            'title' => 'Bab 1: Vektor & Kinematika Gerak Lurus',
            'description' => 'Konsep perpindahan, kecepatan rata-rata, percepatan, GLB dan GLBB.',
            'class_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $tpFis1 = LmsTopic::create([
            'lms_chapter_id' => $chFisMaster->id,
            'title' => 'Sub-Bab 1.1: Gerak Lurus Berubah Beraturan (GLBB)',
            'description' => 'Persamaan v = v0 + at dan s = v0 t + 1/2 a t^2.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '13:45',
            'order' => 1,
            'xp_reward' => 60,
        ]);

        LmsTopicQuiz::create([
            'lms_topic_id' => $tpFis1->id,
            'question' => 'Sebuah mobil bergerak dari keadaan diam dengan percepatan 2 m/s^2. Berapakah kecepatannya setelah 5 detik?',
            'option_a' => '10 m/s',
            'option_b' => '20 m/s',
            'option_c' => '5 m/s',
            'option_d' => '2.5 m/s',
            'correct_option' => 'a',
            'explanation' => 'Gunakan rumus v = v0 + at. Diketahui v0 = 0, a = 2, t = 5. Maka v = 0 + 2(5) = 10 m/s.',
            'xp_reward' => 100,
        ]);

        // Fisika XI-A
        $chFisXIA = LmsChapter::create([
            'subject' => 'Fisika',
            'title' => 'Bab 2: Hukum Newton & Dinamika Gerak (Kelas XI-A)',
            'description' => 'Hukum I, II, dan III Newton tentang gerak dan gaya gesek.',
            'class_id' => $classXIA,
            'order' => 2,
            'is_active' => true,
        ]);

        $tpFisXIA = LmsTopic::create([
            'lms_chapter_id' => $chFisXIA->id,
            'title' => 'Sub-Bab 2.1: Analisis Gaya pada Bidang Miring',
            'description' => 'Gaya berat, gaya normal, dan komponen gaya F = m.a.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '15:10',
            'order' => 1,
            'xp_reward' => 70,
        ]);

        // ==========================================
        // 4. MATA PELAJARAN: BAHASA INGGRIS
        // ==========================================

        $chBigMaster = LmsChapter::create([
            'subject' => 'Bahasa Inggris',
            'title' => 'Chapter 1: Descriptive Text & Expressions of Offering',
            'description' => 'Learning descriptive vocabulary, adjectives, and polite offering expressions.',
            'class_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $tpBig1 = LmsTopic::create([
            'lms_chapter_id' => $chBigMaster->id,
            'title' => 'Sub-Chapter 1.1: Describing Places and Famous Landmarks',
            'description' => 'Using adjectives and noun phrases to describe tourist destinations.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '08:50',
            'order' => 1,
            'xp_reward' => 50,
        ]);

        LmsTopicQuiz::create([
            'lms_topic_id' => $tpBig1->id,
            'question' => 'Which of the following phrases is used to offer help politely?',
            'option_a' => 'May I help you with those bags?',
            'option_b' => 'You must carry this bag.',
            'option_c' => 'Why are you holding that?',
            'option_d' => 'Don\'t touch my luggage!',
            'correct_option' => 'a',
            'explanation' => '"May I help you...?" is a formal and polite expression of offering help.',
            'xp_reward' => 100,
        ]);

        // Bahasa Inggris Kelas XII-A
        $chBigXIIA = LmsChapter::create([
            'subject' => 'Bahasa Inggris',
            'title' => 'Chapter 2: Application Letter & Job Interview (Kelas XII-A)',
            'description' => 'Preparing senior students for professional career writing & interviews.',
            'class_id' => $classXIIA,
            'order' => 2,
            'is_active' => true,
        ]);

        $tpBigXIIA = LmsTopic::create([
            'lms_chapter_id' => $chBigXIIA->id,
            'title' => 'Sub-Chapter 2.1: Structure of Cover Letter & Resume',
            'description' => 'Salutation, body paragraphs, call to action, and formal closing.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '12:00',
            'order' => 1,
            'xp_reward' => 80,
        ]);

        // ==========================================
        // 5. MATA PELAJARAN: INFORMATIKA / KOMPUTER
        // ==========================================

        $chInfMaster = LmsChapter::create([
            'subject' => 'Informatika / Komputer',
            'title' => 'Bab 1: Algoritma Dasar & Pemrograman Python',
            'description' => 'Struktur kontrol, variabel, tipe data, kondisional, dan perulangan.',
            'class_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $tpInf1 = LmsTopic::create([
            'lms_chapter_id' => $chInfMaster->id,
            'title' => 'Sub-Bab 1.1: Pengenalan Sintaksis & Tipe Data Python',
            'description' => 'Integer, float, string, boolean, dan pencetakan data print().',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '11:40',
            'order' => 1,
            'xp_reward' => 60,
        ]);

        LmsTopicQuiz::create([
            'lms_topic_id' => $tpInf1->id,
            'question' => 'Simbol apakah yang digunakan untuk memasukkan komentar satu baris di Python?',
            'option_a' => '# (Pagar)',
            'option_b' => '// (Garis miring dua)',
            'option_c' => '<!-- -->',
            'option_d' => '/* */',
            'correct_option' => 'a',
            'explanation' => 'Di Python, komentar satu baris diawali dengan tanda pagar (#).',
            'xp_reward' => 100,
        ]);

        // ==========================================
        // 6. MATA PELAJARAN: KIMIA
        // ==========================================

        $chKimMaster = LmsChapter::create([
            'subject' => 'Kimia',
            'title' => 'Bab 1: Struktur Atom & Tabel Periodik Unsur',
            'description' => 'Mempelajari partikel penyusun atom, nomor atom, nomor massa, dan konfigurasi elektron.',
            'class_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $tpKim1 = LmsTopic::create([
            'lms_chapter_id' => $chKimMaster->id,
            'title' => 'Sub-Bab 1.1: Konfigurasi Elektron & Elektron Valensi',
            'description' => 'Penulisan konfigurasi elektron Bohr dan kuantum untuk menentukan letak periode dan golongan.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '10:45',
            'order' => 1,
            'xp_reward' => 60,
        ]);

        LmsTopicQuiz::create([
            'lms_topic_id' => $tpKim1->id,
            'question' => 'Jumlah maksimum elektron pada kulit K (n=1) menurut teori atom Bohr adalah...',
            'option_a' => '2',
            'option_b' => '8',
            'option_c' => '18',
            'option_d' => '32',
            'correct_option' => 'a',
            'explanation' => 'Rumus jumlah maksimum elektron pada kulit ke-n adalah 2n^2. Untuk n=1, 2(1)^2 = 2 elektron.',
            'xp_reward' => 100,
        ]);

        // ==========================================
        // 7. MATA PELAJARAN: BIOLOGI
        // ==========================================

        $chBioMaster = LmsChapter::create([
            'subject' => 'Biologi',
            'title' => 'Bab 1: Keanekaragaman Hayati & Kehidupan Organisme',
            'description' => 'Tingkat keanekaragaman gen, jenis, dan ekosistem di Indonesia.',
            'class_id' => null,
            'order' => 1,
            'is_active' => true,
        ]);

        $tpBio1 = LmsTopic::create([
            'lms_chapter_id' => $chBioMaster->id,
            'title' => 'Sub-Bab 1.1: Keanekaragaman Tingkat Gen dan Spesies',
            'description' => 'Contoh variasi genetik pada mawar merah/putih dan keanekaragaman antar genus.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'video_duration' => '09:15',
            'order' => 1,
            'xp_reward' => 50,
        ]);

        // ==========================================
        // 6. DEMO LIVE CLASSES
        // ==========================================
        LmsLiveClass::create([
            'teacher_id' => $teacherId,
            'subject' => 'Matematika',
            'class_id' => $classXA,
            'title' => 'Live Interactive: Pembahasan Soal PAS Matematika Kelas X-A',
            'description' => 'Sesi live bimbingan interaktif khusus siswa X-A sebelum Ujian Semester.',
            'scheduled_at' => now()->addHours(2),
            'duration_minutes' => 60,
            'meeting_url' => 'https://meet.jit.si/SekolahLrvLiveClassMatematika',
            'status' => 'live',
        ]);

        LmsLiveClass::create([
            'teacher_id' => $teacherId,
            'subject' => 'Fisika',
            'class_id' => $classXIA,
            'title' => 'Live Teaching: Simulasi Praktikum Vektor & Hukum Newton',
            'description' => 'Praktikum virtual dan bedah rumus Fisika untuk Kelas XI-A.',
            'scheduled_at' => now()->addDays(1)->setHour(10)->setMinute(0),
            'duration_minutes' => 90,
            'meeting_url' => 'https://meet.jit.si/SekolahLrvLiveClassFisika',
            'status' => 'scheduled',
        ]);

        LmsLiveClass::create([
            'teacher_id' => $teacherId,
            'subject' => 'Bahasa Inggris',
            'class_id' => $classXIIA,
            'title' => 'Live Speaking & Interview Simulation Kelas XII-A',
            'description' => 'Simulasi wawancara kerja dan TOEFL speaking practice.',
            'scheduled_at' => now()->addDays(2)->setHour(13)->setMinute(0),
            'duration_minutes' => 60,
            'meeting_url' => 'https://meet.jit.si/SekolahLrvLiveClassEnglish',
            'status' => 'scheduled',
        ]);

        // ==========================================
        // 7. GAMIFIKASI SISWA
        // ==========================================
        $students = Student::all();
        foreach ($students as $s) {
            LmsGamification::updateOrCreate(
                ['student_id' => $s->id],
                [
                    'xp' => rand(350, 1850),
                    'level' => rand(2, 5),
                    'current_streak' => rand(1, 14),
                    'last_active_date' => now(),
                    'badges' => ['first_step', 'quiz_master', 'top_performer'],
                ]
            );
        }
    }
}
