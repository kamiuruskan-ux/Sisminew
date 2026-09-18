<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamResult;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class CbtExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        ExamResult::truncate();
        Question::truncate();
        Exam::truncate();
        Schema::enableForeignKeyConstraints();

        $class = ClassModel::first();
        $classId = $class ? $class->id : null;

        // Query available teacher users
        $guruIpa = User::where('email', 'ahmad.fauzi@sekolah.id')->first()
            ?? User::whereHas('roles', fn($q) => $q->where('slug', 'guru'))->first()
            ?? User::first();

        $guruMath = User::where('email', 'budi.teacher@sekolah.id')->first()
            ?? User::whereHas('roles', fn($q) => $q->where('slug', 'guru'))->skip(1)->first()
            ?? $guruIpa;

        $guruBahasa = User::where('email', 'dewi.lestari@sekolah.id')->first()
            ?? User::whereHas('roles', fn($q) => $q->where('slug', 'guru'))->skip(2)->first()
            ?? $guruIpa;

        // Helper function to resolve linked LmsTopic based on subject and class
        $findTopic = function ($subject, $classId = null) {
            return \App\Models\LmsTopic::whereHas('chapter', function ($q) use ($subject, $classId) {
                $q->where(function ($sq) use ($subject) {
                    $sq->where('subject', 'LIKE', "%{$subject}%")
                       ->orWhere('subject', $subject);
                })->where(function ($cq) use ($classId) {
                    if ($classId) {
                        $cq->whereNull('class_id')->orWhere('class_id', $classId);
                    } else {
                        $cq->whereNull('class_id');
                    }
                });
            })->first();
        };

        $topicIpa = $findTopic('IPA', $classId) ?? $findTopic('Sains', $classId) ?? \App\Models\LmsTopic::first();
        $topicMath = $findTopic('Matematika', $classId);

        // ----------------------------------------------------
        // 1. Ujian Akhir Semester (UAS) Sains & Pengetahuan Umum
        // Guru Pengampu: Ahmad Fauzi, S.Si., M.Si.
        // ----------------------------------------------------
        $examMulti = Exam::create([
            'title' => 'Ujian Akhir Semester (UAS) Sains & Pengetahuan Umum',
            'description' => 'Ujian komprehensif yang terdiri dari soal Pilihan Ganda, PG Kompleks, Benar/Salah, Menjodohkan, dan Essay.',
            'subject_name' => 'Ilmu Pengetahuan Alam & Umum',
            'teacher_id' => $guruIpa?->id,
            'class_id' => $classId,
            'lms_chapter_id' => $topicIpa?->lms_chapter_id,
            'lms_topic_id' => $topicIpa?->id,
            'duration_minutes' => 60,
            'start_time' => now()->subDays(1),
            'end_time' => now()->addDays(14),
            'is_published' => true,
        ]);

        // Soal 1: Pilihan Ganda (PG)
        $q1 = Question::create([
            'exam_id' => $examMulti->id,
            'type' => 'pg',
            'question_text' => 'Ibu kota negara Indonesia yang baru dibangun di Kalimantan Timur adalah...',
            'image_path' => $this->createDummyImage('ikn_map.png', 'Peta Nusantara (IKN)'),
            'option_a' => 'Nusantara (IKN)',
            'option_b' => 'Samarinda',
            'option_c' => 'Balikpapan',
            'option_d' => 'Banjarmasin',
            'option_e' => 'Pontianak',
            'correct_answer' => 'a',
            'score_weight' => 20,
        ]);

        // Soal 2: Pilihan Ganda Kompleks (PG Kompleks)
        $q2 = Question::create([
            'exam_id' => $examMulti->id,
            'type' => 'pg_kompleks',
            'question_text' => 'Manakah di antara hewan-hewan berikut yang tergolong dalam kelas Mamalia? (Pilih semua yang benar)',
            'option_a' => 'Kucing',
            'option_b' => 'Paus',
            'option_c' => 'Ayam',
            'option_d' => 'Sapi',
            'option_e' => 'Buaya',
            'correct_answer' => 'a,b,d',
            'correct_answer_json' => ['a', 'b', 'd'],
            'score_weight' => 20,
        ]);

        // Soal 3: Benar / Salah (Multi Statements)
        $q3 = Question::create([
            'exam_id' => $examMulti->id,
            'type' => 'benar_salah',
            'question_text' => 'Tentukan nilai kebenaran (Benar atau Salah) dari setiap pernyataan sains berikut:',
            'options_json' => [
                'Air murni mendidih pada suhu 100 derajat Celsius pada tekanan 1 atm.',
                'Matahari bergerak mengelilingi bumi sebagai pusat tata surya.',
                'Tumbuhan membutuhkan cahaya matahari dan karbon dioksida untuk proses fotosintesis.'
            ],
            'correct_answer_json' => ['benar', 'salah', 'benar'],
            'correct_answer' => 'benar,salah,benar',
            'score_weight' => 20,
        ]);

        // Soal 4: Menjodohkan (Matching Pair)
        $q4 = Question::create([
            'exam_id' => $examMulti->id,
            'type' => 'menjodohkan',
            'question_text' => 'Pasangkan nama negara di sebelah kiri dengan nama ibu kotanya yang tepat di sebelah kanan:',
            'options_json' => [
                'left' => ['Indonesia', 'Jepang', 'Malaysia', 'Prancis'],
                'right' => ['Tokyo', 'Jakarta', 'Paris', 'Kuala Lumpur']
            ],
            'correct_answer_json' => [
                '0' => 'Jakarta',
                '1' => 'Tokyo',
                '2' => 'Kuala Lumpur',
                '3' => 'Paris'
            ],
            'correct_answer' => 'menjodohkan',
            'score_weight' => 20,
        ]);

        // Soal 5: Essay / Uraian
        $q5 = Question::create([
            'exam_id' => $examMulti->id,
            'type' => 'essay',
            'question_text' => 'Jelaskan mengapa kelestarian lingkungan hidup dan pemanfaatan energi terbarukan sangat penting bagi kehidupan generasi masa depan!',
            'options_json' => [
                'sample_answer' => 'Kelestarian lingkungan hidup penting untuk mencegah krisis iklim, menjaga ketersediaan air bersih, keseimbangan ekosistem, serta memastikan keberlanjutan sumber daya bagi generasi mendatang.'
            ],
            'correct_answer' => 'Kelestarian lingkungan hidup penting untuk mencegah krisis iklim...',
            'score_weight' => 20,
        ]);

        // ----------------------------------------------------
        // 2. Kuis Latihan Hitungan & Logika Matematika
        // Guru Pengampu: Drs. Budi Santoso, M.Pd.
        // ----------------------------------------------------
        $examMath = Exam::create([
            'title' => 'Kuis Latihan Hitungan & Logika Matematika',
            'description' => 'Kuis latihan hitungan cepat dan logika matematika.',
            'subject_name' => 'Matematika',
            'teacher_id' => $guruMath?->id,
            'class_id' => $classId,
            'lms_chapter_id' => $topicMath?->lms_chapter_id,
            'lms_topic_id' => $topicMath?->id,
            'duration_minutes' => 30,
            'start_time' => now()->subDays(2),
            'end_time' => now()->addDays(5),
            'is_published' => true,
        ]);

        Question::create([
            'exam_id' => $examMath->id,
            'type' => 'pg',
            'question_text' => 'Berapakah hasil dari 25 x 4 + 50 - 20?',
            'option_a' => '130',
            'option_b' => '120',
            'option_c' => '140',
            'option_d' => '150',
            'option_e' => '110',
            'correct_answer' => 'a',
            'score_weight' => 50,
        ]);

        Question::create([
            'exam_id' => $examMath->id,
            'type' => 'pg',
            'question_text' => 'Jika akar kuadrat dari x adalah 9, berapakah nilai x?',
            'option_a' => '18',
            'option_b' => '27',
            'option_c' => '81',
            'option_d' => '72',
            'option_e' => '36',
            'correct_answer' => 'c',
            'score_weight' => 50,
        ]);

        // ----------------------------------------------------
        // 3. Ujian UTS Bahasa & Literasi (Demokan status needs_grading)
        // Guru Pengampu: Dewi Lestari, S.Hum.
        // ----------------------------------------------------
        $examBahasa = Exam::create([
            'title' => 'UTS Bahasa Indonesia & Literasi Budaya',
            'description' => 'Ujian Tengah Semester Bahasa Indonesia dengan penilaian essay dan penulisan tata bahasa.',
            'subject_name' => 'Bahasa Indonesia',
            'teacher_id' => $guruBahasa?->id,
            'class_id' => $classId,
            'duration_minutes' => 45,
            'start_time' => now()->subDays(3),
            'end_time' => now()->addDays(10),
            'is_published' => true,
        ]);

        $qB1 = Question::create([
            'exam_id' => $examBahasa->id,
            'type' => 'pg',
            'question_text' => 'Kalimat berikut yang merupakan ide pokok paragraf induktif adalah...',
            'option_a' => 'Oleh karena itu, penting bagi kita menjaga kebersihan lingkungan.',
            'option_b' => 'Pendidikan adalah modal utama bangsa.',
            'option_c' => 'Ada banyak cara menjaga kesehatan tubuh.',
            'option_d' => 'Musim hujan di Indonesia dimulai pada bulan Oktober.',
            'option_e' => 'Membaca buku dapat menambah wawasan.',
            'correct_answer' => 'a',
            'score_weight' => 40,
        ]);

        $qB2 = Question::create([
            'exam_id' => $examBahasa->id,
            'type' => 'essay',
            'question_text' => 'Tuliskan satu paragraf argumentasi mengenai peranan literasi digital dalam membendung penyebaran berita bohong (hoaks)!',
            'options_json' => [
                'sample_answer' => 'Literasi digital membekali siswa kemampuan berpikir kritis dalam memverifikasi informasi dari sumber terpercaya sebelum membagikannya.'
            ],
            'correct_answer' => 'Literasi digital membekali siswa...',
            'score_weight' => 60,
        ]);

    }

    /**
     * Create a dummy image with text dynamically and save to public disk.
     */
    private function createDummyImage(string $filename, string $text): string
    {
        $path = 'questions/' . $filename;
        
        if (!Storage::disk('public')->exists('questions')) {
            Storage::disk('public')->makeDirectory('questions');
        }

        if (extension_loaded('gd')) {
            $width = 600;
            $height = 350;
            $image = imagecreatetruecolor($width, $height);
            
            $bgColor = imagecolorallocate($image, 79, 70, 229); // Indigo-600
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $gridColor = imagecolorallocate($image, 99, 102, 241); // Indigo-500
            
            imagefill($image, 0, 0, $bgColor);
            
            for ($i = 0; $i < $width; $i += 40) {
                imageline($image, $i, 0, $i, $height, $gridColor);
            }
            for ($j = 0; $j < $height; $j += 40) {
                imageline($image, 0, $j, $width, $j, $gridColor);
            }
            
            imagefilledellipse($image, $width / 2, $height / 2, 220, 220, imagecolorallocate($image, 67, 56, 202));
            
            $fontSize = 5;
            $textWidth = imagefontwidth($fontSize) * strlen($text);
            $textHeight = imagefontheight($fontSize);
            $x = ($width - $textWidth) / 2;
            $y = ($height - $textHeight) / 2;
            imagestring($image, $fontSize, $x, $y, $text, $textColor);
            
            $subtext = "Soal Ujian CBT - Sekolah LRV";
            $subtextWidth = imagefontwidth(3) * strlen($subtext);
            imagestring($image, 3, ($width - $subtextWidth) / 2, $y + 30, $subtext, imagecolorallocate($image, 199, 210, 254));
            
            ob_start();
            imagepng($image);
            $imageData = ob_get_clean();
            imagedestroy($image);
            
            Storage::disk('public')->put($path, $imageData);
        } else {
            $logoPath = public_path('img/logo.png');
            if (file_exists($logoPath)) {
                Storage::disk('public')->put($path, file_get_contents($logoPath));
            } else {
                $dummyPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
                Storage::disk('public')->put($path, $dummyPng);
            }
        }
        
        return $path;
    }
}
