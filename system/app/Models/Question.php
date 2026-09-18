<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',
        'question_text',
        'image_path',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'options_json',
        'correct_answer',
        'correct_answer_json',
        'score_weight',
    ];

    protected $casts = [
        'options_json' => 'array',
        'correct_answer_json' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($question) {
            \Illuminate\Support\Facades\Cache::forget("exam_show_{$question->exam_id}");
        });

        static::deleted(function ($question) {
            \Illuminate\Support\Facades\Cache::forget("exam_show_{$question->exam_id}");
        });
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return get_public_file_url($this->image_path, 'img/questions');
    }
}
