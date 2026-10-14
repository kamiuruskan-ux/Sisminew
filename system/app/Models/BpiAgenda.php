<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BpiAgenda extends Model
{
    use HasFactory;

    protected $table = 'bpi_agendas';

    protected $fillable = [
        'bpi_group_id',
        'mentor_id',
        'academic_year_id',
        'date',
        'meeting_number',
        'topic',
        'material_summary',
        'tilawah_surah',
        'mutabaah_notes',
        'general_notes',
        'status', // submitted, approved, rejected
        'submitted_at',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'meeting_number' => 'integer',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(BpiGroup::class, 'bpi_group_id');
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(BpiAttendance::class, 'bpi_agenda_id');
    }

    public function getAttendanceCountAttribute(): array
    {
        $counts = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total' => 0,
        ];

        foreach ($this->attendances as $att) {
            $status = strtolower($att->attendance_status);
            if (isset($counts[$status])) {
                $counts[$status]++;
            }
            $counts['total']++;
        }

        return $counts;
    }
}
