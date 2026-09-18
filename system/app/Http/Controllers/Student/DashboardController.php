<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Material;
use App\Models\AssignmentSubmission;
use App\Models\Announcement;
use App\Models\StudentPaymentBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('logout')
                ->with('error', 'Profil siswa tidak ditemukan. Silakan hubungi administrator.');
        }

        $classId = $student->class_id;

        // Generate QR code data (SVG vector NISN)
        $qrData = $student->nisn ?? $user->email;
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrData);

        // Today's schedule
        $today = Carbon::today()->format('l');
        $todaySchedule = Schedule::where('class_id', $classId)
            ->where('day', $today)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        // All schedules for the week
        $weekSchedule = Schedule::where('class_id', $classId)
            ->where('is_active', true)
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->orderBy('start_time')
            ->get();

        // Active assignments (pending)
        $pendingAssignments = Assignment::where('class_id', $classId)
            ->where('status', 'published')
            ->where('due_date', '>', now())
            ->orderBy('due_date')
            ->get();

        // Get user's submitted assignments
        $submittedAssignmentIds = AssignmentSubmission::where('student_id', $student->id)
            ->pluck('assignment_id');

        $pendingAssignments = $pendingAssignments->filter(function ($assignment) use ($submittedAssignmentIds) {
            return !$submittedAssignmentIds->contains($assignment->id);
        });

        // Recent assignments (submitted/graded)
        $recentSubmissions = AssignmentSubmission::where('student_id', $student->id)
            ->with('assignment')
            ->latest()
            ->take(5)
            ->get();

        // Attendance stats
        $attendanceStats = [
            'present' => Attendance::where('student_id', $student->id)
                ->whereIn('status', ['present', 'late'])
                ->count(),
            'absent' => Attendance::where('student_id', $student->id)
                ->where('status', 'absent')
                ->count(),
            'late' => Attendance::where('student_id', $student->id)
                ->where('status', 'late')
                ->count(),
            'total' => Attendance::where('student_id', $student->id)->count(),
        ];

        $attendanceStats['percentage'] = $attendanceStats['total'] > 0
            ? round((($attendanceStats['present'] + $attendanceStats['late']) / $attendanceStats['total']) * 100, 2)
            : 0;

        // Grade stats
        $gradeStats = [
            'average' => Grade::where('student_id', $student->id)
                ->avg('score') ?? 0,
            'total' => Grade::where('student_id', $student->id)->count(),
        ];

        // Recent grades
        $recentGrades = Grade::where('student_id', $student->id)
            ->latest()
            ->take(5)
            ->get();

        // Materials
        $materials = Material::where('class_id', $classId)
            ->where('is_published', true)
            ->latest()
            ->take(10)
            ->get();

        // Latest announcements
        $announcements = Announcement::published()
            ->latest()
            ->take(5)
            ->get();

        // Unpaid bills count
        $unpaidBillsCount = StudentPaymentBill::where('student_id', $student->id)
            ->whereIn('status', ['unpaid', 'partially_paid', 'pending'])
            ->count();

        return view('student.dashboard', compact(
            'user',
            'student',
            'todaySchedule',
            'weekSchedule',
            'pendingAssignments',
            'recentSubmissions',
            'attendanceStats',
            'gradeStats',
            'recentGrades',
            'materials',
            'announcements',
            'unpaidBillsCount',
            'qrCode'
        ));
    }

    public function announcements()
    {
        $announcements = Announcement::published()->latest()->paginate(10);
        return view('student.announcements', compact('announcements'));
    }
}
