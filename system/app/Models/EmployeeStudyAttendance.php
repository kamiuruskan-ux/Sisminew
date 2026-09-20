<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeStudyAttendance extends Model
{
    protected $table = 'employee_study_attendances';

    protected $fillable = [
        'session_id',
        'user_id',
        'status',
        'notes',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(EmployeeStudySession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
