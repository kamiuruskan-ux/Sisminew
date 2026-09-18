<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Grade::with(['student', 'class', 'recordedBy'])
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->where('recorded_by', $user->id);
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
        $classes = ClassModel::all();
        $students = Student::with(['user', 'class'])->get();
        $types = ['daily', 'mid_term', 'final_term', 'exam'];
        $subjects = $this->getSubjects();

        return view('admin.grades.index', compact('grades', 'classes', 'students', 'types', 'subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = ClassModel::all();
        $students = Student::with('class')->get();
        $types = ['daily', 'mid_term', 'final_term', 'exam'];
        $subjects = $this->getSubjects();

        return view('admin.grades.create', compact('classes', 'students', 'types', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        return view('admin.grades.show', compact('grade'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grade $grade)
    {
        $classes = ClassModel::all();
        $students = Student::all();
        $types = ['daily', 'mid_term', 'final_term', 'exam'];
        $subjects = $this->getSubjects();

        return view('admin.grades.edit', compact('grade', 'classes', 'students', 'types', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grade)
    {
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
        $grade->delete();

        return redirect()->route('admin.grades.index')
            ->with('success', 'Nilai berhasil dihapus!');
    }

    /**
     * Bulk grade entry form
     */
    public function bulkCreate()
    {
        $classes = ClassModel::all();
        $students = Student::with('class')->get();
        $types = ['daily', 'mid_term', 'final_term', 'exam'];
        $subjects = $this->getSubjects();

        return view('admin.grades.bulk-create', compact('classes', 'students', 'types', 'subjects'));
    }

    /**
     * Store bulk grades
     */
    public function bulkStore(Request $request)
    {
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
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->pluck('name')->toArray();
        if (empty($subjects)) {
            return [
                'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Kimia', 'Biologi', 'Sejarah', 'Pendidikan Agama'
            ];
        }
        return $subjects;
    }
}
