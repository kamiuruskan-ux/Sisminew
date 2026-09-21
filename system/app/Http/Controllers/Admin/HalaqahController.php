<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\HalaqahRecord;
use App\Models\QuranHalaqahMember;
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
     * Pastikan tabel halaqah_records dan quran_halaqah_members sudah dibuat jika migrasi belum dijalankan via CLI
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

            if (!Schema::hasTable('quran_halaqah_members')) {
                Schema::create('quran_halaqah_members', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('teacher_id');
                    $table->unsignedBigInteger('student_id');
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->integer('grade')->nullable();
                    $table->string('group_name', 100)->nullable();
                    $table->timestamps();

                    $table->index(['teacher_id', 'grade']);
                    $table->index('student_id');
                    $table->index('grade');
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika tabel sudah terbuat
        }
    }

    /**
     * Dapatkan daftar seluruh tingkat kelas aktif yang ada di sekolah (misal: 1, 2, 3, 4, 5, 6)
     */
    public function getAvailableGrades(): array
    {
        try {
            $allClasses = ClassModel::where('is_active', true)->get();
            $grades = $allClasses->map(function ($c) {
                if (!empty($c->grade) && is_numeric($c->grade)) {
                    return (int) $c->grade;
                }
                if (preg_match('/^(\d+)/', trim($c->name), $matches)) {
                    return (int) $matches[1];
                }
                return null;
            })->filter()->unique()->sort()->values()->toArray();

            return !empty($grades) ? $grades : [1, 2, 3, 4, 5, 6];
        } catch (\Throwable $e) {
            return [1, 2, 3, 4, 5, 6];
        }
    }

    /**
     * Dapatkan ID seluruh rombel kelas untuk tingkat kelas tertentu
     */
    public function getClassIdsByGrade(int $grade): array
    {
        try {
            return ClassModel::where(function ($q) use ($grade) {
                $q->where('grade', $grade)
                  ->orWhere('name', 'like', "{$grade} %")
                  ->orWhere('name', 'like', "Kelas {$grade}%")
                  ->orWhere('name', 'like', "{$grade}-%")
                  ->orWhere('name', 'like', "{$grade}.%");
            })->pluck('id')->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Halaman Utama Halaqah Al-Qur'an (Tahsin & Tahfidz Eksklusif Berbasis Tingkat Kelas & Kelompok)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole(['super-admin', 'admin', 'kepala-sekolah', 'guru-quran', 'guru', 'teacher'])) {
            abort(403, 'Akses menu Halaqah Al-Qur\'an hanya terkhusus untuk Guru Al-Qur\'an dan Manajemen Sekolah.');
        }

        $isAdmin = $user->hasRole(['super-admin', 'admin', 'kepala-sekolah']);
        
        // Daftar Guru Al-Qur'an (untuk dropdown Admin / selector kelompok)
        $quranTeachers = User::whereHas('roles', function ($rq) {
            $rq->whereIn('slug', ['guru-quran', 'guru_quran', 'guru', 'teacher']);
        })->orderBy('name', 'asc')->get();

        // Tentukan Guru Pembimbing aktif
        $activeTeacherId = $user->id;
        if ($isAdmin && $request->filled('teacher_id')) {
            $activeTeacherId = (int) $request->teacher_id;
        }

        $activeTeacher = User::find($activeTeacherId) ?? $user;

        // Daftar Tingkat Kelas yang tersedia
        $availableGrades = $this->getAvailableGrades();

        // Tentukan Tingkat Kelas terpilih
        $selectedGrade = $request->get('grade');
        if (!$selectedGrade && $request->filled('class_id')) {
            // Backward-compatibility: jika ada parameter class_id lama
            $cls = ClassModel::find($request->class_id);
            if ($cls) {
                $selectedGrade = !empty($cls->grade) ? (int)$cls->grade : (preg_match('/^(\d+)/', $cls->name, $m) ? (int)$m[1] : null);
            }
        }

        if (!$selectedGrade) {
            // Prioritaskan tingkat kelas yang sudah memiliki santri di kelompok guru ini
            $firstAssignedGrade = QuranHalaqahMember::where('teacher_id', $activeTeacherId)
                ->whereIn('grade', $availableGrades)
                ->value('grade');
            $selectedGrade = $firstAssignedGrade ?: ($availableGrades[0] ?? 1);
        }
        $selectedGrade = (int) $selectedGrade;

        // Hitung jumlah santri halaqah per tingkat kelas untuk guru aktif
        $gradeCounts = [];
        foreach ($availableGrades as $g) {
            $gradeClassIds = $this->getClassIdsByGrade($g);
            $count = QuranHalaqahMember::where('teacher_id', $activeTeacherId)
                ->where(function ($q) use ($g, $gradeClassIds) {
                    $q->where('grade', $g)
                      ->orWhereHas('student', function ($sq) use ($gradeClassIds) {
                          $sq->whereIn('class_id', $gradeClassIds);
                      });
                })->count();
            $gradeCounts[$g] = $count;
        }

        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $activeAcademicYear = AcademicYear::getActive() ?? $academicYears->first();
        
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $tab = $request->get('tab', 'input'); // 'input', 'reports', 'attendance'
        $mode = $request->get('mode', 'individual'); // 'individual', 'mass'

        // Ambil santri yang HANYA terdaftar di kelompok halaqah guru ini pada tingkat kelas terpilih
        $selectedGradeClassIds = $this->getClassIdsByGrade($selectedGrade);
        
        $halaqahStudentIds = QuranHalaqahMember::where('teacher_id', $activeTeacherId)
            ->where(function ($q) use ($selectedGrade, $selectedGradeClassIds) {
                $q->where('grade', $selectedGrade)
                  ->orWhereHas('student', function ($sq) use ($selectedGradeClassIds) {
                      $sq->whereIn('class_id', $selectedGradeClassIds);
                  });
            })
            ->pluck('student_id')
            ->toArray();

        // Jika santri ditemukan, ambil data Student lengkap beserta user dan kelas rombelnya
        $students = [];
        if (!empty($halaqahStudentIds)) {
            $students = Student::with(['user', 'class'])
                ->whereIn('id', $halaqahStudentIds)
                ->orderBy('nisn', 'asc')
                ->get();
        }

        // Catatan Riwayat Pembelajaran (Halaqah Records)
        $historyQuery = HalaqahRecord::with(['student.user', 'class', 'teacher'])
            ->latest('assessment_date')
            ->latest('id');

        // Filter riwayat berdasarkan guru (kecuali admin memilih 'all')
        if (!$isAdmin || $request->filled('teacher_id')) {
            $historyQuery->where('teacher_id', $activeTeacherId);
        }

        // Filter riwayat berdasarkan tingkat kelas terpilih
        if (!empty($selectedGradeClassIds)) {
            $historyQuery->whereIn('class_id', $selectedGradeClassIds);
        }

        if ($request->filled('filter_student_id')) {
            $historyQuery->where('student_id', $request->filter_student_id);
        }
        if ($request->filled('filter_program')) {
            $historyQuery->where('program_type', $request->filter_program);
        }

        $records = $historyQuery->paginate(15)->withQueryString();
        $totalRecordsCount = (clone $historyQuery)->count();

        // ── Data Agregasi untuk Tab 2: Laporan & Grafik Statistik ──
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $statsQuery = HalaqahRecord::query();
        if (!$isAdmin || $request->filled('teacher_id')) {
            $statsQuery->where('teacher_id', $activeTeacherId);
        }
        if (!empty($selectedGradeClassIds)) {
            $statsQuery->whereIn('class_id', $selectedGradeClassIds);
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

        // Sebaran Juz Tahfidz
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

        // Rombel kelas untuk tingkat yang dipilih (untuk filter asal kelas pada modal kelompok)
        $classesInSelectedGrade = ClassModel::whereIn('id', $selectedGradeClassIds)->orderBy('name', 'asc')->get();

        return view('admin.halaqah.index', compact(
            'availableGrades',
            'selectedGrade',
            'gradeCounts',
            'quranTeachers',
            'activeTeacherId',
            'activeTeacher',
            'isAdmin',
            'classesInSelectedGrade',
            'academicYears',
            'activeAcademicYear',
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
     * Simpan Penilaian Individu Santri
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

        $student = Student::with('class')->findOrFail($request->student_id);

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

        // Dapatkan tingkat kelas santri untuk redirect
        $studentGrade = $student->class?->grade ?: (preg_match('/^(\d+)/', $student->class?->name ?? '', $m) ? (int)$m[1] : 1);

        return redirect()->route('admin.halaqah.index', [
            'grade' => $request->get('grade', $studentGrade),
            'date' => $request->assessment_date,
            'tab' => 'input',
            'mode' => 'individual'
        ])->with('success', "Penilaian Halaqah {$student->user?->name} berhasil disimpan!");
    }

    /**
     * Simpan Penilaian Massal Seluruh Santri di Kelompok Halaqah
     */
    public function storeMass(Request $request)
    {
        $request->validate([
            'assessment_date' => 'required|date',
            'items' => 'required|array',
        ]);

        $assessmentDate = $request->assessment_date;
        $grade = $request->get('grade', 1);
        $activeYear = AcademicYear::getActive();
        $savedCount = 0;

        DB::transaction(function () use ($request, $assessmentDate, $activeYear, &$savedCount) {
            foreach ($request->items as $studentId => $item) {
                $student = Student::find($studentId);
                if (!$student) continue;

                $attendance = $item['attendance_status'] ?? 'hadir';
                $programType = $item['program_type'] ?? 'tahsin';
                $cognitive = isset($item['score_cognitive']) ? (float) $item['score_cognitive'] : 0;
                $adab = isset($item['score_adab']) ? (float) $item['score_adab'] : 85;

                $predicate = HalaqahRecord::calculatePredicate($cognitive);

                HalaqahRecord::create([
                    'student_id' => $student->id,
                    'teacher_id' => Auth::id(),
                    'class_id' => $student->class_id,
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
            'grade' => $grade,
            'date' => $assessmentDate,
            'tab' => 'input',
            'mode' => 'mass'
        ])->with('success', "Penilaian Massal untuk {$savedCount} santri kelompok Anda berhasil disimpan!");
    }

    /**
     * API JSON: Dapatkan data santri untuk modal/antarmuka Atur Kelompok Halaqah
     */
    public function getGroupStudents(Request $request)
    {
        $user = Auth::user();
        $grade = (int) $request->get('grade', 1);
        $teacherId = (int) ($request->get('teacher_id') ?: $user->id);

        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            $teacherId = $user->id;
        }

        $classIds = $this->getClassIdsByGrade($grade);
        $classes = ClassModel::whereIn('id', $classIds)->orderBy('name', 'asc')->get(['id', 'name']);

        // Ambil semua santri di tingkat kelas ini
        $allStudents = Student::with(['user', 'class'])
            ->whereIn('class_id', $classIds)
            ->where(function ($q) {
                $q->whereIn('student_status', ['active', 'Aktif'])->orWhereNull('student_status');
            })
            ->orderBy('nisn', 'asc')
            ->get();

        // Ambil data keanggotaan halaqah di tingkat kelas ini
        $memberships = QuranHalaqahMember::with('teacher')
            ->where('grade', $grade)
            ->orWhereIn('student_id', $allStudents->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $studentData = $allStudents->map(function ($st) use ($memberships, $teacherId) {
            $membership = $memberships->get($st->id);
            $assignedTeacherId = $membership ? $membership->teacher_id : null;
            $isInMyGroup = ($assignedTeacherId === $teacherId);
            $assignedTeacherName = $membership && $membership->teacher ? $membership->teacher->name : null;

            return [
                'id' => $st->id,
                'name' => $st->user?->name ?? 'Santri',
                'nisn' => $st->nisn ?? $st->nis ?? '-',
                'class_id' => $st->class_id,
                'class_name' => $st->class?->name ?? '-',
                'gender' => $st->gender ?? 'male',
                'photo' => $st->photo_url ?? null,
                'is_in_my_group' => $isInMyGroup,
                'assigned_teacher_id' => $assignedTeacherId,
                'assigned_teacher_name' => $assignedTeacherName,
            ];
        });

        return response()->json([
            'grade' => $grade,
            'teacher_id' => $teacherId,
            'classes' => $classes,
            'students' => $studentData,
            'total_students' => $studentData->count(),
            'my_group_count' => $studentData->where('is_in_my_group', true)->count(),
        ]);
    }

    /**
     * Simpan Pengelompokan Santri Halaqah (Bisa diatur Admin atau Guru Langsung Sekali Saja & Diedit Kemudian)
     */
    public function saveGroup(Request $request)
    {
        $user = Auth::user();
        $grade = (int) $request->input('grade');
        $teacherId = (int) ($request->input('teacher_id') ?: $user->id);

        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            $teacherId = $user->id;
        }

        $studentIds = $request->input('student_ids', []);
        if (!is_array($studentIds)) {
            $studentIds = [];
        }
        $studentIds = array_map('intval', $studentIds);

        $activeYear = AcademicYear::getActive();
        $classIdsInGrade = $this->getClassIdsByGrade($grade);
        $allStudentIdsInGrade = Student::whereIn('class_id', $classIdsInGrade)->pluck('id');

        DB::transaction(function () use ($teacherId, $grade, $studentIds, $allStudentIdsInGrade, $activeYear) {
            // Lepas santri di tingkat ini yang sebelumnya ada di kelompok guru ini tapi sekarang di-uncheck
            QuranHalaqahMember::where('teacher_id', $teacherId)
                ->where(function ($q) use ($grade, $allStudentIdsInGrade) {
                    $q->where('grade', $grade)
                      ->orWhereIn('student_id', $allStudentIdsInGrade);
                })
                ->whereNotIn('student_id', $studentIds)
                ->delete();

            // Simpan / pindahkan santri yang dicentang ke kelompok guru ini (sifatnya eksklusif: 1 santri = 1 guru halaqah)
            foreach ($studentIds as $sid) {
                QuranHalaqahMember::updateOrCreate(
                    ['student_id' => $sid],
                    [
                        'teacher_id' => $teacherId,
                        'academic_year_id' => $activeYear?->id,
                        'grade' => $grade,
                        'group_name' => "Halaqah Tingkat {$grade}",
                    ]
                );
            }
        });

        $count = count($studentIds);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Alhamdulillah! Kelompok Halaqah Tingkat {$grade} berhasil disimpan ({$count} santri).",
                'count' => $count,
                'grade' => $grade,
            ]);
        }

        return redirect()->route('admin.halaqah.index', [
            'grade' => $grade,
            'tab' => 'input',
        ])->with('success', "Alhamdulillah! Kelompok Halaqah Tingkat {$grade} berhasil disimpan ({$count} santri).");
    }

    /**
     * Hapus Data Riwayat Halaqah
     */
    public function destroy($id)
    {
        $record = HalaqahRecord::findOrFail($id);
        $user = Auth::user();

        // Guru Al-Qur'an hanya boleh menghapus catatannya sendiri kecuali admin
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah']) && $record->teacher_id !== $user->id) {
            return back()->with('error', 'Akses ditolak: Anda hanya dapat menghapus catatan halaqah bimbingan Anda sendiri.');
        }

        $record->delete();

        return back()->with('success', 'Catatan riwayat halaqah berhasil dihapus.');
    }

    /**
     * Export Excel Matriks & Rekap Halaqah
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(['super-admin', 'admin', 'kepala-sekolah']);
        
        $query = HalaqahRecord::with(['student.user', 'class', 'teacher'])
            ->latest('assessment_date');

        if (!$isAdmin || $request->filled('teacher_id')) {
            $teacherId = $request->filled('teacher_id') ? $request->teacher_id : $user->id;
            $query->where('teacher_id', $teacherId);
        }

        if ($request->filled('grade')) {
            $grade = (int) $request->grade;
            $gradeClassIds = $this->getClassIdsByGrade($grade);
            if (!empty($gradeClassIds)) {
                $query->whereIn('class_id', $gradeClassIds);
            }
        } elseif ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $records = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Rekap Halaqah Qur'an");

        // Header
        $headers = ['No', 'Tanggal', 'NISN', 'Nama Santri', 'Kelas Asal', 'Kehadiran', 'Program', 'Materi / Detail', 'Nilai Kognitif', 'Nilai Adab', 'Predikat', 'Ustadz Pembimbing', 'Catatan'];
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
