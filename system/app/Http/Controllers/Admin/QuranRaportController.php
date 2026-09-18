<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\HalaqahRecord;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuranRaportController extends Controller
{
    /**
     * Tampilan Daftar Siswa & Filter untuk Cetak E-Raport Khusus Al-Qur'an
     */
    public function index(Request $request)
    {
        $classes = ClassModel::withCount('students')->orderBy('name', 'asc')->get();
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $activeAcademicYear = AcademicYear::getActive() ?? $academicYears->first();

        $query = Student::with(['user', 'class', 'major', 'halaqahRecords']);

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

        return view('admin.quran-raport.index', compact(
            'students',
            'classes',
            'academicYears',
            'activeAcademicYear',
            'selectedClass'
        ));
    }

    /**
     * Render Lembar Cetak E-Raport Khusus Al-Qur'an (Single / Bulk Cetak Siap Print A4 / PDF)
     */
    public function print(Request $request)
    {
        $query = Student::with(['user', 'class', 'major', 'halaqahRecords.teacher']);

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
            return redirect()->route('admin.quran-raport.index')->with('error', 'Pilih minimal satu santri atau kelas untuk mencetak raport Al-Quran.');
        }

        $students = $query->orderBy('nisn', 'asc')->get();

        if ($students->isEmpty()) {
            return redirect()->route('admin.quran-raport.index')->with('error', 'Tidak ada data santri yang ditemukan.');
        }

        $academicYear = $request->filled('academic_year_id')
            ? AcademicYear::find($request->academic_year_id)
            : AcademicYear::getActive();

        $semester = $request->get('semester', 'Ganjil');

        // Pengaturan Kop & Tanda Tangan Sekolah
        $raportSettings = [
            'school_name' => Setting::get('school_name', 'PESANTREN & SEKOLAH ISLAM TERPADU'),
            'school_address' => Setting::get('school_address', 'Jl. Pendidikan Islam Terpadu No. 01'),
            'school_phone' => Setting::get('school_phone', '081234567890'),
            'school_email' => Setting::get('school_email', 'info@sekolah.sch.id'),
            'school_website' => Setting::get('school_website', 'www.sekolah.sch.id'),
            'city' => Setting::get('city', 'Samarinda'),
            'date' => Setting::get('quran_raport_date', now()->translatedFormat('d F Y')),
            'principal_name' => Setting::get('principal_name', 'Ustadz Pembina, Lc., M.H.'),
            'principal_nip' => Setting::get('principal_nip', '-'),
            'signature_path' => Setting::get('raport_signature_path'),
            'stamp_path' => Setting::get('stamp_image_path'),
        ];

        $raportData = [];
        foreach ($students as $student) {
            $raportData[] = $this->buildQuranRaportData($student, $academicYear, $semester);
        }

        return view('admin.quran-raport.print', compact(
            'raportData',
            'raportSettings',
            'academicYear',
            'semester'
        ));
    }

    /**
     * Kalkulasi otomatis data capaian Tahsin, Tahfidz, Nilai Adab, dan Kehadiran Halaqah
     */
    private function buildQuranRaportData(Student $student, ?AcademicYear $academicYear, string $semester): array
    {
        $records = HalaqahRecord::where('student_id', $student->id)
            ->when($academicYear, function ($q) use ($academicYear) {
                return $q->where('academic_year_id', $academicYear->id);
            })
            ->latest('assessment_date')
            ->get();

        // 1. Data Tahsin
        $tahsinRecords = $records->where('program_type', 'tahsin');
        $lastTahsin = $tahsinRecords->first();
        $avgTahsinCognitive = $tahsinRecords->isNotEmpty() ? round($tahsinRecords->avg('score_cognitive'), 1) : 0;
        $avgTahsinAdab = $tahsinRecords->isNotEmpty() ? round($tahsinRecords->avg('score_adab'), 1) : 0;
        $tahsinPredicate = HalaqahRecord::calculatePredicate($avgTahsinCognitive);

        // 2. Data Tahfidz
        $tahfidzRecords = $records->where('program_type', 'tahfidz');
        $lastTahfidz = $tahfidzRecords->first();
        $avgTahfidzCognitive = $tahfidzRecords->isNotEmpty() ? round($tahfidzRecords->avg('score_cognitive'), 1) : 0;
        $avgTahfidzAdab = $tahfidzRecords->isNotEmpty() ? round($tahfidzRecords->avg('score_adab'), 1) : 0;
        $tahfidzPredicate = HalaqahRecord::calculatePredicate($avgTahfidzCognitive);

        // Total Surat/Juz yang Disetorkan
        $surahList = $tahfidzRecords->pluck('surah_name')->filter()->unique()->values()->toArray();
        $juzList = $tahfidzRecords->pluck('juz_number')->filter()->unique()->values()->toArray();

        // 3. Rata-rata Keseluruhan Nilai Al-Qur'an
        $allCognitive = $records->pluck('score_cognitive');
        $overallScore = $allCognitive->isNotEmpty() ? round($allCognitive->avg(), 1) : 0;
        $overallPredicate = HalaqahRecord::calculatePredicate($overallScore);

        // 4. Rekap Kehadiran Halaqah
        $attendance = [
            'hadir' => $records->where('attendance_status', 'hadir')->count(),
            'sakit' => $records->where('attendance_status', 'sakit')->count(),
            'izin'  => $records->where('attendance_status', 'izin')->count(),
            'alpa'  => $records->where('attendance_status', 'alpa')->count(),
        ];

        // 5. Guru / Musyrif Pembimbing
        $teacher = $records->first()?->teacher ?? $student->class?->homeroomTeacher ?? null;
        $teacherName = $teacher ? $teacher->name : 'Ustadz Pembimbing Halaqah';

        // 6. Catatan Pembimbing
        $teacherNote = $records->first()?->teacher_notes ?: "Alhamdulillah ananda menunjukkan adab dan ketekunan yang baik dalam mempelajari Al-Qur'an. Terus istiqamah dalam muroja'ah di rumah.";

        return [
            'student' => $student,
            'teacher_name' => $teacherName,
            'teacher_note' => $teacherNote,
            'attendance' => $attendance,
            'tahsin' => [
                'has_data' => $tahsinRecords->isNotEmpty(),
                'last_jilid' => $lastTahsin ? ($lastTahsin->tahsin_type === 'tilawah' ? 'Al-Quran / Tilawah' : ($lastTahsin->jilid_level ?? 'Jilid 1')) : 'Jilid 1',
                'last_pages' => $lastTahsin && $lastTahsin->page_start ? "Halaman {$lastTahsin->page_start} s/d {$lastTahsin->page_end}" : '-',
                'score' => $avgTahsinCognitive,
                'predicate' => $tahsinPredicate,
                'total_setoran' => $tahsinRecords->count(),
            ],
            'tahfidz' => [
                'has_data' => $tahfidzRecords->isNotEmpty(),
                'last_surah' => $lastTahfidz ? "Surah {$lastTahfidz->surah_name}" : '-',
                'last_ayat' => $lastTahfidz && $lastTahfidz->ayat_start ? "Ayat {$lastTahfidz->ayat_start} s/d {$lastTahfidz->ayat_end}" : '-',
                'juz_list' => !empty($juzList) ? 'Juz ' . implode(', ', $juzList) : 'Juz 30',
                'surah_count' => count($surahList),
                'score' => $avgTahfidzCognitive,
                'predicate' => $tahfidzPredicate,
                'total_setoran' => $tahfidzRecords->count(),
            ],
            'overall_score' => $overallScore,
            'overall_predicate' => $overallPredicate,
        ];
    }
}
