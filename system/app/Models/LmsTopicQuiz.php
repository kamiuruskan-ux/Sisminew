<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LmsTopicQuiz extends Model
{
    protected $fillable = [
        'lms_topic_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'correct_option',
        'explanation',
        'explanation_video',
        'xp_reward',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(LmsTopic::class, 'lms_topic_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(LmsTopicQuizAnswer::class, 'lms_topic_quiz_id');
    }

    public function studentAnswer(): HasOne
    {
        return $this->hasOne(LmsTopicQuizAnswer::class, 'lms_topic_quiz_id');
    }
}
