<?php

namespace Database\Seeders;

use App\Models\BkAssessment;
use App\Models\BkCounseling;
use App\Models\BkStudentViolation;
use App\Models\BkViolationCategory;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class BkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Role Guru BK exists
        $guruBkRole = Role::firstOrCreate(['slug' => 'guru-bk'], [
            'name' => 'Guru BK',
            'description' => 'Bimbingan & Konseling — counseling services, student violations, assessments',
            'is_active' => true,
        ]);

        // 2. Ensure Guru BK Users exist
        $bkUsers = [
            [
                'name' => 'Drs. Bambang Sujatmiko, S.Psi., M.Pd.',
                'email' => 'guru.bk@sekolah.id',
                'phone' => '081234567881',
                'nip' => '19830412 200901 1 004',
            ],
            [
                'name' => 'Siti Rahmawati, S.Psi., M.Psi.',
                'email' => 'bk@sekolah.id',
                'phone' => '081234567882',
                'nip' => '19880721 201402 2 008',
            ],
        ];

        $counselors = [];
        foreach ($bkUsers as $userData) {
            $counselor = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'phone' => $userData['phone'],
                    'nip' => $userData['nip'],
                    'status' => 'active',
                ]
            );

            if ($guruBkRole && !$counselor->hasRole('guru-bk')) {
                $counselor->assignRole($guruBkRole);
            }

            $counselors[] = $counselor;
        }

        $counselorPrimary = $counselors[0] ?? User::first();
        $counselorSecondary = $counselors[1] ?? $counselorPrimary;

        // 3. Ensure Violation Categories exist
        $this->call(BkViolationSeeder::class);
        $categories = BkViolationCategory::all();

        // 4. Get available Students
        $students = Student::with('class')->limit(10)->get();

        if ($students->isEmpty()) {
            return;
        }

        $student1 = $students->get(0);
        $student2 = $students->get(1) ?? $student1;
        $student3 = $students->get(2) ?? $student1;
        $student4 = $students->get(3) ?? $student1;
        $student5 = $students->get(4) ?? $student1;

        // =========================================================
        // 5. Seed Sesi Layanan Konseling (BkCounseling)
        // =========================================================
        $counselings = [
            [
                'student_id' => $student1->id,
                'counselor_id' => $counselorPrimary->id,
                'category' => 'belajar',
                'service_type' => 'individu',
                'title' => 'Konseling Peningkatan Motivasi & Manajemen Waktu Belajar',
                'date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'time' => '09:30:00',
                'place' => 'Ruang Bimbingan Konseling 1',
                'complaint_notes' => 'Siswa merasa kesulitan membagi waktu antara kegiatan ekstrakurikuler dan persiapan ujian semester.',
                'action_plan' => 'Menyusun jadwal kegiatan harian (time-table), membatasi jam e-sport malam hari, dan mentoring berkala 1 minggu sekali.',
                'follow_up_notes' => 'Siswa menunjukkan komitmen tinggi dan nilai tugas harian mulai membaik.',
                'status' => 'completed',
                'is_confidential' => false,
            ],
            [
                'student_id' => $student2->id,
                'counselor_id' => $counselorSecondary->id,
                'category' => 'karier',
                'service_type' => 'individu',
                'title' => 'Bimbingan Konsultasi Perguruan Tinggi Kedinasan & SNBP',
                'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'time' => '11:00:00',
                'place' => 'Ruang Konseling Utama',
                'complaint_notes' => 'Siswa ragu menentukan pilihan prodi di PTN dan membutuhkan analisis rasionalisasi nilai rapor semester 1-5.',
                'action_plan' => 'Melakukan pemetaan nilai mata pelajaran pendukung, simulasi portofolio, dan konsultasi bersama orang tua.',
                'follow_up_notes' => 'Siswa telah memantapkan pilihan pada Jurusan Teknik Informatika / Sistem Informasi.',
                'status' => 'completed',
                'is_confidential' => false,
            ],
            [
                'student_id' => $student3->id,
                'counselor_id' => $counselorPrimary->id,
                'category' => 'kedisiplinan',
                'service_type' => 'individu',
                'title' => 'Sesi Penanganan Pembinaan Keterlambatan Berulang',
                'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'time' => '07:30:00',
                'place' => 'Ruang BK - Meja Mediasi',
                'complaint_notes' => 'Siswa tercatat terlambat masuk sekolah sebanyak 4 kali dalam kurun waktu dua minggu.',
                'action_plan' => 'Penandatanganan komitmen kedisiplinan dan koordinasi wali kelas untuk pemantauan jam tidur malam.',
                'follow_up_notes' => 'Dalam proses pemantauan harian oleh tim piket BK.',
                'status' => 'in_progress',
                'is_confidential' => false,
            ],
            [
                'student_id' => $student4->id,
                'counselor_id' => $counselorSecondary->id,
                'category' => 'pribadi',
                'service_type' => 'individu',
                'title' => 'Konseling Emosional & Adaptasi Lingkungan Sekolah',
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'time' => '10:00:00',
                'place' => 'Ruang Konseling Privat',
                'complaint_notes' => 'Siswa berkonsultasi mengenai rasa cemas saat presentasi di depan kelas.',
                'action_plan' => 'Latihan teknik relaksasi breathing, ekspresi percaya diri, dan simulasi pemaparan materi.',
                'follow_up_notes' => 'Jadwal sesi telah dikonfirmasi bersama wali kelas.',
                'status' => 'scheduled',
                'is_confidential' => true,
            ],
            [
                'student_id' => $student5->id,
                'counselor_id' => $counselorPrimary->id,
                'category' => 'sosial',
                'service_type' => 'kelompok',
                'title' => 'Bimbingan Kelompok Dinamika Pertemanan & Resolusi Konflik',
                'date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'time' => '13:00:00',
                'place' => 'Ruang Diskusi BK',
                'complaint_notes' => 'Membangun komunikasi efektif antar teman sebaya di kelas dan mencegah potensi salah paham.',
                'action_plan' => 'Permainan peran (roleplay) komunikasi asertif dan refleksi kelompok.',
                'follow_up_notes' => 'Hubungan antar anggota kelompok kembali harmonis dan saling mendukung.',
                'status' => 'completed',
                'is_confidential' => false,
            ],
        ];

        foreach ($counselings as $cData) {
            BkCounseling::firstOrCreate(
                [
                    'student_id' => $cData['student_id'],
                    'title' => $cData['title'],
                ],
                $cData
            );
        }

        // =========================================================
        // 6. Seed Catatan Pelanggaran Siswa (BkStudentViolation)
        // =========================================================
        $catTerlambat = $categories->firstWhere('level', 'ringan') ?? $categories->first();
        $catAtribut   = $categories->skip(1)->first() ?? $catTerlambat;
        $catBolos     = $categories->firstWhere('level', 'sedang') ?? $catTerlambat;
        $catRokok     = $categories->firstWhere('level', 'berat') ?? $catTerlambat;

        $violations = [
            [
                'student_id' => $student3->id,
                'violation_category_id' => $catTerlambat ? $catTerlambat->id : null,
                'counselor_id' => $counselorPrimary->id,
                'violation_date' => Carbon::now()->subDays(4)->format('Y-m-d'),
                'title' => 'Terlambat Masuk Sekolah (Pukul 07.35 WIB)',
                'points' => $catTerlambat->points ?? 5,
                'notes' => 'Siswa datang ke sekolah melewati batas toleransi gerbang tanpa membawa surat izin piket sah.',
                'penalty' => 'Peringatan lisan & pembinaan kedisiplinan oleh guru piket BK.',
                'status' => 'processed',
            ],
            [
                'student_id' => $student1->id,
                'violation_category_id' => $catAtribut ? $catAtribut->id : null,
                'counselor_id' => $counselorSecondary->id,
                'violation_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'title' => 'Atribut Seragam Tidak Lengkap (Tidak Memakai Dasi & Logo)',
                'points' => $catAtribut->points ?? 5,
                'notes' => 'Saat pemeriksaan kelengkapan seragam upacara hari Senin, siswa tidak memakai dasi resmi sekolah.',
                'penalty' => 'Teguran lisan & kewajiban melengkapi atribut pada pertemuan berikutnya.',
                'status' => 'resolved',
            ],
            [
                'student_id' => $student4->id,
                'violation_category_id' => $catBolos ? $catBolos->id : null,
                'counselor_id' => $counselorPrimary->id,
                'violation_date' => Carbon::now()->subDays(12)->format('Y-m-d'),
                'title' => 'Meninggalkan Jam Pelajaran Tanpa Surat Izin',
                'points' => $catBolos->points ?? 15,
                'notes' => 'Siswa tidak berada di ruang kelas saat pelajaran Matematika berlangsung tanpa pemberitahuan.',
                'penalty' => 'Surat pernyataan penyesalan & penugasan rangkuman materi pelajaran.',
                'status' => 'processed',
            ],
            [
                'student_id' => $student2->id,
                'violation_category_id' => $catRokok ? $catRokok->id : null,
                'counselor_id' => $counselorSecondary->id,
                'violation_date' => Carbon::now()->subDays(20)->format('Y-m-d'),
                'title' => 'Kedapatan Membawa Device Vape di Lingkungan Kantin Belakang',
                'points' => $catRokok->points ?? 35,
                'notes' => 'Tim ketertiban sekolah menemukan Pod Vape di dalam tas siswa saat inspeksi kedisiplinan berkala.',
                'penalty' => 'Pemanggilan orang tua/wali siswa & penerbitan Surat Peringatan (SP-1).',
                'status' => 'sp1',
            ],
        ];

        foreach ($violations as $vData) {
            BkStudentViolation::firstOrCreate(
                [
                    'student_id' => $vData['student_id'],
                    'title' => $vData['title'],
                ],
                $vData
            );
        }

        // =========================================================
        // 7. Seed Asesmen Minat Bakat & Karir (BkAssessment)
        // =========================================================
        $assessments = [
            [
                'student_id' => $student1->id,
                'counselor_id' => $counselorPrimary->id,
                'title' => 'Hasil Angket Minat & Pemetaan Potensi Jurusan PTN 2026',
                'type' => 'angket_minat',
                'dream_career' => 'Software Engineer / System Architect',
                'recommended_major' => 'Teknik Informatika / Rekayasa Perangkat Lunak - ITB / ITS',
                'strength_notes' => 'Memiliki daya logika analitis yang kuat, ketertarikan tinggi pada pemrograman komputer & matematika terapan.',
                'improvement_notes' => 'Perlu meningkatkan keterampilan komunikasi publik dan Bahasa Inggris akademis.',
            ],
            [
                'student_id' => $student2->id,
                'counselor_id' => $counselorSecondary->id,
                'title' => 'Laporan Psikotes & Tes Bakat Karir Siswa Tingkat Akhir',
                'type' => 'bakat_karier',
                'dream_career' => 'Akuntan Publik / Financial Analyst',
                'recommended_major' => 'Akuntansi / Manajemen Keuangan - Universitas Indonesia (UI)',
                'strength_notes' => 'Kemampuan numerik di atas rata-rata, teliti dalam pengolahan data, serta memiliki jiwa kepemimpinan.',
                'improvement_notes' => 'Perlu latihan manejemen stres saat menghadapi tenggat waktu (deadline).',
            ],
            [
                'student_id' => $student3->id,
                'counselor_id' => $counselorPrimary->id,
                'title' => 'Pemetaan Sosiometri & Dinamika Hubungan Antar Teman Klasikal',
                'type' => 'sosiometri',
                'dream_career' => 'Hubungan Internasional / Diplomat',
                'recommended_major' => 'Ilmu Hubungan Internasional - Universitas Gadjah Mada (UGM)',
                'strength_notes' => 'Mudah beradaptasi, ramah, dan menjadi tokoh populer yang menyatukan rekan-rekan sekelasnya.',
                'improvement_notes' => 'Pertahankan konsistensi kehadiran dan disiplin waktu harian.',
            ],
            [
                'student_id' => $student5->id,
                'counselor_id' => $counselorSecondary->id,
                'title' => 'Hasil Evaluasi Observasi Perilaku & Kepribadian Siswa',
                'type' => 'observasi_perilaku',
                'dream_career' => 'Desain Komunikasi Visual (DKV) / Creative Director',
                'recommended_major' => 'Desain Komunikasi Visual - ISI Yogyakarta / TELKOM University',
                'strength_notes' => 'Kreativitas seni visual sangat menonjol, mampu mengekspresikan gagasan estetika secara original.',
                'improvement_notes' => 'Tingkatkan keteraturan dalam mendokumentasikan portofolio karya secara tertata.',
            ],
        ];

        foreach ($assessments as $aData) {
            BkAssessment::firstOrCreate(
                [
                    'student_id' => $aData['student_id'],
                    'title' => $aData['title'],
                ],
                $aData
            );
        }
    }
}
