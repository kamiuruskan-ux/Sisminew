<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student || !$student->class_id) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Anda belum terdaftar di kelas manapun.');
        }

        $query = Grade::where('student_id', $student->id)
            ->with(['class']);

        // Filter by subject
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by search notes
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('notes', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }

        $grades = $query->latest()->paginate(15)->withQueryString();

        // Calculate statistics
        $allStudentGrades = Grade::where('student_id', $student->id);
        $totalCount = $allStudentGrades->count();
        $average = $allStudentGrades->avg('score') ?? 0;
        $highest = $allStudentGrades->max('score') ?? 0;
        $lowest = $allStudentGrades->min('score') ?? 0;
        $passedCount = Grade::where('student_id', $student->id)->where('score', '>=', 75)->count();

        $stats = [
            'average' => round($average, 1),
            'highest' => round($highest, 1),
            'lowest' => round($lowest, 1),
            'total' => $totalCount,
            'passed' => $passedCount,
            'passed_percentage' => $totalCount > 0 ? round(($passedCount / $totalCount) * 100) : 0,
            'predicate' => $average >= 90 ? 'A (Sangat Baik)' : ($average >= 80 ? 'B (Baik)' : ($average >= 70 ? 'C (Cukup)' : 'D (Perlu Perbaikan)')),
        ];

        // Unique subjects list for filter dropdown
        $subjectsList = Grade::where('student_id', $student->id)
            ->distinct()
            ->pluck('subject');

        // Group by subject with avg, max, min
        $gradesBySubject = Grade::where('student_id', $student->id)
            ->selectRaw('subject, AVG(score) as average, MAX(score) as max_score, MIN(score) as min_score, COUNT(*) as count')
            ->groupBy('subject')
            ->orderBy('subject')
            ->get();

        return view('student.grades.index', compact('grades', 'stats', 'gradesBySubject', 'subjectsList'));
    }

    /**
     * Render printable official report card for student.
     */
    public function printRaport(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Data profil siswa tidak ditemukan.');
        }

        $request->merge(['student_id' => $student->id]);
        return app(\App\Http\Controllers\Admin\RaportController::class)->print($request);
    }
}
