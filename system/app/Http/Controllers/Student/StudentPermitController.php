<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentPermit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StudentPermitController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Data profil siswa tidak ditemukan.');
        }

        $permits = StudentPermit::where('student_id', $student->id)
            ->latest()
            ->paginate(10);

        return view('student.permits.index', compact('permits'));
    }

    public function create()
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Data profil siswa tidak ditemukan.');
        }

        return view('student.permits.create', compact('student'));
    }

    public function store(Request $request)
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Data profil siswa tidak ditemukan.');
        }

        $validated = $request->validate([
            'permit_type' => 'required|in:izin,sakit,dispensasi,lainnya',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:10240',
        ]);

        $validated['student_id'] = $student->id;
        $validated['class_id'] = $student->class_id;
        $validated['status'] = 'pending';

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

        StudentPermit::create($validated);

        return redirect()->route('student.permits.index')
            ->with('success', 'Pengajuan izin berhasil dikirim! Menunggu verifikasi sekolah.');
    }

    public function destroy(StudentPermit $permit)
    {
        $student = auth()->user()->student;

        if (!$student || $permit->student_id !== $student->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if ($permit->status !== 'pending') {
            return redirect()->back()->with('error', 'Permohonan izin yang sudah diproses tidak dapat dibatalkan.');
        }

        if ($permit->proof_file) {
            delete_public_file($permit->proof_file, 'doc/submissions');
        }

        $permit->delete();

        return redirect()->route('student.permits.index')
            ->with('success', 'Pengajuan izin berhasil dibatalkan.');
    }
}
