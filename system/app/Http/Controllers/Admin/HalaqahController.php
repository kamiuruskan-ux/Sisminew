<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\HalaqahRecord;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class HalaqahController extends Controller
{
    /**
     * Pastikan tabel halaqah_records sudah dibuat jika migrasi belum dijalankan via CLI
     */
    public function __construct()
    {
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        try {
            if (!Schema::hasTable('halaqah_records')) {
                Schema::create('halaqah_records', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('student_id');
                    $table->unsignedBigInteger('teacher_id')->nullable();
                    $table->unsignedBigInteger('class_id')->nullable();
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->date('assessment_date');
                    $table->string('attendance_status', 20)->default('hadir');
                    $table->string('program_type', 20)->default('tahsin');
                    $table->string('tahsin_type', 50)->nullable()->default('jilid');
                    $table->string('jilid_level', 50)->nullable();
                    $table->integer('page_start')->nullable();
                    $table->integer('page_end')->nullable();
                    $table->string('surah_name', 100)->nullable();
                    $table->integer('ayat_start')->nullable();
                    $table->integer('ayat_end')->nullable();
                    $table->integer('juz_number')->nullable()->default(30);
                    $table->decimal('score_cognitive', 5, 2)->default(0);
                    $table->decimal('score_adab', 5, 2)->default(0);
                    $table->string('predicate', 50)->default('Mumtaz');
                    $table->text('teacher_notes')->nullable();
                    $table->timestamps();

                    $table->index(['assessment_date', 'program_type']);
                    $table->index(['student_id', 'assessment_date']);
                    $table->index(['class_id', 'assessment_date']);
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika tabel sudah terbuat
        }
    }

    /**
     * Dapatkan daftar ID kelas yang boleh diakses user saat ini.
     * Jika role guru-quran: hanya kelas yang ditugaskan di quran_teacher_classes.
     * Jika super-admin/admin: return null (seluruh kelas).
     */
    private function getAllowedClassIds(): ?array
    {
        $user = Auth::user();
        if (!$user) return [];

        return $user->getAssignedClassIds();
    }

    /**
     * Halaman Utama Halaqah (Tab Input Evaluasi, Tab Laporan & Grafik, Tab Rekap Kehadiran)
     */
    public function index(Request $request)
    {
        $allowedClassIds = $this->getAllowedClassIds();

        $classesQuery = ClassModel::withCount('students')->orderBy('name', 'asc');
        if ($allowedClassIds !== null) {
            $classesQuery->whereIn('id', $allowedClassIds);
        }
        $classes = $classesQuery->get();

        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $activeAcademicYear = AcademicYear::getActive() ?? $academicYears->first();
        
        $selectedClassId = $request->get('class_id');
        if (!$selectedClassId && $allowedClassIds !== null && count($allowedClassIds) > 0) {
            $selectedClassId = $classes->first()?->id;
        }

        if ($selectedClassId && $allowedClassIds !== null && !in_array($selectedClassId, $allowedClassIds)) {
            $selectedClassId = $classes->first()?->id;
        }

        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $tab = $request->get('tab', 'input'); // 'input', 'reports', 'attendance'
        $mode = $request->get('mode', 'individual'); // 'individual', 'mass'

        // Data Santri jika kelas dipilih
        $students = [];
        if ($selectedClassId) {
            $students = Student::with('user')
                ->where('class_id', $selectedClassId)
                ->orderBy('nisn', 'asc')
                ->get();
        }

        // Catatan Riwayat Pembelajaran (Sebelah kanan tampilan individu)
        $historyQuery = HalaqahRecord::with(['student.user', 'class', 'teacher'])
            ->latest('assessment_date')
            ->latest('id');

        if ($allowedClassIds !== null) {
            $historyQuery->whereIn('class_id', $allowedClassIds);
        }

        if ($selectedClassId) {
            $historyQuery->where('class_id', $selectedClassId);
        }
        if ($request->filled('filter_student_id')) {
            $historyQuery->where('student_id', $request->filter_student_id);
        }
        if ($request->filled('filter_program')) {
            $historyQuery->where('program_type', $request->filter_program);
        }

        $records = $historyQuery->paginate(15)->withQueryString();
        $totalRecordsCount = $allowedClassIds !== null 
            ? HalaqahRecord::whereIn('class_id', $allowedClassIds)->count()
            : HalaqahRecord::count();

        // ── Data Agregasi untuk Tab 2: Laporan & Grafik Statistik ──
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $statsQuery = HalaqahRecord::query();
        if ($allowedClassIds !== null) {
            $statsQuery->whereIn('class_id', $allowedClassIds);
        }
        if ($selectedClassId) {
            $statsQuery->where('class_id', $selectedClassId);
        }
        if ($request->filled('filter_student_id')) {
            $statsQuery->where('student_id', $request->filter_student_id);
        }

        $totalSetoran = (clone $statsQuery)->count();
        $tahsinCount = (clone $statsQuery)->where('program_type', 'tahsin')->count();
        $tahfidzCount = (clone $statsQuery)->where('program_type', 'tahfidz')->count();

        $avgScore = (clone $statsQuery)->avg('score_cognitive') ?? 0;
        $avgScore = round($avgScore, 1);

        $mumtazCount = (clone $statsQuery)->where('predicate', 'Mumtaz')->count();
        $mumtazPercentage = $totalSetoran > 0 ? round(($mumtazCount / $totalSetoran) * 100) : 0;

        // Predikat Sebaran
        $predicateDistribution = [
            'Mumtaz' => (clone $statsQuery)->where('predicate', 'Mumtaz')->count(),
            'Jayyid Jiddan' => (clone $statsQuery)->where('predicate', 'Jayyid Jiddan')->count(),
            'Jayyid' => (clone $statsQuery)->where('predicate', 'Jayyid')->count(),
            'Maqbul' => (clone $statsQuery)->where('predicate', 'Maqbul')->count(),
        ];

        // Sebaran Jilid Tahsin
        $jilidList = ['Jilid 1', 'Jilid 2', 'Jilid 3', 'Jilid 4', 'Tilawah'];
        $jilidStats = [];
        foreach ($jilidList as $jld) {
            $jilidStats[$jld] = (clone $statsQuery)->where('program_type', 'tahsin')->where('jilid_level', $jld)->count();
        }

        // Sebaran Juz Tahfidz (30, 29, 1, 2, Lainnya)
        $juzStats = [
            'Juz 30' => (clone $statsQuery)->where('program_type', 'tahfidz')->where('juz_number', 30)->count(),
            'Juz 29' => (clone $statsQuery)->where('program_type', 'tahfidz')->where('juz_number', 29)->count(),
            'Juz 1'  => (clone $statsQuery)->where('program_type', 'tahfidz')->where('juz_number', 1)->count(),
            'Juz 2'  => (clone $statsQuery)->where('program_type', 'tahfidz')->where('juz_number', 2)->count(),
            'Juz 3-28' => (clone $statsQuery)->where('program_type', 'tahfidz')->whereNotIn('juz_number', [1, 2, 29, 30])->count(),
        ];

        // ── Data Tab 3: Rekap Kehadiran Halaqah ──
        $attendanceStats = [
            'hadir' => (clone $statsQuery)->where('attendance_status', 'hadir')->count(),
            'sakit' => (clone $statsQuery)->where('attendance_status', 'sakit')->count(),
            'izin'  => (clone $statsQuery)->where('attendance_status', 'izin')->count(),
            'alpa'  => (clone $statsQuery)->where('attendance_status', 'alpa')->count(),
        ];

        $surahOptions = \App\Helpers\QuranHelper::getDropdownOptions();

        return view('admin.halaqah.index', compact(
            'classes',
            'academicYears',
            'activeAcademicYear',
            'selectedClassId',
            'selectedDate',
            'tab',
            'mode',
            'students',
            'records',
            'totalRecordsCount',
            'totalSetoran',
            'tahsinCount',
            'tahfidzCount',
            'avgScore',
            'mumtazCount',
            'mumtazPercentage',
            'predicateDistribution',
            'jilidStats',
            'juzStats',
            'attendanceStats',
            'surahOptions'
        ));
    }

    /**
     * Simpan Penilaian Individu
     */
    public function storeIndividual(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'assessment_date' => 'required|date',
            'program_type' => 'required|in:tahsin,tahfidz',
            'score_cognitive' => 'required|numeric|min:0|max:100',
            'score_adab' => 'nullable|numeric|min:0|max:100',
        ]);

        $student = Student::findOrFail($request->student_id);

        $allowedClassIds = $this->getAllowedClassIds();
        if ($allowedClassIds !== null && !in_array($student->class_id, $allowedClassIds)) {
            return redirect()->route('admin.halaqah.index')
                ->with('error', 'Akses ditolak: Anda hanya dapat menginput santri pada kelas yang ditugaskan kepada Anda.');
        }

        $activeYear = AcademicYear::getActive();

        $cognitive = (float) $request->score_cognitive;
        $adab = (float) ($request->score_adab ?? 85);
        $predicate = HalaqahRecord::calculatePredicate($cognitive);

        HalaqahRecord::create([
            'student_id' => $student->id,
            'teacher_id' => Auth::id(),
            'class_id' => $student->class_id,
            'academic_year_id' => $activeYear?->id,
            'assessment_date' => $request->assessment_date,
            'attendance_status' => $request->get('attendance_status', 'hadir'),
            'program_type' => $request->program_type,
            'tahsin_type' => $request->tahsin_type ?? 'jilid',
            'jilid_level' => $request->jilid_level,
            'page_start' => $request->page_start,
            'page_end' => $request->page_end,
            'surah_name' => $request->surah_name,
            'ayat_start' => $request->ayat_start,
            'ayat_end' => $request->ayat_end,
            'juz_number' => $request->juz_number ?? 30,
            'score_cognitive' => $cognitive,
            'score_adab' => $adab,
            'predicate' => $predicate,
            'teacher_notes' => $request->teacher_notes,
        ]);

        return redirect()->route('admin.halaqah.index', [
            'class_id' => $student->class_id,
            'date' => $request->assessment_date,
            'tab' => 'input',
            'mode' => 'individual'
        ])->with('success', "Penilaian Halaqah {$student->user?->name} berhasil disimpan!");
    }

    /**
     * Simpan Penilaian Massal Sekelas Sekaligus
     */
    public function storeMass(Request $request)
    {
        $request->validate([
            'assessment_date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'items' => 'required|array',
        ]);

        $classId = $request->class_id;

        $allowedClassIds = $this->getAllowedClassIds();
        if ($allowedClassIds !== null && !in_array($classId, $allowedClassIds)) {
            return redirect()->route('admin.halaqah.index')
                ->with('error', 'Akses ditolak: Anda hanya dapat menginput nilai pada kelas yang ditugaskan kepada Anda.');
        }

        $assessmentDate = $request->assessment_date;
        $activeYear = AcademicYear::getActive();
        $savedCount = 0;

        DB::transaction(function () use ($request, $classId, $assessmentDate, $activeYear, &$savedCount) {
            foreach ($request->items as $studentId => $item) {
                // Lewati jika kehadiran tidak dipilih atau tidak aktif
                $attendance = $item['attendance_status'] ?? 'hadir';
                $programType = $item['program_type'] ?? 'tahsin';
                $cognitive = isset($item['score_cognitive']) ? (float) $item['score_cognitive'] : 0;
                $adab = isset($item['score_adab']) ? (float) $item['score_adab'] : 85;

                // Hitung predikat
                $predicate = HalaqahRecord::calculatePredicate($cognitive);

                HalaqahRecord::create([
                    'student_id' => $studentId,
                    'teacher_id' => Auth::id(),
                    'class_id' => $classId,
                    'academic_year_id' => $activeYear?->id,
                    'assessment_date' => $assessmentDate,
                    'attendance_status' => $attendance,
                    'program_type' => $programType,
                    'tahsin_type' => $item['tahsin_type'] ?? 'jilid',
                    'jilid_level' => $item['jilid_level'] ?? 'Jilid 1',
                    'page_start' => $item['page_start'] ?? null,
                    'page_end' => $item['page_end'] ?? null,
                    'surah_name' => $item['surah_name'] ?? null,
                    'ayat_start' => $item['ayat_start'] ?? null,
                    'ayat_end' => $item['ayat_end'] ?? null,
                    'juz_number' => $item['juz_number'] ?? 30,
                    'score_cognitive' => $cognitive,
                    'score_adab' => $adab,
                    'predicate' => $predicate,
                    'teacher_notes' => $item['teacher_notes'] ?? null,
                ]);

                $savedCount++;
            }
        });

        return redirect()->route('admin.halaqah.index', [
            'class_id' => $classId,
            'date' => $assessmentDate,
            'tab' => 'input',
            'mode' => 'mass'
        ])->with('success', "Penilaian Massal untuk {$savedCount} santri berhasil disimpan!");
    }

    /**
     * Hapus Data Riwayat Halaqah
     */
    public function destroy($id)
    {
        $record = HalaqahRecord::findOrFail($id);

        $allowedClassIds = $this->getAllowedClassIds();
        if ($allowedClassIds !== null && !in_array($record->class_id, $allowedClassIds)) {
            return back()->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus catatan halaqah pada kelas ini.');
        }

        $record->delete();

        return back()->with('success', 'Catatan riwayat halaqah berhasil dihapus.');
    }

    /**
     * Export Excel Matriks & Rekap Halaqah
     */
    public function exportExcel(Request $request)
    {
        $query = HalaqahRecord::with(['student.user', 'class', 'teacher'])
            ->latest('assessment_date');

        $allowedClassIds = $this->getAllowedClassIds();
        if ($allowedClassIds !== null) {
            $query->whereIn('class_id', $allowedClassIds);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $records = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Rekap Halaqah Qur'an");

        // Header
        $headers = ['No', 'Tanggal', 'NISN', 'Nama Santri', 'Kelas', 'Kehadiran', 'Program', 'Materi / Detail', 'Nilai Kognitif', 'Nilai Adab', 'Predikat', 'Ustadz Pembimbing', 'Catatan'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Style Header
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        // Data Rows
        $row = 2;
        foreach ($records as $index => $rec) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $rec->assessment_date->format('Y-m-d'));
            $sheet->setCellValue('C' . $row, $rec->student->nisn ?? '-');
            $sheet->setCellValue('D' . $row, $rec->student->user->name ?? 'Santri');
            $sheet->setCellValue('E' . $row, $rec->class->name ?? '-');
            $sheet->setCellValue('F' . $row, ucfirst($rec->attendance_status));
            $sheet->setCellValue('G' . $row, strtoupper($rec->program_type));
            $sheet->setCellValue('H' . $row, $rec->material_summary);
            $sheet->setCellValue('I' . $row, $rec->score_cognitive);
            $sheet->setCellValue('J' . $row, $rec->score_adab);
            $sheet->setCellValue('K' . $row, $rec->predicate);
            $sheet->setCellValue('L' . $row, $rec->teacher->name ?? '-');
            $sheet->setCellValue('M' . $row, $rec->teacher_notes ?? '-');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Rekap_Halaqah_AlQuran_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
