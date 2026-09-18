<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentPaymentBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'payment_bill_id',
        'total_amount',
        'paid_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'paid_amount' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function paymentBill(): BelongsTo
    {
        return $this->belongsTo(PaymentBill::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(StudentPaymentDetail::class);
    }

    public function recalculateStatus(): void
    {
        $paid = $this->details()->sum('paid_amount');
        $this->paid_amount = $paid;

        if ($paid >= $this->total_amount && $this->total_amount > 0) {
            $this->status = 'paid';
        } elseif ($paid > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'unpaid';
        }

        $this->save();
    }
}
