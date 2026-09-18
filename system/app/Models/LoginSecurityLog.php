<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginSecurityLog extends Model
{
    use HasFactory;

    protected $table = 'login_security_logs';

    protected $fillable = [
        'user_id',
        'identifier',
        'ip_address',
        'user_agent',
        'status',
        'failure_reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
