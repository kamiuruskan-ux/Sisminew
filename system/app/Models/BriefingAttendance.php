<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BriefingAttendance extends Model
{
    protected $table = 'briefing_attendances';

    protected $fillable = [
        'session_id',
        'user_id',
        'date',
        'attended_at',
        'device_info',
        'latitude',
        'longitude',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(BriefingSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        BriefingSession::ensureTablesExist();
    }
}
