<?php

namespace App\Repositories;

use App\Models\TeacherAttendance;
use App\Models\AttendanceAuditLog;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TeacherAttendanceRepository implements AttendanceRepositoryInterface
{
    /**
     * Find attendance record for a specific user and date.
     */
    public function findByUserAndDate(int $userId, string $date): ?TeacherAttendance
    {
        return TeacherAttendance::where('user_id', $userId)
            ->where('date', $date)
            ->first();
    }

    /**
     * Find or instantiate a new attendance record.
     */
    public function findOrNew(int $userId, string $date): TeacherAttendance
    {
        return TeacherAttendance::firstOrNew([
            'user_id' => $userId,
            'date' => $date,
        ]);
    }

    /**
     * Save an attendance record.
     */
    public function save(TeacherAttendance $attendance): TeacherAttendance
    {
        $attendance->save();
        return $attendance;
    }

    /**
     * Get all attendances for a specific date with relations.
     */
    public function getTodayAttendances(string $date): Collection
    {
        return TeacherAttendance::with(['user.homeroomClasses', 'recorder'])
            ->where('date', $date)
            ->latest('updated_at')
            ->get();
    }

    /**
     * Record an audit log entry.
     */
    public function recordAuditLog(array $data): ?AttendanceAuditLog
    {
        try {
            if (Schema::hasTable('attendance_audit_logs')) {
                return AttendanceAuditLog::create($data);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to persist AttendanceAuditLog: ' . $e->getMessage(), $data);
        }

        // Fallback logging into standard log
        Log::info('ATTENDANCE_AUDIT_LOG', $data);
        return null;
    }
}
