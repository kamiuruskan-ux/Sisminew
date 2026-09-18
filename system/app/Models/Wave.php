<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wave extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'academic_year_id',
        'quota',
        'start_date',
        'end_date',
        'registration_fee',
        'spp_discount',
        'status',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_fee' => 'integer',
        'spp_discount' => 'integer',
    ];

    protected $appends = ['year'];

    public function getYearAttribute(): ?string
    {
        // Get year from academic year relationship
        if ($this->academicYear) {
            return $this->academicYear->name;
        }
        // Fallback: extract year from name (e.g., "Gelombang 1 - 2026" -> "2026")
        if (preg_match('/(\d{4})/', $this->name, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function spmbRegistrations(): HasMany
    {
        return $this->hasMany(SpmbRegistration::class);
    }

    public function getRemainingQuotaAttribute(): int
    {
        return $this->quota - $this->spmbRegistrations()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function isActive(): bool
    {
        return $this->status === 'active' 
            && now()->between($this->start_date, $this->end_date);
    }
}
