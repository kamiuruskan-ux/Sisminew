<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_payment_bill_id',
        'month_no',
        'month_name',
        'amount',
        'paid_amount',
        'status',
        'paid_at',
        'bank_account_id',
        'financial_transaction_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function studentPaymentBill(): BelongsTo
    {
        return $this->belongsTo(StudentPaymentBill::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function financialTransaction(): BelongsTo
    {
        return $this->belongsTo(FinancialTransaction::class);
    }
}
