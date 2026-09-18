<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsTopicProgress extends Model
{
    protected $table = 'lms_topic_progress';

    protected $fillable = [
        'student_id',
        'lms_topic_id',
        'is_completed',
        'completed_at',
        'watch_seconds',
        'quiz_completed',
        'quiz_score',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'quiz_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(LmsTopic::class, 'lms_topic_id');
    }
}
