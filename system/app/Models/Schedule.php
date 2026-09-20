<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'day',
        'start_time',
        'end_time',
        'room',
        'teacher',
        'class_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getStartTimeAttribute($value)
    {
        if (!$value) return '';
        return substr((string)$value, 0, 5);
    }

    public function getEndTimeAttribute($value)
    {
        if (!$value) return '';
        return substr((string)$value, 0, 5);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day', $day);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }
}
