<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsTopicQuizAnswer extends Model
{
    protected $table = 'lms_topic_quiz_answers';

    protected $fillable = [
        'student_id',
        'lms_topic_quiz_id',
        'selected_option',
        'is_correct',
        'awarded_xp',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'awarded_xp' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LmsTopicQuiz::class, 'lms_topic_quiz_id');
    }
}
