<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeTaskCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'completion_date',
        'status',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'completion_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(EmployeeTask::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
