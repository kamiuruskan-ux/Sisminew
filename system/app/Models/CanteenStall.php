<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CanteenStall extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'owner_name',
        'phone',
        'banner',
        'logo',
        'description',
        'balance',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'open_time',
        'close_time',
        'operating_hours',
        'rating',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'float',
        'is_active' => 'boolean',
        'balance' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stall) {
            if (empty($stall->slug)) {
                $stall->slug = Str::slug($stall->name);
            }
            if (empty($stall->open_time)) {
                $stall->open_time = '07:00';
            }
            if (empty($stall->close_time)) {
                $stall->close_time = '15:00';
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(CanteenItem::class, 'stall_id');
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(CanteenItem::class, 'stall_id')->where('is_available', true);
    }

    /**
     * Real-Time Check if stall is currently open (Asia/Jakarta timezone).
     */
    public function getIsOpenNowAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!empty($this->open_time) && !empty($this->close_time)) {
            $now = now('Asia/Jakarta')->format('H:i');
            $open = substr($this->open_time, 0, 5);
            $close = substr($this->close_time, 0, 5);

            if ($open <= $close) {
                return $now >= $open && $now <= $close;
            } else {
                return $now >= $open || $now <= $close;
            }
        }

        return $this->is_active;
    }

    public function getBannerUrlAttribute(): string
    {
        return get_public_file_url($this->banner, 'img/canteen/stalls') ?? 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1000&q=80';
    }

    public function getLogoUrlAttribute(): string
    {
        return get_public_file_url($this->logo, 'img/canteen/stalls') ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=300&q=80';
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(CanteenWithdrawal::class, 'stall_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CanteenTransaction::class, 'stall_id');
    }
}
