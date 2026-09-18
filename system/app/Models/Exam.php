<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'subject_name',
        'class_id',
        'lms_chapter_id',
        'lms_topic_id',
        'teacher_id',
        'duration_minutes',
        'start_time',
        'end_time',
        'is_published',
        'exam_type',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($exam) {
            \Illuminate\Support\Facades\Cache::forget("exam_show_{$exam->id}");
        });

        static::deleted(function ($exam) {
            \Illuminate\Support\Facades\Cache::forget("exam_show_{$exam->id}");
        });
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function chapter()
    {
        return $this->belongsTo(LmsChapter::class, 'lms_chapter_id');
    }

    public function topic()
    {
        return $this->belongsTo(LmsTopic::class, 'lms_topic_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
}
