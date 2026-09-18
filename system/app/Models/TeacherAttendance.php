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
        'check_out',
        'status',
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
        return match ($this->status) {
            'present' => 'Hadir Tepat Waktu',
            'late' => 'Terlambat',
            'sick' => 'Sakit',
            'permission' => 'Izin',
            'absent' => 'Alpa',
            default => 'Hadir',
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


