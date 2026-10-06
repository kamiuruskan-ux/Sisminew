<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Grade;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RaportController extends Controller
{
    /**
     * Display listing of classes and students for printing report cards.
     */
    public function index(Request $request)
    {
        $classes = ClassModel::withCount('students')->orderBy('name', 'asc')->get();
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $activeAcademicYear = AcademicYear::getActive() ?? $academicYears->first();

        $query = Student::with(['user', 'class', 'grades']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->orderBy('nisn', 'asc')->paginate(20)->withQueryString();
        $selectedClass = $request->filled('class_id') ? ClassModel::find($request->class_id) : null;

        return view('admin.raport.index', compact('students', 'classes', 'academicYears', 'activeAcademicYear', 'selectedClass'));
    }

    /**
     * Render printable official student report cards (single or bulk).
     */
    public function print(Request $request)
    {
        $query = Student::with(['user', 'class', 'grades', 'attendances']);

        if ($request->filled('student_ids')) {
            $rawIds = is_array($request->student_ids) ? $request->student_ids : explode(',', $request->student_ids);
            $ids = array_map(function ($id) {
                return decrypt_id($id);
            }, array_filter($rawIds));
            $query->whereIn('id', array_filter($ids));
        } elseif ($request->filled('student_id')) {
            $studentId = decrypt_id($request->student_id);
            $query->where('id', $studentId);
        } elseif ($request->filled('class_id')) {
            $classId = decrypt_id($request->class_id);
            $query->where('class_id', $classId);
        } else {
            return redirect()->route('admin.raport.index')->with('error', 'Pilih minimal satu siswa atau kelas untuk mencetak raport.');
        }

        $students = $query->orderBy('nisn', 'asc')->get();

        if ($students->isEmpty()) {
            return redirect()->route('admin.raport.index')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Academic Year & Semester
        $selectedAcademicYearId = $request->input('academic_year_id');
        $academicYear = $selectedAcademicYearId ? AcademicYear::find($selectedAcademicYearId) : AcademicYear::getActive();
        $semester = $request->input('semester', $academicYear?->semester ?? 'Ganjil');

        // Prepare structured report card data for each student
        $raportData = [];
        foreach ($students as $student) {
            $raportData[] = $this->buildStudentRaportData($student, $academicYear, $semester);
        }

        // Raport Settings with SDIT Al-Fahmi Palu defaults
        $raportSettings = [
            'school_name' => Setting::get('school_name', 'SDIT AL-FAHMI'),
            'school_address' => Setting::get('school_address', 'Jl. Lembu No. 02, Kota Palu, Sulawesi Tengah'),
            'school_city' => Setting::get('school_city', 'Palu'),
            'school_province' => Setting::get('school_province', 'Sulawesi Tengah'),
            'school_postal_code' => Setting::get('school_postal_code', '94236'),
            'school_phone' => Setting::get('school_phone', '081234567890'),
            'school_email' => Setting::get('school_email', 'info@sditalfahmi-palu.com'),
            'school_website' => Setting::get('school_website', 'www.sditalfahmi-palu.com'),
            'school_logo' => Setting::get('letterhead_logo_path') ? asset(Setting::get('letterhead_logo_path')) : Setting::getLogoUrl(),
            'school_logo_right' => Setting::get('letterhead_logo_right_path') ? asset(Setting::get('letterhead_logo_right_path')) : null,
            'letterhead_header_top' => Setting::get('letterhead_header_top', 'YAYASAN AL-FAHMI PALU'),
            'letterhead_sub' => Setting::get('letterhead_sub', 'SEKOLAH DASAR ISLAM TERPADU (SDIT) AL-FAHMI'),
            'header_title' => Setting::get('raport_header_title', 'RAPORT HASIL BELAJAR SISWA'),
            'city' => Setting::get('raport_place', Setting::get('school_city', 'Palu')),
            'date' => Setting::get('raport_date', date('d F Y')),
            'principal_name' => Setting::get('school_principal_name', Setting::get('raport_principal_name', Setting::get('school_principal', 'Kepala Sekolah'))),
            'principal_nip' => Setting::get('school_principal_nip', Setting::get('raport_principal_nip', '-')),
            'stamp_path' => Setting::get('raport_stamp_path', Setting::get('student_card_stamp_path')) ? asset(Setting::get('raport_stamp_path', Setting::get('student_card_stamp_path'))) : null,
            'signature_path' => Setting::get('raport_signature_path', Setting::get('student_card_signature_path')) ? asset(Setting::get('raport_signature_path', Setting::get('student_card_signature_path'))) : null,
            'default_note' => Setting::get('raport_default_note', 'Tingkatkan terus semangat belajar, adab islami, dan pertahankan prestasi Anda.'),
            'weight_daily' => (float) Setting::get('raport_weight_daily', 40),
            'weight_mid' => (float) Setting::get('raport_weight_mid', 30),
            'weight_final' => (float) Setting::get('raport_weight_final', 30),
        ];

        return view('admin.raport.print', compact('raportData', 'academicYear', 'semester', 'raportSettings'));
    }

    /**
     * Show report card configuration & setting page.
     */
    public function settings()
    {
        $sampleStudent = Student::with(['user', 'class'])->first();
        return view('admin.raport.settings', compact('sampleStudent'));
    }

    /**
     * Save report card settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'raport_header_title' => 'nullable|string|max:255',
            'raport_place' => 'nullable|string|max:100',
            'raport_date' => 'nullable|string|max:100',
            'raport_principal_name' => 'nullable|string|max:255',
            'raport_principal_nip' => 'nullable|string|max:100',
            'raport_weight_daily' => 'nullable|numeric|min:0|max:100',
            'raport_weight_mid' => 'nullable|numeric|min:0|max:100',
            'raport_weight_final' => 'nullable|numeric|min:0|max:100',
            'raport_default_note' => 'nullable|string',
            'raport_note_a' => 'nullable|string',
            'raport_note_b' => 'nullable|string',
            'raport_note_c' => 'nullable|string',
            'raport_note_d' => 'nullable|string',
            'raport_stamp' => 'nullable|image|max:2048',
            'raport_signature' => 'nullable|image|max:2048',
        ]);

        Setting::set('raport_header_title', $request->input('raport_header_title', 'RAPORT HASIL BELAJAR SISWA'));
        Setting::set('raport_place', $request->input('raport_place', 'Palu'));
        Setting::set('raport_date', $request->input('raport_date', date('d F Y')));
        Setting::set('raport_principal_name', $request->input('raport_principal_name'));
        Setting::set('raport_principal_nip', $request->input('raport_principal_nip'));
        Setting::set('raport_weight_daily', $request->input('raport_weight_daily', 40));
        Setting::set('raport_weight_mid', $request->input('raport_weight_mid', 30));
        Setting::set('raport_weight_final', $request->input('raport_weight_final', 30));
        Setting::set('raport_default_note', $request->input('raport_default_note'));
        Setting::set('raport_note_a', $request->input('raport_note_a'));
        Setting::set('raport_note_b', $request->input('raport_note_b'));
        Setting::set('raport_note_c', $request->input('raport_note_c'));
        Setting::set('raport_note_d', $request->input('raport_note_d'));

        // Handle Upload Stempel
        if ($request->hasFile('raport_stamp')) {
            $oldPath = Setting::get('raport_stamp_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            $file = $request->file('raport_stamp');
            $fileName = 'raport_stamp_' . time() . '.' . $file->getClientOriginalExtension();
            $savedStamp = save_uploaded_public_file($file, 'img/cards', $fileName);
            Setting::set('raport_stamp_path', 'img/cards/' . basename($savedStamp));
        } elseif ($request->boolean('delete_raport_stamp')) {
            $oldPath = Setting::get('raport_stamp_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            Setting::set('raport_stamp_path', null);
        }

        // Handle Upload TTD Digital
        if ($request->hasFile('raport_signature')) {
            $oldPath = Setting::get('raport_signature_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            $file = $request->file('raport_signature');
            $fileName = 'raport_signature_' . time() . '.' . $file->getClientOriginalExtension();
            $savedSig = save_uploaded_public_file($file, 'img/cards', $fileName);
            Setting::set('raport_signature_path', 'img/cards/' . basename($savedSig));
        } elseif ($request->boolean('delete_raport_signature')) {
            $oldPath = Setting::get('raport_signature_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            Setting::set('raport_signature_path', null);
        }

        return back()->with('success', 'Pengaturan template cetak raport & bobot penilaian berhasil diperbarui.');
    }

    /**
     * Download Leger Nilai 1 Kelas (Format Excel Matriks)
     */
    public function exportLeger(Request $request)
    {
        $classId = $request->get('class_id');
        if (!$classId) {
            return back()->with('error', 'Pilih kelas terlebih dahulu untuk mengunduh Leger Nilai.');
        }

        $class = ClassModel::findOrFail($classId);
        $academicYear = $request->filled('academic_year_id') 
            ? AcademicYear::find($request->academic_year_id) 
            : AcademicYear::getActive();
        $semester = $request->get('semester', 'Ganjil');

        $students = Student::with(['user', 'attendances'])
            ->where('class_id', $classId)
            ->orderBy('nisn', 'asc')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Tidak ada santri terdaftar di kelas ini.');
        }

        // Ambil semua grades siswa kelas ini
        $allStudentIds = $students->pluck('id')->toArray();
        $grades = Grade::whereIn('student_id', $allStudentIds)->get();

        // Daftar mapel unik yang memiliki nilai di kelas ini
        $subjects = $grades->pluck('subject')->filter()->unique()->values()->toArray();
        if (empty($subjects)) {
            $subjects = [
                'Pendidikan Agama Islam & Budi Pekerti',
                'Pendidikan Pancasila',
                'Bahasa Indonesia',
                'Matematika',
                'IPAS',
                'PJOK',
                'Seni Budaya',
                'Bahasa Inggris',
                'Bahasa Arab',
                'Tahsin & Tahfidz',
            ];
        }

        // Bobot
        $wDaily = (float) Setting::get('raport_weight_daily', 40);
        $wMid = (float) Setting::get('raport_weight_mid', 30);
        $wFinal = (float) Setting::get('raport_weight_final', 30);
        $totalW = ($wDaily + $wMid + $wFinal) ?: 100;

        // Hitung matriks nilai per siswa
        $studentRows = [];
        foreach ($students as $st) {
            $stGrades = $grades->where('student_id', $st->id);
            $scoresBySub = [];
            $totalScore = 0;
            $subCount = 0;

            foreach ($subjects as $sub) {
                $subGrades = $stGrades->where('subject', $sub);
                $daily = $subGrades->where('type', 'daily')->avg('score');
                $mid = $subGrades->where('type', 'mid_term')->avg('score');
                $final = $subGrades->whereIn('type', ['final_term', 'exam'])->avg('score');

                if ($daily !== null && $mid !== null && $final !== null) {
                    $finalScore = round((($daily * $wDaily) + ($mid * $wMid) + ($final * $wFinal)) / $totalW, 1);
                } elseif ($daily !== null && $final !== null) {
                    $finalScore = round((($daily * $wDaily) + ($final * $wFinal)) / ($wDaily + $wFinal), 1);
                } elseif ($subGrades->isNotEmpty()) {
                    $finalScore = round($subGrades->avg('score'), 1);
                } else {
                    $finalScore = null;
                }

                $scoresBySub[$sub] = $finalScore;
                if ($finalScore !== null) {
                    $totalScore += $finalScore;
                    $subCount++;
                }
            }

            $avgScore = $subCount > 0 ? round($totalScore / $subCount, 1) : 0;
            $atts = $st->attendances ?? collect();
            $sakit = $atts->where('status', 'sick')->count();
            $izin = $atts->whereIn('status', ['excused', 'permit', 'permission', 'izin'])->count();
            $alpa = $atts->where('status', 'absent')->count();

            $studentRows[] = [
                'student' => $st,
                'scores' => $scoresBySub,
                'total' => $totalScore,
                'average' => $avgScore,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
            ];
        }

        // Tentukan ranking berdasarkan total nilai
        usort($studentRows, fn($a, $b) => $b['total'] <=> $a['total']);
        foreach ($studentRows as $rank => &$row) {
            $row['rank'] = $rank + 1;
        }
        unset($row);

        // Urutkan kembali berdasarkan nama / NISN untuk cetak rapi
        usort($studentRows, fn($a, $b) => strcmp($a['student']->user?->name ?? '', $b['student']->user?->name ?? ''));

        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leger ' . substr($class->name, 0, 20));

        // Judul Leger
        $sheet->mergeCells('A1:Z1');
        $sheet->setCellValue('A1', 'LEGER NILAI HASIL BELAJAR SISWA - ' . Setting::get('school_name', 'SDIT AL-FAHMI PALU'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:Z2');
        $sheet->setCellValue('A2', "Kelas: {$class->name} | Tahun Ajaran: {$academicYear?->name} | Semester: {$semester}");
        $sheet->getStyle('A2')->getFont()->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Tabel (Baris 4)
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'NISN');
        $sheet->setCellValue('C4', 'Nama Siswa');
        $sheet->setCellValue('D4', 'L/P');

        $colIdx = 5; // Kolom E
        foreach ($subjects as $sub) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue($colLetter . '4', $sub);
            $colIdx++;
        }

        $colTotal = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
        $colAvg = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
        $colRank = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
        $colS = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
        $colI = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
        $colA = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);

        $sheet->setCellValue($colTotal . '4', 'Jumlah');
        $sheet->setCellValue($colAvg . '4', 'Rata-rata');
        $sheet->setCellValue($colRank . '4', 'Peringkat');
        $sheet->setCellValue($colS . '4', 'S');
        $sheet->setCellValue($colI . '4', 'I');
        $sheet->setCellValue($colA . '4', 'A');

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx - 1);
        $sheet->getStyle("A4:{$lastColLetter}4")->getFont()->setBold(true);
        $sheet->getStyle("A4:{$lastColLetter}4")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2E8F0');
        $sheet->getStyle("A4:{$lastColLetter}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Isi Data Siswa (Baris 5 dst)
        $rowIdx = 5;
        foreach ($studentRows as $idx => $row) {
            $st = $row['student'];
            $sheet->setCellValue("A{$rowIdx}", $idx + 1);
            $sheet->setCellValue("B{$rowIdx}", $st->nisn ?? $st->nis ?? '-');
            $sheet->setCellValue("C{$rowIdx}", $st->user?->name ?? 'Siswa');
            $sheet->setCellValue("D{$rowIdx}", strtoupper(substr($st->gender ?? '-', 0, 1)));

            $cIdx = 5;
            foreach ($subjects as $sub) {
                $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx);
                $val = $row['scores'][$sub] ?? null;
                $sheet->setCellValue($cLetter . $rowIdx, $val !== null ? $val : '-');
                $sheet->getStyle($cLetter . $rowIdx)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $cIdx++;
            }

            $sheet->setCellValue($colTotal . $rowIdx, $row['total']);
            $sheet->setCellValue($colAvg . $rowIdx, $row['average']);
            $sheet->setCellValue($colRank . $rowIdx, $row['rank']);
            $sheet->setCellValue($colS . $rowIdx, $row['sakit']);
            $sheet->setCellValue($colI . $rowIdx, $row['izin']);
            $sheet->setCellValue($colA . $rowIdx, $row['alpa']);

            $sheet->getStyle("{$colTotal}{$rowIdx}:{$lastColLetter}{$rowIdx}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$colRank}{$rowIdx}")->getFont()->setBold(true);

            $rowIdx++;
        }

        // Border Tabel
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCBD5E1'],
                ],
            ],
        ];
        $sheet->getStyle("A4:{$lastColLetter}" . ($rowIdx - 1))->applyFromArray($styleBorder);

        // Auto width
        for ($i = 1; $i < $colIdx; $i++) {
            $colL = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colL)->setAutoSize(true);
        }

        $fileName = 'Leger_Nilai_' . Str::slug($class->name) . '_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Helper to process student grades, attendance, and generate report summary.
     */
    private function buildStudentRaportData(Student $student, ?AcademicYear $academicYear, string $semester): array
    {
        $grades = Grade::where('student_id', $student->id)->get();
        $groupedGrades = $grades->groupBy('subject');

        $subjectResults = [];
        $totalFinalScore = 0;
        $subjectCount = 0;

        // Dynamic Weights from Setting
        $wDaily = (float) Setting::get('raport_weight_daily', 40);
        $wMid = (float) Setting::get('raport_weight_mid', 30);
        $wFinal = (float) Setting::get('raport_weight_final', 30);
        $totalWeight = ($wDaily + $wMid + $wFinal) ?: 100;

        foreach ($groupedGrades as $subjectName => $subjectGrades) {
            $dailyScores = $subjectGrades->where('type', 'daily')->pluck('score');
            $midScores = $subjectGrades->where('type', 'mid_term')->pluck('score');
            $finalScores = $subjectGrades->whereIn('type', ['final_term', 'exam'])->pluck('score');

            $avgDaily = $dailyScores->isNotEmpty() ? round($dailyScores->avg(), 1) : null;
            $avgMid = $midScores->isNotEmpty() ? round($midScores->avg(), 1) : null;
            $avgFinal = $finalScores->isNotEmpty() ? round($finalScores->avg(), 1) : null;

            // Calculate Final Score (Rata-Rata / Bobot Dinamis)
            if ($avgDaily !== null && $avgMid !== null && $avgFinal !== null) {
                $finalScore = round((($avgDaily * $wDaily) + ($avgMid * $wMid) + ($avgFinal * $wFinal)) / $totalWeight, 1);
            } elseif ($avgDaily !== null && $avgFinal !== null) {
                $finalScore = round((($avgDaily * $wDaily) + ($avgFinal * $wFinal)) / ($wDaily + $wFinal), 1);
            } else {
                $finalScore = round($subjectGrades->avg('score'), 1);
            }

            // Determine Predicate & Competency Description
            $predicate = $this->calculatePredicate($finalScore);
            $description = $this->generateDescription($subjectName, $predicate['letter']);

            $subjectResults[] = [
                'subject' => $subjectName,
                'daily' => $avgDaily ?? '-',
                'mid_term' => $avgMid ?? '-',
                'final_term' => $avgFinal ?? '-',
                'final_score' => $finalScore,
                'letter' => $predicate['letter'],
                'predicate' => $predicate['label'],
                'description' => $description,
            ];

            $totalFinalScore += $finalScore;
            $subjectCount++;
        }

        $overallAverage = $subjectCount > 0 ? round($totalFinalScore / $subjectCount, 1) : 0;

        // Attendance summary
        $attendances = Attendance::where('student_id', $student->id)->get();
        $attendanceSummary = [
            'present' => $attendances->whereIn('status', ['present', 'late'])->count(),
            'sick' => $attendances->where('status', 'sick')->count(),
            'permit' => $attendances->whereIn('status', ['excused', 'permit', 'permission', 'izin'])->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
        ];

        // Ekstrakurikuler Wajib & Pilihan Khas SDIT
        $extracurriculars = [
            [
                'name' => 'Kepanduan Pramuka SIT',
                'predicate' => 'A (Sangat Baik)',
                'description' => 'Disiplin, mandiri, dan berjiwa kepemimpinan dalam kegiatan kepanduan islami.',
            ],
            [
                'name' => 'Olahraga Sunnah (Panahan / Futsal)',
                'predicate' => 'A (Sangat Baik)',
                'description' => 'Menunjukkan sportivitas, fokus, ketangkasan, dan kerjasama tim yang baik.',
            ],
            [
                'name' => 'Khat & Seni Kaligrafi Islam',
                'predicate' => 'B (Baik)',
                'description' => 'Mampu menulis kaidah khat hijaiyah dengan rapi, teliti, dan tekun.',
            ],
        ];

        // Dimensi Perkembangan Karakter Islami (P5 & Profil Santri SIT)
        $characterDevelopment = [
            [
                'dimension' => 'Beriman, Bertakwa & Berakhlak Mulia',
                'predicate' => 'Sangat Baik',
                'notes' => 'Rajin sholat dhuha & dzuhur berjamaah, bersikap sopan santun kepada asatidz dan teman.',
            ],
            [
                'dimension' => 'Kemandirian & Tanggung Jawab',
                'predicate' => 'Baik',
                'notes' => 'Menuntaskan tugas pembelajaran secara mandiri dan disiplin merawat perlengkapan sekolah.',
            ],
            [
                'dimension' => 'Gotong Royong & Kepedulian Lingkungan',
                'predicate' => 'Sangat Baik',
                'notes' => 'Aktif dalam piket kelas, senang membantu teman, dan menjaga kebersihan lingkungan sekolah.',
            ],
        ];

        // Keputusan Kenaikan Kelas / Kelulusan (Semester Genap)
        $gradeNumber = $student->class?->grade ?: (preg_match('/^(\d+)/', $student->class?->name ?? '', $m) ? (int)$m[1] : 1);
        $isSemesterGenap = (strtolower($semester) === 'genap');
        $promotionDecision = null;

        if ($isSemesterGenap) {
            if ($gradeNumber >= 6) {
                $promotionDecision = 'LULUS dari SDIT Al-Fahmi Palu';
            } else {
                $nextGrade = $gradeNumber + 1;
                $spelledGrade = match($nextGrade) {
                    2 => 'Dua',
                    3 => 'Tiga',
                    4 => 'Empat',
                    5 => 'Lima',
                    6 => 'Enam',
                    default => (string)$nextGrade,
                };
                $promotionDecision = "NAIK KE KELAS {$nextGrade} ({$spelledGrade})";
            }
        }

        // Dynamic personalized teacher note based on overall average score
        if ($overallAverage >= 88) {
            $dynamicNote = Setting::get('raport_note_a', "Selamat atas pencapaian hasil belajar yang sangat istimewa! Pertahankan ketekunan, adab islami, dan motivasi belajarmu.");
        } elseif ($overallAverage >= 78) {
            $dynamicNote = Setting::get('raport_note_b', "Capaian hasil belajar ananda sudah baik dan konsisten. Tingkatkan terus ketelitian dan keaktifan di semester berikutnya.");
        } elseif ($overallAverage >= 68) {
            $dynamicNote = Setting::get('raport_note_c', "Hasil belajar ananda cukup baik. Perbanyak latihan mandiri dan tingkatkan kedisiplinan dalam mengulang pelajaran di rumah.");
        } else {
            $dynamicNote = Setting::get('raport_note_d', "Memerlukan bimbingan serta semangat belajar yang lebih giat. Tingkatkan waktu belajar dan selalu berkonsultasi dengan ustadz/ustadzah.");
        }

        $customNote = Setting::get('raport_default_note');
        $teacherNote = (!empty($customNote) && $customNote !== 'Tingkatkan terus semangat belajar dan pertahankan prestasi Anda.')
            ? $customNote
            : $dynamicNote;

        return [
            'student' => $student,
            'subjects' => $subjectResults,
            'overall_average' => $overallAverage,
            'attendance' => $attendanceSummary,
            'extracurriculars' => $extracurriculars,
            'character_development' => $characterDevelopment,
            'promotion_decision' => $promotionDecision,
            'teacher_note' => $teacherNote,
        ];
    }

    private function calculatePredicate(float $score): array
    {
        if ($score >= 88) {
            return ['letter' => 'A', 'label' => 'Sangat Baik'];
        } elseif ($score >= 78) {
            return ['letter' => 'B', 'label' => 'Baik'];
        } elseif ($score >= 68) {
            return ['letter' => 'C', 'label' => 'Cukup'];
        } else {
            return ['letter' => 'D', 'label' => 'Perlu Bimbingan'];
        }
    }

    private function generateDescription(string $subject, string $letter): string
    {
        switch ($letter) {
            case 'A':
                return "Sangat menguasai seluruh capaian pembelajaran mata pelajaran {$subject} dengan hasil yang sangat memuaskan.";
            case 'B':
                return "Menguasai capaian pembelajaran mata pelajaran {$subject} dengan baik dan konsisten.";
            case 'C':
                return "Cukup menguasai capaian pembelajaran mata pelajaran {$subject}, perlu peningkatan latihan mandiri.";
            default:
                return "Memerlukan bimbingan tambahan dan perhatian lebih dalam memahami materi {$subject}.";
        }
    }
}
