<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CanteenTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'stall_id',
        'type',
        'amount',
        'balance_after',
        'reference_no',
        'description',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
    ];

    public function stall(): BelongsTo
    {
        return $this->belongsTo(CanteenStall::class, 'stall_id');
    }
}
