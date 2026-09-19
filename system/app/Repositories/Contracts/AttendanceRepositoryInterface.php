<?php

namespace App\Repositories\Contracts;

use App\Models\TeacherAttendance;
use App\Models\AttendanceAuditLog;
use Illuminate\Database\Eloquent\Collection;

interface AttendanceRepositoryInterface
{
    /**
     * Find attendance record for a specific user and date.
     */
    public function findByUserAndDate(int $userId, string $date): ?TeacherAttendance;

    /**
     * Find or instantiate a new attendance record.
     */
    public function findOrNew(int $userId, string $date): TeacherAttendance;

    /**
     * Save an attendance record.
     */
    public function save(TeacherAttendance $attendance): TeacherAttendance;

    /**
     * Get all attendances for a specific date with relations.
     */
    public function getTodayAttendances(string $date): Collection;

    /**
     * Record an audit log entry.
     */
    public function recordAuditLog(array $data): ?AttendanceAuditLog;
}
