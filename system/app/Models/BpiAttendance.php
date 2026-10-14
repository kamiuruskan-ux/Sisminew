<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BpiAttendance extends Model
{
    use HasFactory;

    protected $table = 'bpi_attendances';

    protected $fillable = [
        'bpi_agenda_id',
        'student_id',
        'attendance_status',
        'tilawah_pages',
        'score_attitude',
        'mutabaah_status',
        'notes',
    ];

    protected $casts = [
        'tilawah_pages' => 'integer',
        'score_attitude' => 'float',
    ];

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(BpiAgenda::class, 'bpi_agenda_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
