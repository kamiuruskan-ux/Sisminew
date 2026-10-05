<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\HalaqahRecord;
use App\Models\QuranHalaqahMember;
use App\Models\QuranTarget;
use App\Models\QuranTasmiExam;
use App\Models\Student;
use App\Models\User;
use App\Services\DatabaseSchemaChecker;
use App\Services\WhatsAppService;
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
     * Pastikan seluruh tabel dan kolom fitur baru otomatis dibuat jika belum ada di database
     */
    public function __construct()
    {
        DatabaseSchemaChecker::ensureAllNewTablesExist();
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
     * Terapkan filter komprehensif ke query HalaqahRecord
     */
    protected function applyHalaqahFilters($query, Request $request, bool $isAdmin, $user)
    {
        // 1. Filter Guru Pembimbing
        if (!$isAdmin) {
            // Guru biasa HANYA bisa melihat santri dan riwayat yang dia bimbing sendiri
            $query->where('teacher_id', $user->id);
        } elseif ($request->filled('teacher_id') && $request->teacher_id !== 'all') {
            // Admin memilih filter spesifik guru tertentu
            $query->where('teacher_id', (int) $request->teacher_id);
        }
        // Jika Admin dan teacher_id == 'all' (atau kosong), tampilkan seluruh guru (tanpa filter teacher_id)

        // 2. Filter Tingkat Kelas (Grade)
        $grade = $request->get('grade', $request->get('history_grade', 'all'));
        if ($grade !== 'all' && is_numeric($grade)) {
            $g = (int) $grade;
            $gClassIds = $this->getClassIdsByGrade($g);
            $query->where(function ($q) use ($g, $gClassIds) {
                $q->where('grade', $g);
                if (!empty($gClassIds)) {
                    $q->orWhereIn('class_id', $gClassIds);
                }
                $q->orWhereHas('student', function ($sq) use ($g, $gClassIds) {
                    $sq->whereHas('halaqahMember', function ($hmq) use ($g) {
                        $hmq->where('grade', $g);
                    });
                    if (!empty($gClassIds)) {
                        $sq->orWhereIn('class_id', $gClassIds);
                    }
                });
            });
        }

        // 3. Filter Rombel Kelas (Class ID)
        if ($request->filled('class_id') && $request->class_id !== 'all') {
            $clsId = (int) $request->class_id;
            $query->where(function ($q) use ($clsId) {
                $q->where('class_id', $clsId)
                  ->orWhereHas('student', function ($sq) use ($clsId) {
                      $sq->where('class_id', $clsId);
                  });
            });
        }

        // 4. Filter Program (Tahsin / Tahfidz / Tilawah)
        $program = $request->get('program', $request->get('history_program', $request->get('filter_program', 'all')));
        if ($program !== 'all' && in_array($program, ['tahsin', 'tahfidz', 'tilawah'])) {
            $query->where('program_type', $program);
        }

        // 4b. Filter Kategori Capaian (Ziyadah / Muroja'ah)
        $category = $request->get('record_category', $request->get('category', 'all'));
        if ($category !== 'all' && in_array($category, ['ziyadah', 'murojaah'])) {
            $query->where('record_category', $category);
        }

        // 5. Filter Jilid (Tahsin)
        if ($request->filled('jilid') && $request->jilid !== 'all') {
            $query->where('program_type', 'tahsin')->where('jilid_level', $request->jilid);
        }

        // 6. Filter Juz / Hafalan (Tahfidz / Tilawah)
        if ($request->filled('juz') && $request->juz !== 'all') {
            $query->whereIn('program_type', ['tahfidz', 'tilawah'])->where('juz_number', (int) $request->juz);
        }

        // 7. Filter Waktu (Harian / Bulanan / Rentang Tanggal / Semua)
        $timeFilter = $request->get('time_filter', 'all');
        if ($timeFilter === 'daily' || ($request->filled('date') && !$request->filled('date_from') && !$request->filled('month') && $timeFilter !== 'all')) {
            if ($request->filled('date')) {
                $query->whereDate('assessment_date', $request->date);
            }
        } elseif ($timeFilter === 'monthly' || ($request->filled('month') && !$request->filled('date_from') && $timeFilter !== 'all')) {
            $month = (int) ($request->month ?? Carbon::now()->month);
            $year = (int) ($request->year ?? Carbon::now()->year);
            $query->whereMonth('assessment_date', $month)->whereYear('assessment_date', $year);
        } elseif ($timeFilter === 'range' || $request->filled('date_from') || $request->filled('date_to')) {
            if ($request->filled('date_from')) {
                $query->whereDate('assessment_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('assessment_date', '<=', $request->date_to);
            }
        }

        // 8. Pencarian Nama Santri atau NISN
        $search = $request->get('search_student', $request->get('q'));
        if (!empty($search)) {
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('nisn', 'like', "%{$search}%")
                   ->orWhere('nis', 'like', "%{$search}%")
                   ->orWhereHas('user', function ($uq) use ($search) {
                       $uq->where('name', 'like', "%{$search}%");
                   });
            });
        }

        return $query;
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

        $tab = $request->get('tab', 'input'); // 'input', 'history', 'reports', 'attendance'
        $mode = $request->get('mode', 'individual'); // 'individual', 'mass'

        // ── 1. Resolusi Guru Pembimbing Aktif untuk Tab Input ──
        $activeTeacherId = $user->id;
        if ($isAdmin) {
            if ($request->filled('teacher_id') && $request->teacher_id !== 'all') {
                $activeTeacherId = (int) $request->teacher_id;
            } else {
                $activeTeacherId = $quranTeachers->first()?->id ?? $user->id;
            }
        }
        $activeTeacher = User::find($activeTeacherId) ?? $user;

        // ── 2. Filter Guru Pembimbing untuk Tab History, Reports, Attendance ──
        // Jika Admin: default 'all' (bisa melihat seluruh guru) atau spesifik ID guru
        // Jika Guru Biasa: terkunci ketat hanya pada user->id dirinya sendiri
        $filterTeacherId = $isAdmin ? $request->get('teacher_id', ($tab === 'input' ? (string)$activeTeacherId : 'all')) : (string)$user->id;

        // ── 3. Tingkat Kelas yang Tersedia & Pilihan Tingkat ──
        $availableGrades = $this->getAvailableGrades();
        
        // Untuk Tab Input, wajib ada tingkat integer (1..6)
        $rawGrade = $request->get('grade', $request->get('history_grade'));
        if ($tab === 'input') {
            if (!$rawGrade || $rawGrade === 'all') {
                $firstAssignedGrade = QuranHalaqahMember::where('teacher_id', $activeTeacherId)
                    ->whereIn('grade', $availableGrades)
                    ->value('grade');
                $selectedGrade = (int) ($firstAssignedGrade ?: ($availableGrades[0] ?? 1));
            } else {
                $selectedGrade = (int) $rawGrade;
            }
        } else {
            $selectedGrade = ($rawGrade !== 'all' && is_numeric($rawGrade)) ? (int) $rawGrade : 'all';
        }

        // Hitung jumlah santri halaqah per tingkat kelas untuk guru aktif (untuk modal & tab input)
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

        // Rombel kelas seluruh sekolah untuk filter kelas
        $allClasses = ClassModel::where('is_active', true)->orderBy('grade', 'asc')->orderBy('name', 'asc')->get();
        if ($allClasses->isEmpty()) {
            $allClasses = ClassModel::orderBy('name', 'asc')->get();
        }

        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $activeAcademicYear = AcademicYear::getActive() ?? $academicYears->first();
        
        // Filter options untuk tampilan (Tahsin hanya Jilid 1 - 6, Tilawah berdiri sendiri mengikuti acuan Tahfidz)
        $allJilidOptions = ['Jilid 1', 'Jilid 2', 'Jilid 3', 'Jilid 4', 'Jilid 5', 'Jilid 6'];
        $allJuzOptions = [30, 29, 28, 27, 26, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25];

        $filterGrade = (string) $selectedGrade;
        $filterClassId = $request->get('class_id', 'all');
        $filterProgram = $request->get('program', $request->get('history_program', 'all'));
        $filterJilid = $request->get('jilid', 'all');
        $filterJuz = $request->get('juz', 'all');
        $timeFilter = $request->get('time_filter', 'all');
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedMonth = (int) $request->get('month', Carbon::now()->month);
        $selectedYear = (int) $request->get('year', Carbon::now()->year);
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $searchStudent = $request->get('search_student', $request->get('q', ''));

        // ── TAB 1: DATA SANTRI UNTUK INPUT EVALUASI ──
        $inputGrade = is_numeric($selectedGrade) ? (int)$selectedGrade : ($availableGrades[0] ?? 1);
        $inputGradeClassIds = $this->getClassIdsByGrade($inputGrade);
        
        $halaqahStudentIds = QuranHalaqahMember::where('teacher_id', $activeTeacherId)
            ->where(function ($q) use ($inputGrade, $inputGradeClassIds) {
                $q->where('grade', $inputGrade)
                  ->orWhereHas('student', function ($sq) use ($inputGradeClassIds) {
                      $sq->whereIn('class_id', $inputGradeClassIds);
                  });
            })
            ->pluck('student_id')
            ->toArray();

        $students = [];
        if (!empty($halaqahStudentIds)) {
            $students = Student::with(['user', 'class'])
                ->whereIn('id', $halaqahStudentIds)
                ->orderBy('nisn', 'asc')
                ->get();
        }

        // Catatan Riwayat Pembelajaran (Halaqah Records untuk Tab 1: Input)
        $historyQuery = HalaqahRecord::with(['student.user', 'class', 'teacher'])
            ->latest('assessment_date')
            ->latest('id');

        if (!$isAdmin || ($request->filled('teacher_id') && $request->teacher_id !== 'all')) {
            $historyQuery->where('teacher_id', $activeTeacherId);
        }

        $historyQuery->where(function ($q) use ($inputGrade, $inputGradeClassIds) {
            $q->where('grade', $inputGrade);
            if (!empty($inputGradeClassIds)) {
                $q->orWhereIn('class_id', $inputGradeClassIds);
            }
            $q->orWhereHas('student', function ($sq) use ($inputGrade, $inputGradeClassIds) {
                $sq->whereHas('halaqahMember', function ($hmq) use ($inputGrade) {
                    $hmq->where('grade', $inputGrade);
                });
                if (!empty($inputGradeClassIds)) {
                    $sq->orWhereIn('class_id', $inputGradeClassIds);
                }
            });
        });

        if ($request->filled('filter_student_id')) {
            $historyQuery->where('student_id', $request->filter_student_id);
        }
        if ($request->filled('filter_program')) {
            $historyQuery->where('program_type', $request->filter_program);
        }

        $records = $historyQuery->paginate(15)->withQueryString();
        $totalRecordsCount = (clone $historyQuery)->count();

        // ── TAB 2: DATA HISTORY & LOGBOOK MENGAJAR (MULTI-FILTER) ──
        $historyTeachingQuery = HalaqahRecord::with(['student.user', 'class', 'teacher'])
            ->latest('assessment_date')
            ->latest('id');

        $this->applyHalaqahFilters($historyTeachingQuery, $request, $isAdmin, $user);

        $totalTeachingCount = (clone $historyTeachingQuery)->count();
        $historyUniqueStudentsCount = (clone $historyTeachingQuery)->distinct('student_id')->count('student_id');
        $historyAvgScore = round((clone $historyTeachingQuery)->avg('score_cognitive') ?? 0, 1);
        $historyTahsinCount = (clone $historyTeachingQuery)->where('program_type', 'tahsin')->count();
        $historyTahfidzCount = (clone $historyTeachingQuery)->where('program_type', 'tahfidz')->count();
        $historyTilawahCount = (clone $historyTeachingQuery)->where('program_type', 'tilawah')->count();
        $historyZiyadahCount = (clone $historyTeachingQuery)->where('record_category', 'ziyadah')->count();
        $historyMurojaahCount = (clone $historyTeachingQuery)->where('record_category', 'murojaah')->count();
        $historyMumtazCount = (clone $historyTeachingQuery)->whereIn('predicate', ['Mumtaz', 'Jayyid Jiddan'])->count();
        $historyExcellentPct = $totalTeachingCount > 0 ? round(($historyMumtazCount / $totalTeachingCount) * 100) : 0;

        $historyTeachingRecords = $historyTeachingQuery->paginate(20)->withQueryString();

        // ── TAB 3: DATA LAPORAN & GRAFIK STATISTIK (MULTI-FILTER) ──
        $statsQuery = HalaqahRecord::query();
        $this->applyHalaqahFilters($statsQuery, $request, $isAdmin, $user);

        $totalSetoran = (clone $statsQuery)->count();
        $tahsinCount = (clone $statsQuery)->where('program_type', 'tahsin')->count();
        $tahfidzCount = (clone $statsQuery)->where('program_type', 'tahfidz')->count();
        $tilawahCount = (clone $statsQuery)->where('program_type', 'tilawah')->count();
        $ziyadahCount = (clone $statsQuery)->where('record_category', 'ziyadah')->count();
        $murojaahCount = (clone $statsQuery)->where('record_category', 'murojaah')->count();

        $avgScore = round((clone $statsQuery)->avg('score_cognitive') ?? 0, 1);
        $mumtazCount = (clone $statsQuery)->where('predicate', 'Mumtaz')->count();
        $mumtazPercentage = $totalSetoran > 0 ? round(($mumtazCount / $totalSetoran) * 100) : 0;
        $reportUniqueStudentsCount = (clone $statsQuery)->distinct('student_id')->count('student_id');

        // Predikat Sebaran
        $predicateDistribution = [
            'Mumtaz' => (clone $statsQuery)->where('predicate', 'Mumtaz')->count(),
            'Jayyid Jiddan' => (clone $statsQuery)->where('predicate', 'Jayyid Jiddan')->count(),
            'Jayyid' => (clone $statsQuery)->where('predicate', 'Jayyid')->count(),
            'Maqbul' => (clone $statsQuery)->where('predicate', 'Maqbul')->count(),
        ];

        // Sebaran Jilid Tahsin (Jilid 1 - 6 murni)
        $jilidStats = [];
        foreach ($allJilidOptions as $jld) {
            $jilidStats[$jld] = (clone $statsQuery)->where('program_type', 'tahsin')->where('jilid_level', $jld)->count();
        }

        // Sebaran Juz Tahfidz & Tilawah
        $juzStats = [
            'Juz 30' => (clone $statsQuery)->whereIn('program_type', ['tahfidz', 'tilawah'])->where('juz_number', 30)->count(),
            'Juz 29' => (clone $statsQuery)->whereIn('program_type', ['tahfidz', 'tilawah'])->where('juz_number', 29)->count(),
            'Juz 28' => (clone $statsQuery)->whereIn('program_type', ['tahfidz', 'tilawah'])->where('juz_number', 28)->count(),
            'Juz 1'  => (clone $statsQuery)->whereIn('program_type', ['tahfidz', 'tilawah'])->where('juz_number', 1)->count(),
            'Juz 2'  => (clone $statsQuery)->whereIn('program_type', ['tahfidz', 'tilawah'])->where('juz_number', 2)->count(),
            'Juz Lainnya (3-27)' => (clone $statsQuery)->whereIn('program_type', ['tahfidz', 'tilawah'])->whereNotIn('juz_number', [1, 2, 28, 29, 30])->count(),
        ];

        // Rekapitulasi Capaian Siswa di Tab Laporan
        $studentReportList = collect();
        if ($tab === 'reports') {
            $studentReportList = (clone $statsQuery)
                ->select(
                    'student_id',
                    DB::raw("COUNT(*) as total_setoran"),
                    DB::raw("ROUND(AVG(score_cognitive), 1) as avg_score"),
                    DB::raw("MAX(assessment_date) as last_date")
                )
                ->groupBy('student_id')
                ->with(['student.user', 'student.class'])
                ->orderBy('total_setoran', 'desc')
                ->paginate(20, ['*'], 'report_page')
                ->withQueryString();
        }

        // ── TAB 4: REKAP KEHADIRAN HALAQAH (MULTI-FILTER) ──
        $attendanceQuery = HalaqahRecord::query();
        $this->applyHalaqahFilters($attendanceQuery, $request, $isAdmin, $user);

        $attendanceStats = [
            'hadir' => (clone $attendanceQuery)->where('attendance_status', 'hadir')->count(),
            'sakit' => (clone $attendanceQuery)->where('attendance_status', 'sakit')->count(),
            'izin'  => (clone $attendanceQuery)->where('attendance_status', 'izin')->count(),
            'alpa'  => (clone $attendanceQuery)->where('attendance_status', 'alpa')->count(),
        ];
        $totalAttendanceEntries = array_sum($attendanceStats);
        $attendancePercentage = $totalAttendanceEntries > 0 ? round(($attendanceStats['hadir'] / $totalAttendanceEntries) * 100, 1) : 0;

        // Detail Rekap Kehadiran Per Santri
        $studentAttendanceList = collect();
        if ($tab === 'attendance') {
            $studentAttendanceList = (clone $attendanceQuery)
                ->select(
                    'student_id',
                    DB::raw("COUNT(*) as total_meetings"),
                    DB::raw("SUM(CASE WHEN attendance_status = 'hadir' THEN 1 ELSE 0 END) as count_hadir"),
                    DB::raw("SUM(CASE WHEN attendance_status = 'sakit' THEN 1 ELSE 0 END) as count_sakit"),
                    DB::raw("SUM(CASE WHEN attendance_status = 'izin' THEN 1 ELSE 0 END) as count_izin"),
                    DB::raw("SUM(CASE WHEN attendance_status = 'alpa' THEN 1 ELSE 0 END) as count_alpa")
                )
                ->groupBy('student_id')
                ->with(['student.user', 'student.class'])
                ->paginate(25, ['*'], 'attendance_page')
                ->withQueryString();
        }

        // ── EARLY WARNING SYSTEM (Santri Perlu Perhatian / Belum Setor / Nilai Rendah) ──
        $attentionStudents = [];
        if (!empty($halaqahStudentIds)) {
            $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();
            $groupStudents = Student::with(['user', 'class'])->whereIn('id', $halaqahStudentIds)->get();
            foreach ($groupStudents as $st) {
                $lastRec = HalaqahRecord::where('student_id', $st->id)->latest('assessment_date')->latest('id')->first();
                if (!$lastRec) {
                    $attentionStudents[] = [
                        'student' => $st,
                        'reason' => 'Belum pernah ada riwayat setoran',
                        'type' => 'danger',
                        'days_inactive' => '-',
                        'last_record' => null
                    ];
                } elseif ($lastRec->assessment_date->format('Y-m-d') < $sevenDaysAgo) {
                    $days = Carbon::parse($lastRec->assessment_date)->diffInDays(Carbon::now());
                    $attentionStudents[] = [
                        'student' => $st,
                        'reason' => "Tidak ada setoran selama {$days} hari terakhir",
                        'type' => 'warning',
                        'days_inactive' => $days,
                        'last_record' => $lastRec
                    ];
                } elseif ($lastRec->predicate === 'Maqbul') {
                    $attentionStudents[] = [
                        'student' => $st,
                        'reason' => 'Predikat setoran terakhir Maqbul (Butuh Bimbingan Khusus)',
                        'type' => 'info',
                        'days_inactive' => 0,
                        'last_record' => $lastRec
                    ];
                }
            }
        }
        $attentionStudents = collect($attentionStudents);

        // ── TARGET CAPAIAN HAFALAN & TILAWAH ──
        $targets = QuranTarget::orderBy('grade', 'asc')->orderBy('semester', 'asc')->get();

        // ── UJIAN TASMI' 1 JUZ SEKALI DUDUK & SYAHADAH ──
        $tasmiExams = QuranTasmiExam::with(['student.user', 'student.class', 'teacher'])
            ->latest('exam_date')
            ->paginate(15, ['*'], 'tasmi_page')
            ->withQueryString();

        $surahOptions = \App\Helpers\QuranHelper::getDropdownOptions();
        $classesInSelectedGrade = ClassModel::whereIn('id', $inputGradeClassIds)->orderBy('name', 'asc')->get();
        $filterCategory = $request->get('record_category', $request->get('category', 'all'));

        return view('admin.halaqah.index', compact(
            'availableGrades',
            'selectedGrade',
            'gradeCounts',
            'quranTeachers',
            'activeTeacherId',
            'activeTeacher',
            'isAdmin',
            'allClasses',
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
            'tilawahCount',
            'ziyadahCount',
            'murojaahCount',
            'avgScore',
            'mumtazCount',
            'mumtazPercentage',
            'reportUniqueStudentsCount',
            'predicateDistribution',
            'jilidStats',
            'juzStats',
            'studentReportList',
            'attendanceStats',
            'totalAttendanceEntries',
            'attendancePercentage',
            'studentAttendanceList',
            'surahOptions',
            'historyTeachingRecords',
            'totalTeachingCount',
            'historyUniqueStudentsCount',
            'historyAvgScore',
            'historyTahsinCount',
            'historyTahfidzCount',
            'historyTilawahCount',
            'historyZiyadahCount',
            'historyMurojaahCount',
            'historyMumtazCount',
            'historyExcellentPct',
            'filterGrade',
            'filterClassId',
            'filterTeacherId',
            'filterProgram',
            'filterCategory',
            'filterJilid',
            'filterJuz',
            'timeFilter',
            'selectedMonth',
            'selectedYear',
            'dateFrom',
            'dateTo',
            'searchStudent',
            'allJilidOptions',
            'allJuzOptions',
            'attentionStudents',
            'targets',
            'tasmiExams'
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
            'program_type' => 'required|in:tahsin,tahfidz,tilawah',
            'record_category' => 'nullable|in:ziyadah,murojaah',
            'score_cognitive' => 'required|numeric|min:0|max:100',
            'score_adab' => 'nullable|numeric|min:0|max:100',
        ]);

        $student = Student::with('class')->findOrFail($request->student_id);

        $activeYear = AcademicYear::getActive();
        $cognitive = (float) $request->score_cognitive;
        $adab = (float) ($request->score_adab ?? 85);
        $predicate = HalaqahRecord::calculatePredicate($cognitive);

        $studentGrade = $request->filled('grade') 
            ? (int) $request->grade 
            : ($student->class?->grade ?: (preg_match('/^(\d+)/', $student->class?->name ?? '', $m) ? (int)$m[1] : 1));

        $recordCategory = $request->get('record_category', 'ziyadah');

        HalaqahRecord::create([
            'student_id' => $student->id,
            'teacher_id' => Auth::id(),
            'class_id' => $student->class_id,
            'grade' => (int) $studentGrade,
            'academic_year_id' => $activeYear?->id,
            'assessment_date' => $request->assessment_date,
            'attendance_status' => $request->get('attendance_status', 'hadir'),
            'program_type' => $request->program_type,
            'record_category' => $recordCategory,
            'tahsin_type' => $request->tahsin_type ?? 'jilid',
            'jilid_level' => $request->program_type === 'tahsin' ? $request->jilid_level : null,
            'page_start' => $request->program_type === 'tahsin' ? $request->page_start : null,
            'page_end' => $request->program_type === 'tahsin' ? $request->page_end : null,
            'surah_name' => in_array($request->program_type, ['tahfidz', 'tilawah']) ? $request->surah_name : null,
            'ayat_start' => in_array($request->program_type, ['tahfidz', 'tilawah']) ? $request->ayat_start : null,
            'ayat_end' => in_array($request->program_type, ['tahfidz', 'tilawah']) ? $request->ayat_end : null,
            'juz_number' => in_array($request->program_type, ['tahfidz', 'tilawah']) ? ($request->juz_number ?? 30) : null,
            'score_cognitive' => $cognitive,
            'score_adab' => $adab,
            'predicate' => $predicate,
            'teacher_notes' => $request->teacher_notes,
        ]);

        return redirect()->route('admin.halaqah.index', [
            'grade' => $studentGrade,
            'date' => $request->assessment_date,
            'tab' => 'input',
            'mode' => 'individual'
        ])->with('success', "Penilaian Halaqah ({$request->program_type}) {$student->user?->name} berhasil disimpan!");
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
        $grade = (int) $request->get('grade', 1);
        $activeYear = AcademicYear::getActive();
        $savedCount = 0;

        DB::transaction(function () use ($request, $assessmentDate, $grade, $activeYear, &$savedCount) {
            foreach ($request->items as $studentId => $item) {
                $student = Student::find($studentId);
                if (!$student) continue;

                $attendance = $item['attendance_status'] ?? 'hadir';
                $programType = $item['program_type'] ?? 'tahsin';
                $recordCategory = $item['record_category'] ?? 'ziyadah';
                $cognitive = isset($item['score_cognitive']) ? (float) $item['score_cognitive'] : 0;
                $adab = isset($item['score_adab']) ? (float) $item['score_adab'] : 85;

                $predicate = HalaqahRecord::calculatePredicate($cognitive);

                HalaqahRecord::create([
                    'student_id' => $student->id,
                    'teacher_id' => Auth::id(),
                    'class_id' => $student->class_id,
                    'grade' => (int) $grade,
                    'academic_year_id' => $activeYear?->id,
                    'assessment_date' => $assessmentDate,
                    'attendance_status' => $attendance,
                    'program_type' => $programType,
                    'record_category' => $recordCategory,
                    'tahsin_type' => $item['tahsin_type'] ?? 'jilid',
                    'jilid_level' => $programType === 'tahsin' ? ($item['jilid_level'] ?? 'Jilid 1') : null,
                    'page_start' => $programType === 'tahsin' ? ($item['page_start'] ?? null) : null,
                    'page_end' => $programType === 'tahsin' ? ($item['page_end'] ?? null) : null,
                    'surah_name' => in_array($programType, ['tahfidz', 'tilawah']) ? ($item['surah_name'] ?? null) : null,
                    'ayat_start' => in_array($programType, ['tahfidz', 'tilawah']) ? ($item['ayat_start'] ?? null) : null,
                    'ayat_end' => in_array($programType, ['tahfidz', 'tilawah']) ? ($item['ayat_end'] ?? null) : null,
                    'juz_number' => in_array($programType, ['tahfidz', 'tilawah']) ? ($item['juz_number'] ?? 30) : null,
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
            ->latest('assessment_date')
            ->latest('id');

        $this->applyHalaqahFilters($query, $request, $isAdmin, $user);

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

    /**
     * API JSON: Dapatkan capaian terakhir santri untuk fitur Smart Assist
     */
    public function getLastRecord($studentId)
    {
        $student = Student::with(['user', 'class'])->findOrFail($studentId);
        $lastRecord = HalaqahRecord::where('student_id', $studentId)
            ->latest('assessment_date')
            ->latest('id')
            ->first();

        $suggested = [
            'program_type' => $lastRecord?->program_type ?? 'tahfidz',
            'record_category' => 'ziyadah',
            'page_start' => 1,
            'page_end' => 10,
            'jilid_level' => 'Jilid 1',
            'surah_name' => 'An-Naba\'',
            'juz_number' => 30,
            'ayat_start' => 1,
            'ayat_end' => 10,
        ];

        if ($lastRecord) {
            $suggested['program_type'] = $lastRecord->program_type;
            if ($lastRecord->program_type === 'tahsin') {
                $suggested['jilid_level'] = $lastRecord->jilid_level ?: 'Jilid 1';
                $nextPage = ($lastRecord->page_end ?: 0) + 1;
                $suggested['page_start'] = $nextPage;
                $suggested['page_end'] = $nextPage + 4;
            } else {
                $suggested['surah_name'] = $lastRecord->surah_name ?: 'An-Naba\'';
                $suggested['juz_number'] = $lastRecord->juz_number ?: 30;
                $nextAyat = ($lastRecord->ayat_end ?: 0) + 1;
                $suggested['ayat_start'] = $nextAyat;
                $suggested['ayat_end'] = $nextAyat + 9;
            }
        }

        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->user?->name ?? 'Santri',
                'nisn' => $student->nisn ?? $student->nis ?? '-',
                'class_name' => $student->class?->name ?? '-',
                'phone' => $student->phone,
                'parent_phone' => $student->parent_phone,
            ],
            'record' => $lastRecord ? [
                'id' => $lastRecord->id,
                'date' => $lastRecord->assessment_date->format('d/m/Y'),
                'program_type' => $lastRecord->program_type,
                'record_category' => $lastRecord->record_category ?? 'ziyadah',
                'material_summary' => $lastRecord->material_summary,
                'jilid_level' => $lastRecord->jilid_level,
                'page_start' => $lastRecord->page_start,
                'page_end' => $lastRecord->page_end,
                'surah_name' => $lastRecord->surah_name,
                'ayat_start' => $lastRecord->ayat_start,
                'ayat_end' => $lastRecord->ayat_end,
                'juz_number' => $lastRecord->juz_number,
                'score_cognitive' => $lastRecord->score_cognitive,
                'predicate' => $lastRecord->predicate,
            ] : null,
            'suggested' => $suggested,
        ]);
    }

    /**
     * Kirim Resume Setoran ke WhatsApp Wali Murid
     */
    public function sendWa(Request $request, $recordId)
    {
        $record = HalaqahRecord::with(['student.user', 'class', 'teacher'])->findOrFail($recordId);
        $student = $record->student;
        $parentPhone = $student->parent_phone ?: $student->phone;

        $studentName = $student->user?->name ?? 'Santri';
        $teacherName = $record->teacher?->name ?? 'Ustadz Pembimbing';
        $date = $record->assessment_date->translatedFormat('l, d F Y');
        $program = strtoupper($record->program_type);
        $cat = $record->record_category === 'murojaah' ? "Muroja'ah (Pengulangan)" : "Ziyadah (Hafalan Baru)";
        $material = $record->material_summary;
        $score = $record->score_cognitive;
        $predicate = $record->predicate;
        $notes = $record->teacher_notes ?: '-';

        $msg = "🌙 *LAPORAN MUTABA'AH AL-QUR'AN*\n"
             . "--------------------------------------------------\n"
             . "Assalamu'alaikum Warahmatullahi Wabarakatuh\n"
             . "Yth. Bapak/Ibu Wali dari ananda *{$studentName}*,\n\n"
             . "Alhamdulillah, berikut resume bimbingan Al-Qur'an ananda hari ini:\n"
             . "📅 *Hari/Tanggal*: {$date}\n"
             . "📖 *Program*: {$program}\n"
             . "🏷️ *Kategori*: {$cat}\n"
             . "🎯 *Capaian*: {$material}\n"
             . "⭐ *Nilai & Predikat*: {$score} / 100 (*{$predicate}*)\n"
             . "📝 *Catatan Pembimbing*: {$notes}\n"
             . "👳‍♂️ *Musyrif/ah*: Ust. {$teacherName}\n\n"
             . "Semoga ananda senantiasa diberkahi kelancaran dan istiqomah bersama Al-Qur'an. Aamiin.\n"
             . "Wassalamu'alaikum Warahmatullahi Wabarakatuh.";

        $cleanPhone = WhatsAppService::formatPhoneNumber((string)$parentPhone);
        $encodedMsg = urlencode($msg);
        $waUrl = "https://api.whatsapp.com/send?phone={$cleanPhone}&text={$encodedMsg}";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'phone' => $cleanPhone,
                'message' => $msg,
                'wa_url' => $waUrl,
            ]);
        }

        return redirect()->away($waUrl);
    }

    /**
     * Simpan Target Hafalan / Tilawah Baru
     */
    public function storeTarget(Request $request)
    {
        $request->validate([
            'grade' => 'required|integer',
            'semester' => 'required|in:1,2',
            'program_type' => 'required|in:tahfidz,tahsin,tilawah',
        ]);

        $title = $request->input('title');
        if (empty($title)) {
            $progLabel = match($request->program_type) {
                'tahsin' => 'Tahsin ' . ($request->target_jilid ?? 'Jilid'),
                'tilawah' => 'Tilawah ' . ($request->target_juz ? 'Juz ' . $request->target_juz : ''),
                default => 'Tahfidz ' . ($request->target_juz ? 'Juz ' . $request->target_juz : '')
            };
            $title = "Target {$progLabel} Kelas {$request->grade} Semester {$request->semester}";
        }

        QuranTarget::create([
            'grade' => $request->grade,
            'semester' => $request->semester,
            'program_type' => $request->program_type,
            'title' => $title,
            'target_juz' => $request->target_juz ?? $request->target_juz_start,
            'target_surah_start' => $request->target_surah_start ?? $request->target_surah,
            'target_surah_end' => $request->target_surah_end,
            'target_jilid' => $request->target_jilid,
            'notes' => $request->notes ?? $request->description,
        ]);

        return redirect()->route('admin.halaqah.index', ['tab' => 'target', 'grade' => $request->grade])
            ->with('success', 'Target capaian Al-Qur\'an berhasil disimpan!');
    }

    /**
     * Hapus Target Hafalan
     */
    public function destroyTarget($id)
    {
        QuranTarget::findOrFail($id)->delete();
        return back()->with('success', 'Target capaian Al-Qur\'an berhasil dihapus.');
    }

    /**
     * Simpan Data Ujian Tasmi' 1 Juz Sekali Duduk
     */
    public function storeTasmi(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_date' => 'required|date',
            'score_tajwid' => 'required|numeric|min:0|max:100',
            'score_kelancaran' => 'required|numeric|min:0|max:100',
            'score_fashohah' => 'required|numeric|min:0|max:100',
        ]);

        $juzTested = $request->input('juz_tested') ?: ('Juz ' . $request->input('juz_number', '30'));
        $final = round(($request->score_tajwid * 0.4) + ($request->score_kelancaran * 0.4) + ($request->score_fashohah * 0.2), 1);
        $predicate = HalaqahRecord::calculatePredicate($final);
        $status = $final >= 75 ? 'lulus' : 'perbaikan';

        $student = Student::findOrFail($request->student_id);
        $certNo = 'SYH/' . date('Ym') . '/' . str_pad($student->id, 4, '0', STR_PAD_LEFT) . '/' . rand(100, 999);

        QuranTasmiExam::create([
            'student_id' => $request->student_id,
            'teacher_id' => Auth::id(),
            'academic_year_id' => AcademicYear::getActive()?->id,
            'exam_date' => $request->exam_date,
            'juz_tested' => $juzTested,
            'surah_range' => $request->surah_range,
            'score_tajwid' => $request->score_tajwid,
            'score_kelancaran' => $request->score_kelancaran,
            'score_fashohah' => $request->score_fashohah,
            'score_final' => $final,
            'predicate' => $predicate,
            'status' => $status,
            'certificate_number' => $certNo,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.halaqah.index', ['tab' => 'tasmi'])
            ->with('success', "Hasil Ujian Tasmi' untuk {$student->user?->name} berhasil dicatat!");
    }

    /**
     * Cetak Syahadah / Sertifikat Tasmi' 1 Juz
     */
    public function printCertificate($id)
    {
        $exam = QuranTasmiExam::with(['student.user', 'student.class', 'teacher'])->findOrFail($id);
        return view('admin.halaqah.certificate', compact('exam'));
    }
}
