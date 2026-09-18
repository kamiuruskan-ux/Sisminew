<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CanteenItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'stall_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_percent',
        'original_price',
        'stock',
        'image',
        'stall_name',
        'is_available',
    ];

    protected $casts = [
        'price' => 'integer',
        'discount_percent' => 'integer',
        'original_price' => 'integer',
        'stock' => 'integer',
        'is_available' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name) . '-' . Str::random(5);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CanteenCategory::class, 'category_id');
    }

    public function stall(): BelongsTo
    {
        return $this->belongsTo(CanteenStall::class, 'stall_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(CanteenOrderItem::class, 'canteen_item_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return get_public_file_url($this->image, 'img/canteen/items');
    }
}
