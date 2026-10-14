<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Grade;
use App\Models\GradeHistory;
use App\Models\Student;
use App\Models\TeacherSubjectAssignment;
use App\Models\TeachingAgenda;
use App\Models\TeachingAgendaStudent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource with 4 tabs:
     * Tab 1: Agenda Kelas & Penilaian Bidang Studi
     * Tab 2: Histori Mengajar Saya
     * Tab 3: Rerata Nilai Rapor Siswa
     * Tab 4: Input Nilai Rapor (Harian Otomatis, PTS, PAS)
     */
    public function index(Request $request)
    {
        TeachingAgenda::ensureTableExists();
        TeacherSubjectAssignment::ensureTableExists();
        GradeHistory::ensureTableExists();

        $user = auth()->user();
        $activeYear = AcademicYear::getActive();
        $allowedClassIds = $user->getAssignedClassIds();

        $tab = $request->get('tab', 'agenda'); // 'agenda', 'history', 'summary', 'raport_input'

        // Daftar Kelas yang diizinkan untuk guru
        $classesQuery = ClassModel::withCount('students')->orderBy('name', 'asc');
        if ($allowedClassIds !== null) {
            $classesQuery->whereIn('id', $allowedClassIds);
        }
        $classes = $classesQuery->get();

        // Kelas yang sedang aktif dipilih untuk Tab Agenda
        $selectedClassId = $request->get('class_id') ?? ($classes->first()?->id ?? null);
        if ($selectedClassId && $allowedClassIds !== null && !in_array($selectedClassId, $allowedClassIds)) {
            $selectedClassId = $classes->first()?->id ?? null;
        }

        $selectedClass = $selectedClassId ? ClassModel::find($selectedClassId) : null;
        $studentsInClass = $selectedClassId 
            ? Student::with('user')->where('class_id', $selectedClassId)->orderBy('nisn', 'asc')->get() 
            : collect();

        // Daftar Mata Pelajaran untuk Guru & Kelas terpilih
        $allSchoolSubjects = $this->getAllSchoolSubjects();
        if ($user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            $teacherSubjects = $allSchoolSubjects;
        } else {
            $assignedForClass = $user->getAssignedSubjects($selectedClassId);
            $teacherSubjects = (!empty($assignedForClass)) ? $assignedForClass : ($user->getAssignedSubjects() ?: $allSchoolSubjects);
        }

        $selectedSubject = $request->get('subject') ?? ($teacherSubjects[0] ?? null);
        if ($selectedSubject && !in_array($selectedSubject, $teacherSubjects) && !$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            $selectedSubject = $teacherSubjects[0] ?? null;
        }

        // Data Histori Mengajar (Tab 2)
        $historyQuery = TeachingAgenda::with(['class', 'students.student.user', 'teacher'])->latest('date')->latest('id');
        if ($allowedClassIds !== null) {
            $historyQuery->whereIn('class_id', $allowedClassIds);
        }
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            $historyQuery->where('teacher_id', $user->id);
        }
        if ($activeYear) {
            $historyQuery->where('academic_year_id', $activeYear->id);
        }
        $teachingAgendas = $historyQuery->paginate(10, ['*'], 'history_page')->withQueryString();

        // Data Rerata Nilai Rapor Siswa (Tab 3)
        $allowedSubjects = $user->getAssignedSubjects();
        $query = Grade::with(['student', 'class', 'recordedBy'])
            ->when($allowedClassIds !== null, function ($q) use ($allowedClassIds) {
                return $q->whereIn('class_id', $allowedClassIds);
            })
            ->when($allowedSubjects !== null && !empty($allowedSubjects), function ($q) use ($allowedSubjects) {
                return $q->whereIn('subject', $allowedSubjects);
            })
            ->when($activeYear, function ($q) use ($activeYear) {
                return $q->where('academic_year_id', $activeYear->id);
            })
            ->when($request->major_id, function ($q) use ($request) {
                return $q->whereHas('class', function ($cq) use ($request) {
                    $cq->where('major_id', $request->major_id);
                });
            })
            ->when($request->class_id, function ($q) use ($request) {
                return $q->where('class_id', $request->class_id);
            })
            ->when($request->student_id, function ($q) use ($request) {
                return $q->where('student_id', $request->student_id);
            })
            ->when($request->subject, function ($q) use ($request) {
                return $q->where('subject', $request->subject);
            })
            ->when($request->type, function ($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->latest();

        $grades = $query->paginate(20)->withQueryString();
        $students = $allowedClassIds !== null 
            ? Student::with(['user', 'class'])->whereIn('class_id', $allowedClassIds)->get() 
            : Student::with(['user', 'class'])->get();
        $types = ['daily', 'mid_term', 'final_term', 'exam'];
        $subjects = $allSchoolSubjects;

        // Data Nilai Rapor (Harian, PTS, PAS) untuk Tab Input Nilai Rapor (Tab 4)
        $existingGrades = collect();
        $autoDailyScores = collect();
        $gradeHistories = collect();

        if ($selectedClassId && $selectedSubject) {
            // 1. Ambil nilai rapor yang tersimpan untuk kelas, mapel, dan tahun ajaran aktif
            $existingGrades = Grade::where('class_id', $selectedClassId)
                ->where('subject', $selectedSubject)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->get()
                ->groupBy('student_id');

            // 2. Kalkulasi Nilai Harian Otomatis dari Rerata Agenda Mengajar (score_cognitive)
            $agendaIds = TeachingAgenda::where('class_id', $selectedClassId)
                ->where('subject', $selectedSubject)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->pluck('id');

            if ($agendaIds->isNotEmpty()) {
                $autoDailyScores = TeachingAgendaStudent::whereIn('teaching_agenda_id', $agendaIds)
                    ->whereNotNull('score_cognitive')
                    ->selectRaw('student_id, ROUND(AVG(score_cognitive), 1) as avg_score, COUNT(id) as total_agenda')
                    ->groupBy('student_id')
                    ->get()
                    ->keyBy('student_id');
            }

            // 3. Ambil riwayat modifikasi nilai untuk siswa-siswa di kelas ini
            $gradeHistories = GradeHistory::with('user')
                ->where('class_id', $selectedClassId)
                ->where('subject', $selectedSubject)
                ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->latest()
                ->get()
                ->groupBy('student_id');
        }

        $wDaily = (float) \App\Models\Setting::get('raport_weight_daily', 50);
        $wMid = (float) \App\Models\Setting::get('raport_weight_mid', 25);
        $wFinal = (float) \App\Models\Setting::get('raport_weight_final', 25);
        $totalWeight = ($wDaily + $wMid + $wFinal) ?: 100;

        return view('admin.grades.index', compact(
            'tab',
            'classes',
            'selectedClassId',
            'selectedClass',
            'studentsInClass',
            'teacherSubjects',
            'selectedSubject',
            'teachingAgendas',
            'grades',
            'students',
            'types',
            'subjects',
            'existingGrades',
            'autoDailyScores',
            'gradeHistories',
            'activeYear',
            'wDaily',
            'wMid',
            'wFinal',
            'totalWeight'
        ));
    }

    /**
     * Simpan Nilai Harian, Nilai PTS, dan Nilai PAS Siswa Sekaligus
     * Mendukung kepatuhan Tahun Ajaran dan pencatatan Audit History Perubahan Nilai
     */
    public function saveRaportGrades(Request $request)
    {
        GradeHistory::ensureTableExists();

        $user = auth()->user();
        $classId = (int) $request->class_id;
        $subject = (string) $request->subject;

        if (!$user->canTeachSubjectInClass($classId, $subject)) {
            abort(403, 'Anda tidak memiliki hak akses menginput nilai untuk kelas dan mata pelajaran ini.');
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:150',
            'grades' => 'required|array',
        ]);

        $activeYear = AcademicYear::getActive();
        $academicYearId = $activeYear?->id;
        $semester = $activeYear?->semester ?? '1';

        DB::beginTransaction();
        try {
            $today = date('Y-m-d');

            foreach ($validated['grades'] as $studentId => $item) {
                $notes = $item['notes'] ?? null;

                // 1. Nilai Harian (Daily / Formatif)
                if (isset($item['daily']) && $item['daily'] !== '' && is_numeric($item['daily'])) {
                    $newDaily = (float) $item['daily'];
                    $existingGrade = Grade::where([
                        'student_id' => $studentId,
                        'class_id' => $classId,
                        'subject' => $subject,
                        'type' => 'daily',
                    ])
                    ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
                    ->first();

                    $oldScore = $existingGrade ? (float) $existingGrade->score : null;

                    // Jika nilai diubah manual, catat riwayatnya
                    if ($existingGrade && abs($oldScore - $newDaily) > 0.01) {
                        GradeHistory::create([
                            'student_id' => $studentId,
                            'class_id' => $classId,
                            'academic_year_id' => $academicYearId,
                            'semester' => $semester,
                            'subject' => $subject,
                            'type' => 'daily',
                            'old_score' => $oldScore,
                            'new_score' => $newDaily,
                            'source' => 'manual',
                            'changed_by' => $user->id,
                            'notes' => 'Nilai Harian diedit manual oleh ' . $user->name . ($notes ? ": $notes" : ""),
                        ]);
                    } elseif (!$existingGrade) {
                        GradeHistory::create([
                            'student_id' => $studentId,
                            'class_id' => $classId,
                            'academic_year_id' => $academicYearId,
                            'semester' => $semester,
                            'subject' => $subject,
                            'type' => 'daily',
                            'old_score' => null,
                            'new_score' => $newDaily,
                            'source' => 'manual',
                            'changed_by' => $user->id,
                            'notes' => 'Input pertama kali oleh ' . $user->name . ($notes ? ": $notes" : ""),
                        ]);
                    }

                    Grade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'class_id' => $classId,
                            'academic_year_id' => $academicYearId,
                            'semester' => $semester,
                            'subject' => $subject,
                            'type' => 'daily',
                        ],
                        [
                            'score' => $newDaily,
                            'notes' => $notes ?? 'Nilai Harian / Formatif',
                            'recorded_by' => $user->id,
                            'date' => $today,
                        ]
                    );
                }

                // 2. Nilai PTS / UTS (Mid Term)
                if (isset($item['mid_term']) && $item['mid_term'] !== '' && is_numeric($item['mid_term'])) {
                    $newMid = (float) $item['mid_term'];
                    $existingMid = Grade::where([
                        'student_id' => $studentId,
                        'class_id' => $classId,
                        'subject' => $subject,
                        'type' => 'mid_term',
                    ])
                    ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
                    ->first();

                    $oldMid = $existingMid ? (float) $existingMid->score : null;

                    if ($existingMid && abs($oldMid - $newMid) > 0.01) {
                        GradeHistory::create([
                            'student_id' => $studentId,
                            'class_id' => $classId,
                            'academic_year_id' => $academicYearId,
                            'semester' => $semester,
                            'subject' => $subject,
                            'type' => 'mid_term',
                            'old_score' => $oldMid,
                            'new_score' => $newMid,
                            'source' => 'manual',
                            'changed_by' => $user->id,
                            'notes' => 'Nilai PTS diedit manual oleh ' . $user->name,
                        ]);
                    }

                    Grade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'class_id' => $classId,
                            'academic_year_id' => $academicYearId,
                            'semester' => $semester,
                            'subject' => $subject,
                            'type' => 'mid_term',
                        ],
                        [
                            'score' => $newMid,
                            'notes' => $notes ?? 'Penilaian Tengah Semester (PTS)',
                            'recorded_by' => $user->id,
                            'date' => $today,
                        ]
                    );
                }

                // 3. Nilai PAS / UAS (Final Term)
                if (isset($item['final_term']) && $item['final_term'] !== '' && is_numeric($item['final_term'])) {
                    $newFinal = (float) $item['final_term'];
                    $existingFinal = Grade::where([
                        'student_id' => $studentId,
                        'class_id' => $classId,
                        'subject' => $subject,
                        'type' => 'final_term',
                    ])
                    ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
                    ->first();

                    $oldFinal = $existingFinal ? (float) $existingFinal->score : null;

                    if ($existingFinal && abs($oldFinal - $newFinal) > 0.01) {
                        GradeHistory::create([
                            'student_id' => $studentId,
                            'class_id' => $classId,
                            'academic_year_id' => $academicYearId,
                            'semester' => $semester,
                            'subject' => $subject,
                            'type' => 'final_term',
                            'old_score' => $oldFinal,
                            'new_score' => $newFinal,
                            'source' => 'manual',
                            'changed_by' => $user->id,
                            'notes' => 'Nilai PAS diedit manual oleh ' . $user->name,
                        ]);
                    }

                    Grade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'class_id' => $classId,
                            'academic_year_id' => $academicYearId,
                            'semester' => $semester,
                            'subject' => $subject,
                            'type' => 'final_term',
                        ],
                        [
                            'score' => $newFinal,
                            'notes' => $notes ?? 'Penilaian Akhir Semester (PAS)',
                            'recorded_by' => $user->id,
                            'date' => $today,
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()->route('admin.grades.index', [
                'tab' => 'raport_input',
                'class_id' => $classId,
                'subject' => $subject,
            ])->with('success', 'Nilai Harian, PTS, dan PAS siswa berhasil disimpan dan riwayat perubahan tercatat rapi!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan nilai rapor: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Simpan Agenda Pembelajaran Kelas & Penilaian Siswa Bidang Studi
     */
    public function storeAgenda(Request $request)
    {
        $user = auth()->user();
        $classId = (int) $request->class_id;
        $subject = (string) $request->subject;

        if (!$user->canTeachSubjectInClass($classId, $subject)) {
            abort(403, 'Anda tidak memiliki hak akses menginput agenda untuk kelas dan mata pelajaran ini.');
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:150',
            'date' => 'required|date',
            'material_taught' => 'required|string',
            'class_notes' => 'nullable|string',
            'students' => 'required|array',
        ], [
            'class_id.required' => 'Pilih kelas terlebih dahulu.',
            'subject.required' => 'Mata pelajaran wajib dipilih/diisi.',
            'material_taught.required' => 'Materi pembelajaran wajib diisi.',
            'students.required' => 'Data penilaian dan absensi santri belum tersedia.',
        ]);

        $activeYear = AcademicYear::getActive();
        $academicYearId = $activeYear?->id;
        $semester = $activeYear?->semester ?? '1';

        DB::beginTransaction();
        try {
            TeachingAgenda::ensureTableExists();

            $agenda = TeachingAgenda::create([
                'teacher_id' => $user->id,
                'class_id' => $validated['class_id'],
                'academic_year_id' => $academicYearId,
                'subject' => $validated['subject'],
                'date' => $validated['date'],
                'material_taught' => $validated['material_taught'],
                'class_notes' => $validated['class_notes'] ?? null,
            ]);

            foreach ($validated['students'] as $studentId => $item) {
                $status = $item['attendance'] ?? 'hadir';
                $scoreCognitive = isset($item['cognitive']) && is_numeric($item['cognitive']) ? (float)$item['cognitive'] : 80;
                $scoreAdab = isset($item['adab']) && is_numeric($item['adab']) ? (float)$item['adab'] : 80;

                TeachingAgendaStudent::create([
                    'teaching_agenda_id' => $agenda->id,
                    'student_id' => $studentId,
                    'attendance_status' => $status,
                    'score_cognitive' => $scoreCognitive,
                    'score_adab' => $scoreAdab,
                ]);

                // Sync ke tabel grades (Nilai Harian Mapel Rapor)
                Grade::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'class_id' => $validated['class_id'],
                        'subject' => $validated['subject'],
                        'type' => 'daily',
                        'date' => $validated['date'],
                    ],
                    [
                        'score' => $scoreCognitive,
                        'notes' => 'Materi: ' . Str::limit($validated['material_taught'], 80) . ' [Adab: ' . $scoreAdab . ']',
                        'recorded_by' => $user->id,
                    ]
                );

                // Sinkronisasi kehadiran siswa
                try {
                    if (class_exists(\App\Models\Attendance::class) && Schema::hasTable('attendances')) {
                        \App\Models\Attendance::updateOrCreate(
                            [
                                'student_id' => $studentId,
                                'date' => $validated['date'],
                            ],
                            [
                                'class_id' => $validated['class_id'],
                                'status' => $status,
                                'notes' => 'Sesi Mapel: ' . $validated['subject'],
                            ]
                        );
                    }
                } catch (\Throwable $e) {}
            }

            DB::commit();

            return redirect()->route('admin.grades.index', ['tab' => 'history', 'class_id' => $validated['class_id']])
                ->with('success', 'Agenda pembelajaran kelas & evaluasi siswa berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan agenda pembelajaran: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus Agenda Pembelajaran
     */
    public function destroyAgenda($id)
    {
        $user = auth()->user();
        $agenda = TeachingAgenda::findOrFail($id);

        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah']) && $agenda->teacher_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses menghapus agenda ini.');
        }

        $agenda->delete();

        return back()->with('success', 'Agenda pembelajaran berhasil dihapus.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();

        $classes = $allowedClassIds !== null ? ClassModel::whereIn('id', $allowedClassIds)->get() : ClassModel::all();
        $students = $allowedClassIds !== null 
            ? Student::with('class')->whereIn('class_id', $allowedClassIds)->get() 
            : Student::with('class')->get();
        $types = ['daily', 'mid_term', 'final_term', 'exam'];
        $subjects = $this->getSubjects();

        return view('admin.grades.create', compact('classes', 'students', 'types', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();
        if ($allowedClassIds !== null && !in_array($request->class_id, $allowedClassIds)) {
            abort(403, 'Anda tidak memiliki hak akses menginput nilai untuk kelas ini.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:255',
            'type' => 'required|in:daily,mid_term,final_term,exam',
            'score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['recorded_by'] = Auth::id();

        Grade::create($validated);

        return redirect()->route('admin.grades.index')
            ->with('success', 'Nilai berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Grade $grade)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();
        if ($allowedClassIds !== null && !in_array($grade->class_id, $allowedClassIds)) {
            abort(403, 'Anda tidak memiliki hak akses melihat nilai ini.');
        }

        return view('admin.grades.show', compact('grade'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grade $grade)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();
        if ($allowedClassIds !== null && !in_array($grade->class_id, $allowedClassIds)) {
            abort(403, 'Anda tidak memiliki hak akses mengedit nilai di kelas ini.');
        }

        $classes = $allowedClassIds !== null ? ClassModel::whereIn('id', $allowedClassIds)->get() : ClassModel::all();
        $students = $allowedClassIds !== null 
            ? Student::whereIn('class_id', $allowedClassIds)->get() 
            : Student::all();
        $types = ['daily', 'mid_term', 'final_term', 'exam'];
        $subjects = $this->getSubjects();

        return view('admin.grades.edit', compact('grade', 'classes', 'students', 'types', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grade)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();
        if ($allowedClassIds !== null && !in_array($grade->class_id, $allowedClassIds)) {
            abort(403, 'Anda tidak memiliki hak akses mengubah nilai di kelas ini.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:255',
            'type' => 'required|in:daily,mid_term,final_term,exam',
            'score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $grade->update($validated);

        return redirect()->route('admin.grades.index')
            ->with('success', 'Nilai berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();
        if ($allowedClassIds !== null && !in_array($grade->class_id, $allowedClassIds)) {
            abort(403, 'Anda tidak memiliki hak akses menghapus nilai di kelas ini.');
        }

        $grade->delete();

        return redirect()->route('admin.grades.index')
            ->with('success', 'Nilai berhasil dihapus!');
    }

    /**
     * Bulk grade entry form
     */
    public function bulkCreate()
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();

        $classes = $allowedClassIds !== null ? ClassModel::whereIn('id', $allowedClassIds)->get() : ClassModel::all();
        $students = $allowedClassIds !== null 
            ? Student::with('class')->whereIn('class_id', $allowedClassIds)->get() 
            : Student::with('class')->get();
        $types = ['daily', 'mid_term', 'final_term', 'exam'];
        $subjects = $this->getSubjects();

        return view('admin.grades.bulk-create', compact('classes', 'students', 'types', 'subjects'));
    }

    /**
     * Store bulk grades
     */
    public function bulkStore(Request $request)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();
        if ($allowedClassIds !== null && !in_array($request->class_id, $allowedClassIds)) {
            abort(403, 'Anda tidak memiliki hak akses menginput nilai untuk kelas ini.');
        }

        if ($request->has('grades') && is_array($request->grades)) {
            $filteredGrades = array_filter($request->grades, function ($item) {
                return isset($item['score']) && $item['score'] !== null && $item['score'] !== '';
            });
            $request->merge(['grades' => $filteredGrades]);
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:255',
            'type' => 'required|in:daily,mid_term,final_term,exam',
            'grades' => 'required|array|min:1',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.score' => 'required|numeric|min:0|max:100',
        ], [
            'grades.required' => 'Silakan pilih siswa dan isi nilai yang valid (0 - 100).',
            'grades.min' => 'Silakan pilih sekurang-kurangnya satu siswa dan isi nilainya.',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['grades'] as $grade) {
                Grade::updateOrCreate(
                    [
                        'student_id' => $grade['student_id'],
                        'subject' => $validated['subject'],
                        'type' => $validated['type'],
                    ],
                    [
                        'class_id' => $validated['class_id'],
                        'score' => $grade['score'],
                        'recorded_by' => Auth::id(),
                    ]
                );
            }
        });

        return redirect()->route('admin.grades.index')
            ->with('success', 'Nilai berhasil disimpan untuk siswa terpilih!');
    }

    /**
     * Download Excel template for importing grades
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Nilai');

        // Header
        $headers = ['NISN', 'Nama Siswa', 'Kelas', 'Mata Pelajaran', 'Tipe Nilai', 'Nilai', 'Catatan'];
        $sheet->fromArray([$headers], null, 'A1');

        // Style Header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Sample Rows
        $sampleData = [
            ['1234567890', 'Budi Santoso', 'X IPA 1', 'Matematika', 'Harian', 85, 'Tugas 1 Bagus'],
            ['0987654321', 'Siti Rahma', 'X IPA 1', 'Matematika', 'UTS', 90, 'Ujian Tengah Semester'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Nilai_Siswa.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export grades to Excel file (.xlsx) based on active filters
     */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $query = Grade::with(['student.user', 'class', 'recordedBy'])
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->where('recorded_by', $user->id);
            })
            ->when($request->class_id, function ($q) use ($request) {
                return $q->where('class_id', $request->class_id);
            })
            ->when($request->student_id, function ($q) use ($request) {
                return $q->where('student_id', $request->student_id);
            })
            ->when($request->subject, function ($q) use ($request) {
                return $q->where('subject', $request->subject);
            })
            ->when($request->type, function ($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->latest();

        $grades = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Nilai Siswa');

        // Header
        $headers = ['No', 'NISN', 'Nama Siswa', 'Kelas', 'Mata Pelajaran', 'Tipe Nilai', 'Nilai', 'Catatan', 'Tanggal Diinput'];
        $sheet->fromArray([$headers], null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        $typeLabels = [
            'daily' => 'Harian',
            'mid_term' => 'UTS',
            'final_term' => 'UAS',
            'exam' => 'Ujian',
        ];

        $rows = [];
        foreach ($grades as $index => $g) {
            $rows[] = [
                $index + 1,
                $g->student->nisn ?? '-',
                $g->student->user->name ?? $g->student->name ?? '-',
                $g->class->name ?? '-',
                $g->subject,
                $typeLabels[$g->type] ?? ucfirst($g->type),
                (float)$g->score,
                $g->notes ?? '',
                $g->created_at ? $g->created_at->format('d/m/Y H:i') : '-',
            ];
        }

        if (!empty($rows)) {
            $sheet->fromArray($rows, null, 'A2');
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Export_Nilai_Siswa_' . date('Y-m-d_H-i') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import grades from uploaded Excel file (.xlsx / .xls)
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $file = $request->file('file');
        
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (count($rows) <= 1) {
                return back()->with('error', 'File Excel kosong atau hanya berisi header.');
            }

            $successCount = 0;
            $errorCount = 0;

            // Map type values to DB enum
            $typeMapping = [
                'harian' => 'daily',
                'daily' => 'daily',
                'uts' => 'mid_term',
                'mid_term' => 'mid_term',
                'mid' => 'mid_term',
                'uas' => 'final_term',
                'final_term' => 'final_term',
                'final' => 'final_term',
                'ujian' => 'exam',
                'exam' => 'exam',
            ];

            // Header is row 1
            // Columns: A=NISN, B=Nama Siswa, C=Kelas, D=Mata Pelajaran, E=Tipe Nilai, F=Nilai, G=Catatan
            for ($i = 2; $i <= count($rows); $i++) {
                $row = $rows[$i];
                $nisn = trim((string)($row['A'] ?? ''));
                $studentName = trim((string)($row['B'] ?? ''));
                $className = trim((string)($row['C'] ?? ''));
                $subject = trim((string)($row['D'] ?? ''));
                $typeRaw = strtolower(trim((string)($row['E'] ?? '')));
                $scoreRaw = trim((string)($row['F'] ?? ''));
                $notes = trim((string)($row['G'] ?? ''));

                if (empty($nisn) && empty($studentName)) {
                    continue; // Skip empty row
                }

                // Find student by NISN first, or by User Name
                $student = null;
                if (!empty($nisn)) {
                    $student = Student::where('nisn', $nisn)->first();
                }
                if (!$student && !empty($studentName)) {
                    $student = Student::whereHas('user', function ($q) use ($studentName) {
                        $q->where('name', 'LIKE', "%{$studentName}%");
                    })->first();
                }

                if (!$student) {
                    $errorCount++;
                    continue;
                }

                // Find class
                $classId = $student->class_id;
                if (!empty($className)) {
                    $c = ClassModel::where('name', 'LIKE', "%{$className}%")->first();
                    if ($c) {
                        $classId = $c->id;
                    }
                }

                if (!$classId || empty($subject) || !is_numeric($scoreRaw)) {
                    $errorCount++;
                    continue;
                }

                $type = $typeMapping[$typeRaw] ?? 'daily';
                $score = max(0, min(100, (float)$scoreRaw));

                Grade::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject' => $subject,
                        'type' => $type,
                    ],
                    [
                        'class_id' => $classId,
                        'score' => $score,
                        'notes' => $notes ?: null,
                        'recorded_by' => Auth::id(),
                    ]
                );

                $successCount++;
            }

            if ($successCount > 0) {
                $msg = "Berhasil mengimpor {$successCount} data nilai dari file Excel.";
                if ($errorCount > 0) {
                    $msg .= " ({$errorCount} baris dilewati karena data siswa/nilai tidak valid).";
                }
                return redirect()->route('admin.grades.index')->with('success', $msg);
            } else {
                return back()->with('error', "Gagal mengimpor. Tidak ada data nilai valid yang dapat diproses dari file Excel ({$errorCount} baris bermasalah).");
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan Penempatan Guru Bidang Studi (Penugasan Guru per Kelas & Mapel)
     */
    public function assignments(Request $request)
    {
        TeacherSubjectAssignment::ensureTableExists();

        $user = auth()->user();
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            abort(403, 'Hanya administrator atau pimpinan sekolah yang dapat mengatur penempatan guru bidang studi.');
        }

        $activeYear = AcademicYear::getActive();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $selectedYearId = $request->get('academic_year_id', $activeYear?->id);

        $classes = ClassModel::orderBy('name', 'asc')->get();
        
        // Ambil guru yang aktif berdasarkan relasi roles
        $teachers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'guru-mapel', 'guru-quran', 'admin', 'super-admin', 'kepala-sekolah']);
        })->orderBy('name', 'asc')->get();

        $subjects = $this->getAllSchoolSubjects();

        $query = TeacherSubjectAssignment::with(['teacher', 'classModel', 'academicYear'])
            ->when($selectedYearId, fn($q) => $q->where('academic_year_id', $selectedYearId))
            ->when($request->teacher_id, fn($q) => $q->where('teacher_id', $request->teacher_id))
            ->when($request->class_id, fn($q) => $q->where('class_id', $request->class_id))
            ->when($request->subject, fn($q) => $q->where('subject', $request->subject))
            ->latest('id');

        $assignments = $query->paginate(20)->withQueryString();

        return view('admin.grades.assignments', compact(
            'assignments',
            'teachers',
            'classes',
            'subjects',
            'academicYears',
            'selectedYearId',
            'activeYear'
        ));
    }

    /**
     * Simpan Penempatan Guru Bidang Studi
     */
    public function storeAssignment(Request $request)
    {
        TeacherSubjectAssignment::ensureTableExists();

        $user = auth()->user();
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id',
            'subject' => 'required|string|max:150',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'notes' => 'nullable|string',
        ], [
            'teacher_id.required' => 'Pilih guru pengampu terlebih dahulu.',
            'class_ids.required' => 'Pilih minimal satu kelas penempatan.',
            'subject.required' => 'Pilih mata pelajaran yang diajar.',
        ]);

        $activeYear = AcademicYear::getActive();
        $yearId = $validated['academic_year_id'] ?: $activeYear?->id;

        $count = 0;
        foreach ($validated['class_ids'] as $cId) {
            TeacherSubjectAssignment::updateOrCreate(
                [
                    'teacher_id' => $validated['teacher_id'],
                    'class_id' => $cId,
                    'subject' => $validated['subject'],
                    'academic_year_id' => $yearId,
                ],
                [
                    'notes' => $validated['notes'] ?? null,
                ]
            );
            $count++;
        }

        return redirect()->route('admin.grades.assignments', [
            'academic_year_id' => $yearId,
        ])->with('success', "Berhasil menugaskan guru ke {$count} kelas untuk mata pelajaran {$validated['subject']}.");
    }

    /**
     * Hapus Penempatan Guru Bidang Studi
     */
    public function destroyAssignment($id)
    {
        TeacherSubjectAssignment::ensureTableExists();

        $user = auth()->user();
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            abort(403, 'Akses ditolak.');
        }

        $assignment = TeacherSubjectAssignment::findOrFail($id);
        $assignment->delete();

        return back()->with('success', 'Penempatan guru berhasil dihapus.');
    }

    /**
     * API Riwayat Perubahan Nilai Siswa
     */
    public function getGradeHistory(Request $request)
    {
        GradeHistory::ensureTableExists();

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject' => 'required|string',
        ]);

        $student = Student::with('user')->findOrFail($request->student_id);
        $activeYear = AcademicYear::getActive();

        $histories = GradeHistory::with('user')
            ->where('student_id', $request->student_id)
            ->where('subject', $request->subject)
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($activeYear, fn($q) => $q->where('academic_year_id', $activeYear->id))
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'student_name' => $student->name,
            'student_nisn' => $student->nisn ?? $student->nis,
            'subject' => $request->subject,
            'histories' => $histories->map(function($h) {
                $typeLabels = [
                    'daily' => 'Nilai Harian (Formatif)',
                    'mid_term' => 'Penilaian Tengah Semester (PTS)',
                    'final_term' => 'Penilaian Akhir Semester (PAS)',
                ];

                return [
                    'id' => $h->id,
                    'type' => $h->type,
                    'type_label' => $typeLabels[$h->type] ?? strtoupper($h->type),
                    'old_score' => $h->old_score !== null ? (float)$h->old_score : '-',
                    'new_score' => $h->new_score !== null ? (float)$h->new_score : '-',
                    'source' => $h->source,
                    'user_name' => $h->user?->name ?? 'Sistem / Guru',
                    'notes' => $h->notes ?? '-',
                    'date' => $h->created_at ? $h->created_at->format('d M Y, H:i') : '-',
                ];
            }),
        ]);
    }

    /**
     * Ambil seluruh mata pelajaran resmi sekolah
     */
    public function getAllSchoolSubjects(): array
    {
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->pluck('name')->toArray();
        if (empty($subjects)) {
            return [
                'Pendidikan Agama Islam & Budi Pekerti',
                'Pendidikan Pancasila',
                'Bahasa Indonesia',
                'Matematika',
                'Ilmu Pengetahuan Alam dan Sosial (IPAS)',
                'Pendidikan Jasmani, Olahraga & Kesehatan (PJOK)',
                'Seni Budaya & Prakarya (SBdP)',
                'Bahasa Inggris',
                'Bahasa Arab',
                'Tahsin & Tahfidz Al-Qur\'an',
            ];
        }
        return $subjects;
    }

    /**
     * Get list of subjects
     */
    private function getSubjects(): array
    {
        $user = auth()->user();
        if ($user) {
            $assigned = $user->getAssignedSubjects();
            if ($assigned !== null && !empty($assigned)) {
                return $assigned;
            }
        }

        return $this->getAllSchoolSubjects();
    }
}
