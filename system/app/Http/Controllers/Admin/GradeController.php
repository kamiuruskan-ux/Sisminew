<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Grade;
use App\Models\Student;
use App\Models\TeachingAgenda;
use App\Models\TeachingAgendaStudent;
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
     * Display a listing of the resource with 3 tabs:
     * Tab 1: Agenda Kelas & Penilaian Bidang Studi
     * Tab 2: Histori Mengajar Saya
     * Tab 3: Rerata Nilai Rapor Siswa
     */
    public function index(Request $request)
    {
        TeachingAgenda::ensureTableExists();

        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();
        $allowedSubjects = $user->getAssignedSubjects();

        $tab = $request->get('tab', 'agenda'); // 'agenda', 'history', 'summary'

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

        // Daftar Mata Pelajaran
        $allSubjects = $this->getSubjects();
        $teacherSubjects = ($allowedSubjects !== null && !empty($allowedSubjects)) ? $allowedSubjects : $allSubjects;
        $selectedSubject = $request->get('subject') ?? ($teacherSubjects[0] ?? null);

        // Data Histori Mengajar (Tab 2)
        $historyQuery = TeachingAgenda::with(['class', 'students.student.user', 'teacher'])->latest('date')->latest('id');
        if ($allowedClassIds !== null) {
            $historyQuery->whereIn('class_id', $allowedClassIds);
        }
        if (!$user->hasRole(['super-admin', 'admin', 'kepala-sekolah'])) {
            $historyQuery->where('teacher_id', $user->id);
        }
        $teachingAgendas = $historyQuery->paginate(10, ['*'], 'history_page');

        // Data Rerata Nilai Rapor Siswa (Tab 3)
        $query = Grade::with(['student', 'class', 'recordedBy'])
            ->when($allowedClassIds !== null, function ($q) use ($allowedClassIds) {
                return $q->whereIn('class_id', $allowedClassIds);
            })
            ->when($allowedSubjects !== null && !empty($allowedSubjects), function ($q) use ($allowedSubjects) {
                return $q->whereIn('subject', $allowedSubjects);
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

        $grades = $query->paginate(20);
        $students = $allowedClassIds !== null 
            ? Student::with(['user', 'class'])->whereIn('class_id', $allowedClassIds)->get() 
            : Student::with(['user', 'class'])->get();
        $types = ['daily', 'mid_term', 'final_term', 'exam'];
        $subjects = $allSubjects;

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
            'subjects'
        ));
    }

    /**
     * Simpan Agenda Pembelajaran Kelas & Penilaian Siswa Bidang Studi
     */
    public function storeAgenda(Request $request)
    {
        $user = auth()->user();
        $allowedClassIds = $user->getAssignedClassIds();
        if ($allowedClassIds !== null && !in_array($request->class_id, $allowedClassIds)) {
            abort(403, 'Anda tidak memiliki hak akses menginput agenda untuk kelas ini.');
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

        DB::beginTransaction();
        try {
            TeachingAgenda::ensureTableExists();

            $agenda = TeachingAgenda::create([
                'teacher_id' => $user->id,
                'class_id' => $validated['class_id'],
                'academic_year_id' => AcademicYear::getActive()?->id,
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

        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->pluck('name')->toArray();
        if (empty($subjects)) {
            return [
                'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Kimia', 'Biologi', 'Sejarah', 'Pendidikan Agama'
            ];
        }
        return $subjects;
    }
}
