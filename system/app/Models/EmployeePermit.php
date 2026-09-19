<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EmployeePermit extends Model
{
    protected $fillable = [
        'user_id',
        'permit_type',
        'start_date',
        'end_date',
        'reason',
        'proof_file',
        'emergency_contact',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Synchronize approved permit to teacher_attendances table.
     */
    public function syncAttendance(): void
    {
        if ($this->status !== 'approved') {
            return;
        }

        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        $period = CarbonPeriod::create($startDate, $endDate);

        $attendanceStatus = ($this->permit_type === 'sakit') ? 'sick' : 'permission';
        $workLocation = ($this->permit_type === 'tugas_luar') ? 'outstation' : 'home';
        $recorderId = $this->approved_by ?? auth()->id() ?? 1;

        $notesText = "[Izin Resmi Disetujui ({$this->type_label})]: " . trim($this->reason);
        if (!empty($this->notes)) {
            $notesText .= " (Catatan Kepsek: " . trim($this->notes) . ")";
        }

        foreach ($period as $date) {
            // Skip Sunday (libur mingguan sekolah)
            if ($date->isSunday()) {
                continue;
            }

            $formattedDate = $date->format('Y-m-d');

            TeacherAttendance::updateOrCreate(
                [
                    'user_id' => $this->user_id,
                    'date' => $formattedDate,
                ],
                [
                    'status' => $attendanceStatus,
                    'work_location' => $workLocation,
                    'notes' => $notesText,
                    'attachment' => $this->proof_file,
                    'recorded_by' => $recorderId,
                    'method' => 'permit',
                    'verification_status' => 'verified',
                ]
            );
        }
    }

    /**
     * Revert attendance records previously created by this permit.
     */
    public function revertAttendance(): void
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');

            TeacherAttendance::where('user_id', $this->user_id)
                ->where('date', $formattedDate)
                ->where('method', 'permit')
                ->delete();
        }
    }

    /**
     * Accessor for permit type in Indonesian.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->permit_type) {
            'sakit' => 'Sakit',
            'izin' => 'Izin Pribadi',
            'cuti' => 'Cuti Tahunan/Khusus',
            'tugas_luar' => 'Tugas / Dinas Luar',
            'lainnya' => 'Lainnya',
            default => ucfirst($this->permit_type),
        };
    }

    /**
     * Accessor for permit type badge styling.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->permit_type) {
            'sakit' => 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-300/40',
            'izin' => 'bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border-purple-300/40',
            'cuti' => 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-300/40',
            'tugas_luar' => 'bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border-teal-300/40',
            default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300/40',
        };
    }

    /**
     * Accessor for status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    /**
     * Accessor for status badge styling.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-300/40',
            'approved' => 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-300/40',
            'rejected' => 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-300/40',
            default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300/40',
        };
    }

    /**
     * Calculate duration in days.
     */
    public function getDurationDaysAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 1;
        }
        return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
    }
}
