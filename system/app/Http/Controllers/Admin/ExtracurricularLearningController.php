<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Extracurricular;
use App\Models\ExtracurricularAgenda;
use App\Models\ExtracurricularAgendaStudent;
use App\Models\ExtracurricularEvaluation;
use App\Models\ExtracurricularMember;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExtracurricularLearningController extends Controller
{
    public function index(Request $request)
    {
        ExtracurricularAgenda::ensureTableExists();
        ExtracurricularMember::ensureTableExists();

        $user = auth()->user();
        $extracurriculars = Extracurricular::with('categoryModel')->orderBy('name')->get();
        $classes = ClassModel::orderBy('grade')->orderBy('name')->get();
        $activeYear = AcademicYear::getActive();

        $availableGrades = ClassModel::pluck('grade')->filter()->unique()->sort()->values();
        if ($availableGrades->isEmpty()) {
            $availableGrades = collect([1, 2, 3, 4, 5, 6]);
        }

        $selectedExtraId = $request->get('extracurricular_id') ?: ($extracurriculars->first()?->id ?? null);
        $selectedExtra = $selectedExtraId ? $extracurriculars->firstWhere('id', $selectedExtraId) : null;
        $selectedGrade = $request->get('grade');
        $selectedClassId = $request->get('class_id');
        $currentTab = $request->get('tab', 'agenda'); // agenda, history, evaluation

        // Total anggota santri yang sudah terdaftar di kelompok ekskul ini
        $totalEnrolledMembers = 0;
        $memberStudentIds = [];
        if ($selectedExtraId) {
            $memberStudentIds = ExtracurricularMember::where('extracurricular_id', $selectedExtraId)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->pluck('student_id')
                ->toArray();
            $totalEnrolledMembers = count($memberStudentIds);
        }

        // Santri untuk form input agenda / evaluasi
        $students = collect();
        if (!empty($memberStudentIds)) {
            $stQuery = Student::with(['user', 'class'])
                ->whereIn('id', $memberStudentIds)
                ->where(function($q) {
                    $q->whereIn('student_status', ['active', 'Aktif'])->orWhereNull('student_status');
                });

            if ($selectedClassId && $selectedClassId !== 'all') {
                $stQuery->where('class_id', $selectedClassId);
            } elseif ($selectedGrade && $selectedGrade !== 'all') {
                $gradeClassIds = $this->getClassIdsByGrade((int) $selectedGrade);
                $stQuery->whereIn('class_id', $gradeClassIds);
            }

            $students = $stQuery->get()->sortBy(fn($s) => $s->user?->name ?? '');
        } elseif ($selectedClassId && $selectedClassId !== 'all') {
            // Fallback jika belum mengatur kelompok ekskul
            $students = Student::with(['user', 'class'])
                ->where('class_id', $selectedClassId)
                ->where(function($q) {
                    $q->whereIn('student_status', ['active', 'Aktif'])->orWhereNull('student_status');
                })
                ->get()
                ->sortBy(fn($s) => $s->user?->name ?? '');
        }

        // Riwayat Agenda
        $agendaQuery = ExtracurricularAgenda::with(['extracurricular.categoryModel', 'coach', 'students.student.user'])
            ->latest('date')
            ->latest('id');

        if ($selectedExtraId) {
            $agendaQuery->where('extracurricular_id', $selectedExtraId);
        }

        $agendas = $agendaQuery->paginate(10, ['*'], 'agenda_page')->withQueryString();

        // Data Penilaian Capaian Ekskul untuk Rapor
        $evaluations = [];
        if ($selectedExtraId && $selectedClassId) {
            $evalRecords = ExtracurricularEvaluation::where('extracurricular_id', $selectedExtraId)
                ->where('academic_year_id', $activeYear?->id)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');

            foreach ($students as $st) {
                $eval = $evalRecords->get($st->id);
                $evaluations[$st->id] = [
                    'predicate' => $eval?->predicate ?? 'A',
                    'score' => $eval?->score ?? 85,
                    'description' => $eval?->description ?? 'Aktif mengikuti kegiatan ekstrakurikuler dengan penuh antusias dan disiplin tinggi.',
                ];
            }
        }

        // Data Rekapitulasi Pembina Ekskul (Khusus Admin & Kepala Sekolah)
        $coaches = User::whereIn('id', ExtracurricularAgenda::pluck('coach_id'))
            ->orWhereHas('roles', fn($q) => $q->whereIn('slug', ['guru', 'teacher']))
            ->distinct()
            ->get();

        $coachSummaries = $coaches->map(function($coach) use ($activeYear) {
            $agendas = ExtracurricularAgenda::with(['extracurricular', 'students'])
                ->where('coach_id', $coach->id)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->get();

            $totalMeetings = $agendas->count();
            if ($totalMeetings === 0) return null;

            $approvedMeetings = $agendas->where('status', 'approved')->count();
            $pendingMeetings = $agendas->where('status', 'submitted')->count();
            $rejectedMeetings = $agendas->where('status', 'rejected')->count();
            $extraNames = $agendas->pluck('extracurricular.name')->filter()->unique()->implode(', ');

            $allStudents = $agendas->flatMap->students;
            $attendedCount = $allStudents->where('attendance_status', 'hadir')->count();
            $avgSkill = $allStudents->avg('score_skill') ?? 0;
            $avgAttitude = $allStudents->avg('score_attitude') ?? 0;

            return (object)[
                'coach' => $coach,
                'extra_names' => $extraNames ?: 'Ekstrakurikuler',
                'total_meetings' => $totalMeetings,
                'approved_meetings' => $approvedMeetings,
                'pending_meetings' => $pendingMeetings,
                'rejected_meetings' => $rejectedMeetings,
                'attended_count' => $attendedCount,
                'avg_skill' => round($avgSkill, 1),
                'avg_attitude' => round($avgAttitude, 1),
                'latest_teaching' => $agendas->sortByDesc('date')->first()?->date,
            ];
        })->filter()->sortByDesc('total_meetings')->values();

        return view('admin.extracurriculars.learning', compact(
            'extracurriculars',
            'classes',
            'availableGrades',
            'activeYear',
            'selectedExtraId',
            'selectedExtra',
            'selectedGrade',
            'selectedClassId',
            'totalEnrolledMembers',
            'currentTab',
            'students',
            'agendas',
            'evaluations',
            'coachSummaries'
        ));
    }

    public function storeAgenda(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'extracurricular_id' => 'required|exists:extracurriculars,id',
            'date' => 'required|date',
            'meeting_number' => 'nullable|integer|min:1',
            'activity_title' => 'required|string|max:200',
            'material_taught' => 'required|string',
            'location' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
            'students' => 'required|array|min:1',
            'students.*.attendance' => 'nullable|in:hadir,sakit,izin,alpa',
            'students.*.skill' => 'nullable|numeric|min:0|max:100',
            'students.*.attitude' => 'nullable|numeric|min:0|max:100',
            'students.*.notes' => 'nullable|string',
        ], [
            'extracurricular_id.required' => 'Pilih ekstrakurikuler terlebih dahulu.',
            'activity_title.required' => 'Judul atau agenda kegiatan wajib diisi.',
            'material_taught.required' => 'Materi/latihan yang diajarkan wajib diisi.',
            'students.required' => 'Daftar absensi santri belum tersedia.',
        ]);

        DB::beginTransaction();
        try {
            ExtracurricularAgenda::ensureTableExists();

            $agenda = ExtracurricularAgenda::create([
                'extracurricular_id' => $validated['extracurricular_id'],
                'coach_id' => $user->id,
                'academic_year_id' => AcademicYear::getActive()?->id,
                'date' => $validated['date'],
                'meeting_number' => $validated['meeting_number'] ?? 1,
                'activity_title' => $validated['activity_title'],
                'material_taught' => $validated['material_taught'],
                'location' => $validated['location'] ?? 'Area Kampus Sekolah',
                'notes' => $validated['notes'] ?? null,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            foreach ($validated['students'] as $studentId => $item) {
                $status = $item['attendance'] ?? 'hadir';
                $scoreSkill = isset($item['skill']) && is_numeric($item['skill']) ? (float)$item['skill'] : 80;
                $scoreAttitude = isset($item['attitude']) && is_numeric($item['attitude']) ? (float)$item['attitude'] : 80;

                ExtracurricularAgendaStudent::create([
                    'extracurricular_agenda_id' => $agenda->id,
                    'student_id' => $studentId,
                    'attendance_status' => $status,
                    'score_skill' => $scoreSkill,
                    'score_attitude' => $scoreAttitude,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.extracurriculars.learning.index', [
                'tab' => 'history',
                'extracurricular_id' => $validated['extracurricular_id']
            ])->with('success', 'Agenda pembelajaran & absensi ekstrakurikuler berhasil diajukan untuk disetujui Kepala Sekolah!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan agenda ekstrakurikuler: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Setujui Agenda Pembelajaran oleh Kepala Sekolah
     */
    public function approveAgenda(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            abort(403, 'Hanya Kepala Sekolah dan Administrator yang berhak menyetujui laporan pembelajaran.');
        }

        $agenda = ExtracurricularAgenda::findOrFail($id);
        $agenda->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('approval_notes'),
        ]);

        return back()->with('success', 'Laporan agenda ekskul berhasil disetujui (Approved).');
    }

    /**
     * Minta Revisi / Tolak Agenda Pembelajaran oleh Kepala Sekolah
     */
    public function rejectAgenda(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            abort(403, 'Hanya Kepala Sekolah dan Administrator yang berhak mengelola persetujuan.');
        }

        $agenda = ExtracurricularAgenda::findOrFail($id);
        $agenda->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->input('approval_notes') ?? 'Mohon periksa kembali materi/absensi latihan.',
        ]);

        return back()->with('success', 'Laporan agenda ekskul dikembalikan untuk revisi.');
    }

    public function destroyAgenda($id)
    {
        $user = auth()->user();
        $agenda = ExtracurricularAgenda::findOrFail($id);

        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah']) && $agenda->coach_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses menghapus agenda ini.');
        }

        $agenda->delete();

        return back()->with('success', 'Agenda ekstrakurikuler berhasil dihapus.');
    }

    public function saveEvaluations(Request $request)
    {
        $validated = $request->validate([
            'extracurricular_id' => 'required|exists:extracurriculars,id',
            'class_id' => 'required|exists:classes,id',
            'semester' => 'nullable|string',
            'evaluations' => 'required|array',
            'evaluations.*.predicate' => 'required|string',
            'evaluations.*.score' => 'nullable|numeric|min:0|max:100',
            'evaluations.*.description' => 'required|string',
        ]);

        $activeYear = AcademicYear::getActive();
        $semester = $validated['semester'] ?? ($activeYear?->semester ?? 'ganjil');
        $user = auth()->user();

        DB::beginTransaction();
        try {
            ExtracurricularAgenda::ensureTableExists();

            foreach ($validated['evaluations'] as $studentId => $data) {
                ExtracurricularEvaluation::updateOrCreate(
                    [
                        'extracurricular_id' => $validated['extracurricular_id'],
                        'student_id' => $studentId,
                        'academic_year_id' => $activeYear?->id,
                        'semester' => $semester,
                    ],
                    [
                        'predicate' => $data['predicate'],
                        'score' => $data['score'] ?? 85,
                        'description' => $data['description'],
                        'evaluated_by' => $user->id,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.extracurriculars.learning.index', [
                'tab' => 'evaluation',
                'extracurricular_id' => $validated['extracurricular_id'],
                'class_id' => $validated['class_id']
            ])->with('success', 'Penilaian capaian ekstrakurikuler untuk rapor berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Dapatkan daftar ID kelas berdasarkan tingkat (grade).
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
     * Ambil data santri untuk Modal Atur Kelompok Ekstrakurikuler (AJAX)
     */
    public function getGroupStudents(Request $request)
    {
        ExtracurricularMember::ensureTableExists();

        $extraId = (int) $request->get('extracurricular_id');
        $extra = Extracurricular::find($extraId);
        $grade = $request->get('grade'); // int or 'all'
        $classId = $request->get('class_id');
        $activeYear = AcademicYear::getActive();

        $classQuery = ClassModel::query();
        if ($grade && $grade !== 'all') {
            $classIdsInGrade = $this->getClassIdsByGrade((int) $grade);
            $classQuery->whereIn('id', $classIdsInGrade);
        }
        $classes = $classQuery->orderBy('grade')->orderBy('name')->get(['id', 'name', 'grade']);

        $studentQuery = Student::with(['user', 'class'])
            ->where(function ($q) {
                $q->whereIn('student_status', ['active', 'Aktif'])->orWhereNull('student_status');
            });

        if ($classId && $classId !== 'all') {
            $studentQuery->where('class_id', $classId);
        } elseif ($grade && $grade !== 'all') {
            $classIdsInGrade = $this->getClassIdsByGrade((int) $grade);
            $studentQuery->whereIn('class_id', $classIdsInGrade);
        }

        $allStudents = $studentQuery->orderBy('nisn')->get();

        // Anggota saat ini untuk ekskul ini
        $currentMemberIds = ExtracurricularMember::where('extracurricular_id', $extraId)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->pluck('student_id')
            ->toArray();

        // Ekskul lain yang diikuti santri untuk referensi pembina
        $otherMemberships = ExtracurricularMember::with(['extracurricular.categoryModel'])
            ->where('extracurricular_id', '!=', $extraId)
            ->whereIn('student_id', $allStudents->pluck('id'))
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->get()
            ->groupBy('student_id');

        $studentsData = $allStudents->map(function ($st) use ($currentMemberIds, $otherMemberships) {
            $isInGroup = in_array($st->id, $currentMemberIds);
            $otherExtras = $otherMemberships->get($st->id, collect())
                ->map(function ($m) {
                    if (!$m->extracurricular) return null;
                    return $m->extracurricular->name . ' (' . $m->extracurricular->category_label . ')';
                })
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            return [
                'id' => $st->id,
                'name' => $st->user?->name ?? 'Santri',
                'nisn' => $st->nisn ?? $st->nis ?? '-',
                'class_id' => $st->class_id,
                'class_name' => $st->class?->name ?? '-',
                'grade' => $st->class?->level ?? '-',
                'gender' => $st->gender ?? 'male',
                'is_in_group' => $isInGroup,
                'other_extras' => $otherExtras,
            ];
        });

        return response()->json([
            'extracurricular_id' => $extraId,
            'extracurricular_name' => $extra?->name ?? 'Ekstrakurikuler',
            'grade' => $grade,
            'class_id' => $classId,
            'classes' => $classes,
            'students' => $studentsData,
            'total_students' => $studentsData->count(),
            'in_group_count' => $studentsData->where('is_in_group', true)->count(),
            'all_in_group_count' => count($currentMemberIds),
        ]);
    }

    /**
     * Simpan Pengelompokan Santri Ekstrakurikuler (AJAX / POST)
     */
    public function saveGroup(Request $request)
    {
        ExtracurricularMember::ensureTableExists();

        $extraId = (int) $request->input('extracurricular_id');
        $extra = Extracurricular::findOrFail($extraId);
        $activeYear = AcademicYear::getActive();

        $studentIds = $request->input('student_ids', []);
        if (!is_array($studentIds)) {
            $studentIds = [];
        }
        $studentIds = array_map('intval', $studentIds);

        $scopeGrade = $request->input('scope_grade'); // 'all' atau tingkat tertentu
        $scopeClassId = $request->input('scope_class_id');

        DB::transaction(function () use ($extraId, $studentIds, $scopeGrade, $scopeClassId, $activeYear) {
            $query = ExtracurricularMember::where('extracurricular_id', $extraId)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id));

            if ($scopeClassId && $scopeClassId !== 'all') {
                $classStudentIds = Student::where('class_id', $scopeClassId)->pluck('id')->toArray();
                $query->whereIn('student_id', $classStudentIds)
                      ->whereNotIn('student_id', $studentIds)
                      ->delete();
            } elseif ($scopeGrade && $scopeGrade !== 'all') {
                $classIds = $this->getClassIdsByGrade((int) $scopeGrade);
                $gradeStudentIds = Student::whereIn('class_id', $classIds)->pluck('id')->toArray();
                $query->whereIn('student_id', $gradeStudentIds)
                      ->whereNotIn('student_id', $studentIds)
                      ->delete();
            } else {
                // Hapus santri yang di-uncheck
                $query->whereNotIn('student_id', $studentIds)->delete();
            }

            // Daftarkan santri yang dipilih
            foreach ($studentIds as $sid) {
                ExtracurricularMember::firstOrCreate([
                    'extracurricular_id' => $extraId,
                    'student_id' => $sid,
                    'academic_year_id' => $activeYear?->id,
                ]);
            }
        });

        $totalCount = ExtracurricularMember::where('extracurricular_id', $extraId)
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Alhamdulillah! Susunan kelompok santri ekstrakurikuler {$extra->name} berhasil diperbarui ({$totalCount} santri).",
                'total_count' => $totalCount,
            ]);
        }

        return redirect()->back()->with('success', "Alhamdulillah! Susunan kelompok santri ekstrakurikuler {$extra->name} berhasil diperbarui ({$totalCount} santri).");
    }
}
