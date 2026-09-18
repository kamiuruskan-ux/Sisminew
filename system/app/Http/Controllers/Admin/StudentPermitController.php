<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentPermit;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StudentPermitController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentPermit::with(['student.user', 'student.class', 'class', 'approver'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('permit_type'), fn($q) => $q->where('permit_type', $request->permit_type))
            ->when($request->filled('class_id'), fn($q) => $q->where('class_id', $request->class_id))
            ->when($request->filled('major_id'), function($q) use ($request) {
                if ($request->major_id === 'none') {
                    $q->whereHas('class', fn($cq) => $cq->whereNull('major_id'));
                } else {
                    $q->whereHas('class', fn($cq) => $cq->where('major_id', $request->major_id));
                }
            })
            ->when($request->filled('student_id'), fn($q) => $q->where('student_id', $request->student_id))
            ->when($request->filled('start_date'), fn($q) => $q->where('start_date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->where('end_date', '<=', $request->end_date))
            ->latest();

        $permits = $query->paginate(15)->withQueryString();

        $classes = ClassModel::all();
        $students = Student::with(['user', 'class'])->get();

        $stats = [
            'total' => StudentPermit::count(),
            'pending' => StudentPermit::where('status', 'pending')->count(),
            'approved' => StudentPermit::where('status', 'approved')->count(),
            'rejected' => StudentPermit::where('status', 'rejected')->count(),
        ];

        return view('admin.student-permits.index', compact('permits', 'classes', 'students', 'stats'));
    }

    public function create()
    {
        $classes = ClassModel::all();
        $students = Student::with(['user', 'class'])->get();

        return view('admin.student-permits.create', compact('classes', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'permit_type' => 'required|in:izin,sakit,dispensasi,lainnya',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:10240',
            'notes' => 'nullable|string|max:500',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $validated['class_id'] = $student->class_id;

        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'doc', 'docx', 'xlsx', 'xls']);
            
            $folderName = 'student_permits';
            $targetDir = public_path($isDoc ? "doc/{$folderName}" : "img/{$folderName}");
            
            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }

            $filename = time() . '_' . Str::random(10) . '.' . $ext;
            $file->move($targetDir, $filename);

            $validated['proof_file'] = ($isDoc ? "doc/{$folderName}/" : "img/{$folderName}/") . $filename;
        }

        // Admin-created permits default to approved
        $validated['status'] = 'approved';
        $validated['approved_by'] = auth()->id();
        $validated['approved_at'] = now();

        $permit = StudentPermit::create($validated);
        $permit->syncAttendance();

        return redirect()->back()
            ->with('success', 'Permohonan Izin Siswa berhasil ditambahkan dan disinkronkan ke presensi!');
    }

    public function approve(Request $request, StudentPermit $permit)
    {
        $permit->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->input('notes', $permit->notes),
        ]);

        $permit->syncAttendance();

        return redirect()->back()
            ->with('success', 'Permohonan izin siswa disetujui dan data presensi berhasil diubah otomatis!');
    }

    public function reject(Request $request, StudentPermit $permit)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $permit->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->input('notes'),
        ]);

        return redirect()->back()
            ->with('success', 'Permohonan izin siswa telah ditolak.');
    }

    public function destroy(StudentPermit $permit)
    {
        if ($permit->proof_file) {
            delete_public_file($permit->proof_file, 'doc/submissions');
        }

        $permit->delete();

        return redirect()->route('admin.student-permits.index')
            ->with('success', 'Data permohonan izin berhasil dihapus.');
    }
}
