<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingAgendaStudent extends Model
{
    use HasFactory;

    protected $table = 'teaching_agenda_students';

    protected $fillable = [
        'teaching_agenda_id',
        'student_id',
        'attendance_status',
        'score_cognitive',
        'score_adab',
        'notes',
    ];

    protected $casts = [
        'score_cognitive' => 'float',
        'score_adab' => 'float',
    ];

    public function teachingAgenda(): BelongsTo
    {
        return $this->belongsTo(TeachingAgenda::class, 'teaching_agenda_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
