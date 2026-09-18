<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaBroadcastLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'wa_broadcast_id',
        'recipient_name',
        'recipient_phone',
        'status',
        'response_message',
    ];

    public function broadcast()
    {
        return $this->belongsTo(WaBroadcast::class, 'wa_broadcast_id');
    }
}
