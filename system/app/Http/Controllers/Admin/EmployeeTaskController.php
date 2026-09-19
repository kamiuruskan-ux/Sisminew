<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\EmployeeTaskCompletion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeTaskController extends Controller
{
    /**
     * Display tasks & checklist portal
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $tab = $request->query('tab', 'berlangsung'); // berlangsung, mendatang, riwayat
        $filterCategory = $request->query('category');

        $isPrivileged = $user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('kepala-sekolah');

        // Retrieve all employees for bulk assigner in modal
        $employees = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['guru', 'teacher', 'staff', 'tata-usaha', 'operator', 'kantin', 'kepala-sekolah']);
        })->orderBy('name')->get();

        // Query active tasks
        $baseQuery = EmployeeTask::with(['assignees', 'creator', 'completions'])
            ->when(!$isPrivileged, function ($q) use ($user) {
                // Non-privileged users only see tasks assigned to them
                $q->whereHas('assignees', function ($sq) use ($user) {
                    $sq->where('user_id', $user->id);
                });
            })
            ->when($filterCategory, function ($q) use ($filterCategory) {
                $q->where('category', $filterCategory);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('due_time', 'asc');

        $allTasks = $baseQuery->get();

        // Partition tasks based on today, upcoming, and history
        $todayTasks = collect();
        $upcomingTasks = collect();
        $historyTasks = collect();

        foreach ($allTasks as $task) {
            // Check if applies to today
            if ($task->appliesToDate($today)) {
                $todayTasks->push($task);
            }

            // Check if has upcoming occurrences (e.g. start_date > today or recurring in future)
            if ($task->start_date && Carbon::parse($task->start_date)->gt(Carbon::today())) {
                $upcomingTasks->push($task);
            } elseif ($task->recurrence !== 'none' && (!$task->end_date || Carbon::parse($task->end_date)->gt(Carbon::today()))) {
                $upcomingTasks->push($task);
            }

            // History tasks: one-time tasks in past or tasks with recorded completions
            if ($task->recurrence === 'none' && Carbon::parse($task->start_date)->lt(Carbon::today())) {
                $historyTasks->push($task);
            } elseif ($task->completions()->where('completion_date', '<', $today)->exists()) {
                $historyTasks->push($task);
            }
        }

        // Today's statistics for current user
        $totalTodayCount = $todayTasks->count();
        $completedTodayCount = 0;

        foreach ($todayTasks as $t) {
            if ($t->isCompletedByUserOnDate($user->id, $today)) {
                $completedTodayCount++;
            }
        }

        $progressPercentage = $totalTodayCount > 0 ? round(($completedTodayCount / $totalTodayCount) * 100) : 100;

        // Group completions for history tab
        $recentCompletions = EmployeeTaskCompletion::with(['task', 'user'])
            ->when(!$isPrivileged, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('completion_date', 'desc')
            ->orderBy('completed_at', 'desc')
            ->paginate(15);

        return view('admin.employee-tasks.index', compact(
            'todayTasks',
            'upcomingTasks',
            'historyTasks',
            'recentCompletions',
            'employees',
            'totalTodayCount',
            'completedTodayCount',
            'progressPercentage',
            'today',
            'tab',
            'isPrivileged',
            'filterCategory'
        ));
    }

    /**
     * Store a newly created task with bulk assignments
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'priority' => 'required|in:low,medium,high,urgent',
            'recurrence' => 'required|in:none,daily,weekly,monthly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'due_time' => 'nullable',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $task = EmployeeTask::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'priority' => $request->priority,
                'recurrence' => $request->recurrence,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'due_time' => $request->due_time,
                'created_by' => Auth::id(),
                'is_active' => true,
            ]);

            $task->assignees()->sync($request->assignees);

            DB::commit();
            return redirect()->route('admin.employee-tasks.index')->with('success', 'Tugas harian pegawai berhasil dibuat dan didistribusikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat tugas: ' . $e->getMessage());
        }
    }

    /**
     * Update task details & assignees
     */
    public function update(Request $request, $id)
    {
        $task = EmployeeTask::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'priority' => 'required|in:low,medium,high,urgent',
            'recurrence' => 'required|in:none,daily,weekly,monthly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'due_time' => 'nullable',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'priority' => $request->priority,
                'recurrence' => $request->recurrence,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'due_time' => $request->due_time,
            ]);

            $task->assignees()->sync($request->assignees);

            DB::commit();
            return redirect()->route('admin.employee-tasks.index')->with('success', 'Tugas berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui tugas: ' . $e->getMessage());
        }
    }

    /**
     * Delete task
     */
    public function destroy($id)
    {
        $task = EmployeeTask::findOrFail($id);
        $task->delete();

        return redirect()->route('admin.employee-tasks.index')->with('success', 'Tugas pegawai berhasil dihapus.');
    }

    /**
     * Toggle checklist status for a task on the current active date.
     * Rule: Only tasks for today can be toggled!
     */
    public function toggleChecklist(Request $request, $id)
    {
        $user = Auth::user();
        $task = EmployeeTask::findOrFail($id);
        $today = Carbon::today()->toDateString();
        $targetDate = $request->input('date', $today);

        // Strict validation: Only today's tasks can be toggled!
        if ($targetDate !== $today) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas hanya dapat diklik dan diselesaikan pada tanggal yang sedang berjalan (' . Carbon::parse($today)->translatedFormat('d F Y') . ').',
            ], 403);
        }

        if (!$task->appliesToDate($today)) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas ini tidak dijadwalkan untuk hari ini.',
            ], 422);
        }

        // Check if completion record already exists
        $completion = EmployeeTaskCompletion::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->where('completion_date', $today)
            ->first();

        $isCompleted = false;

        if ($completion && $completion->status === 'completed') {
            // Revert back to uncompleted / delete record
            $completion->delete();
            $isCompleted = false;
            $msg = "Status tugas '{$task->title}' dibatalkan.";
        } else {
            // Mark as completed
            EmployeeTaskCompletion::updateOrCreate(
                [
                    'task_id' => $task->id,
                    'user_id' => $user->id,
                    'completion_date' => $today,
                ],
                [
                    'status' => 'completed',
                    'completed_at' => Carbon::now(),
                    'notes' => $request->input('notes'),
                ]
            );
            $isCompleted = true;
            $msg = "Alhamdulillah! Tugas '{$task->title}' berhasil diselesaikan.";
        }

        // Recalculate today's progress for this user
        $myTodayTasks = EmployeeTask::whereHas('assignees', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get()->filter(fn($t) => $t->appliesToDate($today));

        $totalToday = $myTodayTasks->count();
        $completedToday = 0;
        foreach ($myTodayTasks as $t) {
            if ($t->isCompletedByUserOnDate($user->id, $today)) {
                $completedToday++;
            }
        }

        $percentage = $totalToday > 0 ? round(($completedToday / $totalToday) * 100) : 100;

        return response()->json([
            'success' => true,
            'message' => $msg,
            'is_completed' => $isCompleted,
            'completed_count' => $completedToday,
            'total_count' => $totalToday,
            'percentage' => $percentage,
        ]);
    }
}
