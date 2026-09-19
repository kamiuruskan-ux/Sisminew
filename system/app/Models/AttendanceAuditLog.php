<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceAuditLog extends Model
{
    use HasFactory;

    protected $table = 'attendance_audit_logs';

    protected $fillable = [
        'user_id',
        'teacher_attendance_id',
        'action',
        'method',
        'status',
        'session_type',
        'ip_address',
        'user_agent',
        'device_info',
        'latitude',
        'longitude',
        'distance_meters',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'distance_meters' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(TeacherAttendance::class, 'teacher_attendance_id');
    }
}
