<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class StudentPermit extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'permit_type',
        'start_date',
        'end_date',
        'reason',
        'proof_file',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Synchronize approved permit to attendances table.
     */
    public function syncAttendance(): void
    {
        if ($this->status !== 'approved') {
            return;
        }

        $student = $this->student;
        $classId = $this->class_id ?? ($student ? $student->class_id : null);
        if (!$student || !$classId) {
            return;
        }

        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        $period = CarbonPeriod::create($startDate, $endDate);

        $typeLabel = ucfirst($this->permit_type);
        $notesText = "{$typeLabel}: {$this->reason}";
        $recorderId = $this->approved_by ?? auth()->id() ?? 1;

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            
            Attendance::updateOrCreate(
                [
                    'student_id' => $this->student_id,
                    'date' => $formattedDate,
                ],
                [
                    'class_id' => $classId,
                    'status' => 'excused',
                    'notes' => $notesText,
                    'recorded_by' => $recorderId,
                ]
            );
        }
    }

    /**
     * Accessor for label permit_type in Indonesian.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->permit_type) {
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'dispensasi' => 'Dispensasi',
            'lainnya' => 'Lainnya',
            default => ucfirst($this->permit_type),
        };
    }
}
