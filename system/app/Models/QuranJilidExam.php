<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuranJilidExam extends Model
{
    use HasFactory;

    protected $table = 'quran_jilid_exams';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'academic_year_id',
        'exam_date',
        'current_jilid',
        'target_jilid',
        'page_tested',
        'score_makhraj',
        'score_mad',
        'score_kelancaran',
        'score_final',
        'predicate',
        'status',
        'certificate_number',
        'notes',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'score_makhraj' => 'float',
        'score_mad' => 'float',
        'score_kelancaran' => 'float',
        'score_final' => 'float',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function isLulus(): bool
    {
        return $this->status === 'lulus';
    }
}
