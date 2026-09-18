<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsLiveClass extends Model
{
    protected $fillable = [
        'class_id',
        'teacher_id',
        'subject',
        'title',
        'description',
        'scheduled_at',
        'duration_minutes',
        'meeting_url',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
