<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtracurricularAgendaStudent extends Model
{
    use HasFactory;

    protected $table = 'extracurricular_agenda_students';

    protected $fillable = [
        'extracurricular_agenda_id',
        'student_id',
        'attendance_status',
        'score_skill',
        'score_attitude',
        'notes',
    ];

    protected $casts = [
        'score_skill' => 'float',
        'score_attitude' => 'float',
    ];

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(ExtracurricularAgenda::class, 'extracurricular_agenda_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
