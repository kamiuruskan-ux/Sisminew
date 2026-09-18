<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'start_year',
        'end_year',
        'start_date',
        'end_date',
        'semester',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'start_date_formatted',
        'end_date_formatted',
    ];

    public function getStartDateFormattedAttribute(): string
    {
        return $this->start_date->format('Y-m-d');
    }

    public function getEndDateFormattedAttribute(): string
    {
        return $this->end_date->format('Y-m-d');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(ClassModel::class);
    }

    public function waves(): HasMany
    {
        return $this->hasMany(Wave::class);
    }

    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }
}
