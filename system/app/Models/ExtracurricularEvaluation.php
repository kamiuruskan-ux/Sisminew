<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtracurricularEvaluation extends Model
{
    use HasFactory;

    protected $table = 'extracurricular_evaluations';

    protected $fillable = [
        'extracurricular_id',
        'student_id',
        'academic_year_id',
        'semester',
        'predicate',
        'score',
        'description',
        'evaluated_by',
    ];

    protected $casts = [
        'score' => 'float',
    ];

    public function extracurricular(): BelongsTo
    {
        return $this->belongsTo(Extracurricular::class, 'extracurricular_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
