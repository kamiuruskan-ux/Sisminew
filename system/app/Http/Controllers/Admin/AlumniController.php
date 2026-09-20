<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Major;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassModel::where('is_active', true)->orderBy('name')->get();
        $majors = Major::where('is_active', true)->orderBy('name')->get();

        $query = Student::where('student_status', 'alumni')
            ->with(['user', 'class', 'major']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('graduation_year')) {
            if (Schema::hasColumn('students', 'graduation_year')) {
                $query->where('graduation_year', $request->graduation_year);
            } else {
                $query->where('entry_year', $request->graduation_year);
            }
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $alumniList = $query->latest()->paginate(15)->withQueryString();

        $totalAlumni = Student::where('student_status', 'alumni')->count();
        $maleCount = Student::where('student_status', 'alumni')->where('gender', 'male')->count();
        $femaleCount = Student::where('student_status', 'alumni')->where('gender', 'female')->count();
        
        $currentYear = date('Y');
        $thisYearGraduates = 0;
        if (Schema::hasColumn('students', 'graduation_year')) {
            $thisYearGraduates = Student::where('student_status', 'alumni')->where('graduation_year', $currentYear)->count();
        }

        // Distinct graduation years for filtering
        $graduationYears = [];
        if (Schema::hasColumn('students', 'graduation_year')) {
            $graduationYears = Student::where('student_status', 'alumni')
                ->whereNotNull('graduation_year')
                ->distinct()
                ->pluck('graduation_year')
                ->sortDesc()
                ->values()
                ->toArray();
        }

        return view('admin.alumni.index', compact(
            'alumniList',
            'classes',
            'majors',
            'totalAlumni',
            'maleCount',
            'femaleCount',
            'thisYearGraduates',
            'graduationYears'
        ));
    }

    /**
     * Proses Kelulusan Per Rombel / Kelas Masal
     */
    public function graduateClass(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'graduation_year' => 'required|string|max:20',
            'alumni_notes' => 'nullable|string|max:500',
        ]);

        $class = ClassModel::findOrFail($request->class_id);
        $students = Student::where('class_id', $class->id)
            ->where(function($q) {
                $q->whereIn('student_status', ['active', 'Aktif'])
                  ->orWhereNull('student_status');
            })->get();

        if ($students->isEmpty()) {
            return back()->with('error', "Tidak ada siswa berstatus aktif di kelas {$class->name}.");
        }

        DB::beginTransaction();
        try {
            $count = 0;
            $hasGradYear = Schema::hasColumn('students', 'graduation_year');
            $hasAlumniNotes = Schema::hasColumn('students', 'alumni_notes');

            foreach ($students as $student) {
                $student->student_status = 'alumni';
                if ($hasGradYear) {
                    $student->graduation_year = $request->graduation_year;
                }
                if ($hasAlumniNotes && $request->filled('alumni_notes')) {
                    $student->alumni_notes = $request->alumni_notes;
                }
                // Kosongkan kelas agar tidak tampil di roster kelas aktif berikutnya
                $student->class_id = null;
                $student->save();
                $count++;
            }

            DB::commit();
            return back()->with('success', "Alhamdulillah, berhasil meluluskan {$count} siswa dari kelas {$class->name} angkatan {$request->graduation_year} menjadi Alumni.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses kelulusan: ' . $e->getMessage());
        }
    }

    /**
     * Pindahkan Siswa Terpilih ke Alumni
     */
    public function graduateSelected(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'graduation_year' => 'required|string|max:20',
            'alumni_notes' => 'nullable|string|max:500',
        ]);

        $hasGradYear = Schema::hasColumn('students', 'graduation_year');
        $hasAlumniNotes = Schema::hasColumn('students', 'alumni_notes');

        $students = Student::whereIn('id', $request->student_ids)->get();
        $count = 0;

        foreach ($students as $student) {
            $student->student_status = 'alumni';
            if ($hasGradYear) {
                $student->graduation_year = $request->graduation_year;
            }
            if ($hasAlumniNotes && $request->filled('alumni_notes')) {
                $student->alumni_notes = $request->alumni_notes;
            }
            $student->class_id = null;
            $student->save();
            $count++;
        }

        return back()->with('success', "Berhasil memindahkan {$count} siswa ke Daftar Alumni.");
    }

    /**
     * Kembalikan Status Siswa dari Alumni ke Siswa Aktif
     */
    public function revertStatus(Request $request, Student $student)
    {
        $request->validate([
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $student->student_status = 'active';
        if ($request->filled('class_id')) {
            $student->class_id = $request->class_id;
        }
        $student->save();

        if ($student->user) {
            $student->user->status = 'active';
            $student->user->save();
        }

        return back()->with('success', "Status {$student->name} berhasil dikembalikan menjadi Siswa Aktif.");
    }

    /**
     * Export Data Alumni ke Excel (.xlsx)
     */
    public function export(Request $request)
    {
        $query = Student::where('student_status', 'alumni')->with(['user', 'class', 'major']);

        if ($request->filled('graduation_year') && Schema::hasColumn('students', 'graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }

        $alumni = $query->latest()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Alumni');

        // Header Table
        $headers = ['No', 'Nama Siswa', 'NISN', 'NIS', 'Jenis Kelamin', 'Tahun Lulus', 'No. WhatsApp / HP', 'Email', 'Catatan / Kelanjutan Studi'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Style Header
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 2;
        $hasGradYear = Schema::hasColumn('students', 'graduation_year');
        $hasAlumniNotes = Schema::hasColumn('students', 'alumni_notes');

        foreach ($alumni as $idx => $student) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $student->name);
            $sheet->setCellValue('C' . $row, "'" . ($student->nisn ?: '-'));
            $sheet->setCellValue('D' . $row, "'" . ($student->nis ?: '-'));
            $sheet->setCellValue('E' . $row, $student->gender == 'male' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('F' . $row, $hasGradYear ? ($student->graduation_year ?: '-') : ($student->entry_year ?: '-'));
            $sheet->setCellValue('G' . $row, "'" . ($student->phone ?: $student->parent_phone ?: '-'));
            $sheet->setCellValue('H' . $row, $student->email ?: $student->user?->email ?: '-');
            $sheet->setCellValue('I' . $row, $hasAlumniNotes ? ($student->alumni_notes ?: '-') : '-');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Data_Alumni_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
