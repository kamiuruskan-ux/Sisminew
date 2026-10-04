<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'title',
        'content',
        'rating',
        'avatar',
        'order',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Scope active testimonials.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered testimonials.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Get avatar url or fallback to ui-avatars.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return get_public_file_url($this->avatar, 'img/testimonials');
        }

        $colors = ['4f46e5', '0284c7', '059669', 'd97706', '7c3aed', 'e11d48'];
        $colorIndex = abs(crc32($this->name)) % count($colors);
        $bgColor = $colors[$colorIndex];

        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background={$bgColor}&color=fff";
    }
}
