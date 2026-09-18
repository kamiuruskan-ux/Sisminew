<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = AssignmentSubmission::with(['assignment', 'student', 'gradedBy'])
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->whereHas('assignment', function ($a) use ($user) {
                    $a->where('teacher_id', $user->id);
                });
            })
            ->when($request->assignment_id, function ($q) use ($request) {
                return $q->where('assignment_id', $request->assignment_id);
            })
            ->when($request->student_id, function ($q) use ($request) {
                return $q->where('student_id', $request->student_id);
            })
            ->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->latest();

        $submissions = $query->paginate(20);
        $assignments = $user->isTeacher() ? Assignment::where('teacher_id', $user->id)->get() : Assignment::all();
        $students = Student::with(['user', 'class'])->get();

        return view('admin.assignment-submissions.index', compact('submissions', 'assignments', 'students'));
    }

    /**
     * Display the specified resource.
     */
    public function show(AssignmentSubmission $submission)
    {
        return view('admin.assignment-submissions.show', compact('submission'));
    }

    /**
     * Show the form for grading a submission.
     */
    public function grade(AssignmentSubmission $submission)
    {
        return view('admin.assignment-submissions.grade', compact('submission'));
    }

    /**
     * Store/Update grade for a submission.
     */
    public function storeGrade(Request $request, AssignmentSubmission $submission)
    {
        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
            'status' => 'required|in:graded,late',
        ]);

        $validated['graded_by'] = Auth::id();
        $validated['submitted_at'] = $submission->submitted_at ?? now();

        $submission->update($validated);

        return redirect()->route('admin.assignment-submissions.index')
            ->with('success', 'Nilai berhasil diberikan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssignmentSubmission $submission)
    {
        $validated = $request->validate([
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
            'status' => 'required|in:pending,submitted,graded,late',
        ]);

        if ($request->hasFile('attachment')) {
            if ($submission->attachment) {
                delete_public_file($submission->attachment, 'doc/submissions');
            }
            $savedFile = save_uploaded_public_file($request->file('attachment'), 'doc/submissions');
            $validated['attachment'] = 'doc/submissions/' . basename($savedFile);
        } else {
            unset($validated['attachment']);
        }

        $submission->update($validated);

        return redirect()->route('admin.assignment-submissions.index')
            ->with('success', 'Pengumpulan tugas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignmentSubmission $submission)
    {
        if ($submission->attachment) {
            delete_public_file($submission->attachment, 'doc/submissions');
        }

        $submission->delete();

        return redirect()->route('admin.assignment-submissions.index')
            ->with('success', 'Pengumpulan berhasil dihapus!');
    }

    /**
     * Download submission attachment
     */
    public function download(AssignmentSubmission $submission)
    {
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
