<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'priority',
        'recurrence',
        'start_date',
        'end_date',
        'due_time',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'employee_task_assignees', 'task_id', 'user_id')->withTimestamps();
    }

    public function completions()
    {
        return $this->hasMany(EmployeeTaskCompletion::class, 'task_id');
    }

    /**
     * Determine if this task is active/applies on a given date (Y-m-d).
     */
    public function appliesToDate($dateStr): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $checkDate = \Carbon\Carbon::parse($dateStr);
        $startDate = \Carbon\Carbon::parse($this->start_date);

        if ($checkDate->lt($startDate->startOfDay())) {
            return false;
        }

        if ($this->end_date) {
            $endDate = \Carbon\Carbon::parse($this->end_date);
            if ($checkDate->gt($endDate->endOfDay())) {
                return false;
            }
        }

        switch ($this->recurrence) {
            case 'none':
                return $checkDate->isSameDay($startDate);
            case 'daily':
                return true;
            case 'weekly':
                return $checkDate->dayOfWeek === $startDate->dayOfWeek;
            case 'monthly':
                return $checkDate->day === $startDate->day;
            default:
                return true;
        }
    }

    /**
     * Check if a specific user completed this task on a given date.
     */
    public function isCompletedByUserOnDate($userId, $dateStr): bool
    {
        return $this->completions()
            ->where('user_id', $userId)
            ->where('completion_date', $dateStr)
            ->where('status', 'completed')
            ->exists();
    }
}
