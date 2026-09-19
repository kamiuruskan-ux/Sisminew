<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAttendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'midday_at',
        'check_out',
        'status',
        'delay_minutes',
        'session_name',
        'verification_status',
        'work_location',
        'check_in_lat',
        'check_in_long',
        'check_out_lat',
        'check_out_long',
        'check_in_photo',
        'check_out_photo',
        'notes',
        'attachment',
        'recorded_by',
        'method',
        'device_info',
    ];

    protected $casts = [
        'date' => 'date',
        'delay_minutes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getStatusLabelAttribute(): string
    {
        $delay = $this->delay_minutes ? " ({$this->delay_minutes} mnt)" : '';
        return match ($this->status) {
            'present' => 'Tepat Waktu',
            'late' => 'Terlambat' . $delay,
            'very_late' => 'Sangat Terlambat' . $delay,
            'outside_window' => 'Di Luar Jam Presensi',
            'sick' => 'Sakit',
            'permission' => 'Izin',
            'absent' => 'Alpa / Belum Hadir',
            default => ucfirst(str_replace('_', ' ', $this->status ?? 'Hadir')),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'present' => 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-300/50',
            'late' => 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-300/50',
            'very_late' => 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-300/50',
            'outside_window' => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-300/50',
            'sick' => 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-300/50',
            'permission' => 'bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border-purple-300/50',
            'absent' => 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200 border-rose-400/50',
            default => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300',
        };
    }

    public function getLocationLabelAttribute(): string
    {
        return match ($this->work_location) {
            'school' => 'WFO (Di Sekolah)',
            'home' => 'WFH (Daring / Rumah)',
            'outstation' => 'Dinas Luar',
            default => 'Di Sekolah',
        };
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'fingerprint' => 'Sidik Jari (USB Scanner Admin)',
            'mobile_gps' => 'Mobile HP (Lock GPS)',
            'face_id' => 'Biometrik Face ID',
            default => 'Manual Admin',
        };
    }
}


