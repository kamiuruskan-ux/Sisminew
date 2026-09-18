<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LmsTopic extends Model
{
    protected $fillable = [
        'lms_chapter_id',
        'title',
        'description',
        'video_url',
        'video_duration',
        'summary_file',
        'order',
        'xp_reward',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getDurationInSecondsAttribute(): int
    {
        if (empty($this->video_duration)) {
            return 10;
        }

        $parts = array_map('intval', explode(':', trim($this->video_duration)));
        $count = count($parts);

        if ($count === 3) {
            // HH:MM:SS (e.g. 00:10:00 -> 600s, 00:05:30 -> 330s)
            $secs = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
            return $secs > 0 ? $secs : 10;
        }

        if ($count === 2) {
            // HH:MM from HTML time input (e.g. 00:10 -> 10 mins = 600s) or MM:SS (e.g. 10:30 -> 630s)
            if ($parts[0] === 0) {
                $secs = $parts[1] * 60;
            } else {
                $secs = ($parts[0] * 60) + $parts[1];
            }
            return $secs > 0 ? $secs : 10;
        }

        return is_numeric($this->video_duration) && (int)$this->video_duration > 0 ? (int)$this->video_duration : 10;
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(LmsChapter::class, 'lms_chapter_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(LmsTopicQuiz::class, 'lms_topic_id');
    }

    public function studentProgress(): HasOne
    {
        return $this->hasOne(LmsTopicProgress::class, 'lms_topic_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'lms_topic_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'lms_topic_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'lms_topic_id');
    }
}

