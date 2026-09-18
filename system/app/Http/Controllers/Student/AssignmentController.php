<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Helpers\IdEncrypter;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;

        if (!$student || !$student->class_id) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Anda belum terdaftar di kelas manapun.');
        }

        // Get all assignments for student's class
        $assignments = Assignment::where('class_id', $student->class_id)
            ->where('status', 'published')
            ->with(['teacher', 'class'])
            ->orderBy('due_date')
            ->get();

        // Get submitted assignment IDs
        $submittedIds = AssignmentSubmission::where('student_id', $student->id)
            ->pluck('assignment_id');

        // Add submission status to each assignment
        $assignments->each(function ($assignment) use ($submittedIds, $student) {
            $assignment->is_submitted = $submittedIds->contains($assignment->id);
            $assignment->encrypted_id = IdEncrypter::encrypt($assignment->id);

            if ($assignment->is_submitted) {
                $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                    ->where('student_id', $student->id)
                    ->first();
                $assignment->submission = $submission;
            }
        });

        return view('student.assignments.index', compact('assignments'));
    }

    public function show(string $encryptedId)
    {
        $assignmentId = IdEncrypter::decrypt($encryptedId);
        $student = Auth::user()->student;

        if (!$student || !$student->class_id) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Anda belum terdaftar di kelas manapun.');
        }

        $assignment = Assignment::with(['teacher', 'class'])->findOrFail($assignmentId);

        // Check if assignment is for student's class
        if ($assignment->class_id !== $student->class_id) {
            return redirect()->route('student.assignments.index')
                ->with('error', 'Tugas ini bukan untuk kelas Anda.');
        }

        // Get user's submission if exists
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        return view('student.assignments.show', compact('assignment', 'submission', 'encryptedId'));
    }

    public function submit(Request $request, string $encryptedId)
    {
        $assignmentId = IdEncrypter::decrypt($encryptedId);
        $student = Auth::user()->student;

        if (!$student || !$student->class_id) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Anda belum terdaftar di kelas manapun.');
        }

        $assignment = Assignment::findOrFail($assignmentId);

        // Check if assignment is for student's class
        if ($assignment->class_id !== $student->class_id) {
            return redirect()->route('student.assignments.index')
                ->with('error', 'Tugas ini bukan untuk kelas Anda.');
        }

        // Check if already submitted
        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah mengumpulkan tugas ini.');
        }

        // Check if deadline has passed
        if ($assignment->due_date < now()) {
            return back()->with('error', 'Tenggat waktu pengumpulan telah lewat.');
        }

        $validated = $request->validate([
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $data = [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'content' => $validated['content'] ?? null,
            'submitted_at' => now(),
            'status' => 'submitted',
        ];

        if ($request->hasFile('attachment')) {
            $savedFile = save_uploaded_public_file($request->file('attachment'), 'doc/submissions');
            $data['attachment'] = 'doc/submissions/' . basename($savedFile);
        }

        AssignmentSubmission::create($data);

        return redirect()->route('student.assignments.show', $encryptedId)
            ->with('success', 'Tugas berhasil dikumpulkan!');
    }

    public function download(AssignmentSubmission $submission)
    {
        $student = Auth::user()->student;

        // Only allow student to download their own submission
        if ($submission->student_id !== $student->id) {
            abort(403);
        }

        if (!$submission->attachment) {
            return back()->with('error', 'File tidak tersedia.');
        }

        if (\Illuminate\Support\Facades\File::exists(public_path($submission->attachment))) {
            return response()->download(public_path($submission->attachment));
        }
        if (\Illuminate\Support\Facades\File::exists(public_path('doc/' . $submission->attachment))) {
            return response()->download(public_path('doc/' . $submission->attachment));
        }
        if (\Illuminate\Support\Facades\File::exists(public_path('img/' . $submission->attachment))) {
            return response()->download(public_path('img/' . $submission->attachment));
        }

        if (Storage::disk('doc')->exists($submission->attachment)) {
            return Storage::disk('doc')->download($submission->attachment);
        }
        if (Storage::disk('public')->exists($submission->attachment)) {
            return Storage::disk('public')->download($submission->attachment);
        }

        return back()->with('error', 'File pengumpulan tidak ditemukan.');
    }
}
