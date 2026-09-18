<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'account_number',
        'bank_name',
        'type',
        'initial_balance',
        'current_balance',
        'description',
        'qr_code',
        'is_active',
    ];

    protected $casts = [
        'initial_balance' => 'integer',
        'current_balance' => 'integer',
        'is_active' => 'boolean',
    ];

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function recalculateBalance(): void
    {
        $pemasukan = $this->financialTransactions()->where('type', 'pemasukan')->sum('amount');
        $pengeluaran = $this->financialTransactions()->where('type', 'pengeluaran')->sum('amount');

        $this->current_balance = $this->initial_balance + $pemasukan - $pengeluaran;
        $this->save();
    }
}
