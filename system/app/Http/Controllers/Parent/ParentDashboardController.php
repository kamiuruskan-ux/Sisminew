<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\StudentPaymentBill;
use App\Models\SavingsTransaction;
use App\Models\Schedule;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    /**
     * Display parent monitoring dashboard.
     */
    public function index()
    {
        $studentId = session('parent_student_id');

        if (!$studentId) {
            return redirect()->route('parent.login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $student = Student::with(['user', 'class', 'major'])->find($studentId);

        if (!$student) {
            session()->forget(['parent_student_id', 'parent_logged_in']);
            return redirect()->route('parent.login')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Attendance stats & recent records
        $attendances = Attendance::where('student_id', $student->id)
            ->latest('date')
            ->take(15)
            ->get();

        $attendanceStats = [
            'present' => Attendance::where('student_id', $student->id)->where('status', 'present')->count(),
            'late' => Attendance::where('student_id', $student->id)->where('status', 'late')->count(),
            'absent' => Attendance::where('student_id', $student->id)->where('status', 'absent')->count(),
            'permission' => Attendance::where('student_id', $student->id)->whereIn('status', ['permission', 'sick'])->count(),
            'total' => Attendance::where('student_id', $student->id)->count(),
        ];

        $attendanceStats['percentage'] = $attendanceStats['total'] > 0
            ? round((($attendanceStats['present'] + $attendanceStats['late']) / $attendanceStats['total']) * 100, 1)
            : 100;

        // Grades summary & recent grades
        $grades = Grade::where('student_id', $student->id)
            ->latest()
            ->get();

        $gradeStats = [
            'average' => round(Grade::where('student_id', $student->id)->avg('score') ?? 0, 1),
            'highest' => Grade::where('student_id', $student->id)->max('score') ?? 0,
            'lowest' => Grade::where('student_id', $student->id)->min('score') ?? 0,
            'total' => Grade::where('student_id', $student->id)->count(),
        ];

        // SPP & Financial Payment Bills
        $paymentBills = StudentPaymentBill::with(['paymentBill.paymentPost', 'details'])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $unpaidTotal = $paymentBills->sum(function ($bill) {
            return max(0, (float)$bill->total_amount - (float)$bill->paid_amount);
        });
        $paidTotal = $paymentBills->sum(function ($bill) {
            return (float)$bill->paid_amount;
        });

        // Today's attendance status
        $todayAttendance = Attendance::where('student_id', $student->id)
            ->whereDate('date', Carbon::today())
            ->first();

        // Class Schedules & Announcements
        $todayDay = Carbon::today()->format('l');
        $todaySchedules = Schedule::where('class_id', $student->class_id)
            ->where('day', $todayDay)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        $allSchedules = Schedule::where('class_id', $student->class_id)
            ->where('is_active', true)
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->orderBy('start_time')
            ->get();

        $days = daftar_hari_indo();

        $announcements = Announcement::published()
            ->latest()
            ->take(5)
            ->get();

        $savingsTransactions = SavingsTransaction::where('student_id', $student->id)
            ->latest()
            ->take(10)
            ->get();

        return view('parent.dashboard', compact(
            'student',
            'attendances',
            'attendanceStats',
            'todayAttendance',
            'grades',
            'gradeStats',
            'paymentBills',
            'unpaidTotal',
            'paidTotal',
            'savingsTransactions',
            'todaySchedules',
            'allSchedules',
            'days',
            'announcements'
        ));
    }

    /**
     * Print or save student ID card as PDF for parents.
     */
    public function printCard()
    {
        $studentId = session('parent_student_id');

        if (!$studentId) {
            return redirect()->route('parent.login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $student = Student::with(['user', 'class', 'major'])->find($studentId);

        if (!$student) {
            return redirect()->route('parent.login')->with('error', 'Data siswa tidak ditemukan.');
        }

        $students = collect([$student]);
        $selectedClass = $student->class;

        return view('admin.student_cards.print', compact('students', 'selectedClass'));
    }

    /**
     * Print student report card for parents.
     */
    public function printRaport(Request $request)
    {
        $studentId = session('parent_student_id');

        if (!$studentId) {
            return redirect()->route('parent.login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $request->merge(['student_id' => $studentId]);
        return app(\App\Http\Controllers\Admin\RaportController::class)->print($request);
    }
}
