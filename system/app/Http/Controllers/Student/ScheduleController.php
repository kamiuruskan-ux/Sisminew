<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student || !$student->class_id) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Anda belum terdaftar di kelas manapun.');
        }

        $schedules = Schedule::where('class_id', $student->class_id)
            ->where('is_active', true)
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->orderBy('start_time')
            ->get();

        $days = daftar_hari_indo();

        return view('student.schedules.index', compact('schedules', 'days'));
    }
}
