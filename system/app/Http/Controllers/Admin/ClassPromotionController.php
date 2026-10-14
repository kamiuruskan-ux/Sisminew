<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Services\DatabaseSchemaChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassPromotionController extends Controller
{
    public function __construct()
    {
        DatabaseSchemaChecker::ensureAllNewTablesExist();
    }

    /**
     * Halaman Utama Pengaturan Kenaikan & Kelulusan Kelas Siswa
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $activeYear = AcademicYear::getActive() ?? $academicYears->first();

        // 1. Tahun Ajaran Asal (T.A yang sedang dinilai kenaikannya)
        $sourceYearId = (int) $request->input('source_year_id', $activeYear?->id ?? 0);
        $sourceYear = $academicYears->firstWhere('id', $sourceYearId) ?? $activeYear;

        // 2. Tahun Ajaran Tujuan (T.A baru tempat siswa naik)
        $nextSuggestedYear = $academicYears->firstWhere('start_year', ($sourceYear?->end_year ?? 0))
            ?? $academicYears->where('id', '!=', $sourceYearId)->first()
            ?? $sourceYear;

        $targetYearId = (int) $request->input('target_year_id', $nextSuggestedYear?->id ?? 0);
        $targetYear = $academicYears->firstWhere('id', $targetYearId) ?? $nextSuggestedYear;

        // 3. Daftar Kelas Asal
        $classes = ClassModel::where('is_active', true)
            ->orderBy('grade')
            ->orderBy('name')
            ->get();

        $sourceClassId = (int) $request->input('source_class_id', $classes->first()?->id ?? 0);
        $sourceClass = $classes->firstWhere('id', $sourceClassId);

        // 4. Rekomendasi Kelas Tujuan berdasarkan Tingkat
        $sourceGrade = $sourceClass?->grade ?? 1;
        $targetSuggestedGrade = min(6, $sourceGrade + 1);
        $isFinalGrade = ($sourceGrade >= 6); // Tingkat 6 adalah kelas akhir SDIT

        $targetClasses = ClassModel::where('is_active', true)
            ->orderBy('grade')
            ->orderBy('name')
            ->get();

        $defaultTargetClass = $targetClasses->firstWhere('grade', $targetSuggestedGrade);

        // 5. Ambil Siswa yang terdaftar pada Kelas Asal & Tahun Ajaran Asal
        $students = collect();
        if ($sourceClass) {
            // Ambil dari riwayat jika sudah tercatat, atau fallback ke kelas aktif saat ini
            $historyStudentIds = StudentClassHistory::where('class_id', $sourceClassId)
                ->where('academic_year_id', $sourceYearId)
                ->pluck('student_id')
                ->toArray();

            if (!empty($historyStudentIds)) {
                $students = Student::with(['user', 'class', 'classHistories' => function ($q) use ($sourceYearId, $targetYearId) {
                    $q->whereIn('academic_year_id', array_filter([$sourceYearId, $targetYearId]));
                }])
                ->whereIn('id', $historyStudentIds)
                ->orderBy('nisn', 'asc')
                ->get();
            } else {
                $students = Student::with(['user', 'class', 'classHistories' => function ($q) use ($sourceYearId, $targetYearId) {
                    $q->whereIn('academic_year_id', array_filter([$sourceYearId, $targetYearId]));
                }])
                ->where('class_id', $sourceClassId)
                ->where(function ($q) {
                    $q->whereIn('student_status', ['active', 'Aktif'])->orWhereNull('student_status');
                })
                ->orderBy('nisn', 'asc')
                ->get();
            }
        }

        // 6. Hitung performa siswa untuk pertimbangan kenaikan kelas
        $studentPerformance = [];
        foreach ($students as $st) {
            // Rata-rata nilai pada tahun ajaran asal
            $avgScore = Grade::where('student_id', $st->id)
                ->where('academic_year_id', $sourceYearId)
                ->avg('score');

            if ($avgScore === null) {
                // Fallback jika nilai belum terisi per tahun ajaran
                $avgScore = Grade::where('student_id', $st->id)->avg('score');
            }

            // Presensi kehadiran
            $attQuery = Attendance::where('student_id', $st->id);
            if ($sourceYear && $sourceYear->start_date && $sourceYear->end_date) {
                $attQuery->whereBetween('date', [$sourceYear->start_date->format('Y-m-d'), $sourceYear->end_date->format('Y-m-d')]);
            }
            $totalAtt = $attQuery->count();
            $presentAtt = (clone $attQuery)->whereIn('status', ['present', 'late'])->count();
            $attPercentage = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 100;

            // Cek status history saat ini di tahun asal & tahun tujuan
            $currentHistory = $st->classHistories->firstWhere('academic_year_id', $sourceYearId);
            $targetHistory = $st->classHistories->firstWhere('academic_year_id', $targetYearId);

            $studentPerformance[$st->id] = [
                'avg_score' => $avgScore ? round($avgScore, 1) : 0,
                'att_percentage' => $attPercentage,
                'current_status' => $currentHistory?->status ?? 'active',
                'target_class_id' => $targetHistory?->class_id ?? $defaultTargetClass?->id,
                'is_promoted' => in_array($currentHistory?->status, ['promoted', 'graduated']),
            ];
        }

        // 7. Statistik Ringkasan
        $stats = [
            'total_students' => $students->count(),
            'promoted_count' => count(array_filter($studentPerformance, fn($p) => $p['is_promoted'])),
            'eligible_count' => count(array_filter($studentPerformance, fn($p) => $p['avg_score'] >= 70 && $p['att_percentage'] >= 75)),
        ];

        return view('admin.class-promotions.index', compact(
            'academicYears',
            'sourceYear',
            'sourceYearId',
            'targetYear',
            'targetYearId',
            'classes',
            'sourceClass',
            'sourceClassId',
            'targetClasses',
            'defaultTargetClass',
            'targetSuggestedGrade',
            'isFinalGrade',
            'students',
            'studentPerformance',
            'stats'
        ));
    }

    /**
     * Proses Kenaikan Kelas / Kelulusan Siswa Secara Masal
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'source_year_id' => 'required|exists:academic_years,id',
            'source_class_id' => 'required|exists:classes,id',
            'target_year_id' => 'required|exists:academic_years,id',
            'decisions' => 'required|array|min:1',
            'decisions.*.action' => 'required|in:promote,retain,graduate,transfer',
            'decisions.*.target_class_id' => 'nullable|exists:classes,id',
            'decisions.*.notes' => 'nullable|string|max:255',
        ], [
            'decisions.required' => 'Pilih minimal satu santri untuk diproses kenaikan kelasnya.',
            'decisions.*.action.in' => 'Pilihan aksi kenaikan kelas tidak valid.',
        ]);

        $sourceYear = AcademicYear::findOrFail($validated['source_year_id']);
        $sourceClass = ClassModel::findOrFail($validated['source_class_id']);
        $targetYear = AcademicYear::findOrFail($validated['target_year_id']);

        $promotedCount = 0;
        $retainedCount = 0;
        $graduatedCount = 0;
        $transferredCount = 0;

        DB::transaction(function () use (
            $validated,
            $sourceYear,
            $sourceClass,
            $targetYear,
            &$promotedCount,
            &$retainedCount,
            &$graduatedCount,
            &$transferredCount
        ) {
            $userAuthId = auth()->id();
            $now = now();

            foreach ($validated['decisions'] as $studentId => $decision) {
                $studentId = (int) $studentId;
                $action = $decision['action'];
                $targetClassId = !empty($decision['target_class_id']) ? (int) $decision['target_class_id'] : null;
                $notes = $decision['notes'] ?? null;

                $student = Student::find($studentId);
                if (!$student) continue;

                if ($action === 'promote') {
                    if (!$targetClassId) {
                        // Gunakan kelas tujuan default jika tidak dispesifikasikan
                        $targetClassId = ClassModel::where('grade', min(6, $sourceClass->grade + 1))
                            ->where('is_active', true)
                            ->value('id') ?? $sourceClass->id;
                    }

                    $targetClassModel = ClassModel::find($targetClassId);
                    $targetClassName = $targetClassModel?->name ?? 'Kelas Baru';

                    // 1. Simpan/update riwayat di Tahun Ajaran Asal (status: promoted)
                    StudentClassHistory::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'academic_year_id' => $sourceYear->id,
                        ],
                        [
                            'class_id' => $sourceClass->id,
                            'status' => 'promoted',
                            'notes' => $notes ?: "Naik ke {$targetClassName}",
                            'promoted_at' => $now,
                            'promoted_by' => $userAuthId,
                        ]
                    );

                    // 2. Buat riwayat di Tahun Ajaran Tujuan (status: active)
                    StudentClassHistory::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'academic_year_id' => $targetYear->id,
                        ],
                        [
                            'class_id' => $targetClassId,
                            'status' => 'active',
                            'notes' => "Naik dari {$sourceClass->name}",
                            'promoted_at' => null,
                            'promoted_by' => null,
                        ]
                    );

                    // 3. Perbarui kelas aktif pada data siswa
                    $student->update([
                        'class_id' => $targetClassId,
                        'student_status' => 'active',
                    ]);

                    $promotedCount++;

                } elseif ($action === 'retain') {
                    // Tinggal Kelas (tetap di jenjang yang sama)
                    $retainClassId = $targetClassId ?: $sourceClass->id;
                    $retainClassModel = ClassModel::find($retainClassId);
                    $retainClassName = $retainClassModel?->name ?? $sourceClass->name;

                    StudentClassHistory::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'academic_year_id' => $sourceYear->id,
                        ],
                        [
                            'class_id' => $sourceClass->id,
                            'status' => 'retained',
                            'notes' => $notes ?: "Tinggal kelas di {$retainClassName}",
                            'promoted_at' => $now,
                            'promoted_by' => $userAuthId,
                        ]
                    );

                    StudentClassHistory::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'academic_year_id' => $targetYear->id,
                        ],
                        [
                            'class_id' => $retainClassId,
                            'status' => 'active',
                            'notes' => "Mengulang di {$retainClassName}",
                        ]
                    );

                    $student->update([
                        'class_id' => $retainClassId,
                        'student_status' => 'active',
                    ]);

                    $retainedCount++;

                } elseif ($action === 'graduate') {
                    // Kelulusan (Khusus Kelas Akhir / Tingkat 6)
                    StudentClassHistory::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'academic_year_id' => $sourceYear->id,
                        ],
                        [
                            'class_id' => $sourceClass->id,
                            'status' => 'graduated',
                            'notes' => $notes ?: 'Lulus dari SDIT Al-Fahmi Palu',
                            'promoted_at' => $now,
                            'promoted_by' => $userAuthId,
                        ]
                    );

                    $student->update([
                        'student_status' => 'graduated',
                        'graduation_year' => $targetYear->start_year ?? date('Y'),
                        'alumni_notes' => 'Alumni lulusan resmi SDIT Al-Fahmi Palu.',
                    ]);

                    $graduatedCount++;

                } elseif ($action === 'transfer') {
                    // Mutasi / Pindah Sekolah
                    StudentClassHistory::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'academic_year_id' => $sourceYear->id,
                        ],
                        [
                            'class_id' => $sourceClass->id,
                            'status' => 'transferred',
                            'notes' => $notes ?: 'Pindah sekolah / mutasi keluar',
                            'promoted_at' => $now,
                            'promoted_by' => $userAuthId,
                        ]
                    );

                    $student->update([
                        'student_status' => 'transferred',
                    ]);

                    $transferredCount++;
                }
            }
        });

        $messageParts = [];
        if ($promotedCount > 0) $messageParts[] = "{$promotedCount} siswa berhasil naik kelas";
        if ($retainedCount > 0) $messageParts[] = "{$retainedCount} siswa tinggal kelas";
        if ($graduatedCount > 0) $messageParts[] = "{$graduatedCount} siswa dinyatakan lulus";
        if ($transferredCount > 0) $messageParts[] = "{$transferredCount} siswa mutasi/pindah";

        $summary = !empty($messageParts) ? implode(', ', $messageParts) : 'Tidak ada perubahan yang diproses';

        return redirect()->route('admin.class-promotions.index', [
            'source_year_id' => $sourceYear->id,
            'source_class_id' => $sourceClass->id,
            'target_year_id' => $targetYear->id,
        ])->with('success', "Alhamdulillah! {$summary}. Riwayat tahun akademik tersimpan aman.");
    }

    /**
     * Halaman Riwayat Kenaikan Kelas per Tahun Ajaran (Arsip Lengkap)
     */
    public function history(Request $request)
    {
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $classes = ClassModel::orderBy('grade')->orderBy('name')->get();

        $yearId = $request->input('academic_year_id');
        $classId = $request->input('class_id');
        $status = $request->input('status');
        $search = trim($request->input('search', ''));

        $query = StudentClassHistory::with(['student.user', 'class', 'academicYear', 'promoter']);

        if ($yearId && $yearId !== 'all') {
            $query->where('academic_year_id', $yearId);
        }

        if ($classId && $classId !== 'all') {
            $query->where('class_id', $classId);
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('nisn', 'like', "%{$search}%")
                   ->orWhere('nis', 'like', "%{$search}%")
                   ->orWhereHas('user', function ($uq) use ($search) {
                       $uq->where('name', 'like', "%{$search}%");
                   });
            });
        }

        $histories = $query->latest('promoted_at')->paginate(25)->withQueryString();

        return view('admin.class-promotions.history', compact('histories', 'academicYears', 'classes', 'yearId', 'classId', 'status', 'search'));
    }
}
