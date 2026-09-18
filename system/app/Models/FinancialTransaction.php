<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'type',
        'financial_category_id',
        'bank_account_id',
        'amount',
        'transaction_date',
        'description',
        'recipient_or_payee',
        'proof_file',
        'created_by',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'integer',
        'transaction_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function ($transaction) {
            if ($transaction->bankAccount) {
                $transaction->bankAccount->recalculateBalance();
            }
        });

        static::deleted(function ($transaction) {
            if ($transaction->bankAccount) {
                $transaction->bankAccount->recalculateBalance();
            }
        });
    }

    public function financialCategory(): BelongsTo
    {
        return $this->belongsTo(FinancialCategory::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateTransactionNumber(string $type): string
    {
        $prefix = ($type === 'pemasukan') ? 'KSM' : 'KSK';
        $dateStr = now()->format('Ym');
        $latest = static::where('transaction_number', 'LIKE', "{$prefix}-{$dateStr}-%")
            ->latest('id')
            ->first();

        if ($latest) {
            $parts = explode('-', $latest->transaction_number);
            $seq = intval(end($parts)) + 1;
        } else {
            $seq = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $dateStr, $seq);
    }
}
