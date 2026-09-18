<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'reference_type',
        'reference_id',
        'payment_gateway',
        'invoice_number',
        'amount',
        'status',
        'snap_token',
        'payment_url',
        'payment_method_code',
        'payload',
        'payment_proof',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the associated reference model.
     */
    public function reference()
    {
        if ($this->reference_type === 'spmb') {
            return $this->belongsTo(SpmbRegistration::class, 'reference_id');
        } elseif ($this->reference_type === 'spp') {
            return $this->belongsTo(StudentPaymentDetail::class, 'reference_id');
        }
        return null;
    }
}

