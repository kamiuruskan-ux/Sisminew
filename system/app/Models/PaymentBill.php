<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_post_id',
        'academic_year_id',
        'target_type',
        'class_id',
        'major_id',
        'student_id',
        'name',
        'type',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function paymentPost(): BelongsTo
    {
        return $this->belongsTo(PaymentPost::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function studentPaymentBills(): HasMany
    {
        return $this->hasMany(StudentPaymentBill::class);
    }
}
