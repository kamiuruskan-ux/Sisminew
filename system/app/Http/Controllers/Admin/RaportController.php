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

        $query = Student::with(['user', 'class', 'major', 'grades']);

        if ($request->filled('major_id')) {
            $query->where('major_id', $request->major_id);
        }

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
        $query = Student::with(['user', 'class', 'major', 'grades', 'attendances']);

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

        // Raport Settings
        $raportSettings = [
            'school_name' => Setting::get('school_name', 'SMA Negeri 1 Contoh'),
            'school_address' => Setting::get('school_address', 'Jl. Pendidikan No. 123'),
            'school_city' => Setting::get('school_city', 'Jakarta Pusat'),
            'school_province' => Setting::get('school_province', 'DKI Jakarta'),
            'school_postal_code' => Setting::get('school_postal_code', '10110'),
            'school_phone' => Setting::get('school_phone', '(021) 1234567'),
            'school_email' => Setting::get('school_email', 'info@sekolah.sch.id'),
            'school_website' => Setting::get('school_website', 'www.sekolah.sch.id'),
            'school_logo' => Setting::get('letterhead_logo_path') ? asset(Setting::get('letterhead_logo_path')) : Setting::getLogoUrl(),
            'school_logo_right' => Setting::get('letterhead_logo_right_path') ? asset(Setting::get('letterhead_logo_right_path')) : null,
            'letterhead_header_top' => Setting::get('letterhead_header_top'),
            'letterhead_sub' => Setting::get('letterhead_sub'),
            'header_title' => Setting::get('raport_header_title', 'RAPORT HASIL BELAJAR SISWA'),
            'city' => Setting::get('raport_place', Setting::get('school_city', 'Jakarta')),
            'date' => Setting::get('raport_date', date('d F Y')),
            'principal_name' => Setting::get('school_principal_name', Setting::get('raport_principal_name', 'Kepala Sekolah, M.Pd.')),
            'principal_nip' => Setting::get('school_principal_nip', Setting::get('raport_principal_nip', '-')),
            'stamp_path' => Setting::get('raport_stamp_path', Setting::get('student_card_stamp_path')) ? asset(Setting::get('raport_stamp_path', Setting::get('student_card_stamp_path'))) : null,
            'signature_path' => Setting::get('raport_signature_path', Setting::get('student_card_signature_path')) ? asset(Setting::get('raport_signature_path', Setting::get('student_card_signature_path'))) : null,
            'default_note' => Setting::get('raport_default_note', 'Tingkatkan terus semangat belajar dan pertahankan prestasi Anda.'),
        ];

        return view('admin.raport.print', compact('raportData', 'academicYear', 'semester', 'raportSettings'));
    }

    /**
     * Show report card configuration & setting page.
     */
    public function settings()
    {
        $sampleStudent = Student::with(['user', 'class', 'major'])->first();
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
            'raport_default_note' => 'nullable|string',
            'raport_note_a' => 'nullable|string',
            'raport_note_b' => 'nullable|string',
            'raport_note_c' => 'nullable|string',
            'raport_note_d' => 'nullable|string',
            'raport_stamp' => 'nullable|image|max:2048',
            'raport_signature' => 'nullable|image|max:2048',
        ]);

        Setting::set('raport_header_title', $request->input('raport_header_title', 'RAPORT HASIL BELAJAR SISWA'));
        Setting::set('raport_place', $request->input('raport_place', 'Jakarta'));
        Setting::set('raport_date', $request->input('raport_date', date('d F Y')));
        Setting::set('raport_principal_name', $request->input('raport_principal_name'));
        Setting::set('raport_principal_nip', $request->input('raport_principal_nip'));
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

        return back()->with('success', 'Pengaturan template cetak raport berhasil diperbarui.');
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

        foreach ($groupedGrades as $subjectName => $subjectGrades) {
            $dailyScores = $subjectGrades->where('type', 'daily')->pluck('score');
            $midScores = $subjectGrades->where('type', 'mid_term')->pluck('score');
            $finalScores = $subjectGrades->whereIn('type', ['final_term', 'exam'])->pluck('score');

            $avgDaily = $dailyScores->isNotEmpty() ? round($dailyScores->avg(), 1) : null;
            $avgMid = $midScores->isNotEmpty() ? round($midScores->avg(), 1) : null;
            $avgFinal = $finalScores->isNotEmpty() ? round($finalScores->avg(), 1) : null;

            // Calculate Final Score (Rata-Rata / Bobot)
            if ($avgDaily !== null && $avgMid !== null && $avgFinal !== null) {
                $finalScore = round(($avgDaily * 0.4) + ($avgMid * 0.3) + ($avgFinal * 0.3), 1);
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

        // Dynamic personalized teacher note based on overall average score
        if ($overallAverage >= 88) {
            $dynamicNote = Setting::get('raport_note_a', "Selamat atas pencapaian hasil belajar yang sangat istimewa! Pertahankan ketekunan dan motivasi belajarmu.");
        } elseif ($overallAverage >= 78) {
            $dynamicNote = Setting::get('raport_note_b', "Capaian hasil belajar Anda sudah baik dan konsisten. Tingkatkan terus ketelitian dan keaktifan di semester berikutnya.");
        } elseif ($overallAverage >= 68) {
            $dynamicNote = Setting::get('raport_note_c', "Hasil belajar Anda cukup baik. Perbanyak latihan mandiri dan tingkatkan kedisiplinan dalam mengulas materi.");
        } else {
            $dynamicNote = Setting::get('raport_note_d', "Memerlukan bimbingan serta semangat belajar yang lebih giat. Tingkatkan waktu belajar dan selalu berkonsultasi dengan bapak/ibu guru.");
        }

        $customNote = Setting::get('raport_default_note');
        $teacherNote = (!empty($customNote) && $customNote !== 'Tingkatkan terus prestasi belajar Anda dan tetap semangat dalam menuntut ilmu.')
            ? $customNote
            : $dynamicNote;

        return [
            'student' => $student,
            'subjects' => $subjectResults,
            'overall_average' => $overallAverage,
            'attendance' => $attendanceSummary,
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
                return "Sangat menguasai seluruh kompetensi dasar mata pelajaran {$subject} dengan hasil yang istimewa.";
            case 'B':
                return "Menguasai kompetensi dasar mata pelajaran {$subject} dengan baik dan konsisten.";
            case 'C':
                return "Cukup menguasai kompetensi dasar mata pelajaran {$subject}, perlu peningkatan latihan mandiri.";
            default:
                return "Memerlukan bimbingan tambahan dan perhatian lebih dalam memahami materi {$subject}.";
        }
    }
}
