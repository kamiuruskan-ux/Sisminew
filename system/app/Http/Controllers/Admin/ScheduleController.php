<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Schedule::with('class')
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->where('teacher', 'like', "%{$user->name}%");
            });

        if ($request->filled('major_id')) {
            $query->whereHas('class', function ($q) use ($request) {
                $q->where('major_id', $request->major_id);
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('teacher', 'like', "%{$search}%")
                  ->orWhere('room', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $totalSchedules = (clone $query)->count();
        $activeSchedules = (clone $query)->where('is_active', true)->count();
        $totalRooms = (clone $query)->distinct('room')->count('room');

        $schedules = $query->orderBy('day')->orderBy('start_time')->paginate(20)->withQueryString();

        $classes = ClassModel::orderBy('name')->get();
        $days = daftar_hari_indo();
        $subjectsList = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->get();

        $teachers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        })->orderBy('name')->get();

        return view('admin.schedules.index', compact(
            'schedules',
            'classes',
            'days',
            'subjectsList',
            'teachers',
            'totalSchedules',
            'activeSchedules',
            'totalRooms'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = ClassModel::all();
        $days = daftar_hari_indo();
        $teachers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        })->orderBy('name')->get();

        return view('admin.schedules.create', compact('classes', 'days', 'teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'required|string|max:255',
            'teacher' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'is_active' => 'boolean',
        ]);

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $classes = ClassModel::all();
        $days = daftar_hari_indo();
        $teachers = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        })->orderBy('name')->get();

        return view('admin.schedules.edit', compact('schedule', 'classes', 'days', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'required|string|max:255',
            'teacher' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'is_active' => 'boolean',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dihapus!');
    }
}
